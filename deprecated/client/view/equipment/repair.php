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

use core\request;
use core\utils;

$req = request::getInstance();
$eqid = $req->get('eqid');
$step = $req->get('step');
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
				$('#tbl_equpment').jqGrid().trigger('reloadGrid');
				$('#tbl_rep').jqGrid().trigger('reloadGrid');
			}
		});
	});
</script>
<div class="container-fluid">
	<div class="row">
		<div id="messenger"></div>

		<form id="myForm" enctype="multipart/form-data" action="route/deprecated/server/equipment/repair.php?step=add&eqid=<?= $eqid; ?>" method="post" name="form1" target="_self">
			<label>Кто ремонтирует:</label>
			<div class="form-group">
				<select class="select2" name="kntid" id="kntid">
					<?php
					$knts = utils::getArrayKnt();
					for ($i = 0; $i < count($knts); $i++) {
						echo "<option value=\"{$knts[$i]['id']}\">{$knts[$i]['name']}</option>";
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<div class="col-xs-6 col-md-6 col-sm-6">
					<label>Начало ремонта:</label>
					<input class="form-control" name="dtpost" id="dtpost" size="14">
					<label>Конец ремонта:</label>
					<input class="form-control" name="dt" id="dt" size="14">
				</div>
				<div class="col-xs-6 col-md-6 col-sm-6">
					<label>Стоимость ремонта:</label>
					<input class="form-control" name="cst" id="cst">
					<label>Статус:</label>
					<select class="form-control" name="status" id="status">
						<option value="1">В ремонте</option>
						<option value="0">Ремонт завершен</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label>Комментарии:</label>
				<textarea class="form-control" name="comment"></textarea>
			</div>
			<div class="form-group">
				<input class="btn btn-primary" type="submit" name="Submit" value="Сохранить">
			</div>
		</form>
	</div>
</div>
<script>
	var $dtpost = $('#dtpost'),
		$dt = $('#dt');

	$dtpost.datepicker();
	$dtpost.datepicker('option', 'dateFormat', 'dd.mm.yy');
	$dtpost.datepicker('setDate', '0');

	$dt.datepicker();
	$dt.datepicker('option', 'dateFormat', 'dd.mm.yy');
	$dt.datepicker('setDate', '0');

	$('#status').change(function () {
		$dt.datepicker('show');
	});

	$(function(){
		$('.select2').select2({width: '100%', theme: 'bootstrap'});
	});
</script>
