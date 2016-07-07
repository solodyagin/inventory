<?php

// Скрипт отменяет регистрацию неиспользуемых модулей.

defined('WUO_ROOT') or die('Доступ запрещён'); // Запрещаем прямой вызов скрипта.

$mod = new Tmod;

// Удаляем модуль чата
$mod->UnRegister('chat');
$sqlcn->ExecuteSQL('DROP TABLE IF EXISTS `chat`')
		or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
$sqlcn->ExecuteSQL('DELETE FROM `config_common` WHERE `nameparam` LIKE "user-chat-sites-%"')
		or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));

// Удаляем модуль lanbilling - "Управление LanBilling"
$mod->UnRegister('lanbilling');
$result = $sqlcn->ExecuteSQL(<<<SQL
SELECT table_name AS `name`
FROM information_schema.tables
WHERE table_schema = DATABASE() AND table_name LIKE "lanb%";
SQL
		) or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
$rows = array();
while ($row = mysqli_fetch_array($result)) {
	$rows[] = $row['name'];
}
if (count($rows) > 0) {
	$tables = implode(',', $rows);
	$sqlcn->ExecuteSQL("DROP TABLE IF EXISTS $tables")
			or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
}

// Удаляем модуль zabbix-mon - "Мониторинг dashboard серверов Zabbix"
$mod->UnRegister('zabbix-mon');
$sqlcn->ExecuteSQL('DROP TABLE IF EXISTS `zabbix_mod_cfg`')
		or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
//$sqlcn->ExecuteSQL('DROP TABLE IF EXISTS `hosts`')
//		or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
//$sqlcn->ExecuteSQL('DROP TABLE IF EXISTS `items`')
//		or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
//$sqlcn->ExecuteSQL('DROP TABLE IF EXISTS `groups`')
//		or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
//$sqlcn->ExecuteSQL('DROP TABLE IF EXISTS `triggers`')
//		or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));

unset($mod);
