<?php
/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$morgs = GetArrayOrgs(); // список активных организаций
$mhome = new Tmod;   // обьявляем переменную для работы с классом модуля
$mhome->Register('news', 'Модуль новостей', 'Грибов Павел');
$mhome->Register('stiknews', 'Закрепленные новости', 'Грибов Павел');
$mhome->Register('lastmoved', 'Последние перемещения ТМЦ', 'Грибов Павел');
$mhome->Register('whoonline', 'Кто на сайте?', 'Грибов Павел');
$mhome->Register('commits-widget', 'Виджет разработки на github.com на главной странице', 'Солодягин Сергей');
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 col-md-4 col-sm-4">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title">Пользователь</h4>
				</div>
				<div class="panel-body">
					<?php include_once('login.php'); // форма входа или профиль ?>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Личное меню</h4>
				</div>
				<div class="panel-body">
					<?php include_once('memenu.php'); // личное меню ?>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-4 col-sm-4">
			<?php if ($mhome->IsActive('news') == 1): ?>
				<!-- [Новости] -->
				<div class="panel panel-info">
					<div class="panel-heading">
						<h4 class="panel-title">Новости, обьявления</h4>
					</div>
					<div class="panel-body">
						<div class="well" id="newslist"></div>    
						<ul class="pager">
							<li class="previous"><a href="javascript:void(0)" id="newsprev" name="newsprev">&larr; Назад</a></li>
							<li class="next"><a href="javascript:void(0)" id="newsnext" name="newsnext">Вперед &rarr;</a></li>
						</ul>
						<script src="controller/client/js/news_main.js"></script>
					</div>
				</div>
				<!-- [/Новости] -->
			<?php endif; ?>
			<?php if ($mhome->IsActive('whoonline') == 1): ?>
				<!-- [Кто онлайн] -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">Кто онлайн</h4>
					</div>
					<div class="panel-body">
						<?php include_once('whoonline.php'); ?>
					</div>
				</div>
				<!-- [/Кто онлайн] -->
			<?php endif; ?>
		</div>
		<div class="col-xs-12 col-md-4 col-sm-4">
			<?php if ($mhome->IsActive('stiknews') == 1): ?>
				<?php
				$stiker = GetStiker();
				if ($stiker['title'] != ''):
					?>
					<!-- [Закреплённые новости] -->
					<div class="panel panel-info">
						<div class="panel-heading">
							<h4 class="panel-title"><?php echo $stiker['title']; ?></h4>
						</div>
						<div class="panel-body">
							<?php echo $stiker['body']; ?>
						</div>
					</div>
					<!-- [/Закреплённые новости] -->
				<?php endif; ?>
			<?php endif; ?>
			<?php if (($mhome->IsActive('lastmoved') == 1) && ($user->id != '')): ?>
				<!-- [Последние перемещения ТМЦ] -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">Последние перемещения ТМЦ</h4>
					</div>
					<div class="panel-body">
						<table id="tbl_move"></table>
						<div id="mv_nav"></div>
						<script src="controller/client/js/lastmoved.js"></script>
					</div>
				</div>
				<!-- [/Последние перемещения ТМЦ] -->
			<?php endif; ?>
			<?php if (($mhome->IsActive('commits-widget') == 1) && ($user->mode == 1)): ?>
				<!-- [Виджет разработки] -->
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">Разработка</h4>
					</div>
					<div class="panel-body">
						<iframe src="http://tylerlh.github.com/github-latest-commits-widget/?username=solodyagin&repo=webuseorg3-lite&limit=5" allowtransparency="true" frameborder="0" scrolling="no" width="100%" height="250px"></iframe>
					</div>
				</div>
				<!-- [/Виджет разработки] -->
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
unset($mhome);
