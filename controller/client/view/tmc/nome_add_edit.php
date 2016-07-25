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

// Проверка прав
if (!$user->TestRoles('1,4,5,6')) {
	die('Нужны права администратора!');
}

$step = GetDef('step');
$id = GetDef('id');
$name = '';
$vendorid = '';
$groupid = '';

if ($step == 'edit') {
	$sql = "SELECT * FROM nome WHERE id = '$id'";
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Неверный запрос : ' . mysqli_error($sqlcn->idsqlconnection));
	while ($row = mysqli_fetch_array($result)) {
		$groupid = $row['groupid'];
		$vendorid = $row['vendorid'];
		$name = $row['name'];
	}
}
?>
<script>
	$(function () {
		var field = new Array('namenome');//поля обязательные
		$('form').submit(function () {// обрабатываем отправку формы
			var error = 0; // индекс ошибки
			$('form').find(':input').each(function () {// проверяем каждое поле в форме
				for (var i = 0; i < field.length; i++) { // если поле присутствует в списке обязательных
					if ($(this).attr('name') == field[i]) { //проверяем поле формы на пустоту
						if (!$(this).val()) {// если в поле пустое
							$(this).css('border', 'red 1px solid');// устанавливаем рамку красного цвета
							error = 1;// определяем индекс ошибки
						} else {
							$(this).css('border', 'gray 1px solid');// устанавливаем рамку обычного цвета
						}
					}
				}
			});
			if (error == 0) { // если ошибок нет то отправляем данные
				return true;
			} else {
				$('#messenger').html('Не все обязательные поля заполнены!');
				$('#messenger').fadeIn('slow');
				return false; //если в форме встретились ошибки , не  позволяем отослать данные на сервер.
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
<div id="messenger"></div>
<form class="form-horizontal" role="form" id="myForm" enctype="multipart/form-data" action="index.php?route=/controller/server/tmc/add_edit_tmc.php?step=<?php echo "$step&id=$id"; ?>" method="post" name="form1" target="_self">
	<div class="form-group">
		<label for="groupid" class="col-sm-3 control-label">Группа</label>
		<div class="col-sm-9">
			<select class="chosen-select form-control" name="groupid" id="groupid">
				<?php
				$result = $sqlcn->ExecuteSQL("SELECT * FROM group_nome WHERE active = 1 ORDER BY name");
				while ($row = mysqli_fetch_array($result)) {
					$vl = $row['id'];
					$sl = ($row['id'] == $groupid) ? 'selected' : '';
					echo "<option value=\"$vl\" $sl>{$row['name']}</option>";
				}
				?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="vendorid" class="col-sm-3 control-label">Производитель</label>
		<div class="col-sm-9">
			<select class="chosen-select form-control" name="vendorid" id="vendorid">
				<?php
				$result = $sqlcn->ExecuteSQL("SELECT * FROM vendor WHERE active = 1 ORDER BY name");
				while ($row = mysqli_fetch_array($result)) {
					$vl = $row['id'];
					$sl = ($row['id'] == $vendorid) ? 'selected' : '';
					echo "<option value=\"$vl\" $sl>{$row['name']}</option>";
				}
				?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="namenome" class="col-sm-3 control-label">Наименование</label>
		<div class="col-sm-9">
			<input class="form-control" placeholder="Введите наименование номенклатуры" name="namenome" id="namenome" size="100" value="<?php echo $name; ?>">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button class="btn btn-primary" type="submit" name="Submit">Сохранить</button>
		</div>
	</div>
</form>
<script>
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
</script>
