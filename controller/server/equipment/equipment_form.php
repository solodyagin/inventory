<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

include_once(WUO_ROOT . '/libs/class.phpmailer.php');

function SendEmailByPlaces($plid, $title, $txt) {
	$sql = <<<TXT
SELECT userid AS uid,users.email AS email
FROM   places_users
       INNER JOIN users
               ON users.id = places_users.userid
WHERE  places_users.placesid = $plid
       AND users.email <> ''
TXT;
	try {
		$arr = DB::prepare($sql)->execute()->fetchAll();
		foreach ($arr as $row) {
			smtpmail($row['email'], $title, $txt);
		}
	} catch (PDOException $ex) {
		throw new DBException('Ошибка SendEmailByPlaces', 0, $ex);
	}
}

$step = GetDef('step');
$sorgid = PostDef('sorgid');
$splaces = PostDef('splaces');
$suserid = PostDef('suserid');

// Выполняем только при наличии у пользователя соответствующей роли
// http://грибовы.рф/wiki/doku.php/основы:доступ:роли

if ((($user->mode == 1) || $user->TestRoles('1,4,5,6')) && ($step != '')) {
	if ($step != 'move') {
		$dtpost = DateToMySQLDateTime2(PostDef('dtpost') . ' 00:00:00');
		$dtendgar = DateToMySQLDateTime2(PostDef('dtendgar') . ' 00:00:00');
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
		$sgroupname = PostDef('sgroupname');
		if ($sgroupname == '') {
			$err[] = 'Не выбрана группа номенклатуры!';
		}
		$svendid = PostDef('svendid');
		if ($svendid == '') {
			$err[] = 'Не выбран производитель!';
		}
		$snomeid = PostDef('snomeid');
		if ($snomeid == '') {
			$err[] = 'Не выбрана номенклатура!';
		}
		$kntid = PostDef('kntid');
		if ($kntid == '') {
			$err[] = 'Не выбран поставщик!';
		}
		$os = PostDef('os', '0');
		$mode = PostDef('mode', '0');
		$mapyet = PostDef('mapyet', '0');
		$buhname = PostDef('buhname');
		$sernum = PostDef('sernum');
		$invnum = PostDef('invnum');
		$shtrihkod = PostDef('shtrihkod');
		$cost = PostDef('cost');
		$picphoto = PostDef('picname');
		$currentcost = PostDef('currentcost');
		$comment = PostDef('comment');
		$ip = PostDef('ip');
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
		$comment = PostDef('comment');
	}

	if ($step == 'add') {
		if (count($err) == 0) {
			$sql = <<<TXT
INSERT INTO equipment
            (id,orgid,placesid,usersid,nomeid,buhname,datepost,cost,currentcost,sernum,invnum,shtrihkod,os,mode,comment,
             active,ip,mapyet,photo,kntid,dtendgar,repair,mapx,mapy,mapmoved)
VALUES      (NULL,:sorgid,:splaces,:suserid,:snomeid,:buhname,:dtpost,:cost,:currentcost,:sernum,
             :invnum,:shtrihkod,:os,:mode,:comment,'1',:ip,:mapyet,:picphoto,:kntid,:dtendgar,0,'','',0)
TXT;
			try {
				DB::prepare($sql)->execute(array(
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
				));
			} catch (PDOException $ex) {
				throw new DBException('Не смог добавить номенклатуру', 0, $ex);
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
			$id = GetDef('id');
			$sql = <<<TXT
UPDATE equipment
SET    usersid = :usersid,
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
WHERE  id = :id
TXT;
			try {
				DB::prepare($sql)->execute(array(
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
				));
			} catch (PDOException $ex) {
				throw new DBException('Не смог изменить номенклатуру', 0, $ex);
			}
		}
	}

	if ($step == 'move') {
		if (count($err) == 0) {
			$id = GetDef('id');
			$etmc = new Equipment();
			$etmc->GetById($id);
			$sql = <<<TXT
UPDATE equipment
SET    tmcgo = :tmcgo, mapmoved = 1, orgid = :sorgid, placesid = :splaces, usersid = :suserid
WHERE  id = :id
TXT;
			try {
				DB::prepare($sql)->execute(array(
					':tmcgo' => $tmcgo,
					':sorgid' => $sorgid,
					':splaces' => $splaces,
					':suserid' => $suserid,
					':id' => $id
				));
			} catch (PDOException $ex) {
				throw new DBException('Не смог изменить регистр номенклатуры - перемещение', 0, $ex);
			}
			$sql = <<<TXT
INSERT INTO move
            (id, eqid, dt, orgidfrom, orgidto, placesidfrom, placesidto, useridfrom, useridto, comment)
VALUES      (NULL, :eqid, NOW(), :orgidfrom, :orgidto, :placesidfrom, :placesidto, :useridfrom, :useridto,
             :comment)
TXT;
			try {
				DB::prepare($sql)->execute(array(
					':eqid' => $id,
					':orgidfrom' => $etmc->orgid,
					':orgidto' => $sorgid,
					':placesidfrom' => $etmc->placesid,
					':placesidto' => $splaces,
					':useridfrom' => $etmc->usersid,
					':useridto' => $suserid,
					':comment' => $comment
				));
			} catch (PDOException $ex) {
				throw new DBException('Не смог добавить перемещение', 0, $ex);
			}
			if ($cfg->sendemail == 1) {
				$touser = new BaseUser();
				$touser->getById($suserid);
				$url = $cfg->urlsite;
				$tmcname = $etmc->tmcname;
				$txt = "Внимание! На Вашу ответственность переведена новая единица ТМЦ ($tmcname). <a href=$url/index.php?content_page=eq_list&usid=$suserid>Подробности здесь.</a>";
				smtpmail($touser->email, 'Уведомление о перемещении ТМЦ', $txt);   // отсылаем уведомление кому пришло
				SendEmailByPlaces($etmc->placesid, 'Изменился состав ТМЦ в помещении', "Внимание! В закрепленном за вами помещении изменился состав ТМЦ. <a href=$url/index.php?content_page=eq_list>Подробнее здесь.</a>");
				SendEmailByPlaces($splaces, 'Изменился состав ТМЦ в помещении', "Внимание! В закрепленном за вами помещении изменился состав ТМЦ. <a href=$url/index.php?content_page=eq_list>Подробнее здесь.</a>");
				$touser->getById($etmc->usersid);
				$txt = "Внимание! С вашей отвественности снята единица ТМЦ ($tmcname). <a href=$url/index.php?content_page=eq_list&usid=$etmc->usersid>Подробности здесь.</a>";
				smtpmail($touser->email, 'Уведомление о перемещении ТМЦ', $txt);
			}
		}
	}
}

if (count($err) == 0) {
	echo 'ok';
} else {
	echo "<script>$('#messenger').addClass('alert alert-danger');</script>";
	for ($i = 0; $i <= count($err); $i++) {
		echo "$err[$i]<br>";
	}
}
