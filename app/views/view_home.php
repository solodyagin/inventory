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

$morgs = GetArrayOrgs(); // список активных организаций
$mhome = new Mod();   // обьявляем переменную для работы с классом модуля
$mhome->Register('news', 'Модуль новостей', 'Грибов Павел');
$mhome->Register('stiknews', 'Закрепленные новости', 'Грибов Павел');
$mhome->Register('lastmoved', 'Последние перемещения ТМЦ', 'Грибов Павел');
$mhome->Register('whoonline', 'Кто на сайте?', 'Грибов Павел');
$mhome->Register('commits-widget', 'Виджет разработки на github.com на главной странице', 'Солодягин Сергей');
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
					<?php include_once WUO_ROOT . "/templates/{$cfg->theme}/assets/login.php"; // форма входа или профиль ?>
				</div>
			</div>
			<!-- [/Панель входа] -->
			<!-- [Личное меню] -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Личное меню</h4>
				</div>
				<div class="panel-body">
					<?php include_once WUO_ROOT . "/templates/{$cfg->theme}/assets/memenu.php"; // личное меню ?>
				</div>
			</div>
			<!-- [/Личное меню] -->
		</div>
		<div class="col-xs-12 col-md-9 col-sm-9">
			<div class="row">
				<div class="col-xs-12 col-md-7 col-sm-7">
					<!-- [Новости] -->
					<?php if ($mhome->IsActive('news')): ?>
						<div class="panel panel-info">
							<div class="panel-heading">
								<h4 class="panel-title">Новости, обьявления</h4>
							</div>
							<div class="panel-body">
								<div class="well" id="newslist"></div>
								<ul class="pager">
									<li class="previous"><a href="javascript:;" id="newsprev" name="newsprev"><i class="fa fa-arrow-left fa-fw"></i> Назад</a></li>
									<li class="next"><a href="javascript:;" id="newsnext" name="newsnext">Вперед <i class="fa fa-arrow-right fa-fw"></i></a></li>
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
					if ($mhome->IsActive('stiknews')) {
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
					<?php if ($mhome->IsActive('commits-widget') && $user->isAdmin()): ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">Разработка</h4>
							</div>
							<div class="panel-body">
								<iframe src="//tylerlh.github.com/github-latest-commits-widget/?username=solodyagin&repo=webuseorg3-lite&limit=5" allowtransparency="true" frameborder="0" scrolling="no" width="100%" height="250px"></iframe>
							</div>
						</div>
					<?php endif; ?>
					<!-- [/Виджет разработки] -->
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-md-12 col-sm-12">
					<!-- [Последние перемещения ТМЦ] -->
					<?php if ($mhome->IsActive('lastmoved') && ($user->id != '')): ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">Последние перемещения ТМЦ</h4>
							</div>
							<div class="panel-body">
								<table id="tbl_move"></table>
								<div id="mv_nav"></div>
							</div>
						</div>
						<script src="templates/<?= $cfg->theme; ?>/assets/js/lastmoved.js"></script>
					<?php endif; ?>
					<!-- [/Последние перемещения ТМЦ] -->
					<!-- [Кто онлайн] -->
					<?php if ($mhome->IsActive('whoonline')): ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">Кто онлайн</h4>
							</div>
							<div class="panel-body">
								<?php include_once WUO_ROOT . "/templates/{$cfg->theme}/assets/whoonline.php"; ?>
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
unset($mhome);
