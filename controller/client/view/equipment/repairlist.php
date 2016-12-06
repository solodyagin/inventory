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

$id = GetDef('id');
?>
<table id="list_rep"></table>
<div id="pager_rep"></div>
<div id="comment_rep"></div>
<script>repid = "<?php echo $id; ?>";</script>
<script src="controller/client/js/repair.js"></script>
