<?php
/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$cfg = Config::getInstance();
$user = User::getInstance();

$base_href = $cfg->rewrite_base;
?>
<!-- saved from url=(0014)about:internet -->
<!DOCTYPE html>
<html lang="ru-RU">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Учет ТМЦ в организации">
		<meta name="author" content="(c) 2011-2016 by Gribov Pavel">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?= $cfg->sitename; ?></title>
		<base href="<?= $base_href; ?>">
		<link rel="icon" href="favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="templates/<?= $cfg->theme; ?>/css/jquery-ui.min.css">
		<link rel="stylesheet" href="templates/<?= $cfg->theme; ?>/css/ui.multiselect.css">
		<link rel="stylesheet" href="templates/<?= $cfg->theme; ?>/css/bootstrap.min.css">
		<?php if ($cfg->style == 'Bootstrap'): ?>
			<link rel="stylesheet" href="templates/<?= $cfg->theme; ?>/css/ui.jqgrid-bootstrap.css">
		<?php elseif ($cfg->style == 'Normal'): ?>
			<link rel="stylesheet" href="templates/<?= $cfg->theme; ?>/css/ui.jqgrid.css">
		<?php endif; ?>
		<link rel="stylesheet" href="templates/<?= $cfg->theme; ?>/css/chosen.css">
		<link rel="stylesheet" href="templates/<?= $cfg->theme; ?>/css/jquery.toastmessage-min.css">
		<link rel="stylesheet" href="templates/<?= $cfg->theme; ?>/css/font-awesome.min.css">
		<link rel="stylesheet" href="templates/<?= $cfg->theme; ?>/css/common.css">
		<script src="templates/<?= $cfg->theme; ?>/js/jquery-1.11.0.min.js"></script>
		<script src="templates/<?= $cfg->theme; ?>/js/jquery-ui.min.js"></script>
		<script src="js/jquery.mmenu.min.all.js"></script>
		<script src="templates/<?= $cfg->theme; ?>/js/plugins/localisation/jquery.localisation-min.js"></script>
		<script src="templates/<?= $cfg->theme; ?>/js/ui.multiselect.js"></script>
		<script src="templates/<?= $cfg->theme; ?>/js/i18n/grid.locale-ru.js"></script>
		<script src="templates/<?= $cfg->theme; ?>/js/jquery.jqGrid.min.js"></script>
		<script src="js/chosen.jquery.min.js"></script>
		<script src="js/jquery.toastmessage-min.js"></script>
		<script src="js/jquery.form.js"></script>
		<script src="templates/<?= $cfg->theme; ?>/js/bootstrap.min.js"></script>
		<script src="templates/<?= $cfg->theme; ?>/js/common.js"></script>
		<script>
			defaultorgid = <?= $cfg->defaultorgid; ?>;
			theme = '<?= $cfg->theme; ?>';
			defaultuserid = <?= ($user->id != '') ? $user->id : '-1'; ?>;

			var bootstrapButton = $.fn.button.noConflict();
			$.fn.bootstrapBtn = bootstrapButton;

			$.jgrid.defaults.width = 780;
			$.jgrid.defaults.responsive = true;
<?php if ($cfg->style == 'Bootstrap'): ?>
				$.jgrid.defaults.styleUI = 'Bootstrap';
				$.jgrid.styleUI.Bootstrap.base.headerTable = 'table table-bordered table-condensed';
				$.jgrid.styleUI.Bootstrap.base.rowTable = 'table table-bordered table-condensed';
				$.jgrid.styleUI.Bootstrap.base.footerTable = 'table table-bordered table-condensed';
				$.jgrid.styleUI.Bootstrap.base.pagerTable = 'table table-condensed';
<?php endif; ?>
			var config = {
				'.chosen-select': {},
				'.chosen-select-deselect': {allow_single_deselect: true},
				'.chosen-select-no-single': {disable_search_threshold: 4},
				'.chosen-select-no-results': {no_results_text: 'Ничего не найдено!'},
				'.chosen-select-width': {width: '95%'}
			};

			$(function () {
				$.localise('ui-multiselect', {/*language: 'en',*/ path: 'templates/<?= $cfg->theme; ?>/js/locale/'});
			});
		</script>
		<style>
			.chosen-container .chosen-results {
				max-height:100px;
			}
		</style>
	</head>
	<body style="font-size:<?= $cfg->fontsize; ?>;">
