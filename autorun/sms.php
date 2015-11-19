<?php
// Данный код создан и распространяется по лицензии GPL v3
// Изначальный автор данного кода - Грибов Павел
// http://грибовы.рф

/* 

 Назначение:

если подключен модуль СМС, то смотрим какие агенты введены.
если есть основной агент, то загружаем его "прокладку" для взаимодействия.
"прокладка" должна содержать класс smsinfo со следующими вызовами:
sms=new SmsAgent
sms->sender='bla-bla'
sms->login='bla-bla'
sms->pass='bla-bla'
sms->smsdiff='bla-bla'
sms->agentname='bla-bla'
sms->login(login,pass)
sms->GetBalanse();
sms->sendsms(phone,txt)        
 
*/

$md=new Tmod; // обьявляем переменную для работы с классом модуля
if ($md->IsActive("smscenter")==1) {    
    $sql="select * from sms_center_config where sel='Yes'";
    $result = $sqlcn->ExecuteSQL($sql) or die("Не могу проситать настройки sms_center_config!".mysqli_error($sqlcn->idsqlconnection));
    while($row = mysqli_fetch_array($result)) {
      $fileagent=$row["fileagent"];  
      @include_once ("inc/$fileagent");
      @include_once ("../inc/$fileagent");
      @include_once ("../../inc/$fileagent");
      @include_once ("../../../inc/$fileagent");
      @include_once ("../../../../inc/$fileagent");
      @include_once ("../../../../../inc/$fileagent");      
      @include_once ("../../../../../../inc/$fileagent");
      @include_once ("../../../../../../inc/$fileagent");
      @include_once ("/usr/local/www/apache22/ssl/data/inc/$fileagent");
    };
unset($md);
};

?>