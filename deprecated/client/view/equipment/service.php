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

// Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

//use PDOException;
use core\db;
use core\dbexception;
use core\request;
use core\utils;

$req = request::getInstance();
$eqid = $req->get('eqid');
$step = $req->get('step');

if ($step == 'edit') {
	try {
		$sql = 'select * from repair where id = :eqid';
		$row = db::prepare($sql)->execute([':eqid' => $eqid])->fetch();
		if ($row) {
			$kntid = $row['kntid'];
			$cost = $row['cost'];
			$dtpost = utils::MySQLDateTimeToDateTimeNoTime($row['dt']);
			echo "<script>dtpost='$dtpost';</script>";
			$dt = utils::MySQLDateTimeToDateTimeNoTime($row['dtend']);
			echo "<script>dt='$dt';step='edit';</script>";
			$comment = $row['comment'];
			$status = $row['status'];
			$userfrom = $row['userfrom'];
			$userto = $row['userto'];
			$doc = $row['doc'];
		}
	} catch (PDOException $ex) {
		throw new dbexception('Не получилось выбрать список ремонтов', 0, $ex);
	}
} else {
	$kntid = '-1';
	$cost = '0.0';
	$dtpost = '';
	$dt = '';
	$comment = '';
	$status = '1';
	$userfrom = '-1';
	$userto = '-1';
	$doc = '';
	echo <<<TXT
<script>
	var dtpost='$dtpost',
		dt='$dt',
		step='add';
</script>
TXT;
}

$optionsUsers = '<option value="-1">Не выбрано</option>';
try {
	$sql = <<<TXT
select
	users.id,
	users.login,
	users_profile.fio
from users
	inner join users_profile on users.id = users_profile.usersid
where users.active = 1
order by users.login
TXT;
	$rows = db::prepare($sql)->execute()->fetchAll();
	foreach ($rows as $row) {
		$rowid = $row['id'];
		$sl = ($rowid == $userfrom) ? 'selected' : '';
		$optionsUsers .= "<option value=\"$rowid\" $sl>{$row['fio']}</option>";
	}
} catch (PDOException $ex) {
	throw new dbexception('Не могу выбрать список пользователей', 0, $ex);
}
?>
<script>
	$(function () {
		var fields = ['dtpost', 'dt', 'kntid'];
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
				$('#pg_add_edit').empty().dialog('destroy');
				$('#workmen').jqGrid().trigger('reloadGrid');
				$('#tbl_rep').jqGrid().trigger('reloadGrid');
			}
		});
	});
</script>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="col-xs-12 col-md-12 col-sm-12">
			<div id="messenger"></div>
			<form role="form" id="myForm" enctype="multipart/form-data" action="route/deprecated/server/equipment/service.php?step=<?= $step; ?>&eqid=<?= $eqid; ?>" method="post" name="form1" target="_self">
				<label>Кто ремонтирует:</label>
				<div id="sorg1">
					<select class="select2" name="kntid" id="kntid">
						<?php
						$morgs = utils::getArrayKnt();
						for ($i = 0; $i < count($morgs); $i++) {
							$nid = $morgs[$i]['id'];
							$sl = ($nid == $kntid) ? 'selected' : '';
							echo "<option value=\"$nid\" $sl>{$morgs[$i]['name']}</option>";
						}
						?>
					</select>
				</div>
				<div class="row-fluid">
					<div class="col-xs-6 col-md-6 col-sm-6">
						<label>Начало ремонта:</label>
						<input class="form-control" name="dtpost" id="dtpost" value="<?= $dtpost; ?>">
						<label>Конец ремонта:</label>
						<input class="form-control" name="dt" id="dt" value="<?= $dt; ?>">
						<label>Стоимость ремонта:</label>
						<input class="form-control" name="cst" id="cst" value="<?= $cost; ?>">
					</div>
					<div class="col-xs-6 col-md-6 col-sm-6">
						<label>Отправитель:</label>
						<div id="susers1">
							<select class="select2 form-control" name="suserid1" id="suserid1">
								<?= $optionsUsers; ?>
							</select>
						</div>
						<label>Получатель:</label>
						<div id="susers2">
							<select class="select2 form-control" name="suserid2" id="suserid2">
								<?= $optionsUsers; ?>
							</select>
						</div>
						<label>Статус:</label>
						<select class="select2 form-control" name="status" id="status">
							<option value='1' <?= ($status == '1') ? 'selected' : ''; ?>>В сервисе</option>
							<option value='0' <?= ($status == '0') ? 'selected' : ''; ?>>Работает</option>
							<option value='2' <?= ($status == '2') ? 'selected' : ''; ?>>Есть заявка</option>
							<option value='3' <?= ($status == '3') ? 'selected' : ''; ?>>Списать</option>
						</select>
					</div>
				</div>
				<label>Документы:</label>
				<input class="form-control" name="doc" id="doc" size="14" class="span6" value="<?= $doc; ?>">
				<label>Комментарии:</label>
				<textarea class="form-control" name="comment"><?= $comment; ?></textarea>
				<div class="form-group">
					<input class="btn btn-primary" type="submit" name="Submit" value="Сохранить">
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$(function(){
		var $dt = $('#dt'),
			$dtpost = $('#dtpost');

		$dt.datepicker();
		$dt.datepicker('option', 'dateFormat', 'dd.mm.yy');
		if (step !== 'edit') {
			$dt.datepicker('setDate', '0');
		} else {
			$dt.datepicker('setDate', dt);
		}

		$dtpost.datepicker();
		$dtpost.datepicker('option', 'dateFormat', 'dd.mm.yy');
		if (step !== 'edit') {
			$dtpost.datepicker('setDate', '0');
		} else {
			$dtpost.datepicker('setDate', dtpost);
		}

		$('#status').change(function () {
			$dt.datepicker('show');
		});

		$('.select2').select2({width: '100%', theme: 'bootstrap'});
	});
</script>
