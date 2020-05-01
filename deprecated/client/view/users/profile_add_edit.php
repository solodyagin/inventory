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

use core\baseuser;
use core\request;
use core\user;

$req = request::getInstance();
$userid = $req->get('userid');
?>
<link rel="stylesheet" href="public/css/upload.css">
<link rel="stylesheet" href="public/js/jcrop/jquery.Jcrop.min.css">
<script>
	var examples = [];
	$(function () {
		$('#myForm').ajaxForm(function (msg) {
			if (msg !== 'ok') {
				$('#messenger').html(msg);
			} else {
				$('#add_edit').html('');
				$('#add_edit').dialog('destroy');
				$('#list2').jqGrid().trigger('reloadGrid');
			}
		});
	});
</script>
<?php
$user = user::getInstance();
if ($user->isAdmin()):
	$tmpuser = new baseuser();
	$tmpuser->getById($userid);
	$id = $tmpuser->id;
	$fio = $tmpuser->fio;
	$photo = $tmpuser->jpegphoto;
	if ($photo == '') {
		$photo = 'noimage.jpg';
	}
	$post = $tmpuser->post;
	$phone1 = $tmpuser->telephonenumber;
	$phone2 = $tmpuser->homephone;
	unset($tmpuser);
	?>
	<div class="container-fluid">
		<div class="row">
			<div id="messenger"></div>
			<form role="form" id="myForm" enctype="multipart/form-data" action="route/deprecated/server/users/libre_profile_users_form.php?userid=<?= $userid; ?>" method="post" name="form1" target="_self">
				<div class="row-fluid">
					<div class="col-xs-6 col-md-6 col-sm-6">
						<div class="form-group">
							<label for="fio">ФИО</label>
							<input class="form-control" placeholder="ФИО" name="fio" id="fio" value="<?= $fio; ?>">
							<label for="post">Должность</label>
							<input class="form-control" placeholder="Должность" name="post" id="post" value="<?= $post; ?>">
							<label for="phone1">Сотовый:</label>
							<input class="form-control" placeholder="Сотовый телефон" name="phone1" id="phone1" value="<?= $phone1; ?>">
							<label for="phone2">Стационарный:</label>
							<input class="form-control" placeholder="Стационарный телефон" name="phone2" id="phone2" value="<?= $phone2; ?>">
						</div>
					</div>
					<div class="col-xs-6 col-md-6 col-sm-6">
						<div id="userpic" class="userpic">
							<div class="js-preview userpic__preview thumbnail">
								<img src="photos/<?= $photo; ?>">
							</div>
							<div class="btn btn-success js-fileapi-wrapper">
								<div class="js-browse">
									<span class="btn-txt">Сменить фото</span>
									<input type="file" name="filedata">
								</div>
								<div class="js-upload" style="display: none;">
									<div class="progress progress-success"><div class="js-progress bar"></div></div>
									<span class="btn-txt">Загружаем</span>
								</div>
							</div>
						</div>
						<input name="picname" id="picname" type="hidden" value="<?= $photo; ?>">
					</div>
				</div>
				<div class="form-group">
					<input class="form-control" type="submit" name="Submit" value="Сохранить">
				</div>
			</form>
		</div>
	</div>
	<div id="popup" class="popup" style="display: none;">
		<div class="popup__body"><div class="js-img"></div></div>
		<div style="margin: 0 0 5px; text-align: center;">
			<div class="js-upload btn btn_browse btn_browse_small">Загрузить</div>
		</div>
	</div>
	<script>
		examples.push(function () {
			$('#userpic').fileapi({
				url: 'route/deprecated/server/common/uploadfile.php',
				accept: 'image/*',
				imageSize: {minWidth: 200, minHeight: 200},
				data: {'geteqid': ''},
				elements: {
					active: {show: '.js-upload', hide: '.js-browse'},
					preview: {
						el: '.js-preview',
						width: 200,
						height: 200
					},
					progress: '.js-progress'
				},
				onFileComplete: function (evt, uiEvt) {
					if (uiEvt.result.msg == 'error') {
						$('#messenger').html('Ошибка загрузки фото');
					} else {
						$('#picname').val(uiEvt.result.msg);
					}
				},
				onSelect: function (evt, ui) {
					var file = ui.files[0];
					if (file) {
						$('#popup').modal({
							closeOnEsc: true,
							closeOnOverlayClick: false,
							onOpen: function (overlay) {
								$(overlay).on('click', '.js-upload', function () {
									$.modal().close();
									$('#userpic').fileapi('upload');
								});
								$('.js-img', overlay).cropper({
									file: file,
									bgColor: '#fff',
									maxSize: [$(window).width() - 100, $(window).height() - 100],
									minSize: [200, 200],
									selection: '90%',
									onSelect: function (coords) {
										$('#userpic').fileapi('crop', file, coords);
									}
								});
							}
						}).open();
					}
				}
			});
		});
		var FileAPI = {
			debug: true,
			media: true,
			staticPath: './FileAPI/'
		};
	</script>
	<script src="public/js/FileAPI/FileAPI.min.js"></script>
	<script src="public/js/FileAPI/FileAPI.exif.js"></script>
	<script src="public/js/jquery.fileapi.min.js"></script>
	<script src="public/js/jcrop/jquery.Jcrop.min.js"></script>
	<script src="public/js/statics/jquery.modal.js"></script>
	<script>
		for (var selector in config) {
			$(selector).chosen(config[selector]);
		}
		jQuery(function ($) {
			var $blind = $('.splash__blind');
			$('.splash').mouseenter(function () {
				$('.splash__blind', this).animate({top: -10}, 'fast', 'easeInQuad').animate({top: 0}, 'slow', 'easeOutBounce');
			}).click(function () {
				$(this).off();
				if (!FileAPI.support.media) {
					$blind.animate({top: -$(this).height()}, 'slow', 'easeOutQuart');
				}
				FileAPI.Camera.publish($('.splash__cam'), function (err, cam) {
					if (err) {
						alert('Unfortunately, your browser does not support webcam.');
					} else {
						$blind.animate({top: -$(this).height()}, 'slow', 'easeOutQuart');
					}
				});
			});

			$('.example').each(function () {
				var $example = $(this);
				$('<div></div>').append('<div data-code="javascript"><pre><code>' + $.trim(_getCode($example.find('script'))) + '</code></pre></div>')
						.append('<div data-code="html" style="display: none"><pre><code>' + $.trim(_getCode($example.find('.example__left'), true)) + '</code></pre></div>')
						.appendTo($example.find('.example__right'))
						.find('[data-code]').each(function () {
					/** @namespace hljs -- highlight.js */
					if (window.hljs && (!$.browser.msie || parseInt($.browser.version, 10) > 7)) {
						this.className = 'example__code language-' + $.attr(this, 'data-code');
						hljs.highlightBlock(this);
					}
				});
			});

			$('body').on('click', '[data-tab]', function (evt) {
				evt.preventDefault();
				var el = evt.currentTarget,
						tab = $.attr(el, 'data-tab');
				$(el).closest('.example').find('[data-tab]').removeClass('active')
						.filter('[data-tab="' + tab + '"]').addClass('active').end()
						.find('[data-code]').hide()
						.filter('[data-code="' + tab + '"]').show();
			});

			function _getCode(node, all) {
				var code = FileAPI.filter($(node).prop('innerHTML').split('\n'), function (str) {
					return !!str;
				});
				if (!all) {
					code = code.slice(1, -2);
				}
				var tabSize = (code[0].match(/^\t+/) || [''])[0].length;
				return $('<div/>').text($.map(code, function (line) {
					return line.substr(tabSize).replace(/\t/g, '   ');
				}).join('\n')).prop('innerHTML').replace(/ disabled=""/g, '').replace(/&amp;lt;%/g, '<% ').replace(/%&amp;gt;/g, ' %>');
			}

			FileAPI.each(examples, function (fn) {
				fn();
			});
		});
	</script>
<?php endif; ?>
