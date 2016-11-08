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

// Проверяем может ли пользователь редактировать?
(($user->mode == 1) || $user->TestRoles('1,5')) or die('Для редактирования не хватает прав!');

$nodekey = GetDef('nodekey');
$srnodekey = GetDef('srnodekey');

$sql = "UPDATE cloud_dirs SET parent = '$nodekey' WHERE id = '$srnodekey'";
$sqlcn->ExecuteSQL($sql)
		or die('Не могу обновить дерево папок! ' . mysqli_error($sqlcn->idsqlconnection));
