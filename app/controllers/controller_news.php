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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

class Controller_News extends Controller {

	function index() {
		$cfg = Config::getInstance();
		$this->view->generate('view_news', $cfg->theme);
	}

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
				$sql = 'INSERT INTO news (id, dt, title, body) VALUES (NULL, :dtpost, :title, :txt)';
				try {
					DB::prepare($sql)->execute(array(
							':dtpost' => $dtpost,
							':title' => $title,
							':txt' => $txt
					));
				} catch (PDOException $ex) {
					throw new DBException('Не смог добавить новость!', 0, $ex);
				}
			}
		}
		$this->index();
	}

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
					$sql = 'UPDATE news SET dt = :dtpost, title= :title, body= :txt WHERE id = :newsid';
					try {
						DB::prepare($sql)->execute(array(
								':dtpost' => $dtpost,
								':title' => $title,
								':txt' => $txt,
								':newsid' => $newsid
						));
					} catch (PDOException $ex) {
						throw new DBException('Не смог отредактировать новость!', 0, $ex);
					}
				}
			}
		}
		$this->index();
	}

	function read() {
		$newsid = (isset($_GET['id'])) ? $_GET['id'] : '1';

		$data = array('news_dt' => '', 'news_title' => '', 'news_body' => '');

		if ($newsid != '') {
			$sql = 'SELECT * FROM news WHERE id= :newsid';
			try {
				$row = DB::prepare($sql)->execute(array(':newsid' => $newsid))->fetch();
				if ($row) {
					$data['news_dt'] = $row['dt'];
					$data['news_title'] = $row['title'];
					$data['news_body'] = $row['body'];
				}
			} catch (PDOException $ex) {
				throw new DBException('Не могу выбрать список новостей!', 0, $ex);
			}
		}

		$cfg = Config::getInstance();
		$this->view->generate('view_newsread', $cfg->theme, $data);
	}

	function getnews() {
		$num = filter_input(INPUT_POST, 'num', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
		$rz = 0;
		$sql = "SELECT * FROM news ORDER BY dt DESC LIMIT :num, 4";
		try {
			$stmt = DB::prepare($sql);
			$stmt->bindValue(':num', (int) $num, PDO::PARAM_INT);
			$arr = $stmt->execute()->fetchAll();
			foreach ($arr as $row) {
				$dt = MySQLDateTimeToDateTimeNoTime($row['dt']);
				$title = $row['title'];
				echo '<span class="label label-info">' . $dt . '</span><h5>' . $title . '</h5>';
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
			echo 'error';
		}
	}

}
