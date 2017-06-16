<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$num = GetDef('num', '0');

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
