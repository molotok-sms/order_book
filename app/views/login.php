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
		var login = $(this).find('#login');
		var pass = $(this).find('#pass');
		var err = 0;
		
		$('.error_string').hide();
		$('.error_string').text('');
		
		pass.removeClass('state-error');
		login.removeClass('state-error');
		
		if (pass.val().trim() == '') { pass.addClass('state-error'); pass.focus(); err++; }
		if (login.val().trim() == '') { login.addClass('state-error'); login.focus(); err++; }
		
		if (err) return false;
		
		
		$.ajax
		({
			data: { ajax: 1, login: login.val().trim(), pass: pass.val().trim() },
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
				
				if (data.toString().indexOf('redirect') >= 0)
				{
					location.href = $('.go_main_page').attr('href');
					return;
					
				}
				
				$('.error_string').text(data);
				$('.error_string').show();
				
			},
			timeout: 15000,
			type: 'POST'
			
		});
		
		return false;
		
	});
	
});

</script>
<form action="https://<?=(SITE_DOMAIN . WWW)?>/login/" class="login simple_page" method="post">
	<h1 class="header">Вход на сайт</h1>
	<div class="content">
		<p><input autofocus id="login" placeholder="Имя пользователя" name="login" value="<?=$_data['data']['login']?>"></p>
		<p><input id="pass" placeholder="Пароль" name="pass" type="password" value=""></p>
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
