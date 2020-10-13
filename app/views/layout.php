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
		<!--FontAwesome-->
		<link rel="stylesheet" href="public/css/all.min.css">
		<!--jQuery-->
		<script src="public/js/jquery-1.11.0.min.js"></script>
		<!--jQuery UI-->
		<link rel="stylesheet" href="public/css/jquery-ui.min.css">
		<script src="public/js/jquery-ui.min.js"></script>
		<!--Bootstrap-->
		<link rel="stylesheet" href="public/themes/<?= $cfg->theme; ?>/bootstrap.min.css" id="bs_theme">
		<script src="public/js/bootstrap.min.js"></script>
		<!--Localisation assistance for jQuery-->
		<script src="public/js/plugins/localisation/jquery.localisation-min.js"></script>
		<!--jQuery UI Multiselect-->
		<link rel="stylesheet" href="public/css/ui.multiselect.css">
		<script src="public/js/ui.multiselect.js"></script>
		<!--jqGrid-->
		<link rel="stylesheet" href="public/css/ui.jqgrid-bootstrap.css">
		<script src="public/js/i18n/grid.locale-ru.js"></script>
		<script src="public/js/jquery.jqGrid.min.js"></script>
		<!--Select2-->
		<link rel="stylesheet" href="public/css/select2.min.css">
		<link rel="stylesheet" href="public/css/select2-bootstrap.min.css">
		<script src="public/js/select2.full.min.js"></script>
		<!--Bootstrap Notify-->
		<script src="public/libs/mouse0270-bootstrap-notify/bootstrap-notify.min.js"></script>
		<!--jQuery Form Plugin-->
		<script src="public/js/jquery.form.js"></script>
		<!--Common-->
		<link rel="stylesheet" href="public/css/common.css">
		<script src="public/js/common.js"></script>
		<script>
			var defaultorgid = <?= $cfg->defaultorgid; ?>;
			var theme = '<?= $cfg->theme; ?>';
			var defaultuserid = <?= ($user->isLogged()) ? $user->id : '-1'; ?>;

			$.fn.bootstrapBtn = $.fn.button.noConflict();

			$.jgrid.defaults.width = 780;
			$.jgrid.defaults.responsive = true;
			$.jgrid.defaults.styleUI = 'Bootstrap';
			$.jgrid.styleUI.Bootstrap.base.headerTable = 'table table-bordered table-condensed';
			$.jgrid.styleUI.Bootstrap.base.rowTable = 'table table-bordered table-condensed';
			$.jgrid.styleUI.Bootstrap.base.footerTable = 'table table-bordered table-condensed';
			$.jgrid.styleUI.Bootstrap.base.pagerTable = 'table table-condensed';

			$(function () {
				$.localise('ui-multiselect', {/*language: 'en',*/ path: 'public/js/locale/'});
			});

			$.notifyDefaults({type: 'danger', offset: {x: 10, y: 57}});
		</script>
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

		<div id="bmd_iframe" class="modal fade" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content bmd-modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"></h4>
					</div>
					<div class="modal-body">
						<div class="embed-responsive embed-responsive-16by9">
							<iframe class="embed-responsive-item"></iframe>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
		// Отображение сообщений пользователю (если есть)
		global $err, $ok;

		if (count($err) != 0) {
			echo '<div class="container-fluid"><div class="row"><div class="col-sm-12">';
			echo '<div class="alert alert-danger">';
			echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			for ($i = 0; $i < count($err); $i++) {
				echo "<p>{$err[$i]}</p>";
			}
			echo '</div>';
			echo '</div></div></div>';
		}
		if (count($ok) != 0) {
			echo '<div class="container-fluid"><div class="row"><div class="col-sm-12">';
			echo '<div class="alert alert-success">';
			echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			for ($i = 0; $i < count($ok); $i++) {
				echo "<p>{$ok[$i]}</p>";
			}
			echo '</div>';
			echo '</div></div></div>';
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
					webuserorg3 2011-2017 &copy; <a href="http://xn--90acbu5aj5f.xn--p1ai" target="_blank">Павел Грибов</a><br>
					inventory 2018-<?= date('Y'); ?> &copy; <a href="https://github.com/solodyagin/inventory" target="_blank">Сергей Солодягин</a><br>
					Собрано за <?= $time; ?> сек.
				</p>
			</div>
		</footer>
	</body>
</html>
