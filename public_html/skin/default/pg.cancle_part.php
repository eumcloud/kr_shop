<?PHP

	//$_ordernum  - 주문번호
	//$_paymethod - 결제타입
	// $_apply_point - 연동여부
	// $_applytype - 실행타입 : admin , member ::: member 일 경우 반드시 get_userid() 있어야 함
	$_trigger = "Y"; // 처리형태

	$ordr = _MQ("
		SELECT * FROM odtOrderProduct as op
		left join odtOrder as o on (o.ordernum = op.op_oordernum)
		WHERE ordernum='" . $_ordernum . "' and op_uid = '".$_uid."'
	");
	//$r = _MQ("select * from odtOrderCardlog where oc_oordernum='".$_ordernum."' order by oc_uid desc limit 1");
	$r = _MQ("select * from odtOrderCardlog where oc_oordernum='".$_ordernum."' and oc_tid != '' order by oc_uid desc limit 1"); // 2016-11-15 간혹 주문완료페이지 back키 입력으로 잘못된데이터가 추가되는경우 발생 수정 SSJ

	// - 카드결제/계좌이체 취소 ---
    if( in_array($ordr['paymethod'] , array("C" , "L")) && $ordr['paystatus'] == "Y") {
		if($_force_cancel || $_total_amount < 1 || $ordr['op_cancel_type'] == 'point') {
			$is_pg_status = true;
		}
		else {
			// 결제 취소를 위한 거래 정보 호출
			switch($row_setup[P_KBN]) {
				case "L" :
					require(dirname(__FILE__)."/pg.cancle_part_lgpay.php");
					break;
				case "K" :
					require(dirname(__FILE__)."/pg.cancle_part_kcp.php");
					break;
				case "I" :
					require(dirname(__FILE__)."/pg.cancle_part_inicis.php");
					break;
				case "A" :
					require(dirname(__FILE__)."/pg.cancle_part_allthegate.php");
					break;
				case "M" :
					require(dirname(__FILE__)."/pg.cancle_part_mnbank.php");
					break;
				case "B" :
					$_paymethod = $ordr[paymethod];
					if($_paymethod=='L') {
						require(dirname(__FILE__)."/pg.cancle_part_billgate.account.php");
					} else {
						require(dirname(__FILE__)."/pg.cancle_part_billgate.php");
					}
				break;
				case "D" :
					require(dirname(__FILE__)."/pg.cancle_part_daupay.php");
				break;
			}
		}

		if ($is_pg_status) {	// pg모듈 호출 상태

			// 2018-11-19 SSJ :: 단일 상품 재고 증가 및 판매량 차감 :: $_ordernum , $_uid
			include(dirname(__FILE__)."/shop.order.salecntdel_part.php");

			// - 문자발송 ---
			$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			// 마지막 부분취소일 경우 주문 전체 취소 문자 발송
			$tmp = _MQ(" select count(*) as cnt from odtOrderProduct where op_cancel!='Y' and op_oordernum = '".$_ordernum."' ");
			if($tmp[cnt]==1) {
				$smskbn = "cancel"; // 문자 발송 유형
			}else{
				$smskbn = "cancel_part";    // 문자 발송 유형
			}
			if($row_sms[$smskbn][smschk] == "y") {
				$sms_to		= phone_print($ordr[orderhtel1],$ordr[orderhtel2],$ordr[orderhtel3]);
				$sms_from	= $row_company[tel];

				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				// 치환작업
				$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $_ordernum, array(
					'{{주문번호}}' => $_ordernum,
					'{{사이트명}}' => $row_setup['site_name'],
					'{{주문상품명}}' => trim($ordr['op_pname']) . implode(" " , array_filter(array(' '.$ordr['op_option1'],$ordr['op_option2'],$ordr['op_option3']))),
				));
				$sms_msg = $arr_sms_msg['msg'];
				$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

			}
			// - 문자발송 ---

			_MQ_noreturn(" update odtOrderProduct set
				op_cancel = 'Y',
				op_cancel_returnmsg = '".$_result_msg."',
				op_cancel_tid = '".$_result_tid."',
				op_cancel_cdate = now()
				where op_oordernum = '".$_ordernum."' and op_uid = '".$_uid."'
			");

			// 추가옵션 취소처리
			$add_res = _MQ_assoc(" select * from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$ordr['op_pouid']."' and op_oordernum = '".$ordr['op_oordernum']."' ");
			if( count($add_res) > 0 ) {
				foreach($add_res as $adk=>$adv) {
					_MQ_noreturn(" update odtOrderProduct set
						op_cancel = 'Y',
						op_cancel_returnmsg = '".$_result_msg."',
						op_cancel_tid = '".$_result_tid."',
						op_cancel_cdate = now()
						where op_oordernum = '".$adv['op_oordernum']."' and op_uid = '".$adv['op_uid']."'
					");
				}
			}

			// 마지막 부분취소일 경우 주문 전체 취소
			$tmp = _MQ(" select count(*) as cnt from odtOrderProduct where op_cancel!='Y' and op_oordernum = '".$_ordernum."' ");
			if($tmp[cnt]==0) {
				// 제공변수 : $_ordernum
				include(dirname(__FILE__)."/shop.order.pointdel_pro.php");
				// - 적용된 포인트, 쿠폰적용 취소 ---
				_MQ_noreturn("update odtOrder set canceled='Y', canceldate='".time()."', cancel_mem_type = 'admin' where ordernum='{$_ordernum}' ". ( $_applytype == "member" ? " and orderid='".get_userid()."' " : ""  ) );
			}else{
				// 제공변수 : $_ordernum, $_uid
				include(dirname(__FILE__)."/shop.order.pointdel_part.php");
			}

			// 주문발송 상태 변경
			order_status_update($_ordernum);

			$_trigger = "Y"; // 처리형태
		}
		else {
			$_trigger = "N"; // 처리형태
		}
	}
	// - 카드결제/계좌이체 취소 ---

	// - 무통장입금 취소 ---
	else {

		$ool_bank_name_array = $ksnet_bank;

		if($ordr[paymethod]=='V') { if(!isset($v_cnt)) { $v_cnt = 0; }
			// LGU+ 가상계좌 반납처리
			switch($row_setup[P_KBN]) {
				case "L" :
					$LGD_TID = $r[oc_tid];
					$CST_PLATFORM               = $row_setup[P_MODE];
					$CST_MID                    = $row_setup[P_ID];
					$LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
					$configPath 				= PG_DIR . "/lgpay/lgdacom";
					require_once(PG_DIR. "/lgpay/lgdacom/XPayClient.php");
					$xpay = &new XPayClient($configPath, $CST_PLATFORM); $xpay->Init_TX($LGD_MID);
					$xpay->Set("LGD_TXNAME", "Settlement");
					$xpay->Set("LGD_TID", $LGD_TID);
					$is_pg_status = $xpay->TX();
					break;
			}
		}

		// 2018-11-19 SSJ :: 단일 상품 재고 증가 및 판매량 차감 :: $_ordernum , $_uid
		include(dirname(__FILE__)."/shop.order.salecntdel_part.php");

		_MQ_noreturn(" update odtOrderProduct set
			op_cancel = 'Y',
			op_cancel_cdate = now()
			where op_oordernum = '".$_ordernum."' and op_uid = '".$_uid."'
		");

		// 추가옵션 취소처리
		$add_res = _MQ_assoc(" select * from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$ordr['op_pouid']."' and op_oordernum = '".$ordr['op_oordernum']."' ");
		if( count($add_res) > 0 ) {
			foreach($add_res as $adk=>$adv) {
				_MQ_noreturn(" update odtOrderProduct set
					op_cancel = 'Y',
					op_cancel_cdate = now()
					where op_oordernum = '".$adv['op_oordernum']."' and op_uid = '".$adv['op_uid']."'
				");
			}
		}

		// - 문자발송 ---
		$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		// 마지막 부분취소일 경우 주문 전체 취소 문자 발송
		$tmp = _MQ(" select count(*) as cnt from odtOrderProduct where op_cancel!='Y' and op_oordernum = '".$_ordernum."' ");
		if($tmp[cnt]==0) {
			$smskbn = "cancel"; // 문자 발송 유형
		}else{
			$smskbn = "cancel_part";    // 문자 발송 유형
		}
		if($row_sms[$smskbn][smschk] == "y") {
			$sms_to		= phone_print($ordr[orderhtel1],$ordr[orderhtel2],$ordr[orderhtel3]);
			$sms_from	= $row_company[tel];

			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			// 치환작업
			$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $_ordernum, array(
				'{{주문번호}}' => $_ordernum,
				'{{사이트명}}' => $row_setup['site_name'],
				'{{주문상품명}}' => trim($ordr['op_pname']) . implode(" " , array_filter(array(' '.$ordr['op_option1'],$ordr['op_option2'],$ordr['op_option3']))),
			));
			$sms_msg = $arr_sms_msg['msg'];
			$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

		}
		// - 문자발송 ---

		// 마지막 부분취소일 경우 주문 전체 취소
		$tmp = _MQ(" select count(*) as cnt from odtOrderProduct where op_cancel!='Y' and op_oordernum = '".$_ordernum."' ");
		if($tmp[cnt]==0) {

			// 전액 적립금 결제의 마지막 부분취소일 경우 적립금을 반환하지 않는다.
			if($ordr[paymethod]=='G') { $_no_return_point = true; }

			// 제공변수 : $_ordernum
			include(dirname(__FILE__)."/shop.order.pointdel_pro.php");
			// - 적용된 포인트, 쿠폰적용 취소 ---
			_MQ_noreturn("update odtOrder set canceled='Y', canceldate='".time()."', cancel_mem_type = 'admin' where ordernum='{$_ordernum}' ". ( $_applytype == "member" ? " and orderid='".get_userid()."' " : ""  ) );

			// 현금영수증 취소
			$tmp = _MQ("select * from odtOrderCashlog where ocs_ordernum = '".$_ordernum."' and ocs_method='AUTH' ");
			$tmp_c = _MQ("select count(*) as cnt from odtOrderCashlog where ocs_ordernum = '".$_ordernum."' and ocs_method='CANCEL' ");
			if($tmp[ocs_ordernum] && $tmp_c[cnt]==0) {
				$method = 'CANCEL';
				$tid = $tmp[oc_tid];
				$ordernum = $_ordernum;
				$member = $ordr[orderid];
				$amount = $ordr[tPrice];
				$num = $ordr[orderhtel1].$ordr[orderhtel2].$ordr[orderhtel3];
				$use = '1';
				$store = $company_info[number1];
				include_once dirname(__FILE__)."/totalCashReceipt.php";
			}

			// 주문발송 상태 변경
			order_status_update($_ordernum);
		}else{
			// 제공변수 : $_ordernum, $_uid
			include(dirname(__FILE__)."/shop.order.pointdel_part.php");
		}

		$_trigger = "Y"; // 처리형태
	}

?>