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

//use PDOException;
use core\baseuser;
use core\db;
use core\dbexception;
use core\equipment;
use core\request;
use core\user;
use core\utils;

include_once(SITE_ROOT . '/vendor/phpmailer/class.phpmailer.php');

function sendEmailByPlaces($plid, $title, $txt) {
	try {
		$sql = <<<TXT
select
	userid uid,
	users.email email
from places_users
	inner join users on users.id = places_users.userid
where places_users.placesid = $plid
	and users.email <> ''
TXT;
		$rows = db::prepare($sql)->execute()->fetchAll();
		foreach ($rows as $row) {
			utils::smtpmail($row['email'], $title, $txt);
		}
	} catch (PDOException $ex) {
		throw new dbexception('Ошибка SendEmailByPlaces', 0, $ex);
	}
}

$req = request::getInstance();
$step = $req->get('step');
$sorgid = $req->get('sorgid');
$splaces = $req->get('splaces');
$suserid = $req->get('suserid');

// Выполняем только при наличии у пользователя соответствующей роли
// http://грибовы.рф/wiki/doku.php/основы:доступ:роли

$user = user::getInstance();
if (($user->isAdmin() || $user->testRights([1, 4, 5, 6])) && ($step != '')) {
	if ($step != 'move') {
		$dtpost = utils::DateToMySQLDateTime2($req->get('dtpost') . ' 00:00:00');
		$dtendgar = utils::DateToMySQLDateTime2($req->get('dtendgar') . ' 00:00:00');
		if ($dtpost == '') {
			$err[] = 'Не выбрана дата!';
		}
		if ($sorgid == '') {
			$err[] = 'Не выбрана организация!';
		}
		if ($splaces == '') {
			$err[] = 'Не выбрано помещение!';
		}
		if ($suserid == '') {
			$err[] = 'Не выбран пользователь!';
		}
		$sgroupname = $req->get('sgroupname');
		if ($sgroupname == '') {
			$err[] = 'Не выбрана группа номенклатуры!';
		}
		$svendid = $req->get('svendid');
		if ($svendid == '') {
			$err[] = 'Не выбран производитель!';
		}
		$snomeid = $req->get('snomeid');
		if ($snomeid == '') {
			$err[] = 'Не выбрана номенклатура!';
		}
		$kntid = $req->get('kntid');
		if ($kntid == '') {
			$err[] = 'Не выбран поставщик!';
		}
		$os = $req->get('os', '0');
		$mode = $req->get('mode', '0');
		$mapyet = $req->get('mapyet', '0');
		$buhname = $req->get('buhname');
		$sernum = $req->get('sernum');
		$invnum = $req->get('invnum');
		$shtrihkod = $req->get('shtrihkod');
		$cost = $req->get('cost');
		$picphoto = $req->get('picname');
		$currentcost = $req->get('currentcost');
		$comment = $req->get('comment');
		$ip = $req->get('ip');
	} else {
		if ($sorgid == '') {
			$err[] = 'Не выбрана организация!';
		}
		if ($splaces == '') {
			$err[] = 'Не выбрано помещение!';
		}
		if ($suserid == '') {
			$err[] = 'Не выбран пользователь!';
		}
		if (isset($_POST['tmcgo'])) {
			$tmcgo = ($_POST['tmcgo'] == 'on') ? '1' : '0';
		} else {
			$tmcgo = '0';
		}
		$comment = $req->get('comment');
	}

	if ($step == 'add') {
		if (count($err) == 0) {
			try {
				$sql = <<<TXT
insert into equipment (orgid, placesid, usersid, nomeid, buhname, datepost, cost, currentcost, sernum, invnum, shtrihkod, os, mode, comment,
	active, ip, mapyet, photo, kntid, dtendgar, repair, mapx, mapy, mapmoved)
values (:sorgid, :splaces, :suserid, :snomeid, :buhname, :dtpost, :cost, :currentcost, :sernum, :invnum, :shtrihkod, :os, :mode, :comment,
	'1', :ip, :mapyet, :picphoto, :kntid, :dtendgar, 0, '', '', 0)
TXT;
				db::prepare($sql)->execute([
					':sorgid' => $sorgid,
					':splaces' => $splaces,
					':suserid' => $suserid,
					':snomeid' => $snomeid,
					':buhname' => $buhname,
					':dtpost' => $dtpost,
					':cost' => $cost,
					':currentcost' => $currentcost,
					':sernum' => $sernum,
					':invnum' => $invnum,
					':shtrihkod' => $shtrihkod,
					':os' => $os,
					':mode' => $mode,
					':comment' => $comment,
					':ip' => $ip,
					':mapyet' => $mapyet,
					':picphoto' => $picphoto,
					':kntid' => $kntid,
					':dtendgar' => $dtendgar
				]);
			} catch (PDOException $ex) {
				throw new dbexception('Не смог добавить номенклатуру', 0, $ex);
			}
			if ($cfg->sendemail == 1) {
				// $txt="Внимание! На Вашу ответственность переведена новая единица ТМЦ. <a href=$url?content_page=eq_list&usid=$suserid>Подробности здесь.</a>";
				// smtpmail("$touser->email","Уведомление о перемещении ТМЦ",$txt);
				// SendEmailByPlaces($splaces,"Изменился состав ТМЦ в помещении","Внимание! В закрепленном за вами помещении изменился состав ТМЦ. <a href=$url?content_page=eq_list>Подробнее здесь.</a>");
			}
		}
	}

	if ($step == 'edit') {
		if (count($err) == 0) {
			$id = $req->get('id');
			try {
				$sql = <<<TXT
update equipment
set usersid = :usersid,
	nomeid = :nomeid,
	buhname = :buhname,
	datepost = :datepost,
	cost = :cost,
	currentcost = :currentcost,
	sernum = :sernum,
	invnum = :invnum,
	shtrihkod = :shtrihkod,
	os = :os,
	mode = :mode,
	comment = :comment,
	photo = :photo,
	ip = :ip,
	mapyet = :mapyet,
	kntid = :kntid,
	dtendgar = :dtendgar
where id = :id
TXT;
				db::prepare($sql)->execute([
					':usersid' => $suserid,
					':nomeid' => $snomeid,
					':buhname' => $buhname,
					':datepost' => $dtpost,
					':cost' => $cost,
					':currentcost' => $currentcost,
					':sernum' => $sernum,
					':invnum' => $invnum,
					':shtrihkod' => $shtrihkod,
					':os' => $os,
					':mode' => $mode,
					':comment' => $comment,
					':photo' => $picphoto,
					':ip' => $ip,
					':mapyet' => $mapyet,
					':kntid' => $kntid,
					':dtendgar' => $dtendgar,
					':id' => $id
				]);
			} catch (PDOException $ex) {
				throw new dbexception('Не смог изменить номенклатуру', 0, $ex);
			}
		}
	}

	if ($step == 'move') {
		if (count($err) == 0) {
			$id = $req->get('id');
			$etmc = new equipment();
			$etmc->getById($id);
			try {
				$sql = "update equipment set tmcgo = :tmcgo, mapmoved = 1, orgid = :sorgid, placesid = :splaces, usersid = :suserid where id = :id";
				db::prepare($sql)->execute([
					':tmcgo' => $tmcgo,
					':sorgid' => $sorgid,
					':splaces' => $splaces,
					':suserid' => $suserid,
					':id' => $id
				]);
			} catch (PDOException $ex) {
				throw new dbexception('Не смог изменить регистр номенклатуры - перемещение', 0, $ex);
			}
			try {
				$sql = <<<TXT
insert into move (eqid, dt, orgidfrom, orgidto, placesidfrom, placesidto, useridfrom, useridto, comment)
values (:eqid, now(), :orgidfrom, :orgidto, :placesidfrom, :placesidto, :useridfrom, :useridto, :comment)
TXT;
				db::prepare($sql)->execute([
					':eqid' => $id,
					':orgidfrom' => $etmc->orgid,
					':orgidto' => $sorgid,
					':placesidfrom' => $etmc->placesid,
					':placesidto' => $splaces,
					':useridfrom' => $etmc->usersid,
					':useridto' => $suserid,
					':comment' => $comment
				]);
			} catch (PDOException $ex) {
				throw new dbexception('Не смог добавить перемещение', 0, $ex);
			}
			if ($cfg->sendemail == 1) {
				$touser = new baseuser();
				$touser->getById($suserid);
				$url = $cfg->urlsite;
				$tmcname = $etmc->tmcname;
				$txt = "Внимание! На Вашу ответственность переведена новая единица ТМЦ ($tmcname). <a href=$url/index.php?content_page=eq_list&usid=$suserid>Подробности здесь.</a>";
				utils::smtpmail($touser->email, 'Уведомление о перемещении ТМЦ', $txt);   // отсылаем уведомление кому пришло
				sendEmailByPlaces($etmc->placesid, 'Изменился состав ТМЦ в помещении', "Внимание! В закрепленном за вами помещении изменился состав ТМЦ. <a href=$url/index.php?content_page=eq_list>Подробнее здесь.</a>");
				sendEmailByPlaces($splaces, 'Изменился состав ТМЦ в помещении', "Внимание! В закрепленном за вами помещении изменился состав ТМЦ. <a href=$url/index.php?content_page=eq_list>Подробнее здесь.</a>");
				$touser->getById($etmc->usersid);
				$txt = "Внимание! С вашей отвественности снята единица ТМЦ ($tmcname). <a href=$url/index.php?content_page=eq_list&usid=$etmc->usersid>Подробности здесь.</a>";
				utils::smtpmail($touser->email, 'Уведомление о перемещении ТМЦ', $txt);
			}
		}
	}
}

if (count($err) == 0) {
	echo 'ok';
} else {
	echo "<script>$('#messenger').addClass('alert alert-danger');</script>";
	echo implode('<br>', $err);
}
