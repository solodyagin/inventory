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

namespace app\views;

use core\config;
//use PDOException;
use core\db;
use core\dbexception;

$cfg = config::getInstance();
?>
<!DOCTYPE html>
<html lang="ru-RU">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<base href="<?= $cfg->rewrite_base; ?>">
		<!--FontAwesome-->
		<link rel="stylesheet" href="public/css/all.min.css">
		<!--jQuery-->
		<script src="public/js/jquery-1.11.0.min.js"></script>
		<!--Bootstrap-->
		<link rel="stylesheet" href="public/themes/<?= $cfg->theme; ?>/bootstrap.min.css">
		<script src="public/js/bootstrap.min.js"></script>
		<!--Localisation assistance for jQuery-->
		<script src="public/js/plugins/localisation/jquery.localisation-min.js"></script>
		<!--jQuery Form Plugin-->
		<script src="public/js/jquery.form.js"></script>
		<!--jqGrid-->
		<link rel="stylesheet" href="public/css/ui.jqgrid-bootstrap.css">
		<script src="public/js/i18n/grid.locale-ru.js"></script>
		<script src="public/js/jquery.jqGrid.min.js"></script>
		<!--Select2-->
		<link rel="stylesheet" href="public/css/select2.min.css">
		<link rel="stylesheet" href="public/css/select2-bootstrap.min.css">
		<script src="public/js/select2.full.min.js"></script>
		<script>
			$(function () {
				var fields = ['namenome'];

				$('form').submit(function () {
					var $form = $(this),
							error = false;
					$form.find(':input').each(function () {
						var $input = $(this);
						for (var i = 0; i < fields.length; i++) {
							if ($input.attr('name') === fields[i]) {
								if (!$input.val()) {
									error = true;
									$input.parent().addClass('has-error');
								} else {
									$input.parent().removeClass('has-error');
								}
							}
						}
					});
					if (error) {
						$('#messenger').addClass('alert alert-danger').html('Не все обязательные поля заполнены!').fadeIn('slow');
						return false;
					}
					return true;
				});

				$('#myForm').ajaxForm(function (msg) {
					if (msg !== 'ok') {
						$('#messenger').addClass('alert alert-danger').html(msg);
					} else {
						if (window.top) {
							window.top.$('#bmd_iframe').modal('hide');
							window.top.$('#grid1').jqGrid().trigger('reloadGrid');
						}
					}
				});

				$('.select2').select2({
					theme: 'bootstrap',
					width: '100%'
				});
			});
		</script>
	</head>
	<body style="font-size:<?= $cfg->fontsize; ?>;">
		<form id="myForm" enctype="multipart/form-data" action="route/deprecated/server/tmc/add_edit_tmc.php?step=add&id=" method="post" name="form1" target="_self">
			<div class="form-group">
				<label for="groupid" class="control-label">Группа:</label>
				<select class="select2 form-control" name="groupid" id="groupid">
					<?php
					try {
						$sql = 'select * from group_nome where active = 1 order by name';
						$rows = db::prepare($sql)->execute()->fetchAll();
						foreach ($rows as $row) {
							echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
						}
					} catch (PDOException $ex) {
						throw new dbexception('Не могу выбрать группу номенклатуры', 0, $ex);
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="vendorid" class="control-label">Производитель:</label>
				<select class="select2 form-control" name="vendorid" id="vendorid">
					<?php
					try {
						$sql = 'select * from vendor where active = 1 order by name';
						$arr = db::prepare($sql)->execute()->fetchAll();
						foreach ($arr as $row) {
							echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
						}
					} catch (PDOException $ex) {
						throw new dbexception('Не могу выбрать производителя', 0, $ex);
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="namenome" class="control-label">Наименование:</label>
				<input class="form-control" placeholder="Введите наименование номенклатуры" name="namenome" id="namenome" size="100" value="">
			</div>
			<div class="form-group">
				<button class="btn btn-primary" type="submit" name="Submit">Добавить</button>
			</div>
		</form>
		<div id="messenger"></div>
	</body>
</html>