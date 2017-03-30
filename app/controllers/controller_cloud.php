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

class Controller_Cloud extends Controller {

	function index() {
		$cfg = Config::getInstance();
		$this->view->generate('view_cloud', $cfg->theme);
	}

	/**
	 * TODO: Добавить метод Model_Cloud->addFolder()
	 * @throws DBException
	 */
	function addfolder() {
		$user = User::getInstance();

		// Проверка: может ли пользователь добавлять?
		($user->isAdmin() || $user->TestRoles('1,4')) or die('У вас не хватает прав на добавление!');

		$foldername = (isset(Router::$args['foldername'])) ? Router::$args['foldername'] : '';

		if (!empty($foldername)) {
			$sql = 'INSERT INTO cloud_dirs (parent, name) VALUES (0, :foldername)';
			try {
				DB::prepare($sql)->execute(array(':foldername' => $foldername));
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
		($user->isAdmin() || $user->TestRoles('1,6')) or die('У вас не хватает прав на удаление!');

		$folderkey = (isset(Router::$args['folderkey'])) ? Router::$args['folderkey'] : '';
		if (!empty($foldername)) {
			$sql = 'DELETE FROM cloud_dirs WHERE id = :folderkey';
			try {
				DB::prepare($sql)->execute(array(':folderkey' => $folderkey));
			} catch (PDOException $ex) {
				throw new DBException('Не могу удалить папку', 0, $ex);
			}
		}
	}

	function GetTreeRecurse($key) {
		$sql = 'SELECT * FROM cloud_dirs WHERE parent = :key';
		try {
			$arr = DB::prepare($sql)->execute(array(':key' => $key))->fetchAll();
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

		($user->isAdmin() || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');

		echo '[';

		// читаю корневые папки
		$sql = 'SELECT * FROM cloud_dirs WHERE parent = 0';
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

		($user->isAdmin() || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');

		$id = (isset(Router::$args['id'])) ? Router::$args['id'] : '';
		is_numeric($id) or die('Переданы неправильные параметры');

		$filename = '';

		$sql = 'SELECT * FROM cloud_files WHERE id = :id';
		try {
			$row = DB::prepare($sql)->execute(array(':id' => $id))->fetch();
			if ($row) {
				$filename = WUO_ROOT . '/files/' . $row['filename'];
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
			($user->isAdmin() || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');

			// Готовим ответ
			$responce = new stdClass();
			$responce->page = 0;
			$responce->total = 0;
			$responce->records = 0;

			$sql = 'SELECT COUNT(*) AS cnt FROM cloud_files WHERE cloud_dirs_id = :cloud_dirs_id';
			try {
				$row = DB::prepare($sql)->execute(array(':cloud_dirs_id' => $cloud_dirs_id))->fetch();
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

			$sql = <<<TXT
SELECT   *
FROM     cloud_files
WHERE    cloud_dirs_id = :cloud_dirs_id
ORDER BY $sidx $sord
LIMIT    $start, $limit
TXT;
			try {
				$i = 0;
				$arr = DB::prepare($sql)->execute(array(':cloud_dirs_id' => $cloud_dirs_id))->fetchAll();
				foreach ($arr as $row) {
					$responce->rows[$i]['id'] = $row['id'];
					switch (pathinfo($row['filename'], PATHINFO_EXTENSION)) {
						case 'jpeg':
						case 'jpg':
						case 'png':
							$ico = '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
							break;
						case 'xls':
						case 'ods':
							$ico = '<i class="fa a-file-excel-o" aria-hidden="true"></i>';
							break;
						case 'doc':
						case 'odt':
							$ico = '<i class="fa fa-file-word-o" aria-hidden="true"></i>';
							break;
						default:
							$ico = '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
					}
					$ico = '<a target="_blank" href="/cloud/download?id=' . $row['id'] . '">' . $ico . '</a>';
					$title = $row['title'];
					$responce->rows[$i]['cell'] = array($row['id'], $ico, $title, $row['dt'], human_sz($row['sz']));
					$i++;
				}
			} catch (PDOException $ex) {
				throw new DBException('Не могу выбрать список файлов', 0, $ex);
			}
			jsonExit($responce);
		}

		if ($oper == 'edit') {
			// Проверка: может ли пользователь редактировать?
			($user->isAdmin() || $user->TestRoles('1,5')) or die('Для редактирования не хватает прав!');

			$sql = 'UPDATE cloud_files SET title = :title WHERE id = :id';
			try {
				DB::prepare($sql)->execute(array(':title' => $title, ':id' => $id));
			} catch (PDOException $ex) {
				throw new DBException('Не могу выполнить запрос', 0, $ex);
			}
			exit;
		}

		if ($oper == 'del') {
			// Проверка: может ли пользователь удалять?
			($user->isAdmin() || $user->TestRoles('1,6')) or die('Для удаления не хватает прав!');

			$sql = 'DELETE FROM cloud_files WHERE id = :id';
			try {
				DB::prepare($sql)->execute(array(':id' => $id));
			} catch (PDOException $ex) {
				throw new DBException('Не могу выполнить запрос', 0, $ex);
			}
			exit;
		}
	}

	function movefolder() {
		$user = User::getInstance();

		// Проверяем может ли пользователь редактировать?
		($user->isAdmin() || $user->TestRoles('1,5')) or die('Для редактирования не хватает прав!');

		$nodekey = GetDef('nodekey');
		$srnodekey = GetDef('srnodekey');

		$sql = 'UPDATE cloud_dirs SET parent = :nodekey WHERE id = :srnodekey';
		try {
			DB::prepare($sql)->execute(array(':nodekey' => $nodekey, ':srnodekey' => $srnodekey));
		} catch (PDOException $ex) {
			throw new DBException('Не могу обновить дерево папок', 0, $ex);
		}
	}

	function uploadfiles() {
		$user = User::getInstance();

		// Проверяем: может ли пользователь добавлять файлы?
		($user->isAdmin() || $user->TestRoles('1,4')) or die('Недостаточно прав');

		$selectedkey = PostDef('selectedkey');
		$orig_file = $_FILES['filedata']['name'];
		$dis = array('.htaccess'); // Запрещённые для загрузки файлы

		$rs = array('msg' => 'error'); // Ответ по умолчанию, если пойдёт что-то не так

		if (!in_array($orig_file, $dis)) {
			$userfile_name = GetRandomId(8) . '.' . pathinfo($orig_file, PATHINFO_EXTENSION);
			$src = $_FILES['filedata']['tmp_name'];
			$dst = WUO_ROOT . '/files/' . $userfile_name;
			$res = move_uploaded_file($src, $dst);
			if ($res) {
				$rs['msg'] = $userfile_name;
				$sz = filesize($dst);
				if ($selectedkey != '') {
					$sql = <<<TXT
INSERT INTO cloud_files
            (id,cloud_dirs_id,title,filename,dt,sz)
VALUES      (NULL, :selectedkey, :orig_file, :userfile_name, NOW(), :sz)
TXT;
					try {
						DB::prepare($sql)->execute(array(
							':selectedkey' => $selectedkey,
							':orig_file' => $orig_file,
							':userfile_name' => $userfile_name,
							':sz' => $sz
						));
					} catch (PDOException $ex) {
						throw new DBException('Не могу добавить файл', 0, $ex);
					}
				}
			}
		}

		jsonExit($rs);
	}

}
