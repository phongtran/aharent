jQuery( document ).ready( function ( $ ) {


	$( '.section_choice' ).click( function () {

		$( '.section_choice' ).removeClass( 'active' );
		$( this ).addClass( 'active' );

		$( '.settings-section' ).removeClass( 'active' );
		$( '#' + $( this ).data( 'section' ) + '_section' ).addClass( 'active' );

		window.location.href = $( this ).attr( 'href' );
	} );

	setTimeout( function () {
		if ( window.location.hash.indexOf( 'section' ) !== - 1 ) {
			$( '.section_choice[href="' + window.location.hash + '"]' ).click()
		} else {
			$( '.section_choice' ).first().click()
		}
	}, 0 );

	setTimeout(function () {
		$('#update_price_with_qty').change(function() {
			if (this.checked) {
				$('#show_spinner_when_update_price').closest('tr').show()
				$('#replace_variable_price').closest('tr').show()
			} else {
				$('#replace_variable_price').prop('checked', false);
				$('#replace_variable_price').closest('tr').hide()
        $('#show_spinner_when_update_price').closest('tr').hide()
			}
		}).trigger('change');
	}, 0);

} );
