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

$cfg = Config::getInstance();
?>
<label for="orgs">Организация:</label>
<select class="chosen-select form-control" name="orgs" id="orgs">
	<?php
	for ($i = 0; $i < count($morgs); $i++) {
		$idorg = $morgs[$i]['id'];
		$nameorg = $morgs[$i]['name'];
		$sl = ($idorg == $cfg->defaultorgid) ? 'selected' : '';
		echo "<option value=\"$idorg\" $sl>$nameorg</option>";
	}
	?>
</select>
<label for="stl">Стиль:</label>
<select class="chosen-select form-control" name="stl" id="stl">
	<?php
	$sl = ($cfg->style == 'Bootstrap') ? 'selected' : '';
	echo "<option value=\"Bootstrap\" $sl>Bootstrap</option>";
	$sl = ($cfg->style == 'Normal') ? 'selected' : '';
	echo "<option value=\"Normal\" $sl>Normal</option>";
	?>
</select>
<label for="fontsize">Размер шрифта:</label>
<select class="chosen-select form-control" name="fontsize" id="fontsize">
	<option value="11px">11px</option>
	<option value="12px">12px</option>
	<option value="13px">13px</option>
	<option value="14px">14px</option>
</select>
<script>
	$(document).ready(function () {
		for (var selector in config) {
			$(selector).chosen(config[selector]);
		}
		$('#fontsize').val("<?= $cfg->fontsize; ?>").trigger('chosen:updated');
	});
</script>
<script src="templates/<?= $cfg->theme; ?>/assets/js/memenu.js"></script>
