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
namespace app\views;

use core\user;
use core\config;

$user = user::getInstance();
$cfg = config::getInstance();
?>
<link rel="stylesheet" href="public/css/upload.css">
<link rel="stylesheet" href="public/js/skin/ui.dynatree.css">
<script src="public/js/jquery.dynatree.min.js"></script>
<script src="public/js/FileAPI/FileAPI.min.js"></script>
<script src="public/js/FileAPI/FileAPI.exif.js"></script>
<script src="public/js/jquery.fileapi.min.js"></script>
<script src="public/js/jcrop/jquery.Jcrop.min.js"></script>
<script src="public/js/statics/jquery.modal.js"></script>
<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row">
		<div class="col-xs-4 col-md-4 col-sm-4">
			<div id="tree"></div>
			<div class="form-inline">
				<p>
					<input name="foldername" id="foldername" type="text" placeholder="Имя папки" class="form-control">
					<?php if ($user->isAdmin() || $user->testRights([1,4])): ?>
						<button name="newfolder" id="newfolder" class="btn btn-small btn-success" type="button">Новая папка</button>
					<?php endif; ?>
					<?php if ($user->isAdmin() || $user->testRights([1,6])): ?>
						<button name="delfolder" id="delfolder" class="btn btn-small btn-danger" type="button">Удалить</button>
					<?php endif; ?>
				</p>
				<?php if ($user->isAdmin() || $user->testRights([1,4])): ?>
					<div align="center" id="simple-btn" class="btn btn-primary js-fileapi-wrapper" style="text-align:center;visibility:hidden">
						<div class="js-browse" align="center">
							<span class="upload-btn__txt">Загрузить файл</span>
							<input type="file" name="filedata">
						</div>
						<div class="js-upload" style="display: none">
							<div class="progress progress-success"><div class="js-progress bar"></div></div>
							<span align="center" class="btn-txt">Загружаю (<span class="js-size"></span>)</span>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="col-xs-8 col-md-8 col-sm-8">
			<table id="cloud_files"></table>
			<div id="cloud_files_pager"></div>
		</div>
	</div>
</div>
<script>
	function viewFileList(keyme) {
		$.jgrid.gridUnload('#cloud_files');
		$('#cloud_files').jqGrid({
			url: 'cloud/listfiles?cloud_dirs_id=' + keyme,
			datatype: 'json',
			colNames: ['Id', 'Скачать', 'Наименование документа', 'Дата', 'Размер', 'Действия'],
			colModel: [
				{name: 'id', index: 'id', width: 25, hidden: true},
				{name: 'ico', index: 'ico', width: 25, align: 'center'},
				{name: 'title', index: 'title', width: 265, editable: true},
				{name: 'dt', index: 'dt', width: 90},
				{name: 'sz', index: 'sz', width: 50},
				{name: 'myac', width: 80, fixed: true, sortable: false, resize: false,
					formatter: 'actions', formatoptions: {keys: true}}
			],
			autowidth: true,
			pager: '#cloud_files_pager',
			sortname: 'dt',
			scroll: 1,
			shrinkToFit: true,
			viewrecords: true,
			height: 200,
			sortorder: 'desc',
			editurl: 'cloud/listfiles?cloud_dirs_id=' + keyme,
			caption: 'Файлы для просмотра'
		});
	}

	$('#simple-btn').fileapi({
		url: 'cloud/uploadfiles',
		data: {'geteqid': 0},
		multiple: true,
		maxSize: 20 * FileAPI.MB,
		autoUpload: true,
		onFileComplete: function (evt, uiEvt) {
			if (uiEvt.result.msg !== 'error') {
				$('#cloud_files').jqGrid().trigger('reloadGrid');
			}
		},
		elements: {
			size: '.js-size',
			active: {show: '.js-upload', hide: '.js-browse'},
			progress: '.js-progress'
		}
	});

	function getTree() {
		$('#tree').dynatree({
			autoCollapse: false,
			minExpandLevel: 3,
			initAjax: {
				url: 'cloud/gettree'
			},
			onActivate: function (node) {
				selectedkey = node.data.key;
				viewFileList(selectedkey);
				$('#simple-btn').fileapi('data', {'selectedkey': selectedkey});
				$("#simple-btn").css('visibility', 'visible');
			},
			dnd: {
				onDragStart: function (node) {
					/** This function MUST be defined to enable dragging for the tree.
					 *  Return false to cancel dragging of node.
					 */
					//logMsg('tree.onDragStart(%o)', node);
					return true;
				},
				onDragStop: function (node) {
					/* This function is optional. */
					//logMsg('tree.onDragStop(%o)', node);
				},
				autoExpandMS: 1000,
				preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
				onDragEnter: function (node, sourceNode) {
					/** sourceNode may be null for non-dynatree droppables.
					 *  Return false to disallow dropping on node. In this case
					 *  onDragOver and onDragLeave are not called.
					 *  Return 'over', 'before, or 'after' to force a hitMode.
					 *  Return ['before', 'after'] to restrict available hitModes.
					 *  Any other return value will calc the hitMode from the cursor position.
					 */
					//logMsg('tree.onDragEnter(%o, %o)', node, sourceNode);
					return true;
				},
				onDragOver: function (node, sourceNode, hitMode) {
					/** Return false to disallow dropping this node.
					 *
					 */
					//logMsg('tree.onDragOver(%o, %o, %o)', node, sourceNode, hitMode);
					// Prevent dropping a parent below it's own child
					if (node.isDescendantOf(sourceNode)) {
						return false;
					}
					// Prohibit creating childs in non-folders (only sorting allowed)
					if (!node.data.isFolder && hitMode === 'over') {
						return 'after';
					}
				},
				onDrop: function (node, sourceNode, hitMode, ui, draggable) {
					/** This function MUST be defined to enable dropping of items on
					 * the tree.
					 */
					//logMsg('tree.onDrop(%o, %o, %s)', node, sourceNode, hitMode);
					sourceNode.move(node, hitMode);
					$.get('cloud/movefolder?nodekey=' + node.data.key + '&srnodekey=' + sourceNode.data.key, function (data) {
						if (data !== '') {
							$.notify(data);
						}
					});
					//SaveAllNodes(node, sourceNode);
					//expand the drop target
					//sourceNode.expand(true);
				},
				onDragLeave: function (node, sourceNode) {
					//logMsg('tree.onDragLeave(%o, %o)', node, sourceNode);
				}
			}
		});
	}

	selectedkey = '';
	getTree();

	$('#newfolder').click(function () {
		if ($('#foldername').val() === '') {
			$.notify('Введите имя папки!');
		} else {
			$('#tree').dynatree('destroy');
			$.get('cloud/addfolder?foldername=' + $('#foldername').val(), function (data) {
				if (data !== '') {
					$.notify(data);
				}
				getTree();
			});
		}
	});

	$('#delfolder').click(function () {
		if (selectedkey === '') {
			$.notify('Не выбрана папка!');
		} else {
			if (confirm('Вы подтверждаете удаление?')) {
				$('#tree').dynatree('destroy');
				$.get('cloud/delfolder?folderkey=' + selectedkey, function (data) {
					if (data !== '') {
						$.notify(data);
					}
					getTree();
				});
			}
		}
	});
</script>
