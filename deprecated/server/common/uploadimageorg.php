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

function cuttingimg($zoom, $fn, $sz) {
	@mkdir(SITE_ROOT . '/photos/maps');
	$img = imagecreatefrompng(SITE_ROOT . '/photos/maps/0-0-0-' . $fn);  // получаем идентификатор загруженного изрбражения которое будем резать
	$info = getimagesize(SITE_ROOT . '/photos/maps/0-0-0-' . $fn);  // получаем в массив информацию об изображении
	$w = $info[0];
	$h = $info[1]; // ширина и высота исходного изображения
	$sx = round($w / $sz, 0);  // длина куска изображения
	$sy = round($h / $sz, 0);  // высота куска изображения
	$px = 0;
	$py = 0; // координаты шага "реза"
	for ($y = 0; $y <= $sz; $y++) {
		for ($x = 0; $x <= $sz; $x++) {
			$imgcropped = imagecreatetruecolor($sx, $sy);
			imagecopy($imgcropped, $img, 0, 0, $px, $py, $sx, $sy);
			imagepng($imgcropped, SITE_ROOT . '/photos/maps/' . $zoom . '-' . $y . '-' . $x . '-' . $fn);
			$px = $px + $sx;
		}
		$px = 0;
		$py = $py + $sy;
	}
}

$geteqid = PostDef('geteqid');
$uploaddir = SITE_ROOT . '/photos/maps/';

$userfile_name = strtoupper(basename($_FILES['filedata']['name']));
$len = strlen($userfile_name);
$ext_file = substr($userfile_name, $len - 4, $len);

if ($ext_file == '.PNG') {
	$tmp = getRandomDigits(20);
	$userfile_name = $tmp . $ext_file;
	$uploadfile = $uploaddir . '0-0-0-' . $userfile_name;

	$sr = $_FILES['filedata']['tmp_name'];
	$dest = $uploadfile;
	$rs = array('fname' => '', 'msg' => '');
	$res = move_uploaded_file($sr, $dest);
	if ($res) {
		$rs = array('fname' => "0-0-0-$userfile_name", 'msg' => '');
		if ($geteqid != '') {
			$sql = 'UPDATE org SET picmap = :userfile_name WHERE id = :geteqid';
			try {
				DB::prepare($sql)->execute(array(
					':userfile_name' => $userfile_name,
					':geteqid' => $geteqid
				));
			} catch (PDOException $ex) {
				throw new DBException('Не могу обновить фото', 0, $ex);
			}
			cuttingimg(1, $userfile_name, 2);
			cuttingimg(2, $userfile_name, 4);
			cuttingimg(3, $userfile_name, 8);
		} else {
			$rs = array('fname' => "0-0-0-$userfile_name", 'msg' => 'error org');
		}
	} else {
		$rs = array('fname' => "0-0-0-$userfile_name", 'msg' => 'error file load');
	}
} else {
	$rs = array('fname' => "0-0-0-$userfile_name", 'msg' => 'Файл не формата png');
}
jsonExit($rs);
