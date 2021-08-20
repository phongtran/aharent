<?php
/**
 * Plugin Name: OnePAY payment gateway for WooCommerce - Credit card
 * Plugin URI: http://onepay.vn
 * Description: Full integration for Onepay payment gateway for WooCommerce
 * Version: 1.4.3
 * Author: OnePAY
 * Author URI: http://onepay.vn
 * License: GPL2
 */

add_action('plugins_loaded', 'woocommerce_onepayUS_init', 0);

function woocommerce_onepayUS_init(){
  if(!class_exists('WC_Payment_Gateway')) return;

  class WC_onepayUS extends WC_Payment_Gateway{

    // URL checkout của onepay.vn - Checkout URL for OnePay
    private $onepay_url;

    // Mã merchant site code
    private $merchant_site_code;

    // Mật khẩu bảo mật - Secure password
    private $secure_pass;

    // Debug parameters
    private $debug_params;
    private $debug_md5;

    function __construct(){

      $this->icon = plugins_url( 'onepay-payment-gateway-for-woocommerce-paygate/logo.png', dirname(__FILE__) ); // Icon URL
      $this->id = 'onepayus';
      $this->method_title = 'OnePAY-PAGATE';
      $this->has_fields = false;

      $this->init_form_fields();
      $this->init_settings();

      $this->title = $this->settings['title'];
      $this->description = $this->settings['description'];

      $this->onepay_url = $this->settings['onepay_url'];
      $this->merchant_access_code = $this->settings['merchant_access_code'];
      $this->merchant_id = $this->settings['merchant_id'];
      $this->secure_secret = $this->settings['secure_secret'];
      //$this->redirect_page_id = $this->settings['redirect_page_id'];

      $this->debug = $this->settings['debug'];
      $this->order_button_text = __( 'Pay now', 'monepayus' );

      $this->msg['message'] = "";
      $this->msg['class'] = "";

      if ( version_compare( WOOCOMMERCE_VERSION, '2.0.8', '>=' ) ) {
                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
             } else {
                add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
            }
    // Add the page after checkout to redirect to OnePAY
    add_action( 'woocommerce_receipt_onepayus', array(&$this, 'receipt_page') );
    add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	add_action( 'woocommerce_thankyou_onepayus', array( $this, 'thankyou_page' ) );
   }
    function init_form_fields(){
        // Admin fields
       $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Activate', 'monepayus'),
                    'type' => 'checkbox',
                    'label' => __('Activate the payment gateway for OnePAY', 'monepayus'),
                    'default' => 'no'),
                'title' => array(
                    'title' => __('Name:', 'monepayus'),
                    'type'=> 'text',
                    'description' => __('Name of payment method (as the customer sees it)', 'monepayus'),
                    'default' => __('onepayUS', 'monepayus')),
                'description' => array(
                    'title' => __('', 'monepayus'),
                    'type' => 'textarea',
                    'description' => __('Payment gateway description', 'monepayus'),
                    'default' => __('Click place order and you will be directed to the OnePAY website in order to make payment', 'monepayus')),


                'nlcurrency' => array(
                    'title' => __('Currency', 'monepayus'),
                   'type' => 'text',
                   'default' => 'vnd',
                    'description' => __('"vnd" or "usd"', 'monepayus')
                ),
               'onepay_url' => array(
                  'title' => __( 'OnePAY URL', 'monepayus'),
                  'type' => 'text'
                ),
				'merchant_id' => array(
                  'title' => __( 'Merchant ID', 'monepayus'),
                  'type' => 'text'
                ),
               'merchant_access_code' => array(
                  'title' => __( 'Merchant Access Code', 'monepayus'),
                  'type' => 'text'
                ),

                'secure_secret' => array(
                  'title' => __( 'Secure Secret', 'monepayus'),
                  'type' => 'text'
                ),
                'debug' => array(
                    'title' => __('Debug', 'monepayus'),
                    'type' => 'checkbox',
                    'label' => __('Debug OnePAY plugin', 'monepayus'),
                    'default' => 'no')
            );
    }

    public function admin_options(){
      echo '<h3>'.__('onepayUS Payment Gateway', 'monepayus').'</h3>';
      echo '<table class="form-table">';
      // Generate the HTML For the settings form.
      $this->generate_settings_html();
      echo '</table>';
    }

    /**
     *  There are no payment fields for onepayUS, but we want to show the description if set.
     **/
    function payment_fields(){
        if($this->description) echo wpautop(wptexturize(__($this->description, 'monepayus')));
    }

    /**
     * Process the payment and return the result
     **/
    function process_payment( $order_id ) {
      $order = new WC_Order( $order_id );

		if ( ! $this->form_submission_method ) {

			return array(
				'result'    => 'success',
				'redirect'  => $this->generate_onepayUS_url( $order_id )
			);

		}
    }

    /**
     * Receipt Page
     **/
    function receipt_page( $order_id ){
        echo '<p>'.__('Chúng tôi đã nhận đơn đặt hàng của Quý khách. <br /><b>Hệ thống sẽ tự động chuyển tiếp đến hệ thống của OnePay để xử lý.', 'monepayus').'</p>';
        $checkouturl = $this->generate_onepayUS_url( $order_id );

        if ($this->debug == 'yes') {
          // Debug just shows the URL
          echo '<code>' . $checkouturl . '</code>';
          // echo '<p>secure pass ' . $this->secure_pass . '</p>';
          // echo '<p>params ' . strval($this->debug_params) . '</p>';
          // echo '<p>md5 ' . strval($this->debug_md5) . '</p>';
        } else {
          // Adds javascript to the post-checkout screen to redirect to OnePAY with a fully-constructed URL
          // Note: wp_redirect() fails with OnePAY
          echo '<a href="' . $checkouturl . '">' . __('Kích vào đây để thanh toán nếu hệ thống không tự động chuyển tiếp sau 5 giây', 'monepayus') . '</a>';
          echo "<script type='text/javaScript'><!--
          setTimeout(\"location.href = '" . $checkouturl . "';\",1500);
          --></script>";
        }
    }

    function thankyou_page( $order_id ) {

	// Return to site after checking out with OnePAY
      // Note this has not been fully-tested
      global $woocommerce;

      $order = new WC_Order( $order_id );

      // *********************
		// START OF MAIN PROGRAM
		// *********************


		// Define Constants
		// ----------------
		// This is secret for encoding the MD5 hash
		// This secret will vary from merchant to merchant
		// To not create a secure hash, let SECURE_SECRET be an empty string - ""
		// $SECURE_SECRET = "secure-hash-secret";
		$SECURE_SECRET = $this->settings['secure_secret'];//93E963BC17BF022F2A03B685784D0CFA
		//$SECURE_SECRET = "93E963BC17BF022F2A03B685784D0CFA";
		// If there has been a merchant secret set then sort and loop through all the
		// data in the Virtual Payment Client response. While we have the data, we can
		// append all the fields that contain values (except the secure hash) so that
		// we can create a hash and validate it against the secure hash in the Virtual
		// Payment Client response.


		// NOTE: If the vpc_TxnResponseCode in not a single character then
		// there was a Virtual Payment Client error and we cannot accurately validate
		// the incoming data from the secure hash. */

		// get and remove the vpc_TxnResponseCode code from the response fields as we
		// do not want to include this field in the hash calculation
		$vpc_Txn_Secure_Hash = $_GET ["vpc_SecureHash"];
		unset ( $_GET ["vpc_SecureHash"] );

		// set a flag to indicate if hash has been validated
		$errorExists = false;

		if (strlen ( $SECURE_SECRET ) > 0 && $_GET ["vpc_TxnResponseCode"] != "7" && $_GET ["vpc_TxnResponseCode"] != "No Value Returned") {
			ksort($_GET);
			//$stringHashData = $SECURE_SECRET;
			//*****************************khởi tạo chuỗi mã hóa rỗng*****************************
			$stringHashData = "";

			// sort all the incoming vpc response fields and leave out any with no value
			foreach ( $_GET as $key => $value ) {
		//        if ($key != "vpc_SecureHash" or strlen($value) > 0) {
		//            $stringHashData .= $value;
		//        }
		//      *****************************chỉ lấy các tham số bắt đầu bằng "vpc_" hoặc "user_" và khác trống và không phải chuỗi hash code trả về*****************************
				if ($key != "vpc_SecureHash" && (strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
					$stringHashData .= $key . "=" . $value . "&";
				}
			}
		//  *****************************Xóa dấu & thừa cuối chuỗi dữ liệu*****************************
			$stringHashData = rtrim($stringHashData, "&");


		//    if (strtoupper ( $vpc_Txn_Secure_Hash ) == strtoupper ( md5 ( $stringHashData ) )) {
		//    *****************************Thay hàm tạo chuỗi mã hóa*****************************
			if (strtoupper ( $vpc_Txn_Secure_Hash ) == strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*',$SECURE_SECRET)))) {
				// Secure Hash validation succeeded, add a data field to be displayed
				// later.
				$hashValidated = "CORRECT";
			} else {
				// Secure Hash validation failed, add a data field to be displayed
				// later.
				$hashValidated = "INVALID HASH";
			}
		} else {
			// Secure Hash was not validated, add a data field to be displayed later.
			$hashValidated = "INVALID HASH";
		}

		// Define Variables
		// ----------------
		// Extract the available receipt fields from the VPC Response
		// If not present then let the value be equal to 'No Value Returned'
		// Standard Receipt Data
		$amount = $this->null2unknown ( $_GET ["vpc_Amount"] );
		$orderInfo = $this->null2unknown ( $_GET ["vpc_OrderInfo"] );
		$txnResponseCode = $this->null2unknown ( $_GET ["vpc_TxnResponseCode"] );

		// This is the display title for 'Receipt' page
		//$title = $_GET ["Title"];


		// This method uses the QSI Response code retrieved from the Digital
		// Receipt and returns an appropriate description for the QSI Response Code
		//
		// @param $responseCode String containing the QSI Response Code
		//
		// @return String containing the appropriate description
		//

		//  ----------------------------------------------------------------------------

		$transStatus = "";
		if($hashValidated=="CORRECT" && $txnResponseCode=="0"){
			$transStatus = '<h1 class = "entry-title" style="color:green;">Payment was successful</h1>';
			// Mark as on-hold (we're awaiting the cheque)
			//$order->update_status( 'onepayVN', __( 'Payment complete', 'woocommerce' ) ,'Payment complete');
			$order->add_order_note( __( 'Payment completed', 'woocommerce' ) );
			// Reduce stock levels
			//$order->reduce_order_stock();
			// Remove cart
			$order->payment_complete();
			//$order->update_status( 'completed' );
			WC()->cart->empty_cart();
		}elseif ($txnResponseCode!="0"){
			$tranDesc = $this->getResponseDescription($txnResponseCode);
			$transStatus = '<h1 class = "entry-title" style="color:red;">Payment was fail-'.$tranDesc.'</h1>';			
			$order->update_status( 'failed' );
			$order->add_order_note( __( 'Payment failed-'.$tranDesc, 'woocommerce' ) );
		}elseif ($hashValidated=="INVALID HASH"){
			$transStatus = '<h1 class = "entry-title" style="color:red;">Payment Pending</h1>';
			$order->add_order_note( __( 'Payment pending', 'woocommerce' ) );
		}
        print $transStatus;
      

    }

    function generate_onepayUS_url( $order_id ){
      // This is from the class provided by OnePAY. Not advisable to mess.
      global $woocommerce;


      $order = new WC_Order( $order_id );

      $order_items = $order->get_items();

	  $return_url = $this->get_return_url( $order );
	  /////////////////////////////////////////////////////////////

      $amount = $order->get_total();
	  $oder_info = $order_id;
	  $checkouturl = $this->buildCheckoutUrl($amount, $oder_info, $return_url);


      return $checkouturl;
    }

    function showMessage($content){
            return '<div class="box '.$this->msg['class'].'-box">'.$this->msg['message'].'</div>'.$content;
        }




  public function buildCheckoutUrl($amount, $oder_info, $return_url)
  {

	///////////////////////////////////////////////////////////////////////
	////////////// ONEPAY CODE /////////////////////
	// *********************
	// START OF MAIN PROGRAM
	// *********************
	$url_return = $return_url; // Fixed - url return
	/////////////////////////////////////////////////////////////////////////////

	///////////////////////// URL payment ////////////////////////////////
	$vpcURL = $this->settings['onepay_url']; // Test mode
	//$vpcURL = "http://mtf.onepay.vn/vpcpay/vpcpay.op"; // Live mode
	//////////////////////// OnePAY ACC //////////////////////////////////
	$Merchant_ID = $this->settings['merchant_id']; // Fixed, provide by OnePAY
	$Access_Code=	$this->settings['merchant_access_code']; // Fixed, provide by OnePAY
	$SECURE_SECRET = $this->settings['secure_secret']; // Fixed, provide by OnePAY
	///////////////////////////////////////////////////////////////////////
	$lang=get_bloginfo("language"); // get current language of website
	//print $lang;exit;
	if ($lang == "vi") {
		$vpc_Locale = "vn";
	} else $vpc_Locale = "en";
	// Define Constants
	// ----------------
	// This is secret for encoding the MD5 hash
	// This secret will vary from merchant to merchant
	// To not create a secure hash, let SECURE_SECRET be an empty string - ""
	// $SECURE_SECRET = "secure-hash-secret";
	// Khóa bí mật - được cấp bởi OnePAY
	$op_var = array(
				'AgainLink'				=>	'onepay.vn',
				'Title'					=>	'onepay.vn',
				'vpc_Locale'			=>	$vpc_Locale,//ngôn ngữ hiển thị trên cổng thanh toán
				'vpc_Version'			=>	'2',//Phiên bản modul
				'vpc_Command'			=>	'pay',//tên hàm
				//'vpc_Currency'			=>  'VND',
				'vpc_Merchant'			=>	$Merchant_ID,//mã đơn vị(OP cung cấp)
				'vpc_AccessCode'		=>	$Access_Code,//mã truy nhập cổng thanh toán (OP cung cấp)
				'vpc_MerchTxnRef'		=>	date ( 'YmdHis' ) . rand (),//ID giao dịch (duy nhất)
				'vpc_OrderInfo'			=>	$oder_info,//mã đơn hàng
				'vpc_Amount'			=>	$amount*100,//số tiền thanh toán
				'vpc_ReturnURL'			=>	$url_return,	//url nhận kết quả trả về từ OnePAY
				'vpc_TicketNo'			=>	$_SERVER["REMOTE_ADDR"]//ip khách hàng
			);
	// add the start of the vpcURL querystring parameters
	// *****************************Lấy giá trị url cổng thanh toán*****************************

	$vpcURL .= "?";
	// Remove the Virtual Payment Client URL from the parameter hash as we
	// do not want to send these fields to the Virtual Payment Client.
	// bỏ giá trị url và nút submit ra khỏi mảng dữ liệu
	//unset($arr_variables["virtualPaymentClientURL"]);
	//unset($arr_variables["SubButL"]);

	//$stringHashData = $SECURE_SECRET; *****************************Khởi tạo chuỗi dữ liệu mã hóa trống*****************************
	$stringHashData = "";
	// sắp xếp dữ liệu theo thứ tự a-z trước khi nối lại
	// arrange array data a-z before make a hash

	ksort ($op_var);

	// set a parameter to show the first pair in the URL
	// đặt tham số đếm = 0
	$appendAmp = 0;


	foreach($op_var as $key => $value) {

		// create the md5 input and URL leaving out any fields that have no value
		// tạo chuỗi đầu dữ liệu những tham số có dữ liệu
		if (strlen($value) > 0) {
			// this ensures the first paramter of the URL is preceded by the '?' char
			if ($appendAmp == 0) {
				$vpcURL .= urlencode($key).'='.urlencode($value);
				$appendAmp = 1;
			} else {
				$vpcURL .= '&'.urlencode($key) . "=".urlencode($value);
			}
			//$stringHashData .= $value; *****************************sử dụng cả tên và giá trị tham số để mã hóa*****************************
			if ((strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
				$stringHashData .= $key . "=" . $value . "&";

			}
		}
	}

	//*****************************xóa ký tự & ở thừa ở cuối chuỗi dữ liệu mã hóa*****************************
	$stringHashData = rtrim($stringHashData, "&");

	//print_r($stringHashData);
	// Create the secure hash and append it to the Virtual Payment Client Data if
	// the merchant secret has been provided.
	// thêm giá trị chuỗi mã hóa dữ liệu được tạo ra ở trên vào cuối url
	if (strlen($SECURE_SECRET) > 0) {
		//$vpcURL .= "&vpc_SecureHash=" . strtoupper(md5($stringHashData));
		// *****************************Thay hàm mã hóa dữ liệu*****************************
		$vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*',$SECURE_SECRET)));
	}
	return $vpcURL;
  }

  function null2unknown($data) {
		if ($data == "") {
			return "No Value Returned";
		} else {
			return $data;
		}
  }
  
  // @return String containing the appropriate description
	//
	function getResponseDescription($responseCode)
	{

		switch ($responseCode) {
			case "0" :
				$result = "Giao dịch thành công";
				break;
			case "?" :
				$result = "Transaction status is unknown";
				break;
			case "1" :
				$result = "Giao dịch không thành công,Ngân hàng phát hành thẻ không cấp phép cho giao dịch hoặc thẻ chưa được kích hoạt dịch vụ thanh toán trên Internet. Vui lòng liên hệ ngân hàng theo số điện thoại sau mặt thẻ được hỗ trợ chi tiết.";
				break;
			case "2" :
				$result = "Giao dịch không thành công,Ngân hàng phát hành thẻ từ chối cấp phép cho giao dịch. Vui lòng liên hệ ngân hàng theo số điện thoại sau mặt thẻ để biết chính xác nguyên nhân Ngân hàng từ chối.";
				break;
			case "3" :
				$result = "Giao dịch không thành công, Cổng thanh toán không nhận được kết quả trả về từ ngân hàng phát hành thẻ. Vui lòng liên hệ với ngân hàng theo số điện thoại sau mặt thẻ để biết chính xác trạng thái giao dịch và thực hiện thanh toán lại ";
				break;
			case "4" :
				$result = "Giao dịch không thành công do thẻ hết hạn sử dụng hoặc nhập sai thông tin tháng/ năm hết hạn của thẻ. Vui lòng kiểm tra lại thông tin và thanh toán lại";
				break;
			case "5" :
				$result = "Giao dịch không thành công,Thẻ không đủ hạn mức hoặc tài khoản không đủ số dư để thanh toán. Vui lòng kiểm tra lại thông tin và thanh toán lại";
				break;
			case "6" :
				$result = "Giao dịch không thành công, Quá trình xử lý giao dịch phát sinh lỗi từ ngân hàng phát hành thẻ. Vui lòng liên hệ ngân hàng theo số điện thoại sau mặt thẻ được hỗ trợ chi tiết.";
				break;
			case "7" :
				$result = "Giao dịch không thành công,Đã có lỗi phát sinh trong quá trình xử lý giao dịch. Vui lòng thực hiện thanh toán lại.";
				break;
			case "8" :
				$result = "Giao dịch không thành công. Số thẻ không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại ";
				break;
			case "9" :
				$result = "Giao dịch không thành công. Tên chủ thẻ không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại";
				break;
			case "10" :
				$result = "Giao dịch không thành công. Thẻ hết hạn/Thẻ bị khóa. Vui lòng kiểm tra và thực hiện thanh toán lại ";
				break;
			case "11" :
				$result = "Giao dịch không thành công. Thẻ chưa đăng ký sử dụng dịch vụ thanh toán trên Internet. Vui lòng liên hê ngân hàng theo số điện thoại sau mặt thẻ để được hỗ trợ";
				break;
			case "12" :
				$result = "Giao dịch không thành công. Ngày phát hành/Hết hạn không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại";
				break;
			case "13" :
				$result = "Giao dịch không thành công. thẻ/ tài khoản đã vượt quá hạn mức thanh toán. Vui lòng kiểm tra và thực hiện thanh toán lại ";
				break;				
			case "21" :
				$result = "Giao dịch không thành công. Số tiền không đủ để thanh toán. Vui lòng kiểm tra và thực hiện thanh toán lại";
				break;
			case "22" :
				$result = "Giao dịch không thành công. Thông tin tài khoản không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại ";
				break;
			case "23" :
				$result = "Giao dịch không thành công. Tài khoản bị khóa.Vui lòng liên hê ngân hàng theo số điện thoại sau mặt thẻ để được hỗ trợ";
				break;				
			case "24" :
				$result = "Giao dịch không thành công. Thông tin thẻ không đúng. Vui lòng kiểm tra và thực hiện thanh toán lại";
				break;
			case "25" :
				$result = "Giao dịch không thành công. OTP không đúng.Vui lòng kiểm tra và thực hiện thanh toán lại ";
				break;
			case "253" :
				$result = "Giao dịch không thành công. Quá thời gian thanh toán. Vui lòng thực hiện thanh toán lại";
				break;
			case "99" :
				$result = "Giao dịch không thành công. Người sử dụng hủy giao dịch";
				break;
			case "B" :
				$result = "Giao dịch không thành công do không xác thực được 3D-Secure. Vui lòng liên hệ ngân hàng theo số điện thoại sau mặt thẻ được hỗ trợ chi tiết.";
				break;
			case "E" :
				$result = "Giao dịch không thành công do nhập sai CSC (Card Security Card) hoặc ngân hàng từ chối cấp phép cho giao dịch. Vui lòng liên hệ ngân hàng theo số điện thoại sau mặt thẻ được hỗ trợ chi tiết.";
				break;
			case "F" :
				$result = "Giao dịch không thành công do không xác thực được 3D-Secure. Vui lòng liên hệ ngân hàng theo số điện thoại sau mặt thẻ được hỗ trợ chi tiết";
				break;
			case "Z" :
				$result = "Giao dịch của bạn bị từ chối. Vui lòng liên hệ Đơn vị chấp nhận thẻ để được hỗ trợ.";
				break;
			default  :
				$result = "Payment fail";
		}
		return $result;
	}

}


  function woocommerce_add_onepayUS_gateway($methods) {
      $methods[] = 'WC_onepayUS';
      return $methods;
  }

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_onepayUS_gateway' );
}
