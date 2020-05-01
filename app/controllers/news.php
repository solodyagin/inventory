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

namespace app\controllers;

use PDO;
use PDOException;
use stdClass;
use core\controller;
use core\request;
use core\user;
use core\db;
use core\dbexception;
use core\utils;

class news extends controller {

	function index() {
		$user = user::getInstance();
		$data['section'] = 'Журналы / Новости';
		if ($user->isAdmin() || $user->testRights([1])) {
			$this->view->renderTemplate('news/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

	/** Добавление новости через jQuery UI диалог */
	function add() {
		global $err;
		$user = user::getInstance();
		if ($user->isAdmin()) {
			$req = request::getInstance();
			$dtpost = utils::DateToMySQLDateTime2($req->get('dtpost'));
			if ($dtpost == '') {
				$err[] = 'Не введена дата!';
			}
			$title = $req->get('title');
			if ($title == '') {
				$err[] = 'Не задан заголовок!';
			}
			$txt = $req->get('txt');
			if ($txt == '') {
				$err[] = 'Нет текста новости!';
			}
			if (count($err) == 0) {
				$sql = 'insert into news (dt, title, body) values (:dtpost, :title, :txt)';
				try {
					db::prepare($sql)->execute([':dtpost' => $dtpost, ':title' => $title, ':txt' => $txt]);
				} catch (PDOException $ex) {
					throw new dbexception('Не смог добавить новость!', 0, $ex);
				}
			}
		}
		$this->index();
	}

	/** Редактирование новости через jQuery UI диалог */
	function edit() {
		global $err;
		$user = user::getInstance();
		if ($user->isAdmin()) {
			$req = request::getInstance();
			$dtpost = utils::DateToMySQLDateTime2($req->get('dtpost'));
			if ($dtpost == '') {
				$err[] = 'Не введена дата!';
			}
			$title = $req->get('title');
			if ($title == '') {
				$err[] = 'Не задан заголовок!';
			}
			$txt = $req->get('txt');
			if ($txt == '') {
				$err[] = 'Нет текста новости!';
			}
			if (count($err) == 0) {
				$newsid = $req->get('newsid');
				if ($newsid != '') {
					$sql = 'update news set dt = :dtpost, title = :title, body = :txt where id = :newsid';
					try {
						db::prepare($sql)->execute([':dtpost' => $dtpost, ':title' => $title, ':txt' => $txt, ':newsid' => $newsid]);
					} catch (PDOException $ex) {
						throw new dbexception('Не смог отредактировать новость!', 0, $ex);
					}
				}
			}
		}
		$this->index();
	}

	/** Получение новости из виджета на главной странице */
	function read() {
		$req = request::getInstance();
		$newsid = $req->get('id', '1');
		$data = ['news_dt' => '', 'news_title' => '', 'news_body' => ''];
		if ($newsid != '') {
			try {
				$sql = 'select * from news where id = :newsid';
				$row = db::prepare($sql)->execute([':newsid' => $newsid])->fetch();
				if ($row) {
					$data['news_dt'] = $row['dt'];
					$data['news_title'] = $row['title'];
					$data['news_body'] = $row['body'];
				}
			} catch (PDOException $ex) {
				throw new dbexception('Не могу выбрать список новостей!', 0, $ex);
			}
		}
		$this->view->renderTemplate('news/read', $data);
	}

	/** Получение списка новостей из виджета на главной странице */
	function getnews() {
		$num = filter_input(INPUT_POST, 'num', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
		$rz = 0;
		try {
			switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
				case 'mysql':
					$sql = 'select * from news order by dt desc limit :num, 4';
					break;
				case 'pgsql':
					$sql = 'select * from news order by dt desc offset :num limit 4';
					break;
			}
			$stmt = db::prepare($sql);
			$stmt->bindValue(':num', (int) $num, PDO::PARAM_INT);
			$arr = $stmt->execute()->fetchAll();
			foreach ($arr as $row) {
				$dt = utils::MySQLDateTimeToDateTimeNoTime($row['dt']);
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
			throw new dbexception('Не могу выбрать список новостей', 0, $ex);
		}
		if ($rz == 0) {
			echo '';
		}
	}

	/** Для работы jqGrid */
	function list() {
		$user = user::getInstance();
		// Проверяем: может ли пользователь просматривать?
		($user->isAdmin() || $user->testRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		$req = request::getInstance();
		$page = $req->get('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = $req->get('rows');
		$sidx = $req->get('sidx', '1');
		$sord = $req->get('sord');
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'SELECT COUNT(*) AS cnt FROM news';
			$row = db::prepare($sql)->execute()->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список новостей (1)', 0, $ex);
		}
		if ($count == 0) {
			utils::jsonExit($responce);
		}
		$total_pages = ceil($count / $limit);
		if ($page > $total_pages) {
			$page = $total_pages;
		}
		$start = $limit * $page - $limit;
		if ($start < 0) {
			utils::jsonExit($responce);
		}
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		try {
			switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
				case 'mysql':
					$sql = "select * from news order by $sidx $sord limit $start, $limit";
					break;
				case 'pgsql':
					$sql = "select * from news order by $sidx $sord offset $start limit $limit";
					break;
			}
			$arr = db::prepare($sql)->execute()->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$responce->rows[$i]['id'] = $row['id'];
				$responce->rows[$i]['cell'] = [$row['id'], $row['dt'], $row['title'], $row['stiker']];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать список новостей (2)', 0, $ex);
		}
		utils::jsonExit($responce);
	}

	/** Для работы jqGrid (editurl) */
	function change() {
		$user = user::getInstance();
		$req = request::getInstance();
		$oper = $req->get('oper');
		$id = $req->get('id');
		$title = $req->get('title');
		$stiker = $req->get('stiker');
		switch ($oper) {
			case 'edit':
				// Проверяем: может ли пользователь редактировать?
				($user->isAdmin() || $user->testRights([1, 5])) or die('Для редактирования недостаточно прав');
				$sql = 'update news set title = :title, stiker = :stiker where id = :id';
				try {
					db::prepare($sql)->execute([':title' => $title, ':stiker' => $stiker, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу обновить заголовок новости', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->testRights([1, 6])) or die('Для удаления недостаточно прав');
				$sql = 'delete from news where id = :id';
				try {
					db::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new dbexception('Не могу удалить новость', 0, $ex);
				}
				break;
		}
	}

}
