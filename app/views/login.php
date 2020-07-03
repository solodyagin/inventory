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

namespace app\views;

use core\user;
use core\config;

$user = user::getInstance();
$cfg = config::getInstance();

if (!$user->isLogged()):
	?>
	<form action="account/login" method="post" name="form1" target="_self">
		<div class="form-group">
			<input type="text" class="form-control" id="login" name="login" placeholder="Логин">
			<input type="password" class="form-control" id="password" name="password" placeholder="Пароль">
		</div>
		<button type="submit" class="btn btn-primary">Войти</button>
	</form>
<?php else: ?>
	<link rel="stylesheet" href="public/css/upload.css">
	<link rel="stylesheet" href="public/js/jcrop/jquery.Jcrop.min.css">
	<div class="container-fluid">
		<?php
		$photo = $user->jpegphoto;
		if (!is_file(SITE_ROOT . "/photos/$photo")) {
			$photo = 'noimage.jpg';
		}
		?>
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<div id="userpic" class="userpic">
					<div class="js-preview userpic__preview thumbnail">
						<img width="100%" src="photos/<?= $photo; ?>">
					</div>
					<div class="btn btn-success js-fileapi-wrapper">
						<div class="js-browse">
							<span class="btn-txt">Сменить фото</span>
							<input type="file" name="filedata">
						</div>
						<div class="js-upload" style="display: none;">
							<div class="progress progress-success">
								<div class="js-progress bar"></div>
							</div>
							<span class="btn-txt">Загружаем</span>
						</div>
					</div>
				</div>
				<input name="picname" id="picname" type="hidden" value="<?= $photo; ?>">
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<p>
					<b>ФИО:</b> <?= $user->fio; ?><br>
					<b>Учётная запись:</b> <?= $user->login; ?><br>
					<b>Почта:</b> <a href="mailto:<?= $user->email; ?>"><?= $user->email; ?></a><br>
					<b>Моб.тел.:</b> <?= $user->telephonenumber; ?><br>
					<b>Раб.тел.:</b> <?= $user->homephone; ?><br>
					<b>Роль:</b> <?= ($user->isAdmin()) ? 'Администратор' : 'Пользователь'; ?>
				</p>
			</div>
		</div>
		<div id="popup" class="popup" style="display: none;">
			<div class="popup__body">
				<div class="js-img"></div>
			</div>
			<div style="margin: 0 0 5px; text-align: center;">
				<div class="js-upload btn btn_browse btn_browse_small">Загрузить</div>
			</div>
		</div>
		<form class="form-horizontal" action="account/logout" method="get" name="form1" target="_self">
			<div class="form-group">
				<div class="controls">
					<button type="submit" class="btn btn-default"><i class="fas fa-sign-out-alt"></i> Выйти из системы</button>
				</div>
			</div>
		</form>
	</div>
	<script>
		var examples = [];

		examples.push(function () {
			$('#userpic').fileapi({
				url: 'route/deprecated/server/common/uploadfile.php',
				accept: 'image/*',
				imageSize: {minWidth: 200, minHeight: 200},
				data: {'geteqid': ""},
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
					if (uiEvt.result.msg === 'error') {
						$('#messenger').html('Ошибка загрузки фото');
					} else {
						$('#picname').val(uiEvt.result.msg);
						//сохраняем аватарку
						$.get('route/deprecated/server/common/save_avatar.php?photo=' + uiEvt.result.msg, function (data) {});
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
		jQuery(function ($) {
			var $blind = $('.splash__blind');

			$('.splash').mouseenter(function () {
				$('.splash__blind', this)
						.animate({top: -10}, 'fast', 'easeInQuad')
						.animate({top: 0}, 'slow', 'easeOutBounce');
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
				$('<div></div>')
						.append('<div data-code="javascript"><pre><code>' + $.trim(_getCode($example.find('script'))) + '</code></pre></div>')
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
				return $('<div/>')
						.text($.map(code, function (line) {
							return line.substr(tabSize).replace(/\t/g, '   ');
						}).join('\n'))
						.prop('innerHTML')
						.replace(/ disabled=""/g, '')
						.replace(/&amp;lt;%/g, '<% ')
						.replace(/%&amp;gt;/g, ' %>');
			}

			// Init examples
			FileAPI.each(examples, function (fn) {
				fn();
			});
		});
	</script>
<?php endif;
