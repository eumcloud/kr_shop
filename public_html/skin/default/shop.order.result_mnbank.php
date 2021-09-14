<?PHP

	require_once $_SERVER["DOCUMENT_ROOT"] . '/pages/mnbank/Encryptor.class.php';

	$mid = $row_setup[P_ID];	//상점id
    $merchantKey = $row_setup[P_PW];	//상점키
   	$amt = $r[tPrice];	 //결제금액
	$moid = $ordernum;//주문번호

    //$ediDate, $mid, $merchantKey, $amt    
	$encryptor = new Encryptor($merchantKey);

	$encryptData = $encryptor->encData($amt.$mid.$moid);
	$ediDate = $encryptor->getEdiDate();	
    $vbankExpDate = $encryptor->getVBankExpDate();	
        	
	$payActionUrl = "https://webtx.mnbank.co.kr";
	$payLocalUrl = "http://" . $_SERVER["HTTP_HOST"];

?>
<link rel="stylesheet" href="/pages/mnbank/css/nyroModal.mnbank.custom.css" type="text/css" media="screen" />
<script type="text/javascript" src="/pages/mnbank/js/jquery.nyroModal.mnbank.custom.js"></script>
<script type="text/javascript" src="/pages/mnbank/js/client.mnbank.mnwebtx.js" defer="defer" async="async"></script>

<script type="text/javascript">
	function changeAmt(){
		frm = document.transMgr;
		frm.action = "mainPay.jsp";
		frm.target = "_self";
		$('#transMgr').removeClass("nyroModal");
		frm.submit();
	}

	function resultResponse(param){
		$('#resultDiv').append(param.resultCd +":"+param.resultMsg+":"+param.tid);
		//다른 페이지로 파라미터 넘기기
		submitParametersToNextPage(param, "/pages/shop.order.result_mnbank.pro.php");
	}

	function mn_submit(){
		if($('input[name=transType]:checked').val()=='1' && $('#payMethod').val()!='CARD' && $('#payMethod').val()!='BANK' && $('#payMethod').val()!='VBANK' ){
			alert("에스크로에서 지원하지 않는 결제수단입니다.");
			return;
		}
		if($("select[name=payMethod]").val() == ""){
			$('#transMgr').submit();
		}else{
			$('#transMgr').submit();
		}
	}
</script>



<?PHP
		if($r[paymethod] == "C") $gopaymethod = "CARD";
		if($r[paymethod] == "L") $gopaymethod = "BANK";
		if($r[paymethod] == "V") $gopaymethod = "VBANK";
?>
<form id="transMgr" method="post" action="<?=$payActionUrl ?>/webTxInit" class="nyroModal" target="_blank">

	<input type="hidden" name="payMethod" value="<?=$gopaymethod?>"><!-- 결제수단 -->
	<input type="hidden" name="transType" value="<?=($gopaymethod == "VBANK" ? "1" : "0")?>"><!-- 에스크로여부 -->
	<input type="hidden" name="goodsName" value="<?=$app_product_name?>"><!-- 상품명 -->
	<input type="hidden" name="amt" value="<?=$amt?>"><!-- 상품가격 -->
	<input type="hidden" name="moid" value="<?=$moid?>"><!-- 상품주문번호 -->
	<input type="hidden" name="mid" value="<?=$mid?>"><!-- 회원사아이디 -->

	<?/* 결과페이지는 페이지명 변경하면 오류남.. 반드시 iframeResponse.php 로 고정 */?>
	<input type="hidden" name="returnUrl" value="<?=$payLocalUrl?>/pages/mnbank/iframeResponse.php"><!-- 결제결과 전송 URL -->

	<input type="hidden" name="mallUserId" value="<?=$r[orderid]?>"><!-- 회원사고객ID -->
	<input type="hidden" name="buyerName" value="<?=$r[ordername]?>"><!-- 구매자명 -->
	<input type="hidden" name="buyerTel" value="<?=$r[orderhtel1].$r[orderhtel2].$r[orderhtel3]?>"><!-- 구매자연락처(-)없이 입력 -->
	<input type="hidden" name="buyerEmail" value="<?=$r[orderemail]?>"><!-- 구매자메일주소 -->
	<input type="hidden" name="buyerAddr" value="<?=$r[recaddress]." ".$r[recaddress1]?>"><!-- 배송지주소 -->
	<input type="hidden" name="buyerPostNo" value="<?=$r[reczip1].$r[reczip2]?>"><!-- 우편번호 -->
	<input type="hidden" name="mallIp" value="<?=$_SERVER['SERVER_ADDR']?>"><!-- Mall IP -->
	<input type="hidden" name="mallReserved" value=""><!-- 상점예비정보 -->
	<input type="hidden" name="vbankExpDate" value="<?=$vbankExpDate?>"><!-- 가상계좌입금기한 -->
	<input type="hidden" name="rcvrMsg" value=""><!-- 수취인전달메시지 -->
	<input type="hidden" name="prdtExpDate" value=""><!-- 제공기간 -->
	<input type="hidden" name="resultYn" value="Y"><!-- 결제결과 페이지 유무 -->

	<input type="hidden" name="payType" value="1">	
	<input type="hidden" name="ediDate"	value="<?=$ediDate?>">
	<input type="hidden" name="encryptData" value="<?=$encryptData?>">
	<input type="hidden" name="userIp"	value="<?=$_SERVER['REMOTE_ADDR']?>">
</form>