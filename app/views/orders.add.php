<?php

// Настройка заголовка
$_header_title = 'Разместить заказ';

// Подключение верхнего колонтитула
require('header.php');

?><script language="javascript">

$(document).ready(function ()
{
	$('form.orders-add').on('submit', function (e)
	{
		var order_title = $(this).find('#order_title');
		var order_description = $(this).find('#order_description');
		var order_price = $(this).find('#order_price');
		var err = 0;
		
		$('.error_string').hide();
		$('.error_string').text('');
		
		order_price.removeClass('state-error');
		order_description.removeClass('state-error');
		order_title.removeClass('state-error');
		
		if (order_price.val().trim() == '') { order_price.addClass('state-error'); order_price.focus(); err++; }
		if (order_description.val().trim() == '') { order_description.addClass('state-error'); order_description.focus(); err++; }
		if (order_title.val().trim() == '') { order_title.addClass('state-error'); order_title.focus(); err++; }
		
		if (err) return false;
		
		
		$.ajax
		({
			data: { ajax: 1, order_title: order_title.val().trim(), order_description: order_description.val().trim(), order_price: order_price.val().trim() },
			error: function (jqXHR, textStatus, errorThrown)
			{
				$('.error_string').text('Ошибка обращения к серверу!');
				$('.error_string').show();
				
			},
			success: function (data, textStatus)
			{
				if (textStatus != 'success')
				{
					$('.error_string').text('Ошибка выполнения запроса!');
					$('.error_string').show();
					return;
					
				}
				
				$('.main_frame').html(data);
				window.scrollTo(0, 0);
				
				var title = document.title.replace(/^(.*) ::/, '');
				history.replaceState('', title, '<?=WWW?>/orders');
				document.title = title;
				
			},
			timeout: 15000,
			type: 'POST'
			
		});
		
		return false;
		
	});
	
});

</script>
<form action="" class="orders-add simple_page" method="post">
	<h1 class="header">Разместить заказ</h1>
	<div class="content">
		<table align="center">
			<tr>
				<td>Заголовок:</td>
				<td><input <?=(!$_data['error_field'] ? 'autofocus' : '')?><?=(($_data['error_field'] == 'order_title') ? 'autofocus class="state-error"' : '')?> id="order_title" placeholder="Заголовок" name="order_title" value="<?=$_data['data']['order_title']?>"></td>
			</tr>
			<tr>
				<td colspan=2><textarea <?=(($_data['error_field'] == 'order_description') ? 'autofocus class="state-error"' : '')?> id="order_description" placeholder="Описание заказа" name="order_description"><?=$_data['data']['order_description']?></textarea></td>
			</tr>
			<tr>
				<td>Цена:</td>
				<td><input <?=(($_data['error_field'] == 'order_price') ? 'autofocus class="state-error"' : '')?> id="order_price" placeholder="Цена" name="order_price" value="<?=$_data['data']['order_price']?>"> руб.</td>
			</tr>
		</table>
		<div class="action">
			<input class="button" type="submit" value="Разместить" />
		</div>
		<div class="error_string state-error" <?php if (!$_data['error']) { ?>style="display: none;"<?php } ?>><?=$_data['error']?></div>
	</div>
</form>
<?php

// Подключение нижнего колонтитула
require('footer.php');

?>
