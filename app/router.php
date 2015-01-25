<?php

// Подключение движка сайта
require_once('common/common.php');

// Получение входных параметров
$_url = isset($_REQUEST['_url']) ? $_REQUEST['_url'] : '/';


//////////////////////////////////////////////////
//
// Инициализация глобальных переменных
//
//////////////////////////////////////////////////

// Вызванный контроллер
$_controller = '';
// Запрошенное действие (не используется в контроллере "auth")
$_action = '';
// Дополнительные параметры в виде массива (в "сыром" виде)
$_params = array();

// Ошибка аутентификации
$_auth_error = '';

// Флаг "вывод колонтитулов" (может сбрасываться при AJAX-запросах)
$_header = true;
// Дополнительный заголовок страницы
$_header_title = '';

//////////////////////////////////////////////////


// Удаляем имя домена из адреса (при наличии О_о)
$_url = preg_replace('#^http[s]{0,1}://' .  SITE_DOMAIN . WWW . '/#i', '/', $_url);
// Разбор входных параметров по разделителю (слэш)
$_params = preg_split('#/+#', $_url, -1, PREG_SPLIT_NO_EMPTY);


// Подключение и вызов контроллера аутентификации
require_once(APP . '/controllers/auth.php'); call_user_func('controller_auth', $_params);


// Получение имени запрошенного контроллера
if (count($_params)) $_controller = array_shift($_params);
// Очищение имени контроллера от посторонних символов
$_controller = preg_replace('#[^A-Za-z0-9]+#', '', $_controller);


// Если указан несуществующий контроллер, подстановка значения по умолчанию
if (!$_controller || !file_exists(APP . '/controllers/' . $_controller . '.php'))
{
	// Замена контроллера по умолчанию
	$_controller = 'orders';
	
}


// Если существует файл модели
if (file_exists(APP . '/models/' . $_controller . '.php'))
{
	// Подключение модели
	require_once(APP . '/models/' . $_controller . '.php');
	
}

// Если существует файл контроллера
if (file_exists(APP . '/controllers/' . $_controller . '.php'))
{
	// Подключение контроллера
	require_once(APP . '/controllers/' . $_controller . '.php');
	
	// Вызов функции контроллера
	call_user_func('controller_' . $_controller, $_params);
	
}
else
{
	// Вывод сообщения об ошибке в лог-файл
	print_log('error', 'Not found controller "' . $_controller . '.php"');
	
	// Установка кода ошибка HTTP
	http_response_code(500);
	
	// Завершение выполнения скрипта
	exit;
	
}


?>
