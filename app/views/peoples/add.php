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
<script>
	$(function () {
		var fields = ['login', 'pass', 'email'];
		$('form').submit(function () {
			var error = 0;
			$('form').find(':input').each(function () {
				for (var i = 0; i < fields.length; i++) {
					if ($(this).attr('name') === fields[i]) {
						if (!$(this).val()) {
							error = 1;
							$(this).parent().addClass('has-error');
						} else {
							$(this).parent().removeClass('has-error');
						}
					}
				}
			});
			if (error === 1) {
				$('#messenger').addClass('alert alert-danger').html('Не все обязательные поля заполнены!').fadeIn('slow');
				return false;
			}
			return true;
		});

		$('#myForm').ajaxForm(function (msg) {
			if (msg !== 'ok') {
				$('#messenger').html(msg);
			} else {
				$('#add_edit').html('');
				$('#add_edit').dialog('destroy');
				$('#list1').jqGrid().trigger('reloadGrid');
			}
		});
	});
</script>
<style>
	.input-group-btn{
		font-size: inherit;
	}
</style>
<script src="js/jquery.passgen.min.js"></script>
<script>
	$(function () {
		$('#pass').val($.passGen());
		$('#passgen').click(function () {
			$('#pass').val($.passGen());
		});
	});
</script>
<form id="myForm" enctype="multipart/form-data" action="route/deprecated/server/users/libre_users_form.php?step=add" method="post" class="form-horizontal">
	<div class="form-group">
		<label class="control-label col-sm-2">Организация:</label>
		<div class="col-sm-10">
			<select class="chosen-select form-control" name="orgid" id="orgid">
				<?php
				$morgs = GetArrayOrgs();
				for ($i = 0; $i < count($morgs); $i++) {
					$id = $morgs[$i]['id'];
					$sl = ($id == $cfg->defaultorgid) ? 'selected' : '';
					echo "<option value=\"$id\" $sl>{$morgs[$i]['name']}</option>";
				}
				?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2">Роль:</label>
		<div class="col-sm-10">
			<select name="mode" id="mode" class="chosen-select form-control">
				<option value="0" selected>Пользователь</option>
				<option value="1">Администратор</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2">Логин:</label>
		<div class="col-sm-10">
			<input class="form-control" placeholder="Логин" name="login" id="login" value="">
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2">Пароль:</label>
		<div class="col-sm-10">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Пароль" name="pass" id="pass" value="">
				<span class="input-group-btn">
					<button type="button" class="btn btn-default" id="passgen"><i class="fa fa-refresh"></i></button>
				</span>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2">Почта:</label>
		<div class="col-sm-10">
			<input class="form-control" placeholder="Email" name="email" id="email" size="16" value="">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button class="btn btn-primary" type="submit">Добавить</button>
		</div>
	</div>
</form>
<div id="messenger"></div>
<script>
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
</script>
