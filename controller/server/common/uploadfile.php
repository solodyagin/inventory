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

$geteqid = PostDef('geteqid');
$uploaddir = WUO_ROOT . '/photos/';

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
		$sql = "UPDATE equipment SET photo = '$userfile_name' WHERE id = '$geteqid'";
		$result = $sqlcn->ExecuteSQL($sql)
				or die('Не могу обновить фото! ' . mysqli_error($sqlcn->idsqlconnection));
	}
} else {
	$rs = array('msg' => 'error');
}
jsonExit($rs);
