<?php

// Настройка заголовка
$_header_title = 'Информация о заказе';

// Подключение верхнего колонтитула
require('header.php');


// Если произошла ошибка
if (!$_data['status'])
{
	// Вывод сообщения об ошибке
?><div class="orders-item simple_page error">
	<div class="content">
		<p>Ошибка получения информации о заказе :-(</p>
		<p>Мы уже знаем об ошибке и работаем над её устранением.</p>
	</div>
</div>
<?php
	
}
else
{
	
?><script language="javascript">

$(document).ready(function ()
{
	$('form.orders-item').on('submit', function (e)
	{
		var btn = $(this).find('input');
		
		$('.error_string').hide();
		$('.error_string').text('');
		
		btn.addClass('ajax');
		btn.prop('disabled', true);
		
		$.ajax
		({
			data: { ajax: 1 },
			error: function (jqXHR, textStatus, errorThrown)
			{
				$('.error_string').text('Ошибка обращения к серверу!');
				$('.error_string').show();
				
				btn.removeClass('ajax');
				btn.prop('disabled', false);
				
			},
			success: function (data, textStatus)
			{
				if (textStatus != 'success')
				{
					$('.error_string').text('Ошибка выполнения запроса!');
					$('.error_string').show();
					
					btn.removeClass('ajax');
					btn.prop('disabled', false);
					
					return;
					
				}
				
				if (data == 'OK')
				{
					$('.success_string').text('Заказ выполнен!');
					$('.success_string').show();
					
					btn.hide();
					btn.removeClass('ajax');
					
					return;
					
				}
				
				if (data.toString().indexOf('redirect') >= 0)
				{
					location.href = $('.go_main_page').attr('href');
					return;
					
				}
				
				$('.error_string').text(data);
				$('.error_string').show();
				
			},
			timeout: 15000,
			type: 'POST',
			url: $(this).attr('action')
			
		});
		
		return false;
		
	});
	
});

</script>
<form action="<?=WWW?>/orders/item/<?=$_data['data']['oid']?>/go" class="orders-item" method="post">
<?php
	
	// Для аналогии со списком заказов
	$item = $_data['data'];
	
	// Обрамление строк в отдельные абзацы
	$item['description'] = preg_replace('/^(.*)$/m', '<p>\1</p>', $item['description']);
	
	// Форматирование даты и времени
	$item['update_datetime'] = format_date('d F Y', strtotime($item['update_datetime']));
	
	// Форматирование стоимости
	$item['price'] = number_format($item['price'], 0, ',', ' ') . ' руб.';
	
	// Вывод текущего элемента
?>	<div class="header"><h2><?=$item['title']?></h2></div>
	<div class="content"><?=$item['description']?></div>
	<div class="action">
<?php

// Если пользователь аутентифицирован, является Исполнителем и заказ еще не выполнен
if (UID && $_user && $_user['executor'] && !$item['executor_uid'])
{
	// Вывод возможных действий
?>		<input type="submit" value="Выполнить заказ">
<?php
	
}

?>		<div class="error_string state-error" <?php if (!$_data['error']) { ?>style="display: none;"<?php } ?>><?=$_data['error']?></div>
		<div class="success_string state-success" <?php if (!$item['executor_uid']) { ?>style="display: none;"<?php } ?>>Заказ уже выполнен!</div>
	</div>
	<div class="info">
		<div class="date"><?=$item['update_datetime']?></div>
		<div class="customer"><?=$item['customer_short_name']?></div>
		<div class="price"><?=$item['price']?></div>
	</div>
</form>
<?php
	
}


// Подключение нижнего колонтитула
require('footer.php');

?>
