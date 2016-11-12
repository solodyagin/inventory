<?php

/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

// Проверка: может ли пользователь удалять?
(($user->mode == 1) || $user->TestRoles('1,6')) or die('У вас не хватает прав на удаление!');

$folderkey = GetDef('folderkey');

$sql = 'DELETE FROM cloud_dirs WHERE id = :folderkey';
try {
	DB::prepare($sql)->execute(array(':folderkey' => $folderkey));
} catch (PDOException $ex) {
	throw new DBException('Не могу удалить папку!', 0, $ex);
}
