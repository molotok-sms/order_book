<?php

// Настройка заголовка
$_header_title = 'Регистрация завершена';

// Подключение верхнего колонтитула
require('header.php');

?><div class="register simple_page">
	<h1 class="header">Регистрация завершена</h1>
	<div class="content">
		<p>Спасибо за регистрацию на нашем сайте!</p>
		<p>Теперь вы можете войти на сайт используя свое имя пользователя и пароль.</p>
		<div class="action">
			<a href="http://<?=(SITE_DOMAIN . WWW)?>/">Перейти на главную страницу</a>
		</div>
	</div>
</div>
<?php

// Подключение нижнего колонтитула
require('footer.php');

?>
