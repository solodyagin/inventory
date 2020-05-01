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

//use DOMDocument;
use core\db;
use core\dbexception;

// Создает XML-строку и XML-документ при помощи DOM
$dom = new DOMDocument('1.0', 'UTF-8');

$orguse = $dom->appendChild($dom->createElement('orguse'));
try {
	$sql = <<<TXT
select
	equipment.id as eqid,
	equipment.orgid as eqorgid,
	org.name as orgname,
	getvendorandgroup.vendorname as vname,
	getvendorandgroup.groupname as grnome,
	places.name as placesname,
	users.login as userslogin,
	getvendorandgroup.nomename as nomenamez,
	buhname,sernum,
	invnum,
	shtrihkod,
	datepost,
	cost,
	currentcost,
	os,
	equipment.mode as eqmode,
	equipment.comment as eqcomment,
	equipment.active as eqactive
from equipment
	inner join (
		select
			nome.id as nomeid,
			vendor.name as vendorname,
			group_nome.name as groupname,
			nome.name as nomename
		from nome
			inner join group_nome on nome.groupid = group_nome.id
			inner join vendor on nome.vendorid = vendor.id
	) as getvendorandgroup on getvendorandgroup.nomeid = equipment.nomeid
	inner join org on org.id = equipment.orgid
	inner join places on places.id = equipment.placesid
	inner join users on users.id = equipment.usersid
where equipment.active = 1
TXT;
	$rows = db::prepare($sql)->execute()->fetchAll();
	foreach ($rows as $row) {
		$orgtehnika = $orguse->appendChild($dom->createElement('orgtehnika'));
		$orgid = $orgtehnika->appendChild($dom->createElement('orgid'));
		$orgid->appendChild($dom->createTextNode("$row[eqorgid]"));
		$namehouses = $orgtehnika->appendChild($dom->createElement('namehouses'));
		$namehouses->appendChild($dom->createTextNode("$row[placesname]"));
		$nomename = $orgtehnika->appendChild($dom->createElement('nomename'));
		$nomename->appendChild($dom->createTextNode("$row[nomenamez]"));
		$buhname = $orgtehnika->appendChild($dom->createElement('buhname'));
		$buhname->appendChild($dom->createTextNode("$row[buhname]"));
		$invnum = $orgtehnika->appendChild($dom->createElement('invnum'));
		$invnum->appendChild($dom->createTextNode("$row[invnum]"));
		$shtrihkod = $orgtehnika->appendChild($dom->createElement('shtrihkod'));
		$shtrihkod->appendChild($dom->createTextNode("$row[shtrihkod]"));
		$spisano = $orgtehnika->appendChild($dom->createElement('spisano'));
		$spisano->appendChild($dom->createTextNode("$row[eqmode]"));
		$os = $orgtehnika->appendChild($dom->createElement('os'));
		$os->appendChild($dom->createTextNode("$row[os]"));
	}
} catch (PDOException $ex) {
	throw new dbexception('Не получилось выбрать список оргтехники', 0, $ex);
}

$dom->formatOutput = true; // установка атрибута formatOutput

$content = $dom->saveXML(); // передача строки
if (!$content) {
	exit('Нечего сохранять');
}

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename=export.xml');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . strlen($content));
echo $content;
