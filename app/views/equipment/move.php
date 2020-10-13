<?php

namespace app\views;

use core\equipment;
use core\request;
use core\user;
use core\utils;
use core\config;

$user = user::getInstance();
$cfg = config::getInstance();

$req = request::getInstance();
$id = $req->get('id');

$tmptmc = new equipment();
$tmptmc->getById($id);
$dtpost = utils::MySQLDateTimeToDateTime($tmptmc->datepost);

$orgid = $tmptmc->orgid;
$placesid = $tmptmc->placesid;
$userid = $tmptmc->usersid;
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
	</head>
	<body style="font-size:<?= $cfg->fontsize; ?>;">
		<script>
			var orgid = '<?= $user->orgid; ?>';
			var placesid = '';
			var userid = '<?= $user->id; ?>';
			var orgid1 = '<?= $tmptmc->orgid; ?>';
			var placesid1 = '<?= $tmptmc->placesid; ?>';
			var userid1 = '<?= $tmptmc->usersid; ?>';

			$(function () {
				$('#myForm').ajaxForm(function (msg) {
					if (msg !== 'ok') {
						$('#messenger').html(msg);
					} else {
						if (window.top) {
							window.top.$('#bmd_iframe').modal('hide');
							window.top.$('#tbl_equpment').jqGrid().trigger('reloadGrid');
						}
					}
				});
			});
		</script>
		<div class="container-fluid">
			<div class="row">
				<div id="messenger"></div>
				<form id="myForm" enctype="multipart/form-data" action="route/deprecated/server/equipment/equipment_form.php?step=move&id=<?= $id; ?>" method="post" name="form1" target="_self">
					<div class="row-fluid">
						<div class="col-xs-12 col-md-12 col-sm-12">
							<div class="form-group">
								<label>Организация (куда):</label>
								<div id="sorg">
									<select class="select2" name="sorgid" id="sorgid">
										<?php
										$morgs = utils::getArrayOrgs();
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
										<input type="checkbox" id="tmcgo" name="tmcgo">Оргтехника в "пути"
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
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="col-xs-12 col-md-12 col-sm-12">
							<input class="btn btn-primary" type="submit" name="Submit" value="Сохранить">
						</div>
					</div>
				</form>
			</div>
		</div>
		<script>
			function updateChosen() {
				$('.select2').select2({width: '100%', theme: 'bootstrap'});
			}

			function getListUsers(orgid, userid) {
				$.get('route/deprecated/server/common/getlistusers.php?orgid=' + orgid + '&userid=' + userid, function (data) {
					$('#susers').html(data);
					updateChosen();
				});
			}

			function getListPlaces(orgid, placesid) {
				$.get('route/deprecated/server/common/getlistplaces.php?orgid=' + orgid + '&placesid=' + placesid, function (data) {
					$('#splaces').html(data);
					updateChosen();
				});
			}

			$('#sorgid').click(function () {
				$('#splaces').html('идет загрузка...');
				$('#susers').html('идет загрузка...');
				getListPlaces($('#sorgid :selected').val(), ''); // перегружаем список помещений организации
				getListUsers($('#sorgid :selected').val(), ''); // перегружаем пользователей организации
				updateChosen();
			});

			getListUsers(orgid, userid1);
			getListPlaces(orgid, placesid1);
			updateChosen();
		</script>
	</body>
</html>