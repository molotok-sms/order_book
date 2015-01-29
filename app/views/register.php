<?php

// Настройка заголовка
$_header_title = 'Регистрация на сайте';

// Подключение верхнего колонтитула
require('header.php');

?><script language="javascript">

$(document).ready(function ()
{
	$('form.register').on('submit', function (e)
	{
		var d = {};
		var errs = false;
		var err;
		
		var messages = {
			user_login: 'Укажите имя пользователя',
			user_pass: 'Укажите пароль',
			user_pass_confirm: 'Укажите подтверждение пароля',
			user_last_name: 'Укажите фамилию',
			user_name: 'Укажите имя',
			user_second_name: 'Укажите отчество',
			user_email: 'Укажите E-Mail',
			pass_compare: 'Введенные пароли не совпадают',
			email_pattern: 'Некорректный E-Mail',
			customer_executor: 'Выберите хотя бы что-то одно: Заказчик или Исполнитель'
		};
		
		e.preventDefault();
		
		$('.error_string').hide().text('');
		$('input.state-error').removeClass('state-error');
		
		d = get_form_data(this);
		
		err = function (form, field, error)
		{
			$(form.elements[field]).addClass('state-error').focus();
			$('.error_string').html(error);
			
			return true;
			
		};
		
		if (!d['user_customer'] && !d['user_executor'])	errs |= err(this, 'user_executor', messages['customer_executor']);
		if (!d['user_email'].trim())					errs |= err(this, 'user_email', messages['user_email']);
		if (!/.+@.+/.test(d['user_email']))				errs |= err(this, 'user_email', messages['email_pattern']);
		if (!d['user_second_name'].trim())				errs |= err(this, 'user_second_name', messages['user_second_name']);
		if (!d['user_name'].trim())						errs |= err(this, 'user_name', messages['user_name']);
		if (!d['user_last_name'].trim())				errs |= err(this, 'user_last_name', messages['user_last_name']);
		if (d['user_pass'] != d['user_pass_confirm'])	errs |= err(this, 'user_pass_confirm', messages['pass_compare']);
		if (!d['user_pass_confirm'].trim())				errs |= err(this, 'user_pass_confirm', messages['user_pass_confirm']);
		if (!d['user_pass'].trim())						errs |= err(this, 'user_pass', messages['user_pass']);
		if (!d['user_login'].trim())					errs |= err(this, 'user_login', messages['user_login']);
		
		if (errs)
		{
			$('.error_string').show();
			return false;
		}
		
		
		form_ajax_submit({ form: this, data: { ajax: 1 }, error: '.error_string', content: '.main_frame', xhrFields: { withCredentials: true }, callback: function (data)
		{
			window.scrollTo(0, 0);
			
		}});
		
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
				<td><input <?=(!$_data['error_field'] ? 'autofocus' : '')?><?=(($_data['error_field'] == 'user_login') ? 'autofocus class="state-error"' : '')?> maxlength=32 name="user_login" placeholder="Имя пользователя" value="<?=$_data['data']['user_login']?>"></td>
			</tr>
			<tr>
				<td>Пароль:</td>
				<td><input <?=(($_data['error_field'] == 'user_pass') ? 'autofocus class="state-error"' : '')?> maxlength=20 name="user_pass" placeholder="Пароль" type="password" value=""></td>
			</tr>
			<tr>
				<td>Подтверждение пароля:</td>
				<td><input <?=(($_data['error_field'] == 'user_pass_confirm') ? 'autofocus class="state-error"' : '')?> maxlength=20 name="user_pass_confirm" placeholder="Подтверждение пароля" type="password" value=""></td>
			</tr>
			<tr>
				<td>Фамилия:</td>
				<td><input <?=(($_data['error_field'] == 'user_last_name') ? 'autofocus class="state-error"' : '')?> maxlength=64 name="user_last_name" placeholder="Фамилия" value="<?=$_data['data']['user_last_name']?>"></td>
			</tr>
			<tr>
				<td>Имя:</td>
				<td><input <?=(($_data['error_field'] == 'user_name') ? 'autofocus class="state-error"' : '')?> maxlength=64 name="user_name" placeholder="Имя" value="<?=$_data['data']['user_name']?>"></td>
			</tr>
			<tr>
				<td>Отчество:</td>
				<td><input <?=(($_data['error_field'] == 'user_second_name') ? 'autofocus class="state-error"' : '')?> maxlength=64 name="user_second_name" placeholder="Отчество" value="<?=$_data['data']['user_second_name']?>"></td>
			</tr>
			<tr>
				<td>E-Mail:</td>
				<td><input <?=(($_data['error_field'] == 'user_email') ? 'autofocus class="state-error"' : '')?> maxlength=64 name="user_email" placeholder="E-Mail" value="<?=$_data['data']['user_email']?>"></td>
			</tr>
			<tr>
				<td>Заказчик:</td>
				<td><input <?=(($_data['error_field'] == 'user_customer') ? 'autofocus class="state-error"' : '')?> <?=($_data['data']['user_customer'] ? 'checked' : '')?> name="user_customer" type="checkbox"></td>
			</tr>
			<tr>
				<td>Исполнитель:</td>
				<td><input <?=(($_data['error_field'] == 'user_executor') ? 'autofocus class="state-error"' : '')?> <?=($_data['data']['user_executor'] ? 'checked' : '')?> name="user_executor" type="checkbox"></td>
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
