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

/* Запрещаем прямой вызов скрипта. */
defined('SITE_EXEC') or die('Доступ запрещён');

$cfg = Config::getInstance();
?>
<div class="form-group">
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
</div>
<div class="form-group">
	<label for="fontsize">Размер шрифта:</label>
	<select class="chosen-select form-control" name="fontsize" id="fontsize">
		<option value="11px">11px</option>
		<option value="12px">12px</option>
		<option value="13px">13px</option>
		<option value="14px">14px</option>
	</select>
</div>
<script>
	$(function () {
		for (var selector in config) {
			$(selector).chosen(config[selector]);
		}
		$('#fontsize').val("<?= $cfg->fontsize; ?>").trigger('chosen:updated');
	});
</script>
<script src="templates/<?= $cfg->theme; ?>/assets/js/memenu.js"></script>
