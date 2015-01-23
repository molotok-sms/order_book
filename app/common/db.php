<?php

// Функция для получения кол-ва обработанных записей
function db_affected_rows ($db_link=false)
{
	global $_db;
	
	// Если ID подключения не передан
	if (!$db_link)
	{
		// Использование глобального ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан или ошибка подключения
	if (!$db_link || $db_link->connect_error)
	{
		// Возврат ошибки
		return false;
		
	}
	
	// Возврат кол-ва обработанных записей при последнем запросе
	return $db_link->affected_rows;
	
}


// Функция для подключения к Базе Данных
function db_connect ($connection_string, $fglobal=true)
{
	global $_db;
	
	// Разбор строки подключения на массив параметров
	parse_str(str_replace(' ', '&', $connection_string), $params);
	
	// Дополнение параметрами по умолчанию
	if (!isset($params['host'])) $params['host'] = 'localhost';
	if (!isset($params['port'])) $params['port'] = '3306';
	if (!isset($params['user'])) $params['user'] = 'root';
	if (!isset($params['password'])) $params['password'] = '';
	if (!isset($params['dbname'])) $params['dbname'] = 'mysql';
	
	// Подключение к Базе Данных
	$db_link = new mysqli($params['host'], $params['user'], $params['password'], $params['dbname'], $params['port']);
	
	// Если требуется, то сохраняем идентификатор подключения глобально
	if ($fglobal) $_db = $db_link;
	
	// Возврат результата
	return $db_link;
	
}


// Функция для закрытия подключения к Базе Данных
function db_close ($db_link=false)
{
	global $_db;
	
	// Если ID подключения не передан
	if (!$db_link)
	{
		// Использование глобального ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан или ошибка подключения
	if (!$db_link || $db_link->connect_error)
	{
		// Возврат ошибки
		return false;
		
	}
	
	// Возврат результата закрытия подключения
	return $db_link->close();
	
}


// Функция получения последней ошибки обращения к БД
function db_error_string ($db_link=false)
{
	global $_db;
	
	// Если ID подключения не передан
	if (!$db_link)
	{
		// Использование глобального ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан
	if (!$db_link)
	{
		// Возврат ошибки
		return false;
		
	}
	
	// Если ошибка подключения
	if ($db_link->connect_error)
	{
		// Возврат ошибки
		return $db_link->connect_error;
		
	}
	
	// Возврат текста последней ошибки обращения к БД
	return $db_link->error;
	
}


// Функция для экранирования данных перед записью в БД
function db_escape_string ($value, $db_link=false)
{
	global $_db;
	
	// Если ID подключения не передан
	if (!$db_link)
	{
		// Использование глобального ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан или ошибка подключения
	if (!$db_link || $db_link->connect_error)
	{
		// Возврат ошибки
		return false;
		
	}
	
	// Возврат результата экранирования средствами Базы Данных
	return $db_link->escape_string($value);
	
}


// Функция для получения идентификатора последней добавленной записи
function db_last_insert_id ($db_link=false)
{
	global $_db;
	
	// Если ID подключения не передан
	if (!$db_link)
	{
		// Использование глобального ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан или ошибка подключения
	if (!$db_link || $db_link->connect_error)
	{
		// Возвращаем ноль
		return 0;
		
	}
	
	// Возврат ID вставленной записи при последнем запросе
	return $db_link->insert_id;
	
}


// Функция для выполнения SQL-запроса
function db_query ($query, $db_link=false)
{
	global $_db;
	
	// Если ID подключения не передан
	if (!$db_link)
	{
		// Использование глобального ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан или ошибка подключения
	if (!$db_link || $db_link->connect_error)
	{
		// Возврат ошибки
		return false;
		
	}
	
	
	// Выполнение запроса
	$res = $db_link->query($query);
	
	// Если в качестве результата полученое логическое ИСТИНА
	if ($res === true) return true;
	// Иначе, если получен набор записей
	elseif ($res)
	{
		// Инициализация результата
		$result = array();
		
		// Разбор результата по записям
		while (($rec = $res->fetch_assoc()) != NULL)
		{
			// Добавление записи к результату
			$result[] = $rec;
			
		}
		
		// Освобождение памяти
		$res->free();
		
		// Возврат результата
		return $result;
		
	}
	
	// По умолчанию, возврат неуспеха
	return false;
	
}


// Функция для задания клиентской кодировки для работы с Базой Данных
function db_set_encoding ($encoding, $db_link=false)
{
	global $_db;
	
	// Если ID подключения не передан
	if (!$db_link)
	{
		// Использование глобального ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан или ошибка подключения
	if (!$db_link || $db_link->connect_error)
	{
		// Возврат ошибки
		return false;
		
	}
	
	// Возврат результата установки кодировки
	return $db_link->set_charset($encoding);
	
}


?>
