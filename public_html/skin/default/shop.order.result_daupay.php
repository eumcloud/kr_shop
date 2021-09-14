<?

	$CPID			= $row_setup[P_ID];							// 상점ID
	$ORDERNO		= $ordernum;								// 주문번호
	$AMOUNT			= $r[tPrice];								// 결제금액
	$PRODUCTNAME	= $app_product_name; 						// 상품명
	$EMAIL			= $r[orderemail];							// 구매자 이메일
	$USERNAME		= $r[ordername];							// 구매자 명
	$ISTEST			= $row_setup[P_MODE] == "test" ? 1 : 0;		// 테스트결제 여부
	$CASHRECEIPTFLAG= $r[taxorder] == "Y" ? 1 : 0;				// 현금영수증 발형여부
	$ISESCROW		= $row_setup[P_SKBN] == "1" ? 1 : 0;			// 에스크로 사용여부 (계좌이체, 가상계좌만 적용) (1:사용 , 0:미사용)
	$HOMEURL		= "http://".$_SERVER[HTTP_HOST]."/pages/shop.order.result_daupay_return.php?ordernum=".$ordernum;	// 리턴URL
	$RETURNURL; // 리턴 URL은 opener창으로 값을 던지지 않고 새창으로 처리하므로, HOMEURL에서 직접 컨트롤 한다. 32.05.07 오찬식
	$ISMOBILE		= is_mobile() ? 1 : 0;

	// 상품구분 - 다우페이측에 아이디를 생성할때 신청한 상품구분값을 넣는다. (1:디지털, 2:실물)
	$PRODUCTTYPE	= $row_setup[P_PG_PRO_TYPE];

	// 에스크로적용시 실물로 강제 적용한다. (계좌이체, 가상계좌만)
	$PRODUCTTYPE = preg_match("/V|L/",$r[paymethod]) && $ISESCROW == 1 ? 2 : $PRODUCTTYPE;

	// 결제방식
	if($r[paymethod] == "C")		$PAYMETHOD = "card";
	else if($r[paymethod] == "L")	$PAYMETHOD = "iche";
	else if($r[paymethod] == "V")	$PAYMETHOD = "virtual";
	else error_alt("결제수단 선택오류입니다. 관리자에게 문의하세요");

	// 결제방식 마다 팝업창의 사이즈가 다르다.
	$popup_size		= array(
								"card" => "width=579,height=527",
								"iche" => "width=480,height=480",
								"virtual" => "width=468,height=538",
							);
?>

<script type="text/javascript">
// 다우페이에서 utf8을 지원하지 않고, 또, 무슨이유에선지 인코딩을 해도 한글이 깨지는 문제로 인해, 팝업창을 직접 띄워서 결제페이지로 넘긴다.
function fnSubmit() {
  if (<?=$ISMOBILE?>){
	window.open("/pages/shop.order.result_daupay_popup.php", "DAOUPAY", "fullscreen");
  }else{
	window.open("/pages/shop.order.result_daupay_popup.php", "DAOUPAY", "<?=$popup_size[$PAYMETHOD]?>");
  }
}
</script>

<form name="order_info"> 
	<input type="hidden" name="CPID"  value="<?=$CPID?>">
	<input type="hidden" name="ORDERNO" value="<?=$ORDERNO?>">
	<input type="hidden" name="AMOUNT" value="<?=$AMOUNT?>">
	<input type="hidden" name="PRODUCTNAME" value="<?=$PRODUCTNAME?>">
	<input type="hidden" name="PRODUCTTYPE" value="<?=$PRODUCTTYPE?>">
	<input type="hidden" name="BILLTYPE" value="1"> <!-- 과금유형 1로 고정 -->
	<input type="hidden" name="EMAIL" value="<?=$EMAIL?>">
	<input type="hidden" name="USERNAME" value="<?=$USERNAME?>">
	<input type="hidden" name="RETURNURL" value="<?=$RETURNURL?>">
	<input type="hidden" name="HOMEURL" value="<?=$HOMEURL?>">
	<input type="hidden" name="TAXFREECD" value="00"> <!-- 과세 00, 비과세 01 -->
	<input type="hidden" name="CASHRECEIPTFLAG" value="<?=$CASHRECEIPTFLAG?>"> <!-- 현금영수증 발행여부 -->
	<input type="hidden" name="PAYMETHOD" value="<?=$PAYMETHOD?>"> <!-- 결제수단 -->
	<input type="hidden" name="ISTEST" value="<?=$ISTEST?>"> <!-- 테스트 여부 -->
	<input type="hidden" name="ISESCROW" value="<?=$ISESCROW?>"> <!-- 테스트 여부 -->
	<input type="hidden" name="RESERVEDINDEX1" value="<?=$ISMOBILE?>"> <!-- 모바일여부 -->
</form>