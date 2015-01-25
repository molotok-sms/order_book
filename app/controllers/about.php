<?php

// Функция реализации контроллера
function controller_about ()
{
	global $_header;
	global $_header_title;
	global $_user;
	
	
	// Подключение представления
	require(APP . '/views/about.php');
	
}


?>
