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

if ($user->TestRoles('1,4,5,6')):
	?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="list2"></table>
				<div id="pager2"></div>
				<div id="add_edit"></div>
			</div>
		</div>
	</div>
	<script src="controller/client/js/libre_nome.js"></script>
<?php else: ?>
	<div class="alert alert-error">
		У вас нет доступа в данный раздел!
	</div>
<?php endif;
