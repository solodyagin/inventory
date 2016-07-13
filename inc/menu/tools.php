<?php

// Данный код создан и распространяется по лицензии GPL v3
// Разработчики:
//   Грибов Павел,
//   Сергей Солодягин (solodyagin@gmail.com)
//   (добавляйте себя если что-то делали)
// http://грибовы.рф

defined('WUO_ROOT') or die('Доступ запрещён'); // Запрещаем прямой вызов скрипта.

$md = new Tmod; // обьявляем переменную для работы с классом модуля

$this->Add("main", "<i class='fa fa-cog fa-fw'> </i>Инструменты", "Инструменты", 3, "tools", "");

$md->Register("tasks", "Задачи", "Грибов Павел");
if ($md->IsActive("tasks") == 1) {
	$this->Add("tools", "<i class='fa fa-tasks fa-fw'> </i>Мои задачи", "Мои задачи", 3, "tools/mytasks", "mytasks");
}

$md->Register("workmen", "Менеджер по обслуживанию ", "Грибов Павел");
if ($md->IsActive("workmen") == 1) {
	$this->Add("tools", "<i class='fa fa-bug fa-fw'> </i>Менеджер по обслуживанию", "Менеджер по обслуживанию", 3, "tools/workmen", "workmen");
}

$this->Add("tools", "<i class='fa fa-check fa-fw'> </i>Контроль договоров", "Контроль договоров", 3, "tools/dog_knt", "dog_knt");
$this->Add("tools", "<i class='fa fa-clone fa-fw'> </i>ТМЦ на моем рабочем месте", "ТМЦ на моем рабочем месте", 3, "tools/eq_list", "eq_list");

$md->Register("ping", "Проверка доступности ТМЦ по ping", "Грибов Павел");
// если модуль ping активирован, то тогда показываем пункт меню
if ($md->IsActive("ping") == 1) {
	$this->Add("tools", "<i class='fa fa-bolt fa-fw'> </i>Проверка доступности ТМЦ", "Проверка доступности ТМЦ", 3, "tools/ping", "ping");
}

unset($md);
