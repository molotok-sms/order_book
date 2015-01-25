<?php

// Функция реализации контроллера
function controller_orders ($params)
{
	global $_header;
	global $_header_title;
	global $_user;
	
	
	// Получение POST-параметров
	$fajax = isset($_POST['ajax']) && $_POST['ajax'] ? true : false;
	
	
	// Инициализация фильтра списка заказов
	$filter = false;
	
	// Инициализация параметров
	if (!isset($params[0])) $params[0] = '';
	if (!isset($params[1])) $params[1] = '';
	if (!isset($params[2])) $params[2] = '';
	
	
	// Если запрошена страница "Разместить заказ"
	if ($params[0] == 'add')
	{
		// Вызов обработчика размещения заказа
		controller_orders_add();
		
	}
	// Иначе, если запрошена страница "Выполненные заказы"
	elseif ($params[0] == 'go')
	{
		// Настройка заголовка страницы
		$_header_title = 'История заказов';
		
		// Настраиваем фильтр "только выполненные мною заказы"
		$filter = array('executor_uid' => UID);
		
		// Удаляем первый параметр
		array_shift($params);
		
	}
	// Иначе, если запрошен конкретный заказ
	elseif (($params[0] == 'item') && is_numeric($params[1]) && ($params[1] > 0))
	{
		// Вызов обработчика просмотра заказа
		controller_orders_item($params[1], $params[2]);
		
	}
	// Иначе, если запрошен список заказов
	elseif ($params[0] == 'list')
	{
		// Настройка заголовка страницы
		$_header_title = '';
		
		// Удаляем первый параметр
		array_shift($params);
		
	}
	// Иначе, если запрошена страница "Мои заказы"
	elseif ($params[0] == 'my')
	{
		// Настройка заголовка страницы
		$_header_title = 'Мои заказы';
		
		// Настраиваем фильтр "выводить только мои заказы"
		$filter = array('customer_uid' => UID);
		
		// Удаляем первый параметр
		array_shift($params);
		
	}
	
	
	// Получение кол-ва заказов
	$orders_count = orders_get_count();
	$orders_count = $orders_count['result'];
	// Получение кол-ва страниц
	$page_count = floor($orders_count / ORDERS_ON_PAGE);
	
	// Приведение к типу номера страницы
	$params[0] = (int) $params[0];
	// Проверка на граничные значения
	if ($params[0] < 1) $params[0] = 1;
	if ($params[0] > $page_count) $params[0] = 1;
	
	
	// Инициализация данных
	$_data = array('status' => false, 'error' => '', 'error_field' => '', 'data' => array(), 'page' => $params[0], 'page_count' => $page_count);
	
	// Получение списка заказов
	$result = orders_get(false, $filter, ORDERS_ON_PAGE * ($params[0] - 1), ORDERS_ON_PAGE);
	
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
	global $_header_title;
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


// Функция реализации просмотра заказа
function controller_orders_item ($oid, $action='')
{
	global $_header;
	global $_header_title;
	global $_user;
	
	
	// Получение входных параметров
	$fajax = isset($_POST['ajax']) && $_POST['ajax'] ? true : false;
	$fgo = ($action == 'go') ? true : false;
	
	
	// Инициализация данных
	$_data = array('status' => false, 'error' => '', 'error_field' => '', 'data' => array());
	
	
	// Если запрошено выполнение заказа
	if ($fgo)
	{
		// Выполнение заказа
		$result = orders_go($oid);
		
		// Сохранение текста ошибки
		$_data['error'] = $result['error'];
		
		// Если выполнение заказа успешно
		if ($result['result'])
		{
			// Сохранение результата
			$_data['status'] = true;
			
		}
		
		// Если это AJAX-запрос
		if ($fajax)
		{
			// Если выполнение заказа успешно
			if ($result['result'])
			{
				// Завершение выполнения с выводом успешного результата
				die('OK');
				
			}
			else
			{
				// Завершение выполнения с выводом ошибки
				die($result['error']);
				
			}
			
		}
		
	}
	
	
	// Получение информации о заказе
	$result = orders_get($oid);
	
	// Если выполнение запроса успешно
	if (is_array($result['result']) && count($result['result']))
	{
		// Сохранение результата
		$_data['status'] = true;
		$_data['data'] = $result['result'];
		
	}
	
	
	// Если это AJAX-запрос, отключение вывода колонтитулов
	if ($fajax) $_header = false;
	
	// Подключение представления
	require(APP . '/views/orders.item.php');
	
	// Завершение выполнения
	exit;
	
}


// Функция реализации просмотра своих заказов
function controller_orders_my ()
{
	global $_header;
	global $_header_title;
	global $_user;
	
	
	// Подключение представления
	require(APP . '/views/orders.my.php');
	
	// Завершение выполнения
	exit;
	
}


?>
