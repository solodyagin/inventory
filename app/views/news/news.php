<?php

namespace app\views;

use PDOException;
use core\config;
use core\db;
use core\dbexception;
use core\request;
use core\utils;

$cfg = config::getInstance();

$req = request::getInstance();
$step = $req->get('step');
$id = $req->get('id');
$dtpost = '';
$title = '';
$txt = '';

if ($step == 'edit') {
	try {
		$sql = 'select * from news where id = :id';
		$row = db::prepare($sql)->execute([':id' => $id])->fetch();
		if ($row) {
			$dtpost = utils::MySQLDateTimeToDateTimeNoTime($row['dt']);
			$title = $row['title'];
			$txt = $row['body'];
		}
	} catch (PDOException $ex) {
		throw new dbexception('Не смог выбрать новость', 0, $ex);
	}
} else {
	$step = 'add';
	$id = '';
}
?>
<!DOCTYPE html>
<html lang="ru-RU">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<base href="<?= $cfg->rewrite_base; ?>">
		<!--FontAwesome-->
		<link rel="stylesheet" href="public/css/all.min.css">
		<!--jQuery-->
		<script src="public/js/jquery-1.11.0.min.js"></script>
		<!--Bootstrap-->
		<link rel="stylesheet" href="public/themes/<?= $cfg->theme; ?>/bootstrap.min.css" id="bs_theme">
		<script src="public/js/bootstrap.min.js"></script>
		<!--Localisation assistance for jQuery-->
		<script src="public/js/plugins/localisation/jquery.localisation-min.js"></script>
		<!--jQuery Form Plugin-->
		<script src="public/js/jquery.form.js"></script>
		<!--Bootstrap Datepicker-->
		<link rel="stylesheet" href="public/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
		<script src="public/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
		<script src="public/libs/bootstrap-datepicker/locales/bootstrap-datepicker.ru.min.js" charset="UTF-8"></script>
		<!--TinyMCE-->
		<script src="public/js/tinymce/jquery.tinymce.min.js"></script>
	</head>
	<body style="font-size:<?= $cfg->fontsize; ?>;">
		<script>
			$(function () {
				var fields = ['dtpost', 'title', 'txt'];

				$('form').submit(function () {
					var $form = $(this),
							error = false;
					$form.find(':input').each(function () {
						var $input = $(this);
						for (var i = 0; i < fields.length; i++) {
							if ($input.attr('name') === fields[i]) {
								if (!$input.val()) {
									error = true;
									$input.parent().addClass('has-error');
								} else {
									$input.parent().removeClass('has-error');
								}
							}
						}
					});
					if (error) {
						$('#messenger').addClass('alert alert-danger').html('Не все обязательные поля заполнены!').fadeIn('slow');
					} else {
						$form.ajaxSubmit({
							success: function () {
								if (window.top) {
									window.top.$('#bmd_iframe').modal('hide');
								}
							}
						});
					}
					return false;
				});

				$('#dtpost').datepicker({
					todayBtn: true,
					language: 'ru',
					autoclose: true,
					todayHighlight: true
				});

<?php if ($step != 'edit'): ?>
					$('#dtpost').datepicker('setDate', new Date());
<?php else: ?>
					$('#dtpost').datepicker('setDate', "<?= $dtpost; ?>");
<?php endif; ?>

//				$(document).on('focusin', function (e) {
//					if ($(event.target).closest('.mce-window').length) {
//						e.stopImmediatePropagation();
//					}
//				});
//				if ($('.textarea').length) {
//					$('.textarea').tinymce().hide();
//				}
				$('textarea').tinymce({
					script_url: '<?= $cfg->rewrite_base; ?>public/js/tinymce/tinymce.min.js',
					theme: 'modern',
					mode: 'none',
					theme_advanced_buttons3_add: 'code',
					plugins: 'fullscreen link emoticons code',
					toolbar: 'fullscreen link emoticons',
					save_enablewhendirty: true
				});
			});
		</script>

		<form enctype="multipart/form-data" action="news/<?= $step; ?>?newsid=<?= $id; ?>" method="post" name="form1" target="_self">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-4">
						<input type="text" name="dtpost" id="dtpost" class="form-control" value="<?= $dtpost; ?>">
					</div>
				</div>
				<div class="row-fluid">
					<input type="text" name="title" id="title" class="form-control" value="<?= $title; ?>" placeholder="Заголовок">
				</div>
				<div class="row-fluid">
					<textarea id="txt" name="txt" rows="15" placeholder="Введите новость">
						<?= $txt; ?>
					</textarea>
				</div>
				<div class="row">
					<div class="col-md-offset-10 col-md-2">
						<input type="submit" class="form-control btn btn-primary" name="Submit" value="Сохранить">
					</div>
				</div>
				<div class="row-fluid">
					<div id="messenger"></div>
				</div>
			</div>
		</form>
	</body>
</html>