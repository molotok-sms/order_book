<?php

// Функция получения информации о пользователе или списка всех пользователей
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
	`create_datetime` AS "create_datetime_unix",
	`update_datetime` AS "update_datetime_unix",
	`last_datetime` AS "last_datetime_unix",
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
	`create_datetime` AS "create_datetime_unix",
	`update_datetime` AS "update_datetime_unix",
	`last_datetime` AS "last_datetime_unix",
	FROM_UNIXTIME(`create_datetime`) AS "create_datetime",
	FROM_UNIXTIME(`update_datetime`) AS "update_datetime",
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
	
	// По умолчанию, возврат ошибки
	return false;
	
}


// Функция проверки запроса и аутентификации пользователя
function users_login ()
{
	// Если пришли данные аутентификации
	if (isset($_POST['login']) && isset($_POST['pass']))
	{
		// Экранирование входных данных
		$_POST['login'] = db_escape_string($_POST['login']);
		$_POST['pass'] = db_escape_string($_POST['pass']);
		
		
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
			
			// Возврат идентификатора пользователя
			return $result['uid'];
			
		}
		
		// Возврат ошибки
		return false;
		
	}
	
	// По умолчанию, третье состояние (данные не поступали)
	return NULL;
	
}


// Функция регистрации нового пользователя
function users_register ($user_login, $user_pass, $user_pass_confirm, $user_last_name, $user_name, $user_second_name, $user_email, $user_customer, $user_executor, $fconfirm=false)
{
	// Удаление крайних пробелов
	$user_login = trim($user_login);
	$user_pass = trim($user_pass);
	$user_pass_confirm = trim($user_pass_confirm);
	$user_last_name = trim($user_last_name);
	$user_name = trim($user_name);
	$user_second_name = trim($user_second_name);
	$user_email = trim($user_email);
	
	// Экранирование входных данных
	$user_login = db_escape_string($user_login);
	$user_pass = db_escape_string($user_pass);
	$user_last_name = db_escape_string($user_last_name);
	$user_name = db_escape_string($user_name);
	$user_second_name = db_escape_string($user_second_name);
	$user_email = db_escape_string($user_email);
	
	// Приведение к типу
	$user_customer = ($user_customer) ? 1 : 0;
	$user_executor = ($user_executor) ? 1 : 0;
	
	
	// Проверка наличия необходимых входных данных
	if ($user_login == '') return array('result' => false, 'error' => 'Не указано имя для входа', 'error_arg' => 'user_login');
	if ($user_pass == '') return array('result' => false, 'error' => 'Не указан пароль', 'error_arg' => 'user_pass');
	if ($user_pass_confirm == '') return array('result' => false, 'error' => 'Не указано подтверждение пароля', 'error_arg' => 'user_pass_confirm');
	if ($user_last_name == '') return array('result' => false, 'error' => 'Не указана фамилия', 'error_arg' => 'user_last_name');
	if ($user_name == '') return array('result' => false, 'error' => 'Не указано имя', 'error_arg' => 'user_name');
	if ($user_second_name == '') return array('result' => false, 'error' => 'Не указано отчество', 'error_arg' => 'user_second_name');
	if ($user_email == '') return array('result' => false, 'error' => 'Не указан адрес электронной почты', 'error_arg' => 'user_email');
	
	// Проверка подтверждения пароля
	if ($user_pass != $user_pass_confirm) return array('result' => false, 'error' => 'Введенные пароли не совпадают', 'error_arg' => 'user_pass_confirm');
	
	// Проверка корректности адреса электронной почты
	if (!preg_match('#.+@.+#', $user_email)) return array('result' => false, 'error' => 'Некорректный адрес электронной почты', 'error_arg' => 'user_email');
	
	// Проверка выбора хотя бы чего-то одного: Заказчик или Исполнитель
	if (!$user_customer && !$user_executor) return array('result' => false, 'error' => 'Выберите хотя бы что-то одно: Заказчик или Исполнитель', 'error_arg' => 'user_executor');
	
	
	// Формирование запроса на выборку
	$query = '
SELECT COUNT(*) AS "count"
FROM `users`
WHERE `login`="' . $user_login . '";
	';
	
	// Выполнение запроса
	$result = db_query($query);
	
	// Если выполнение запроса успешно
	if (is_array($result) && count($result))
	{
		// Получение первой записи
		$result = current($result);
		
		// Если пользователь с таким именем уже существует
		if ($result['count'])
		{
			// Возврат ошибки
			return array('result' => false, 'error' => 'Пользователь с таким именем уже существует', 'error_arg' => 'user_login');
			
		}
		
	}
	
	// Если все проверки выполнены и подтверждение регистрации еще не дано, возврат успеха
	if (!$fconfirm) return array('result' => true, 'error' => '', 'error_arg' => '');
	
	
	// Формирование запроса на добавление
	$query = '
INSERT INTO `users`
(`login`, `pass`, `last_name`, `name`, `second_name`, `email`, `customer`, `executor`, `create_datetime`, `update_datetime`)
VALUES ("' . $user_login . '", sha2("' . $user_pass . '", 256), "' . $user_last_name . '", "' . $user_name . '", "' . $user_second_name . '", "' . $user_email . '", "' . $user_customer . '", "' . $user_executor . '", UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
	';
	
	// Выполнение запроса
	$result = db_query($query);
	
	// Если выполнение запроса успешно
	if ($result)
	{
		// Возврат идентификатора вставленной записи
		return array('result' => db_last_insert_id(), 'error' => '', 'error_arg' => '');
		
	}
	
	// По умолчанию, возврат ошибки
	return array('result' => false, 'error' => 'Неизвестная ошибка', 'error_arg' => '');
	
}


// Функция обновления метки времени последнего запроса пользователя
function users_update_last_datetime ()
{
	// Проверка входных данных
	if (!isset($_SESSION['uid']) || !is_numeric($_SESSION['uid']) || !($_SESSION['uid'] > 0)) return false;
	
	// Формирование запроса обновления
	$query = '
UPDATE `users`
SET `last_datetime`=UNIX_TIMESTAMP()
WHERE `uid`="' . $_SESSION['uid'] . '";
	';
	
	// Выполнение запроса
	$result = db_query($query);
	
	// Возврат результата выполнения запроса
	return ($result) ? true : false;
	
}


// Функция для использования пользовательского часового пояса
function users_use_time_zone ()
{
	// Проверка входных данных
	if (!isset($_SESSION['uid']) || !is_numeric($_SESSION['uid']) || !($_SESSION['uid'] > 0)) return false;
	
	
	// Формирование запроса на выборку
	$query = '
SELECT `time_zone`
FROM `users`
WHERE `uid`="' . $_SESSION['uid'] . '";
	';
	
	// Выполнение запроса
	$result = db_query($query);
	
	// Если выполнение запроса успешно
	if (is_array($result) && count($result))
	{
		// Получение первой записи
		$result = current($result);
		
		// Настройка часового пояса в PHP
		date_default_timezone_set($result['time_zone']);
		
		// Формирование запроса установки часового пояса
		$query = 'SET `time_zone`="' . date('P') . '";';
		
		// Выполнение запрос
		$result = db_query($query);
		
	}
	
	// Возврат результата выполнения операции
	return ($result) ? true : false;
	
}


?>
