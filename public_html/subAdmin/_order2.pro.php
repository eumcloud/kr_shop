<?PHP

	include_once("inc.php");





	switch ($_mode) {



		// - 쿠폰 사용여부 체크
		case "coupon_use":
			if( $type == "use" ) {
				$Query=" UPDATE odtOrderProductCoupon SET opc_udatetime=now() , opc_status ='사용'  WHERE opc_uid = '" . $uid . "' ";
				$result = _MQ_noreturn($Query);

				// 문자 발송유형
				$smskbn = "coupon_use";
			}
			else if( $type == "unuse" ) {
				$Query=" UPDATE odtOrderProductCoupon SET opc_udatetime='0000-00-00 00:00:00', opc_status ='대기'  WHERE opc_uid = '" . $uid . "' ";
				$result = _MQ_noreturn($Query);

				// 문자 발송유형
				$smskbn = "coupon_unuse";
			}

			// 쿠폰사용시 문자알림
			if($result) {

				// - 주문정보 추출 ---
				$s_que = " select *
							from odtOrderProductCoupon as opc
							inner join odtOrderProduct as op on ( op.op_uid = opc.opc_opuid )
							inner join odtOrder as o on ( o.ordernum = op.op_oordernum )
							where opc.opc_uid = '" . $uid . "' ";
				$s_res = _MQ($s_que);
				// - 주문정보 추출 ---

				// 2015-11-18 - 모든 쿠폰 사용한 주문상품건 op_coupon_use 갱신
				$chkr = _MQ("select count(*) as cnt from odtOrderProductCoupon where opc_opuid = '". $s_res[opc_opuid] ."' and opc_status != '사용' ");
				if($chkr[cnt] == 0 ){
					$opque = " update odtOrderProduct set op_coupon_use=now() where op_uid = '".$s_res[opc_opuid]."' ";
					_MQ_noreturn($opque);
				}
				// 2017-01-31 ::: 쿠폰 사용일 오류 수정 ::: JJC
				else {
					$opque = " update odtOrderProduct set op_coupon_use='0000-00-00 00:00:00' where op_uid = '".$s_res[opc_opuid]."' ";
					_MQ_noreturn($opque);
				}
				// 2015-11-18 - 모든 쿠폰 사용한 주문상품건 op_coupon_use 갱신

				// - 문자발송 ---
				$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
//				$smskbn = "coupon_unuse";	// 문자 발송 유형
				if($row_sms[$smskbn][smschk] == "y") {

					// 사용자 전화번호 적용 - 2016-01-22
					$app_htel = tel_format($s_res[userhtel1].$s_res[userhtel2].$s_res[userhtel3]);
					$app_htel_tmp = tel_format($s_res[orderhtel1].$s_res[orderhtel2].$s_res[orderhtel3]);
					$sms_to		=  $app_htel ? $app_htel : $app_htel_tmp ;
					$sms_from	= $row_company[tel];

					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
					// 치환작업
					$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $s_res['ordernum'], array("{{쿠폰번호}}"=>$s_res['opc_expressnum'],"{{주문상품명}}"=>$s_res['op_pname']));
					$sms_msg = $arr_sms_msg['msg'];
					$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

				}
				//onedaynet_sms_multisend($arr_send);
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				onedaynet_alimtalk_multisend($arr_send);
				// - 문자발송 ---
			}
			// 쿠폰사용시 문자알림

			error_frame_reload_nomsg() ; // 부모창 reload
			break;
		// - 쿠폰 사용여부 체크




	}

	exit;

?>