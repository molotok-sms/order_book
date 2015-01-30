<?php

// Старт сессии пользователя
session_start();

// Проверка наличия конфигурационного файла сайта
if (!file_exists(__DIR__ . '/config.php'))
{
	// Завершение выполнения с ошибкой
	die('<div style="background-color:pink;border-radius:6px;margin:100 auto;padding:30;width:500;"><h1>Config file not found!</h1><h2>Please, copy "config.php.dist" and use it.</h2></div>');
	
}

// Подключение конфигурационного файла сайта
require_once('config.php');


// Настройка локали
setlocale(LC_ALL, LOCALE, LOCALE_ALT);
setlocale(LC_ALL, '.' . CODEPAGE);
// Настройка часового пояса по умолчанию
date_default_timezone_set('UTC');


// Подключение библиотеки вспомогательных функций
require_once(__DIR__ . '/funcs.php');
// Подключение обработчика ошибок и исключений
require_once(__DIR__ . '/error_handler.php');


// Подключение оболочки Базы Данных
require_once(__DIR__ . '/db.php');
// Подключение к Базе Данных
//db_connect(DB);


// Настройка кодировки страниц
header('Content-type: text/html; charset=' . CODEPAGE);
// Разрешение отправки клиентом cookies и данных аутентифицкаии (для отправки форм через HTTPS)
header('Access-Control-Allow-Credentials: true');
// Разрешение отправки HTTP-запросов (для отправки форм через HTTPS)
header('Access-Control-Allow-Origin: http://' . SITE_DOMAIN);


?>
