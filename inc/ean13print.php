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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="ru-RU">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	</head>
	<body>
		<table border="1">
			<?php
			$idmass = explode(',', GetDef('mass'));

			$rw = 0;

			for ($i = 0; $i < count($idmass); $i++) {
				$idm = $idmass[$i];
				if ($rw == 0) {
					echo '<tr>';
				}
				echo '<td>';
				$sql = <<<TXT
SELECT equipment.shtrihkod, equipment.buhname, nome.name AS nomename, equipment.invnum
FROM equipment
	INNER JOIN nome ON nome.id = equipment.nomeid
WHERE equipment.id = :idm
TXT;
				try {
					$row = DB::prepare($sql)->execute(array(':idm' => $idm))->fetch();
					if ($row) {
						echo "<font size=\"1\">Бух:{$row['buhname']}<br>";
						echo "ИТ:{$row['nomename']}</font><br>";
						echo "<img src=\"ean13.php?shtrihkod={$row['shtrihkod']}\"><br>";
						echo "№{$row['invnum']}<br>";
					}
				} catch (PDOException $ex) {
					throw new DBException('Не могу выбрать', 0, $ex);
				}
				echo '</td>';
				if ($rw == 3) {
					echo '</tr>';
				}
				$rw++;
				if ($rw == 4) {
					$rw = 0;
				}
			}
			?>
		</table>
	</body>
</html>