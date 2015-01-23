<?php

// Функция создания константы из идентификатора и получения информации о текущем пользователе
function users_declare_current ()
{
	global $_user;
	
	// Получаем идентификатор пользователя
	$uid = (isset($_SESSION['uid'])) ? $_SESSION['uid'] : 0;
	
	// Если константа еще не создана, создание константы
	if (!defined('UID')) define('UID', $uid);
	
	// Сохранениее информации о текущем пользователе в глобальной переменной
	$_user = users_get($uid);
	
}


// Функция получения информации о пользователе
function users_get ($uid=false)
{
	// Если запрошена информация о конкретном пользователе
	if (is_numeric($uid) && ($uid > 0))
	{
		// Формирование запроса для выборки
		$query = '
SELECT
	`login`,
	`last_name`,
	`name`,
	`second_name`,
	`customer`,
	`executor`,
	`bank`,
	`timezone`,
	FROM_UNIXTIME(`create_datetime`) AS "create_datetime",
	FROM_UNIXTIME(`update_datetime`) AS "update_datetime",
	FROM_UNIXTIME(`last_datetime`) AS "last_datetime"
	
FROM `users`
WHERE `uid`="' . $uid . '";
		';
		
		// Выполнение запроса
		$result = db_query($query);
		
		// Если выполнение запроса успешно
		if (is_array($result) && count($result))
		{
			// Возврат первой записи
			return current($result);
			
		}
		
	}
	// Иначе, если запрошен список пользователей
	elseif ($uid === false)
	{
		// Формирование запроса для проверки данных
		$query = '
SELECT
	`last_name`,
	`name`,
	`second_name`,
	`customer`,
	`executor`,
	`timezone`,
	FROM_UNIXTIME(`create_datetime`) AS "create_datetime",
	FROM_UNIXTIME(`last_datetime`) AS "last_datetime"
	
FROM `users`;
		';
		
		// Выполнение запроса
		$result = db_query($query);
		
		// Если выполнение запроса успешно
		if (is_array($result) && count($result))
		{
			// Возврат списка пользователей
			return $result;
			
		}
		
	}
	
	// По умолчанию, возврат неуспеха
	return false;
	
}


// Функция проверки запроса и аутентификации пользователя
function users_login ()
{
	// Если пришли данные аутентификации
	if (isset($_POST['login']) && isset($_POST['pass']))
	{
		// Сброс прежнего идентификатора пользователя
		$_SESSION['uid'] = 0;
		
		// Формирование запроса для проверки данных
		$query = '
SELECT *
FROM `users`
WHERE `login`="' . $_POST['login'] . '"
	AND `pass`=sha2("' . $_POST['pass'] . '", 256);
		';
		
		// Выполнение запроса
		$result = db_query($query);
		
		// Если выполнение запроса успешно
		if (is_array($result) && count($result))
		{
			// Получение первой записи
			$result = current($result);
			
			// Сохранение идентификатора пользователя
			$_SESSION['uid'] = $result['uid'];
			
			// Возврат успеха
			return true;
			
		}
		
		// По умолчанию, возврат неуспеха
		return false;
		
	}
	
}


// Функция проверки запроса и выхода из пользователя
function users_logout ($force=false)
{
	// Если запрошен или принудительный выход из пользователя
	if (isset($_REQUEST['logout']) || $force)
	{
		// Сброс идентификатор пользователя
		$_SESSION['uid'] = 0;
		
		// Перенаправление на главную страницу
		return redirect();
		
	}
	
	// По умолчанию, возврат неуспеха
	return false;
	
}


?>
