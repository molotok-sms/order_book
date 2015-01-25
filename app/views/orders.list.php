<?php

// Подключение верхнего колонтитула
require('header.php');



// Если произошла ошибка
if (!$_data['status'])
{
	// Вывод сообщения об ошибке
?><div class="orders simple_page error">
	<div class="content">
		<p>Ошибка получения списка заказов :-(</p>
		<p>Мы уже знаем об ошибке и работаем над её устранением.</p>
	</div>
</div>
<?php
	
}
// Иначе, если нет данных
elseif (!count($_data['data']))
{
	// Вывод сообщения
?><div class="orders simple_page empty">
	<div class="content">
		<p>Список заказов пока пуст :-(</p>
<?php
	
	// Если пользователь аутентифицирован и является Заказчиком
	if (UID && $_user && $_user['customer'])
	{
		// Выводим ссылку на размещение заказа
?>		<p>Но Вы можете <a href="<?=WWW?>/orders/add">стать первым</a>.</p>
<?php
		
	}
	
?>	</div>
</div>
<?php
	
}
else
{
	
?><div class="orders">
<?php
	
	// Перебор элементов
	foreach ($_data['data'] as $item)
	{
		// Обрамление строк в отдельные абзацы
		$item['description'] = preg_replace('/^(.*)$/m', '<p>\1</p>', $item['description']);
		
		// Форматирование даты и времени
		$item['update_datetime'] = format_date('d F Y', strtotime($item['update_datetime']));
		
		// Форматирование стоимости
		$item['price'] = number_format($item['price'], 0, ',', ' ') . ' руб.';
		
		// Вывод текущего элемента
?>	<div class="item padding30">
		<div class="header"><h2><?=$item['title']?></h2></div>
		<div class="content"><?=$item['description']?></div>
		<div class="info">
			<div class="date"><?=$item['update_datetime']?></div>
			<div class="customer"><?=$item['customer_short_name']?></div>
			<div class="price"><?=$item['price']?></div>
		</div>
	</div>
<?php
		
	}
	
?></div>
<?php
	
}


// Подключение нижнего колонтитула
require('footer.php');

?>
