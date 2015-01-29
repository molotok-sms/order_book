<?php

// Функция реализации контроллера
function controller_about ()
{
	global $_header;
	global $_header_title;
	global $_user;
	
	
	// Получение входных параметров
	$fajax = isset($_REQUEST['ajax']) && $_REQUEST['ajax'] ? true : false;
	
	
	// Получение данных
	$_data = about_get();
	
	
	// Если это AJAX-запрос, отключение вывода колонтитулов
	if ($fajax) $_header = false;
	
	// Настройка заголовка страницы
	$_header_title = 'О проекте';
	// Настройка адреса текущей страницы
	$_page = 'about';
	
	// Подключение представления
	require(APP . '/views/about.php');
	
}


?>
