(function($) {

    $(".add-to-cart input#quantity-input").inputSpinner().change(function(e) {
		getProductPrice();
	});

	// tinymce.init({
    //     selector: '#rental-terms'
    // });s

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
			dateFrom 		= $("#date-from").val(),
			dateTo 			= $("#date-to").val();
		
		if ( productID && dateFrom && dateTo) {
			$('.loading-price').show();
			wp.ajax.post( "get_product_price", {
				id 			: productID,
				quantity	: quantity,
				date_from	: dateFrom,
				date_to		: dateTo,	
			} )
				.done(function(response) {
					$(".price-item-value.rental").first().html( response['data']['price']);
					$(".deposit .price-item-value").first().html( response['data']['deposit']);
					$(".deposit").attr( "style", "display: flex !important; ");

					$('.woocommerce-cart-form .quantity input').removeClass('validate-enabled');
					$('.add-to-cart :input[name="_date_from"]').removeClass('validate-enabled');
					$('.add-to-cart :input[name="_date_to"]').removeClass('validate-enabled');
					$('.validate').hide();
					$('.loading-price').hide();
				});

		}
			
	}
	

})(jQuery);

