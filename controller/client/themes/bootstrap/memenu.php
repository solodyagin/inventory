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
<script>
$(document).ready(function () {
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
});
</script>
<script src="controller/client/js/memenu.js"></script>
