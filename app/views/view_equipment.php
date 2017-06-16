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
 * Журналы / Имущество
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');

$user = User::getInstance();
$cfg = Config::getInstance();

// Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1,3,4,5,6')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Журналы / Имущество"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ", "Просмотр", "Добавление", "Редактирование", "Удаление".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-3 col-sm-3">
				<select class="chosen-select form-control" name="orgs" id="orgs">
					<?php
					$morgs = GetArrayOrgs(); // список активных организаций
					for ($i = 0; $i < count($morgs); $i++) {
						$idorg = $morgs[$i]['id'];
						$nameorg = $morgs[$i]['name'];
						$sl = ($idorg == $cfg->defaultorgid) ? 'selected' : '';
						echo "<option value=\"$idorg\" $sl>$nameorg</option>";
					}
					?>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="tbl_equpment"></table>
				<div id="pg_nav"></div>
				<div id="pg_add_edit"></div>
				<div class="row-fluid">
					<div class="col-xs-2 col-md-2 col-sm-2">
						<div id="photoid"></div>
					</div>
					<div class="col-xs-10 col-md-10 col-sm-10">
						<table id="tbl_move"></table>
						<div id="mv_nav"></div>
						<table id="tbl_rep"></table>
						<div id="rp_nav"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="templates/<?= $cfg->theme; ?>/assets/js/equipment.js"></script>

<?php endif;
