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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

$geteqid = PostDef('geteqid');
$uploaddir = SITE_ROOT . '/photos/';

$userfile_name = basename($_FILES['filedata']['name']);
$len = strlen($userfile_name);
$ext_file = substr($userfile_name, $len - 4, $len);
$tmp = GetRandomId(20);
$userfile_name = $tmp . $ext_file;
$uploadfile = $uploaddir . $userfile_name;

$sr = $_FILES['filedata']['tmp_name'];
$dest = $uploadfile;
$res = move_uploaded_file($sr, $dest);
if ($res) {
	$rs = array('msg' => $userfile_name);
	if ($geteqid != '') {
		$sql = 'UPDATE equipment SET photo = :userfile_name WHERE id = :geteqid';
		try {
			DB::prepare($sql)->execute(array(
				':userfile_name' => $userfile_name,
				':geteqid' => '$geteqid'
			));
		} catch (PDOException $ex) {
			throw new DBException('Не могу обновить фото', 0, $ex);
		}
	}
} else {
	$rs = array('msg' => 'error');
}
jsonExit($rs);
