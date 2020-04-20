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

/* Объявляем глобальные переменные */
define('SITE_EXEC', true);
define('SITE_ROOT', dirname(dirname(__FILE__)));
define('SITE_VERSION', '2020-04-20');

header('Content-Type: text/html; charset=utf-8');

/* Проверяем версию PHP */
define('SITE_MINIMUM_PHP', '7.0.22');
if (version_compare(PHP_VERSION, SITE_MINIMUM_PHP, '<')) {
	die('Для запуска этой версии Inventory хост должен использовать PHP ' . SITE_MINIMUM_PHP . ' или выше!');
}

/* Запускаем установщик при условии, что файл настроек отсутствует */
if (file_exists(SITE_ROOT . '/app/config.php')) {
	die('Система уже установлена.<br>Если желаете переустановить, то удалите файл /app/config.php');
}

$action = filter_input(INPUT_GET, 'action');
if ($action == 'install') {
	require_once SITE_ROOT . '/install/install.php';
	die();
}
?>
<!DOCTYPE HTML>
<html lang="ru">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Учёт оргтехники в организации</title>
		<link rel="icon" type="image/png" href="../favicon.png" sizes="16x16">
		<link rel="stylesheet" href="css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="js/jquery-1.11.0.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="../js/jquery.form.js"></script>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-3">
					<div id="error" class="alert alert-danger" style="display:none"></div>
				</div>
				<div class="col-sm-6">
					<div class="panel panel-primary">
						<div class="panel-heading">Установка "Учёт оргтехники в организации"</div>
						<div class="panel-body" id="prim">
							<form class="form-horizontal" id="myform" name="myform" action="?action=install" method="post" target="_self">
								<div class="form-group">
									<label class="control-label col-sm-4" for="dbdriver">СУБД:</label>
									<div class="col-sm-8">
										<select id="dbdriver" name="dbdriver" class="form-control">
											<option value="mysql">MySQL</option>
											<option value="pgsql">PostgreSQL</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-4" for="dbhost">Сервер:</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" name="dbhost" id="dbhost" placeholder="localhost" value="localhost"
											   data-toggle="tooltip" data-html="true" data-placement="right" title="Для Docker укажите <strong>mysql</strong>">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-4" for="dbname">Имя БД:</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" name="dbname" id="dbname" placeholder="inventory" value="inventory">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-4" for="dbuser">Пользователь БД:</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" name="dbuser" id="dbuser" placeholder="Введите имя пользователя БД" value="root">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-4" for="dbpass">Пароль БД:</label>
									<div class="col-sm-8">
										<input type="password" class="form-control" name="dbpass" id="dbpass" placeholder="Введите пароль БД" value="">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-4" for="orgname">Название организации:</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" name="orgname" id="orgname" placeholder="Введите название организации" value="ООО Рога и Копыта">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-4" for="login">Логин администратора:</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" name="login" id="login" placeholder="Введите логин администратора" value="admin">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-4" for="pass">Пароль администратора:</label>
									<div class="col-sm-8">
										<input type="password" class="form-control" name="pass" id="pass" placeholder="Пароль администратора" value="">
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-offset-4 col-sm-8">
										<button type="submit" class="btn btn-primary">Начать установку</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(function () {
				var $error = $('#error'),
					$dbuser = $('#dbuser'),
					fields = ['dbhost', 'dbname', 'dbuser', 'orgname', 'login', 'pass'];
				$('form').submit(function () {
					$error.hide();
					var error = 0;
					$('form').find(':input').each(function () {
						var $input = $(this);
						for (var i = 0; i < fields.length; i++) {
							if ($input.attr('name') === fields[i]) {
								if (!$input.val()) {
									error = 1;
									$input.parent().addClass('has-error');
								} else {
									$input.parent().removeClass('has-error');
								}
							}
						}
					});
					if (error === 1) {
						$error.html('Обязательные поля не заполнены!').fadeIn('slow');
						return false;
					}
					return true;
				});
				$('#myform').ajaxForm(function (msg) {
					if (msg !== 'ok') {
						$error.html(msg).fadeIn('slow');
					} else {
						$error.hide();
						$('#prim').html('<div class="alert alert-info">Внимание!<br>Инсталляция прошла успешно.<br>Не забудьте удалить каталог <strong>install</strong></div>');
					}
				});
				$('[data-toggle="tooltip"]').tooltip({
					container: 'body'
				});
				$('#dbdriver').change(function () {
					switch($(this).find(':selected').val()) {
						case 'mysql':
							$dbuser.val('root');
							break;
						case 'pgsql':
							$dbuser.val('postgres');
							break;
					}
				});
			});
		</script>
	</body>
</html>