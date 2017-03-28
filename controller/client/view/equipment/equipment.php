<?php
/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$cfg = Config::getInstance();
?>
<link rel="stylesheet" href="/templates/<?= $cfg->theme; ?>/css/upload.css">
<link href="/js/jcrop/jquery.Jcrop.min.css" rel="stylesheet">
<script>
	var examples = [];
	$(function () {
		var fields = ['dtpost', 'sorgid', 'splaces', 'suserid', 'sgroupname', 'svendid', 'snomeid'];
		$('form').submit(function () {
			var error = 0;
			$('form').find(':input').each(function () {
				for (var i = 0; i < fields.length; i++) {
					if ($(this).attr('name') == fields[i]) {
						if (!$(this).val()) {
							error = 1;
							$(this).parent().addClass('has-error');
						} else {
							$(this).parent().removeClass('has-error');
						}
					}
				}
			});
			if (error == 1) {
				$('#messenger').addClass('alert alert-danger');
				$('#messenger').html('Не все обязательные поля заполнены!');
				$('#messenger').fadeIn('slow');
				return false;
			}
			return true;
		});
	});
	$(document).ready(function () {
		$('#myForm').ajaxForm(function (msg) {
			if (msg != 'ok') {
				$('#messenger').html(msg);
			} else {
				$('#dtpost').datepicker('destroy');
				$('#pg_add_edit').html('');
				$('#pg_add_edit').dialog('destroy');
				jQuery('#tbl_equpment').jqGrid().trigger('reloadGrid');
			}
		});
	});
</script>
<?php
$step = GetDef('step', 'add');
$id = GetDef('id');

$user = User::getInstance();

