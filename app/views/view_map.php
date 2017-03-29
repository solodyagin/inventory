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
 * Отчёты / Размещение ТМЦ на карте
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$user = User::getInstance();
$cfg = Config::getInstance();

// Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (!$user->isAdmin() && !$user->TestRoles('1')):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Отчёты / Размещение ТМЦ на карте"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ".
	</div>

<?php else: ?>

	<?php
	$morgs = GetArrayOrgs();
	?>

	<div class="container-fluid">
		<div class="row-fluid">
			<div class="col-xs-4 col-md-4 col-sm-4">
				<div class="form-group">
					<label>
						<input type="checkbox" checked id="grpom" name="grpom"> Группировка по помещению
					</label>
					<div name="sel_pom" id="sel_pom"></div>
					<div name="sel_tmc" id="sel_tmc"></div>
				</div>
				<div class="form-group">
					<input type="checkbox" id="moveme" name="moveme"> Двигать ТМЦ</br>
					<input type="checkbox" checked id="stmetka" name="stmetka"> Стиль - метки
				</div>
			</div>
			<div class="col-xs-8 col-md-8 col-sm-8" id="map" style="height:600px;width:800px;">
			</div>
		</div>
	</div>
	<div id="msgid"></div>
	<div id="myConfig" name="myConfig"></div>
	<script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU"></script>
	<script src="templates/<?= $cfg->theme; ?>/assets/js/mapsplaces.js"></script>
	<script src="templates/<?= $cfg->theme; ?>/assets/js/map.js"></script>

<?php endif;
