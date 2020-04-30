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

class Controller_ContractFiles extends Controller {

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
		$idcontract = GetDef('idcontract');
		// Готовим ответ
		$responce = new stdClass();
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		try {
			$sql = 'select count(*) cnt from files_contract where idcontract = :idcontract';
			$row = DB::prepare($sql)->execute([':idcontract' => $idcontract])->fetch();
			$count = ($row) ? $row['cnt'] : 0;
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список договоров (1)', 0, $ex);
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
select * from files_contract
where idcontract = :idcontract
order by $sidx $sord
limit $start, $limit
TXT;
					break;
				case 'pgsql':
					$sql = <<<TXT
select * from files_contract
where idcontract = :idcontract
order by $sidx $sord
offset $start limit $limit
TXT;
					break;
			}
			$arr = DB::prepare($sql)->execute([':idcontract' => $idcontract])->fetchAll();
			$i = 0;
			foreach ($arr as $row) {
				$rowid = $row['id'];
				$responce->rows[$i]['id'] = $rowid;
				$name = $row['userfreandlyfilename'];
				if ($name == '') {
					$name = 'Посмотреть';
				}
				$responce->rows[$i]['cell'] = [$rowid, $name];
				$i++;
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать список договоров (2)', 0, $ex);
		}
		jsonExit($responce);
	}

	/**
	 * Для работы jqGrid (editurl)
	 * @todo Удалять файл физически
	 */
	function change() {
		$user = User::getInstance();
		$oper = PostDef('oper');
		$id = PostDef('id');
		$name = PostDef('filename');
		switch ($oper) {
			case 'edit':
				// Проверка: может ли пользователь редактировать?
				($user->isAdmin() || $user->TestRights([1, 5])) or die('Для редактирования не хватает прав!');
				$sql = 'update files_contract set userfreandlyfilename = :name where id = :id';
				try {
					DB::prepare($sql)->execute([':name' => $name, ':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не могу выполнить запрос', 0, $ex);
				}
				break;
			case 'del':
				// Проверяем: может ли пользователь удалять?
				($user->isAdmin() || $user->TestRights([1, 6])) or die('Для удаления не хватает прав!');
				try {
					$sql = 'delete from files_contract where id = :id';
					DB::prepare($sql)->execute([':id' => $id]);
				} catch (PDOException $ex) {
					throw new DBException('Не смог удалить файл', 0, $ex);
				}
				break;
		}
	}

	/** Загрузка отсканированного документа */
	function upload() {
		$user = User::getInstance();
		// Проверяем: может ли пользователь добавлять файлы?
		($user->isAdmin() || $user->TestRights([1, 4])) or die('Недостаточно прав');
		$contractid = PostDef('contractid');
		$orig_file = $_FILES['filedata']['name'];
		$dis = ['.htaccess']; // Запрещённые для загрузки файлы
		$rs = ['msg' => 'error']; // Ответ по умолчанию, если пойдёт что-то не так
		if (!in_array($orig_file, $dis)) {
			$userfile_name = guid() . '.' . pathinfo($orig_file, PATHINFO_EXTENSION);
			$src = $_FILES['filedata']['tmp_name'];
			$dst = SITE_ROOT . '/files/' . $userfile_name;
			$res = move_uploaded_file($src, $dst);
			if ($res) {
				$rs = ['msg' => $userfile_name];
				if ($contractid != '') {
					try {
						switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
							case 'mysql':
								$sql = <<<TXT
insert into files_contract (id, idcontract, filename, userfreandlyfilename)
values (null, :contractid, :userfile_name, :orig_file)
TXT;
								break;
							case 'pgsql':
								$sql = <<<TXT
insert into files_contract (idcontract, filename, userfreandlyfilename)
values (:contractid, :userfile_name, :orig_file)
TXT;
								break;
						}
						DB::prepare($sql)->execute([':contractid' => $contractid, ':userfile_name' => $userfile_name, ':orig_file' => $orig_file]);
					} catch (PDOException $ex) {
						throw new DBException('Не могу добавить файл', 0, $ex);
					}
				}
			}
		}
		jsonExit($rs);
	}

	/** Скачивание файла */
	function download() {
		$user = User::getInstance();
		($user->isAdmin() || $user->TestRights([1, 3, 4, 5, 6])) or die('Недостаточно прав');
		$id = (isset(Router::$params['id'])) ? Router::$params['id'] : '';
		is_numeric($id) or die('Переданы неправильные параметры');
		$filename = '';
		$sql = 'select * from files_contract where id = :id';
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
		$name = rawurldecode($row['userfreandlyfilename']);
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

}
