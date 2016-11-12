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
		$err[] = 'Не задан заголовок!';
	}
	$txt = PostDef('txt');
	if ($txt == '') {
		$err[] = 'Нет текста новости!';
	}

	if (count($err) == 0) {
		if ($step == 'add') {
			$sql = 'INSERT INTO news (id, dt, title, body) VALUES (NULL, :dtpost, :title, :txt)';
			try {
				DB::prepare($sql)->execute(array(
					':dtpost' => $dtpost,
					':title' => $title,
					':txt' => $txt
				));
			} catch (PDOException $ex) {
				throw new DBException('Не смог добавить новость!', 0, $ex);
			}
		}

		if ($step == 'edit') {
			$newsid = GetDef('newsid');
			if ($newsid != '') {
				$sql = 'UPDATE news SET dt = :dtpost, title= :title, body= :txt WHERE id = :newsid';
				try {
					DB::prepare($sql)->execute(array(
						':dtpost' => $dtpost,
						':title' => $title,
						':txt' => $txt,
						':newsid' => $newsid
					));
				} catch (PDOException $ex) {
					throw new DBException('Не смог отредактировать новость!', 0, $ex);
				}
			}
		}
	}
}
