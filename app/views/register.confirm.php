<?php

// Настройка заголовка
$_header_title = 'Подтверждение регистрации';

// Подключение верхнего колонтитула
require('header.php');

?><script language="javascript">

$(document).ready(function ()
{
	$('form.register').on('submit', function (e)
	{
		e.preventDefault();
		
		form_ajax_submit({ form: this, data: { ajax: 1 }, error: '.error_string', content: '.main_frame', callback: function (data)
		{
			window.scrollTo(0, 0);
			
		}});
		
	});
	
	$('#go_back').get(0).onclick = '';
	$('#go_back').on('click', function (e)
	{
		e.preventDefault();
		
		var d = get_form_data(this.form);
		d['ajax'] = 1;
		d['register'] = 0;
		
		form_ajax_submit({ form: this, action: 'https://<?=(SITE_DOMAIN . WWW)?>/register', data: d, form_data: false, error: '.error_string', content: '.main_frame', callback: function (data)
		{
			window.scrollTo(0, 0);
			
		}});
		
	});
	
});

</script>
<form action="https://<?=(SITE_DOMAIN . WWW)?>/register/confirm" class="register simple_page" method="post">
	<input name="register" type="hidden" value="1">
	<input name="user_login" type="hidden" value="<?=$_data['data']['user_login']?>">
	<input name="user_last_name" type="hidden" value="<?=$_data['data']['user_last_name']?>">
	<input name="user_name" type="hidden" value="<?=$_data['data']['user_name']?>">
	<input name="user_second_name" type="hidden" value="<?=$_data['data']['user_second_name']?>">
	<input name="user_email" type="hidden" value="<?=$_data['data']['user_email']?>">
	<input name="user_customer" type="hidden" value="<?=$_data['data']['user_customer']?>">
	<input name="user_executor" type="hidden" value="<?=$_data['data']['user_executor']?>">
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
