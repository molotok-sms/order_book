
$(document).ready(function ()
{
	history.replaceState({ title: document.title }, document.title, location.href);
	
	$(document).on('click', 'a', function (e)
	{
		var document_title;
		var title;
		var url = this.href;
		var menu_item;
		
		if ((url.indexOf('/login/') >= 0) || (url.indexOf('/logout/') >= 0)) return;
		
		e.preventDefault();
		
		document_title = document.title.replace(/^(.*) ::/, '');
		menu_item = ($(this).closest('.menu')) ? this.parentNode : false;
		
		if (this.hasAttribute('data-title'))
		{
			title = (this['data-title'] ? this['data-title'].trim() : '');
		}
		else
		{
			title = this.innerText.trim();
		}
		
		title = ((title && title != document_title) ? title + ' ::' : '') + document_title;
		
		load_content(url, true, title, menu_item);
		
	});
	
	$(window).on('popstate', function (e)
	{
		if (!e.originalEvent) return;
		if (!e.originalEvent.state) return;
		
		document.title = e.originalEvent.state['title'];
		load_content(location.href);
		
	});
	
});


function get_form_data (form)
{
	if (typeof form === 'undefined') return false;
	if (typeof form.elements === 'undefined') return false;
	
	var els = form.elements;
	var el;
	var i;
	
	var form_data = {};
	
	for (i = 0; i < els.length; i++)
	{
		el = els[i];
		if (!el.name) continue;
		
		if (el.type && (el.type == 'checkbox'))
		{
			if (el.checked === true)
			{
				form_data[el.name] = 'on';
			}
		}
		else
		{
			form_data[el.name] = el.value;
		}
		
	}
	
	return form_data;
	
}


function form_ajax_submit (p)
{
	/*
		form		- (required) form
		action		- (optional) url
		callback	- (optional) function (data)
		content		- (optional) selector
		data		- (optional) { data }, complements the form data
		error		- (optional) selector
		form_data	- (optional) boolean, true: (form data + data), false: data only, DEFAULT: true
		xhrFields
	*/
	
	if (typeof p !== 'object') return false;
	
	if (typeof p.form === 'undefined') return false;
	if (typeof p.form.ownerDocument === 'undefined') p.form = $(p.form).get(0);
	if (typeof p.form.ownerDocument === 'undefined') return false;
	
	
	var ajax_data = {};
	
	if ((typeof p.form_data === 'undefined') || p.form_data)
	{
		var form_data = get_form_data(p.form);
		if (form_data) $.extend(ajax_data, form_data);
	}
	
	if (typeof p.data === 'object') $.extend(ajax_data, p.data);
	
	$.ajax
	({
		data: ajax_data,
		error: function (jqXHR, textStatus, errorThrown)
		{
			$(p.error).text('Ошибка обращения к серверу!').show();
			
		},
		success: function (data, textStatus)
		{
			if (textStatus != 'success')
			{
				$(p.error).text('Ошибка выполнения запроса!').show();
				return;
				
			}
			
			$(p.content).html(data);
			
			if (typeof p.callback === 'function')
			{
				p.callback(data);
				
			}
			
		},
		timeout: 15000,
		type: 'POST',
		url: (typeof p.action !== 'undefined') ? p.action : p.form.action,
		xhrFields: p.xhrFields
		
	});
	
	return true;
	
}


function load_content (url, push_history, title, menu_item)
{
	$.ajax
	({
		data: { ajax: 1 },
		error: function (jqXHR, textStatus, errorThrown)
		{
			console.log('Ошибка обращения к серверу!');
			
		},
		success: function (data, textStatus, jqXHR)
		{
			if (textStatus != 'success')
			{
				console.log('Ошибка выполнения запроса!');
				return;
				
			}
			
			$('.main_frame').html(data);
			
			if (push_history)
			{
				history.pushState({ title: title }, title, url);
				document.title = title;
				
			}
			
			if (menu_item)
			{
				$('.menu .selected').removeClass('selected');
				$(menu_item).addClass('selected');
				
			}
			
		},
		timeout: 15000,
		type: 'GET',
		url: url,
		
	});
	
}

