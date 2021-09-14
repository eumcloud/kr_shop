<?PHP

	// 회원일경우 사용한 적립금 반환처리 하고, 연동 취소 처리한다.
	$osr = get_order_info($_ordernum);

//	if( $osr[apply_point] == "Y" ) { // 연동처리 된 상태라면..
	if( $osr['member_type']=='member' ) { // 연동처리 된 상태라면..

		// 사용한 적립금 반환
		$part_cancel_chk = _MQ_result(" select count(*) from odtOrderProduct where op_oordernum = '".$_ordernum."' and op_cancel != 'N' ");
		if( ($part_cancel_chk == 0 && $osr['paymethod'] == 'G') || $osr['paymethod'] <> 'G') {
			shop_pointlog_insert( $osr[orderid] , "주문취소에 따른 사용 적립금 반환 (주문번호 : {$_ordernum})" , $osr[gPrice] , "N" , 0);
		}

		// 지급된 적립금 회수
		shop_pointlog_delete( $osr[orderid] , "구매 적립금 적용 (주문번호 : {$_ordernum})" );

		// 쿠폰 사용 취소
		$coup_log_info = _MQ("select cl_coNo from odtOrderCouponLog where cl_type = 'member' and cl_oordernum='".$osr[ordernum]."'");
		if($coup_log_info[cl_coNo]) _MQ_noreturn("update odtCoupon set coUse ='N', coUsedate = NULL where coNo = '".$coup_log_info[cl_coNo]."'");

		// 참여점수 삭제
		$isActionpoint = _MQ("select * from odtActionLog where acID = '".$osr[orderid]."' and acTitle = '상품구매 (주문번호 : {$_ordernum})' ");
		if($isActionpoint[acPoint]>0){
			_MQ_noreturn("insert into odtActionLog set acID = '".$osr[orderid]."', acTitle = '상품구매취소 (주문번호 : {$_ordernum})', acPoint = -". $isActionpoint[acPoint] .", acRegidate = now()");
			_MQ_noreturn("update odtMember set action = action - ". $isActionpoint[acPoint] ." where id = '".$osr[orderid]."'");
		}
	}

	// 연동 취소 처리
	_MQ_noreturn("update odtOrder set apply_point='N' where ordernum='".$_ordernum."' ");	

?>