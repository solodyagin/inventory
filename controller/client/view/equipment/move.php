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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

$user = User::getInstance();

$id = GetDef('id');

$tmptmc = new Equipment();
$tmptmc->GetById($id);
$dtpost = MySQLDateTimeToDateTime($tmptmc->datepost);

$orgid = $tmptmc->orgid;
$placesid = $tmptmc->placesid;
$userid = $tmptmc->usersid;
?>
<script>
	var orgid = '<?= $user->orgid; ?>';
	var placesid = '';
	var userid = '<?= $user->id; ?>';
	var orgid1 = '<?= $tmptmc->orgid; ?>';
	var placesid1 = '<?= $tmptmc->placesid; ?>';
	var userid1 = '<?= $tmptmc->usersid; ?>';

	$(document).ready(function () {
		$('#myForm').ajaxForm(function (msg) {
			if (msg != 'ok') {
				$('#messenger').html(msg);
			} else {
				$('#pg_add_edit').html('');
				$('#pg_add_edit').dialog('destroy');
				jQuery('#tbl_equpment').jqGrid().trigger('reloadGrid');
			}
		});
	});
</script>
<div class="container-fluid">
	<div class="row">
		<div id="messenger"></div>
		<form id="myForm" enctype="multipart/form-data" action="route/controller/server/equipment/equipment_form.php?step=move&id=<?= $id; ?>" method="post" name="form1" target="_self">
			<div class="row-fluid">
				<div class="col-xs-12 col-md-12 col-sm-12">
					<div class="form-group">
						<label>Организация (куда):</label>
						<div id="sorg">
							<select class="chosen-select" name="sorgid" id="sorgid">
								<?php
								$morgs = GetArrayOrgs();
								for ($i = 0; $i < count($morgs); $i++) {
									$nid = $morgs[$i]['id'];
									$sl = ($nid == $user->orgid) ? 'selected' : '';
									echo "<option value=\"$nid\" $sl>{$morgs[$i]['name']}</option>";
								}
								?>
							</select>
						</div>
						<label>Помещение:</label>
						<div name="splaces" id="splaces">идет загрузка...</div>
						<label>Сотрудник:</label>
						<div name="susers" id="susers">идет загрузка...</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" id="tmcgo" name="tmcgo">ТМЦ в "пути"
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="col-xs-12 col-md-12 col-sm-12">
					<div class="form-group">
						<label>Комментарий: </label>
						<textarea class="form-control" name="comment"></textarea>
						<input class="form-control btn btn-primary" type="submit" name="Submit" value="Сохранить">
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
	function updateChosen() {
		for (var selector in config) {
			$(selector).chosen({width: '100%'});
			$(selector).chosen(config[selector]);
		}
	}
	function getListUsers(orgid, userid) {
		$.get('route/controller/server/common/getlistusers.php?orgid=' + orgid + '&userid=' + userid, function (data) {
			$('#susers').html(data);
			updateChosen();
		});
	}
	function getListPlaces(orgid, placesid) {
		$.get('route/controller/server/common/getlistplaces.php?orgid=' + orgid + '&placesid=' + placesid, function (data) {
			$('#splaces').html(data);
			updateChosen();
		});
	}
	$('#sorgid').click(function () {
		$('#splaces').html = 'идет загрузка...'; // заглушка. Зачем?? каналы счас быстрые
		$('#susers').html = 'идет загрузка...';
		getListPlaces($('#sorgid :selected').val(), ''); // перегружаем список помещений организации
		getListUsers($('#sorgid :selected').val(), ''); // перегружаем пользователей организации
		updateChosen();
	});
	getListUsers(orgid, userid1);
	getListPlaces(orgid, placesid1);
	updateChosen();
</script>
