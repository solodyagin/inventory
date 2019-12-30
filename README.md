# Inventory

Система предназначена для учёта оргтехники в небольших организациях и будет полезна в основном инженерам IT отдела.

Основным отличием от [donpadlo/webuseorg3](https://github.com/donpadlo/webuseorg3) является "облегчение" системы  от модулей, не относящихся, по-моему мнению, к учёту оргтехники.

Домашняя страница проекта WebUseOrg: <a href="http://xn--90acbu5aj5f.xn--p1ai/?page_id=1202" target="_blank">http://грибовы.рф/?page_id=1202</a>

Wiki: [http://грибовы.рф/wiki/doku.php/start](http://xn--90acbu5aj5f.xn--p1ai/wiki/doku.php/start)

### Требования
1. Apache 2
  - mod_rewrite
2. PHP 7
  - extension=php_gd2.dll
  - extension=php_ldap.dll
  - extension=php_pdo_mysql.dll
  - extension=php_xmlrpc.dll
3. MySQL или MariaDB

### Установка

1. Запустить инсталлятор _http://адрессайта/install/index.php_
2. Поправить права на папки `files`, `photo`, `maps` на 0777
3. Удалить каталог _install_
