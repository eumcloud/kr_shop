<?PHP

	require_once $_SERVER["DOCUMENT_ROOT"] . '/../pg/m/mnbank/Encryptor.class.php';

	$mid = $row_setup[P_ID];	//상점id
    $merchantKey = $row_setup[P_PW];	//상점키
   	$amt = $r[tPrice];	 //결제금액
	$moid = $ordernum;//주문번호

    //$ediDate, $mid, $merchantKey, $amt    
	$encryptor = new Encryptor($merchantKey);

	$encryptData = $encryptor->encData($amt.$mid.$moid);
	$ediDate = $encryptor->getEdiDate();	
    $vbankExpDate = $encryptor->getVBankExpDate();	

	$payActionUrl = "https://mtx.mnbank.co.kr";
	$payLocalUrl = "http://" . $_SERVER["HTTP_HOST"];

?>
<script type="text/javascript">
	function mn_submit(){
		frm = document.transMgr;
		if(frm.transType.value){
			if(frm.payMethod.value != "CARD" && frm.payMethod.value != "BANK" && frm.payMethod.value != "VBANK"){
				alert("에스크로에서 지원하지 않는 결제수단입니다.");
				return;
			}else{
				frm.submit();
			}
		}else{
			frm.submit();
		}
	}
</script>



<?PHP
		if($r[paymethod] == "C") $gopaymethod = "CARD";
		if($r[paymethod] == "L") $gopaymethod = "BANK";
		if($r[paymethod] == "V") $gopaymethod = "VBANK";
?>
<form id="transMgr" name="transMgr" method="post" action="<?=$payActionUrl ?>/webTxInit" >

	<input type="hidden" name="payMethod" value="<?=$gopaymethod?>"><!-- 결제수단 -->
	<input type="hidden" name="transType" value="<?=($gopaymethod == "VBANK" ? "1" : "0")?>"><!-- 에스크로여부 -->
	<input type="hidden" name="goodsName" value="<?=$app_product_name?>"><!-- 상품명 -->
	<input type="hidden" name="amt" value="<?=$amt?>"><!-- 상품가격 -->
	<input type="hidden" name="moid" value="<?=$moid?>"><!-- 상품주문번호 -->
	<input type="hidden" name="mid" value="<?=$mid?>"><!-- 회원사아이디 -->

	<input type="hidden" name="returnUrl" value="<?=$payLocalUrl?>/m/shop.order.result_mnbank.pro.php"><!-- 결제결과 전송 URL -->
	<input type="hidden" name="cancelUrl" value="<?=$payLocalUrl?>/m/shop.order.result_mnbank.pro.php"><!-- 취소결과 전송 URL -->

	<input type="hidden" name="mallUserId" value="<?=$r[orderid]?>"><!-- 회원사고객ID -->
	<input type="hidden" name="buyerName" value="<?=$r[ordername]?>"><!-- 구매자명 -->
	<input type="hidden" name="buyerTel" value="<?=$r[orderhtel1].$r[orderhtel2].$r[orderhtel3]?>"><!-- 구매자연락처(-)없이 입력 -->
	<input type="hidden" name="buyerEmail" value="<?=$r[orderemail]?>"><!-- 구매자메일주소 -->
	<input type="hidden" name="buyerAddr" value="<?=$r[recaddress]." ".$r[recaddress1]?>"><!-- 배송지주소 -->
	<input type="hidden" name="buyerPostNo" value="<?=$r[reczip1].$r[reczip2]?>"><!-- 우편번호 -->
	<input type="hidden" name="mallIp" value="<?=$_SERVER['SERVER_ADDR']?>"><!-- Mall IP -->
	<input type="hidden" name="vbankExpDate" value="<?=$vbankExpDate?>"><!-- 가상계좌입금기한 -->
	<input type="hidden" name="rcvrMsg" value=""><!-- 수취인전달메시지 -->
	<input type="hidden" name="prdtExpDate" value=""><!-- 제공기간 -->
	<input type="hidden" name="resultYn" value="Y"><!-- 결제결과 페이지 유무 -->

	<input type="hidden" name="payType" value="1">	
	<input type="hidden" name="ediDate"	value="<?=$ediDate?>">
	<input type="hidden" name="encryptData" value="<?=$encryptData?>">
	<input type="hidden" name="userIp"	value="<?=$_SERVER['REMOTE_ADDR']?>">

	<input type="hidden" name="connType" value="0"><!-- 접속방식 : Web(M-browser) -->
	<input type="hidden" name="appPrefix" value="ibWebTest"><!-- 앱스키마 -->
	<input type="hidden" name="browserType" id="browserType" value="SPG"><!-- SmartPhone Payment Gateway -->
	<input type="hidden" name="mallReserved" value="MallReserved">
</form>