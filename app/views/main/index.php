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

use PDO;
use PDOException;
use core\config;
use core\db;
use core\dbexception;
use core\mod;
use core\user;
use core\utils;

$cfg = config::getInstance();
$user = user::getInstance();

$morgs = utils::getArrayOrgs(); // список активных организаций
$mod = new mod();  // обьявляем переменную для работы с классом модуля
$mod->register('news', 'Модуль новостей', 'Грибов Павел');
$mod->register('stiknews', 'Закрепленные новости', 'Грибов Павел');
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
					<?php include_once SITE_ROOT . "/app/views/login.php"; # форма входа или профиль  ?>
				</div>
			</div>
			<!-- [/Панель входа] -->
			<!-- [Личное меню] -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Личное меню</h4>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label for="orgs">Организация:</label>
						<select class="select2 form-control" name="orgs" id="orgs">
							<?php
							for ($i = 0; $i < count($morgs); $i++) {
								$idorg = $morgs[$i]['id'];
								$nameorg = $morgs[$i]['name'];
								$sl = ($idorg == $cfg->defaultorgid) ? 'selected' : '';
								echo "<option value=\"$idorg\" $sl>$nameorg</option>";
							}
							?>
						</select>
					</div>
					<div class="form-group">
						<label for="fontsize">Размер шрифта:</label>
						<select class="select2 form-control" name="fontsize" id="fontsize">
							<option value="11px">11px</option>
							<option value="12px">12px</option>
							<option value="13px">13px</option>
							<option value="14px">14px</option>
						</select>
					</div>
					<script>
						$(function () {
							$('.select2').select2({theme: 'bootstrap'});

							$('#fontsize').val("<?= $cfg->fontsize; ?>").trigger('change.select2');

							$('#orgs').change(function () {
								var exdate = new Date();
								exdate.setDate(exdate.getDate() + 365);
								orgid = $('#orgs :selected').val();
								document.cookie = 'defaultorgid=' + orgid + '; path=/; expires=' + exdate.toUTCString();
							});

							$('#fontsize').change(function () {
								var exdate = new Date();
								exdate.setDate(exdate.getDate() + 365);
								fontsize = $('#fontsize :selected').val();
								document.cookie = 'fontsize=' + fontsize + '; path=/; expires=' + exdate.toUTCString();
								window.location.reload();
							});
						});
					</script>
				</div>
			</div>
			<!-- [/Личное меню] -->
		</div>
		<div class="col-xs-12 col-md-9 col-sm-9">
			<div class="row">
				<div class="col-xs-12 col-md-7 col-sm-7">
					<!-- [Новости] -->
					<?php if ($mod->isActive('news')): ?>
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
						<script>
							var pznews = 0,
									url = 'news/getnews?num=0';

							$('#newsprev').click(function () {
								if (pznews >= 1) {
									pznews--;
									url = 'news/getnews?num=' + pznews;
									$('#newslist').load(url);
								}
							});

							$('#newsnext').click(function () {
								pznews++;
								url = 'news/getnews?num=' + pznews;
								$prev = $('#newsnext').html();
								$('#newslist').load(url);
							});

							$('#newslist').load(url);
						</script>
					<?php endif; ?>
					<!-- [/Новости] -->
				</div>
				<div class="col-xs-12 col-md-5 col-sm-5">
					<!-- [Закреплённые новости] -->
					<?php
					if ($mod->isActive('stiknews')) {
						$stiker = utils::getStiker();
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
					<?php if ($mod->isActive('commits-widget') && $user->isAdmin()): ?>
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
					<?php if ($mod->isActive('lastmoved') && $user->isLogged()): ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">Последние перемещения оргтехники</h4>
							</div>
							<div class="panel-body">
								<table id="tbl_move"></table>
								<div id="mv_nav"></div>
							</div>
						</div>
						<script>
							var $tblMove = $('#tbl_move');
							$tblMove.jqGrid({
								url: 'moveinfo/list?eqid=',
								datatype: 'json',
								colNames: ['Id', 'Дата', 'Организация', 'Помещение', 'Сотрудник', 'Организация', 'Помещение', 'Сотрудник', 'Оргтехника', 'Комментарий'],
								colModel: [
									{name: 'id', index: 'id', width: 25, hidden: true},
									{name: 'dt', index: 'dt', width: 60, sorttype: 'date', formatter: 'date', formatoptions: {srcformat: 'Y-m-d H:i:s', newformat: 'd.m.Y H:i:s'}},
									{name: 'orgname1', index: 'orgname1', width: 140, hidden: true},
									{name: 'place1', index: 'place1', width: 90},
									{name: 'user1', index: 'user1', width: 90},
									{name: 'orgname2', index: 'orgname2', width: 140, hidden: true},
									{name: 'place2', index: 'place2', width: 90},
									{name: 'user2', index: 'user2', width: 90},
									{name: 'name', index: 'name', width: 120},
									{name: 'comment', index: 'comment', width: 100, editable: true}
								],
								autowidth: true,
								pager: '#pager2',
								sortname: 'dt',
								scroll: 1,
								shrinkToFit: true,
								height: 200,
								sortorder: 'desc'
							});
							$tblMove.jqGrid('destroyGroupHeader');
							$tblMove.jqGrid('setGroupHeaders', {
								useColSpanStyle: true,
								groupHeaders: [
									{startColumnName: 'orgname1', numberOfColumns: 3, titleText: 'Откуда'},
									{startColumnName: 'orgname2', numberOfColumns: 3, titleText: 'Куда'}
								]
							});
						</script>
					<?php endif; ?>
					<!-- [/Последние перемещения оргтехники] -->
					<!-- [Кто онлайн] -->
					<?php if ($mod->isActive('whoonline')): ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">Кто онлайн</h4>
							</div>
							<div class="panel-body">
								<?php
								$crd = date('Y-m-d H:i:s');
								try {
									switch (db::getAttribute(PDO::ATTR_DRIVER_NAME)) {
										case 'mysql':
											$sql = <<<TXT
select
	users_profile.fio as fio,
	users_profile.jpegphoto
from users
	inner join users_profile on users_profile.usersid = users.id
where unix_timestamp(:crd) - unix_timestamp(lastdt) < 600
TXT;
											break;
										case 'pgsql':
											$sql = <<<TXT
select
	users_profile.fio fio,
	users_profile.jpegphoto
from users
	inner join users_profile on users_profile.usersid = users.id
where trunc(extract(epoch from (current_timestamp - lastdt))) < 600
TXT;
											break;
									}
									$arr = db::prepare($sql)->execute([':crd' => $crd])->fetchAll();
									foreach ($arr as $row) {
										$fio = $row['fio'];
										$jpegphoto = $row['jpegphoto'];
										if (empty($jpegphoto) || !is_file(SITE_ROOT . "/photos/$jpegphoto")) {
											$jpegphoto = 'noimage.jpg';
										}
										echo <<<TXT
<div class="col-sm-1 col-md-1">
	<div class="thumbnail">
		<img src="photos/$jpegphoto" title="$fio">
	</div>
</div>
TXT;
									}
								} catch (PDOException $ex) {
									throw new dbexception('Не могу выбрать список заходов пользователей!', 0, $ex);
								}
								?>
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
