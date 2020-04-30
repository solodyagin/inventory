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

//namespace App\Controllers;
//use Core\Controller;
//use Core\Config;
//use Core\Router;
//use Core\User;
//use Core\DB;
//use \PDOException;
//use Core\DBException;

class Controller_Modules extends Controller {

	function index() {
		$user = User::getInstance();
		$data['section'] = 'Настройка / Подключенные модули';
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->renderTemplate('modules/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

	function list() {
		$user = User::getInstance();
		// Проверка: может ли пользователь просматривать?
		($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		$page = GetDef('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = GetDef('rows');
		$sidx = GetDef('sidx', '1');
		$sord = GetDef('sord');
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		$sql = "select count(*) as cnt from config_common where nameparam like 'modulename_%'";
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
		switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
			case 'mysql':
				$sql = <<<TXT
select	t1.id id,
		substr(t1.nameparam, 12) as name,
		t2.valueparam as comment,
		t3.valueparam as copy,
		t1.valueparam as active
from	config_common t1
	left join config_common t2
		on (substr(t2.nameparam, 15) = substr(t1.nameparam, 12) and
			t2.nameparam like "modulecomment_%")
	left join config_common t3
		on (substr(t3.nameparam, 12) = substr(t1.nameparam, 12) and
			t3.nameparam like "modulecopy_%")
where	t1.nameparam like "modulename_%"
order by $sidx $sord
limit	:start, :limit
TXT;
				break;
			case 'pgsql':
				$sql = <<<txt
select
  t1.id id,
  substr(t1.nameparam, 12) as name,
  t2.valueparam as comment,
  t3.valueparam as copy,
  t1.valueparam as active
from config_common t1
  left join config_common t2 on (substr(t2.nameparam, 15) = substr(t1.nameparam, 12) and t2.nameparam like 'modulecomment_%')
  left join config_common t3 on (substr(t3.nameparam, 12) = substr(t1.nameparam, 12) and t3.nameparam like 'modulecopy_%')
where t1.nameparam like 'modulename_%'
order by $sidx $sord
offset :start limit :limit
txt;
				break;
		}
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
				// Проверка: может ли пользователь редактировать?
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Недостаточно прав');
				$active = PostDef('active');
				$sql = 'update config_common set valueparam = :active where id = :id';
				try {
					DB::prepare($sql)->execute([':active' => $active, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу обновить данные по модулю', 0, $ex);
				}
				break;
			case 'del':
				// Проверка: может ли пользователь удалять?
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Недостаточно прав');
				$sql = 'select * from config_common where id = :id';
				try {
					$row = DB::prepare($sql)->execute([':id' => $id])->fetch();
					if ($row) {
						$modname = explode('_', $row['nameparam'])[1];
						DB::prepare("delete from config_common where nameparam like 'module%_$modname'")->execute();
					}
				} catch (PDOException $ex) {
					throw new DBException('Не могу удалить модуль', 0, $ex);
				}
				break;
		}
	}

}
