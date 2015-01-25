<?php

// Подключение верхнего колонтитула
require('header.php');

?><script language="javascript">

$(document).ready(function ()
{
	$('form.register').on('submit', function (e)
	{
		var data = { ajax: 1 , register: 1 };
		var items = { user_email: 'e-Mail', user_second_name: 'отчество', user_name: 'имя', user_last_name: 'фамилию', user_pass_confirm: 'подтверждение пароля', user_pass: 'пароль', user_login: 'имя пользователя' };
		var item;
		var err = 0;
		var i;
		
		$('.error_string').hide();
		$('.error_string').text('');
		
		data['user_customer'] = ($('#user_customer').get(0).checked ? 1 : 0);
		data['user_executor'] = ($('#user_executor').get(0).checked ? 1 : 0);
		
		if (!data['user_customer'] && !data['user_executor'])
		{
			$('#user_executor').addClass('state-error').focus();
			$('.error_string').text('Выберите хотя бы что-то одно: Заказчик или Исполнитель');
			$('.error_string').show();
			err++;
			
		}
		
		if (!/.+@.+/.test($('#user_email').val().trim()))
		{
			$('#user_email').addClass('state-error').focus();
			$('.error_string').text('Некорректный E-Mail');
			$('.error_string').show();
			err++;
			
		}
		
		if ($('#user_pass').val().trim() != $('#user_pass_confirm').val().trim())
		{
			$('#user_pass_confirm').addClass('state-error').focus();
			$('.error_string').text('Введенные пароли не совпадают');
			$('.error_string').show();
			err++;
			
		}
		
		for (i in items)
		{
			item = $('#' + i).removeClass('state-error');
			data[i] = item.val().trim();
			
			if (data[i] == '')
			{
				item.addClass('state-error').focus();
				$('.error_string').text('Укажите ' + items[i]);
				$('.error_string').show();
				err++;
				
			}
			
		}
		
		if (err) return false;
		
		
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
	
});

</script>
<form action="https://<?=(SITE_DOMAIN . WWW)?>/register" class="register simple_page" method="post">
	<input name="register" type="hidden" value="1">
	<h1 class="header">Регистрация на сайте</h1>
	<div class="content">
		<table align="center">
			<tr>
				<td>Имя пользователя:</td>
				<td><input <?=(!$_data['error_field'] ? 'autofocus' : '')?><?=(($_data['error_field'] == 'user_login') ? 'autofocus class="state-error"' : '')?> id="user_login" placeholder="Имя пользователя" name="user_login" value="<?=$_data['data']['user_login']?>"></td>
			</tr>
			<tr>
				<td>Пароль:</td>
				<td><input <?=(($_data['error_field'] == 'user_pass') ? 'autofocus class="state-error"' : '')?> id="user_pass" placeholder="Пароль" name="user_pass" type="password" value=""></td>
			</tr>
			<tr>
				<td>Подтверждение пароля:</td>
				<td><input <?=(($_data['error_field'] == 'user_pass_confirm') ? 'autofocus class="state-error"' : '')?> id="user_pass_confirm" placeholder="Подтверждение пароля" name="user_pass_confirm" type="password" value=""></td>
			</tr>
			<tr>
				<td>Фамилия:</td>
				<td><input <?=(($_data['error_field'] == 'user_last_name') ? 'autofocus class="state-error"' : '')?> id="user_last_name" placeholder="Фамилия" name="user_last_name" value="<?=$_data['data']['user_last_name']?>"></td>
			</tr>
			<tr>
				<td>Имя:</td>
				<td><input <?=(($_data['error_field'] == 'user_name') ? 'autofocus class="state-error"' : '')?> id="user_name" placeholder="Имя" name="user_name" value="<?=$_data['data']['user_name']?>"></td>
			</tr>
			<tr>
				<td>Отчество:</td>
				<td><input <?=(($_data['error_field'] == 'user_second_name') ? 'autofocus class="state-error"' : '')?> id="user_second_name" placeholder="Отчество" name="user_second_name" value="<?=$_data['data']['user_second_name']?>"></td>
			</tr>
			<tr>
				<td>E-Mail:</td>
				<td><input <?=(($_data['error_field'] == 'user_email') ? 'autofocus class="state-error"' : '')?> id="user_email" placeholder="E-Mail" name="user_email" value="<?=$_data['data']['user_email']?>"></td>
			</tr>
			<tr>
				<td>Заказчик:</td>
				<td><input <?=(($_data['error_field'] == 'user_customer') ? 'autofocus class="state-error"' : 'class="checkbox"')?> <?=($_data['data']['user_customer'] ? 'checked' : '')?> id="user_customer" name="user_customer" type="checkbox"></td>
			</tr>
			<tr>
				<td>Исполнитель:</td>
				<td><input <?=(($_data['error_field'] == 'user_executor') ? 'autofocus class="state-error"' : 'class="checkbox"')?> <?=($_data['data']['user_executor'] ? 'checked' : '')?> id="user_executor" name="user_executor" type="checkbox"></td>
			</tr>
		</table>
		<div class="action">
			<input class="button" type="submit" value="Подтвердить" />
		</div>
		<div class="error_string state-error" <?php if (!$_data['error']) { ?>style="display: none;"<?php } ?>><?=$_data['error']?></div>
	</div>
</form>
<?php

// Подключение нижнего колонтитула
require('footer.php');

?>
