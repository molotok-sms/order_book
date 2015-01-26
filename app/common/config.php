<?php

//
// Общие настройки сайта
//

// Кодовая страница сайта
define('CODEPAGE', 'utf-8');
// Локальные настройки сайта
define('LOCALE', 'ru_RU');
define('LOCALE_ALT', 'rus_RUS');
// Кол-во заказов на странице
define('ORDERS_ON_PAGE', 5);
// Процент системы с заказов (0.05 - 5%)
define('PERCENTAGE_OF_ORDERS', '0.05');
// Максимальное время жизни сессии при простое
define('SESSION_MAX_IDLE', 1440);
// Домен сайта
define('SITE_DOMAIN', 'order_book');

// Размещение сайта в поддиректории корневой директории веб-сервера
define('WWW', '');
//define('WWW', '/subdir');


//
// Настройки подключения к БД
//

define('DB', 'host=localhost port=3306 user=order_book password=a6WyYSvq5y dbname=order_book codepage=utf8');


//
// Прочие настройки
//

// Определение корневой директории сайта
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . WWW);

// Настройка вспомогательных директорий
define('APP', ROOT . '/app');
define('LOG', ROOT . '/log');


?>
