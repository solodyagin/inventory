<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */


// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$mfiles = GetArrayFilesInDir(WUO_ROOT . '/inc/deleterules');

foreach ($mfiles as $fname) {
	if (strripos($fname, '.xml') != false) {
		echo "- обрабатываю правила $fname<br>";
		$xml = simplexml_load_file(WUO_ROOT . "/inc/deleterules/$fname");
		foreach ($xml->entertable as $data) {
			$entertable_name = $data['name'];
			$entertable_comment = $data['comment'];
			$entertable_key = $data['key'];
			echo "-- таблица $entertable_name ($entertable_comment). Поиск зависимостей по ключу $entertable_key<br>";
			try {
				$arr = DB::prepare("SELECT * FROM $entertable_name WHERE active = 0")->execute()->fetchAll();
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
							DB::prepare("DELETE FROM $data_req_name WHERE $data_req_key = $entertable_id")->execute(); // удаляем содержимое таблицы
							echo '----- удалено<br>';
						} else {
							$row2 = DB::prepare("SELECT * FROM $data_req_name WHERE $data_req_key = $entertable_id")->execute()->fetch();  // проверяем наличие записей
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
						DB::prepare("DELETE FROM $entertable_name WHERE $entertable_key = $entertable_id")->execute(); // удаляем содержимое таблицы
						echo "--- удалена запись в $entertable_name с $entertable_key = $entertable_id<br>";
					}
				}
			} catch (PDOException $ex) {
				throw new DBException('Неверный запрос', 0, $ex);
			}
		}
	}
}
