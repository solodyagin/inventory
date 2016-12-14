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

// Создает XML-строку и XML-документ при помощи DOM
$dom = new DomDocument('1.0', 'UTF-8');

$orguse = $dom->appendChild($dom->createElement('orguse'));

$sql = <<<TXT
SELECT equipment.id AS eqid,equipment.orgid AS eqorgid,org.name AS orgname,getvendorandgroup.vendorname AS vname,
       getvendorandgroup.groupname AS grnome,places.name AS placesname,users.login AS userslogin,
       getvendorandgroup.nomename AS nomenamez,buhname,sernum,invnum,shtrihkod,datepost,cost,currentcost,os,
       equipment.mode AS eqmode,equipment.comment AS eqcomment,equipment.active AS eqactive
FROM   equipment
       INNER JOIN (SELECT nome.id AS nomeid,vendor.name AS vendorname,group_nome.name AS groupname,nome.name AS nomename
                   FROM   nome
                          INNER JOIN group_nome
                                  ON nome.groupid = group_nome.id
                          INNER JOIN vendor
                                  ON nome.vendorid = vendor.id) AS getvendorandgroup
               ON getvendorandgroup.nomeid = equipment.nomeid
       INNER JOIN org
               ON org.id = equipment.orgid
       INNER JOIN places
               ON places.id = equipment.placesid
       INNER JOIN users
               ON users.id = equipment.usersid
WHERE  equipment.active = 1
TXT;

try {
	$arr = DB::prepare($sql)->execute()->fetchAll();
	foreach ($arr as $row) {
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
	throw new DBException('Не получилось выбрать список оргтехники', 0, $ex);
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
