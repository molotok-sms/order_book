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
	// Логирование ошибки
	return print_log('error', 'Исключение: ' . $exception->getMessage());
	
}


?>
