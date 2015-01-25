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


// Функция получения списка заказов
function orders_get ()
{
	// Формирование запроса на выборку
	$query = '
SELECT
	`oid`,
	`customer_uid`,
	`title`,
	`description`,
	`price`,
	`create_datetime` AS "create_datetime_unix",
	`update_datetime` AS "update_datetime_unix",
	FROM_UNIXTIME(`create_datetime`) AS "create_datetime",
	FROM_UNIXTIME(`update_datetime`) AS "update_datetime"
	
FROM `orders`
WHERE `executor_uid` = 0
ORDER BY `update_datetime` DESC;
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


?>
