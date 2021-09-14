<?PHP
	// sms 발송정보 초기화.
	$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

	// *** 결제확인 시 --> 티켓발행 ***

	// - 주문정보 추출 ---
	$osr = get_order_info($_ordernum);

	// - 주문상품 정보 추출 ---
	$op_assoc = get_order_product_info($_ordernum);

	foreach($op_assoc as $op_key => $op_row) {

		if($op_row[op_orderproduct_type] == "coupon") {
			for($i=0;$i<$op_row[op_cnt];$i++) {	// 갯수만큼 티켓 생성.
				$coupon_num = shop_couponnum_create();
				_MQ_noreturn("insert into odtOrderProductCoupon set opc_opuid = '".$op_row[op_uid]."', opc_expressnum = '".$coupon_num."', opc_rdatetime=now(), opc_status='대기'");

				// 쿠폰번호 sms 발송.
				$smskbn = "coupon";	// 문자 발송 유형
				if($row_sms[$smskbn][smschk] == "y") {

					// 사용자 전화번호 적용 - 2016-01-22
					$app_htel = tel_format($osr[userhtel1].$osr[userhtel2].$osr[userhtel3]);
					$app_htel_tmp = tel_format($osr[orderhtel1].$osr[orderhtel2].$osr[orderhtel3]);
					$sms_to		=  $app_htel ? $app_htel : $app_htel_tmp ;
					$sms_from	= $row_company[tel];

					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
					// 치환작업
					$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $osr['ordernum'], array(
						'{{쿠폰번호}}'     => $coupon_num
					));
					$sms_msg = $arr_sms_msg['msg'];
					$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

				}
			}

			// 상태를 발급완료로 변경
			_MQ_noreturn("update odtOrderProduct set op_delivstatus = 'Y' where op_uid = '".$op_row[op_uid]."'");

		}

	}

	// 결제확인을 Y로 수정.
	if( $osr[order_type] != 'product' ) {
		_MQ_noreturn("update odtOrder set paystatus2 = 'Y' where ordernum = '".$_ordernum."'");
	}

	// 티켓 메일과 티켓 문자를 전송
	//onedaynet_sms_multisend($arr_send);
	//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
	onedaynet_alimtalk_multisend($arr_send);

	if( $osr['member_type']=='member' ) {

		// 구매상품 적립금 지급
		shop_pointlog_insert( $osr[orderid] , "구매 적립금 적용 (주문번호 : {$_ordernum})" , $osr[gGetPrice] , "N" , $row_setup[paypoint_productdate]);

		// 참여점수 입력
		if($row_setup[s_action_order]>0){
			_MQ_noreturn("insert into odtActionLog set acID = '".$osr[orderid]."', acTitle = '상품구매 (주문번호 : {$_ordernum})', acPoint = ". $row_setup[s_action_order] .", acRegidate = now()");
			_MQ_noreturn("update odtMember set action = action + ". $row_setup[s_action_order] ." where id = '".$osr[orderid]."'");
		}

	}


?>