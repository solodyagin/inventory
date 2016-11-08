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
$eqid = GetDef('eqid');
$oper = PostDef('oper');
$id = PostDef('id');
if ($id != '') {
	$eqid = $id;
}

if (($user->mode == 1) || $user->TestRoles('1,4,5,6')) {
	if ($step == 'add') {
		$dtpost = DateToMySQLDateTime2(PostDef('dtpost') . ' 00:00:00');
		if ($dtpost == '') {
			$err[] = 'Не выбрана дата!';
		}
		$dt = DateToMySQLDateTime2(PostDef('dt') . ' 00:00:00');
		if ($dt == '') {
			$err[] = 'Не выбрана дата!';
		}
		$kntid = PostDef('kntid');
		if ($kntid == '') {
			$err[] = 'Не выбран контрагент!';
		}
		$cst = PostDef('cst');
		$status = PostDef('status');
		$comment = PostDef('comment');
		$doc = PostDef('doc');
		$suserid1 = PostDef('suserid1', '-1');
		$suserid2 = PostDef('suserid2', '-1');
		if (count($err) == 0) {
			$sql = <<<TXT
INSERT INTO repair
            (id,dt,kntid,eqid,cost,comment,dtend,status,userfrom,userto,doc)
VALUES      (NULL,'$dtpost','$kntid','$eqid','$cst','$comment','$dt','1','$suserid1','$suserid2','$doc')
TXT;
			$result = $sqlcn->ExecuteSQL($sql)
					or die('Не смог добавить ремонт!: ' . mysqli_error($sqlcn->idsqlconnection));
			// ставим статус "ремонт", только если нужен сервис в общем списке ТМЦ
			if ($status != 0) {
				$sql = "UPDATE equipment SET repair = '$status' WHERE id = '$eqid'";
				$sqlcn->ExecuteSQL($sql)
						or die('Не смог обновить запись о ремонте!: ' . mysqli_error($sqlcn->idsqlconnection));
			}
		}
	}
	if ($step == 'edit') {
		$dt = DateToMySQLDateTime2(PostDef('dtpost') . ' 00:00:00');
		$dtend = DateToMySQLDateTime2(PostDef('dt') . ' 00:00:00');
		$cost = PostDef('cst');
		$comment = PostDef('comment');
		$rstatus = PostDef('status');
		$doc = PostDef('doc');
		$suserid1 = PostDef('suserid1');
		$suserid2 = PostDef('suserid2');
		$kntid = PostDef('kntid');
		$sql = <<<TXT
UPDATE repair
SET    dt = '$dt',dtend = '$dtend',cost = '$cost',comment = '$comment',status = '$rstatus',doc = '$doc',
       userfrom = '$suserid1',
       userto = '$suserid2',kntid = '$kntid'
WHERE  id = '$eqid'
TXT;
		$sqlcn->ExecuteSQL($sql)
				or die('Не смог обновить статус ремонта! ' . mysqli_error($sqlcn->idsqlconnection));
		ReUpdateRepairEq();
		exit;
	}
}

if ($step != 'list') {
	if (count($err) == 0) {
		echo 'ok';
	} else {
		echo '<script>$("#messenger").addClass("alert alert-error");</script>';
		for ($i = 0; $i <= count($err); $i++) {
			echo "$err[$i]<br>";
		}
	}
}
