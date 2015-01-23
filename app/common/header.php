<?php

// Подключение движка сайта
require_once('common.php');

?>
<!DOCTYPE html>
<html>
<head>
<link href="<?=WWW?>/public/images/favicon.ico" rel="icon" type="image/x-icon" />
<link href="<?=WWW?>/public/css/style.css" rel="stylesheet" type="text/css" />
<meta http-equiv="content-type" content="text/html;charset=<?=CODEPAGE?>">
<script language="javascript" src="<?=WWW?>/public/js/jquery.min.js"></script>
<script language="javascript" src="<?=WWW?>/public/js/jquery-ui.min.js"></script>
<script language="javascript" src="<?=WWW?>/public/js/jquery.blockUI.js"></script>
<title>Система размещения заказов</title>
</head>
<body>
	<div class="header_frame">
		<div class="header">
			<div class="title">
				<a class="go_main_page" href="<?=WWW?>/" title="Перейти на главную страницу">Система размещения заказов</a>
			</div>
			<div class="menu">
				<ul>
					<li class="highlight">Разместить заказ</li>
					<li>Меню 2</li>
					<li>Меню 3</li>
				</ul>
<?php

// Если пользователь авторизован на сайте
if (UID)
{
	// Вывод кнопки выхода из пользователя
?>				<div class="login">
					<a href="<?=WWW?>/?logout" title="Выйти из пользователя">Выход: <?=$_user['name']?></a>
				</div>
<?php
	
}
else
{
	// Вывод кнопки входа на сайт
?>				<div class="login">
					<a href="<?=WWW?>/login/">Войти на сайт</a>
				</div>
<?php
	
}

?>			</div>
		</div>
	</div>
	<div class="header_spacer">&nbsp;</div>
	<div class="main_frame">
<?php


// Назначение кеширующей функции
ob_start(function ($data)
{
	// Формируем нижний колонтитул
	$footer = <<<EOF
	</div>
	<div class="footer">© 2015 Алексей В. Коньшин. Все права защищены.</div>
</body>
</html>
EOF;
	
	// Возвращаем данные вместе с нижним колонтитулом
	return $data . $footer;
	
});

?>
