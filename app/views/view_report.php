<?php
/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

/*
 * Отчёты / Имущество
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$user = User::getInstance();
$cfg = Config::getInstance();

// Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Отчёты / Имущество"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<form class="form-horizontal" enctype="multipart/form-data" action="?content_page=reports&step=view" method="post" name="form1" target="_self">
			<div class="form-group">
				<div class="col-xs-12 col-md-4 col-sm-4">
					<label for="sel_rep" class="control-label">Название отчета</label>
					<select class="chosen-select" name="sel_rep" id="sel_rep">
						<option value="1">Наличие ТМЦ</option>
						<option value="2">Наличие ТМЦ - только не ОС и не списанное</option>
					</select>
					<label for="sel_plp" class="control-label">Сотрудник</label>
					<div name="sel_plp" id="sel_plp"></div>
				</div>
				<div class="col-xs-12 col-md-4 col-sm-4">
					<label for="sel_orgid" class="control-label">Организация</label>
					<select class="chosen-select" name="sel_orgid" id="sel_orgid">
						<?php
						$morgs = GetArrayOrgs();
						for ($i = 0; $i < count($morgs); $i++) {
							$nid = $morgs[$i]['id'];
							$sl = ($nid == $user->orgid) ? 'selected' : '';
							echo "<option value=\"$nid\" $sl>{$morgs[$i]['name']}</option>";
						}
						?>
					</select>
					<div class="checkbox">
						<label class="checkbox">
							<input type="checkbox" name="os" id="os" value="1"> Основные
						</label>
						<label class="checkbox">
							<input type="checkbox" name="mode" id="mode" value="1"> Списано
						</label>
						<label class="checkbox">
							<input type="checkbox" name="gr" id="gr" value="1"> По группам
						</label>
					</div>
				</div>
				<div class="col-xs-12 col-md-4 col-sm-4">
					<label for="sel_pom" class="control-label">Помещение</label>
					<div name="sel_pom" id="sel_pom"></div>
					<div class="checkbox">
						<label class="checkbox">
							<input type="checkbox" name="repair" id="repair" value="1"> В ремонте
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-4">
					<button class="btn btn-primary" id="sbt">Сформировать</button>
					<button class="btn btn-default" id="btprint">Распечатать</button>
				</div>
			</div>
		</form>
		<table id="list2"></table>
		<div id="pager2"></div>
	</div>
	<script>curuserid = <?= $user->id; ?>;</script>
	<script src="templates/<?= $cfg->theme; ?>/assets/js/report.js"></script>

<?php endif;
