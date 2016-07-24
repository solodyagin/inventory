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

$kntid = GetDef('kntid');

$sql = "SELECT * FROM knt WHERE id = '$kntid' AND active = 1";
$result = $sqlcn->ExecuteSQL($sql)
		or die('Не могу выбрать список контрагентов!' . mysql_error());
$dogcount = 0;
while ($row = mysqli_fetch_array($result)) {
	if ($row['dog'] == '1') {
		echo '<div class="alert alert-success">Контрагент:';
		$nm = $row['name'];
		echo "$nm<br>";
		$sql = "SELECT * FROM contract WHERE kntid = '$kntid' AND work = 1 AND datestart <= CURDATE() AND dateend >= CURDATE() AND active = 1";
		$result2 = $sqlcn->ExecuteSQL($sql)
				or die('Не могу выбрать список договоров! ' . mysqli_error($sqlcn->idsqlconnection));
		while ($row2 = mysqli_fetch_array($result2)) {
			$dogcount++;
			echo '<div class="well"><span class="label label-info">Активный договор:</span><br>';
			$dt1 = MySQLDateToDate($row2['datestart']);
			$dt2 = MySQLDateToDate($row2['dateend']);
			$num = $row2['num'];
			$nm = $row2['name'];
			echo "Номер: $num, $nm</br>";
			echo "Срок действия с $dt1 по $dt2<br>";
			echo "Файлы: ";
			$rid = $row2['id'];
			$sql = "SELECT * FROM files_contract WHERE idcontract = $rid";
			$result3 = $sqlcn->ExecuteSQL($sql)
					or die('Не могу выбрать список файлов! ' . mysqli_error($sqlcn->idsqlconnection));
			while ($row3 = mysqli_fetch_array($result3)) {
				$fn1 = $row3['filename'];
				$fn2 = $row3['userfreandlyfilename'];
				echo "<a target=\"_blank\" href=\"files/$fn1\">$fn2</a>; ";
			}
			echo '<br>';
			echo '</div>';
		}
		if ($dogcount == 0) {
			echo '<div class="alert alert-error">';
			echo '<b>Внимание!</b> У контрагента нет активных договоров. Обратитесь в юридический отдел!';
			echo '</div>';
		}
		echo '</div>';
	} else {
		echo '<div class="alert alert-error">';
		$nm = $row['name'];
		echo "<b>Внимание!</b> У контрагента $nm не выставлен конроль договоров. Обратитесь в юридический отдел!";
		echo '</div>';
	}
}
