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

class Controller_News extends Controller {

	function index() {
		$user = User::getInstance();
		$data['section'] = 'Журналы / Новости';
		if ($user->isAdmin() || $user->TestRights([1])) {
			$this->view->renderTemplate('news/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

	/** Добавление новости через jQuery UI диалог */
	function add() {
		global $err;
		$user = User::getInstance();
		if ($user->isAdmin()) {
			$dtpost = DateToMySQLDateTime2(PostDef('dtpost'));
			if ($dtpost == '') {
				$err[] = 'Не введена дата!';
			}
			$title = PostDef('title');
			if ($title == '') {
				$err[] = 'Не задан заголовок!';
			}
			$txt = PostDef('txt');
			if ($txt == '') {
				$err[] = 'Нет текста новости!';
			}
			if (count($err) == 0) {
				$sql = 'insert into news (dt, title, body) values (:dtpost, :title, :txt)';
				try {
					DB::prepare($sql)->execute([':dtpost' => $dtpost, ':title' => $title, ':txt' => $txt]);
				} catch (PDOException $ex) {
					throw new DBException('Не смог добавить новость!', 0, $ex);
				}
			}
		}
		$this->index();
	}

	/** Редактирование новости через jQuery UI диалог */
	function edit() {
		global $err;
		$user = User::getInstance();
		if ($user->isAdmin()) {
			$dtpost = DateToMySQLDateTime2(PostDef('dtpost'));
			if ($dtpost == '') {
				$err[] = 'Не введена дата!';
			}
			$title = PostDef('title');
			if ($title == '') {
				$err[] = 'Не задан заголовок!';
			}
			$txt = PostDef('txt');
			if ($txt == '') {
				$err[] = 'Нет текста новости!';
			}
			if (count($err) == 0) {
				$newsid = GetDef('newsid');
				if ($newsid != '') {
					$sql = 'update news set dt = :dtpost, title = :title, body = :txt where id = :newsid';
					try {
						DB::prepare($sql)->execute([':dtpost' => $dtpost, ':title' => $title, ':txt' => $txt, ':newsid' => $newsid]);
					} catch (PDOException $ex) {
						throw new DBException('Не смог отредактировать новость!', 0, $ex);
					}
				}
			}
		}
		$this->index();
	}

	/** Получение новости из виджета на главной странице */
	function read() {
		$newsid = (isset($_GET['id'])) ? $_GET['id'] : '1';
		$data = ['news_dt' => '', 'news_title' => '', 'news_body' => ''];
		if ($newsid != '') {
			$sql = 'select * from news where id=:newsid';
			try {
				$row = DB::prepare($sql)->execute([':newsid' => $newsid])->fetch();
				if ($row) {
					$data['news_dt'] = $row['dt'];
					$data['news_title'] = $row['title'];
					$data['news_body'] = $row['body'];
				}
			} catch (PDOException $ex) {
				throw new DBException('Не могу выбрать список новостей!', 0, $ex);
			}
		}
		$this->view->renderTemplate('news/read', $data);
	}

	/** Получение списка новостей из виджета на главной странице */
	function getnews() {
		$num = filter_input(INPUT_POST, 'num', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
		$rz = 0;
		try {
			switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
				case 'mysql':
					$sql = 'select * from news order by dt desc limit :num, 4';
					break;
				case 'pgsql':
					$sql = 'select * from news order by dt desc offset :num limit 4';
					break;
			}
			$stmt = DB::prepare($sql);
			$stmt->bindValue(':num', (int) $num, PDO::PARAM_INT);
			$arr = $stmt->execute()->fetchAll();
			foreach ($arr as $row) {
				$dt = MySQLDateTimeToDateTimeNoTime($row['dt']);
				$title = $row['title'];
				echo '<h5><span class="label label-info">' . $dt . '</span> ' . $title . '</h5>';
				$pieces = explode('<!-- pagebreak -->', $row['body']);
				echo "<p>$pieces[0]</p>";
				if (isset($pieces[1])) {
					echo '<div align="right"><a href="news/read?id=' . $row['id'] . '">Читать дальше</a></div>';
				}
				$rz++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список новостей', 0, $ex);
		}
		if ($rz == 0) {
			echo '';
		}
	}

	/** Для работы jqGrid */
	function list() {
		$user = User::getInstance();
		// Проверяем: может ли пользователь просматривать?
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
		try {
			$sql = 'SELECT COUNT(*) AS cnt FROM news';
			$row = DB::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список новостей (1)', 0, $ex);
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
					$sql = "select * from news order by $sidx $sord limit $start, $limit";
					break;
				case 'pgsql':
					$sql = "select * from news order by $sidx $sord offset $start limit $limit";
					break;
			}
			$arr = DB::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$responce->rows[$i]['cell'] = [$row['id'], $row['dt'], $row['title'], $row['stiker']];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список новостей (2)', 0, $ex);
		}
		jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = User::getInstance();
		$oper = PostDef('oper');
		$id = PostDef('id');
		$title = PostDef('title');
		$stiker = PostDef('stiker');
		switch ($oper) {
			case 'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Для редактирования недостаточно прав');
				$sql = 'update news set title = :title, stiker = :stiker where id = :id';
				try {
					DB::prepare($sql)->execute([':title' => $title, ':stiker' => $stiker, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу обновить заголовок новости', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Для удаления недостаточно прав');
				$sql = 'delete from news where id = :id';
				try {
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу удалить новость', 0, $ex);
				}
				break;
		}
	}

}
