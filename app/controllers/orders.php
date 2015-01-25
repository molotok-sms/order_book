<?php

// Функция реализации контроллера
function controller_orders ($action='')
{
	global $_header;
	global $_user;
	
	
	// Получение входных параметров
	$fajax = isset($_POST['ajax']) && $_POST['ajax'] ? true : false;
	
	
	// Если запрошена страница "Разместить заказ"
	if ($action == 'add')
	{
		// Вызов обработчика размещения заказа
		controller_orders_add();
		
	}
	// Иначе, если запрошена страница "Мои заказы"
	elseif ($action == 'my')
	{
		// Вызов обработчика просмотра своих заказов
		controller_orders_my();
		
	}
	
	
	// Инициализация данных
	$_data = array('status' => false, 'error' => '', 'error_field' => '', 'data' => array(), 'page' => 1);
	
	
	// Получение списка заказов
	$result = orders_get();
	
	// Если выполнение запроса успешно
	if (is_array($result['result']))
	{
		// Сохранение результата
		$_data['status'] = true;
		$_data['data'] = $result['result'];
		
	}
	
	
	// Если это AJAX-запрос, отключение вывода колонтитулов
	if ($fajax) $_header = false;
	
	// Подключение представления
	require(APP . '/views/orders.list.php');
	
}


// Функция реализации размещения заказа
function controller_orders_add ()
{
	global $_header;
	global $_user;
	
	
	// Получение входных параметров
	$fajax				= isset($_POST['ajax']) && $_POST['ajax'] ? true : false;
	$faction			= isset($_POST['order_title']) ? true : false;
	$order_title		= isset($_POST['order_title']) ? $_POST['order_title'] : '';
	$order_description	= isset($_POST['order_description']) ? $_POST['order_description'] : '';
	$order_price		= isset($_POST['order_price']) ? $_POST['order_price'] : '';
	
	
	// Экранирование входных параметров с удалением пробельных символов
	$order_title		= htmlentities(trim($order_title), ENT_QUOTES);
	$order_description	= htmlentities(trim($order_description), ENT_QUOTES);
	$order_price		= htmlentities(trim($order_price), ENT_QUOTES);
	
	// Приведение к типу
	$order_price = (double) str_replace(',', '.', $order_price);
	
	
	// Инициализация данных
	$_data = array('status' => false, 'error' => '', 'error_field' => '', 'data' => array
	(
		'order_title' => $order_title,
		'order_description' => $order_description,
		'order_price' => $order_price
		
	));
	
	
	// Если запрошено размещение заказа
	if ($faction)
	{
		// Размещение заказа
		$result = orders_add($order_title, $order_description, $order_price);
		
		// Сохранение текста ошибки
		$_data['error'] = $result['error'];
		// Сохранение имени поля содержащего ошибку
		$_data['error_field'] = $result['error_arg'];
		
	}
	
	
	// Если нет действия или произошла ошибка размещения заказа
	if (!$faction || !$result['result'])
	{
		// Если это AJAX-запрос, отключение вывода колонтитулов
		if ($fajax) $_header = false;
		
		// Подключение представления
		require(APP . '/views/orders.add.php');
		
		// Завершение выполнения
		exit;
		
	}
	
}


// Функция реализации просмотра своих заказов
function controller_orders_my ()
{
	// Подключение представления
	require(APP . '/views/orders.my.php');
	
	// Завершение выполнения
	exit;
	
}


?>
