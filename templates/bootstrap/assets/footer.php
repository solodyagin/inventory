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

global $time_start;

$time_end = microtime(true);
$time = round($time_end - $time_start, 2);
?>
<footer class="navbar">
	<div class="row-fluid container-fluid">
		<div class="span12" align="center">
			<p>&copy; <a href="http://грибовы.рф" target="_blank"> 2011-<?php echo date('Y'); ?></a>. Собрано за <?php echo $time; ?> сек.</p>
		</div>
	</div>
</footer>
</body>
</html>