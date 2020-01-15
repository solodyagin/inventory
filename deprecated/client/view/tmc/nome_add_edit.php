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

/* Проверка прав */
$user = User::getInstance();
(($user->isAdmin()) || $user->TestRights([1,4,5,6])) or die('Недостаточно прав');

$step = GetDef('step');
$id = GetDef('id');
$name = '';
$vendorid = '';
$groupid = '';

if ($step == 'edit') {
	$sql = 'SELECT * FROM nome WHERE id = :id';
	try {
		$row = DB::prepare($sql)->execute([':id' => $id])->fetch();
		if ($row) {
			$groupid = $row['groupid'];
			$vendorid = $row['vendorid'];
			$name = $row['name'];
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать номенклатуру', 0, $ex);
	}
}
?>
<script>
	$(function () {
		var fields = ['namenome'];
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
				$('#messenger').addClass('alert alert-danger').html(msg);
			} else {
				$('#add_edit').html('').dialog('destroy');
				$('#list1').jqGrid().trigger('reloadGrid');
			}
		});
	});
</script>
<form class="form-horizontal" role="form" id="myForm" enctype="multipart/form-data" action="route/deprecated/server/tmc/add_edit_tmc.php?step=<?= "$step&id=$id"; ?>" method="post" name="form1" target="_self">
	<div class="form-group">
		<label for="groupid" class="col-sm-3 control-label">Группа:</label>
		<div class="col-sm-9">
			<select class="chosen-select form-control" name="groupid" id="groupid">
				<?php
				$sql = 'SELECT * FROM group_nome WHERE active = 1 ORDER BY name';
				try {
					$arr = DB::prepare($sql)->execute()->fetchAll();
					foreach ($arr as $row) {
						$vl = $row['id'];
						$sl = ($row['id'] == $groupid) ? 'selected' : '';
						echo "<option value=\"$vl\" $sl>{$row['name']}</option>";
					}
				} catch (PDOException $ex) {
					throw new DBException('Не могу выбрать группу номенклатуры', 0, $ex);
				}
				?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="vendorid" class="col-sm-3 control-label">Производитель:</label>
		<div class="col-sm-9">
			<select class="chosen-select form-control" name="vendorid" id="vendorid">
				<?php
				$sql = 'SELECT * FROM vendor WHERE active = 1 ORDER BY name';
				try {
					$arr = DB::prepare($sql)->execute()->fetchAll();
					foreach ($arr as $row) {
						$vl = $row['id'];
						$sl = ($row['id'] == $vendorid) ? 'selected' : '';
						echo "<option value=\"$vl\" $sl>{$row['name']}</option>";
					}
				} catch (PDOException $ex) {
					throw new DBException('Не могу выбрать производителя', 0, $ex);
				}
				?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="namenome" class="col-sm-3 control-label">Наименование:</label>
		<div class="col-sm-9">
			<input class="form-control" placeholder="Введите наименование номенклатуры" name="namenome" id="namenome" size="100" value="<?= $name; ?>">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button class="btn btn-primary" type="submit" name="Submit">Сохранить</button>
		</div>
	</div>
</form>
<div id="messenger"></div>
<script>
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
</script>
