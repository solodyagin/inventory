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

use core\config;
use core\db;
use core\dbexception;
use core\request;
use core\utils;

$cfg = config::getInstance();

$req = request::getInstance();
$id = $req->get('id');
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
		<link rel="stylesheet" href="public/themes/<?= $cfg->theme; ?>/bootstrap.min.css">
		<script src="public/js/bootstrap.min.js"></script>
		<!--Bootstrap Datepicker-->
		<link rel="stylesheet" href="public/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
		<script src="public/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
		<script src="public/libs/bootstrap-datepicker/locales/bootstrap-datepicker.ru.min.js" charset="UTF-8"></script>
		<!--Localisation assistance for jQuery-->
		<script src="public/js/plugins/localisation/jquery.localisation-min.js"></script>
		<!--jQuery Form Plugin-->
		<script src="public/js/jquery.form.js"></script>
		<!--jqGrid-->
		<link rel="stylesheet" href="public/css/ui.jqgrid-bootstrap.css">
		<script src="public/js/i18n/grid.locale-ru.js"></script>
		<script src="public/js/jquery.jqGrid.min.js"></script>
		<!--Select2-->
		<link rel="stylesheet" href="public/css/select2.min.css">
		<link rel="stylesheet" href="public/css/select2-bootstrap.min.css">
		<script src="public/js/select2.full.min.js"></script>
		<!--PassGen-->
		<script src="public/js/jquery.passgen.min.js"></script>

		<link rel="stylesheet" href="public/css/upload.css">
		<link href="public/js/jcrop/jquery.Jcrop.min.css" rel="stylesheet">
		<style>
			#binv, #bshtr {
				font: initial;
			}
		</style>
		<script>
			$(function () {
				var fields = ['dtpost', 'sorgid', 'splaces', 'suserid', 'sgroupname', 'svendid', 'snomeid'];

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
						return false;
					}
					return true;
				});

				$('#myForm').ajaxForm(function (msg) {
					if (msg !== 'ok') {
						$('#messenger').html(msg);
					} else {
						if (window.top) {
							window.top.$('#bmd_iframe').modal('hide');
							window.top.$('#tbl_equpment').jqGrid().trigger('reloadGrid');
						}
					}
				});

				$('.select2').select2({theme: 'bootstrap'});
			});
		</script>
		<?php
		$dtpost = '';
		$dtendgar = '';
		$orgid = '';
		$placesid = '';
		$userid = '';
		$nomeid = '';
		try {
			$sql = 'select * from equipment where id = :id';
			$row = db::prepare($sql)->execute([':id' => $id])->fetch();
			if ($row) {
				$dtpost = utils::MySQLDateTimeToDateTimeNoTime($row['datepost']);
				$dtendgar = utils::MySQLDateTimeToDateTimeNoTime($row['dtendgar']);
				$orgid = $row['orgid'];
				$placesid = $row['placesid'];
				$userid = $row['usersid'];
				$nomeid = $row['nomeid'];
				$buhname = $row['buhname'];
				$cost = $row['cost'];
				$currentcost = $row['currentcost'];
				$sernum = $row['sernum'];
				$invnum = $row['invnum'];
				$shtrihkod = $row['shtrihkod'];
				$os = $row['os'];
				$mode = $row['mode'];
				$mapyet = $row['mapyet'];
				$comment = $row['comment'];
				$photo = $row['photo'];
				$ip = $row['ip'];
				$kntid = $row['kntid'];
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать объект имущества', 0, $ex);
		}

		$vendorid = '';
		$groupid = '';
		try {
			$sql = 'select * from nome where id = :nomeid';
			$row = db::prepare($sql)->execute([':nomeid' => $nomeid])->fetch();
			if ($row) {
				$vendorid = $row['vendorid'];
				$groupid = $row['groupid'];
			}
		} catch (PDOException $ex) {
			throw new dbexception('Не могу выбрать номенклатуру', 0, $ex);
		}
		if ($photo == '') {
			$photo = 'noimage.jpg';
		}

		echo <<<TXT
<script>
	var dtpost='$dtpost',
		dtendgar='$dtendgar',
		orgid='$orgid',
		placesid='$placesid',
		userid='$userid',
		nomeid='$nomeid',
		vendorid='$vendorid',
		groupid='$groupid';
</script>
TXT;
		?>
	</head>
	<body style="font-size:<?= $cfg->fontsize; ?>;">
		<form role="form" id="myForm" class="form-horizontal" enctype="multipart/form-data" action="route/deprecated/server/equipment/equipment_form.php?step=edit&id=<?= $id; ?>" method="post" name="form1" target="_self">
			<div id="messenger"></div>
			<div class="row-fluid">
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-xs-3 control-label">От кого:</label>
						<div class="col-xs-9">
							<select class="select2" name="kntid" id="kntid">
								<?php
								$knts = utils::getArrayKnt();
								for ($i = 0; $i < count($knts); $i++) {
									$nid = $knts[$i]['id'];
									$sl = ($nid == $kntid) ? 'selected' : '';
									echo "<option value=\"$nid\" $sl>{$knts[$i]['name']}</option>";
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-3 control-label">Что:</label>
						<div class="col-xs-9">
							<div id="sgroups">
								<select class="select2" name="sgroupname" id="sgroupname">
									<?php
									try {
										$sql = 'select * from group_nome where active = 1 order by name';
										$arr = db::prepare($sql)->execute()->fetchAll();
										foreach ($arr as $row) {
											$rid = $row['id'];
											$sl = ($rid == $groupid) ? 'selected' : '';
											echo "<option value=\"$rid\" $sl>{$row['name']}</option>";
										}
									} catch (PDOException $ex) {
										throw new dbexception('Не могу выбрать список групп', 0, $ex);
									}
									?>
								</select>
							</div>
							<div id="svendors">идет загрузка..</div>
							<div id="snomes">идет загрузка..</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-3 control-label">Куда:</label>
						<div class="col-xs-9">
							<div id="sorg">
								<select class="select2" name="sorgid" id="sorgid">
									<?php
									$orgs = utils::getArrayOrgs();
									for ($i = 0; $i < count($orgs); $i++) {
										$nid = $orgs[$i]['id'];
										$sl = ($nid == $orgid) ? 'selected' : '';
										echo "<option value=\"$nid\" $sl>{$orgs[$i]['name']}</option>";
									}
									?>
								</select>
							</div>
							<div id="splaces">идет загрузка..</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-3 control-label">Кому:</label>
						<div class="col-xs-9">
							<div id="susers">идет загрузка..</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-3 control-label">Когда:</label>
						<div class="col-xs-9">
							<input class="form-control" name="dtpost" id="dtpost" value="<?= $dtpost; ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-4 control-label">Статический IP:</label>
						<div class="col-xs-8">
							<input class="form-control" name="ip" id="ip" value="<?= $ip; ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-4 control-label">Серийный номер:</label>
						<div class="col-xs-8">
							<input class="form-control" name="sernum" value="<?= $sernum; ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-4 control-label">Инвентарный номер:</label>
						<div class="col-xs-8">
							<div class="input-group">
								<input class="form-control" id="invnum" name="invnum" value="<?= $invnum; ?>">
								<span class="input-group-btn">
									<button class="btn btn-default" name="binv" id="binv"><i class="fas fa-dice"></i></button>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<div id="userpic" class="userpic">
							<div class="js-preview userpic__preview thumbnail">
								<img src="photos/<?= $photo; ?>">
							</div>
							<div class="btn btn-success btn-xs js-fileapi-wrapper">
								<div class="js-browse">
									<span class="btn-txt">Сменить фото</span>
									<input type="file" name="filedata">
								</div>
								<div class="js-upload" style="display:none;">
									<div class="progress progress-success"><div class="js-progress bar"></div></div>
									<span class="btn-txt">Загружаем</span>
								</div>
							</div>
						</div>
						<input name="picname" id="picname" type="hidden" value="<?= $photo; ?>">
					</div>
					<div class="form-group">
						<div class="checkbox">
							<label>
								<?php $ch = ($os == '1') ? 'checked' : ''; ?>
								<input type="checkbox" name="os" value="1" <?= $ch; ?>> Основные ср-ва
							</label>
						</div>
						<div class="checkbox">
							<label>
								<?php
								$ch = ($mode == '1') ? 'checked' : '';
								?>
								<input type="checkbox" name="mode" value="1" <?= $ch; ?>> Списано
							</label>
						</div>
						<div class="checkbox" style="display:none">
							<label>
								<?php
								$ch = ($mapyet == '1') ? 'checked' : '';
								?>
								<input type="checkbox" name="mapyet" value="1" <?= $ch; ?>> Есть на карте
							</label>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label class="control-label">Гарантия до:</label>
						<input class="form-control" name="dtendgar" id="dtendgar" value="<?= $dtendgar; ?>">
					</div>
					<div class="form-group">
						<label class="control-label">Имя по бухгалтерии:</label>
						<input class="form-control" name="buhname" value="<?= $buhname; ?>">
					</div>
					<div class="form-group">
						<label class="control-label">Стоимость покупки:</label>
						<input class="form-control" name="cost" value="<?= $cost; ?>">
					</div>
					<div class="form-group">
						<label class="control-label">Текущая стоимость:</label>
						<input class="form-control" name="currentcost" value="<?= $currentcost; ?>">
					</div>
					<div class="form-group">
						<div class="input-group">
							<input title="Штрихкод" class="form-control" placeholder="Штрихкод" name="shtrihkod" id="shtrihkod" value="<?= $shtrihkod; ?>">
							<span class="input-group-btn">
								<button class="btn btn-default" name="bshtr" id="bshtr"><i class="fas fa-dice"></i></button>
							</span>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label">Комментарий:</label>
						<textarea class="form-control" name="comment" rows="4"><?= $comment; ?></textarea>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="col-sm-2 col-sm-offset-2">
					<input type="submit" class="btn btn-primary" name="Submit" value="Сохранить">
				</div>
			</div>
		</form>

		<div id="popup" class="popup" style="display:none;">
			<div class="popup__body"><div class="js-img"></div></div>
			<div style="margin:0 0 5px;text-align:center;">
				<div class="js-upload btn btn_browse btn_browse_small">Загрузить</div>
			</div>
		</div>

		<script>
			var examples = [];

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
						$('#picname').val(uiEvt.result.msg);
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

			$('#dtpost').datepicker({
				todayBtn: true,
				language: 'ru',
				autoclose: true,
				todayHighlight: true
			}).datepicker('setDate', dtpost);

			$('#dtendgar').datepicker({
				todayBtn: true,
				language: 'ru',
				autoclose: true,
				todayHighlight: true
			}).datepicker('setDate', dtendgar);

			function updateChosen() {
				$('.select2').select2({width: '100%', theme: 'bootstrap'});
			}

			function getListPlaces(orgid, placesid) {
				$('#splaces').load('route/deprecated/server/common/getlistplaces.php?orgid=' + orgid + '&placesid=' + placesid);
				updateChosen();
			}

			function getListUsers(orgid, userid) {
				$('#susers').load('route/deprecated/server/common/getlistusers.php?orgid=' + orgid + '&userid=' + userid);
				updateChosen();
			}

			function getListNome(groupid, vendorid, nmd) {
				$.ajax({
					url: 'route/deprecated/server/common/getlistnomes.php?groupid=' + groupid + '&vendorid=' + (vendorid || '') + '&nomeid=' + nmd,
					success: function (answ) {
						$('#snomes').html(answ);
						updateChosen();
					}
				});
			}

			function getListVendors(groupid, vendorid) {
				$.ajax({
					url: 'route/deprecated/server/common/getlistvendors.php?groupid=' + groupid + '&vendorid=' + (vendorid || ''),
					success: function (answ) {
						$('#svendors').html(answ);
						getListNome($('#sgroupname :selected').val(), $('#svendid :selected').val(), nomeid);
						$('#svendid').on('change', function (evt, params) {
							$('#snomes').html = 'идет загрузка...'; // заглушка. Зачем?? каналы счас быстрые
							getListNome($('#sgroupname :selected').val(), $('#svendid :selected').val());
						});
					}
				});
			}

			// Заполняем инвентарник и штрихкод
			function getRandomNum(lbound, ubound) {
				return (Math.floor(Math.random() * (ubound - lbound)) + lbound);
			}

			$('#binv').click(function () {
				var today = new Date();
				$('#invnum').val(today.getDay() + today.getMonth() + today.getFullYear() + today.getUTCHours() + today.getMinutes() + today.getSeconds());
				return false;
			});

			$('#bshtr').click(function () {
				$.get('route/deprecated/server/common/getean13.php', function (data) {
					$('#shtrihkod').val(data);
				});
				return false;
			});

			$('#sorgid').on('change', function () {
				$('#splaces').html = 'идет загрузка...';
				$("#susers").html = 'идет загрузка...';
				getListPlaces($('#sorgid :selected').val(), ''); // перезагружаем список помещений организации
				getListUsers($('#sorgid :selected').val(), ''); // перезагружаем пользователей организации
			});

			// выбираем производителя по группе
			$('#sgroupname').on('change', function () {
				$('#svendors').html = 'идет загрузка...';
				getListVendors($('#sgroupname :selected').val()); // перезагружаем список vendors
			});

			// загружаем места
			getListPlaces($('#sorgid :selected').val(), placesid);

			// загружаем пользователей
			getListUsers($('#sorgid :selected').val(), userid);

			// загружаем производителя
			getListVendors($('#sgroupname :selected').val(), vendorid);

			// номенклатура
			getListNome($('#sgroupname :selected').val(), $('#svendid :selected').val(), nomeid);

			if ($('#shtrihkod').val() === '') {
				$('#bshtr').click();
			}
		</script>
		<script>
			var FileAPI = {
				debug: false,
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
				$('.splash')
						.mouseenter(function () {
							$('.splash__blind', this).animate({top: -10}, 'fast', 'easeInQuad').animate({top: 0}, 'slow', 'easeOutBounce');
						})
						.click(function () {
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
					var el = evt.currentTarget;
					var tab = $.attr(el, 'data-tab');
					var $example = $(el).closest('.example');
					$example
							.find('[data-tab]')
							.removeClass('active')
							.filter('[data-tab="' + tab + '"]')
							.addClass('active')
							.end()
							.end()
							.find('[data-code]')
							.hide()
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
	</body>
</html>
