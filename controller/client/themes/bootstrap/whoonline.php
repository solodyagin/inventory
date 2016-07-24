<?php

/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$crd = date('Y-m-d H:i:s');
$sql = <<<TXT
SELECT UNIX_TIMESTAMP('$crd') - UNIX_TIMESTAMP(lastdt) AS res,users_profile.fio AS fio,users_profile.jpegphoto
FROM   users
       INNER JOIN users_profile
               ON users_profile.usersid = users.id
TXT;
$result = $sqlcn->ExecuteSQL($sql)
		or die('Не могу выбрать список заходов пользователей! ' . mysqli_error($sqlcn->idsqlconnection));
while ($row = mysqli_fetch_array($result)) {
	$res = $row['res'];
	$fio = $row['fio'];
	$jpegphoto = $row['jpegphoto'];
	if (!file_exists(WUO_ROOT . "/photos/$jpegphoto")) {
		$jpegphoto = 'noimage.jpg';
	}
	if ($res < 10000) {
		echo '<div class="col-sm-6 col-md-4">';
		echo '<div class="thumbnail">';
		echo "<img src=\"photos/$jpegphoto\">";
		echo "<p align=\"center\">$fio</p>";
		echo '</div>';
		echo '</div>';
	}
}
