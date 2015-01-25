<?php

// Функция реализации контроллера
function controller_auth ($action='', $redirect='')
{
	global $_auth_error; // TODO: может убрать из global
	global $_header;
	global $_header_title;
	global $_user;
	
	
	// Подключение модели "Пользователи"
	require(APP . '/models/users.php');
	
	
	// Получение входных параметров
	$fajax = isset($_POST['ajax']) && $_POST['ajax'] ? true : false;
	$flogin = isset($_POST['login']) ? true : false;
	$login = isset($_POST['login']) ? $_POST['login'] : '';
	$pass = isset($_POST['pass']) ? $_POST['pass'] : '';
	
	
	// Инициализация текста ошибки
	$_auth_error = '';
	
	// Инициализация идентификатора пользователя
	if (!isset($_SESSION['uid'])) $_SESSION['uid'] = 0;
	
	
	// Если запрошен выход из пользователя
	if ($action == 'logout')
	{
		// Сброс идентификатора пользователя
		$_SESSION['uid'] = 0;
		
		// Если задан адрес для перенаправления
		if ($redirect)
		{
			// Декодирование URL
			$redirect = urldecode($redirect);
			
			// Если в адресе использовано имя домена, игнорируем его
			if (strpos($redirect, '://') !== false) $redirect = '';
			
		}
		
		// Перенаправление на заданную страницу сайта или Главную страницу
		redirect(($redirect ? $redirect : false));
		
	}
	// Иначе, если открыта страница "вход на сайт" или переданы данные для аутентификации
	elseif (($action == 'login') || $flogin)
	{
		// Если переданы данные аутентификации
		if ($flogin)
		{
			// Если имя пользователя пустое
			if ($login == '')
			{
				// Формирование текста ошибки
				$_auth_error = 'Введите имя пользователя';
				
			}
			// Иначе, если пароль пуст
			elseif ($pass == '')
			{
				// Формирование текста ошибки
				$_auth_error = 'Введите пароль';
				
			}
			else
			{
				// Аутентификация пользователя
				$uid = users_login();
				
				// Если пользователь аутентифицирован
				if (is_numeric($uid) && ($uid > 0))
				{
					// Сохранение идентификатора пользователя в сессии
					$_SESSION['uid'] = $uid;
					
					// Обновление метки времени последнего запроса пользователя
					users_update_last_datetime();
					
					
					// Если это AJAX-запрос
					if ($fajax)
					{
						// Завершение выполнения с выводом команды перенаправления
						die('redirect');
						
					}
					else
					{
						// Если пользователь сам зашел на страницу "вход на сайт"
						if ($action == 'login')
						{
							// Перенаправление на Главную страницу сайта
							redirect();
							
						}
						
					}
					
				}
				else
				{
					// Сброс идентификатора пользователя
					$_SESSION['uid'] = 0;
					
					// Формирование текста ошибки
					$_auth_error = 'Неправильное имя пользователя или пароль';
					
				}
				
			}
			
		}
		
		
		// Если это AJAX-запрос и есть данные об ошибке
		if ($fajax && ($_auth_error != ''))
		{
			// Завершение выполнения с выводом ошибки
			die($_auth_error);
			
		}
		
		
		// Если открыта страница "вход на сайт" или есть данные об ошибке
		if (($action == 'login') || $_auth_error)
		{
			// Создание константы с идентификатором пользователя
			define('UID', $_SESSION['uid']);
			
			// Формирование данных
			$_data = array('status' => false, 'error' => $_auth_error, 'data' => array('login' => $login));
			
			// Подключение представления "вход на сайт"
			require(APP . '/views/login.php');
			
			// Завершение выполнения
			exit;
			
		}
		
	}
	
	
	// Сохранение информации о текущем пользователе в глобальной переменной (или false в случае 0)
	$_user = users_get($_SESSION['uid']);
	
	
	// Если просрочена сессия аутентифицированного пользователя
	if (SESSION_MAX_IDLE && $_user && $_user['last_datetime_unix'] && (strtotime('now') - $_user['last_datetime_unix'] >= SESSION_MAX_IDLE))
	{
		// Сброс идентификатора пользователя
		$_SESSION['uid'] = 0;
		// Создание константы с идентификатором пользователя
		define('UID', $_SESSION['uid']);
		
		// Формирование текста ошибки
		$_auth_error = 'Отсутствие активности более ' . SESSION_MAX_IDLE . ' секунд, пожалуйста, авторизуйтесь заново';
		
		// Формирование данных
		$_data = array('status' => false, 'error' => $_auth_error, 'data' => array('login' => $_user['login']));
		
		// Подключение представления "вход на сайт"
		require(APP . '/views/login.php');
		
		// Завершение выполнения
		exit;
		
	}
	
	
	// Если пользователь аутентифицирован
	if ($_SESSION['uid'])
	{
		// Обновление метки времени последнего запроса
		users_update_last_datetime();
		
		// Использование пользовательского часового пояса
		users_use_time_zone();
		
	}
	
	// Создание константы с идентификатором пользователя
	define('UID', $_SESSION['uid']);
	
}


?>
