<?php
include_once("inc.php");


switch ($_mode) {

	// - 정산대기처리 LMH???+LDD007
	case "settlementstatus_ready":
		if( sizeof($OpUid) > 0 ) {
				$sque = " update odtOrderProduct set op_settlementstatus='ready' where op_uid in ('". implode("' , '" , array_values($OpUid) ) ."') and op_settlementstatus='none' ";
				_MQ_noreturn($sque);
				order_settlement_status_opuid(array_values($OpUid));//2015-08-19 추가 - 정준철
		}
		error_frame_reload_nomsg() ; // 부모창 reload
	break;
	// - 정산대기처리 LMH???+LDD007

	// - 정산완료처리 LMH???+LDD007
	case "settlementstatus_complete":

		$datetime = date('Y-m-d H:i:s', time());
		$data = array();
		$data2 = array();
		// 같은 입점엄체 끼리 묶어 배열화
		foreach($OpUid as $k=>$v) {

			unset($_usepoint);
			$_usepoint = _MQ_result(" select op_usepoint from odtOrderProduct where op_uid = '".$v."' ");

			$data[$settle_data[$v]['partnerCode']]['opuid'][] = $v;
			$data[$settle_data[$v]['partnerCode']]['price'][] = $settle_data[$v]['price'];
			$data[$settle_data[$v]['partnerCode']]['delivery_price'][] = $settle_data[$v]['delivery_price'];
			$data[$settle_data[$v]['partnerCode']]['com_price'][] = $settle_data[$v]['com_price'];
			//$data[$settle_data[$v]['partnerCode']]['usepoint'][] = $settle_data[$v]['usepoint'];
			$data[$settle_data[$v]['partnerCode']]['usepoint'][] = $_usepoint;
			$data[$settle_data[$v]['partnerCode']]['discount'][] = $settle_data[$v]['discount'];
		}

		// 묶인 입점업체 데이터를 통합
		foreach($data as $k=>$v) {

			// 내부 계산을 위한 초기화값
			$opuid = '';
			$price = 0;
			$delivery_price = 0;
			$com_price = 0;
			$usepoint = 0;
			$discount = 0;

			$data2['partnerCode'][] = $k;
			$data2['count'][] = sizeof($v['price']);

			foreach($v['price'] as $kk=>$vv) {

				if($kk > 0) $opuid .= ',';
				$opuid .= $v['opuid'][$kk];
				$price += $v['price'][$kk];
				$delivery_price += $v['delivery_price'][$kk];
				$com_price += $v['com_price'][$kk];
				$usepoint += $v['usepoint'][$kk];
				$discount += $v['discount'][$kk];
			}

			$data2['opuid'][] = $opuid;
			$data2['price'][] = $price;
			$data2['delivery_price'][] = $delivery_price;
			$data2['com_price'][] = $com_price;
			$data2['usepoint'][] = $usepoint;
			$data2['discount'][] = $discount;
		}

		// odtOrderSettleComplete(정산완료테이블) 기록 및 odtTableText에 주문상품 고유값저장
		foreach($data2['partnerCode'] as $k=>$v) {

			$que = "
						insert into `odtOrderSettleComplete` set
							`s_partnerCode` = '{$data2['partnerCode'][$k]}',
							`s_price` = '{$data2['price'][$k]}',
							`s_delivery_price` = '{$data2['delivery_price'][$k]}',
							`s_com_price` = '{$data2['com_price'][$k]}',
							`s_usepoint` = '{$data2['usepoint'][$k]}',
							`s_discount` = '{$data2['discount'][$k]}',
							`s_count` = '{$data2['count'][$k]}',
							`s_date` = '{$datetime}'
						";
			$rst = _MQ_noreturn($que);
			$serialnum = mysql_insert_id();
			_text_info_insert( "odtOrderSettleComplete" , $serialnum , "s_opuid" , $data2['opuid'][$k] , "ignore");
		}
		if( sizeof($OpUid) > 0 ) {
				$sque = " update odtOrderProduct set op_settlementstatus='complete' where op_uid in ('". implode("' , '" , array_values($OpUid) ) ."') and op_settlementstatus='ready' ";
				_MQ_noreturn($sque);
				order_settlement_status_opuid(array_values($OpUid));//2015-08-19 추가 - 정준철
		}
		error_frame_reload_nomsg() ; // 부모창 reload
	break;
	// - 정산완료처리 LMH???+LDD007


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
			// $smskbn = "coupon_unuse";	// 문자 발송 유형
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