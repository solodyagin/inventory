<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

/*
 * Описание: Скрипт удаляет неиспользуемые модули.
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$mod = new Tmod;
$drop_tables = false;

// Удаляем модуль astra - "Управление серверами Astra"
$mod->UnRegister('astra');
if ($drop_tables) {
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
}

// Удаляем модуль bprocess - "Бизнес-процессы"
$mod->UnRegister('bprocess');
if ($drop_tables) {
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
}

// Удаляем модуль cables - "Справочник кабелей и муфт"
$mod->UnRegister('cables');
if ($drop_tables) {
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
}

// Удаляем модуль чата
$mod->UnRegister('chat');
if ($drop_tables) {
	$sqlcn->ExecuteSQL('DROP TABLE IF EXISTS `chat`')
			or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
	$sqlcn->ExecuteSQL('DELETE FROM `config_common` WHERE `nameparam` LIKE "user-chat-sites-%"')
			or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
}

// Удаляем модули: devicescontrol - "Управление устройствами", scriptalert - "Мониторинг выполнения скриптов"
$mod->UnRegister('devicescontrol');
$mod->UnRegister('scriptalert');
if ($drop_tables) {
	$tables = array('devnames', 'devgroups', 'devices', 'devices_snmp', 'script_run_monitoring');
	if (count($tables) > 0) {
		$str = implode(',', $tables);
		$sqlcn->ExecuteSQL("DROP TABLE IF EXISTS $str")
				or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
	}
}

// Удаляем модуль lanbilling - "Управление LanBilling"
$mod->UnRegister('lanbilling');
if ($drop_tables) {
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
}

// Удаляем модуль ical - "Календарь"
$mod->UnRegister('ical');
if ($drop_tables) {
	$tables = array('jqcalendar');
	if (count($tables) > 0) {
		$str = implode(',', $tables);
		$sqlcn->ExecuteSQL("DROP TABLE IF EXISTS $str")
				or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
	}
}

// Удаляем модуль smscenter - "СМС-Центр"
$mod->UnRegister('smscenter');
if ($drop_tables) {
	$tables = array();
	$result = $sqlcn->ExecuteSQL(<<<SQL
SELECT table_name AS `name`
FROM information_schema.tables
WHERE table_schema = DATABASE() AND table_name LIKE "sms%";
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
}

// Удаляем модуль tasks - "Задачи"
$mod->UnRegister('tasks');
if ($drop_tables) {
	$tables = array('tasks');
	if (count($tables) > 0) {
		$str = implode(',', $tables);
		$sqlcn->ExecuteSQL("DROP TABLE IF EXISTS $str")
				or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
	}
}

// Удаляем модуль usersfaze - "Где сотрудник?"
$mod->UnRegister('usersfaze');

// Удаляем модуль workandplans - "Оперативная обстановка на заводе"
$mod->UnRegister('workandplans');

// Удаляем модуль worktime - "Вход и выход работников организации (турникет Орион)"
$mod->UnRegister('worktime');

// Удаляем модуль zabbix-mon - "Мониторинг dashboard серверов Zabbix"
$mod->UnRegister('zabbix-mon');
if ($drop_tables) {
	$tables = array('zabbix_mod_cfg', 'hosts', 'items', 'groups', 'triggers');
	if (count($tables) > 0) {
		$str = implode(',', $tables);
		$sqlcn->ExecuteSQL("DROP TABLE IF EXISTS $str")
				or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
	}
}

unset($mod);
