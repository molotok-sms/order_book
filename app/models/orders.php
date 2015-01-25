<?php

// Функция получения списка заказов
function orders_add ($order_title, $order_description, $order_price)
{
	// Удаление крайних пробелов
	$order_title = trim($order_title);
	$order_description = trim($order_description);
	
	// Экранирование входных данных
	$order_title = db_escape_string($order_title);
	$order_description = db_escape_string($order_description);
	
	// Приведение к типу
	$order_price = (double) $order_price;
	
	
	// Проверка входных данных
	if ($order_title == '') return array('result' => false, 'error' => 'Не указан заголовок', 'error_arg' => 'order_title');
	if ($order_description == '') return array('result' => false, 'error' => 'Нет описания', 'error_arg' => 'order_description');
	if ($order_price < 0) return array('result' => false, 'error' => 'Цена не может быть отрицательной', 'error_arg' => 'order_price');
	
	
	// Формирование запроса на добавление
	$query = '
INSERT INTO `orders`
(`customer_uid`, `title`, `description`, `price`, `create_datetime`, `update_datetime`)
VALUES ("' . UID . '", "' . $order_title . '", "' . $order_description . '", "' . $order_price . '", UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
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


// Функция получения информации о заказе или списка всех заказов
function orders_get ($oid=false, $filter=false, $limit_offset=0, $limit_count=ORDERS_ON_PAGE)
{
	// Если запрошена информация о конкретном пользователе
	if (is_numeric($oid) && ($oid > 0))
	{
		// Формирование запроса на выборку
		$query = '
SELECT
	`oid`,
	`customer_uid`,
	`users`.`last_name` AS "customer_last_name",
	`users`.`name` AS "customer_name",
	`users`.`second_name` AS "customer_second_name",
	CONCAT(`users`.`last_name`, " ", SUBSTRING(`users`.`name`, 1, 1), ". ", SUBSTRING(`users`.`second_name`, 1, 1), ".") AS "customer_short_name",
	`executor_uid`,
	`title`,
	`description`,
	`price`,
	`orders`.`create_datetime` AS "create_datetime_unix",
	`orders`.`update_datetime` AS "update_datetime_unix",
	FROM_UNIXTIME(`orders`.`create_datetime`) AS "create_datetime",
	FROM_UNIXTIME(`orders`.`update_datetime`) AS "update_datetime"
	
FROM `orders` INNER JOIN `users` ON `orders`.`customer_uid` = `users`.`uid`
WHERE `oid` = "' . $oid . '"
	AND ((`executor_uid` = 0) OR (`executor_uid` = "' . UID . '"));
		';
		
		// Выполнение запроса
		$result = db_query($query);
		
		// Если выполнение запроса успешно
		if (is_array($result) && count($result))
		{
			// Получение первой записи
			$result = current($result);
			
			// Возврат списка
			return array('result' => $result, 'error' => '', 'error_arg' => '');
			
		}
		
		// По умолчанию, возврат ошибки
		return array('result' => false, 'error' => 'Ошибка получения информации о заказе', 'error_arg' => '');
		
	}
	// Иначе, если запрошен список заказов
	elseif ($oid === false)
	{
		// Инициализация фильтра
		if (!is_array($filter) || !count($filter)) $filter = array('executor_uid' => 0);
		
		
		// Инициализация условия
		$filter_where = '1';
		
		// Если задан фильтр заказов
		if (is_array($filter) && count($filter))
		{
			// Перебираем поля фильтра
			foreach ($filter as $key => $value)
			{
				// Экранирование входных данных
				$key = db_escape_string($key);
				$value = db_escape_string($value);
				
				// Добавление условия
				$filter_where .= ' AND `' . $key . '`="' . $value . '"';
				
			}
			
		}
		
		
		// Приведение к типу
		$limit_offset = (int) $limit_offset;
		$limit_count = (int) $limit_count;
		
		// Проверка граничных значений
		if ($limit_offset < 0) $limit_offset = 0;
		if ($limit_count < 0) $limit_count = 0;
		
		
		// Формирование запроса на выборку
		$query = '
SELECT
	`oid`,
	`customer_uid`,
	`users`.`last_name` AS "customer_last_name",
	`users`.`name` AS "customer_name",
	`users`.`second_name` AS "customer_second_name",
	CONCAT(`users`.`last_name`, " ", SUBSTRING(`users`.`name`, 1, 1), ". ", SUBSTRING(`users`.`second_name`, 1, 1), ".") AS "customer_short_name",
	`title`,
	IF(LENGTH(`description`) > 1000, CONCAT(SUBSTRING(`description`, 1, 1000), "..."), `description`) AS "description",
	`price`,
	`orders`.`create_datetime` AS "create_datetime_unix",
	`orders`.`update_datetime` AS "update_datetime_unix",
	FROM_UNIXTIME(`orders`.`create_datetime`) AS "create_datetime",
	FROM_UNIXTIME(`orders`.`update_datetime`) AS "update_datetime"
	
FROM `orders` INNER JOIN `users` ON `orders`.`customer_uid` = `users`.`uid`
WHERE
	(
		(`customer_uid` = "' . UID . '")
		OR (`executor_uid` = "' . UID . '")
		OR (`executor_uid` = 0)
	)
	AND ' . $filter_where . '
ORDER BY `update_datetime` DESC
LIMIT ' . $limit_offset . ', ' . $limit_count . ';
		';
		
		// Выполнение запроса
		$result = db_query($query);
		
		// Если выполнение запроса успешно
		if (is_array($result))
		{
			// Возврат списка
			return array('result' => $result, 'error' => '', 'error_arg' => '');
			
		}
		
		// По умолчанию, возврат ошибки
		return array('result' => false, 'error' => 'Ошибка получения списка заказов', 'error_arg' => '');
		
	}
	
}


// Функция получения кол-ва заказов
function orders_get_count ()
{
	// Формирование запроса на выборку
	$query = '
SELECT COUNT(*) AS "count"
FROM `orders`
WHERE `executor_uid` = 0;
	';
	
	// Выполнение запроса
	$result = db_query($query);
	
	// Если выполнение запроса успешно
	if (is_array($result) && count($result))
	{
		// Получение первой записи
		$result = array_shift($result);
		
		// Возврат списка
		return array('result' => $result['count'], 'error' => '', 'error_arg' => '');
		
	}
	
	// По умолчанию, возврат ошибки
	return array('result' => false, 'error' => 'Ошибка получения кол-ва заказов', 'error_arg' => '');
	
}


// Функция выполнения заказа
function orders_go ($oid)
{
	// Проверка входных параметров
	if (!is_numeric($oid) || !($oid > 0)) return array('result' => false, 'error' => 'Не указан заказ', 'error_arg' => '');
	
	
	// Формирование запроса начала транзакции
	$query = 'START TRANSACTION;';
	// Выполнение запроса
	$result_tran_start = db_query($query);
	
	
	// Формирование запроса на получение информации о заказе
	$query = '
SELECT
	`customer_uid`,
	`price`,
	`price` * "' . PERCENTAGE_OF_ORDERS . '" AS "percent"
FROM `orders`
WHERE `oid` = "' . $oid . '" AND `executor_uid` = 0;
	';
	
	// Выполнение запроса
	$result_order_info = db_query($query);
	
	
	// Если начало транзакции и получение информации успешно (заказ еще не выполнен)
	if ($result_tran_start && $result_order_info)
	{
		// Получение первой записи
		$result_order_info = current($result_order_info);
		
		
		// Формирование запроса начисления денег Исполнителю (за вычетом процентов)
		$query = '
UPDATE `users`
SET `bank` = `bank` + "' . $result_order_info['price'] . '" - "' . $result_order_info['percent'] . '"
WHERE `uid` = "' . UID . '";
		';
		
		// Выполнение запроса
		$result_bank_inc = db_query($query);
		
		
		// Формирование запроса снятия денег у Заказчика (полной суммы)
		$query = '
UPDATE `users`
SET `bank` = `bank` - "' . $result_order_info['price'] . '"
WHERE `uid` = "' . $result_order_info['customer_uid'] . '";
		';
		
		// Выполнение запроса
		$result_bank_dec = db_query($query);
		
		
		// Формирование запроса фиксации исполнения заказа
		$query = '
UPDATE `orders`
SET `executor_uid` = "' . UID . '", `update_datetime` = UNIX_TIMESTAMP()
WHERE `oid` = "' . $oid . '" AND `executor_uid` = 0;
		';
		
		// Выполнение запроса
		$result_order_update = db_query($query);
		
		
		// Формирование запроса фиксации операции получения процентов
		$query = '
INSERT INTO `transactions`
(`oid`, `percent`, `create_datetime`)
VALUES ("' . $oid . '", "' . $result_order_info['percent'] . '", UNIX_TIMESTAMP());
		';
		
		// Выполнение запроса
		$result_insert_tran = db_query($query);
		
		
		// Формирование запроса подтверждения транзакции
		$query = 'COMMIT;';
		// Выполнение запроса
		$result_tran_commit = db_query($query);
		
		
		// Если выполнение всех запросов успешно
		if ($result_bank_inc && $result_bank_dec && $result_order_update && $result_insert_tran && $result_tran_commit)
		{
			// Возврат списка
			return array('result' => true, 'error' => '', 'error_arg' => '');
			
		}
		
	}
	else
	{
		// Формирование запроса подтверждения транзакции
		$query = 'ROLLBACK;';
		// Выполнение запроса
		db_query($query);
		
	}
	
	
	// По умолчанию, возврат ошибки
	return array('result' => false, 'error' => 'Ошибка фиксации выполнения заказа', 'error_arg' => '');
	
}


?>
