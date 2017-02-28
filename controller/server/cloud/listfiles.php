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

$page = GetDef('page', 1);
if ($page == 0) {
	$page = 1;
}
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$oper = PostDef('oper');
$id = PostDef('id');
$title = PostDef('title');
$cloud_dirs_id = GetDef('cloud_dirs_id');

if ($oper == '') {
	// Проверка: может ли пользователь просматривать?
	($user->isAdmin() || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');

	// Готовим ответ
	$responce = new stdClass();
	$responce->page = 0;
	$responce->total = 0;
	$responce->records = 0;

	$sql = 'SELECT COUNT(*) AS cnt FROM cloud_files WHERE cloud_dirs_id = :cloud_dirs_id';
	try {
		$row = DB::prepare($sql)->execute(array(':cloud_dirs_id' => $cloud_dirs_id))->fetch();
		$count = ($row) ? $row['cnt'] : 0;
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать количество записей', 0, $ex);
	}
	if ($count == 0) {
		jsonExit($responce);
	}

	$total_pages = ceil($count / $limit);
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	if ($start < 0) {
		jsonExit($responce);
	}

	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;

	$sql = <<<TXT
SELECT   *
FROM     cloud_files
WHERE    cloud_dirs_id = :cloud_dirs_id
ORDER BY $sidx $sord
LIMIT    $start, $limit
TXT;
	try {
		$i = 0;
		$arr = DB::prepare($sql)->execute(array(':cloud_dirs_id' => $cloud_dirs_id))->fetchAll();
		foreach ($arr as $row) {
			$responce->rows[$i]['id'] = $row['id'];
			switch (pathinfo($row['filename'], PATHINFO_EXTENSION)) {
				case 'jpeg':
				case 'jpg':
				case 'png':
					$ico = '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
					break;
				case 'xls':
				case 'ods':
					$ico = '<i class="fa a-file-excel-o" aria-hidden="true"></i>';
					break;
				case 'doc':
				case 'odt':
					$ico = '<i class="fa fa-file-word-o" aria-hidden="true"></i>';
					break;
				default:
					$ico = '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
			}
			$ico = '<a target="_blank" href="/route/controller/server/cloud/download.php?id=' . $row['id'] . '">' . $ico . '</a>';
			$title = $row['title'];
			$responce->rows[$i]['cell'] = array($row['id'], $ico, $title, $row['dt'], human_sz($row['sz']));
			$i++;
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список файлов', 0, $ex);
	}
	jsonExit($responce);
}

if ($oper == 'edit') {
	// Проверка: может ли пользователь редактировать?
	($user->isAdmin() || $user->TestRoles('1,5')) or die('Для редактирования не хватает прав!');

	$sql = 'UPDATE cloud_files SET title = :title WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':title' => $title, ':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не могу выполнить запрос', 0, $ex);
	}
	exit;
}

if ($oper == 'del') {
	// Проверка: может ли пользователь удалять?
	($user->isAdmin() || $user->TestRoles('1,6')) or die('Для удаления не хватает прав!');

	$sql = 'DELETE FROM cloud_files WHERE id = :id';
	try {
		DB::prepare($sql)->execute(array(':id' => $id));
	} catch (PDOException $ex) {
		throw new DBException('Не могу выполнить запрос', 0, $ex);
	}
	exit;
}
