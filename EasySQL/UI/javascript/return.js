$('form').on('change', '.return-selector:visible', function(e) {
	$('.update-selector').hide();
	$('.to-return').html('');
	$('.to-update').html('');
	$('.updated').html('');

	var field = $(this).children('option:selected').val();
	if($(this).children('option:selected').val() == '') {
		$('.to-return').html('');
	} else {
		$('.to-return').html('\'return\' => \''+field+'\'');
	}
});

$('form').on('change', '.update-selector:visible', function(e) {

	$('.return-selector').hide();
	$('.to-return').html('');
	$('.to-update').html('');
	$('.updated').html('');

	var field = $(this).children('option:selected').val();

	$('.to-update').html('\'to_update\' => \''+field+'\'');

});