<?php
/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

/* Объявляем глобальные переменные */
define('WUO_ROOT', dirname(__FILE__));

// Запускаем установщик при условии, что файл настроек отсутствует
if (file_exists(WUO_ROOT . '/config.php')) {
	header('Content-Type: text/html; charset=utf-8');
	die('Система уже установлена.<br>Если желаете переустановить, то удалите файл config.php');
}

$action = (isset($_GET['action'])) ? $_GET['action'] : '';
if ($action == 'install') {
	include_once(WUO_ROOT . '/inc/install.php');
	die();
}
?>
<!-- saved from url=(0014)about:internet -->
<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Учет ТМЦ в организации">
		<meta name="author" content="(c) 2011-2016 by Gribov Pavel">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Учет ТМЦ в организации</title>
		<meta name="keywords" content="учет тмц">
		<link href="favicon.ico" type="image/ico" rel="icon">
		<link href="favicon.ico" type="image/ico" rel="shortcut icon">
		<link rel="stylesheet" href="controller/client/themes/bootstrap/css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="controller/client/themes/bootstrap/css/bootstrap.min.css">
		<script src="controller/client/themes/bootstrap/js/jquery-1.11.0.min.js"></script>
		<script src="controller/client/themes/bootstrap/js/bootstrap.min.js"></script>
		<script src="js/jquery.form.js"></script>
	</head>
	<body>
		<script>
			$(function () {
				var field = new Array('dbhost', 'dbname', 'dbuser', 'orgname', 'login', 'pass'); // поля обязательные
				$('form').submit(function () {
					var error = 0;
					$('form').find(':input').each(function () {
						for (var i = 0; i < field.length; i++) {
							if ($(this).attr('name') == field[i]) {
								if (!$(this).val()) {
									$(this).parent().addClass('has-error');
									error = 1;
								} else {
									$(this).parent().removeClass('has-error');
								}
							}
						}
					});
					if (error == 1) {
						$('#messenger').addClass('alert alert-danger');
						$('#messenger').html('Обязательные поля не заполнены!');
						$('#messenger').fadeIn('slow');
						return false;
					}
					return true;
				});
				$('#myform').ajaxForm(function (msg) {
					if (msg == 'ok') {
						$('#messenger').hide();
						$('#prim').html('<div class="alert alert-info">Внимание!<br>Инсталляция прошла успешно.<br>Не забудьте удалить файл install.php</div>');
					} else {
						$('#messenger').html(msg);
					}
				});
			});
		</script>
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-6 col-md-4 col-sm-4">
					<div id="messenger"></div>
				</div>
				<div class="col-xs-6 col-md-4 col-sm-4">
					<div class="panel panel-primary">
						<div class="panel-heading">Установка "Учет ТМЦ в организации"</div>
						<div class="panel-body" id="prim">
							<form role="form" name="myform" id="myform" action="install.php?action=install" method="post" target="_self">
								<div class="form-group">
									<label for="dbhost">Сервер MySQL</label>
									<input type="dbhost" class="form-control" name="dbhost" id="dbhost" placeholder="localhost" value="localhost">
								</div>
								<div class="form-group">
									<label for="dbname">Имя базы</label>
									<input type="dbname" class="form-control" name="dbname" id="dbname" placeholder="webuser" value="webuser">
								</div>
								<div class="form-group">
									<label for="dbuser">Имя пользователя базы</label>
									<input type="dbuser" class="form-control" name="dbuser" id="dbuser" placeholder="Введите имя пользователя mysql" value="root">
								</div>
								<div class="form-group">
									<label for="dbpass">Пароль пользователя</label>
									<input type="password" class="form-control" name="dbpass" id="dbpass" placeholder="Введите пароль" value="">
								</div>
								<div class="form-group">
									<label for="orgname">Название организации</label>
									<input type="orgname" class="form-control" name="orgname" id="orgname" placeholder="Введите название организации" value="ООО Рога и Копыта">
								</div>
								<div class="form-group">
									<label for="login">Логин администратора</label>
									<input type="login" class="form-control" name="login" id="login" placeholder="Введите логин администратора" value="admin">
								</div>
								<div class="form-group">
									<label for="pass">Пароль администратора</label>
									<input type="password" class="form-control" name="pass" id="pass" placeholder="Пароль администратора" value="">
								</div>
								<button type="submit" class="btn btn-default">Начать инсталляцию</button>
							</form>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-md-4 col-sm-4"></div>
			</div>
		</div>
	</body>
</html>