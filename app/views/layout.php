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

use core\config;
use core\user;

$cfg = config::getInstance();
$user = user::getInstance();
?>
<!DOCTYPE html>
<html lang="ru-RU">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?= $cfg->sitename; ?></title>
		<base href="<?= $cfg->rewrite_base; ?>">
		<link rel="icon" type="image/png" href="public/images/favicon.png" sizes="16x16">
		<link rel="stylesheet" href="public/css/jquery-ui.min.css">
		<link rel="stylesheet" href="public/css/ui.multiselect.css">
		<link rel="stylesheet" href="public/themes/<?= $cfg->theme; ?>/bootstrap.min.css" id="bs_theme">
		<link rel="stylesheet" href="public/css/ui.jqgrid-bootstrap.css">
		<link rel="stylesheet" href="public/css/chosen.css">
		<link rel="stylesheet" href="public/css/jquery.toastmessage-min.css">
		<link rel="stylesheet" href="public/css/all.min.css">
		<link rel="stylesheet" href="public/css/common.css">
		<script src="public/js/jquery-1.11.0.min.js"></script>
		<script src="public/js/jquery-ui.min.js"></script>
		<script src="public/js/plugins/localisation/jquery.localisation-min.js"></script>
		<script src="public/js/ui.multiselect.js"></script>
		<script src="public/js/i18n/grid.locale-ru.js"></script>
		<script src="public/js/jquery.jqGrid.min.js"></script>
		<script src="public/js/chosen.jquery.min.js"></script>
		<script src="public/js/jquery.toastmessage-min.js"></script>
		<script src="public/js/jquery.form.js"></script>
		<script src="public/js/bootstrap.min.js"></script>
		<script src="public/js/common.js"></script>
		<script>
			var defaultorgid = <?= $cfg->defaultorgid; ?>,
					theme = '<?= $cfg->theme; ?>',
					defaultuserid = <?= ($user->isLogged()) ? $user->id : '-1'; ?>;

			$.fn.bootstrapBtn = $.fn.button.noConflict();

			$.jgrid.defaults.width = 780;
			$.jgrid.defaults.responsive = true;
			$.jgrid.defaults.styleUI = 'Bootstrap';
			$.jgrid.styleUI.Bootstrap.base.headerTable = 'table table-bordered table-condensed';
			$.jgrid.styleUI.Bootstrap.base.rowTable = 'table table-bordered table-condensed';
			$.jgrid.styleUI.Bootstrap.base.footerTable = 'table table-bordered table-condensed';
			$.jgrid.styleUI.Bootstrap.base.pagerTable = 'table table-condensed';

			var config = {
				'.chosen-select': {},
				'.chosen-select-deselect': {allow_single_deselect: true},
				'.chosen-select-no-single': {disable_search_threshold: 4},
				'.chosen-select-no-results': {no_results_text: 'Ничего не найдено!'},
				'.chosen-select-width': {width: '95%'}
			};

			$(function () {
				$.localise('ui-multiselect', {/*language: 'en',*/ path: 'public/js/locale/'});
			});
		</script>
		<style>
			.chosen-container .chosen-results {
				max-height:100px;
			}
		</style>
	</head>
	<body style="font-size:<?= $cfg->fontsize; ?>;">
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?= $cfg->rewrite_base; ?>">Inventory</a>
				</div>
				<div class="collapse navbar-collapse" id="navbar-collapse-1">
					<ul class="nav navbar-nav">
						<?php

						function putMenu($par) {
							global $gmenu;
							$list = $gmenu->getList($par);
							foreach ($list as $key => $pmenu) {
								$nm = $pmenu['name'];
								$path = $pmenu['path'];
								$uid = $pmenu['uid'];
								$url = ($path == '') ? 'javascript:void(0);' : "$path";
								if (count($gmenu->getList($uid)) > 0) {
									echo '<li class="dropdown">';
									echo "<a href=\"$url\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">$nm <span class=\"caret\"></span></a>";
									echo '<ul class="dropdown-menu">';
									putMenu($uid);
									echo '</ul>';
								} else {
									echo '<li>';
									echo "<a href=\"$url\">$nm</a>";
								}
								echo '</li>';
							}
						}

						putMenu('main');
						unset($mm);
						?>
					</ul>
				</div>
			</div>
		</nav>

		<?php
		// Отображение сообщений пользователю (если есть)
		global $err, $ok;

		if (count($err) != 0) {
			echo '<div class="alert alert-danger">';
			echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			for ($i = 0; $i < count($err); $i++) {
				echo "<p>$err[$i]</p>";
			}
			echo '</div>';
		}
		if (count($ok) != 0) {
			echo '<div class="alert alert-success">';
			echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			for ($i = 0; $i < count($ok); $i++) {
				echo "<p>$ok[$i]</p>";
			}
			echo '</div>';
		}
		?>

		<?php
		echo (isset($view)) ? $view : '';
		?>

		<?php
		// Подвал страницы
		global $time_start;

		$time_end = microtime(true);
		$time = round($time_end - $time_start, 2);
		?>
		<footer class="footer">
			<div class="container-fluid">
				<p class="text-muted text-right">
					2011-2017 &copy; <a href="http://xn--90acbu5aj5f.xn--p1ai" target="_blank">Павел Грибов</a><br>
					2018-<?= date('Y'); ?> &copy; <a href="https://github.com/solodyagin/inventory" target="_blank">Сергей Солодягин</a><br>
					Собрано за <?= $time; ?> сек.
				</p>
			</div>
		</footer>
	</body>
</html>
