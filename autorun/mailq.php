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

/*
 * смотрим почту в очереди и отправляем одно письмо за раз
 * после чего очередь сокращаем на 1 письмо
 */
try {
	$row = DB::prepare('SELECT * FROM mailq LIMIT 1')->execute()->fetch();
	if ($row) {
		mailq($row['to'], $row['title'], $row['btxt']);
		try {
			DB::prepare('DELETE FROM mailq WHERE id = :id')->execute(array(':id' => $row['id']));
		} catch (PDOException $ex) {
			$err[] = 'Не получилось удалить сообщение из очереди ' . $ex->getMessage();
		}
	}
} catch (PDOException $ex) {
	$err[] = 'Не получилось прочитать очередь сообщений ' . $ex->getMessage();
}
