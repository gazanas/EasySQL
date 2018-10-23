function checkFirstWhere() {
	
	var toReturn = $('.to-return').text();
	var toUpdate = $('.to-update').text();
	if((toReturn != '' || toUpdate != '')) {
		return true;
	} else {
		return false;
	}
}

$('#entities').on('change', function(e) {
		
	var action = $('#action').children('option:selected').val();
	var entity = $('#entities').children('option:selected').val();

	$('.return-selector').hide();
	$('.update-selector').hide();
	$('.where').remove();
	$('.insert-section').hide();
	$('.where-clause').empty();
	$('.api-conds').remove();
	$('span.entity').text(entity);
	$('.order').hide();
	$('.option-area').remove();

	if(action == 'GET') {

		if($('.option-area').length) {
			var place = $('.option-area').last();
		} else {
			var place = $('.test');
		}
		
		place.after('<div class=\'form-group row option-area\' id=\''+$('.option-area').length+'\'></div>');
		var count = parseInt($('.option-area').length) - 1;
		$('.option-area#'+count).append('<select class=\'order-selector\'>\
			<option value=\'order\'>ORDER BY</option>\
			</select>');
		place.after('<div class=\'form-group row option-area\' id=\''+$('.option-area').length+'\'></div>');
		var count = parseInt($('.option-area').length) - 1;
		$('.option-area#'+count).append('<select class=\'limit-selector\'>\
			<option value=\'limit\'>LIMIT</option>\
			</select>');

		var field = $('select.return-selector#'+entity).children('option:selected').val();
		$('select.return-selector#'+entity).show();
		if($('.where-clause').children('span:first-child').text().indexOf(',') < 0)
			$('.where-clause').children('span:first-child').html(', '+$('.where-clause').children('span:first-child').text());	
				
		$('.to-return').html('\'return\' => \''+field+'\'');

		if($('.option-area').length) {
			var place = $('.option-area').last();
		} else {
			var place = $('.test');
		}
		
		place.after('<div class=\'form-group row option-area\' id=\''+$('.option-area').length+'\'></div>');
		var count = parseInt($('.option-area').length) - 1;
		$('.option-area#'+count).append('<select class=\'order-selector\'>\
			<option value=\'order\'>ORDER BY</option>\
			</select>');
		place.after('<div class=\'form-group row option-area\' id=\''+$('.option-area').length+'\'></div>');
		var count = parseInt($('.option-area').length) - 1;
		$('.option-area#'+count).append('<select class=\'limit-selector\'>\
			<option value=\'limit\'>LIMIT</option>\
			</select>');

	} else if(action == 'UPDATE') {

		$('.update-selector#'+entity).show();
		var to_update = $('.update-selector#'+entity).children('option:selected').val();
		$('.to-update').html('\'to_update\' => \''+to_update+'\'');
	
	} else if(action == 'INSERT') {
		
		$('.insert-section#'+entity).show();
	
		$('.insert-section#'+entity).children('.left-insert').children('.insert-input').each(function(index){
			$('.where-clause').append('<span class=\'insert\' id=\''+$(this).attr('id')+'\'></span>');
		});
	}
});

$('.test').click(function(e) {

	var action = $('#action').children('option:selected').val();
	var entity = $('#entities').children('option:selected').val();
	var return_flag = (action == 'VALUE' && $('.return-selector#'+entity).children('option:selected').val() == 'Return');
	var update_flag = (action == 'UPDATE' && ($('.update-selector#'+entity).children('option:selected').val() == 'Update' || $('.update-value').val() == ''));
	if(return_flag) {
		alert('Please select a value to return');
		return false;
	} else if(update_flag) {
		alert('Please complete the updated field and value');
		return false;
	}
	var command = $('.api-call').text();
	
	$.ajax({
		url: 'test_api.php',
		type: 'POST',
		data: {'command': command},
		success: function(data, status) {
			$('body .result').html(data);
		}, error: function(xhr) {
			$('body .result').html(xhr.responseText);
		}
	});
	
	e.preventDefault();
});