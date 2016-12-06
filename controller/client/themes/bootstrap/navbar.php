<?php
/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

if (count($cfg->navbar) > 0):
	?>
	<ul class="breadcrumb">
		<?php
		for ($i = 0; $i < count($cfg->navbar); $i++) {
			$ntxt = $cfg->navbar[$i];
			echo "<li>$ntxt <span class=\"divider\">/</span></li>";
		}
		?>
	</ul>
	<?php
endif;
?>
