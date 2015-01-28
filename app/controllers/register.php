<?php

// Функция реализации контроллера
function controller_register ($params='')
{
	global $_header;
	global $_header_title;
	global $_user;
	
	
	// Получение параметров из URL
	$fconfirm = (is_array($params) && count($params)) ? true : false;
	
	// Получение POST-параметров
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
	
	
	// Инициализация данных
	$_data = array('status' => false, 'error' => '', 'error_field' => '', 'data' => array
	(
		'user_login' => $user_login,
		'user_pass' => '',
		'user_pass_confirm' => '',
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
		// Если дано подтверждение регистрации и в сессии сохранен хеш пароля
		if ($fconfirm && isset($_SESSION['user_pass']))
		{
			// Получение из сессии пароля сохраненного перед подтверждением
			$user_pass = $user_pass_confirm = $_SESSION['user_pass'];
			
		}
		
		// Регистрация пользователя
		// ($user_pass вернется захешированным)
		$result = users_register($user_login, $user_pass, $user_pass_confirm, $user_last_name, $user_name, $user_second_name, $user_email, $user_customer, $user_executor, $fconfirm);
		
		// Сохранение текста ошибки
		$_data['error'] = $result['error'];
		// Сохранение имени поля содержащего ошибку
		$_data['error_field'] = $result['error_arg'];
		
	}
	
	
	// Удаление хешированных паролей из сессии
	$_SESSION['user_pass'] = '';
	
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
		
		// Сохранение хешированного пароля в сессии
		$_SESSION['user_pass'] = $user_pass;
		
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
