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

$time_end = microtime(true);
$time = $time_end - $time_start;
$time = round($time, 2);
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