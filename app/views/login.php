<?php

// Настройка заголовка
$_header_title = 'Вход на сайт';

// Подключение верхнего колонтитула
require('header.php');

?><script language="javascript">

$(document).ready(function ()
{
	$('form.login').on('submit', function (e)
	{
		var login = this.elements['login'];
		var pass = this.elements['pass'];
		var err = 0;
		
		e.preventDefault();
		
		$('.error_string').hide().text('');
		
		$('input.state-error').removeClass('state-error');
		if (!pass.value.trim()) { $(pass).addClass('state-error').focus(); err++; }
		if (!login.value.trim()) { $(login).addClass('state-error').focus(); err++; }
		if (err) return false;
		
		
		form_ajax_submit({ form: this, action: '', data: { ajax: 1 }, error: '.error_string', callback: function (data)
		{
			if (data.toString().indexOf('redirect') >= 0)
			{
				location.href = $('.go_main_page').attr('href');
				return;
				
			}
			
			$('.error_string').text(data).show();
			
		}});
		
	});
	
});

</script>
<form action="https://<?=(SITE_DOMAIN . WWW)?>/login/" class="login simple_page" method="post">
	<h1 class="header">Вход на сайт</h1>
	<div class="content">
		<p><input autofocus maxlength=32 name="login" placeholder="Имя пользователя" value="<?=$_data['data']['login']?>"></p>
		<p><input maxlength=20 name="pass" placeholder="Пароль" type="password" value=""></p>
		<div class="action">
			<input class="button" type="submit" value="Войти" />
			<a href="<?=WWW?>/register">Регистрация</a>
		</div>
		<div class="error_string state-error" <?php if (!$_data['error']) { ?>style="display: none;"<?php } ?>><?=$_data['error']?></div>
	</div>
</form>
<?php

// Подключение нижнего колонтитула
require('footer.php');

?>
