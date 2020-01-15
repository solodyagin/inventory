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

# Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

$cfg = Config::getInstance();
$user = User::getInstance();

$morgs = GetArrayOrgs(); # список активных организаций
$mod = new Mod();	 # обьявляем переменную для работы с классом модуля
$mod->Register('news', 'Модуль новостей', 'Грибов Павел');
$mod->Register('stiknews', 'Закрепленные новости', 'Грибов Павел');
$mod->Register('lastmoved', 'Последние перемещения оргтехники', 'Грибов Павел');
$mod->Register('whoonline', 'Кто на сайте?', 'Грибов Павел');
$mod->Register('commits-widget', 'Виджет разработки на github.com на главной странице', 'Солодягин Сергей');
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 col-md-3 col-sm-3">
			<!-- [Панель входа] -->
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title">Пользователь</h4>
				</div>
				<div class="panel-body">
					<?php include_once SITE_ROOT . "/templates/{$cfg->theme}/assets/login.php"; # форма входа или профиль ?>
				</div>
			</div>
			<!-- [/Панель входа] -->
			<!-- [Личное меню] -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Личное меню</h4>
				</div>
				<div class="panel-body">
					<?php include_once SITE_ROOT . "/templates/{$cfg->theme}/assets/memenu.php"; # личное меню ?>
				</div>
			</div>
			<!-- [/Личное меню] -->
		</div>
		<div class="col-xs-12 col-md-9 col-sm-9">
			<div class="row">
				<div class="col-xs-12 col-md-7 col-sm-7">
					<!-- [Новости] -->
					<?php if ($mod->IsActive('news')): ?>
						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">Новости, обьявления</h4>
							</div>
							<div class="panel-body">
								<div class="well" id="newslist"></div>
								<ul class="pager">
									<li class="previous"><a href="javascript:void(0);" id="newsprev" name="newsprev"><i class="fa fa-arrow-left fa-fw"></i> Назад</a></li>
									<li class="next"><a href="javascript:void(0);" id="newsnext" name="newsnext">Вперед <i class="fa fa-arrow-right fa-fw"></i></a></li>
								</ul>
							</div>
						</div>
						<script src="templates/<?= $cfg->theme; ?>/assets/js/news_main.js"></script>
					<?php endif; ?>
					<!-- [/Новости] -->
				</div>
				<div class="col-xs-12 col-md-5 col-sm-5">
					<!-- [Закреплённые новости] -->
					<?php
					if ($mod->IsActive('stiknews')) {
						$stiker = GetStiker();
						if ($stiker['title'] != '') {
							?>
							<div class="panel panel-info">
								<div class="panel-heading">
									<h4 class="panel-title"><?= $stiker['title']; ?></h4>
								</div>
								<div class="panel-body"><?= $stiker['body']; ?></div>
							</div>
							<?php
						}
					}
					?>
					<!-- [/Закреплённые новости] -->
					<!-- [Виджет разработки] -->
					<?php if ($mod->IsActive('commits-widget') && $user->isAdmin()): ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">Разработка</h4>
							</div>
							<div class="panel-body">
								<iframe src="//tylerlh.github.com/github-latest-commits-widget/?username=solodyagin&repo=inventory&limit=5" allowtransparency="true" frameborder="0" scrolling="no" width="100%" height="250px"></iframe>
							</div>
						</div>
					<?php endif; ?>
					<!-- [/Виджет разработки] -->
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-md-12 col-sm-12">
					<!-- [Последние перемещения оргтехники] -->
					<?php if ($mod->IsActive('lastmoved') && ($user->id != '')): ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">Последние перемещения оргтехники</h4>
							</div>
							<div class="panel-body">
								<table id="tbl_move"></table>
								<div id="mv_nav"></div>
							</div>
						</div>
						<script src="templates/<?= $cfg->theme; ?>/assets/js/lastmoved.js"></script>
					<?php endif; ?>
					<!-- [/Последние перемещения оргтехники] -->
					<!-- [Кто онлайн] -->
					<?php if ($mod->IsActive('whoonline')): ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">Кто онлайн</h4>
							</div>
							<div class="panel-body">
								<?php include_once SITE_ROOT . "/templates/{$cfg->theme}/assets/whoonline.php"; ?>
							</div>
						</div>
					<?php endif; ?>
					<!-- [/Кто онлайн] -->
				</div>
			</div>
		</div>
	</div>
</div>
<?php
unset($mod);
