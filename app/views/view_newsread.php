<?php
/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

# Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');
?>
<div class="row-fluid">
	<div class="span12 well" id="news_read">
		<span class="label label-info"><?= "$news_title / $news_dt"; ?></span>
		<p><?= $news_body; ?></p>
	</div>
</div>
