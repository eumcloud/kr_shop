<?PHP

	// *** 결제확인 시 --> 적립금 / 쿠폰 등 적용 ***

	// - 주문정보 추출 ---
	$osr = get_order_info($_ordernum);

	// 주문에 사용된 쿠폰정보 추출.
	$coup_log_info = _MQ("select cl_coNo from odtOrderCouponLog where cl_type = 'member' and cl_oordernum='".$osr[ordernum]."'");

//	if( $osr[apply_point] == "N" ) {
	if( $osr['member_type']=='member' ) {

		// 관리자 승인시 무시(관리자 동작+무통장) 2015-11-04 LDD - 무통장 쿠폰 패치
		if( in_array( $_mode , array("modify" , "select_paystatus")) && $osr[paymethod] == "B") { return; }
		// 관리자 승인시 무시(관리자 동작+무통장) 2015-11-04 LDD - 무통장 쿠폰 패치

		// 무통장/적립금결제 주문시, 회원 적립금와 쿠폰이 유효한지 검증한다.
		if($osr[paymethod] == "B" || $osr[paymethod] == "G") {
			// 적립금
			$ind_info = _MQ("select point from odtMember where id='".$osr[orderid]."'");
			if($ind_info[point] < $osr[gPrice] && $osr[gPrice] > 0) error_msg("보유 적립금이 충분하지 않습니다.");

			// 쿠폰
			if($coup_log_info[cl_coNo] != "") {
				$coup_info = _MQ("select coUse from odtCoupon where coNo='".$coup_log_info[cl_coNo]."'");
				if($coup_info[coUse] == "Y") error_msg("이미 결제에 사용된 쿠폰입니다.");
			}

		}

		// 사용한 적립금 차감
		shop_pointlog_insert( $osr[orderid] , "주문 시 적립금 사용 (주문번호 : {$_ordernum})" , $osr[gPrice] * -1 , "N" , 0);

		// 구매상품 적립금 지급 -> shop.order.couponadd_pro.php로 이동
		//shop_pointlog_insert( $osr[orderid] , "구매 적립금 적용 (주문번호 : {$_ordernum})" , $osr[gGetPrice] , "N" , $row_setup[paypoint_productdate]);

		// 쿠폰 사용처리
		if($coup_log_info[cl_coNo]) _MQ_noreturn("update odtCoupon set coUse ='Y', coUsedate = now() where coNo = '".$coup_log_info[cl_coNo]."'");

//		// 참여점수 입력
//		if($row_setup[s_action_order]>0){
//			_MQ_noreturn("insert into odtActionLog set acID = '".$osr[orderid]."', acTitle = '상품구매 (주문번호 : {$_ordernum})', acPoint = ". $row_setup[s_action_order] .", acRegidate = now()");
//			_MQ_noreturn("update odtMember set action = action + ". $row_setup[s_action_order] ." where id = '".$osr[orderid]."'");
//		}
	}
	// -- 적립금 사용량에 따른  ---

	// 결제 확인에 따른 적립금, 쿠폰등의 적용 처리
	_MQ_noreturn("update odtOrder set apply_point='Y' where ordernum='".$_ordernum."' ");

?>