if ($user->isAdmin() || $user->TestRoles('1,4,5,6')):
	echo "<script>orgid='';</script>";
	echo "<script>placesid='';</script>";
	echo "<script>userid='';</script>";
	echo "<script>vendorid='';</script>";
	echo "<script>groupid='';</script>";
	echo "<script>nomeid='';</script>";
	echo "<script>step='$step';</script>";

	if ($step == 'edit') {
		$sql = 'SELECT * FROM equipment WHERE id = :id';
		try {
			$row = DB::prepare($sql)->execute(array(':id' => $id))->fetch();
			if ($row) {
				$dtpost = MySQLDateTimeToDateTimeNoTime($row['datepost']);
				echo "<script>dtpost='$dtpost';</script>";

				$dtendgar = MySQLDateTimeToDateTimeNoTime($row['dtendgar']);
				echo "<script>dtendgar='$dtendgar';</script>";

				$orgid = $row['orgid'];
				echo "<script>orgid='$orgid';</script>";

				$placesid = $row['placesid'];
				echo "<script>placesid='$placesid';</script>";

				$userid = $row['usersid'];
				echo "<script>userid='$userid';</script>";

				$nomeid = $row['nomeid'];
				echo "<script>nomeid='$nomeid';</script>";

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
			throw new DBException('Не могу выбрать объект имущества', 0, $ex);
		}

		$sql = 'SELECT * FROM nome WHERE id = :nomeid';
		try {
			$row = DB::prepare($sql)->execute(array(':nomeid' => $nomeid))->fetch();
			if ($row) {
				$vendorid = $row['vendorid'];
				echo "<script>vendorid='$vendorid';</script>";

				$groupid = $row['groupid'];
				echo "<script>grouid='$groupid';</script>";
			}
		} catch (PDOException $ex) {
			throw new DBException('Не могу выбрать номенклатуру', 0, $ex);
		}
	} else {
		$dtpost = '';
		echo "<script>dtpost='$dtpost';</script>";

		$orgid = $cfg->defaultorgid;
		echo "<script>orgid=defaultorgid;</script>";

		$placesid = 1;
		echo "<script>placesid='$placesid';</script>";

		$userid = $user->id;
		echo "<script>userid='$userid';</script>";

		$nomeid = 1;
		echo "<script>nomeid='$nomeid';</script>";

		$buhname = '';
		$cost = 0;
		$currentcost = 0;
		$sernum = '';
		$invnum = '';
		$shtrihkod = '';
		$os = 0;
		$mode = 0;
		$mapyet = 0;
		$comment = '';
		$photo = '';
		$ip = '';
		$groupid = 1;
		$kntid = '';

		$dtendgar = '';
		echo "<script>dtendgar='$dtendgar';</script>";
	}
	?>
	<div class="container-fluid">
		<div class="row">
			<div id="messenger"></div>
			<form role="form" id="myForm" enctype="multipart/form-data" action="/route/controller/server/equipment/equipment_form.php?step=<?= $step; ?>&id=<?= $id; ?>" method="post" name="form1" target="_self">
				<div class="row-fluid">
					<div class="col-xs-4 col-md-4 col-sm-4">
						<div class="form-group">
							<label>Когда/Куда/Кому:</label><br>
							<input class="form-control" name="dtpost" id="dtpost" value="<?= $dtpost; ?>">
							<div id="sorg">
								<select class="chosen-select" name="sorgid" id="sorgid">
									<?php
									$morgs = GetArrayOrgs();
									for ($i = 0; $i < count($morgs); $i++) {
										$nid = $morgs[$i]['id'];
										$sl = ($nid == $orgid) ? 'selected' : '';
										echo "<option value=\"$nid\" $sl>{$morgs[$i]['name']}</option>";
									}
									?>
								</select>
							</div>
							<div id="splaces">идет загрузка..</div>
							<div id="susers">идет загрузка..</div>
							<input title="Серийный номер" class="form-control" placeholder="Серийный номер" name="sernum" value="<?= $sernum; ?>">
							<input title="Статический IP" class="form-control" placeholder="Статический IP" name="ip" id="ip" value="<?= $ip; ?>">
						</div>
					</div>
					<div class="col-xs-4 col-md-4 col-sm-4">
						<label>От кого/Что:</label><br>
						<select class="chosen-select" name="kntid" id="kntid">
							<?php
							$morgs = GetArrayKnt();
							for ($i = 0; $i < count($morgs); $i++) {
								$nid = $morgs[$i]['id'];
								$sl = ($nid == $kntid) ? 'selected' : '';
								echo "<option value=\"$nid\" $sl>{$morgs[$i]['name']}</option>";
							}
							?>
						</select>
						<div id="sgroups">
							<select class="chosen-select" name="sgroupname" id="sgroupname">
								<?php
								$sql = 'SELECT * FROM group_nome WHERE active = 1 ORDER BY name';
								try {
									$arr = DB::prepare($sql)->execute()->fetchAll();
									foreach ($arr as $row) {
										$sl = ($row['id'] == $groupid) ? 'selected' : '';
										echo "<option value=\"{$row['id']}\" $sl>{$row['name']}</option>";
									}
								} catch (PDOException $ex) {
									throw new DBException('Не могу выбрать список групп', 0, $ex);
								}
								?>
							</select>
						</div>
						<div id="svendors">идет загрузка..</div>
						<div id="snomes">идет загрузка..</div>
						<input title="Инвентарный номер" class="form-control" placeholder="Инвентарный номер" id="invnum" name="invnum" value="<?= $invnum; ?>">
						<button class="form-control btn btn-primary" name="binv" id="binv">Создать</button>
						<div class="checkbox">
							<label>
								<?php $ch = ($os == '1') ? 'checked' : ''; ?>
								<input type="checkbox" name="os" value="1" <?= $ch; ?>> Основные ср-ва
							</label>
						</div>
					</div>
					<div class="col-xs-4 col-md-4 col-sm-4">
						<label>Гарантия до:</label><br>
						<input  class="form-control"  name="dtendgar" id="dtendgar" value="<?= $dtendgar; ?>">
						<?php
						$buhname = htmlspecialchars($buhname);
						?>
						<input title="Имя по бухгалтерии" class="form-control" placeholder="Имя по бухгалтерии" name="buhname" value="<?= $buhname; ?>">
						<input title="Стоимость покупки" class="form-control" name="cost" value="<?= $cost; ?>" placeholder="Начальная стоимость" >
						<input title="Текущая стоимость" class="form-control" name="currentcost" value="<?= $currentcost; ?>" placeholder="Текущая стоимость">
						<input title="Штрихкод" class="form-control" placeholder="Штрихкод" name="shtrihkod" id="shtrihkod" value="<?= $shtrihkod; ?>">
						<button class="form-control btn btn-primary" name="bshtr" id="bshtr">Создать</button>
						<div class="checkbox">
							<label>
								<?php
								$ch = ($mode == '1') ? 'checked' : '';
								?>
								<input type="checkbox" name="mode" value="1" <?= $ch; ?>> Списано
							</label>
							<label>
								<?php
								$ch = ($mapyet == '1') ? 'checked' : '';
								?>
								<input type="checkbox" name="mapyet" value="1" <?= $ch; ?>> Есть на карте
							</label>
						</div>
					</div>
				</div>
				<div class="row-fluid">
					<div class="col-xs-4 col-md-4 col-sm-4">
						<div id="userpic" class="userpic">
							<div class="js-preview userpic__preview thumbnail">
								<img src="photos/<?= $photo; ?>">
							</div>
							<div class="btn btn-success js-fileapi-wrapper">
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
					<div class="col-xs-8 col-md-8 col-sm-8">
						<textarea class="form-control" name="comment" rows="8"><?= $comment; ?></textarea>
						<div align="center">
							<input type="submit" class="form-control btn btn-primary" name="Submit" value="Сохранить">
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div id="popup" class="popup" style="display:none;">
		<div class="popup__body"><div class="js-img"></div></div>
		<div style="margin:0 0 5px;text-align:center;">
			<div class="js-upload btn btn_browse btn_browse_small">Загрузить</div>
		</div>
	</div>
	<script>
		examples.push(function () {
			$('#userpic').fileapi({
				url: '/route/controller/server/common/uploadfile.php',
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

		$('#dtendgar').datepicker();
		$('#dtendgar').datepicker('option', 'dateFormat', 'dd.mm.yy');

		$('#dtpost').datepicker();
		$('#dtpost').datepicker('option', 'dateFormat', 'dd.mm.yy');

		if (step != 'edit') {
			$('#dtpost').datepicker('setDate', '0');
			$('#dtendgar').datepicker('setDate', '0');
		} else {
			$('#dtpost').datepicker('setDate', dtpost);
			$('#dtendgar').datepicker('setDate', dtendgar);
		}

		$('#sernum').focus();

		$('#pg_add_edit').dialog({
			close: function () {
				$('#dtpost').datepicker('destroy');
			}
		});

		function UpdateChosen() {
			for (var selector in config) {
				$(selector).chosen({width: '100%'});
				$(selector).chosen(config[selector]);
			}
		}

		function GetListPlaces(orgid, placesid) {
			$('#splaces').load('/route/controller/server/common/getlistplaces.php?orgid=' + orgid + '&placesid=' + placesid);
			UpdateChosen();
		}

		function GetListUsers(orgid, userid) {
			$('#susers').load('/route/controller/server/common/getlistusers.php?orgid=' + orgid + '&userid=' + userid);
			UpdateChosen();
		}

		function GetListGroups(groupid) {
			$('#sgroups').load('/route/controller/server/common/getlistgroupname.php?groupid=' + groupid);
			UpdateChosen();
		}

		function GetListNome(groupid, vendorid, nmd) {
			$.ajax({
				url: '/route/controller/server/common/getlistnomes.php?groupid=' + groupid + '&vendorid=' + vendorid + '&nomeid=' + nmd,
				success: function (answ) {
					$('#snomes').html(answ);
					UpdateChosen();
				}
			});
		}

		function GetListVendors(groupid, vendorid) {
			$.ajax({
				url: '/route/controller/server/common/getlistvendors.php?groupid=' + groupid + '&vendorid=' + vendorid,
				success: function (answ) {
					$('#svendors').html(answ);
					GetListNome($('#sgroupname :selected').val(), $('#svendid :selected').val(), nomeid);
					$('#svendid').on('change', function (evt, params) {
						$('#snomes').html = 'идет загрузка...'; // заглушка. Зачем?? каналы счас быстрые
						GetListNome($('#sgroupname :selected').val(), $('#svendid :selected').val());
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

		// правка Мазур
		$('#bshtr').click(function Calculate() {
			$.get('/route/controller/server/common/getean13.php', function (data) {
				$('#shtrihkod').val(data);
			});
			return false;
		});
		// конец правки Мазур

		$('#sorgid').on('change', function (evt, params) {
			$('#splaces').html = 'идет загрузка...'; // заглушка. Зачем?? каналы счас быстрые
			$("#susers").html = 'идет загрузка...';
			GetListPlaces($('#sorgid :selected').val(), ''); // перегружаем список помещений организации
			GetListUsers($('#sorgid :selected').val(), ''); // перегружаем пользователей организации
		});

		// выбираем производителя по группе
		$('#sgroupname').on('change', function (evt, params) {
			console.log('--обработка выбора группы номенклатуры');
			$('#svendors').html = 'идет загрузка...'; // заглушка. Зачем?? каналы счас быстрые
			GetListVendors($('#sgroupname :selected').val()); // перегружаем список vendors
		});

		// загружаем места
		GetListPlaces($('#sorgid :selected').val(), placesid);

		// загружаем пользователей
		GetListUsers($('#sorgid :selected').val(), userid);

		// загружаем производителя
		GetListVendors($('#sgroupname :selected').val(), vendorid);

		// номенклатура
		GetListNome($('#sgroupname :selected').val(), $('#svendid :selected').val(), nomeid);
	</script>
	<script>
		var FileAPI = {
			debug: true,
			media: true,
			staticPath: './FileAPI/'
		};
	</script>
	<script src="/js/FileAPI/FileAPI.min.js"></script>
	<script src="/js/FileAPI/FileAPI.exif.js"></script>
	<script src="/js/jquery.fileapi.min.js"></script>
	<script src="/js/jcrop/jquery.Jcrop.min.js"></script>
	<script src="/js/statics/jquery.modal.js"></script>
	<script>
		for (var selector in config) {
			$(selector).chosen(config[selector]);
		}
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
<?php endif; ?>
