(function($) {

	$('input.number-spinner').inputSpinner();

	tinymce.init({
		selector: '.tinymce-form'
	});


    $(".add-to-cart input.number-spinner").change(function(e) {
		getProductPrice();
	});

	$('.add-to-cart select.time-unit').change(function(e) {
		$('.time-delimiter .time-unit').text($(this).find('option:selected').first().text().toLowerCase());
		getProductPrice();
	});

	var dateFormat = "d/m/Y"; // "d/m/Y H:i";

	jQuery.datetimepicker.setLocale('vi');
	

	$('.add-to-cart :input[name="_date_from"]').datetimepicker({
		format: dateFormat,
		timepicker: false,
		minDate: 0,
	}).change(function(e) {
		
		e.preventDefault();
		getProductPrice();
	});


	$('.add-to-cart :input[name="_date_to"]').datetimepicker({
		format: dateFormat,
		timepicker: false,
		onShow:	function( ct ){
			
			var _dateFrom = $('.add-to-cart :input[name="_date_from"]').datetimepicker("getValue");

			this.setOptions({
				minDate: _dateFrom,
			});

		}
	}).change(function(e) {
		e.preventDefault();

		getProductPrice();
	});

	$('.add-to-cart button.aha-button').click(function(e) {

		var quantity 		= $("#quantity-input").val(),
			dateFrom 		= $("#date-from").val(),
			dateTo 			= $("#date-to").val();
		
		if ( "" == quantity ) {
			$('.woocommerce-cart-form .quantity input').addClass('validate-enabled');
			e.preventDefault();
		}

		if ( "" == dateFrom ) {
			$('.add-to-cart :input[name="_date_from"]').addClass('validate-enabled');
			$('.validate').show();
			e.preventDefault();
		}

		if ( "" == dateTo ) {
			$('.add-to-cart :input[name="_date_to"]').addClass('validate-enabled');
			$('.validate').show();
			e.preventDefault();
		}
	});


	$('.woocommerce-cart-form .quantity input').inputSpinner().change(function(e) {
		cartItemChanged(e);
	});

	$('.woocommerce-cart-form .duration input').change(function(e) {
		cartItemChanged(e);
	});

	$('.woocommerce-cart-form .delivery-option input').change(function(e) {
		cartItemChanged(e);
	});

	$('.woocommerce-cart-form select.time-unit').change(function(e) {
		var dataKey = $(this).attr('data-key'),
			selector = 'span[data-key=' + dataKey + ']';

		$('.woocommerce-cart-form').find(selector).first().text(($(this).find('option:selected').first().text().toLowerCase()));
		cartItemChanged(e);
	});

	$('.woocommerce-cart-form .date-picker-input input').datetimepicker({
		format: dateFormat,
	}).change(function(e) {
		cartItemChanged(e);
	});

	function cartItemChanged(e) {
		$('.woocommerce-cart-form :input[name="update_cart"]').attr('disabled', false).attr('aria-disabled', false);
	}




	function getProductPrice() {

		var productIdArr 	= $(".product").first().attr("id").split("-"),
			productID 		= productIdArr[1],
			quantity 		= $("#quantity-input").val(),
			duration		= $("#duration").val(),
			dateFrom 		= $("#date-from").val(),
			// dateTo 			= $("#date-to").val();
			postData 		= {
				id 			: productID,
				quantity	: quantity,
				duration	: duration,
				date_from	: dateFrom,
				// date_to		: dateTo,
			};
		
		postData['time_unit'] = $('#time-unit').val();
		
		
		if ( productID && dateFrom && duration) {
			$('.loading-price').show();
			wp.ajax.post( "get_product_price", postData )
				.done(function(response) {
					$(".price-item-value.rental").first().html( response['data']['price']);
					$(".deposit .price-item-value").first().html( response['data']['deposit']);
					$(".deposit").attr( "style", "display: flex !important; ");

					$('.woocommerce-cart-form .quantity input').removeClass('validate-enabled');
					$('.add-to-cart :input[name="_date_from"]').removeClass('validate-enabled');
					$('.add-to-cart :input[name="_date_to"]').removeClass('validate-enabled');
					$('.validate').hide();
					$('.loading-price').hide();

					//$('.price-wrapper').show().attr('style', 'display: flex;')
				});

		}
			
	}


	$('#order_vat').change(function() {
		
		if ($(this).is(':checked')) {
			$('#order_vat_company').prop('disabled', false);
			$('#order_vat_company_field').addClass('validate-required').removeClass('woocommerce-invalid woocommerce-validated');

			$('#order_vat_code').prop('disabled', false).addClass('validate-required');
			$('#order_vat_code_field').addClass('validate-required').removeClass('woocommerce-invalid woocommerce-validated');
			
			$('#order_vat_address').prop('disabled', false).addClass('validate-required');
			$('#order_vat_address_field').addClass('validate-required').removeClass('woocommerce-invalid woocommerce-validated');
		} else {
			$('#order_vat_company').prop('disabled', true).removeClass('validate-required');
			$('#order_vat_company_field').removeClass('validate-required woocommerce-invalid woocommerce-validated');

			$('#order_vat_code').prop('disabled', true).removeClass('validate-required');
			$('#order_vat_code_field').removeClass('validate-required woocommerce-invalid woocommerce-validated');

			$('#order_vat_address').prop('disabled', true).removeClass('validate-required');
			$('#order_vat_address_field').removeClass('validate-required woocommerce-invalid woocommerce-validated');
		}


		$('body').trigger('update_checkout');
	});


	$('.wc_payment_methods input[type=radio]').click(function(e) {
		if ( $( '.payment_methods input.input-radio' ).length > 1 ) {
			var target_payment_box = $( 'div.payment_box.' + $( this ).attr( 'ID' ) ),
				is_checked         = $( this ).is( ':checked' );

			if ( is_checked && ! target_payment_box.is( ':visible' ) ) {
				$( 'div.payment_box' ).filter( ':visible' ).slideUp( 230 );

				if ( is_checked ) {
					target_payment_box.slideDown( 230 );
				}
			}
		} else {
			$( 'div.payment_box' ).show();
		}

		if ( $( this ).data( 'order_button_text' ) ) {
			$( '#place_order' ).text( $( this ).data( 'order_button_text' ) );
		} else {
			$( '#place_order' ).text( $( '#place_order' ).data( 'value' ) );
		}
	});
	

})(jQuery);

