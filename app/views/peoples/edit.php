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

$id = GetDef('id');
$cfg = Config::getInstance();

$tmpuser = new BaseUser();
$tmpuser->getById($id);
$orgid = $tmpuser->orgid;
$login = $tmpuser->login;
$email = $tmpuser->email;
$mode = $tmpuser->mode;
unset($tmpuser);
?>
<script>
	$(function () {
		var fields = ['login', 'email'];
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
</script>
<style>
	#pass_gen, #pass_show {
		font: initial;
	}
</style>
<script src="public/js/jquery.passgen.min.js"></script>
<script>
	$(function () {
		$('#pass_gen').click(function () {
			$('#pass').val($.passGen());
		});
		$('#pass_show').click(function () {
			var $t = $(this);
			$t.toggleClass('active');
			if ($t.hasClass('active')) {
				$t.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
				$t.closest('.input-group').find('input').prop('type', 'text');
			} else {
				$t.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
				$t.closest('.input-group').find('input').prop('type', 'password');
			}
		});
	});
</script>
<form id="myForm" enctype="multipart/form-data" action="route/deprecated/server/users/libre_users_form.php?step=edit&id=<?= $id; ?>" method="post" class="form-horizontal">
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
				<option value="0" <?= ($mode == 0) ? 'selected' : ''; ?>>Пользователь</option>
				<option value="1" <?= ($mode == 1) ? 'selected' : ''; ?>>Администратор</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2">Логин:</label>
		<div class="col-sm-10">
			<input class="form-control" placeholder="Логин" name="login" id="login" value="<?= $login; ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2">Пароль:</label>
		<div class="col-sm-10">
			<div class="input-group">
				<input type="password" class="form-control" placeholder="Пароль" name="pass" id="pass" value="">
				<span class="input-group-btn">
					<button type="button" class="btn btn-default" id="pass_gen"><i class="fas fa-dice"></i></button>
					<button type="button" class="btn btn-default" id="pass_show"><i class="fas fa-eye-slash"></i></button>
				</span>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2">Почта:</label>
		<div class="col-sm-10">
			<input class="form-control" placeholder="Email" name="email" id="email" size="16" value="<?= $email; ?>">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button class="btn btn-primary" type="submit">Обновить</button>
		</div>
	</div>
</form>
<div id="messenger"></div>
<script>
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
</script>
