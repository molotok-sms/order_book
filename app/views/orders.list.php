<?php

// Подключение верхнего колонтитула
require('header.php');

?><div class="orders">
<?php

// Перебор элементов
foreach ($_data['data'] as $item)
{
	// Вывод текущего элемента
?>	<div class="item padding30">
		<div class="header">
			<?=$item['title']?>
		</div>
		<div class="content">
			<?=$item['content']?>
		</div>
	</div>
<?php
	
}


?></div>
<?php

// Подключение нижнего колонтитула
require('footer.php');

?>
