<?php

/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$step = GetDef('step');

if (($user->mode == 1) && ($step != '')) {
	$dtpost = DateToMySQLDateTime2(PostDef('dtpost'));
	if ($dtpost == '') {
		$err[] = 'Не введена дата!';
	}
	$title = PostDef('title');
	if ($title == '') {
		$title = 'Не задан заголовок!';
	}
	$txt = ClearMySqlString($sqlcn->idsqlconnection, PostDef('txt'));
	if ($txt == '') {
		$txt = 'Нету тела новости!';
	}
	$newsid = GetDef('newsid');

	if ($step == 'add') {
		if (count($err) == 0) {
			$sql = "INSERT INTO news (id,dt,title,body) VALUES (NULL,'$dtpost','$title','$txt')";
			$result = $sqlcn->ExecuteSQL($sql);
			//echo "$sql";
			if ($result == '') {
				die('Не смог добавить новость!: ' . mysqli_error($sqlcn->idsqlconnection));
			}
		}
	}

	if (($step == 'edit') && ($newsid != '')) {
		if (count($err) == 0) {
			$sql = "UPDATE news SET dt='$dtpost',title='$title',body='$txt' WHERE id='$newsid'";
			$result = $sqlcn->ExecuteSQL($sql);
			if ($result == '') {
				die('Не смог отредактировать новость!: ' . mysqli_error($sqlcn->idsqlconnection));
			}
		}
	}
}
