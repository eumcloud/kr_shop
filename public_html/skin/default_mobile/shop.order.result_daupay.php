<?

/*
	//PC버전과 동일하게 동작한다.
	include dirname(__FILE__)."/../../pages/shop.order.result_daupay.php";
	return;
*/

	$CPID			= $row_setup[P_ID];							// 상점ID
	$ORDERNO		= $ordernum;								// 주문번호
	$AMOUNT			= $r[tPrice];								// 결제금액
	$PRODUCTNAME	= $app_product_name; 						// 상품명
	$EMAIL			= $r[orderemail];							// 구매자 이메일
	$USERNAME		= $r[ordername];							// 구매자 명
	$ISTEST			= $row_setup[P_MODE] == "test" ? 1 : 0;		// 테스트결제 여부
	$CASHRECEIPTFLAG= $r[taxorder] == "Y" ? 1 : 0;				// 현금영수증 발형여부
	$ISESCROW		= $row_setup[P_SKBN] == "1" ? 1 : 0;			// 에스크로 사용여부 (계좌이체, 가상계좌만 적용) (1:사용 , 0:미사용)
	$HOMEURL		= "http://".$_SERVER[HTTP_HOST]."/m/shop.order.result_daupay_return.php?ordernum=".$ordernum;	// 리턴URL
	$RETURNURL; // 리턴 URL은 opener창으로 값을 던지지 않고 새창으로 처리하므로, HOMEURL에서 직접 컨트롤 한다. 32.05.07 오찬식
	$ISMOBILE		= is_mobile() ? 1 : 0;

	// 상품구분 - 다우페이측에 아이디를 생성할때 신청한 상품구분값을 넣는다. (1:디지털, 2:실물)
	$PRODUCTTYPE	= $row_setup[P_PG_PRO_TYPE];

	// 에스크로적용시 실물로 강제 적용한다. (계좌이체, 가상계좌만)
	$PRODUCTTYPE = preg_match("/V|L/",$r[paymethod]) && $ISESCROW == 1 ? 2 : $PRODUCTTYPE;

	// 결제방식
	if($r[paymethod] == "C")		$PAYMETHOD = "card";
	else if($r[paymethod] == "V")	$PAYMETHOD = "virtual";
	else error_alt("결제수단 선택오류입니다. 관리자에게 문의하세요");

?>

<script type="text/javascript">
function fnSubmit() {
	var fileName,fileName_mobile;
	var paymethod = $("input[name=PAYMETHOD]").val();
	var subdomain = $("input[name=ISTEST]").val() == "1" ? "ssltest" : "ssl";
	var ismobile = $("input[name=RESERVEDINDEX1]").val() == "1" ? 1 : 0;
	var isescrow = $("input[name=ISESCROW]").val() == "1" ? 1 : 0;

	if(paymethod == "card") {
		fileName_mobile 	= "https://"+subdomain+".kiwoompay.co.kr/m/card_webview/DaouCardMng.jsp";
	}else if(paymethod == "virtual") {
		fileName_mobile 	= "http://"+subdomain+".kiwoompay.co.kr/m/vaccount_webview/DaouVaccountMng.jsp";
	}
	pf = document.order_info;
	pf.action = fileName_mobile;
	pf.method = "post";
	pf.submit();
}
</script>

<form name="order_info" accept-charset="euc-kr"> 
	<input type="hidden" name="CPID"  value="<?=$CPID?>">
	<input type="hidden" name="ORDERNO" value="<?=$ORDERNO?>">
	<input type="hidden" name="AMOUNT" value="<?=$AMOUNT?>">
	<input type="hidden" name="PRODUCTNAME" value="<?=$PRODUCTNAME?>">
	<input type="hidden" name="PRODUCTTYPE" value="<?=$PRODUCTTYPE?>">
	<input type="hidden" name="BILLTYPE" value="1"> <!-- 과금유형 1로 고정 -->
	<input type="hidden" name="EMAIL" value="<?=$EMAIL?>">
	<input type="hidden" name="USERNAME" value="<?=$USERNAME?>">

	<input type="hidden" name="HOMEURL" value="<?=$HOMEURL?>">
	<input type="hidden" name="FAILURL" value="http://<?php echo $_SERVER[HTTP_HOST].'/?pn=shop.order.result'; ?>">
	<input type="hidden" name="CLOSEURL" value="http://<?php echo $_SERVER[HTTP_HOST].'/?pn=shop.order.result'; ?>">
	<input type="hidden" name="RETURNURL" value="<?=$RETURNURL?>">

	<input type="hidden" name="TAXFREECD" value="00"> <!-- 과세 00, 비과세 01 -->
	<input type="hidden" name="CASHRECEIPTFLAG" value="<?=$CASHRECEIPTFLAG?>"> <!-- 현금영수증 발행여부 -->
	<input type="hidden" name="PAYMETHOD" value="<?=$PAYMETHOD?>"> <!-- 결제수단 -->
	<input type="hidden" name="ISTEST" value="<?=$ISTEST?>"> <!-- 테스트 여부 -->
	<input type="hidden" name="ISESCROW" value="<?=$ISESCROW?>"> <!-- 테스트 여부 -->
	<input type="hidden" name="RESERVEDINDEX1" value="<?=$ISMOBILE?>"> <!-- 모바일여부 -->
</form>