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
		},
		saveCommonParam: function (stname) {
			var colarray = $(this).jqGrid('getGridParam', 'colModel');
			localStorage.setItem(stname, JSON.stringify(colarray));
		},
		loadCommonParam: function (stname) {
			if (localStorage[stname] !== undefined) {
				var colarray = localStorage[stname];
				if (colarray !== '') {
					var arr = JSON.parse(colarray);
					for (var i in arr) {
						if (arr[i].hidden === true) {
							$(this).hideCol(arr[i].name);
						} else {
							$(this).showCol(arr[i].name);
							if (arr[i].fixed === true) {
								$(this).setColWidth(arr[i].name, arr[i].width);
							}
						}
					}
				}
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

		$dlg.off('show.bs.modal').on('show.bs.modal', function () {
			$dlg.find('.modal-title').text(settings.title || '');
			$dlg.find('.modal-dialog').css({
				width: settings.width + 'px'
//				, height: settings.height + 'px'
			});
			$dlg.find('iframe').attr('src', settings.src);
		});

		$dlg.off('hidden.bs.modal').on('hidden.bs.modal', function () {
			$dlg.find('iframe').empty().attr('src', '');
		});

		return $dlg;
	};
})(jQuery);
