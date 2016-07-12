<?php

// Данный код создан и распространяется по лицензии GPL v3
// Разработчики:
//   Грибов Павел,
//   Сергей Солодягин (solodyagin@gmail.com)
//   (добавляйте себя если что-то делали)
// http://грибовы.рф

$md = new Tmod; // обьявляем переменную для работы с классом модуля

$this->Add("main", "<i class='fa fa-list-ul fa-fw'> </i>Справочники", "Справочники", 10, "libre", "");

$this->Add("libre", "<i class='fa fa-sitemap fa-fw'> </i>Список организаций", "Список организаций", 10, "libre/org_list", "org_list");
$this->Add("libre", "<i class='fa fa-users fa-fw'> </i>Пользователи", "Пользователи", 10, "libre/pipl_list", "pipl_list");
$this->Add("libre", "<i class='fa fa-location-arrow fa-fw'> </i>Помещения", "Помещения", 10, "libre/places", "places");
$this->Add("libre", "<i class='fa fa-cogs fa-fw'> </i>Контрагенты", "Контрагенты", 10, "libre/knt_list", "knt_list");

$this->Add("libre", "<i class='fa fa-cubes fa-fw'> </i>Производители", "Производители", 10, "libre/knt_list", "vendors");
$this->Add("libre", "<i class='fa fa-object-group fa-fw'> </i>Группы ТМЦ", "Группы ТМЦ", 10, "libre/knt_list", "tmc_group");
$this->Add("libre", "<i class='fa fa-empire fa-fw'> </i>Номенклатура", "Номенклатура", 10, "libre/knt_list", "nome");

unset($md);
