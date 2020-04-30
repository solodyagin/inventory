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
//use Core\Mod;

class Controller_Cloud extends Controller {

	function index() {
		$user = User::getInstance();
		$data['section'] = 'Хранилище документов';
		// Проверка: включен ли модуль "cloud"?
		$mod = new Mod();
		$active = $mod->IsActive('cloud');
		unset($mod);
		if (!$active) {
			$this->view->renderTemplate('disabled', $data);
			exit;
		}
		// Проверка: назначены ли права?
		if ($user->isAdmin() || $user->TestRights([1, 3, 4, 6])) {
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
		$user = User::getInstance();
		// Проверка: может ли пользователь добавлять?
		($user->isAdmin() || $user->TestRights([1, 4])) or die('У вас не хватает прав на добавление!');
		$foldername = (isset(Router::$params['foldername'])) ? Router::$params['foldername'] : '';
		if (!empty($foldername)) {
			$sql = 'insert into cloud_dirs (parent, name) values (0, :foldername)';
			try {
				DB::prepare($sql)->execute([':foldername' => $foldername]);
			} catch (PDOException $ex) {
				throw new DBException('Не могу добавить папку', 0, $ex);
			}
		}
	}

	/**
	 * TODO: Добавить метод Model_Cloud->delFolder()
	 * @throws DBException
	 */
	function delfolder() {
		$user = User::getInstance();
		// Проверка: может ли пользователь удалять?
		($user->isAdmin() || $user->TestRights([1, 6])) or die('У вас не хватает прав на удаление!');
		$folderkey = (isset(Router::$params['folderkey'])) ? Router::$params['folderkey'] : '';
		if (!empty($folderkey)) {
			$sql = 'delete from cloud_dirs where id = :folderkey';
			try {
				DB::prepare($sql)->execute([':folderkey' => $folderkey]);
			} catch (PDOException $ex) {
				throw new DBException('Не могу удалить папку', 0, $ex);
			}
		}
	}

	function GetTreeRecurse($key) {
		try {
			$sql = 'select * from cloud_dirs where parent = :key';
			$arr = DB::prepare($sql)->execute([':key' => $key])->fetchAll();
			$pz = 0;
			foreach ($arr as $row) {
				$name = $row['name'];
				$key = $row['id'];
				echo '{"title":"' . $name . '","isFolder":true,"key":"' . $key . '","children":[';
				$this->GetTreeRecurse($key);
				echo ']}';
				$pz++;
				if ($pz < count($arr)) {
					echo ',';
				}
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу прочитать папку', 0, $ex);
		}
	}

	/**
	 * TODO: Добавить метод Model_Cloud->getTree()
	 * @throws DBException
	 */
	function gettree() {
		$user = User::getInstance();
		($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		echo '[';
		// Получаем корневые папки
		$sql = 'select * from cloud_dirs where parent = 0';
		try {
			$arr = DB::prepare($sql)->execute()->fetchAll();
			$pz = 0;
			foreach ($arr as $row) {
				$name = $row['name'];
				$key = $row['id'];
				echo '{"title":"' . $name . '","isFolder":true,"key":"' . $key . '","children":[';
				$this->GetTreeRecurse($key);
				echo ']}';
				$pz++;
				if ($pz < count($arr)) {
					echo ',';
				}
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу прочитать папку', 0, $ex);
		}
		echo ']';
	}

	function download() {
		$user = User::getInstance();
		($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		$id = (isset(Router::$params['id'])) ? Router::$params['id'] : '';
		is_numeric($id) or die('Переданы неправильные параметры');
		$filename = '';
		$sql = 'select * from cloud_files where id = :id';
		try {
			$row = DB::prepare($sql)->execute([':id' => $id])->fetch();
			if ($row) {
				$filename = SITE_ROOT . '/files/' . $row['filename'];
			}
		} catch (PDOException $ex) {
			throw new DBException('Ошибка получения файла из базы', 0, $ex);
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
		$user = User::getInstance();
		$page = GetDef('page', 1);
		if ($page == 0) {
			$page = 1;
		}
		$limit = GetDef('rows');
		$sidx = GetDef('sidx', '1');
		$sord = GetDef('sord');
		$oper = PostDef('oper');
		$id = PostDef('id');
		$title = PostDef('title');
		$cloud_dirs_id = GetDef('cloud_dirs_id');
		if ($oper == '') {
			// Проверка: может ли пользователь просматривать?
			($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
			// Готовим ответ
			$responce = new stdClass();
			$responce->page = 0;
			$responce->total = 0;
			$responce->records = 0;
			$sql = 'select count(*) as cnt from cloud_files where cloud_dirs_id = :cloud_dirs_id';
			try {
				$row = DB::prepare($sql)->execute([':cloud_dirs_id' => $cloud_dirs_id])->fetch();
				$count = ($row) ? $row['cnt'] : 0;
			} catch (PDOException $ex) {
				throw new DBException('Не могу выбрать количество записей', 0, $ex);
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
				$arr = DB::prepare($sql)->execute([':cloud_dirs_id' => $cloud_dirs_id])->fetchAll();
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
						humanSize($row['sz'])
					];
					$i++;
				}
			} catch (PDOException $ex) {
				throw new DBException('Не могу выбрать список файлов', 0, $ex);
			}
			jsonExit($responce);
		}

		if ($oper == 'edit') {
			// Проверка: может ли пользователь редактировать?
			($user->isAdmin() || $user->TestRights([1, 5])) or die('Для редактирования не хватает прав!');
			$sql = 'update cloud_files set title = :title where id = :id';
			try {
				DB::prepare($sql)->execute([':title' => $title, ':id' => $id]);
			} catch (PDOException $ex) {
				throw new DBException('Не могу выполнить запрос', 0, $ex);
			}
			exit;
		}

		if ($oper == 'del') {
			// Проверка: может ли пользователь удалять?
			($user->isAdmin() || $user->TestRights([1, 6])) or die('Для удаления не хватает прав!');
			$sql = 'delete from cloud_files where id = :id';
			try {
				DB::prepare($sql)->execute([':id' => $id]);
			} catch (PDOException $ex) {
				throw new DBException('Не могу выполнить запрос', 0, $ex);
			}
			exit;
		}
	}

	function movefolder() {
		$user = User::getInstance();
		// Проверяем: может ли пользователь редактировать?
		($user->isAdmin() || $user->TestRights([1, 5])) or die('Для редактирования не хватает прав!');
		$nodekey = GetDef('nodekey');
		$srnodekey = GetDef('srnodekey');
		$sql = 'update cloud_dirs set parent = :nodekey where id = :srnodekey';
		try {
			DB::prepare($sql)->execute([':nodekey' => $nodekey, ':srnodekey' => $srnodekey]);
		} catch (PDOException $ex) {
			throw new DBException('Не могу обновить дерево папок', 0, $ex);
		}
	}

	function uploadfiles() {
		$user = User::getInstance();
		// Проверяем: может ли пользователь добавлять файлы?
		($user->isAdmin() || $user->TestRights([1, 4])) or die('Недостаточно прав');
		$selectedkey = PostDef('selectedkey');
		$orig_file = $_FILES['filedata']['name'];
		$dis = ['.htaccess']; // Запрещённые для загрузки файлы
		$rs = ['msg' => 'error']; // Ответ по умолчанию, если пойдёт что-то не так
		if (!in_array($orig_file, $dis)) {
			$userfile_name = guid() . '.' . pathinfo($orig_file, PATHINFO_EXTENSION);
			$src = $_FILES['filedata']['tmp_name'];
			$dst = SITE_ROOT . '/files/' . $userfile_name;
			$res = move_uploaded_file($src, $dst);
			if ($res) {
				$rs['msg'] = $userfile_name;
				$sz = filesize($dst);
				if ($selectedkey != '') {
					try {
						switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
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
						DB::prepare($sql)->execute([
							':selectedkey' => $selectedkey, ':orig_file' => $orig_file, ':userfile_name' => $userfile_name, ':sz' => $sz
						]);
					} catch (PDOException $ex) {
						throw new DBException('Не могу добавить файл', 0, $ex);
					}
				}
			}
		}
		jsonExit($rs);
	}

}
