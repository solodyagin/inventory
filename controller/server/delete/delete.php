<?php

// Данный код создан и распространяется по лицензии GPL v3
// Изначальный автор данного кода - Грибов Павел
// http://грибовы.рф

defined('WUO_ROOT') or die('Доступ запрещён'); // Запрещаем прямой вызов скрипта.

$mfiles1 = GetArrayFilesInDir(WUO_ROOT . '/modules/deleterules');
foreach ($mfiles1 as &$fname1) {
	if (strripos($fname1, '.xml') != false) {
		echo "-обрабатываю правила $fname1<br>";
		$xml = simplexml_load_file(WUO_ROOT . "/modules/deleterules/$fname1");
//		if (isset(simplexml_load_file(WUO_ROOT . "/modules/deleterules/$fname1"))) {
//			$xml = simplexml_load_file(WUO_ROOT . "/modules/deleterules/$fname1");
//		} else {
//			echo '-- Файл пустой!';
//		}
		foreach ($xml->entertable as $data) {
			$entertable_name = $data['name'];
			$entertable_comment = $data['comment'];
			$entertable_key = $data['key'];
			echo "--таблица $entertable_name ($entertable_comment). Поиск зависимостей по ключу $entertable_key<br>";
			$result = $sqlcn->ExecuteSQL("SELECT * FROM $entertable_name where active=0");
			// проверяется на пустой запрос или неверные данные в xml файле
			if ($result == '') {
				die('<b>Неверный запрос 1:</b> ' . mysqli_error($sqlcn->idsqlconnection));
			}
			// листаем все записи таблицы помеченные на удаление
			while ($myrow = mysqli_fetch_array($result)) {
				$entertable_id = $myrow["$entertable_key"];
				echo "---проверяем зависимости в $entertable_name с $entertable_key=$entertable_id<br>";
				foreach ($data->reqtable as $data_req) {
					$data_req_name = $data_req['name'];
					$data_req_comment = $data_req['comment'];
					$data_req_key = $data_req['key'];
					$data_req_is_delete = $data_req['is_delete'];
					$yet = false;
					echo "----зависимая таблица $data_req_name ($data_req_comment). Поиск зависимостей по ключу $data_req_key. Удалять зависимости: $data_req_is_delete<br>";
					// если удаляем безоговорочно, то удаляем. Иначе - если записи есть в таблице зависимые, то прерываем выполнение скрипта
					if ($data_req_is_delete == 'yes') {
						$result2 = $sqlcn->ExecuteSQL("delete FROM $data_req_name where $data_req_key=$entertable_id"); // удаляем содержимое таблицы
						echo '-----удалено<br>';
					} else {
						$result2 = $sqlcn->ExecuteSQL("SELECT * FROM $data_req_name where $data_req_key=$entertable_id");  // проверяем наличие записей
						while ($myrow2 = mysqli_fetch_array($result2)) {
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
					$result2 = $sqlcn->ExecuteSQL("DELETE FROM $entertable_name where $entertable_key=$entertable_id"); // удаляем содержимое таблицы
					echo "---удалена запись в $entertable_name с $entertable_key=$entertable_id<br>";
				}
			}
			//var_dump($data);
		}
	}
}
unset($fname1);
