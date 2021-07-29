(function($) {
	var dateFormat = "dd/mm/yy",
	from = $( "#date-from" )
	  .datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		changeYear: true,
	  })
	  .on( "change", function() {
		to.datepicker( "option", "minDate", getDate( this ) );
	  }),
	to = $( "#date-to" ).datepicker({
	  defaultDate: "+1w",
	  changeMonth: true,
	  changeYear: true,
	})
	.on( "change", function() {
	  from.datepicker( "option", "maxDate", getDate( this ) );
	});

  function getDate( element ) {
	var date;
	try {
	  date = $.datepicker.parseDate( dateFormat, element.value );
	} catch( error ) {
	  date = null;
	}

	return date;
}

}(jQuery));