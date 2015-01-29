<?php

// Настройка заголовка
$_header_title = 'О проекте';

// Подключение верхнего колонтитула
require('header.php');

?><div class="about simple_page">
	<h1 class="header"><?=$_data['data']['title']?></h1>
	<div class="content"><?=$_data['data']['content']?></div>
</div>
<?php

// Подключение нижнего колонтитула
require('footer.php');

?>
