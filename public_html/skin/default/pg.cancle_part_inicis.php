<?

		//$ocl = _MQ("select oc_tid from odtOrderCardlog where oc_oordernum = '".$_ordernum."' order by oc_uid desc limit 1");
		$ocl = _MQ("select oc_tid from odtOrderCardlog where oc_oordernum = '".$_ordernum."' and oc_tid != '' order by oc_uid desc limit 1"); // 2016-11-15 간혹 주문완료페이지 back키 입력으로 잘못된데이터가 추가되는경우 발생 수정 SSJ
		$tid = $ocl[oc_tid]; // PG사 거래 번호
		$row_setup[P_SID] = $row_setup[P_SID]?$row_setup[P_SID]:$row_setup[P_ID];
    	$_pg_mid = $ordr[paymethod]=='V'?$row_setup[P_SID]:$row_setup[P_ID];

		require_once(PG_DIR."/inicis/libs/INILib.php");

		// 취소할 금액
		//$tmp = _MQ(" select sum( (op_pprice + op_poptionprice) * op_cnt + op_delivery_price + op_add_delivery_price) as sum from odtOrderProduct where op_cancel = 'N' and op_oordernum = '".$_ordernum."' ");
		// -- 2016-09-09 ::: 취소가능잔액 오류 수정 --- SSJ
		$tmp = _MQ(" select sum( (op_pprice + op_poptionprice) * op_cnt + op_delivery_price + op_add_delivery_price) as sum from odtOrderProduct where IF(op_cancel_type = 'pg' , op_cancel != 'Y' , 1 )  and op_oordernum = '".$_ordernum."' ");
		// -- 2016-09-09 ::: 취소가능잔액 오류 수정 --- SSJ
		$tmp2 = _MQ(" select sum(op_usepoint) as sum from odtOrderProduct where op_oordernum = '".$_ordernum."' and op_cancel != 'Y' ");
		$_cancel_price = trim($_total_amount);
		$_confirm_price = ($tmp[sum] - $tmp2[sum] - $_cancel_price) > 0 ? ($tmp[sum] - $tmp2[sum] - $_cancel_price ) : 0;

		$inipay = new INIpay50;

		$inipay->SetField("inipayhome", PG_DIR."/inicis"); // 이니페이 홈디렉터리(상점수정 필요)
		$inipay->SetField("type", "repay");      // 고정 (절대 수정 불가)
		$inipay->SetField("pgid", "INIphpRPAY");      // 고정 (절대 수정 불가)
		$inipay->SetField("subpgip","203.238.3.10"); 				// 고정
		$inipay->SetField("debug", false);        // 로그모드("true"로 설정하면 상세로그가 생성됨.)
		$inipay->SetField("mid", $_pg_mid);                         // 상점아이디
		$inipay->SetField("admin", "1111");         //비대칭 사용키 키패스워드
		$inipay->SetField("oldtid", $tid);            // 취소할 거래의 거래아이디
		$inipay->SetField("currency", 'WON');     // 화폐단위
		$inipay->SetField("price", $_cancel_price);      //취소금액
		$inipay->SetField("confirm_price", $_confirm_price);      //승인요청금액

		$inipay->startAction();

		// 취소 성공 여부
		$is_pg_status = $inipay->getResult('ResultCode') == "00" ? true : false;

		// 취소결과 로그 기록
		card_cancle_log_write($tid,iconv("euckr","utf8",$inipay->getResult('ResultMsg')));	// 카드거래번호 , 결과 메세지
?>