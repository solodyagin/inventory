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

// Запрещаем прямой вызов скрипта.
defined('SITE_EXEC') or die('Доступ запрещён');

//use PDOException;
use core\db;
use core\dbexception;
use core\request;
use core\utils;

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
<script src="public/js/tinymce/jquery.tinymce.min.js"></script>
<script>
	$('#pg_add_edit').dialog({
		close: function () {
			$('#dtpost').datepicker('destroy');
			tinymce.activeEditor.destroy();
		}
	});

	$(function () {
		var fields = ['dtpost', 'title', 'txt'];
		$('form').submit(function () {
			var error = 0;
			$('form').find(':input').each(function () {
				for (var i = 0; i < fields.length; i++) {
					if ($(this).attr('name') === fields[i]) {
						if (!$(this).val()) {
							error = 1;
							$(this).parent().addClass('has-error');
						} else {
							$(this).parent().removeClass('has-error');
						}
					}
				}
			});
			if (error === 1) {
				$('#messenger').addClass('alert alert-danger').html('Не все обязательные поля заполнены!').fadeIn('slow');
				return false;
			}
			return true;
		});
	});

	$(function () {
		$(document).on('focusin', function (e) {
			if ($(event.target).closest('.mce-window').length) {
				e.stopImmediatePropagation();
			}
		});
		if ($('.textarea').length) {
			$('.textarea').tinymce().hide();
		}
		$('textarea').tinymce({
			script_url: 'public/js/tinymce/tinymce.min.js',
			theme: 'modern',
			mode: 'none',
			'theme_advanced_buttons3_add': 'code',
			plugins: 'fullscreen link emoticons code',
			toolbar: 'fullscreen link emoticons',
			save_enablewhendirty: true,
		});
	});

	$('#dtpost').datepicker();
	$('#dtpost').datepicker('option', 'dateFormat', 'dd.mm.yy');
<?php if ($step != 'edit'): ?>
		$('#dtpost').datepicker('setDate', '0');
<?php else: ?>
		$('#dtpost').datepicker('setDate', "<?= $dtpost; ?>");
<?php endif; ?>
</script>
<form enctype="multipart/form-data" action="news/<?= $step; ?>?newsid=<?= $id; ?>" method="post" name="form1" target="_self">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4">
				<input name="dtpost" id="dtpost" class="form-control" value="<?= $dtpost; ?>">
			</div>
		</div>
		<div class="row-fluid">
			<input name="title" id="title" class="form-control" value="<?= $title; ?>" placeholder="Заголовок">
		</div>
		<div class="row-fluid">
			<textarea id="txt" name="txt" rows="13" placeholder="Введите новость">
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

