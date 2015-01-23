<?php

// Подключение движка сайта
require_once('common/common.php');

// Получение входных параметров
$url = isset($_REQUEST['_url']) ? $_REQUEST['_url'] : '/';


// Удаляем имя домена из адреса (при наличии О_о)
$url = preg_replace('#^http[s]{0,1}://' .  SITE_DOMAIN . WWW . '/#i', '/', $url);
// Разбор входных параметров по разделителю (слэш)
$params = preg_split('#/+#', $url, -1, PREG_SPLIT_NO_EMPTY);
var_dump_log($params);


// Инициализация переменных
$_model = '';
$_view = '';
$_controller = '';

// Заполнение параметров
if (count($params)) $_model = array_shift($params);
if (count($params)) $_view = array_shift($params);
if (count($params)) $_controller = array_shift($params);

// Очищение названий модулей от посторонних символов
$_model = preg_replace('#[^A-Za-z0-9]+#', '', $_model);
$_view = preg_replace('#[^A-Za-z0-9]+#', '', $_view);
$_controller = preg_replace('#[^A-Za-z0-9]+#', '', $_controller);


// Если указана несуществующая модель
if (!$_model || !file_exists(APP . '/models/' . $_model . '.php'))
{
	// Замена модели по умолчанию
	$_model = 'index';
	// Замена представления по умолчанию
	$_view = 'index';
	// Замена контроллера по умолчанию
	$_controller = '';
	
	// Если модель (по умолчанию) не существует
	if (!file_exists(APP . '/models/' . $_model . '.php'))
	{
		// Вывод сообщения об ошибке в лог-файл
		print_log('error', 'Not found default model "' . $_model . '.php"');
		
		// Установка кода ошибка HTTP
		http_response_code(500);
		
		// Завершение выполнения скрипта
		exit;
		
	}
	
}

// Если указано несуществующее представление
if (!$_view || !file_exists(APP . '/views/' . $_model . ($_view != 'index' ? '.' . $_view : '') . '.php'))
{
	// Замена представления по умолчанию
	$_view = 'index';
	
	// Если представление (по умолчанию) не существует
	if (!file_exists(APP . '/views/' . $_view . '.php'))
	{
		// Вывод сообщения об ошибке в лог-файл
		print_log('error', 'Not found default view "' . $_view  . '.php"');
		
		// Установка кода ошибка HTTP
		http_response_code(500);
		
		// Завершение выполнения скрипта
		exit;
		
	}
	
}

// Если указан несуществующий контроллер, подстановка значения по умолчанию
if (!$_controller || !file_exists(APP . '/controllers/' . $_model  . '.' . $_controller . '.php'))
{
	// Замена контроллера по умолчанию
	$_controller = '';
	
}





?>
