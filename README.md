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

### Запуск Inventory в Windows 10 через Docker.
1. Установить пакетный менеджер chocolatey, если он не установлен ( https://goo.gl/dVvhWp )
2. Установить docker, docker-compose, git. Для этого открыть новую командную строку от имени Администратора и ввести
```
  choco install -y docker-for-windows docker-compose git
```
3. Тут желательно перезагрузить компьютер. И в настройках Docker разрешить доступ к дискам (Shared Drives)
4. Скачать WAMP stack (Apache, MySQL, PHP)
```
  mkdir wamp
  cd wamp
  git clone https://github.com/solodyagin/docker-compose-wamp.git .
```
5. Скачать Inventory
```
  cd www
  rm .gitignore
  git clone https://github.com/solodyagin/inventory.git .
```
6. Запустить docker
```
  cd ..
  docker-compose up
```
Для удобства команды 4, 5 и 6 пунктов объединил в файл inventory-install.cmd ( https://goo.gl/AxwSxL )

Система Inventory доступна по адресу: http://localhost

При установке системы в качестве сервера MySQL указать "**mysql**", пользователь баз данных "root", пароль: "tiger"

phpMyAdmin: http://localhost:8080 \
Пользователь: root \
Пароль: tiger
