<?php

// Настройка поддиректории в корневой директории веб-сервера
define('WWW', '');
//define('WWW', '/test');

// Определение корневой директории сайта
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . WWW);

// Настройка вспомогательных директорий
define('LOG', ROOT . '/log');
define('TMP', ROOT . '/tmp');
define('IMG', ROOT . '/images');
define('IMG_WWW', WWW . '/images');

// Настройка темы оформления
define('THEME', 'cupertino');

// Настройка подключения к Базе Данных
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'orders');
define('DB_USER', 'orders');
define('DB_PASS', 'a6WyYSvq5y');
define('DB_PREFIX', '');
define('DB_CODEPAGE', 'utf8');

// Прочие настройки
define('CODEPAGE', 'utf-8');


?>
