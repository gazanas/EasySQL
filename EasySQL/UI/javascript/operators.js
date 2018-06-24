$('form').on('change', '.where-operators', function(e) {
	var id = $(this).attr('id');
	var value = $(this).val();
	
	if($('.where-clause').children('#api-'+id).text().match(/^, (\'.+\'|array)/)) {
		if($('.where-clause').children('#api-'+id).text().match(/, array\(/)) {
			var new_value = $('.where-clause').children('#api-'+id).text().replace('array(', '');
			new_value = new_value.replace(/\'operator\' =\> \'(=|\>|\<|LIKE|\<\>)\',/, '');
			new_value = new_value.replace(/\)$/, '');
			$('.where-clause').children('#api-'+id).text(new_value);
		}
		$('.where-clause').children('#api-'+id).text(', array(\'operator\' => \''+value+'\', '+$('.where-clause').children('#api-'+id).text().replace(',', '')+')');
	} else {
		if($('.where-clause').children('#api-'+id).text().match(/array\(/)) {
			var new_value = $('.where-clause').children('#api-'+id).text().replace('array(', '');
			new_value = new_value.replace(/\'operator\' =\> \'(=|\>|\<|LIKE|\<\>)\',/, '');
			new_value = new_value.replace(/\)$/, '');
			$('.where-clause').children('#api-'+id).text(new_value);
		}
		$('.where-clause').children('#api-'+id).text('array(\'operator\' => \''+value+'\', '+$('.where-clause').children('#api-'+id).text()+')');		
	}
});