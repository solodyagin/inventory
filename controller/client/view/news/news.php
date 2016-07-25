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

$step = GetDef('step');
$id = GetDef('id');
$dtpost = '';
$title = '';
$txt = '';

if ($step == 'edit') {
	$result = $sqlcn->ExecuteSQL("SELECT * FROM news WHERE id = '$id'");
	while ($row = mysqli_fetch_array($result)) {
		$dtpost = MySQLDateTimeToDateTimeNoTime($row['dt']);
		$title = $row['title'];
		$txt = $row['body'];
	}
} else {
	$step = 'add';
	$id = '';
}
?>
<script src="js/tinymce/jquery.tinymce.min.js"></script>
<div id="messenger"></div>
<form enctype="multipart/form-data" action="?content_page=news&step=<?php echo "$step&newsid=$id"; ?>" method="post" name="form1" target="_self">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4">
				<input name="dtpost" id="dtpost" class="form-control" value="<?php echo $dtpost; ?>">
			</div>
		</div>
		<div class="row">
			<input name="title" id="title" class="form-control" value="<?php echo $title; ?>" placeholder="Заголовок">
		</div>
		<div class="row">
			<textarea id="txt" name="txt" rows="15" placeholder="Введите новость">
				<?php echo $txt; ?>
			</textarea>
		</div>
	</div>
</form>
<script>
	$('#pg_add_edit').dialog({
		close: function () {
			$('#dtpost').datepicker('destroy');
			tinymce.activeEditor.destroy();
		}
	});

	$(function () {
		var field = new Array('dtpost', 'title', 'txt');//поля обязательные
		$('form1').submit(function () {// обрабатываем отправку формы
			var error = 0; // индекс ошибки
			$('form').find(':input').each(function () {// проверяем каждое поле в форме
				for (var i = 0; i < field.length; i++) { // если поле присутствует в списке обязательных
					if ($(this).attr('name') == field[i]) { //проверяем поле формы на пустоту
						if (!$(this).val()) {// если в поле пустое
							$(this).css('border', 'red 1px solid');// устанавливаем рамку красного цвета
							error = 1;// определяем индекс ошибки
						} else {
							$(this).css('border', 'gray 1px solid');// устанавливаем рамку обычного цвета
						}

					}
				}
			});
			if (error == 0) { // если ошибок нет то отправляем данные
				return true;
			} else {
				$('#messenger').html('Не все обязательные поля заполнены!');
				$('#messenger').fadeIn('slow');
				return false; //если в форме встретились ошибки , не  позволяем отослать данные на сервер.
			}
		});
	});

	$().ready(function () {
		$(document).on('focusin', function (e) {
			if ($(event.target).closest('.mce-window').length) {
				e.stopImmediatePropagation();
			}
		});
		if ($('.textarea').length) {
			$('.textarea').tinymce().hide();
		}
		$('textarea').tinymce({
			script_url: 'js/tinymce/tinymce.min.js',
			theme: 'modern',
			mode: 'none',
			'theme_advanced_buttons3_add': 'code',
			plugins: 'save fullscreen link emoticons code',
			toolbar: 'save fullscreen link emoticons',
			save_enablewhendirty: true,
			save_onsavecallback: function () {
				document.form1.submit();
			}
		});
	});

	$('#dtpost').datepicker();
	$('#dtpost').datepicker('option', 'dateFormat', 'dd.mm.yy');
<?php if ($step != 'edit'): ?>
		$('#dtpost').datepicker('setDate', '0');
<?php else: ?>
		$('#dtpost').datepicker('setDate', "<?php echo $dtpost; ?>");
<?php endif; ?>
</script>
