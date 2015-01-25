<?php

// Функция реализации контроллера
function controller_register ($confirm='')
{
	global $_header;
	global $_header_title;
	global $_user;
	
	
	// Получение входных параметров
	$fajax				= isset($_POST['ajax']) && $_POST['ajax'] ? true : false;
	$fdata				= isset($_POST['register']) && $_POST['register'] ? true : false;
	$user_login			= isset($_POST['user_login']) ? $_POST['user_login'] : '';
	$user_pass			= isset($_POST['user_pass']) ? $_POST['user_pass'] : '';
	$user_pass_confirm	= isset($_POST['user_pass_confirm']) ? $_POST['user_pass_confirm'] : '';
	$user_last_name		= isset($_POST['user_last_name']) ? $_POST['user_last_name'] : '';
	$user_name			= isset($_POST['user_name']) ? $_POST['user_name'] : '';
	$user_second_name	= isset($_POST['user_second_name']) ? $_POST['user_second_name'] : '';
	$user_email			= isset($_POST['user_email']) ? $_POST['user_email'] : '';
	$user_customer		= isset($_POST['user_customer']) && $_POST['user_customer'] ? 1 : 0;
	$user_executor		= isset($_POST['user_executor']) && $_POST['user_executor'] ? 1 : 0;
	$fconfirm			= ($confirm) ? true : false;
	
	
	// Экранирование входных параметров с удалением пробельных символов
	$user_login			= htmlentities(trim($user_login), ENT_QUOTES);
	$user_pass			= htmlentities(trim($user_pass), ENT_QUOTES);
	$user_pass_confirm 	= htmlentities(trim($user_pass_confirm), ENT_QUOTES);
	$user_last_name 	= htmlentities(trim($user_last_name), ENT_QUOTES);
	$user_name			= htmlentities(trim($user_name), ENT_QUOTES);
	$user_second_name 	= htmlentities(trim($user_second_name), ENT_QUOTES);
	$user_email			= htmlentities(trim($user_email), ENT_QUOTES);
	
	
	// Инициализация данных
	$_data = array('status' => false, 'error' => '', 'error_field' => '', 'data' => array
	(
		'user_login' => $user_login,
		'user_pass' => $user_pass,
		'user_pass_confirm' => $user_pass_confirm,
		'user_last_name' => $user_last_name,
		'user_name' => $user_name,
		'user_second_name' => $user_second_name,
		'user_email' => $user_email,
		'user_customer' => $user_customer,
		'user_executor' => $user_executor
		
	));
	
	
	// Если есть данные
	if ($fdata)
	{
		// Регистрация пользователя
		$result = users_register($user_login, $user_pass, $user_pass_confirm, $user_last_name, $user_name, $user_second_name, $user_email, $user_customer, $user_executor, $fconfirm);
		
		// Сохранение текста ошибки
		$_data['error'] = $result['error'];
		// Сохранение имени поля содержащего ошибку
		$_data['error_field'] = $result['error_arg'];
		
	}
	
	
	// Если нет данных или произошла ошибка регистрации пользователя
	if (!$fdata || !$result['result'])
	{
		// Если это AJAX-запрос, отключение вывода колонтитулов
		if ($fajax) $_header = false;
		
		// Подключение представления
		require(APP . '/views/register.php');
		
	}
	// Иначе, если регистрация еще не подтверждена
	elseif (!$fconfirm)
	{
		// Формирование данных
		$_data['status'] = true;
		
		// Если это AJAX-запрос, отключение вывода колонтитулов
		if ($fajax) $_header = false;
		
		// Подключение представления
		require(APP . '/views/register.confirm.php');
		
	}
	else
	{
		// Формирование данных
		$_data['status'] = true;
		
		// Если это AJAX-запрос, отключение вывода колонтитулов
		if ($fajax) $_header = false;
		
		// Подключение представления
		require(APP . '/views/register.success.php');
		
	}
	
}


?>
