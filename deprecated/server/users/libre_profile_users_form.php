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

// Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

use core\baseuser;
use core\request;

$req = request::getInstance();
$step = $req->get('step');
$userid = $req->get('userid');
$fio = $req->get('fio');
$post = $req->get('post');
$photo = $req->get('picname');
$phone1 = $req->get('phone1');
$phone2 = $req->get('phone2');

$tmpuser = new baseuser();
$tmpuser->getById($userid);
$tmpuser->fio = $fio;
$tmpuser->jpegphoto = $photo;
$tmpuser->post = $post;
$tmpuser->telephonenumber = $phone1;
$tmpuser->homephone = $phone2;
$tmpuser->update();
unset($tmpuser);

echo 'ok';
