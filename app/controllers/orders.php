<?php

// Функция реализации контроллера
function controller_orders ($action='')
{
	global $_header;
	global $_user;
	
	
	// Если запрошена страница "Разместить заказ"
	if ($action == 'add')
	{
		// Подключение представления
		require(APP . '/views/orders.add.php');
		
	}
	// Иначе, если запрошена страница "Мои заказы"
	elseif ($action == 'my')
	{
		// Подключение представления
		require(APP . '/views/orders.my.php');
		
	}
	else
	{
		// Получение списка заказов
		$_data = orders_get();
		
		// Подключение представления
		require(APP . '/views/orders.list.php');
		
	}
	
}


?>
