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

$placesid = GetDef('placesid');
$addnone = GetDef('addnone');

$sql = <<<TXT
SELECT nome.id AS nid,nome.name AS nomename,eq.id AS eqid
FROM   nome
       INNER JOIN (SELECT nomeid,id
                   FROM   equipment
                   WHERE  placesid = '$placesid') AS eq
               ON nome.id = eq.nomeid
TXT;
$result = $sqlcn->ExecuteSQL($sql)
		or die('Не могу выбрать список помещений! ' . mysqli_error($sqlcn->idsqlconnection));
echo '<select name="splacestmc" id="splacestmc" size="10">';
if ($addnone == 'true') {
	echo '<option value="-1">нет выбора</option>';
}
while ($row = mysqli_fetch_array($result)) {
	$sl = ($row['eqid'] == $placesid) ? 'selected' : '';
	echo "<option value=\"{$row[eqid]}\" $sl>{$row['nomename']}</option>";
}
echo '</select>';
?>
<script src="controller/client/js/mapeq.js"></script>
