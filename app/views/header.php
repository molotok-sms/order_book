<?php if (!$_header) return; ?>
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
<?php

// Если пользователь является Заказчиком
if (UID && $_user && $_user['customer'])
{
	// Вывод специфичных элементов меню
?>					<li class="highlight"><a href="<?=WWW?>/orders/add">Разместить заказ</a></li>
<?php
	
}

// Если пользователь является Исполнителем
if (UID && $_user && $_user['executor'])
{
	// Вывод специфичных элементов меню
?>					<li class="highlight"><a href="<?=WWW?>/orders/my">Мои заказы</a></li>
<?php
	
}

?>
					<li><a href="<?=WWW?>/orders">Заказы</a></li>
					<li><a href="<?=WWW?>/about">О проекте</a></li>
				</ul>
<?php

// Если пользователь авторизован на сайте
if (UID)
{
	// Вывод кнопки выхода из пользователя
?>				<div class="login">
					<a href="<?=WWW?>/logout" title="Выйти из пользователя">Выход: <?=$_user['name']?></a>
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
