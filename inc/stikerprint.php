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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="ru-RU">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
	</head>
	<body>
		<?php
		$idmass = explode(",", GetDef('mass'));
		echo '<table border="1" width="1380px" cellpadding="8" cellspacing="0">';
		$rw = 0;
		for ($i = 0; $i < count($idmass); $i++) {
			$idm = $idmass[$i];
			$sql = "SELECT * FROM users_profile WHERE usersid = '$idm'";
			$result = $sqlcn->ExecuteSQL($sql)
					or die('Не могу выбрать!' . mysqli_error($sqlcn->idsqlconnection));
			if ($rw == 0) {
				echo '<tr style="vertical-align: top;">';
			}
			echo '<td width="460px">';
			while ($row = mysqli_fetch_array($result)) {
				$fio = $row['fio'];
				$code = $row['code'];
				$post = $row['post'];
				echo "<h1>$fio</h1>";
				echo "<h2>$post</h2>";
				echo "<h3>№$code</h3>";
			}
			echo '</td>';
			if ($rw == 2) {
				echo '</tr>';
			}
			$rw++;
			if ($rw == 3) {
				$rw = 0;
			}
		}
		echo '</table>';
		?>
	</body>
</html>