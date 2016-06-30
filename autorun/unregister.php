<?php

// Скрипт отменяет регистрацию неиспользуемых модулей.

defined('WUO_ROOT') or die('Доступ запрещён'); // Запрещаем прямой вызов скрипта.

$mod = new Tmod;

// Удаляем модуль чата
$mod->UnRegister('chat');
$sqlcn->ExecuteSQL('DROP TABLE IF EXISTS `chat`')
		or die('Неверный запрос: ' . mysqli_error($sqlcn->idsqlconnection));
