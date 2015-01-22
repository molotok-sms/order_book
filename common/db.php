<?php

// Функция для получения кол-ва обработанных записей
function db_affected_rows ($db_link=false)
{
	global $_db;
	
	// Если ID подключения не передан
	if (!$db_link)
	{
		// Используем глобальный ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан или ошибка подключения
	if (!$db_link || $db_link->connect_error)
	{
		// Возвращаем ошибку
		return false;
		
	}
	
	// Возвращаем результат функции из средств самой СУБД
	return $db_link->affected_rows;
	
}


// Функция для подключения к Базе Данных
function db_connect ($connection_string, $fglobal=true)
{
	global $_db;
	
	// Разбираем строку подключения на массив параметров
	parse_str(str_replace(' ', '&', $connection_string), $params);
	
	// Дополняем параметрами по умолчанию
	if (!isset($params['host'])) $params['host'] = 'localhost';
	if (!isset($params['port'])) $params['port'] = '3306';
	if (!isset($params['user'])) $params['user'] = 'root';
	if (!isset($params['password'])) $params['password'] = '';
	if (!isset($params['dbname'])) $params['dbname'] = 'mysql';
	
	// Подключаемся к Базе Данных
	$db_link = new mysqli($params['host'], $params['user'], $params['password'], $params['dbname'], $params['port']);
	
	// Если нужно, то сохраняем идентификатор подключения глобально
	if ($fglobal) $_db = $db_link;
	
	// Возвращаем результат
	return $db_link;
	
}


// Функция для закрытия подключения к Базе Данных
function db_close ($db_link=false)
{
	global $_db;
	
	// Если ID-подключения не передан
	if (!$db_link)
	{
		// Используем глобальный ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан или ошибка подключения
	if (!$db_link || $db_link->connect_error)
	{
		// Возвращаем ошибку
		return false;
		
	}
	
	// Возвращаем результат закрытия подключения
	return $db_link->close();
	
}


// Функция для экранирования данных перед записью в БД
function db_escape_string ($value, $db_link=false)
{
	global $_db;
	
	// Если ID-подключения не передан
	if (!$db_link)
	{
		// Используем глобальный ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан или ошибка подключения
	if (!$db_link || $db_link->connect_error)
	{
		// Возвращаем ошибку
		return false;
		
	}
	
	// Возвращаем результат экранирования средствами Базы Данных
	return $db_link->escape_string($value);
	
}


// Функция для получения идентификатора последней добавленной записи
function db_last_insert_id ($db_link=false)
{
	global $_db;
	
	// Если ID-подключения не передан
	if (!$db_link)
	{
		// Используем глобальный ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан или ошибка подключения
	if (!$db_link || $db_link->connect_error)
	{
		// Возвращаем ноль
		return 0;
		
	}
	
	// Возвращаем результат функции из средств самой СУБД
	return $db_link->insert_id;
	
}


// Функция для выполнения SQL-запроса
function db_query ($query, $db_link=false)
{
	global $_db;
	
	// Если ID-подключения не передан
	if (!$db_link)
	{
		// Используем глобальный ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан или ошибка подключения
	if (!$db_link || $db_link->connect_error)
	{
		// Возвращаем ошибку
		return false;
		
	}
	
	
	// Выполняем SQL-запрос
	$res = $db_link->query($query);
	
	// Если в качестве результата полученое логическое ИСТИНА
	if ($res === true) return true;
	// Иначе, если получен набор записей
	elseif ($res)
	{
		// Инициализируем результат
		$result = array();
		
		// Разбираем результат по записям
		while (($rec = $res->fetch_assoc()) != NULL)
		{
			// Добавляем запись к результату
			$result[] = $rec;
			
		}
		
		// Освобождаем память
		$res->free();
		
		// Возвращаем результат
		return $result;
		
	}
	// Иначе, возвращаем неуспех
	else return false;
	
}


// Функция для задания клиентской кодировки для работы с Базой Данных
function db_set_encoding ($encoding, $db_link=false)
{
	global $_db;
	
	// Если ID-подключения не передан
	if (!$db_link)
	{
		// Используем глобальный ID подключения
		$db_link = $_db;
		
	}
	
	// Если ID подключения не задан или ошибка подключения
	if (!$db_link || $db_link->connect_error)
	{
		// Возвращаем ошибку
		return false;
		
	}
	
	// Возвращем результат выполнения нескольких последовательных запросов к СУБД
	return $db_link->set_charset($encoding);
	
}


?>
