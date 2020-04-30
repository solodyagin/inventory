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
try {
	$sql = 'select * from knt where id = :id and active = 1';
	$row = DB::prepare($sql)->execute([':id' => $kntid])->fetch();
	if ($row) {
		if ($row['dog'] == '1') {
			echo "<div class=\"alert alert-success\">Контрагент: {$row['name']}<br>";
			try {
				switch (DB::getAttribute(PDO::ATTR_DRIVER_NAME)) {
					case 'mysql':
						$sql = <<<TXT
select * from contract
where kntid = :kntid
	and work = 1
	and datestart <= curdate()
	and dateend >= curdate()
	and active = 1
TXT;
						break;
					case 'pgsql':
						$sql = <<<TXT
select * from contract
where kntid = :kntid
	and work = 1
	and datestart <= current_date
	and dateend >= current_date
	and active = 1
TXT;
						break;
				}
				$arr2 = DB::prepare($sql)->execute([':kntid' => $kntid])->fetchAll();
				$dogcount = count($arr2);
				foreach ($arr2 as $row2) {
					echo '<div class="well"><span class="label label-info">Активный договор:</span><br>';
					echo "Номер: {$row2['num']}, {$row2['name']}</br>";
					$dt1 = MySQLDateToDate($row2['datestart']);
					$dt2 = MySQLDateToDate($row2['dateend']);
					echo "Срок действия с $dt1 по $dt2<br>";
					echo "Файлы: ";
					$idcontract = $row2['id'];
					try {
						$sql = 'select * from files_contract where idcontract = :idcontract';
						$arr3 = DB::prepare($sql)->execute([':idcontract' => $idcontract])->fetchAll();
						foreach ($arr3 as $row3) {
							echo "<a target=\"_blank\" href=\"contractfiles/download?id={$row3['id']}\">{$row3['userfreandlyfilename']}</a>; ";
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
