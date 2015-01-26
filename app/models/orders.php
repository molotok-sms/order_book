<?php

// Функция получения списка заказов
function orders_add ($order_title, $order_description, $order_price)
{
	// Удаление крайних пробелов
	$order_title = trim($order_title);
	$order_description = trim($order_description);
	
	// Приведение к типу
	$order_price = (double) $order_price;
	
	
	// Проверка входных данных
	if ($order_title == '') return array('result' => false, 'error' => 'Не указан заголовок', 'error_arg' => 'order_title');
	if ($order_description == '') return array('result' => false, 'error' => 'Нет описания', 'error_arg' => 'order_description');
	if ($order_price < 0) return array('result' => false, 'error' => 'Цена не может быть отрицательной', 'error_arg' => 'order_price');
	
	
	// Подключение к базе данных
	$db = db_connect(ORDERS_DB, false, true);
	
	// Экранирование входных данных
	$order_title = db_escape_string($order_title, $db);
	$order_description = db_escape_string($order_description, $db);
	
	// Формирование запроса на добавление
	$query = '
INSERT INTO `orders`
(`customer_uid`, `title`, `description`, `price`, `create_datetime`, `update_datetime`)
VALUES ("' . UID . '", "' . $order_title . '", "' . $order_description . '", "' . $order_price . '", UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
	';
	
	// Выполнение запроса
	$result = db_query($query, $db);
	
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
	// Подключение к базе данных
	$db_orders = db_connect(ORDERS_DB, false, true);
	$db_users = db_connect(USERS_DB, false, true);
	
	// Если запрошена информация о конкретном пользователе
	if (is_numeric($oid) && ($oid > 0))
	{
		// Если подключения к базе данных совпадает
		if ($db_orders === $db_users)
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
			$result = db_query($query, $db_orders);
			
			// Если выполнение запроса успешно
			if (is_array($result) && count($result))
			{
				// Получение первой записи
				$result = current($result);
				
				// Возврат списка
				return array('result' => $result, 'error' => '', 'error_arg' => '');
				
			}
			
		}
		else
		{
			// Формирование запроса на выборку информации о заказе
			$query = '
SELECT
	`oid`,
	`customer_uid`,
	"" AS "customer_last_name",
	"" AS "customer_name",
	"" AS "customer_second_name",
	"" AS "customer_short_name",
	`executor_uid`,
	`title`,
	`description`,
	`price`,
	`orders`.`create_datetime` AS "create_datetime_unix",
	`orders`.`update_datetime` AS "update_datetime_unix",
	FROM_UNIXTIME(`orders`.`create_datetime`) AS "create_datetime",
	FROM_UNIXTIME(`orders`.`update_datetime`) AS "update_datetime"
	
FROM `orders`
WHERE `oid` = "' . $oid . '"
	AND ((`executor_uid` = 0) OR (`executor_uid` = "' . UID . '"));
			';
			
			// Выполнение запроса
			$result = db_query($query, $db_orders);
			
			// Если выполнение запроса успешно
			if (is_array($result) && count($result))
			{
				// Получение первой записи
				$result = current($result);
				
				
				// Формирование запроса на выборку информации о пользователе
				$query = '
SELECT
	`users`.`last_name` AS "customer_last_name",
	`users`.`name` AS "customer_name",
	`users`.`second_name` AS "customer_second_name",
	CONCAT(`users`.`last_name`, " ", SUBSTRING(`users`.`name`, 1, 1), ". ", SUBSTRING(`users`.`second_name`, 1, 1), ".") AS "customer_short_name"
	
FROM `users`
WHERE `uid` = "' . $result['customer_uid'] . '";
				';
				
				// Выполнение запроса
				$result_users = db_query($query, $db_users);
				
				// Если выполнение запроса успешно
				if (is_array($result_users) && count($result_users))
				{
					// Получение первой записи
					$result_users = current($result_users);
					
					// Добавление информации о пользователе
					$result = array_merge($result, $result_users);
					
				}
				
				
				// Возврат списка
				return array('result' => $result, 'error' => '', 'error_arg' => '');
				
			}
			
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
				$key = db_escape_string($key, $db_orders);
				$value = db_escape_string($value, $db_orders);
				
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
		
		
		// Если подключения к базе данных совпадает
		if ($db_orders === $db_users)
		{
			// Формирование запроса на выборку списка заказов
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
			$result = db_query($query, $db_orders);
			
			// Если выполнение запроса успешно
			if (is_array($result))
			{
				// Возврат списка
				return array('result' => $result, 'error' => '', 'error_arg' => '');
				
			}
			
		}
		else
		{
			// Формирование запроса на выборку заказов
			$query = '
SELECT
	`oid`,
	`customer_uid`,
	"" AS "customer_last_name",
	"" AS "customer_name",
	"" AS "customer_second_name",
	"" AS "customer_short_name",
	`title`,
	IF(LENGTH(`description`) > 1000, CONCAT(SUBSTRING(`description`, 1, 1000), "..."), `description`) AS "description",
	`price`,
	`orders`.`create_datetime` AS "create_datetime_unix",
	`orders`.`update_datetime` AS "update_datetime_unix",
	FROM_UNIXTIME(`orders`.`create_datetime`) AS "create_datetime",
	FROM_UNIXTIME(`orders`.`update_datetime`) AS "update_datetime"
	
FROM `orders`
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
			$result = db_query($query, $db_orders);
			
			// Если выполнение запроса успешно
			if (is_array($result))
			{
				// Если есть записи
				if (count($result))
				{
					// Инициализация списка ID пользователей
					$uids = '';
					
					// Перебор всех заказов
					foreach ($result as $item)
					{
						// Добавление разделителя
						if ($uids) $uids .= ',';
						
						// Добавление ID пользователя
						$uids .= $item['customer_uid'];
						
					}
					
					
					// Формирование запроса на выборку информации о пользователях
					$query = '
SELECT
	`uid`,
	`users`.`last_name` AS "customer_last_name",
	`users`.`name` AS "customer_name",
	`users`.`second_name` AS "customer_second_name",
	CONCAT(`users`.`last_name`, " ", SUBSTRING(`users`.`name`, 1, 1), ". ", SUBSTRING(`users`.`second_name`, 1, 1), ".") AS "customer_short_name"
	
FROM `users`
WHERE `uid` IN(' . $uids . ');
					';
					
					// Выполнение запроса
					$result_users = db_query($query, $db_users);
					
					// Если выполнение запроса успешно
					if (is_array($result_users) && count($result_users))
					{
						// Перебор всех заказов
						foreach ($result as &$item)
						{
							// Перебор всех пользователей
							foreach ($result_users as &$user)
							{
								// Если текущий пользователь является Заказчиком
								if ($item['customer_uid'] == $user['uid'])
								{
									// Добавление информации о заказчике
									$item = array_merge($item, $user);
									
									// Выход из цикла перебора пользователей
									break;
									
								}
								
							}
							
						}
						
					}
					
				}
				
				
				// Возврат списка
				return array('result' => $result, 'error' => '', 'error_arg' => '');
				
			}
			
		}
		
		
		// По умолчанию, возврат ошибки
		return array('result' => false, 'error' => 'Ошибка получения списка заказов', 'error_arg' => '');
		
	}
	
}


// Функция получения кол-ва заказов
function orders_get_count ()
{
	// Подключение к базе данных
	$db = db_connect(ORDERS_DB, false, true);
	
	// Формирование запроса на выборку
	$query = '
SELECT COUNT(*) AS "count"
FROM `orders`
WHERE `executor_uid` = 0;
	';
	
	// Выполнение запроса
	$result = db_query($query, $db);
	
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
	
	
	// Отладка функции
	$debug = true;
	
	
	/*
		Последовательность операций:
		
		+----------------------+----------------------+----------------------+
		| orders               | users                | transactions         |
		+----------------------+----------------------+----------------------+
		| start_transaction    |                      |                      |
		| get_info             |                      |                      |
		| update               |                      |                      |
		|                      | start_transaction    |                      |
		|                      | +bank                |                      |
		|                      | -bank                |                      |
		|                      |                      | start_transaction    |
		|                      |                      | insert               |
		|                      |                      |                      |
		| commit               | commit               | commit               |
		|                      |                      |                      |
		|                      |                      | start_transaction    |
		|                      |                      | update               |
		|                      |                      | commit               |
		+----------------------+----------------------+----------------------+
		
	*/
	
	
	// Подключение к базам данных
	$db_orders = db_connect(ORDERS_DB, false, true);
	$db_tran = db_connect(TRAN_DB, false, true);
	$db_users = db_connect(USERS_DB, false, true);
	
	
	// Инициализация состояний начала транзакции
	$result_orders_tran_start = false;
	$result_transactions_tran_start = false;
	$result_users_tran_start = false;
	
	// Инициализация состояний подтверждения транзакции
	$result_orders_tran_commit = false;
	$result_transactions_tran_commit = false;
	$result_users_tran_commit = false;
	
	// Инициализация состояний отката транзакции
	$result_orders_tran_rollback = false;
	$result_transactions_tran_rollback = false;
	$result_users_tran_rollback = false;
	
	
	// Формирование запроса начала транзакции для ORDERS
	$query = 'START TRANSACTION;';
	// Выполнение запроса
	$result_orders_tran_start = db_query($query, $db_orders);
	if ($debug) print_log('debug', 'start_transaction from orders'); // TODO
	
	
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
	$result_orders_info = db_query($query, $db_orders);
	if ($debug) print_log('debug', 'orders->get_info'); // TODO
	
	
	// Формирование запроса фиксации исполнения заказа
	$query = '
UPDATE `orders`
SET `executor_uid` = "' . UID . '", `update_datetime` = UNIX_TIMESTAMP()
WHERE `oid` = "' . $oid . '" AND `executor_uid` = 0;
	';
	
	// Выполнение запроса
	$result_orders_update = db_query($query, $db_orders);
	if ($debug) print_log('debug', 'orders->update'); // TODO
	
	
	// Если выполнение всех запросов над ORDERS успешно
	if ($result_orders_tran_start && is_array($result_orders_info) && count($result_orders_info) && $result_orders_update)
	{
		// Получение первой записи
		$result_orders_info = current($result_orders_info);
		
		
		// Если используется одно подключение к базе данных с ORDERS
		if ($db_users === $db_orders)
		{
			// Заимствование состояния начала транзакции
			$result_users_tran_start = $result_orders_tran_start;
			
		}
		else
		{
			// Формирование запроса начала транзакции для USERS
			$query = 'START TRANSACTION;';
			// Выполнение запроса
			$result_users_tran_start = db_query($query, $db_users);
			if ($debug) print_log('debug', 'start_transaction from users'); // TODO
			
		}
		
		
		// Формирование запроса начисления денег Исполнителю (за вычетом процентов)
		$query = '
UPDATE `users`
SET `bank` = `bank` + "' . $result_orders_info['price'] . '" - "' . $result_orders_info['percent'] . '"
WHERE `uid` = "' . UID . '";
		';
		
		// Выполнение запроса
		$result_users_bank_inc = db_query($query, $db_users);
		if ($debug) print_log('debug', 'users->bank_inc'); // TODO
		
		
		// Формирование запроса снятия денег у Заказчика (полной суммы)
		$query = '
UPDATE `users`
SET `bank` = `bank` - "' . $result_orders_info['price'] . '"
WHERE `uid` = "' . $result_orders_info['customer_uid'] . '";
		';
		
		// Выполнение запроса
		$result_users_bank_dec = db_query($query, $db_users);
		if ($debug) print_log('debug', 'users->bank_dec'); // TODO
		
		
		// Если выполнение всех запросов над USERS успешно
		if ($result_users_tran_start && $result_users_bank_inc && $result_users_bank_dec)
		{
			// Если используется одно подключение к базе данных с ORDERS
			if ($db_tran === $db_orders)
			{
				// Заимствование состояния начала транзакции
				$result_transactions_tran_start = $result_orders_tran_start;
				
			}
			// Иначе, если используется одно подключение к базе данных с USEERS
			elseif ($db_tran === $db_users)
			{
				// Заимствование состояния начала транзакции
				$result_transactions_tran_start = $result_users_tran_start;
				
			}
			else
			{
				// Формирование запроса начала транзакции для TRANSACTIONS
				$query = 'START TRANSACTION;';
				// Выполнение запроса
				$result_transactions_tran_start = db_query($query, $db_tran);
				if ($debug) print_log('debug', 'start_transaction from transactions'); // TODO
				
			}
			
			
			// Формирование запроса фиксации операции получения процентов
			$query = '
INSERT INTO `transactions`
(`oid`, `percent`, `executed`, `create_datetime`, `update_datetime`)
VALUES ("' . $oid . '", "' . $result_orders_info['percent'] . '", 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
			';
			
			// Выполнение запроса
			$result_transactions_insert = db_query($query, $db_tran);
			if ($debug) print_log('debug', 'transactions->insert'); // TODO
			
			// Если выполнение фиксации успешно, сохранение ID вставленной записи
			if ($result_transactions_insert) $tid = db_last_insert_id($db_tran);
			
			
			// Формирование запроса подтверждения транзакции для TRANSACTIONS
			$query = 'COMMIT;';
			// Выполнение запроса
			$result_transactions_tran_commit = db_query($query, $db_tran);
			if ($debug) print_log('debug', 'commit from transactions'); // TODO
			
			
			// Если выполнение всех запросов над TRANSACTIONS успешно
			if ($result_transactions_tran_start && $result_transactions_insert && $result_transactions_tran_commit)
			{
				// Если используется одно подключение к базе данных с TRANSACTIONS
				if ($db_users === $db_tran)
				{
					// Заимствование состояния начала транзакции
					$result_users_tran_commit = $result_transactions_tran_commit;
					
				}
				else
				{
					// Формирование запроса подтверждения транзакции для USEERS
					$query = 'COMMIT;';
					// Выполнение запроса
					$result_users_tran_commit = db_query($query, $db_users);
					if ($debug) print_log('debug', 'commit from users'); // TODO
					
				}
				
				
				// Если подтверждение транзакции для USERS успешно
				if ($result_users_tran_commit)
				{
					// Если используется одно подключение к базе данных с TRANSACTIONS
					if ($db_orders === $db_tran)
					{
						// Заимствование состояния начала транзакции
						$result_orders_tran_commit = $result_transactions_tran_commit;
						
					}
					// Если используется одно подключение к базе данных с USERS
					elseif ($db_orders === $db_users)
					{
						// Заимствование состояния начала транзакции
						$result_orders_tran_commit = $result_users_tran_commit;
						
					}
					else
					{
						// Формирование запроса подтверждения транзакции для ORDERS
						$query = 'COMMIT;';
						// Выполнение запроса
						$result_orders_tran_commit = db_query($query, $db_orders);
						if ($debug) print_log('debug', 'commit from orders'); // TODO
						
					}
					
					
					// Если подтверждение транзакции для ORDERS успешно
					if ($result_orders_tran_commit)
					{
						// Формирование запроса начала транзакции для TRANSACTIONS
						$query = 'START TRANSACTION;';
						// Выполнение запроса
						$result_transactions_tran_start2 = db_query($query, $db_tran);
						if ($debug) print_log('debug', 'start_transaction(2) from transactions'); // TODO
						
						
						// Формирование запроса подтверждения фиксации операции получения процентов
						$query = '
UPDATE `transactions`
SET `executed` = 1, `update_datetime` = UNIX_TIMESTAMP()
WHERE `tid` = "' . $tid . '";
						';
						
						// Выполнение запроса
						$result_transactions_update = db_query($query, $db_tran);
						if ($debug) print_log('debug', 'transactions->update'); // TODO
						
						
						// Формирование запроса подтверждения транзакции для TRANSACTIONS
						$query = 'COMMIT;';
						// Выполнение запроса
						$result_transactions_tran_commit2 = db_query($query, $db_tran);
						if ($debug) print_log('debug', 'commit(2) from transactions'); // TODO
						
						
						// Если выполнение всех операций над TRANSACTIONS успешно
						if ($result_transactions_tran_start2 && $result_transactions_update && $result_transactions_tran_commit2)
						{
							// Возврат успеха операции
							return array('result' => true, 'error' => '', 'error_arg' => '');
							
						}
						else
						{
							//
							// Глобальные откаты транзакций делать уже поздно
							// и у нас все равно останется непроведенная операция.
							//
							// Откатим подтверждение фиксации операции
							//
							
							// Формирование запроса отката транзакции для TRANSACTIONS
							$query = 'ROLLBACK;';
							// Выполнение запроса
							$result_transactions_tran_rollback2 = db_query($query, $db_tran);
							if ($debug) print_log('debug', 'rollback(2) from transactions'); // TODO
							
							// Вывод ошибки в лог-файл
							print_log('error', 'Not fixed transactions #' . $tid);
							
							// Возврат ошибки
							return array('result' => false, 'error' => 'Ошибка фиксации выполнения заказа', 'error_arg' => '');
							
						}
						
					}
					
				}
				
			}
			
			// Формирование запроса отката транзакции для TRANSACTIONS
			$query = 'ROLLBACK;';
			// Выполнение запроса
			$result_transactions_tran_rollback = db_query($query, $db_tran);
			if ($debug) print_log('debug', 'rollback from transactions'); // TODO
			
		}
		
		
		// Если используется подключение отличное от TRANSACTIONS или в TRANSACTIONS не сделан откат транзакции
		if (($db_users != $db_tran) || !$result_transactions_tran_rollback)
		{
			// Формирование запроса отката транзакции для USERS
			$query = 'ROLLBACK;';
			// Выполнение запроса
			$result_users_tran_rollback = db_query($query, $db_users);
			if ($debug) print_log('debug', 'rollback from users'); // TODO
			
		}
		
	}
	
	// Если используется подключение отличное от TRANSACTIONS или в TRANSACTIONS не сделан откат транзакции
	// И
	// Если используется подключение отличное от USERS или в USEERS не сделан откат транзакции
	if ((($db_orders != $db_tran) || !$result_transactions_tran_rollback) &&
		(($db_orders != $db_users) || !$result_users_tran_rollback))
	{
		// Формирование запроса отката транзакции для ORDERS
		$query = 'ROLLBACK;';
		// Выполнение запроса
		$result_orders_tran_rollback = db_query($query, $db_orders);
		if ($debug) print_log('debug', 'rollback from orders'); // TODO
		
	}
	
	
	// По умолчанию, возврат ошибки
	return array('result' => false, 'error' => 'Ошибка фиксации выполнения заказа', 'error_arg' => '');
	
}


?>
