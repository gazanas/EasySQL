$('form').on('keyup', '.where-clause-value', function(e) {

	var is_null = false;
	var id = $(this).attr('id');
	var entity = $('#entities option:selected').val();
	var field = $(this).parent().children('.where-selector:visible').children('option:selected').val();
	var operator = $(this).parent().children('.where-operators').children('option:selected').val();
	var count = $('.where-clause-value').length;
	var first_id = $('.where-clause').children('span:first-child').attr('id');
	
	if(operator == '=')
		api_operator = '';
			
	value = $(this).val();
			
	if(value == '')
		is_null = true;
			
	if(!$.isNumeric(value))
		value = '\''+value+'\'';
		
	if(count >= 1) {
		if(is_null) {
			if($('span #api-'+id).children('span.multiple-'+field).length > 0){
				$('span#api-'+id+' .multiple-'+field).html('');
			} else {
				$('.where-clause span#api-'+id).html('');
			}

		} else {
			if(!checkFirstWhere() && 'api-'+id == first_id) {
				if($('.where-operators#'+id+' option:selected').val() != '=') {
					$('.where-clause span#api-'+id).html('array(\'operator\' => \''+operator+'\', \''+field+'\' => '+value+')');						
				} else if($('.where-clause span#api-'+id).text().match(/array\(\'operator\'/)) {
					var regex = new RegExp('array\\(\'operator\' => \''+operator+'\',( )+\''+field+'\' => .+\\)');
					var new_value = $('.where-clause span#api-'+id).text().replace(regex, 'array(\'operator\' => \''+operator+'\', \''+field+'\' => '+value+')');						
					$('.where-clause span#api-'+id).text(new_value);
				} else {
					$('.where-clause span#api-'+id).html('\''+field+'\' => '+value);	
				}
			} else {
				if($('span#api-'+id+' .multiple-'+field).length) {
					if($('.where-operators#'+id+' option:selected').val() != '=') {
						$('.where-clause span#api-'+id).html(', array(\'operator\' => \''+operator+'\', \''+field+'\' => '+value+')');						
					} else if($('.where-clause span#api-'+id).text().match(/, array\(\'operator\'/)) {
						var regex = new RegExp('array\\(\'operator\' =\> \''+operator+'\',( )+\''+field+'\' =\> .+\\)');
						var new_value = $('.where-clause span#api-'+id).text().replace(regex, 'array(\'operator\' => \''+operator+'\', \''+field+'\' => '+value+')');						
						$('.where-clause span#api-'+id).text(new_value);
					} else {
						$('span#api-'+id+' .multiple-'+field).html(', array(<span class=\''+field+'-elements\' id=\''+id+'\'>\''+field+'\' => '+value+'</span>)');
					}
				} else {
					if($('.where-operators#'+id+' option:selected').val() != '=') {
						$('.where-clause span#api-'+id).html(', array(\'operator\' => \''+operator+'\', \''+field+'\' => '+value+')');						
					} else if($('.where-clause span#api-'+id).text().match(/, array\(\'operator\'/)) {
						var regex = new RegExp('array\\(\'operator\' =\> \''+operator+'\',( )+\''+field+'\' =\> .+\\)');
						var new_value = $('.where-clause span#api-'+id).text().replace(regex, 'array(\'operator\' => \''+operator+'\', \''+field+'\' => '+value+')');		
						$('.where-clause span#api-'+id).text(new_value);
					} else {
						$('.where-clause span#api-'+id).html(', \''+field+'\' => '+value);
					}
				}
			}
		}
	}
});

$('form').on('click', '.remove', function(e) {
	
	var id = $(this).attr('id').split('-')[1];
	entity = $('#entities option:selected').val();
	$(this).parent().remove();

	if('api-'+id == $('.where-clause span').first().attr('id')) {
		$('.where .conditions').first().remove();
		var next_id = parseInt(id)+2;
		$('span.api-conds').children('span.cond-in').children('span').first().remove();
		if($('span.api-conds').children('span.cond-in').children('span').first().attr('id') == next_id) {
			var new_value = $('span.api-conds').children('.cond-in').children('span').first().text()		
			new_value = new_value.replace(',', '');
			$('span.api-conds').children('.cond-in').children('span').first().html(new_value);
		}
	} else {
		var cond_id = parseInt(id);
		$('span.api-conds').children('span.cond-in').children('span#'+String(cond_id)).remove();
		var next_id = parseInt(cond_id)+1;
		if($('span.api-conds').children('span.cond-in').children('span').first().attr('id') == next_id) {
			var new_value = $('span.api-conds').children('span.cond-in').children('span').first().text()		
			new_value = new_value.replace(',', '');
			$('span.api-conds').children('span.cond-in').children('span').first().html(new_value);
		}
	}

	is_first = $('.where-clause span:first-child').attr('id') == 'api-'+id;
	field = $(this).parent().children('.where-selector#'+entity).children('option:selected').val();
	is_multiple = $('.'+field+'-elements').length > 0;
	is_first_multiple = is_multiple && $('span#api-'+id).children().length == 0;

	$('span#api-'+id).remove();


	if(is_first_multiple) {

		var value = $('.'+field+'-elements').first().parent().text();
		var next_id = $('.'+field+'-elements').first().attr('id');
		$('.'+field+'-elements#'+next_id).parent().remove();

		if(value.indexOf('array(') > 0) {
			var new_value = value.replace('array(', '');
			new_value = new_value.replace(')', '');
		}

		if('api-'+next_id == $('.where-clause span:first-child').attr('id'))
			new_value = new_value.replace(',', '');

		$('span#api-'+next_id).html(new_value);
	}

	if(is_first) {

		var first_value = $('.where-clause').children('span:first-child').text();
		first_value = first_value.replace(',', '');
		$('.where-clause span').first().html(first_value);
	}
	
	if(!$.trim($('.cond-in').html())) {
		$('.api-conds').remove();
	}

	e.preventDefault();
});

$('form').on('change', '.where-selector', function(e) {
	var field = $(this).parent().children('.where-selector:visible').children('option:selected').val();
	var operator = $(this).parent().children('.where-operators').children('option:selected').val();
	
	if(operator == '=')
			api_operator = '';
		
	var value = $(this).parent().children('input').val();
	var id = $(this).parent().children('input').attr('id');
	var first_id = $('.where-clause').children('span:first-child').attr('id');

	multiple = $('.where-clause span').map(function(index) {
		value = $(this).text().split('=>')[0];
		return $.trim(value.replace(/(\'|,)/g, ''));
	}).get();

	
	if($.inArray(field, multiple) >= 0) {
	
		$('.where-clause span#api-'+id).html('<span class=\'multiple-'+field+'\' id=\''+$('.multiple-'+field).length+'\'>, array(<span class=\''+field+'-elements\' id=\''+id+'\'>\''+field+'\' => </span>)</span>');

	} else {

		if(!checkFirstWhere() && 'api-'+id == first_id) {
			$('span#api-'+id).html('\''+field+'\' => '+api_operator+' '+value);
			
		} else {
			$('span#api-'+id).html(', \''+field+'\' => '+api_operator+' '+value);
		}
	}
});