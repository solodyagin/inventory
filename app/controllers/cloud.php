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
use core\mod;

class cloud extends controller {

	function index() {
		$data['section'] = 'Хранилище документов';
		// Проверка: включен ли модуль "cloud"?
		$mod = new mod();
		$active = $mod->isActive('cloud');
		unset($mod);
		if (!$active) {
			$this->view->renderTemplate('disabled', $data);
			exit;
		}
		// Проверка: назначены ли права?
		$user = user::getInstance();
		if ($user->isAdmin() || $user->testRights([1, 3, 4, 6])) {
			$this->view->renderTemplate('cloud/index', $data);
		} else {
			$this->view->renderTemplate('restricted', $data);
		}
	}

	/**
	 * TODO: Добавить метод Model_Cloud->addFolder()
	 * @throws DBException
	 */
	function addfolder() {
		// Проверка: может ли пользователь добавлять?
		$user = user::getInstance();
		($user->isAdmin() || $user->testRights([1, 4])) or die('У вас не хватает прав на добавление!');
		$req = request::getInstance();
		$foldername = $req->get('foldername');
		if (!empty($foldername)) {
			$sql = 'insert into cloud_dirs (parent, name) values (0, :foldername)';
			try {
				db::prepare($sql)->execute([':foldername' => $foldername]);
			} catch (PDOException $ex) {
				throw new dbexception('Не могу добавить папку', 0, $ex);
			}
		}
	}

	/**
	 * TODO: Добавить метод Model_Cloud->delFolder()
	 * @throws DBException
	 */
	function delfolder() {
		// Проверка: может ли пользователь удалять?
		$user = user::getInstance();
		($user->isAdmin() || $user->testRights([1, 6])) or die('У вас не хватает прав на удаление!');
		$req = request::getInstance();
		$folderkey = $req->get('folderkey');
		if (!empty($folderkey)) {
			try {
				$sql = 'delete from cloud_dirs where id = :folderkey';
				db::prepare($sql)->execute([':folderkey' => $folderkey]);
			} catch (PDOException $ex) {
				throw new dbexception('Не могу удалить папку', 0, $ex);
			}
		}
	}

