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

// Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

use core\request;
use core\utils;

$req = request::getInstance();
$geteqid = $req->get('geteqid');
$uploaddir = SITE_ROOT . '/photos/';
$userfile_name = basename($_FILES['filedata']['name']);
$len = strlen($userfile_name);
$ext_file = substr($userfile_name, $len - 4, $len);
$tmp = utils::getRandomDigits(20);
$userfile_name = $tmp . $ext_file;
$uploadfile = $uploaddir . $userfile_name;
$sr = $_FILES['filedata']['tmp_name'];
$dest = $uploadfile;
$res = move_uploaded_file($sr, $dest);
if ($res) {
	$rs = ['msg' => $userfile_name];
	if ($geteqid != '') {
		try {
			$sql = 'update equipment set photo = :userfile_name where id = :geteqid';
			db::prepare($sql)->execute([':userfile_name' => $userfile_name, ':geteqid' => $geteqid]);
		} catch (PDOException $ex) {
			throw new dbexception('Не могу обновить фото', 0, $ex);
		}
	}
} else {
	$rs = ['msg' => 'error'];
}
utils::jsonExit($rs);
