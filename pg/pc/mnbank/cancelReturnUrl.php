<?
	require_once dirname(__FILE__).'/Encryptor.class.php';
	
	$payMethod = $_POST['payMethod'];
	$ediDate = $_POST['ediDate'];
	$returnUrl = $_POST['returnUrl'];
	$resultMsg = $_POST['resultMsg'];
	$cancelDate = $_POST['cancelDate'];
	$cancelTime = $_POST['cancelTime'];
	$resultCd = $_POST['resultCd'];
	$cancelNum = $_POST['cancelNum'];
	$cancelAmt = $_POST['cancelAmt'];
	$moid = $_POST['moid'];
	
	$mid = "mnbank001m";	//상점id
    $merchantKey = "zutht7y2mL0DQWk7mkY2Jt+2B7hxqRBtnQ0tK0nl3ZhfztnX5sXSyApEatooQODfz5wNa7DTxzogjWqbxLfa6Q==";	//상점키
		
	$encryptor = new Encryptor($merchantKey, $ediDate);
	$decAmt = $encryptor->decData($cancelAmt);
	$decMoid = $encryptor->decData($moid);
	
	echo "payMethod : ".$payMethod."<br>";
	echo "encryptData : ".$encryptData."<br>";
	echo "returnUrl : ".$returnUrl."<br>";
	echo "resultMsg : ".$resultMsg."<br>";
	echo "cancelDate : ".$cancelDate."<br>";
	echo "cancelTime : ".$cancelTime."<br>";
	echo "resultCd : ".$resultCd."<br>";
	echo "cancelNum : ".$cancelNum."<br>";
	echo "cancelAmt : ".$cancelAmt."<br>";
	echo "decryptData : ".$decryptData."<br>";
	
	if($decAmt!="상점요청금액" || $decMoid!="상점요청moid"){
		echo "위변조 데이터를 오류입니다.";
		exit;
	}
	
	//취소처리
	
?>