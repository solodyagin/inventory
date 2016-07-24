<?php
/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

echo '<link rel="stylesheet" href="controller/client/themes/' . $cfg->theme . '/css/upload.css">';

if ($user->mode == 1):
	?>
	<script src="js/FileAPI/FileAPI.min.js"></script>
	<script src="js/FileAPI/FileAPI.exif.js"></script>
	<script src="js/jquery.fileapi.min.js"></script>
	<script src="js/jcrop/jquery.Jcrop.min.js"></script>
	<script src="js/statics/jquery.modal.js"></script>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="list2"></table>
				<div id="pager2"></div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="col-xs-12 col-md-8 col-sm-8">
				<table id="list3"></table>
				<div id="pager3"></div>
			</div>
			<div class="col-xs-12 col-md-4 col-sm-4">
				<table id="list4" style="visibility:hidden"></table>
				<div id="pager4"></div>
				<div align="center" id="simple-btn" class="btn btn-primary js-fileapi-wrapper" style="text-align:center;visibility:hidden">
					<div class="js-browse" align="center">
						<span class="btn-txt">Загрузить сканированный документ</span>
						<input type="file" name="filedata">
					</div>
					<div class="js-upload" style="display: none">
						<div class="progress progress-success"><div class="js-progress bar"></div></div>
						<span align="center" class="btn-txt">Загружаю (<span class="js-size"></span>)</span>
					</div>
				</div>
				<div id="status"></div>
			</div>
		</div>
		<script src="controller/client/js/libre_knt.js"></script>
	</div>
<?php else: ?>
	<div class="alert alert-error">
		У вас нет доступа в данный раздел!
	</div>
<?php endif;
