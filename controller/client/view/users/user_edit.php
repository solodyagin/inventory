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

$id = GetDef('id');

// Если пользователь - "Администратор"
if ($user->mode == '1'):
	$tmpuser = new Tusers;
	$tmpuser->GetById($id);
	$orgid = $tmpuser->orgid;
	$login = $tmpuser->login;
	$email = $tmpuser->email;
	$mode = $tmpuser->mode;
	unset($tmpuser);
	?>
	<script>
		$(function () {
			var field = new Array('login', 'email'); // поля обязательные
			$('form').submit(function () { // обрабатываем отправку формы
				var error = 0; // индекс ошибки
				$('form').find(':input').each(function () { // проверяем каждое поле в форме
					for (var i = 0; i < field.length; i++) { // если поле присутствует в списке обязательных
						if ($(this).attr('name') == field[i]) { // проверяем поле формы на пустоту
							if (!$(this).val()) { // если в поле пустое
								$(this).css('border', 'red 1px solid'); // устанавливаем рамку красного цвета
								error = 1; // определяем индекс ошибки
							} else {
								$(this).css('border', 'gray 1px solid'); // устанавливаем рамку обычного цвета
							}
						}
					}
				});
				if (error == 0) { // если ошибок нет то отправляем данные
					return true;
				} else {
					var err_text = 'Не все обязательные поля заполнены!';
					$('#messenger').addClass('alert alert-error');
					$('#messenger').html(err_text);
					$('#messenger').fadeIn('slow');
					return false; // если в форме встретились ошибки , не  позволяем отослать данные на сервер.
				}
			});
		});

		$(document).ready(function () {
			// навесим на форму 'myForm' обработчик отлавливающий сабмит формы и передадим функцию callback.
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
			<form role="form" id="myForm" enctype="multipart/form-data" action="index.php?route=/controller/server/users/libre_users_form.php?step=edit&id=<?php echo $id; ?>" method="post" name="form1" target="_self">
				<div class="form-group">
					<label for="orgid">Организация</label>
					<select class="form-control" name="orgid" id="orgid">
						<?php
						$morgs = GetArrayOrgs();
						for ($i = 0; $i < count($morgs); $i++) {
							$idorg = $morgs[$i]['id'];
							$sl = ($idorg == $cfg->defaultorgid) ? 'selected' : '';
							echo "<option value=\"$idorg\" $sl>{$morgs[$i]['name']}</option>";
						}
						?>
					</select>
					<label for="mode">Организация</label>
					<select name="mode" id="mode" class="form-control">
						<option value="0" <?php echo ($mode == 0) ? 'selected' : ''; ?>>Пользователь</option>
						<option value="1" <?php echo ($mode == 1) ? 'selected' : ''; ?>>Администратор</option>
					</select>
				</div>
				<div class="form-group">
					<input class="form-control" placeholder="Логин" name="login" id="login" value="<?php echo $login; ?>">
					<input class="form-control" placeholder="Пароль" name="pass" id="pass" type="password" value="">
					<input class="form-control" placeholder="Email" name="email" id="email" size="16" value="<?php echo $email; ?>">
				</div>
				<div align="center">
					<input class="btn btn-default" type="submit" name="Submit" value="Сохранить">
				</div>
			</form>
			<div id="messenger"></div>
		</div>
	</div>
	<?php
else:
	echo 'Нужны права администратора!';
endif;
