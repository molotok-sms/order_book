<?php

// Подключение верхнего колонтитула
require('header.php');

?><script language="javascript">

$(document).ready(function ()
{
	var data = { ajax: 1, register: 1, user_executor: '<?=$_data['data']['user_executor']?>', user_customer: '<?=$_data['data']['user_customer']?>', user_email: '<?=$_data['data']['user_email']?>', user_second_name: '<?=$_data['data']['user_second_name']?>', user_name: '<?=$_data['data']['user_name']?>', user_last_name: '<?=$_data['data']['user_last_name']?>', user_pass_confirm: '<?=$_data['data']['user_pass_confirm']?>', user_pass: '<?=$_data['data']['user_pass']?>', user_login: '<?=$_data['data']['user_login']?>' };
	
	$('form.register').on('submit', function (e)
	{
		$.ajax
		({
			data: data,
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
				
			},
			timeout: 15000,
			type: 'POST',
			url: $(this).attr('action')
			
		});
		
		return false;
		
	});
	
	$('#go_back').get(0).onclick = '';
	$('#go_back').on('click', function (e)
	{
		data['register'] = 0;
		
		$.ajax
		({
			data: data,
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
				
			},
			timeout: 15000,
			type: 'POST',
			url: 'https://<?=(SITE_DOMAIN . WWW)?>/register'
			
		});
		
		return false;
		
	});
	
});

</script>
<form action="https://<?=(SITE_DOMAIN . WWW)?>/register/confirm" class="register simple_page" method="post">
	<h1 class="header">Подтверждение регистрации</h1>
	<div class="content">
		<table align="center">
			<tr>
				<td>Имя пользователя:</td>
				<td><?=$_data['data']['user_login']?></td>
			</tr>
			<tr>
				<td>Фамилия:</td>
				<td><?=$_data['data']['user_last_name']?></td>
			</tr>
			<tr>
				<td>Имя:</td>
				<td><?=$_data['data']['user_name']?></td>
			</tr>
			<tr>
				<td>Отчество:</td>
				<td><?=$_data['data']['user_second_name']?></td>
			</tr>
			<tr>
				<td>E-Mail:</td>
				<td><?=$_data['data']['user_email']?></td>
			</tr>
			<tr>
				<td>Заказчик:</td>
				<td><?=($_data['data']['user_customer'] ? 'Да' : 'Нет')?></td>
			</tr>
			<tr>
				<td>Исполнитель:</td>
				<td><?=($_data['data']['user_executor'] ? 'Да' : 'Нет')?></td>
			</tr>
		</table>
		<div class="action">
			<input class="button" id="go_back" onclick="history.back();" type="submit" value="&lt; Назад" />
			<input class="button" type="submit" value="Подтвердить" />
		</div>
		<div class="error_string state-error" <?php if (!$_data['error']) { ?>style="display: none;"<?php } ?>><?=$_data['error']?></div>
	</div>
</form>
<?php

// Подключение нижнего колонтитула
require('footer.php');

?>
