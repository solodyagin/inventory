/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

$(function () {
	$.jgrid.extend({
		setColWidth: function (iCol, newWidth, adjustGridWidth) {
			return this.each(function () {
				var $self = $(this), grid = this.grid, p = this.p, colName, colModel = p.colModel, i, nCol;
				if (typeof iCol === 'string') {
					colName = iCol;
					for (i = 0, nCol = colModel.length; i < nCol; i++) {
						if (colModel[i].name === colName) {
							iCol = i;
							break;
						}
					}
					if (i >= nCol) {
						return;
					}
				} else if (typeof iCol !== 'number') {
					return;
				}
				grid.resizing = {idx: iCol};
				grid.headers[iCol].newWidth = newWidth;
				grid.newWidth = p.tblwidth + newWidth - grid.headers[iCol].width;
				grid.dragEnd();
				if (adjustGridWidth !== false) {
					$self.jqGrid('setGridWidth', grid.newWidth, false);
				}
			});
		}
	});
	$.jgrid.extend({
		saveCommonParam: function (stname) {
			// Информация о колонках
			colarray = $(this).jqGrid('getGridParam', 'colModel');
			localStorage.setItem(stname, JSON.stringify(colarray));
			//console.log(JSON.stringify(colarray));
		},
		loadCommonParam: function (stname) {
			if (localStorage[stname] !== undefined) {
				colarray = localStorage[stname];
				if (colarray !== '') {
					obj_for_load = JSON.parse(colarray); // загружаем JSON в массив
					for (i in obj_for_load) {
						//console.log("name:",obj_for_load[i].name);
						//console.log("width:",obj_for_load[i].width);
						if (obj_for_load[i].hidden === true) {
							$(this).hideCol(obj_for_load[i].name);
						} else {
							$(this).showCol(obj_for_load[i].name);
							if (obj_for_load[i].fixed === true) {
								$(this).setColWidth(obj_for_load[i].name, obj_for_load[i].width);
							}
						}
					}
				}
			} else {
				console.log('В локальном хранилище не найден ключ ' + stname);
			}
		}
	});
});

// iFrame inside Bootstrap 3 modal
(function ($) {
	$.fn.bmdIframe = function (options) {
		var $dlg = $(this),
				settings = $.extend({
					classBtn: '.bmd-modal-button',
					width: Math.max(Math.round($(window).width() * 0.6), 640),
					height: Math.max(Math.round($(window).height() * 0.6), 360)
				}, options);

		$(settings.classBtn).on('click', function () {
			var $btn = $(this);
			$dlg.find('.modal-title').text($btn.attr('title') || '');
			$dlg.find('.modal-dialog').css({
				width: ($btn.data('bmdWidth') || settings.width) + 'px'
				//, height: ($btn.data('bmdHeight') || settings.height) + 'px'
			});
			$dlg.find('iframe').attr('src', $btn.data('bmdSrc') || settings.src);
		});

		$dlg.on('show.bs.modal', function () {
			$dlg.find('.modal-title').text(settings.title || '');
			$dlg.find('.modal-dialog').css({
				width: settings.width + 'px'
				//, height: settings.height + 'px'
			});
			$dlg.find('iframe').attr('src', settings.src);
		});

		$dlg.on('hidden.bs.modal', function () {
			$dlg.find('iframe').empty().attr('src', '');
		});

		return $dlg;
	};
})(jQuery);
