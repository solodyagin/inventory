<?php
/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$id = GetDef('id');
$step = GetDef('step');
$comment = '';

echo "<script>orgid={$user->orgid};</script>";
echo "<script>placesid='';</script>";
echo "<script>userid={$user->id};</script>";

include_once(WUO_ROOT . '/class/equipment.php');

$tmptmc = new Tequipment;
$tmptmc->GetById($id);
$dtpost = MySQLDateTimeToDateTime($tmptmc->datepost);

$orgid = $tmptmc->orgid;
echo "<script>orgid1='{$tmptmc->orgid}';</script>";

$placesid = $tmptmc->placesid;
echo "<script>placesid1='{$tmptmc->placesid}';</script>";

$userid = $tmptmc->usersid;
echo "<script>userid1='{$tmptmc->usersid}';</script>";
?>
<script>
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
		<form id="myForm" enctype="multipart/form-data" action="index.php?route=/controller/server/equipment/equipment_form.php?step=move&id=<?php echo $id ?>" method="post" name="form1" target="_self">
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
									echo "<option value=\"$nid\">{$morgs[$i]['name']}</option>";
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
						<textarea class="form-control" name="comment"><?php echo $comment; ?></textarea>                
						<input class="form-control btn btn-primary" type="submit" name="Submit" value="Сохранить">
					</div>
				</div>  
			</div> 
		</form>
	</div>
</div>    
<script>
	function UpdateChosen() {
		for (var selector in config) {
			$(selector).chosen({width: '100%'});
			$(selector).chosen(config[selector]);
		}
	}
	function GetListUsers(orgid, userid) {
		$.get(route + 'controller/server/common/getlistusers.php?orgid=' + orgid + '&userid=' + userid, function (data) {
			$('#susers').html(data);
			UpdateChosen();
		});
	}
	function GetListPlaces(orgid, placesid) {
		$.get(route + 'controller/server/common/getlistplaces.php?orgid=' + orgid + '&placesid=' + placesid, function (data) {
			$('#splaces').html(data);
			UpdateChosen();
		});
	}
	$('#sorgid').click(function () {
		$('#splaces').html = 'идет загрузка...'; // заглушка. Зачем?? каналы счас быстрые
		$('#susers').html = 'идет загрузка...';
		GetListPlaces($('#sorgid :selected').val(), ''); // перегружаем список помещений организации
		GetListUsers($('#sorgid :selected').val(), ''); // перегружаем пользователей организации
		UpdateChosen();
	});
	GetListUsers(orgid, userid);
	GetListPlaces(orgid, placesid);
	UpdateChosen();
</script>    
