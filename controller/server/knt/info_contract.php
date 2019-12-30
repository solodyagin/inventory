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

$kntid = GetDef('kntid');

$sql = 'SELECT * FROM knt WHERE id = :id AND active = 1';
try {
	$row = DB::prepare($sql)->execute(array(':id' => $kntid))->fetch();
	if ($row) {
		if ($row['dog'] == '1') {
			echo "<div class=\"alert alert-success\">Контрагент: {$row['name']}<br>";
			$sql = <<<TXT
SELECT * FROM contract
WHERE kntid = :kntid
	AND work = 1
	AND datestart <= CURDATE()
	AND dateend >= CURDATE()
	AND active = 1
TXT;
			try {
				$arr2 = DB::prepare($sql)->execute(array(':kntid' => $kntid))->fetchAll();
				$dogcount = count($arr2);
				foreach ($arr2 as $row2) {
					echo '<div class="well"><span class="label label-info">Активный договор:</span><br>';
					echo "Номер: {$row2['num']}, {$row2['name']}</br>";
					$dt1 = MySQLDateToDate($row2['datestart']);
					$dt2 = MySQLDateToDate($row2['dateend']);
					echo "Срок действия с $dt1 по $dt2<br>";
					echo "Файлы: ";

					$idcontract = $row2['id'];

					$sql = 'SELECT * FROM files_contract WHERE idcontract = :idcontract';
					try {
						$arr3 = DB::prepare($sql)->execute(array(':idcontract' => $idcontract))->fetchAll();
						foreach ($arr3 as $row3) {
							$fn1 = $row3['filename'];
							$fn2 = $row3['userfreandlyfilename'];
							echo "<a target=\"_blank\" href=\"files/$fn1\">$fn2</a>; ";
						}
					} catch (PDOException $ex) {
						throw new DBException('Не могу выбрать список файлов', 0, $ex);
					}
					echo '<br>';
					echo '</div>';
				}
			} catch (PDOException $ex) {
				throw new DBException('Не могу выбрать список договоров', 0, $ex);
			}
			if ($dogcount == 0) {
				echo '<div class="alert alert-danger">';
				echo '<b>Внимание!</b> У контрагента нет активных договоров. Обратитесь в юридический отдел!';
				echo '</div>';
			}
			echo '</div>';
		} else {
			echo '<div class="alert alert-danger">';
			echo "<b>Внимание!</b> У контрагента {$row['name']} не выставлен контроль договоров. Обратитесь в юридический отдел!";
			echo '</div>';
		}
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список контрагентов', 0, $ex);
}
