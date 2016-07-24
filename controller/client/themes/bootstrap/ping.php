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

if (in_array($user->mode, [0, 1])):
	?>
	<div class="well">
		<input id="test_ping" class="btn btn-primary" name="test_ping" value="Проверить">
		<div id="ping_add"></div>
	</div>
	<script src="controller/client/js/ping.js"></script>
<?php else: ?>
	<div class="alert alert-error">
		У вас нет доступа в данный раздел!
	</div>
<?php endif;
