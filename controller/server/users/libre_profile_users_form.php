<?php

/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

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
