<?php

// Скрипт отменяет регистрацию неиспользуемых модулей.

defined('WUO_ROOT') or die('Доступ запрещён'); // Запрещаем прямой вызов скрипта.

$mod = new Tmod;

// Удаляем модуль astra - "Управление серверами Astra"
$mod->UnRegister('astra');
$tables = array();
$result = $sqlcn->ExecuteSQL(<<<SQL
SELECT table_name AS `name`
FROM information_schema.tables
WHERE table_schema = DATABASE() AND table_name LIKE "astra%";
SQL
		) or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
while ($row = mysqli_fetch_array($result)) {
	$tables[] = $row['name'];
}
if (count($tables) > 0) {
	$str = implode(',', $tables);
	$sqlcn->ExecuteSQL("DROP TABLE IF EXISTS $str")
			or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
}

// Удаляем модуль bprocess - "Бизнес-процессы"
$mod->UnRegister('bprocess');
$tables = array();
$result = $sqlcn->ExecuteSQL(<<<SQL
SELECT table_name AS `name`
FROM information_schema.tables
WHERE table_schema = DATABASE() AND table_name LIKE "bp_%";
SQL
		) or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
while ($row = mysqli_fetch_array($result)) {
	$tables[] = $row['name'];
}
if (count($tables) > 0) {
	$str = implode(',', $tables);
	$sqlcn->ExecuteSQL("DROP TABLE IF EXISTS $str")
			or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
}

// Удаляем модуль cables - "Справочник кабелей и муфт"
$mod->UnRegister('cables');
$tables = array('lib_lines_in_muft');
$result = $sqlcn->ExecuteSQL(<<<SQL
SELECT table_name AS `name`
FROM information_schema.tables
WHERE table_schema = DATABASE() AND table_name LIKE "lib_cable_%";
SQL
		) or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
while ($row = mysqli_fetch_array($result)) {
	$tables[] = $row['name'];
}
if (count($tables) > 0) {
	$str = implode(',', $tables);
	$sqlcn->ExecuteSQL("DROP TABLE IF EXISTS $str")
			or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
}

// Удаляем модуль чата
$mod->UnRegister('chat');
$sqlcn->ExecuteSQL('DROP TABLE IF EXISTS `chat`')
		or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
$sqlcn->ExecuteSQL('DELETE FROM `config_common` WHERE `nameparam` LIKE "user-chat-sites-%"')
		or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));

// Удаляем модули: devicescontrol - "Управление устройствами", scriptalert - "Мониторинг выполнения скриптов"
$mod->UnRegister('devicescontrol');
$mod->UnRegister('scriptalert');
$tables = array('devnames', 'devgroups', 'devices', 'devices_snmp', 'script_run_monitoring');
if (count($tables) > 0) {
	$str = implode(',', $tables);
	$sqlcn->ExecuteSQL("DROP TABLE IF EXISTS $str")
			or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
}

// Удаляем модуль lanbilling - "Управление LanBilling"
$mod->UnRegister('lanbilling');
$tables = array('lbcfg');
$result = $sqlcn->ExecuteSQL(<<<SQL
SELECT table_name AS `name`
FROM information_schema.tables
WHERE table_schema = DATABASE() AND table_name LIKE "lanb%";
SQL
		) or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
while ($row = mysqli_fetch_array($result)) {
	$tables[] = $row['name'];
}
if (count($tables) > 0) {
	$str = implode(',', $tables);
	$sqlcn->ExecuteSQL("DROP TABLE IF EXISTS $str")
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
