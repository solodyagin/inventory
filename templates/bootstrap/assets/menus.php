<?php
/*
 * WebUseOrg3 Lite - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

# Запрещаем прямой вызов скрипта.
defined('WUO') or die('Доступ запрещён');
?>
<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/">WebUseOrg3 Lite</a>
		</div>
		<div class="collapse navbar-collapse" id="navbar-collapse-1">
			<ul class="nav navbar-nav">
				<?php

				function PutMenu($par) {
					global $gmenu;
					$list = $gmenu->GetList($par);
					foreach ($list as $key => $pmenu) {
						$nm = $pmenu['name'];
						$path = $pmenu['path'];
						$uid = $pmenu['uid'];
						$url = ($path == '') ? 'javascript:;' : "$path";
						if (count($gmenu->GetList($uid)) > 0) {
							echo '<li class="dropdown">';
							echo "<a href=\"$url\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">$nm <span class=\"caret\"></span></a>";
							echo '<ul class="dropdown-menu">';
							PutMenu($uid);
							echo '</ul>';
						} else {
							echo '<li>';
							echo "<a href=\"$url\">$nm</a>";
						}
						echo '</li>';
					}
				}

				PutMenu('main');
				unset($mm);
				?>
			</ul>
		</div>
	</div>
</nav>
