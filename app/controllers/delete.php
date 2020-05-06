<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчик: Грибов Павел
 * Сайт: http://грибовы.рф
 */
/*
 * Inventory - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчик: Сергей Солодягин (solodyagin@gmail.com)
 */

namespace app\controllers;

use PDOException;
use core\controller;
use core\user;
use core\db;
use core\dbexception;
use core\utils;

class delete extends controller {

	function index() {
		$user = user::getInstance();
		$data['section'] = 'Настройка / Удаление объектов';
		if ($user->isAdmin() || $user->testRights([1, 6])) {
			$this->view->renderTemplate('delete/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

	function execute() {
		$mfiles = utils::getArrayFilesInDir(SITE_ROOT . '/inc/deleterules');
		foreach ($mfiles as $fname) {
			if (strripos($fname, '.xml') != false) {
				echo "- обрабатываю правила $fname<br>";
				$xml = simplexml_load_file(SITE_ROOT . "/inc/deleterules/$fname");
				foreach ($xml->entertable as $data) {
					$entertable_name = $data['name'];
					$entertable_comment = $data['comment'];
					$entertable_key = $data['key'];
					echo "-- таблица $entertable_name ($entertable_comment). Поиск зависимостей по ключу $entertable_key<br>";
					try {
						$arr = db::prepare("select * from $entertable_name where active = 0")->execute()->fetchAll();
						foreach ($arr as $row) {
							// листаем все записи таблицы помеченные на удаление
							$entertable_id = $row["$entertable_key"];
							echo "--- проверяем зависимости в $entertable_name с $entertable_key = $entertable_id<br>";
							foreach ($data->reqtable as $data_req) {
								$data_req_name = $data_req['name'];
								$data_req_comment = $data_req['comment'];
								$data_req_key = $data_req['key'];
								$data_req_is_delete = $data_req['is_delete'];
								$yet = false;
								echo "---- зависимая таблица $data_req_name ($data_req_comment). Поиск зависимостей по ключу $data_req_key. Удалять зависимости: $data_req_is_delete<br>";
								// если удаляем безоговорочно, то удаляем. Иначе - если записи есть в таблице зависимые, то прерываем выполнение скрипта
								if ($data_req_is_delete == 'yes') {
									db::prepare("delete from $data_req_name where $data_req_key = $entertable_id")->execute(); // удаляем содержимое таблицы
									echo '----- удалено<br>';
								} else {
									$row2 = db::prepare("select * from $data_req_name where $data_req_key = $entertable_id")->execute()->fetch();  // проверяем наличие записей
									if ($row2) {
										$yet = true;
										echo '----- найдена неудаляемая зависимость. Выход из цикла удаления<br>';
									}
								}
								if (($yet) && ($data_req_is_delete == 'no')) {
									break;
								}
							}
							if (($yet) && ($data_req_is_delete == 'no')) {
								break;
							} else {
								db::prepare("delete from $entertable_name where $entertable_key = $entertable_id")->execute(); // удаляем содержимое таблицы
								echo "--- удалена запись в $entertable_name с $entertable_key = $entertable_id<br>";
							}
						}
					} catch (PDOException $ex) {
						throw new dbexception('Неверный запрос', 0, $ex);
					}
				}
			}
		}
	}

}
