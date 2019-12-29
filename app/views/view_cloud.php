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
 * Хранилище документов
 */

# Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

# Проверка: включен ли модуль "cloud"?
$mod = new Mod();
$active = $mod->IsActive('cloud');
unset($mod);
if (!$active):
	?>
	<div class="alert alert-info">
		Модуль "Хранилище документов" выключен
	</div>
	<?php
	exit;
endif;

$user = User::getInstance();
$cfg = Config::getInstance();

# Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1,3,4,6')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Хранилище документов"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ", "Просмотр", "Добавление", "Удаление"
	</div>

<?php else: ?>

	<link rel="stylesheet" href="templates/<?= $cfg->theme; ?>/css/upload.css">
	<link rel="stylesheet" href="js/skin/ui.dynatree.css">
	<script src="js/jquery.dynatree.min.js"></script>
	<script src="js/FileAPI/FileAPI.min.js"></script>
	<script src="js/FileAPI/FileAPI.exif.js"></script>
	<script src="js/jquery.fileapi.min.js"></script>
	<script src="js/jcrop/jquery.Jcrop.min.js"></script>
	<script src="js/statics/jquery.modal.js"></script>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="col-xs-4 col-md-4 col-sm-4">
				<div id="tree"></div>
				<div class="form-inline">
					<p>
						<input name="foldername" id="foldername" type="text" placeholder="Имя папки" class="form-control">
						<?php if ($user->isAdmin() || $user->TestRoles('1,4')): ?>
							<button name="newfolder" id="newfolder" class="btn btn-small btn-success" type="button">Новая папка</button>
						<?php endif; ?>
						<?php if ($user->isAdmin() || $user->TestRoles('1,6')): ?>
							<button name="delfolder" id="delfolder" class="btn btn-small btn-danger" type="button">Удалить</button>
						<?php endif; ?>
					</p>
					<?php if ($user->isAdmin() || $user->TestRoles('1,4')): ?>
						<div align="center" id="simple-btn" class="btn btn-primary js-fileapi-wrapper" style="text-align:center;visibility:hidden">
							<div class="js-browse" align="center">
								<span class="btn-txt">Загрузить файл</span>
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
		function ViewFileList(keyme) {
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
				if (uiEvt.result.msg != 'error') {
					$('#cloud_files').jqGrid().trigger('reloadGrid');
				}
			},
			elements: {
				size: '.js-size',
				active: {show: '.js-upload', hide: '.js-browse'},
				progress: '.js-progress'
			}
		});

		function GetTree() {
	// --- Initialize first Dynatree -------------------------------------------
			$('#tree').dynatree({
				autoCollapse: false,
				minExpandLevel: 3,
				initAjax: {
					url: 'cloud/gettree'
				},
				onActivate: function (node) {
					selectedkey = node.data.key;
					ViewFileList(selectedkey);
					$('#simple-btn').fileapi('data', {'selectedkey': selectedkey});
					$("#simple-btn").css('visibility', 'visible');
				},
				onLazyRead: function (node) {
					// Mockup a slow reqeuest ...
					node.appendAjax({
						url: 'sample-data2.json',
						debugLazyDelay: 750 // don't do this in production code
					});
				},
				dnd: {
					onDragStart: function (node) {
						/** This function MUST be defined to enable dragging for the tree.
						 *  Return false to cancel dragging of node.
						 */
						logMsg('tree.onDragStart(%o)', node);
						return true;
					},
					onDragStop: function (node) {
						// This function is optional.
						logMsg('tree.onDragStop(%o)', node);
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
						logMsg('tree.onDragEnter(%o, %o)', node, sourceNode);
						return true;
					},
					onDragOver: function (node, sourceNode, hitMode) {
						/** Return false to disallow dropping this node.
						 *
						 */
						logMsg('tree.onDragOver(%o, %o, %o)', node, sourceNode, hitMode);
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
						logMsg('tree.onDrop(%o, %o, %s)', node, sourceNode, hitMode);
						sourceNode.move(node, hitMode);
						$.get('cloud/movefolder?nodekey=' + node.data.key + '&srnodekey=' + sourceNode.data.key, function (data) {
							if (data != '') {
								$().toastmessage('showWarningToast', data);
							}
						});

						//SaveAllNodes(node, sourceNode);
						//expand the drop target
						//sourceNode.expand(true);
					},
					onDragLeave: function (node, sourceNode) {
						logMsg('tree.onDragLeave(%o, %o)', node, sourceNode);
					}
				}
			});
		}

		selectedkey = '';
		GetTree();

		$('#newfolder').click(function () {
			if ($('#foldername').val() == '') {
				$().toastmessage('showWarningToast', 'Введите имя папки!');
			} else {
				$('#tree').dynatree('destroy');
				$.get('cloud/addfolder?foldername=' + $('#foldername').val(), function (data) {
					if (data != '') {
						$().toastmessage('showWarningToast', data);
					}
					GetTree();
				});
			}
		});

		$('#delfolder').click(function () {
			if (selectedkey == '') {
				$().toastmessage('showWarningToast', 'Не выбрана папка!');
			} else {
				if (confirm('Вы подтверждаете удаление?')) {
					$('#tree').dynatree('destroy');
					$.get('cloud/delfolder?folderkey=' + selectedkey, function (data) {
						if (data != '') {
							$().toastmessage('showWarningToast', data);
						}
						GetTree();
					});
				}
			}
		});
	</script>

<?php endif;
