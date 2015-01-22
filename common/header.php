<?php

// Подключение движка сайта
require_once('common.php');

?>
<!DOCTYPE html>
<html>
<head>
<link href="<?=WWW?>/css/style.css" rel="stylesheet" type="text/css" />
<meta http-equiv="content-type" content="text/html;charset=<?=CODEPAGE?>">
<script language="javascript" src="<?=WWW?>/js/jquery.min.js"></script>
<script language="javascript" src="<?=WWW?>/js/jquery-ui.min.js"></script>
<script language="javascript" src="<?=WWW?>/js/jquery.blockUI.js"></script>
<title>Система размещения заказов</title>
</head>
<body>
	<div class="header_frame">
		<div class="header">
			<div class="title">
				Система размещения заказов
			</div>
			<div class="menu">
				<ul>
					<li class="highlight">Разместить заказ</li>
					<li>Меню 2</li>
					<li>Меню 3</li>
				</ul>
				<div class="login">
					<a href="<?=WWW?>/login/">Войти на сайт</a>
				</div>
			</div>
		</div>
	</div>
	<div class="header_spacer">&nbsp;</div>
	<div class="main_frame">
<?php


// Назначение кеширующей функции
ob_start(function ($data)
{
	// Формируем нижнюю часть сайта (футер)
	$footer = <<<EOF
	</div>
	<div class="footer">© 2015 Алексей В. Коньшин. Все права защищены.</div>
</body>
</html>
EOF;
	
	// Возвращаем данные вместе с футером
	return $data . $footer;
	
});

?>
