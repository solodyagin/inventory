<?php

/* 
 * (с) 2014 Грибов Павел
 * http://грибовы.рф * 
 * Если исходный код найден в сети - значит лицензия GPL v.3 * 
 * В противном случае - код собственность ГК Яртелесервис, Мультистрим, Телесервис, Телесервис плюс * 
 */

include_once ("../../../config.php");                    // загружаем первоначальные настройки

// загружаем классы

include_once("../../../class/sql.php");               // загружаем классы работы с БД
include_once("../../../class/config.php");		// загружаем классы настроек
include_once("../../../class/users.php");		// загружаем классы работы с пользователями
include_once("../../../class/employees.php");		// загружаем классы работы с профилем пользователя


// загружаем все что нужно для работы движка

include_once("../../../inc/connect.php");		// соеденяемся с БД, получаем $mysql_base_id
include_once("../../../inc/config.php");              // подгружаем настройки из БД, получаем заполненый класс $cfg
include_once("../../../inc/functions.php");		// загружаем функции
include_once("../../../inc/login.php");		// загружаем функции

$foldername=_GET('foldername'); 

// Роли:  
//            1="Полный доступ"
//            2="Просмотр финансовых отчетов"
//            3="Просмотр количественных отчетов"
//            4="Добавление"
//            5="Редактирование"
//            6="Удаление"


if ($user->TestRoles("1,4")==true){

$sql="insert into cloud_dirs (parent,name) values (0,'$foldername')";
$result = $sqlcn->ExecuteSQL($sql) or die("Не могу добавить папку!!".mysqli_error($sqlcn->idsqlconnection));

} else {echo "У вас не хватает прав на добавление!";};