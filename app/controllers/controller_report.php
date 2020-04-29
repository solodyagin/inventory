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

class Controller_Report extends Controller {

	function index() {
		$cfg = Config::getInstance();
		$user = User::getInstance();
		$data['section'] = 'Отчёты / Имущество';
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->generate('report/index', $cfg->theme, $data);
		} else {
			$this->view->generate('restricted', $cfg->theme, $data);
		}
	}

	/** Для работы jqGrid */
	function list() {
		$page = GetDef('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = GetDef('rows');
		$sidx = GetDef('sidx', '1');
		$sord = GetDef('sord');
		$curuserid = GetDef('curuserid');
		$curplid = GetDef('curplid');
		$curorgid = GetDef('curorgid');
		$tpo = GetDef('tpo');
		$os = GetDef('os');
		$repair = GetDef('repair');
		$mode = GetDef('mode');
		$where = '';
		if ($curuserid != '-1') {
			$where .= " AND equipment.usersid = '$curuserid'";
		}
		if ($curplid != '-1') {
			$where .= " AND equipment.placesid = '$curplid'";
		}
		if ($curorgid != '-1') {
			$where .= " AND equipment.orgid = '$curorgid'";
		}
		if ($os == 'true') {
			$where .= " AND equipment.os = 1";
		}
		if ($repair == 'true') {
			$where .= " AND equipment.repair = 1";
		}
		if ($mode == 'true') {
			$where .= " AND equipment.mode = 1";
		}
		if ($tpo == '2') {
			$where .= " AND equipment.mode = 0  AND equipment.os = 0";
		}
		/* Готовим ответ */
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = <<<TXT
SELECT
	places.name AS plname,
	res.*
FROM places
	INNER JOIN (
		SELECT
			name AS namenome,
			eq.*
		FROM nome
			INNER JOIN (
				SELECT
					equipment.id AS eqid,
					equipment.placesid AS plid,
					equipment.nomeid AS nid,
					equipment.buhname AS bn,
					equipment.cost AS cs,
					equipment.currentcost AS curc,
					equipment.invnum,
					equipment.sernum,
					equipment.shtrihkod,
					equipment.mode,
					equipment.os
				FROM equipment
				WHERE equipment.active = 1 $where
			) AS eq ON nome.id = eq.nid
	) AS res ON places.id = res.plid
TXT;
			$rows = DB::prepare($sql)->execute()->fetchAll();
			$count = ($rows) ? count($rows) : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу сформировать список по оргтехнике/помещениям/пользователю!(1)', 0, $ex);
		}
		if ($count == 0) {
			jsonExit($responce);
		}
		$total_pages = ceil($count / $limit);
		if ($page > $total_pages) {
			$page = $total_pages;
		}
		$start = $limit * $page - $limit;
		if ($start < 0) {
			jsonExit($responce);
		}
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		try {
			switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
				case 'mysql':
					$sql = <<<TXT
SELECT
	name AS grname,
	res2.*
FROM group_nome
	INNER JOIN (
		SELECT
			places.name AS plname,
            res.*
			FROM places
				INNER JOIN (
					SELECT
						name AS namenome,
						nome.groupid AS grpid,
						eq.*
					FROM nome
						INNER JOIN (
							SELECT
								equipment.id AS eqid,
								equipment.placesid AS plid,
								equipment.nomeid AS nid,
								equipment.buhname AS bn,
								equipment.cost AS cs,
								equipment.currentcost AS curc,
								equipment.invnum,
								equipment.sernum,
								equipment.shtrihkod,
								equipment.mode,
								equipment.os
							FROM equipment
							WHERE equipment.active = 1 $where
						) AS eq ON nome.id = eq.nid
				) AS res ON places.id = res.plid
			) AS res2 ON group_nome.id = res2.grpid
ORDER BY $sidx $sord
LIMIT $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
SELECT
	name AS grname,
	res2.*
FROM group_nome
	INNER JOIN (
		SELECT
			places.name AS plname,
            res.*
			FROM places
				INNER JOIN (
					SELECT
						name AS namenome,
						nome.groupid AS grpid,
						eq.*
					FROM nome
						INNER JOIN (
							SELECT
								equipment.id AS eqid,
								equipment.placesid AS plid,
								equipment.nomeid AS nid,
								equipment.buhname AS bn,
								equipment.cost AS cs,
								equipment.currentcost AS curc,
								equipment.invnum,
								equipment.sernum,
								equipment.shtrihkod,
								equipment.mode,
								equipment.os
							FROM equipment
							WHERE equipment.active = 1 $where
						) AS eq ON nome.id = eq.nid
				) AS res ON places.id = res.plid
			) AS res2 ON group_nome.id = res2.grpid
ORDER BY $sidx $sord
OFFSET $start LIMIT $limit
TXT;
					break;
			}
			$arr = DB::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['eqid'];
				$responce->rows[$i]['cell'] = [
					$row['eqid'], $row['plname'], $row['namenome'], $row['grname'], $row['invnum'], $row['sernum'], $row['shtrihkod'], $row['mode'], $row['os'], $row['bn'],
					$row['cs'], $row['curc']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу сформировать список по оргтехнике/помещениям/пользователю! (2)', 0, $ex);
		}
		jsonExit($responce);
	}

}
