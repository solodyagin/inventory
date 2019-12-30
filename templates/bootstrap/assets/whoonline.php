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

$crd = date('Y-m-d H:i:s');
$sql = <<<TXT
SELECT
		UNIX_TIMESTAMP(:crd) - UNIX_TIMESTAMP(lastdt) AS res,
		users_profile.fio AS fio,
		users_profile.jpegphoto
FROM users
	INNER JOIN users_profile ON users_profile.usersid = users.id
TXT;
try {
	$arr = DB::prepare($sql)->execute([':crd' => $crd])->fetchAll();
	foreach ($arr as $row) {
		$res = $row['res'];
		$fio = $row['fio'];
		$jpegphoto = $row['jpegphoto'];
		if ($res < 10000) {
			if (!file_exists(SITE_ROOT . "/photos/$jpegphoto")) {
				$jpegphoto = 'noimage.jpg';
			}
			echo <<<TXT
<div class="col-sm-1 col-md-1">
	<div class="thumbnail">
		<img src="photos/{$jpegphoto}" title="{$fio}">
	</div>
</div>
TXT;
		}
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список заходов пользователей!', 0, $ex);
}
