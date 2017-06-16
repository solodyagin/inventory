<?php
/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');
?>
<script>
	$(function () {
		$('#menu').mmenu({
			extensions: ['effect-zoom-menu', 'effect-zoom-panels', 'pageshadow', 'iconbar'],
			header: true,
			searchfield: false,
			counters: true,
			dragOpen: true,
			navbar: {title: 'Меню', panelTitle: 'Меню'},
			onClick: {
				setSelected: true,
				close: false
			}
		});
	});
</script>
<nav id="menu">
	<?php

	function PutMenu($par) {
		global $gmenu, $content_page;
		echo '<ul>';
		$list = $gmenu->GetList($par);
		foreach ($list as $key => $pmenu) {
			$nm = $pmenu['name'];
			$path = $pmenu['path'];
			$uid = $pmenu['uid'];
			$url = ($path == '') ? 'javascript:;' : "$path";
			$sel = ($content_page == $path) ? ' class="selected"' : '';
			echo "<li$sel>";
			echo "<a href=\"$url\">$nm</a>";
			if (count($gmenu->GetList($uid)) > 0) {
				PutMenu($uid);
			}
			echo '</li>';
		}
		echo '</ul>';
	}

	PutMenu('main');
	unset($mm);
	?>
</nav>