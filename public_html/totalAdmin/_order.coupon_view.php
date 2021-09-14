<?PHP

	include "inc.php";

	$_ordernum = $ordernum;

	$_type = "coupon"; // 쿠폰발송

	include("../pages/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
	
	$_title = "[".$row_setup[site_name]."] 주문하신 상품의 쿠폰이 발송되었습니다.";
	$_title_content = '
	<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">고객님</strong>께서 주문하신 쿠폰이 발송되었습니다. <br />
	저희 사이트에서 구매해주셔서 감사합니다. 보다 나은 상품과 큰 만족을 위해 최선을 다하겠습니다.
	';
	$_content = $mailing_app_content;
	$_content = get_mail_content($_title, $_title_content, $_content);

	echo $_content ;

?>