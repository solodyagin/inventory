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
 * Справочники / Контрагенты
 */

# Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$user = User::getInstance();
$cfg = Config::getInstance();

# Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Справочники / Контрагенты"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ".
	</div>

<?php else: ?>

	<link rel="stylesheet" href="templates/<?php echo $cfg->theme; ?>/css/upload.css">
	<script src="js/FileAPI/FileAPI.min.js"></script>
	<script src="js/FileAPI/FileAPI.exif.js"></script>
	<script src="js/jquery.fileapi.min.js"></script>
	<script src="js/jcrop/jquery.Jcrop.min.js"></script>
	<script src="js/statics/jquery.modal.js"></script>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="list2"></table>
				<div id="pager2"></div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="col-xs-12 col-md-8 col-sm-8">
				<table id="list3"></table>
				<div id="pager3"></div>
			</div>
			<div class="col-xs-12 col-md-4 col-sm-4">
				<table id="list4" style="visibility:hidden"></table>
				<div id="pager4"></div>
				<div align="center" id="simple-btn" class="btn btn-primary js-fileapi-wrapper" style="text-align:center;visibility:hidden">
					<div class="js-browse" align="center">
						<span class="btn-txt">Загрузить сканированный документ</span>
						<input type="file" name="filedata">
					</div>
					<div class="js-upload" style="display: none">
						<div class="progress progress-success"><div class="js-progress bar"></div></div>
						<span align="center" class="btn-txt">Загружаю (<span class="js-size"></span>)</span>
					</div>
				</div>
				<div id="status"></div>
			</div>
		</div>
	</div>
	<script>
		var addOptions = {
			top: 0,
			left: 0,
			width: 500,
			addCaption: 'Добавить запись',
			closeAfterAdd: true,
			closeOnEscape: true
		};
		$('#list2').jqGrid({
			url: 'route/controller/server/knt/libre_knt.php?org_status=list',
			datatype: 'json',
			colNames: [' ', 'Id', 'Имя', 'ИНН', 'КПП', 'Пок', 'Прод', 'Контролировать', 'ERPCode', 'Комментарий', 'Действия'],
			colModel: [
				{name: 'active', index: 'active', width: 20, search: false},
				{name: 'id', index: 'id', width: 55, search: false, hidden: true},
				{name: 'name', index: 'name', width: 200, editable: true},
				{name: 'INN', index: 'INN', width: 100, editable: true},
				{name: 'KPP', index: 'KPP', width: 100, editable: true, hidden: true},
				{name: 'bayer', index: 'bayer', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: 'Yes:No'}, search: false, hidden: true},
				{name: 'supplier', index: 'supplier', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: 'Yes:No'}, search: false, hidden: true},
				{name: 'dog', index: 'dog', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: 'Yes:No'}, search: false},
				{name: 'ERPCode', index: 'ERPCode', width: 100, editable: true, search: false, hidden: true},
				{name: 'comment', index: 'comment', width: 200, editable: true},
				{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}, search: false}
			],
			autowidth: true,
			pager: '#pager2',
			sortname: 'id',
			scroll: 1,
			viewrecords: true,
			sortorder: 'asc',
			editurl: 'route/controller/server/knt/libre_knt.php?org_status=edit',
			caption: 'Справочник контрагентов',
			onSelectRow: function (ids) {
				$('#list4').css('visibility', 'hidden');
				$('#simple-btn').css('visibility', 'hidden');
				$('#list3').jqGrid('setGridParam', {url: 'route/controller/server/knt/getcontrakts.php?idknt=' + ids});
				$('#list3').jqGrid('setGridParam', {editurl: 'route/controller/server/knt/getcontrakts.php?idknt=' + ids});
				$('#list3').jqGrid({
					url: 'route/controller/server/knt/getcontrakts.php?idknt=' + ids,
					datatype: 'json',
					colNames: [' ', 'Id', 'Номер', 'Название', 'Начало', 'Конец', 'Рабочий', 'Комментарий', 'Действия'],
					colModel: [
						{name: 'active', index: 'active', width: 20},
						{name: 'id', index: 'id', width: 55, hidden: true},
						{name: 'num', index: 'num', width: 50, editable: true},
						{name: 'name', index: 'name', width: 100, editable: true},
						{name: 'datestart', index: 'datestart', width: 100, editable: true, editoptions:
											{
												dataInit: function (el) {
													$(el).datepicker({
														dateFormat: 'dd.mm.yy',
														weekStart: 1,
														dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
														monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
													});
												}
											}
						},
						{name: 'dateend', index: 'dateend', width: 100, editable: true, editoptions:
											{
												dataInit: function (el) {
													$(el).datepicker({
														dateFormat: 'dd.mm.yy',
														weekStart: 1,
														dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
														monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
													});
												}
											}
						},
						{name: 'work', index: 'work', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: 'Yes:No'}},
						{name: 'comment', index: 'comment', width: 200, editable: true},
						{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}, search: false}
					],
					autowidth: true,
					pager: '#pager3',
					sortname: 'id',
					scroll: 1,
					viewrecords: true,
					sortorder: 'asc',
					editurl: 'route/controller/server/knt/getcontrakts.php?idknt=' + ids,
					caption: 'Заключенные договора',
					onSelectRow: function (ids) {
						$('#list4').css('visibility', 'visible');
						$('#simple-btn').css('visibility', 'visible');
						$('#simple-btn').fileapi('data', {'contractid': ids});
						$('#list4').jqGrid('setGridParam', {url: 'route/controller/server/knt/getfilescontrakts.php?idcontract=' + ids});
						$('#list4').jqGrid('setGridParam', {editurl: 'route/controller/server/knt/getfilescontrakts.php?idcontract=' + ids});
						$('#list4').jqGrid({
							url: 'route/controller/server/knt/getfilescontrakts.php?idcontract=' + ids,
							datatype: 'json',
							colNames: ['Id', 'Имя файла', 'Действия'],
							colModel: [
								{name: 'id', index: 'id', width: 55, hidden: true},
								{name: 'filename', index: 'filename', width: 100},
								{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}, search: false}
							],
							autowidth: true,
							height: 100,
							pager: '#pager4',
							sortname: 'id',
							scroll: 1,
							viewrecords: true,
							sortorder: 'asc',
							editurl: 'route/controller/server/knt/getfilescontrakts.php?idcontract=' + ids,
							caption: 'Прикрепленные файлы'
						}).trigger('reloadGrid');
						$('#list4').jqGrid('navGrid', '#pager4', {edit: false, add: false, del: false, search: false});
					}
				}).trigger('reloadGrid');
				$('#list3').jqGrid('navGrid', '#pager3', {edit: true, add: true, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});
			}
		});
		$('#list2').jqGrid('setGridHeight', $(window).innerHeight() / 3);
		$('#list2').jqGrid('navGrid', '#pager2', {edit: false, add: true, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});
		$('#list2').jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
		$('#list2').jqGrid('bindKeys', '');
		$('#list2').jqGrid('navButtonAdd', '#pager2', {
			caption: '<i class="fa fa-tag" aria-hidden="true"></i>',
			title: 'Выбор колонок',
			buttonicon: 'none',
			onClickButton: function () {
				$('#list2').jqGrid('columnChooser', {
					'done': function (perm) {
						if (perm) {
							this.jqGrid('remapColumns', perm, true);
							var outerwidth = $('#grid').width();
							$('#list2').setGridWidth(outerwidth);
						}
					}
				});
			}
		});
		$('#simple-btn').fileapi({
			url: 'route/controller/server/common/uploadanyfiles.php',
			data: {'geteqid': 0},
			multiple: true,
			maxSize: 20 * FileAPI.MB,
			autoUpload: true,
			onFileComplete: function (evt, uiEvt) {
				if (uiEvt.result.msg != 'error') {
					$('#list4').jqGrid().trigger('reloadGrid');
				} else {
					$().toastmessage('showErrorToast', 'Ошибка загрузки файла!');
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
