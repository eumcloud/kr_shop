<?
	// 취소 서버 아이피
	$SERVER_IP = $row_setup[P_MODE] == "test" ? "27.102.213.205" : "27.102.213.207";

	define( "ENCKEY"	,$row_setup[P_PG_ENC_KEY] );	// 암호화 키값	
	define( "SERVER_IP"	,$SERVER_IP ); 				// 다우페이 결제취소 서버
	define( "CARD_PORT"	,64001 );					// 신용카드 포트
	define( "BANK_PORT"	,46001 );					// 계좌이체 포트
	define( "TIMEOUT"	,10 );	

	// 카드정보
	//$ocl = _MQ("select oc_tid from odtOrderCardlog where oc_oordernum = '".$_ordernum."' order by oc_uid desc limit 1");
	$ocl = _MQ("select oc_tid from odtOrderCardlog where oc_oordernum = '".$_ordernum."' and oc_tid != '' order by oc_uid desc limit 1"); // 2016-11-15 간혹 주문완료페이지 back키 입력으로 잘못된데이터가 추가되는경우 발생 수정 SSJ

	// 주문정보
	$r = get_order_info($_ordernum);

	if($r[paymethod] == "V") {		// 가상계좌는 다우페이 취소연동이 되지 않는다. 직접 환불처리해야한다.

		$is_pg_status = true;

	} else if($r[paymethod] == "C") {	// 카드결제

		require_once(PG_DIR. "/daupay/library/Card_library.php");

		$CPID				= $row_setup[P_ID];
		$DAOUTRX			= $ocl[oc_tid];
		$AMOUNT				= $r[tPrice];
		$IPADDRESS			= $r[SERVER_ADDR];
		$CANCELMEMO			= "관리자모드 취소";

		CardCancel(  SERVER_IP, CARD_PORT, $CPID, ENCKEY, TIMEOUT );

		// 카드거래번호 , 결과 메세지
		card_cancle_log_write($DAOUTRX,"resultcode:".$res_resultcode."|".iconv("euckr","utf8",$res_errormessage));

		// 결과코드 0000 : 성공	그외 : 실패
		$is_pg_status = $res_resultcode == "0000" ? true : false;

	} else if($r[paymethod] == "L") {	// 계좌이체

		require_once(PG_DIR. "/daupay/library/Bank_library.php");

		$CPID				= $row_setup[P_ID];
		$DAOUTRX			= $ocl[oc_tid];
		$AMOUNT				= $r[tPrice];
		$CANCELMEMO			= "관리자모드 취소";

		BankCancel(  SERVER_IP, BANK_PORT, $CPID, ENCKEY, TIMEOUT );

		// 카드거래번호 , 결과 메세지
		card_cancle_log_write($DAOUTRX,"resultcode:".$res_resultcode."|".iconv("euckr","utf8",$res_errormessage));

		// 결과코드 0000 : 성공	그외 : 실패
		$is_pg_status = $res_resultcode == "0000" ? true : false;

	}

?>