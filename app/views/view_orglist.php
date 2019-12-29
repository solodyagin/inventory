<?php
/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

/*
 * Справочники / Список организаций
 */

# Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$user = User::getInstance();
$cfg = Config::getInstance();

# Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Справочники / Список организаций"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ".
	</div>

<?php else: ?>

	<link rel="stylesheet" href="templates/<?= $cfg->theme; ?>/css/upload.css">
	<script src="js/FileAPI/FileAPI.min.js"></script>
	<script src="js/FileAPI/FileAPI.exif.js"></script>
	<script src="js/jquery.fileapi.min.js"></script>
	<script src="js/jcrop/jquery.Jcrop.min.js"></script>
	<script src="js/statics/jquery.modal.js"></script>
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="o_list"></table>
				<div id="o_pager"></div>
				<div id="simple-btn" class="btn btn-success js-fileapi-wrapper" style="visibility:hidden">
					<div class="js-browse">
						<span class="btn-txt">Загрузить схему в формате PNG</span>
						<input type="file" name="filedata">
					</div>
					<div class="js-upload" style="display: none">
						<div class="progress progress-success"><div class="js-progress bar"></div></div>
						<span class="btn-txt">Загрузка... (<span class="js-size"></span>)</span>
					</div>
				</div>
				<div id="pg_add_edit"></div>
			</div>
		</div>
	</div>
	<script>
		$('#o_list').jqGrid({
			url: 'route/controller/server/common/libre_org.php?org_status=list',
			datatype: 'json',
			colNames: [' ', 'Id', 'Имя организации', 'Действия'],
			colModel: [
				{name: 'active', index: 'active', width: 20},
				{name: 'id', index: 'id', width: 55},
				{name: 'name', index: 'name', width: 400, editable: true},
				{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
			],
			onSelectRow: function (ids) {
				$('#pg_add_edit').load('route/controller/server/common/getphotoorg.php?eqid=' + ids);
				$('#simple-btn').css('visibility', 'visible');
				$('#simple-btn').fileapi('data', {geteqid: ids});
			},
			autowidth: true,
			pager: '#o_pager',
			sortname: 'id',
			scroll: 1,
			viewrecords: true,
			sortorder: 'asc',
			editurl: 'route/controller/server/common/libre_org.php?org_status=edit',
			caption: 'Справочник организаций'
		});
		$('#o_list').jqGrid('setGridHeight', $(window).innerHeight() / 2);
		$('#o_list').jqGrid('navGrid', '#o_pager', {edit: false, add: true, del: false, search: false}, {}, {}, {}, {multipleSearch: false}, {closeOnEscape: true});

		$('#simple-btn').fileapi({
			url: 'route/controller/server/common/uploadimageorg.php',
			data: {'geteqid': 0},
			multiple: true,
			maxSize: 20 * FileAPI.MB,
			autoUpload: true,
			onFileComplete: function (evt, uiEvt) {
				if (uiEvt.result.msg != '') {
					$().toastmessage('showErrorToast', 'Ошибка загрузки файла:' + uiEvt.result.msg);
				}
			},
			elements: {
				size: '.js-size',
				active: {show: '.js-upload', hide: '.js-browse'},
				progress: '.js-progress'
			}
		});
	</script>

<?php endif;
