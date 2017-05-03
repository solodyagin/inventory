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
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title">Пользователь</h4>
				</div>
				<div class="panel-body">
					<?php include_once WUO_ROOT . "/templates/{$cfg->theme}/assets/login.php"; // форма входа или профиль ?>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Личное меню</h4>
				</div>
				<div class="panel-body">
					<?php include_once WUO_ROOT . "/templates/{$cfg->theme}/assets/memenu.php"; // личное меню ?>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-5 col-sm-5">
			<?php if ($mhome->IsActive('news')): ?>
				<!-- [Новости] -->
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
				<!-- [/Новости] -->
			<?php endif; ?>
			<?php if ($mhome->IsActive('lastmoved') && ($user->id != '')): ?>
				<!-- [Последние перемещения ТМЦ] -->
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
				<!-- [/Последние перемещения ТМЦ] -->
			<?php endif; ?>
		</div>
		<div class="col-xs-12 col-md-4 col-sm-4">
			<?php if ($mhome->IsActive('stiknews')): ?>
				<!-- [Закреплённые новости] -->
				<?php
				$stiker = GetStiker();
				if ($stiker['title'] != ''):
					?>
					<div class="panel panel-info">
						<div class="panel-heading">
							<h4 class="panel-title"><?= $stiker['title']; ?></h4>
						</div>
						<div class="panel-body">
							<?= $stiker['body']; ?>
						</div>
					</div>
				<?php endif; ?>
				<!-- [/Закреплённые новости] -->
			<?php endif; ?>
			<?php if ($mhome->IsActive('whoonline')): ?>
				<!-- [Кто онлайн] -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">Кто онлайн</h4>
					</div>
					<div class="panel-body">
						<?php include_once WUO_ROOT . "/templates/{$cfg->theme}/assets/whoonline.php"; ?>
					</div>
				</div>
				<!-- [/Кто онлайн] -->
			<?php endif; ?>
			<?php if ($mhome->IsActive('commits-widget') && $user->isAdmin()): ?>
				<!-- [Виджет разработки] -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">Разработка</h4>
					</div>
					<div class="panel-body">
						<iframe src="//tylerlh.github.com/github-latest-commits-widget/?username=solodyagin&repo=webuseorg3-lite&limit=5" allowtransparency="true" frameborder="0" scrolling="no" width="100%" height="250px"></iframe>
					</div>
				</div>
				<!-- [/Виджет разработки] -->
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
unset($mhome);
