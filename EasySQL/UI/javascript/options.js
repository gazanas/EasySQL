$('form').on('change', '.order', function(e) {
	var value = $(this).val();
	if($('.where-clause').html().length > 0 || $('.to-return').text().length > 0 || $('.to-update').text().length > 0 || $('.updated').text().length > 0) {
		if($('.options').length == 0)
			$('.where-clause').append('<span class=\'options\'>, \'options\' => array(<span class=\'options-in\'></span>)</span>');
	} else {
		if($('.options').length == 0)
			$('.where-clause').append('<span class=\'options\'>\'options\' => array(<span class=\'options-in\'></span>)</span>');
	}

	if($(this).parent().children('.type').length == 0) {
		$(this).after('<select class=\'type\'>\
					<option value=\'asc\'>ASCENDING</option>\
					<option value=\'desc\'>DESCENDING</option>\
				</select>');
	}
	var type = $(this).parent().children('.type').children('option:selected').val();
	if($('.options-in').text().length > 0) {
		if($('.options-in').text().match(/\'order\' =\> (\'|).+(\'|)/)) {
			var new_value = $('.options-in').text().replace(/\'order\' =\> \'[a-zA-Z0-9_]+ (asc|desc)\'/, '\'order\' => \''+value+' '+type+'\'');
			$('.options-in').text(new_value);
		} else {
			var new_value = '\'order\' => \''+value+' '+type+'\', ';
			$('.options-in').prepend(new_value);
		}
	} else {
		$('.options-in').append('\'order\' => \''+value+' '+type+'\'');
	}
});

$('form').on('change', '.type', function(e) {
	var entity = $('#entities option:selected').val();

	if($('.order#'+entity).children('option:selected').val() == 'order') {
		alert('Can not order by none value');
		$(this).val('type');
		e.preventDefault();
		return false;
	}

	var new_value = $('.options-in').text().replace(/(asc|desc)/, $(this).children('option:selected').val());
	$('.options-in').text(new_value);
});

$('form').on('click', '.remove-order', function(e) {
	if($('.options').text().length) {
		var value = $('.options-in').text().replace(/\'order\' => \'[a-zA-Z0-9_]+ (asc|desc)\'/, '');
		value = value.replace(', \'limit', '\'limit');
		$('.order').val('order');
		$('.type').remove();
		$('.options-in').text(value);

		if($('.options-in').text().length == 0) {
			$('.options').remove();
			e.preventDefault();
			return false;
		}
	}
	
	e.preventDefault();

});

$('form').on('keyup', '.limit', function(e) {
	var value = $(this).val();

	if($('.where-clause').html().length > 0 || $('.to-return').text().length > 0 || $('.to-update').text().length > 0 || $('.updated').text().length > 0) {
		if($('.options').length == 0)
			$('.where-clause').append('<span class=\'options\'>, \'options\' => array(<span class=\'options-in\'></span>)</span>');
	} else {
		if($('.options').length == 0)
			$('.where-clause').append('<span class=\'options\'>\'options\' => array(<span class=\'options-in\'></span>)</span>');
	}

	if($('.options-in').text().match(/(,| |)\'limit\' =\> [0-9]/) && e.keyCode == 8) {
	} else if(value == '' && e.keyCode == 8) {
		return false;
	} else if(!value.match(/\d+$/)) {
		$(this).val('');
		alert('Only integer values');
		return false;
	}

	if($('.options-in').text().length > 0) {
		if($('.options-in').text().match(/(,| |)\'limit\' =\> ([0-9]+|)/)) {
			var new_value = $('.options-in').text().replace(/\'limit\' =\> ([0-9]+|)/, '\'limit\' => '+value);
			$('.options-in').text(new_value);
		} else {
			var new_value = ', \'limit\' => '+value;
			$('.options-in').append(new_value);
		}
	} else {
		$('.options-in').append('\'limit\' => '+value);
	}
});

$('form').on('click', '.remove-limit', function(e) {
	if($('.options').text().length) {
		var value = $('.options-in').text().replace(/(, | |)\'limit\' => (([0-9]+)|)/, '');
		$('.limit').val('');
		$('.options-in').text(value);
		e.preventDefault();

		if($('.options-in').text().length == 0) {
			$('.options').remove();
			e.preventDefault();
			return false;
		}
	}
	e.preventDefault();
});