	function getTreeRecurse($key) {
		try {
			$sql = 'select * from cloud_dirs where parent = :key';
			$arr = db::prepare($sql)->execute([':key' => $key])->fetchAll();
			$pz = 0;
			foreach ($arr as $row) {
				$name = $row['name'];
				$key = $row['id'];
				echo '{"title":"' . $name . '","isFolder":true,"key":"' . $key . '","children":[';
				$this->getTreeRecurse($key);
				echo ']}';
				$pz++;
				if ($pz < count($arr)) {
					echo ',';
				}
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу прочитать папку', 0, $ex);
		}
	}

	/**
	 * TODO: Добавить метод Model_Cloud->getTree()
	 * @throws DBException
	 */
	function gettree() {
		$user = user::getInstance();
		($user->isAdmin() || $user->testRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		echo '[';
		// Получаем корневые папки
		try {
			$sql = 'select * from cloud_dirs where parent = 0';
			$rows = db::prepare($sql)->execute()->fetchAll();
			$pz = 0;
			foreach ($rows as $row) {
				$name = $row['name'];
				$key = $row['id'];
				echo '{"title":"' . $name . '","isFolder":true,"key":"' . $key . '","children":[';
				$this->getTreeRecurse($key);
				echo ']}';
				$pz++;
				if ($pz < count($rows)) {
					echo ',';
				}
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу прочитать папку', 0, $ex);
		}
		echo ']';
	}

	function download() {
		$user = user::getInstance();
		($user->isAdmin() || $user->testRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		$req = request::getInstance();
		$id = $req->get('id');
		is_numeric($id) or die('Переданы неправильные параметры');
		$filename = '';
		try {
			$sql = 'select * from cloud_files where id = :id';
			$row = db::prepare($sql)->execute([':id' => $id])->fetch();
			if ($row) {
				$filename = SITE_ROOT . '/files/' . $row['filename'];
			}
		} catch (PDOException $ex) {
			throw new dbexception('Ошибка получения файла из базы', 0, $ex);
		}
		(!empty($filename) && file_exists($filename) && is_file($filename)) or die('Файл не найден');
		// Органичение скорости скачивания - 10.0 MB/s
		$download_rate = 10.0;
		$size = filesize($filename);
		$name = rawurldecode($row['title']);
		// Decrease CPU usage extreme.
		@ob_end_clean();
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $name . '"');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Accept-Ranges: bytes');
		header('Cache-control: private');
		header('Pragma: private');
		// Multipart-download and resume-download.
		if (isset($_SERVER['HTTP_RANGE'])) {
			list($a, $range) = explode('=', $_SERVER['HTTP_RANGE']);
			str_replace($range, '-', $range);
			$size2 = $size - 1;
			$new_length = $size - $range;
			header('HTTP/1.1 206 Partial Content');
			header("Content-Length: $new_length");
			header("Content-Range: bytes $range$size2/$size");
		} else {
			$size2 = $size - 1;
			header("Content-Length: $size");
			header("Content-Range: bytes 0-$size2/$size");
		}
		$chunksize = round($download_rate * 1048576);
		// Flush content.
		flush();
		if ($fp = @fopen($filename, 'rb')) {
			flock($fp, LOCK_SH);
			if (isset($_SERVER['HTTP_RANGE'])) {
				fseek($fp, $range);
			}
			while (!feof($fp) and ( connection_status() == 0)) {
				echo fread($fp, $chunksize);
				// Flush the content to the browser.
				flush();
				// Decrease download speed.
				sleep(1);
			}
			flock($fp, LOCK_UN);
			fclose($fp);
		} else {
			//die('Невозможно открыть файл');
		}
	}

	function listfiles() {
		$user = user::getInstance();
		$req = request::getInstance();
		$page = $req->get('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = $req->get('rows');
		$sidx = $req->get('sidx', '1');
		$sord = $req->get('sord', '');
		$oper = $req->get('oper', '');
		$id = $req->get('id');
		$title = $req->get('title');
		$cloud_dirs_id = $req->get('cloud_dirs_id');
		if ($oper == '') {
			// Проверка: может ли пользователь просматривать?
			($user->isAdmin() || $user->testRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
			// Готовим ответ
			$responce = new stdClass();
			$responce->page = 0;
			$responce->total = 0;
			$responce->records = 0;
			try {
				$sql = 'select count(*) as cnt from cloud_files where cloud_dirs_id = :cloud_dirs_id';
				$row = db::prepare($sql)->execute([':cloud_dirs_id' => $cloud_dirs_id])->fetch();
				$count = ($row) ? $row['cnt'] : 0;
			} catch (PDOException $ex) {
				throw new dbexception('Не могу выбрать количество записей', 0, $ex);
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
						$sql = <<<TXT
select * from cloud_files
where cloud_dirs_id = :cloud_dirs_id
order by $sidx $sord
limit $start, $limit
TXT;
						break;
					case 'pgsql':
						$sql = <<<TXT
select
	id,
	cloud_dirs_id,
	title,
	filename,
	dt::timestamp(0),
	sz
from cloud_files
where cloud_dirs_id = :cloud_dirs_id
order by $sidx $sord
offset $start limit $limit
TXT;
						break;
				}
				$arr = db::prepare($sql)->execute([':cloud_dirs_id' => $cloud_dirs_id])->fetchAll();
				$i = 0;
				foreach ($arr as $row) {
					switch (pathinfo($row['filename'], PATHINFO_EXTENSION)) {
						case 'jpeg':
						case 'jpg':
						case 'png':
							$ico = 'fa-file-image';
							break;
						case 'xls':
						case 'ods':
							$ico = 'fa-file-excel';
							break;
						case 'doc':
						case 'odt':
							$ico = 'fa-file-word';
							break;
						case 'pdf':
							$ico = 'fa-file-pdf';
							break;
						default:
							$ico = 'fa-file';
					}
					$rowid = $row['id'];
					$responce->rows[$i]['id'] = $rowid;
					$responce->rows[$i]['cell'] = [
						$rowid,
						"<a target=\"_blank\" href=\"cloud/download?id=$rowid\"><i class=\"fas $ico\"></i></a>",
						$row['title'],
						$row['dt'],
						utils::humanSize($row['sz'])
					];
					$i++;
				}
			} catch (PDOException $ex) {
				throw new dbexception('Не могу выбрать список файлов', 0, $ex);
			}
			utils::jsonExit($responce);
		}

		if ($oper == 'edit') {
			// Проверка: может ли пользователь редактировать?
			($user->isAdmin() || $user->testRights([1, 5])) or die('Для редактирования не хватает прав!');
			try {
				$sql = 'update cloud_files set title = :title where id = :id';
				db::prepare($sql)->execute([':title' => $title, ':id' => $id]);
			} catch (PDOException $ex) {
				throw new dbexception('Не могу выполнить запрос', 0, $ex);
			}
			exit;
		}

		if ($oper == 'del') {
			// Проверка: может ли пользователь удалять?
			($user->isAdmin() || $user->testRights([1, 6])) or die('Для удаления не хватает прав!');
			try {
				$sql = 'delete from cloud_files where id = :id';
				db::prepare($sql)->execute([':id' => $id]);
			} catch (PDOException $ex) {
				throw new dbexception('Не могу выполнить запрос', 0, $ex);
			}
			exit;
		}
	}

	function movefolder() {
		$user = user::getInstance();
		// Проверяем: может ли пользователь редактировать?
		($user->isAdmin() || $user->testRights([1, 5])) or die('Для редактирования не хватает прав!');
		$req = request::getInstance();
		$nodekey = $req->get('nodekey');
		$srnodekey = $req->get('srnodekey');
		try {
			$sql = 'update cloud_dirs set parent = :nodekey where id = :srnodekey';
			db::prepare($sql)->execute([':nodekey' => $nodekey, ':srnodekey' => $srnodekey]);
		} catch (PDOException $ex) {
			throw new dbexception('Не могу обновить дерево папок', 0, $ex);
		}
	}

	function uploadfiles() {
		$user = user::getInstance();
		// Проверяем: может ли пользователь добавлять файлы?
		($user->isAdmin() || $user->testRights([1, 4])) or die('Недостаточно прав');
		$req = request::getInstance();
		$selectedkey = $req->get('selectedkey');
		$orig_file = $_FILES['filedata']['name'];
		$dis = ['.htaccess']; // Запрещённые для загрузки файлы
		$rs = ['msg' => 'error']; // Ответ по умолчанию, если пойдёт что-то не так
		if (!in_array($orig_file, $dis)) {
			$userfile_name = utils::guid() . '.' . pathinfo($orig_file, PATHINFO_EXTENSION);
			$src = $_FILES['filedata']['tmp_name'];
			$dst = SITE_ROOT . '/files/' . $userfile_name;
			$res = move_uploaded_file($src, $dst);
			if ($res) {
				$rs['msg'] = $userfile_name;
				$sz = filesize($dst);
				if ($selectedkey != '') {
					try {
						switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
							case 'mysql':
								$sql = <<<TXT
insert into cloud_files (id, cloud_dirs_id, title, filename, dt, sz)
values (null, :selectedkey, :orig_file, :userfile_name, now(), :sz)
TXT;
								break;
							case 'pgsql':
								$sql = <<<TXT
insert into cloud_files (cloud_dirs_id, title, filename, dt, sz)
values (:selectedkey, :orig_file, :userfile_name, now(), :sz)
TXT;
								break;
						}
						db::prepare($sql)->execute([
							':selectedkey' => $selectedkey, ':orig_file' => $orig_file, ':userfile_name' => $userfile_name, ':sz' => $sz
						]);
					} catch (PDOException $ex) {
						throw new dbexception('Не могу добавить файл', 0, $ex);
					}
				}
			}
		}
		utils::jsonExit($rs);
	}

}
