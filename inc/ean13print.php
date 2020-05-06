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

// Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

use core\db;
use core\dbexception;
use core\request;

$req = request::getInstance();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="ru-RU">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	</head>
	<body>
		<table border="1">
			<?php
			$idmass = explode(',', $req->get('mass'));
			$rw = 0;
			for ($i = 0; $i < count($idmass); $i++) {
				$idm = $idmass[$i];
				if ($rw == 0) {
					echo '<tr>';
				}
				echo '<td>';
				try {
					$sql = <<<TXT
select
	equipment.shtrihkod,
	equipment.buhname,
	nome.name as nomename,
	equipment.invnum
from equipment
	inner join nome on nome.id = equipment.nomeid
where equipment.id = :idm
TXT;
					$row = db::prepare($sql)->execute([':idm' => $idm])->fetch();
					if ($row) {
						echo "<font size=\"1\">Бух:{$row['buhname']}<br>";
						echo "ИТ:{$row['nomename']}</font><br>";
						echo "<img src=\"inc/ean13.php?shtrihkod={$row['shtrihkod']}\"><br>";
						echo "№{$row['invnum']}<br>";
					}
				} catch (PDOException $ex) {
					throw new dbexception('Не могу выбрать', 0, $ex);
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