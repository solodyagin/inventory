<link rel="stylesheet" href="public/css/upload.css">
<script src="public/js/FileAPI/FileAPI.min.js"></script>
<script src="public/js/FileAPI/FileAPI.exif.js"></script>
<script src="public/js/jquery.fileapi.min.js"></script>
<script src="public/js/jcrop/jquery.Jcrop.min.js"></script>
<script src="public/js/statics/jquery.modal.js"></script>
<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row">
		<div class="col-xs-12 col-md-12 col-sm-12">
			<table id="list1"></table>
			<div id="pager1"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-md-8 col-sm-8">
			<table id="list2"></table>
			<div id="pager2"></div>
		</div>
		<div class="col-xs-12 col-md-4 col-sm-4">
			<table id="list3" style="visibility: hidden"></table>
			<div id="pager3"></div>
			<div align="center" id="btn_upload" class="btn btn-primary js-fileapi-wrapper" style="text-align: center; visibility: hidden">
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
	var addOptions = {top: 0, left: 0, width: 500, addCaption: 'Добавить запись', closeAfterAdd: true, closeOnEscape: true},
			$list1 = $('#list1'),
			$list2 = $('#list2'),
			$list3 = $('#list3'),
			$btnUpload = $('#btn_upload');
	$list1.jqGrid({
		url: 'knt/list',
		datatype: 'json',
		colNames: [' ', 'Id', 'Имя', 'ИНН', 'КПП', 'Потребитель', 'Поставщик', 'Контролировать', 'ERPCode', 'Комментарий', 'Действия'],
		colModel: [
			{name: 'active', index: 'active', width: 20, search: false},
			{name: 'id', index: 'id', width: 55, search: false, hidden: true},
			{name: 'name', index: 'name', width: 200, editable: true},
			{name: 'inn', index: 'inn', width: 100, editable: true},
			{name: 'kpp', index: 'kpp', width: 100, editable: true, hidden: true},
			{name: 'bayer', index: 'bayer', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: '1:0'}, search: false},
			{name: 'supplier', index: 'supplier', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: '1:0'}, search: false},
			{name: 'dog', index: 'dog', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: '1:0'}, search: false},
			{name: 'erpcode', index: 'erpcode', width: 100, editable: true, search: false, hidden: true},
			{name: 'comment', index: 'comment', width: 200, editable: true},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}, search: false}
		],
		autowidth: true,
		pager: '#pager1',
		sortname: 'name',
		sortorder: 'asc',
		scroll: 1,
		viewrecords: true,
		editurl: 'knt/change',
		caption: 'Справочник контрагентов',
		onSelectRow: function (ids) {
			$list3.css('visibility', 'hidden');
			$btnUpload.css('visibility', 'hidden');
			$list2.jqGrid('setGridParam', {
				url: 'contracts/list?idknt=' + ids,
				editurl: 'contracts/change?idknt=' + ids
			}).trigger('reloadGrid');
		}
	});
	$list1.jqGrid('setGridHeight', $(window).innerHeight() / 3);
	$list1.jqGrid('navGrid', '#pager1', {edit: false, add: true, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});
	$list1.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
	$list1.jqGrid('bindKeys', '');
	$list1.jqGrid('navButtonAdd', '#pager1', {
		caption: '<i class="fas fa-tag"></i>',
		title: 'Выбор колонок',
		buttonicon: 'none',
		onClickButton: function () {
			$list1.jqGrid('columnChooser', {
				width: 550,
				dialog_opts: {
					modal: true,
					minWidth: 470,
					height: 470
				},
				msel_opts: {
					dividerLocation: .5
				},
				done: function (perm) {
					if (perm) {
						this.jqGrid('remapColumns', perm, true);
						this.jqGrid('setGridWidth', null);
					}
				}
			});
		}
	});

	$list2.jqGrid({
		//url: 'contracts/list?idknt=' + ids,
		datatype: 'json',
		colNames: [' ', 'Id', 'Номер', 'Название', 'Начало', 'Конец', 'Рабочий', 'Комментарий', 'Действия'],
		colModel: [
			{name: 'active', index: 'active', width: 22, fixed: true, sortable: false, search: false},
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
			{name: 'work', index: 'work', width: 50, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: '1:0'}},
			{name: 'comment', index: 'comment', width: 200, editable: true},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}, search: false}
		],
		autowidth: true,
		pager: '#pager2',
		sortname: 'id',
		scroll: 1,
		viewrecords: true,
		sortorder: 'asc',
		//editurl: 'contracts/change?idknt=' + ids,
		caption: 'Заключенные договора',
		onSelectRow: function (ids) {
			$list3.css('visibility', 'visible');
			$btnUpload.css('visibility', 'visible');
			$btnUpload.fileapi('data', {'contractid': ids});
			$list3.jqGrid('setGridParam', {
				url: 'contractfiles/list?idcontract=' + ids,
				editurl: 'contractfiles/change?idcontract=' + ids
			}).trigger('reloadGrid');
		}
	});
	$list2.jqGrid('navGrid', '#pager2', {edit: true, add: true, del: false, search: false}, {}, addOptions, {}, {multipleSearch: false}, {closeOnEscape: true});

	$list3.jqGrid({
		//url: 'contractfiles/list?idcontract=' + ids,
		datatype: 'json',
		colNames: ['Id', 'Имя файла', 'Действия'],
		colModel: [
			{name: 'id', index: 'id', width: 55, hidden: true},
			{name: 'filename', index: 'filename', editable: true,
				formatter: function (cellvalue, options, rowObject) {
					return '<a target="_blank" href="contractfiles/download?id=' + options.rowId + '">' + cellvalue + '</a>';
				},
				unformat: function (cellvalue, options, cell) {
					return $('a', cell).text();
				}
			},
			{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}, search: false}
		],
		autowidth: true,
		height: 100,
		pager: '#pager3',
		sortname: 'id',
		scroll: 1,
		viewrecords: true,
		sortorder: 'asc',
		//editurl: 'contractfiles/change?idcontract=' + ids,
		caption: 'Прикрепленные файлы'
	});
	$list3.jqGrid('navGrid', '#pager3', {edit: false, add: false, del: false, search: false});

	$btnUpload.fileapi({
		url: 'contractfiles/upload',
		data: {'geteqid': 0},
		multiple: true,
		maxSize: 20 * FileAPI.MB,
		autoUpload: true,
		onFileComplete: function (evt, uiEvt) {
			if (uiEvt.result.msg !== 'error') {
				$list3.jqGrid().trigger('reloadGrid');
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
