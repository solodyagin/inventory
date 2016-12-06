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

// Если пользователь - "Администратор"
if ($user->mode == 1):
	?>
	<script>
		$(function () {
			var fields = new Array('login', 'pass', 'email');
			$('form').submit(function () {
				var error = 0;
				$('form').find(':input').each(function () {
					for (var i = 0; i < fields.length; i++) {
						if ($(this).attr('name') == fields[i]) {
							if (!$(this).val()) {
								error = 1;
								$(this).parent().addClass('has-error');
							} else {
								$(this).parent().removeClass('has-error');
							}
						}
					}
				});
				if (error == 1) {
					$('#messenger').addClass('alert alert-danger');
					$('#messenger').html('Не все обязательные поля заполнены!');
					$('#messenger').fadeIn('slow');
					return false;
				}
				return true;
			});
		});

		$(document).ready(function () {
			$('#myForm').ajaxForm(function (msg) {
				if (msg != 'ok') {
					$('#messenger').html(msg);
				} else {
					$('#add_edit').html('');
					$('#add_edit').dialog('destroy');
					jQuery('#list2').jqGrid().trigger('reloadGrid');
				}
			});
		});
	</script>
	<div class="container-fluid">
		<div class="row">
			<form role="form" id="myForm" enctype="multipart/form-data" action="index.php?route=/controller/server/users/libre_users_form.php?step=add" method="post" name="form1" target="_self">
				<div class="form-group">
					<label for="orgid">Организация</label>
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
					<label for="mode">Права</label>
					<select name="mode" id="mode" class="chosen-select form-control">
						<option value="0" selected>Пользователь</option>
						<option value="1">Администратор</option>
					</select>
				</div>
				<div class="form-group">
					<input class="form-control" placeholder="Логин" name="login" id="login" value="">
					<input class="form-control" placeholder="Пароль" name="pass" id="pass"  type="password" value="">
					<input class="form-control" placeholder="Email" name="email" id="email" size="16" value="">
				</div>
				<div align="center">
					<input class="btn btn-default" type="submit" name="Submit" value="Сохранить">
				</div>
			</form>
			<div id="messenger"></div>
		</div>
	</div>
	<script>
		for (var selector in config) {
			$(selector).chosen(config[selector]);
		}
	</script>
	<?php
else:
	echo 'Нужны права администратора!';
endif;
