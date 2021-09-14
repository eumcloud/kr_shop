<?
	require_once dirname(__FILE__).'/Encryptor.class.php';
	
	//webTx에서 받은 결과값들
	$payMethod = $_POST['payMethod'];
	$mid = $_POST['mid'];
	$tid = $_POST['tid'];
	$mallUserId = $_POST['mallUserId'];
	$amt = $_POST['amt'];
	$buyerName = $_POST['buyerName'];
	$buyerTel = $_POST['buyerTel'];
	$buyerEmail = $_POST['buyerEmail'];
	$mallReserved = $_POST['mallReserved'];
	$goodsName = $_POST['goodsName'];
	$moid = $_POST['moid'];
	$authDate = $_POST['authDate'];
	$authCode = $_POST['authCode'];
	$fnCd = $_POST['fnCd'];
	$fnName = $_POST['fnName'];
	$resultCd = $_POST['resultCd'];
	$resultMsg = $_POST['resultMsg'];
	$errorCd = $_POST['errorCd'];
	$errorMsg = $_POST['errorMsg'];
	$vbankNum = $_POST['vbankNum'];
	$vbankExpDate = $_POST['vbankExpDate'];
	$ediDate = $_POST['ediDate'];
	
	//회원사 DB에 저장되어있던 값
	$amtDb = "";//금액
	$moidDb = "";//moid
	$mKey = "";//상점키
	
	$encryptor = new Encryptor($mKey, $ediDate);
	$decAmt = $encryptor->decData($amt);
	$decMoid = $encryptor->decData($moid);
	
	if( $decAmt!=$amtDb || $decMoid!=$moidDb ){
		echo "위변조 데이터를 오류입니다.";
	}else{
		//결제결과 수신 여부 알림
		ResultConfirm::send($tid, "000");
		//DB처리
	}
?>