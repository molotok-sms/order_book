<?php

// Функция реализации контроллера
function controller_orders ()
{
	global $_header;
	global $_user;
	
	
	// Получение списка заказов
	$_data = orders_get();
	
	// Подключение представления
	require(APP . '/views/orders.list.php');
	
}


?>
