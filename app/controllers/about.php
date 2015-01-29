<?php

// Функция реализации контроллера
function controller_about ()
{
	global $_header;
	global $_header_title;
	global $_user;
	
	
	// Получение данных
	$_data = about_get();
	
	// Настройка адреса текущей страницы
	$_page = 'about';
	
	// Подключение представления
	require(APP . '/views/about.php');
	
}


?>
