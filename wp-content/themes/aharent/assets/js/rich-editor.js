
	var dateFormat = "d/m/Y";
	
    tinymce.init({
		selector: 'textarea.tinymce-form'
	});

	(function($) {
		$('.date-time-picker').datetimepicker({
			format: dateFormat,
			timepicker: false,
			minDate: 0,
		});
	
	})(jQuery)