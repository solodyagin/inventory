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

class Controller_MoveInfo extends Controller {

	/** Для работы jqGrid */
	function list() {
		$page = GetDef('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = GetDef('rows');
		$sidx = GetDef('sidx', '1');
		$sord = GetDef('sord');
		$eqid = GetDef('eqid');
		/* Если не задано ТМЦ по которому показываем перемещения, то тогда просто листаем последние */
		if ($eqid == '') {
			$where = '';
		} else {
			$where = "WHERE move.eqid = '$eqid'";
		}
		/* Готовим ответ */
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'SELECT COUNT(*) cnt FROM move';
			$row = DB::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException($ex->getMessage());
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
SELECT mv.id,
	mv.eqid,
	nome.name,
	mv.nomeid,
	mv.dt,
	mv.orgname1,
	org.name AS orgname2,
	mv.place1,
	places.name AS place2,
	mv.user1,
	users_profile.fio AS user2,
	move.comment AS comment
FROM move
	INNER JOIN (
		SELECT move.id,
			move.eqid,
			equipment.nomeid,
			move.dt AS dt,
			org.name AS orgname1,
			places.name AS place1,
			users_profile.fio AS user1
		FROM move
			INNER JOIN org ON org.id = orgidfrom
			INNER JOIN places ON places.id = placesidfrom
		INNER JOIN users_profile ON users_profile.usersid = useridfrom
		INNER JOIN equipment ON equipment.id = eqid
	) AS mv ON move.id = mv.id
	INNER JOIN org ON org.id = move.orgidto
	INNER JOIN places ON places.id = placesidto
	INNER JOIN users_profile ON users_profile.usersid = useridto
	INNER JOIN nome ON nome.id = mv.nomeid
$where
ORDER BY $sidx $sord
LIMIT $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
SELECT mv.id,
	mv.eqid,
	nome.name,
	mv.nomeid,
	mv.dt,
	mv.orgname1,
	org.name AS orgname2,
	mv.place1,
	places.name AS place2,
	mv.user1,
	users_profile.fio AS user2,
	move.comment AS comment
FROM move
	INNER JOIN (
		SELECT move.id,
			move.eqid,
			equipment.nomeid,
			move.dt AS dt,
			org.name AS orgname1,
			places.name AS place1,
			users_profile.fio AS user1
		FROM move
			INNER JOIN org ON org.id = orgidfrom
			INNER JOIN places ON places.id = placesidfrom
		INNER JOIN users_profile ON users_profile.usersid = useridfrom
		INNER JOIN equipment ON equipment.id = eqid
	) AS mv ON move.id = mv.id
	INNER JOIN org ON org.id = move.orgidto
	INNER JOIN places ON places.id = placesidto
	INNER JOIN users_profile ON users_profile.usersid = useridto
	INNER JOIN nome ON nome.id = mv.nomeid
$where
ORDER BY $sidx $sord
OFFSET $start LIMIT $limit
TXT;
					break;
			}
			$i = 0;
			$arr = DB::prepare($sql)->execute()->fetchAll();
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$responce->rows[$i]['cell'] = [
					$row['id'], $row['dt'],
					$row['orgname1'], $row['place1'], $row['user1'], $row['orgname2'],
					$row['place2'], $row['user2'], $row['name'], $row['comment']
				];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список перемещений', 0, $ex);
		}
		jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = User::getInstance();
		$oper = PostDef('oper');
		$comment = PostDef('comment');
		$id = PostDef('id');
		switch ($oper) {
			case'edit':
				/* Проверяем может ли пользователь редактировать? */
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Недостаточно прав');
				try {
					$sql = 'UPDATE move SET comment = :comment WHERE id = :id';
					DB::prepare($sql)->execute([':comment' => $comment, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу обновить комментарий', 0, $ex);
				}
				break;
			case 'del':
				/* Проверяем может ли пользователь удалять? */
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Недостаточно прав');
				try {
					$sql = 'DELETE FROM move WHERE id = :id';
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу удалить запись о перемещении', 0, $ex);
				}
				break;
		}
	}

}
