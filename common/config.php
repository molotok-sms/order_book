<?php

//
// Общие настройки сайта
//

// Кодовая страница сайта
define('CODEPAGE', 'utf-8');
// Домен сайта
define('SITE_DOMAIN', 'orders');

// Размещение сайта в поддиректории корневой директории веб-сервера
define('WWW', '');
//define('WWW', '/test');


//
// Настройки подключения к БД
//

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'orders');
define('DB_USER', 'orders');
define('DB_PASS', 'a6WyYSvq5y');
define('DB_PREFIX', '');
define('DB_CODEPAGE', 'utf8');


//
// Прочие настройки
//

// Определение корневой директории сайта
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . WWW);

// Настройка вспомогательных директорий
define('LOG', ROOT . '/log');
define('TMP', ROOT . '/tmp');
define('IMG', ROOT . '/images');
define('IMG_WWW', WWW . '/images');


?>
