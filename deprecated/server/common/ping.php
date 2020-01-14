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

$orgid = GetDef('orgid');
?>
<table id="mytable" class="table table-striped">
	<thead>
		<tr>
			<th>Статус</th>
			<th>IP (имя)</th>
			<th>Название</th>
			<th>Группа</th>
			<th>Где</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$sql = <<<TXT
SELECT places.name AS pname,eq3.grname AS grname,eq3.ip AS ip,eq3.nomename AS nomename
FROM   places
       INNER JOIN (SELECT eq2.placesid AS placesid,group_nome.name AS grname,eq2.ip AS ip,eq2.nomename AS nomename
                   FROM   group_nome
                          INNER JOIN (SELECT eq.placesid AS placesid,nome.groupid AS groupid,eq.ip AS ip,
                                             nome.name AS nomename
                                      FROM   nome
                                             INNER JOIN (SELECT equipment.placesid AS placesid,
                                                                equipment.nomeid AS nomeid,
                                                                equipment.ip AS ip
                                                         FROM   equipment
                                                         WHERE  equipment.active = 1
                                                                AND equipment.ip <> ''
                                                                AND equipment.orgid = :orgid) AS eq
                                                     ON eq.nomeid = nome.id) AS eq2
                                  ON eq2.groupid = group_nome.id) AS eq3
               ON places.id = eq3.placesid
TXT;
		try {
			$arr = DB::prepare($sql)->execute(array(':orgid' => $orgid))->fetchAll();
			foreach ($arr as $row) {
				exec("ping $row[ip] -c 1 -w 1 && exit", $output, $retval);
				$res = ($retval != 0) ? 'glyphicon-remove' : 'glyphicon-ok';
				echo '<tr>';
				echo "<td><i class=\"glyphicon $res\"></i></td>";
				echo "<td>{$row['ip']}</td>";
				echo "<td>{$row['nomename']}</td>";
				echo "<td>{$row['grname']}</td>";
				echo "<td>{$row['pname']}</td>";
				echo '</tr>';
			}
		} catch (PDOException $ex) {
			throw new DBException('Не получилось выполнить запрос на получение списка номенклатуры', 0, $ex);
		}
		?>
	</tbody>
</table>
