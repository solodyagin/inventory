<?php

/* 
 * (с) 2011-2015 Грибов Павел
 * http://грибовы.рф * 
 * Если исходный код найден в сети - значит лицензия GPL v.3 * 
 * В противном случае - код собственность ГК Яртелесервис, Мультистрим, Телесервис, Телесервис плюс * 
 */

include_once ("../../../config.php");                    // загружаем первоначальные настройки

// загружаем классы

include_once("../../../class/sql.php");               // загружаем классы работы с БД
include_once("../../../class/config.php");		// загружаем классы настроек
include_once("../../../class/users.php");		// загружаем классы работы с пользователями
include_once("../../../class/employees.php");		// загружаем классы работы с профилем пользователя


// загружаем все что нужно для работы движка

include_once("../../../inc/connect.php");		// соеденяемся с БД, получаем $mysql_base_id
include_once("../../../inc/config.php");              // подгружаем настройки из БД, получаем заполненый класс $cfg
include_once("../../../inc/functions.php");		// загружаем функции
include_once("../../../inc/login.php");		// загружаем функции


$page = _GET('page');
$limit = _GET('rows');
$sidx = _GET('sidx'); 
$sord = _GET('sord'); 
$oper= _POST('oper');
$id = _POST('id');
$name = _POST('name');
$chanel_id = _POST('chanel_id');
$astra_id=_GET('astra_id');
$group_id=_POST('group_id');

if ($oper==''){
	if(!$sidx) $sidx =1;
	$result = $sqlcn->ExecuteSQL("SELECT COUNT(*) AS count FROM astra_chanels where astra_id='$astra_id'");
        //echo "SELECT COUNT(*) AS count FROM astra_mon where astra_id='$astra_id'";
	$row = mysqli_fetch_array($result);
	$count = $row['count'];

	if( $count >0 ) {$total_pages = ceil($count/$limit);} else {$total_pages = 0;};
	if ($page > $total_pages) $page=$total_pages;

        $responce=new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
        if ($count>0){
            $start = $limit*$page - $limit;
            $SQL = "SELECT * FROM astra_chanels where astra_id='$astra_id' ORDER BY $sidx $sord LIMIT $start , $limit";
            //echo "$SQL";
            $result = $sqlcn->ExecuteSQL( $SQL ) or die("Не могу выбрать список страниц!".mysqli_error($sqlcn->idsqlconnection));
            $i=0;
            while($row = mysqli_fetch_array($result)) {
                    $responce->rows[$i]['id']=$row['id'];
                    $responce->rows[$i]['cell']=array($row['id'],$row['chanel_id'],$row['name'],$row['group_id']);		
                    $i++;
            };
        } else {
          $responce->page = 1;  
        };
	echo json_encode($responce);
};
if (($oper=='add')){
	$SQL = "INSERT INTO astra_chanels (id,astra_id,name,chanel_id,group_id) VALUES (null,'$astra_id','$name','$chanel_id','$group_id')";        
	$result = $sqlcn->ExecuteSQL( $SQL ) or die("Не могу добавить страницу!".mysqli_error($sqlcn->idsqlconnection));

};
if (($oper=='edit')){
	$SQL = "UPDATE astra_chanels SET name='$name',chanel_id='$chanel_id',group_id='$group_id' WHERE id='$id'";        
	$result = $sqlcn->ExecuteSQL( $SQL ) or die("Не могу обновить страницу!".mysqli_error($sqlcn->idsqlconnection));

};

if ($oper=='del'){
	$SQL = "delete FROM astra_chanels WHERE id='$id'";
	$result = $sqlcn->ExecuteSQL( $SQL ) or die("Не могу удалить страницу!".mysqli_error($sqlcn->idsqlconnection));
};

?>