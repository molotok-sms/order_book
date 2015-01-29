
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
		xhrFields: { withCredentials: true }
		
	});
	
	return true;
	
}

