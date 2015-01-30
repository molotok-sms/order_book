<?php

// Регистрация обработчика ошибок
set_error_handler('error_handler', E_ALL);
// Регистрация обработчика исключений
set_exception_handler('exception_handler');


// Функция обработки ошибок
function error_handler ($err_num, $err_str, $err_file, $err_line, $err_context)
{
	// Приведение к типу
	$err_num = (int) $err_num;
	$err_line = (int) $err_line;
	
	// Логирование ошибки
	return print_log('error', 'Ошибка #' . $err_num . ': "' . $err_str . '" в строке №' . $err_line . ' файла "' . $err_file .'"');
	
}


// Функция обработки исключений
function exception_handler ($exception)
{
	// Установка кода ошибка HTTP
	http_response_code(500);
	
	// Логирование ошибки
	print_log('error', 'Исключение: "' . $exception->getMessage() . '" в строке №' . $exception->getLine() . ' файла "' . $exception->getFile() .'". Стек вызовов:' . "\n" . get_error_trace($exception->getTrace()));
	
	// Вывод ошибки
	echo('<div style="background-color:pink;border-radius:6px;margin:100 auto;padding:30;max-width:500;text-align:center;"><h1>Sorry, an error occurred!</h1><h2>We are working on it.</h2></div>');
	
	// Возврат успеха обработки
	return true;
	
}


// Функция формирования текстового представления стека вызовов
function get_error_trace ($trace)
{
	// Проверка входных параметров
	if (!is_array($trace) || !count($trace)) return '';
	
	// Инициализация результата
	$result = '';
	
	// Инициализация счетчика
	$k = count($trace) - 1;
	
	// Перебор все элементов в стеке вызова
	foreach ($trace as $item)
	{
		// Если это не первый элемент, добавление разделителя
		if ($result) $result .= "\n";
		
		// Добавление номера элемента
		$result .= "\t#" . $k;
		
		// Если известно имя файла
		if (isset($item['file']))
		{
			// Добавление имени файла
			$result .= ' ' . $item['file'];
			
			// Если известен номер строки
			if (isset($item['line'])) $result .= '(' . $item['line'] . ')';
			
		}
		
		// Если известно имя функции
		if (isset($item['function']))
		{
			// Добавление имени функции
			$result .= (isset($item['file']) ? ':' : '') . ' ' . $item['function'] . '(';
			
			// Если известны аргументы функции
			if (isset($item['args']))
			{
				// Добавление
				$result .= preg_replace('#\n[ ]*#m', ' ', var_export($item['args'], true));
				
			}
			
			// Добавление закрывающего разделителя
			$result .= ')';
			
		}
		
		// Уменьшение счетчика
		$k--;
		
	}
	
	// Возврат результата
	return $result;
	
}


?>
