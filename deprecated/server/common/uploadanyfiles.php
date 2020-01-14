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

/* Запрещаем прямой вызов скрипта. */
defined('SITE_EXEC') or die('Доступ запрещён');

$contractid = PostDef('contractid');
$uploaddir = SITE_ROOT . '/files/';
$orig_file = $_FILES['filedata']['name'];
$userfile_name = basename($orig_file);
//$len = strlen($userfile_name);
//$ext_file = substr($userfile_name, $len - 4, $len);
$tmp = GetRandomId(5);
$userfile_name = $tmp . $userfile_name;
$uploadfile = $uploaddir . $userfile_name;
$sr = $_FILES['filedata']['tmp_name'];
$dest = $uploadfile;
$res = move_uploaded_file($sr, $dest);
if ($res) {
	$rs = ['msg' => $userfile_name];
	if ($contractid != '') {
		$sql = <<<TXT
INSERT INTO files_contract
            (id,idcontract,filename,userfreandlyfilename)
VALUES      (NULL, :contractid, :userfile_name, :orig_file)
TXT;
		try {
			DB::prepare($sql)->execute([
				':contractid' => $contractid,
				':userfile_name' => $userfile_name,
				':orig_file' => $orig_file
			]);
		} catch (PDOException $ex) {
			throw new DBException('Не могу добавить файл', 0, $ex);
		}
	}
} else {
	$rs = ['msg' => 'error'];
}
jsonExit($rs);
