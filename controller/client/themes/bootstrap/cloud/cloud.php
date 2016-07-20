<?php
// Данный код создан и распространяется по лицензии GPL v3
// Разработчики:
//   Грибов Павел,
//   Сергей Солодягин (solodyagin@gmail.com)
//   (добавляйте себя если что-то делали)
// http://грибовы.рф

/*
  Роли:
  1 = 'Полный доступ'
  2 = 'Просмотр финансовых отчетов'
  3 = 'Просмотр'
  4 = 'Добавление'
  5 = 'Редактирование'
  6 = 'Удаление'
  7 = 'Отправка СМС'
  8 = 'Манипуляции с деньгами'
  9 = 'Редактирование карт'
 */

if ($user->TestRoles('1,3,4,6')):
?>
<link rel="stylesheet" href="controller/client/themes/<?php echo $cfg->theme; ?>/css/upload.css">
<link rel="stylesheet" href="js/skin/ui.dynatree.css">
<script src="js/jquery.dynatree.min.js"></script>
<script src="js/FileAPI/FileAPI.min.js"></script>
<script src="js/FileAPI/FileAPI.exif.js"></script>
<script src="js/jquery.fileapi.min.js"></script>
<script src="js/jcrop/jquery.Jcrop.min.js"></script>
<script src="js/statics/jquery.modal.js"></script>
<div class="container-fluid">
	<div class="row-fluid">       
		<div class="col-xs-4 col-md-4 col-sm-4">
			<div id="tree"></div>
			<div class="form-inline">
				<p>
					<input name="foldername" id="foldername" type="text" placeholder="Имя папки" class="form-control">
<?php if ($user->TestRoles('1,4')): ?>
					<button name="newfolder" id="newfolder" class="btn btn-small btn-success" type="button">Новая папка</button>
<?php endif; ?>
<?php if ($user->TestRoles('1,6')): ?>
					<button name="delfolder" id="delfolder" class="btn btn-small btn-danger" type="button">Удалить</button>
<?php endif; ?>
				</p>
<?php if ($user->TestRoles('1,4')):	?>
				<div align="center" id="simple-btn" class="btn btn-primary js-fileapi-wrapper" style="text-align:center;visibility:hidden">
					<div class="js-browse" align="center">
						<span class="btn-txt">Загрузить файл</span>
						<input type="file" name="filedata">
					</div>
					<div class="js-upload" style="display: none">
						<div class="progress progress-success"><div class="js-progress bar"></div></div>
						<span align="center" class="btn-txt">Загружаю (<span class="js-size"></span>)</span>
					</div>
				</div>
<?php endif; ?>
			</div>
		</div>
		<div class="col-xs-8 col-md-8 col-sm-8"> 
			<table id="cloud_files"></table>
			<div id="cloud_files_pager"></div>
		</div>
		</div>
</div>
<script src="controller/client/js/cloud.js"></script>
<?php else: ?>
<div class="alert alert-error">
	У вас нет доступа в данный раздел! Не назначены роли!
</div>
<?php endif; ?>
