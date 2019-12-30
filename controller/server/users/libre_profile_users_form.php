<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчик: Грибов Павел
 * Сайт: http://грибовы.рф
 */
/*
 * Inventory - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчик: Сергей Солодягин (solodyagin@gmail.com)
 */

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

$step = GetDef('step');
$userid = GetDef('userid');
$fio = PostDef('fio');
$post = PostDef('post');
$photo = PostDef('picname');
$phone1 = PostDef('phone1');
$phone2 = PostDef('phone2');

$tmpuser = new BaseUser();
$tmpuser->getById($userid);
$tmpuser->fio = $fio;
$tmpuser->jpegphoto = $photo;
$tmpuser->post = $post;
$tmpuser->telephonenumber = $phone1;
$tmpuser->homephone = $phone2;
$tmpuser->Update();
unset($tmpuser);

echo 'ok';
