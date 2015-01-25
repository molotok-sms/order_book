<?php

// Функция очищения входных данных
function clean_request (&$arr)
{
	// Перебор всех элементов массива
	foreach ($arr as &$val)
	{
		// Удаление граничных пробельных символов
		// Преобразование символов в HTML-сущности
		$val = htmlentities(trim($val), ENT_QUOTES);
		
	}
	
}


// Функция форматирования даты
function format_date ($format, $date_or_timestamp, $null_text=false)
{
	// Определение массива названий месяцев в именительном падеже
	$lst_search = array
	(
		'Monday', 'Tuesday', 'Wednesday', 'Thursday',
		'Friday', 'Saturday', 'Sunday',
		'Mon', 'Tue', 'Wed', 'Thu',
		'Fri', 'Sat', 'Sun',
		
		'January', 'February', 'March',
		'April', 'May', 'June',
		'July', 'August', 'September',
		'October', 'November', 'December',
		'Jan', 'Feb', 'Mar', 'Apr',
		'May', 'Jun', 'Jul', 'Aug',
		'Sep', 'Oct', 'Nov', 'Dec'
		
	);
	
	// Определение массива названий месяцев в родительном падеже
	$lst_replace = array
	(
		'Понедельник', 'Вторник', 'Среда', 'Четверг',
		'Пятница', 'Суббота', 'Воскресенье',
		'Пн', 'Вт', 'Ср', 'Чт',
		'Пт', 'Сб', 'Вс',
		
		'Января', 'Февраля', 'Марта',
		'Апреля', 'Мая', 'Июня',
		'Июля', 'Августа', 'Сентября',
		'Октября', 'Ноября', 'Декабря',
		'Янв', 'Фев', 'Мар',
		'Апр', 'Мая', 'Июн',
		'Июл', 'Авг', 'Сен',
		'Окт', 'Ноя', 'Дек'
		
	);
	
	
	// Если передано текстовое значение даты
	if (!is_numeric($date_or_timestamp))
	{
		// Преобразование в метку времени
		$date_or_timestamp = strtotime($date_or_timestamp);
		
	}
	
	
	// Если дата соответствует 01.01.1970 и задан замещающий текст
	if (!$date_or_timestamp && ($null_text !== false)) return $null_text;
	
	
	// Преобразование даты
	$date = date($format, $date_or_timestamp);
	// Локализация
	$date = str_replace($lst_search, $lst_replace, $date);
	
	
	// Возврат результата
	return $date;
	
}


// Функция регистрации отладочных сообщений
function print_log ($log, $text)
{
	// Задаем список лог-файлов
	$lst_log_files = array
	(
		'alert',
		'debug',
		'error',
		
	);
	
	// Если указан допустимый лог-файл
	if (!in_array($log, $lst_log_files)) return false;
	
	// Если директории для логов не существует
	if (!file_exists(LOG))
	{
		// Пробуем создать
		mkdir(LOG);
		
	}
	
	// Открываем лог для дозаписи
	if (($ferr = fopen(LOG . '/' . $log . '.log', 'ab')) != NULL)
	{
		// Дописываем данные
		fwrite($ferr, date('[Y-m-d H:i:s \U\T\C P] ') . $text . "\r\n");
		// Закрываем лог
		fclose($ferr);
		
		// Возвращаем успех
		return true;
		
	}
	// Иначе, возвращаем неуспех
	else return false;
	
}


// Функция перенаправления на URL
function redirect ($url=false, $force_js=false, $https=false)
{
	// Если URL-адрес не задан
	if ($url === false)
	{
		// Формирование адреса Главной страницы сайта
		$url = ($https ? 'https' : 'http') . '://' . SITE_DOMAIN . WWW;
		
	}
	
	
	// Если включена трансформация URL
	if (ini_get('session.use_trans_sid'))
	{
		// Если адрес в нашем домене
		if ((strpos($url, '://') === false) || (strpos($url, SITE_DOMAIN) !== false))
		{
			// Добавление разделителя GET-параметров
			if (strpos($url, '?') === false) $url .= '?';
			else $url .= '&';
			
			// Добавление идентификатора сессии
			$url .= session_name() . '=' . session_id();
			
		}
		
	}
	
	
	// Сохранение данных сессии и закрытие
	session_write_close();
	
	// Если заголовки уже отправлены
	if (headers_sent() || $force_js)
	{
		// Перенаправляем с помощью JavaScript
?><script language="javascript">location.href='<?=$url?>';</script>
<?php
	}
	// Иначе
	else
	{
		// Установка заголовка перенаправления
		header('Location: ' . $url);
	}
	
	// Завершение выполнения
	exit();
	
}


// Функция для дампа содержимого переменной в лог
function var_dump_log ($var)
{
	// Включаем буферизацию вывода
	ob_start();
	// Выводим содержимое переменной
	var_dump($var);
	// Получаем содержимое буфера вывода
	$contents = ob_get_contents();
	// Отключаем буферизацию с очищением буфера
	ob_end_clean();
	
	// Возвращаем результат выполнения функции вывода данных в лог-файл
	return print_log('debug', $contents);
	
}


?>
