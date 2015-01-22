<?php

// Старт сессии пользователя
session_start();

// Подключение конфигурационного файла сайта
require_once('config.php');

// Настройка часового пояса по умолчанию
date_default_timezone_set('UTC');

// Подключение оболочки Базы Данных
require_once(ROOT . '/common/db.php');
// Подключение к Базе Данных
db_connect('host=' . DB_HOST . ' port=' . DB_PORT . ' user=' . DB_USER . ' password=' . DB_PASS . ' dbname=' . DB_NAME);
// Настройка кодировки подключения к Базе Данных
db_set_encoding(DB_CODEPAGE);

// Настройка кодировки страниц
header('Content-type: text/html; charset=' . CODEPAGE);


?>
