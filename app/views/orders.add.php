<?php

// Настройка заголовка
$_header_title = 'Разместить заказ';

// Подключение верхнего колонтитула
require('header.php');

?><script language="javascript">

$(document).ready(function ()
{
	$('form.orders-add').on('submit', function (e)
	{
		var d = {};
		var errs = false;
		var err;
		
		var messages = {
			order_title: 'Укажите заголовок',
			order_description: 'Введите описание',
			order_price: 'Укажите цену'
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
		
		if (!d['order_price'].trim())		errs |= err(this, 'order_price', messages['order_price']);
		if (!d['order_description'].trim())	errs |= err(this, 'order_description', messages['order_description']);
		if (!d['order_title'].trim())		errs |= err(this, 'order_title', messages['order_title']);
		
		if (errs)
		{
			$('.error_string').show();
			return false;
		}
		
		
		form_ajax_submit({ form: this, data: { ajax: 1 }, error: '.error_string', content: '.main_frame', callback: function (data)
		{
			window.scrollTo(0, 0);
			
			var title = document.title.replace(/^(.*) ::/, '');
			history.replaceState('', title, '<?=WWW?>/orders');
			document.title = title;
			
		}});
		
	});
	
});

</script>
<form action="" class="orders-add simple_page" method="post">
	<h1 class="header">Разместить заказ</h1>
	<div class="content">
		<table align="center">
			<tr>
				<td>Заголовок:</td>
				<td><input <?=(!$_data['error_field'] ? 'autofocus' : '')?><?=(($_data['error_field'] == 'order_title') ? 'autofocus class="state-error"' : '')?> maxlength=256 name="order_title" placeholder="Заголовок" value="<?=$_data['data']['order_title']?>"></td>
			</tr>
			<tr>
				<td colspan=2><textarea <?=(($_data['error_field'] == 'order_description') ? 'autofocus class="state-error"' : '')?> maxlength=65533 name="order_description" placeholder="Описание заказа"><?=$_data['data']['order_description']?></textarea></td>
			</tr>
			<tr>
				<td>Цена:</td>
				<td><input <?=(($_data['error_field'] == 'order_price') ? 'autofocus class="state-error"' : '')?> id="order_price" maxlength=7 name="order_price" placeholder="Цена" value="<?=$_data['data']['order_price']?>"> руб.</td>
			</tr>
		</table>
		<div class="action">
			<input class="button" type="submit" value="Разместить" />
		</div>
		<div class="error_string state-error" <?php if (!$_data['error']) { ?>style="display: none;"<?php } ?>><?=$_data['error']?></div>
	</div>
</form>
<?php

// Подключение нижнего колонтитула
require('footer.php');

?>
