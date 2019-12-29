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
 * Журналы / Новости
 */

# Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$user = User::getInstance();
$cfg = Config::getInstance();

# Проверка: если не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Журналы / Новости"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="list2"></table>
				<div id="pager2"></div>
				<div id="pg_add_edit"></div>
			</div>
		</div>
	</div>
	<script>
		// загружаем в таблицу #list2 список новостей
		$('#list2').jqGrid({
			url: 'route/controller/server/news/news.php',
			datatype: 'json',
			colNames: ['Id', 'Дата', 'Заголовок', 'Закреплено', 'Действия'],
			colModel: [
				{name: 'id', index: 'id', width: 55, editable: false},
				{name: 'dt', index: 'dt', width: 60, editable: false},
				{name: 'title', index: 'title', width: 200, editable: true},
				{name: 'stiker', index: 'stiker', width: 200, editable: true, formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: '1:0'}},
				{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions', formatoptions: {keys: true}}
			],
			autowidth: true,
			pager: '#pager2',
			sortname: 'dt',
			height: 480,
			rowNum: 30,
			viewrecords: true,
			sortorder: 'desc',
			editurl: 'route/controller/server/news/news.php',
			caption: 'Новости'
		});

		// загружаем навигационную панель
		$('#list2').jqGrid('navGrid', '#pager2', {edit: false, add: false, del: false, search: false});

		// добавляем в таблицу кнопку "Добавить"
		$('#list2').jqGrid('navButtonAdd', '#pager2', {
			caption: '<i class="fa fa-plus-circle" aria-hidden="true"></i>',
			title: 'Добавить',
			buttonicon: 'none',
			onClickButton: function () {
				$('#pg_add_edit').empty();
				$('#pg_add_edit').dialog({
					autoOpen: false,
					height: 600,
					width: 800,
					modal: false,
					title: 'Добавление новости',
					open: function () {
						$(this).load('route/controller/client/view/news/news.php?step=add');
					}
				});
				$('#pg_add_edit').dialog('open');
			}
		});

		// добавляем в таблицу кнопку "Отредактировать"
		$('#list2').jqGrid('navButtonAdd', '#pager2', {
			caption: '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
			title: 'Отредактировать',
			buttonicon: 'none',
			onClickButton: function () {
				var gsr = $('#list2').jqGrid('getGridParam', 'selrow');
				if (gsr) {
					$('#pg_add_edit').empty();
					$('#pg_add_edit').dialog({
						autoOpen: false,
						height: 600,
						width: 800,
						modal: false,
						title: 'Редактирование новости',
						open: function () {
							$(this).load('route/controller/client/view/news/news.php?step=edit&id=' + gsr);
						}
					});
					$('#pg_add_edit').dialog('open');
				} else {
					$().toastmessage('showWarningToast', 'Сначала выберите строку!');
				}
			}
		});
	</script>

<?php endif;
