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

class Tequipment {

	var $id; // уникальный идентификатор
	var $orgid; // какой организации принадлежит
	var $placesid; // в каком помещении
	var $usersid; // какому пользователю принадлежит
	var $nomeid; // связь со справочником номенклатуры
	var $tmcname; // наименование ТМЦ из справочника номенклатуры
	var $buhname; // имя по "бухгалтерии"
	var $datepost; // дата прихода
	var $cost; // стоимость прихода
	var $currentcost; // текущая стоимость
	var $sernum; // серийный номер
	var $invnum; // инвентарный номер
	var $shtrihkod; // штрихкод
	var $os; // основные средства? 1 - да, 0 - нет
	var $mode; // списано? 1 - да, 0 - нет
	var $comment; // комментарий к ТМЦ
	var $photo; // файл с фото
	var $repair; // в ремонте? 1 - да, 0 - нет
	var $active; // помечено на удаление? 1 - да, 0 - нет
	var $ip; // IP адрес
	var $mapx; // координата Х на карте
	var $mapy; // координата У на карте
	var $mapmoved; // было перемещение? 1 - да, 0 - нет
	var $mapyet; // отображать на карте 1 - да, 0 - нет

	/**
	 * Обновляем профиль работника с текущими данными (все что заполнено)
	 * @param type $id
	 */

	function GetById($id) {
		$sql = <<<TXT
SELECT equipment.comment,equipment.mapyet,equipment.mapmoved,equipment.mapx,equipment.mapy,equipment.ip,equipment.photo,
       equipment.nomeid,getvendorandgroup.grnomeid,equipment.id AS eqid,equipment.orgid AS eqorgid,org.name AS orgname,
       getvendorandgroup.vendorname AS vname,getvendorandgroup.groupname AS grnome,places.id AS placesid,
       places.name AS placesname,users.login AS userslogin,users.id AS usersid,getvendorandgroup.nomename AS nomename,
       buhname,sernum,invnum,shtrihkod,datepost,cost,currentcost,os,equipment.mode AS eqmode,
       equipment.mapyet AS eqmapyet,equipment.comment AS eqcomment,equipment.active AS eqactive,
       equipment.repair AS eqrepair
FROM   equipment
       INNER JOIN (SELECT nome.groupid AS grnomeid,nome.id AS nomeid,vendor.name AS vendorname,
                          group_nome.name AS groupname,
                                                      nome.name AS nomename
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
WHERE  equipment.id = :id
TXT;
		try {
			$row = DB::prepare($sql)->execute(array(':id' => $id))->fetch();
			if ($row) {
				$this->id = $row['eqid'];
				$this->orgid = $row['eqorgid'];
				$this->placesid = $row['placesid'];
				$this->usersid = $row['usersid'];
				$this->nomeid = $row['nomeid'];
				$this->buhname = $row['buhname'];
				$this->datepost = $row['datepost'];
				$this->cost = $row['cost'];
				$this->currentcost = $row['currentcost'];
				$this->sernum = $row['sernum'];
				$this->invnum = $row['invnum'];
				$this->shtrihkod = $row['shtrihkod'];
				$this->os = $row['os'];
				$this->mode = $row['eqmode'];
				$this->comment = $row['comment'];
				$this->photo = $row['photo'];
				$this->repair = $row['eqrepair'];
				$this->active = $row['eqactive'];
				$this->ip = $row['ip'];
				$this->mapx = $row['mapx'];
				$this->mapy = $row['mapy'];
				$this->mapmoved = $row['mapmoved'];
				$this->mapyet = $row['mapyet'];
				$this->tmcname = $row['nomename'];
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка выполнения Tequipment.GetById', 0, $ex);
		}
	}

}
