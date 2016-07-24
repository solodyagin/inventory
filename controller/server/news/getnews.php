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

$num = GetDef('num', '0');

$sql = "SELECT * FROM news ORDER BY dt DESC limit $num, 4";
$result = $sqlcn->ExecuteSQL($sql)
		or die('Не могу выбрать список новостей! ' . mysqli_error($sqlcn->idsqlconnection));
$cnt = 0;
$rz = 0;
while ($row = mysqli_fetch_array($result)) {
	$dt = MySQLDateTimeToDateTimeNoTime($row['dt']);
	$title = $row['title'];
	echo '<span class="label label-info">' . $dt . '</span><h5>' . $title . '</h5>';
	$pieces = explode('<!-- pagebreak -->', $row['body']);
	echo "<p>$pieces[0]</p>";
	if (isset($pieces[1])) {
		echo '<div align="right"><a class="btn btn-primary btn-small" href="?content_page=news_read&id=' . $row[id] . '">Читать дальше</a></div>';
	}
	$rz++;
}
if ($rz == 0) {
	echo 'error';
}
