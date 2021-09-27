(function($) {

	$('input.number-spinner').inputSpinner();


    $(".add-to-cart input.number-spinner").change(function(e) {
		getProductPrice();
	});

	tinymce.init({
        selector: '#rental-terms'
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
	

})(jQuery);

