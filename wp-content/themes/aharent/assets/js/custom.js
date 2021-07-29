(function($) {

    $("input#quantity-input").inputSpinner();


	var dateFormat = "d/m/Y H:i";

	$("#date-from").datetimepicker({
		format: dateFormat,
  		onShow:	function( ct ) {
			var _dateTo = $("#date-to").datetimepicker("getValue");

   			this.setOptions({
				maxDate: $("#date-to").val() ? _dateTo : false
			});
		},
	});


	$("#date-to").datetimepicker({
		format: dateFormat,
		onShow:	function( ct ){
			
			var _dateFrom = $("#date-from").datetimepicker("getValue");
			var _dateFromValue = $("#date-from").val();

			this.setOptions({
				minDate: _dateFromValue ? _dateFrom : false,
				value: _dateFromValue ? _dateFrom : "",
			});
		},
	});


    // var dateFormat = "dd/mm/yy H:i ",
	// from = $( "#date-from" )
	//   .datetimepicker({
	// 	format: dateFormat,
	//   })
	//   .on( "change", function() {
	// 	to.datepicker( "option", "minDate", getDate( this ) );
	//   }),
	// to = $( "#date-to" ).datetimepicker({
	//   format: dateFormat,
	// })
	// .on( "change", function() {
	//   from.datetimepicker( "option", "maxDate", getDate( this ) );
	// });

    // function getDate( element ) {
    //     var date;
    //     try {
    //         date = $.datepicker.parseDate( dateFormat, element.value );
    //     } catch( error ) {
    //         date = null;
    //     }

    //     return date;
    // }

})(jQuery);