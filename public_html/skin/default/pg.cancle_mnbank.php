<?PHP

	require_once dirname(__FILE__).'/mnbank/Encryptor.class.php';

	$ordr = _MQ("SELECT * FROM odtOrder WHERE ordernum='" . $_ordernum . "'");

	$mid = $row_setup[P_ID];	//상점id
    $merchantKey = $row_setup[P_PW];	//상점키
   	$cancelAmt = $ordr[tPrice];	 //결제금액
	$moid = $_ordernum;//주문번호
   	
    //$ediDate, $mid, $merchantKey, $cancelAmt    
	$encryptor = new Encryptor($merchantKey);
	$encryptData = $encryptor->encData($cancelAmt.$mid.$moid);
	$ediDate = $encryptor->getEdiDate();	

	$url = "https://webtx.mnbank.co.kr/payCancel";

	$data = "mid=" . $mid . "&";
	$data .= "tid=" . $ordr[authum] . "&";//
	$data .= "moid=" . $moid . "&";//주문번호
	$data .= "cancelPw=" . $P_KEY . "&";//취소비밀번호
	$data .= "cancelAmt=" . $cancelAmt . "&";//취소비용
	$data .= "cancelMsg=고객요청&";//취소사유
	$data .= "partialCancelCode=0&";//부분취소 여부 (0:전체, 1:부분)
	$data .= "returnUrl=". urlencode("http://" . $_SERVER["HTTP_HOST"] . "/pages/mnbank/cancelReturnUrl.php") ."&";//returnUrl
	$data .= "encryptData=" . $encryptData . "&";//encryptData
	$data .= "ediDate=" . $ediDate . "&";//ediDate
	$data .= "cc_ip=" . $_SERVER['REMOTE_ADDR'] . "&";//아이피

	$data_return = CurlPostExec( $url , $data , 1500); // 대기상태 1.5초 적용

	$ex1 = explode("input " , str_replace("'" , "" , $data_return));
	$app_return = "";
	foreach($ex1 as $k=>$v){
		if(preg_match("/resultCd/i" , $v)) {
			$ex2 = explode("value=" , $v );
			$ex3 = explode(">" , $ex2[1] );
			$app_return = trim($ex3[0]);
		}
	}
	

	// 2001 : 취소성공
	$is_pg_status = ( preg_match("/2001/i" , $app_return) ? true : false );

//ViewArr($data_return);
//exit;
?>