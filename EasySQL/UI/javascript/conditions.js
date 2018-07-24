$('form').on('change', '.conditions', function(e) {
	var value = $(this).children('option:selected').val();
	var id = $(this).attr('id');

	if($.trim($(".cond-in").html())) {
		if($('.cond-in').children('span#'+id).length) {
			if(id == $('.cond-in').children('span:first-child').attr('id')) {
				$('.cond-in').children('span#'+id).html('\''+value+'\'');
			} else {
				$('.cond-in').children('span#'+id).html(', \''+value+'\'');				
			}
		} else {
			if(id == $('.cond-in').children('span:first-child').attr('id')) {
				$('.cond-in').append('<span id=\''+id+'\'>\''+value+'\'</span>');
			} else {
				$('.cond-in').append('<span id=\''+id+'\'>, \''+value+'\'</span>');
			}
		}
	} else {
		$('.api-conds').empty();
		$('.where-clause').after('<span class=\'api-conds\'>, \'condition\' => array(<span class=\'cond-in\'></span>)</span>');
		$('.where').each(function(index) {
			var id = $(this).children('.where-clause-value').attr('id');
			var first = $('.where-clause-value').first().attr('id');
			if(id != first) {
				if(!$.trim($(".cond-in").html())) {
					$('.cond-in').append('<span id=\''+id+'\'>\''+$(this).children('.conditions').children('option:selected').val()+'\'');
				} else {
					$('.cond-in').append('<span id=\''+id+'\'>, \''+$(this).children('.conditions').children('option:selected').val()+'\'');
				}
			}
		});
	}
});