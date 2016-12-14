# Учёт оргтехники в организации v3.X 2011-2016 Lite

Система предназначена для учёта оргтехники в небольших организациях и будет полезна в основном инженерам IT отдела.

Основным отличием от [donpadlo/webuseorg3](https://github.com/donpadlo/webuseorg3) является "облегчение" системы  от модулей, не относящихся, по-моему мнению, к учёту оргтехники.

Домашняя страница проекта: <a href="http://xn--90acbu5aj5f.xn--p1ai/?page_id=1202" target="_blank">http://грибовы.рф/?page_id=1202</a>

Wiki: [http://грибовы.рф/wiki/doku.php/start](http://xn--90acbu5aj5f.xn--p1ai/wiki/doku.php/start)

### Требования
1. Apache 2
  - mod_rewrite
2. PHP 5
  - extension=php_gd2.dll
  - extension=php_ldap.dll
  - extension=php_mysqli.dll
  - extension=php_pdo_mysql.dll
  - extension=php_xmlrpc.dll
3. MySQL или MariaDB

### Установка

1. Запустить инсталлятор _http://адрессайта/install.php_
2. Переименовать файл `config.php.dist` в `config.php` и отредактировать.
3. Поправить права на папки `files`, `photo`, `maps` на 0777
4. Удалить инсталлятор  _http://адрессайта/install.php_

Если используете пакет "Денвер", то необходимо в `httpd.conf` изменить кодироку по умолчанию: 
`AddDefaultCharset utf-8`  
Так-же при отображении "кракозябров", возможно стоит поменять кодировку в файле `config.php`. Например `$mysql_char = 'utf8-bin';` или `$mysql_char = 'utf8_general_ci';`

### Обновление

Обновления являются куммулятивными — для обновления до последней версии нужен только самый последний релиз.

1. Скачать последнюю версию данного ПО.
2. Копировать её 1 в 1 с заменой файлов в каталог с предыдущей версией (за исключением `config.php` в корне).
3. Открыть в браузере _http://адрессайта/update.php_

Не забудьте после обновления удалить файл `update.php`
