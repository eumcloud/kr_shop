<?

		$tid = $r[oc_tid]; // PG사 거래 번호
		$row_setup[P_SID] = $row_setup[P_SID]?$row_setup[P_SID]:$row_setup[P_ID];
    	$_pg_mid = $ordr[paymethod]=='V'?$row_setup[P_SID]:$row_setup[P_ID];

		require_once(PG_DIR."/inicis/libs/INILib.php");

		$inipay = new INIpay50;

		$inipay->SetField("inipayhome", PG_DIR."/inicis"); // 이니페이 홈디렉터리(상점수정 필요)
		if( $ordr[paymethod] == "V" ) $inipay->SetField("type", "refund"); // 고정 (절대 수정 불가)
		else $inipay->SetField("type", "cancel"); // 고정 (절대 수정 불가)
		$inipay->SetField("debug", false);                             // 로그모드("true"로 설정하면 상세로그가 생성됨.)
		$inipay->SetField("mid", $_pg_mid);                         // 상점아이디
		$inipay->SetField("admin", "1111");                             // 비대칭 사용키 키패스워드
		$inipay->SetField("tid", $tid);                     // 취소할 거래의 거래아이디
		$inipay->SetField("cancelmsg", "normal");                             // 취소사유


		########### 가상계좌일 경우 항목추가 - 2016-07-05 수정 ###########
		if( $ordr[paymethod] == "V" ) {
			if($cancel_bank && $cancel_bank_account && $cancel_bank_name) {

				$inipay->SetField("refundacctnum", $cancel_bank_account);     
				$inipay->SetField("refundbankcode", $cancel_bank);    
				$inipay->SetField("refundacctname", $cancel_bank_name);  
			}
		}
		########### 가상계좌일 경우 항목추가 - 2016-07-05 수정 ###########

		$inipay->startAction();

		// 취소 성공 여부
		$is_pg_status = $inipay->getResult('ResultCode') == "00" ? true : false;

		// 취소결과 로그 기록
		card_cancle_log_write($tid,iconv("euckr","utf8",$inipay->getResult('ResultMsg')));	// 카드거래번호 , 결과 메세지
?>