<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to handle the domestic OnePay gateway https://mtf.onepay.vn/developer/?page=modul_noidia
 *
 *
 * @author   htdat
 * @since    1.5.0
 *
 */

require_once('onepay/abstract-payment.php');
class WooViet_OnePay_Domestic extends WooViet_OnePay_Abstract {

	public function configure_payment() {
		$this->method_title       = __( 'OnePay Domestic Gateway (by Woo Viet)', 'woo-viet' );
		$this->method_description = __( 'OnePay supports all major bank ATMs in Vietnam.', 'woo-viet' );
	}

	public function get_onepay_payment_link( $testmode ) {
		//return $testmode ? 'https://mtf.onepay.vn/onecomm-pay/vpc.op' : 'https://onepay.vn/onecomm-pay/vpc.op';
		return $testmode ? 'https://mtf.onepay.vn/paygate/vpcpay.op' : 'https://onepay.vn/paygate/vpcpay.op';
	}

	public function get_onepay_querydr_link( $testmode ) {
		//return $testmode ? 'https://mtf.onepay.vn/onecomm-pay/Vpcdps.op' : 'https://onepay.vn/onecomm-pay/Vpcdps.op';
	}

	public function OnePay_getResponseDescription( $responseCode ) {
		switch ( $responseCode ) 
		{
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

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = include( 'onepay/domestic-settings.php' );
	}
}