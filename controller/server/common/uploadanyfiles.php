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

$contractid = PostDef('contractid');
$uploaddir = WUO_ROOT . '/files/';

$userfile_name = basename($_FILES['filedata']['name']);
$orig_file = $_FILES['filedata']['name'];
$len = strlen($userfile_name);
$ext_file = substr($userfile_name, $len - 4, $len);
$tmp = GetRandomId(5);
$userfile_name = $tmp . $userfile_name;
$uploadfile = $uploaddir . $userfile_name;

$sr = $_FILES['filedata']['tmp_name'];
$dest = $uploadfile;
$res = move_uploaded_file($sr, $dest);
if ($res) {
	$rs = array('msg' => $userfile_name);
	if ($contractid != '') {
		$sql = <<<TXT
INSERT INTO files_contract
            (id,idcontract,filename,userfreandlyfilename)
VALUES      (NULL,'$contractid','$userfile_name','$orig_file')
TXT;
		$sqlcn->ExecuteSQL($sql)
				or die('Не могу добавить файл! ' . mysqli_error($sqlcn->idsqlconnection));
	}
} else {
	$rs = array('msg' => 'error');
}
jsonExit($rs);
