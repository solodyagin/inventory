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

$eqid = GetDef('eqid');
$coor = GetDef('coor');

$x = $coor[0][1];
$y = $coor[0][0];

$sql = "UPDATE equipment SET mapx = '$x', mapy = '$y', mapmoved = 0 WHERE id = '$eqid'";
$result = $sqlcn->ExecuteSQL($sql)
		or die('Не могу обновить координаты ТМЦ! ' . mysqli_error($sqlcn->idsqlconnection));
