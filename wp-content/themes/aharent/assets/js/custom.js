(function($) {

    $(".add-to-cart input#quantity-input").inputSpinner().change(function(e) {
		getProductPrice();
	});

	tinymce.init({
        selector: '#rental-terms'
    });

	var popover = document.querySelector(".popover-dismiss");
	if ( undefined != popover ) {
		new bootstrap.Popover(popover, { trigger: 'focus' });
	}

	var dateFormat = "d/m/Y"; // "d/m/Y H:i";

	$('.add-to-cart :input[name="_date_from"]').datetimepicker({
		format: dateFormat,
		timepicker: false,
  		onShow:	function( ct ) {
			var _dateTo = $('.add-to-cart :input[name="_date_to"]').datetimepicker("getValue");

   			this.setOptions({
				maxDate: $('.add-to-cart :input[name="_date_to"]').val() ? _dateTo : false
			});
		},
	}).change(function(e) {
		getProductPrice();
	});


	$('.add-to-cart :input[name="_date_to"]').datetimepicker({
		format: dateFormat,
		timepicker: false,
		onShow:	function( ct ){
			
			var _dateFrom = $('.add-to-cart :input[name="_date_from"]').datetimepicker("getValue");
			var _dateFromValue = $('.add-to-cart :input[name="_date_from"]').val();

			this.setOptions({
				minDate: _dateFromValue ? _dateFrom : false,
				value: _dateFromValue ? _dateFrom : "",
			});

		}
	}).change(function(e) {
		getProductPrice();
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
		
		if ( productID && dateFrom && dateTo)
			wp.ajax.post( "get_product_price", {
				id 			: productID,
				quantity	: quantity,
				date_from	: dateFrom,
				date_to		: dateTo,	
			} )
				.done(function(response) {
					$(".price-item-value.rental").first().html( response['data']['price']);
					$(".deposit .price-item-value").first().html( response['data']['deposit']);
					$(".deposit").attr( "style", "display: flex !important; ")
				});
	}
	
		
	// $("button.remove").click(function(e) {

	// 	e.preventDefault();

	// 	var productId = $(this).parent().attr("data-product_id"),
	// 		cart_item_key = $(this).parent().attr("data-cart_item_key");


	// 	wp.ajax.post('product_remove', {
	// 		product_id:		productId,
	// 		cart_item_key:	cart_item_key
	// 	}).done( function( response ) {
	// 		alert(response);
	// 	});

	// });

})(jQuery);

