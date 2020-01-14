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

class Controller_Modules extends Controller {

	function index() {
		$user = User::getInstance();
		$cfg = Config::getInstance();
		$data['section'] = 'Настройка / Подключенные модули';
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->generate('modules/index', $cfg->theme, $data);
		} else {
			$this->view->generate('restricted', $cfg->theme, $data);
		}
	}

	function list() {
		$user = User::getInstance();
		/* Проверка: может ли пользователь просматривать? */
		($user->isAdmin() || $user->TestRights([1,3,4,5,6])) or die('Недостаточно прав');
		$page = GetDef('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = GetDef('rows');
		$sidx = GetDef('sidx', '1');
		$sord = GetDef('sord');
		/* Готовим ответ */
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		$sql = "SELECT COUNT(*) AS cnt FROM config_common WHERE nameparam LIKE 'modulename_%'";
		try {
			$row = DB::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список модулей (1)', 0, $ex);
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
		$sql = <<<TXT
SELECT	t1.id `id`,
		SUBSTR(t1.nameparam, 12) AS `name`,
		t2.valueparam `comment`,
		t3.valueparam `copy`,
		t1.valueparam `active`
FROM	config_common t1
	LEFT JOIN config_common t2
		ON (SUBSTR(t2.nameparam, 15) = SUBSTR(t1.nameparam, 12) AND
			t2.nameparam LIKE "modulecomment_%")
	LEFT JOIN config_common t3
		ON (SUBSTR(t3.nameparam, 12) = SUBSTR(t1.nameparam, 12) AND
			t3.nameparam LIKE "modulecopy_%")
WHERE	t1.nameparam LIKE "modulename_%"
ORDER BY $sidx $sord
LIMIT	:start, :limit
TXT;
		try {
			$stmt = DB::prepare($sql);
			$stmt->bindValue(':start', (int) $start, PDO::PARAM_INT);
			$stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
			$arr = $stmt->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$responce->rows[$i]['cell'] = [$row['id'], $row['name'], $row['comment'], $row['copy'], $row['active']];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список модулей (2)', 0, $ex);
		}
		jsonExit($responce);
	}

	function change() {
		$user = User::getInstance();
		$oper = PostDef('oper');
		$id = PostDef('id');
		switch ($oper) {
			case 'edit':
				/* Проверка: может ли пользователь редактировать? */
				($user->isAdmin() || $user->TestRights([1,5])) or die('Недостаточно прав');
				$active = PostDef('active');
				$sql = 'UPDATE config_common SET valueparam = :active WHERE id = :id';
				try {
					DB::prepare($sql)->execute([':active' => $active, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу обновить данные по модулю', 0, $ex);
				}
				break;
			case 'del':
				/* Проверка: может ли пользователь удалять? */
				($user->isAdmin() || $user->TestRights([1,6])) or die('Недостаточно прав');
				$sql = 'SELECT * FROM config_common WHERE id = :id';
				try {
					$row = DB::prepare($sql)->execute([':id' => $id])->fetch();
					if ($row) {
						$modname = explode('_', $row['nameparam'])[1];
						DB::prepare("DELETE FROM config_common WHERE nameparam LIKE 'module%_$modname'")->execute();
					}
				} catch (PDOException $ex) {
					throw new DBException('Не могу удалить модуль', 0, $ex);
				}
				break;
		}
	}

}
