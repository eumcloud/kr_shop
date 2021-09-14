<?PHP

	//$_ordernum  - 주문번호
	//$_paymethod - 결제타입
	// $_apply_point - 연동여부
	// $_applytype - 실행타입 : admin , member ::: member 일 경우 반드시 get_userid() 있어야 함
	$_trigger = "Y"; // 처리형태

	$ordr = _MQ("SELECT * FROM odtOrder WHERE ordernum='" . $_ordernum . "'");
	//$r = _MQ("select * from odtOrderCardlog where oc_oordernum='".$_ordernum."' order by oc_uid desc limit 1");
	$r = _MQ("select * from odtOrderCardlog where oc_oordernum='".$_ordernum."' and oc_tid != '' order by oc_uid desc limit 1"); // 2016-11-15 간혹 주문완료페이지 back키 입력으로 잘못된데이터가 추가되는경우 발생 수정 SSJ

	// - 카드결제/계좌이체/가상계좌 취소 ---
	if( in_array($ordr[paymethod] , array("C" , "L" , "V")) && $ordr[paystatus] == "Y") {
		if($_force_cancel) {
			$is_pg_status = true;
		}
		else {
			// 결제 취소를 위한 거래 정보 호출
			switch($row_setup[P_KBN]) {
				case "L" :
					require(dirname(__FILE__)."/pg.cancle_lgpay.php");
					break;
				case "K" :
					require(dirname(__FILE__)."/pg.cancle_kcp.php");
					break;
				case "I" :
					require(dirname(__FILE__)."/pg.cancle_inicis.php");
					break;
				case "A" :
					require(dirname(__FILE__)."/pg.cancle_allthegate.php");
					break;
				case "M" :
					require(dirname(__FILE__)."/pg.cancle_mnbank.php");
					break;
				case "B" :
					$_paymethod = $ordr[paymethod];
					if($_paymethod=='L') {
						require(dirname(__FILE__)."/pg.cancle_billgate.account.php");
					} else {
						require(dirname(__FILE__)."/pg.cancle_billgate.php");
					}
				break;
				case "D" :
					require(dirname(__FILE__)."/pg.cancle_daupay.php");
					break;
			}
		}
		if ($is_pg_status) {	// pg모듈 호출 상태

			if($ordr[apply_point] == "Y") {
				// - 적용된 포인트, 쿠폰적용 취소 ---
				// 제공변수 : $_ordernum
				include(dirname(__FILE__)."/shop.order.pointdel_pro.php");
				// - 적용된 포인트, 쿠폰적용 취소 ---
			}
			if($ordr[paystatus] == "Y") {
				// 상품 재고 증가 및 판매량 차감
				$_ordernum = $_ordernum;
				include_once(dirname(__FILE__)."/shop.order.salecntdel_pro.php");

				// - 문자발송 ---
				$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				$smskbn = "cancel";	// 문자 발송 유형
				if($row_sms[$smskbn][smschk] == "y") {
					$sms_to		= phone_print($ordr[orderhtel1],$ordr[orderhtel2],$ordr[orderhtel3]);
					$sms_from	= $row_company[tel];

					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
					// 치환작업
					$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $_ordernum);
					$sms_msg = $arr_sms_msg['msg'];
					$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

				}
				// - 문자발송 ---
			}

			_MQ_noreturn("update odtOrder set canceled='Y', canceldate='".time()."', cancel_mem_type = '".$_applytype."' where ordernum='{$_ordernum}' ". ( $_applytype == "member" ? " and orderid='".get_userid()."' " : ""  ) . ( $_applytype == "guest" ? " and ordername='".addslashes($_COOKIE["guest_order_name"])."' and  replace(ordernum,'-','') = '".addslashes(rm_str($_COOKIE["guest_order_num"]))."'  " : ""  ) );

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

		$ool_bank_name_array = array(
		'39'=>'경남',
		'34'=>'광주',
		'04'=>'국민',
		'03'=>'기업',
		'11'=>'농협',
		'31'=>'대구',
		'32'=>'부산',
		'02'=>'산업',
		'45'=>'새마을금고',
		'07'=>'수협',
		'88'=>'신한',
		'26'=>'신한',
		'48'=>'신협',
		'05'=>'외환',
		'20'=>'우리',
		'71'=>'우체국',
		'37'=>'전북',
		'35'=>'제주',
		'81'=>'하나',
		'27'=>'한국씨티',
		'53'=>'씨티',
		'23'=>'SC은행',
		'09'=>'동양증권',
		'78'=>'신한금융투자증권',
		'40'=>'삼성증권',
		'30'=>'미래에셋증권',
		'43'=>'한국투자증권',
		'69'=>'한화증권'
		);

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

			if($ordr[paystatus]=='Y' && $_force_cancel!=true) { // 환불요청관리 메뉴에 표시될 내용
				if($bank_code && $refund_account && $refund_nm) {
					$moneyback_content = '환불계좌: ['.$ool_bank_name_array[$bank_code].'] '.$refund_account.' '.$refund_nm;
					$que = "update odtOrder set moneyback = '환불요청' , moneyback_date = now() , moneyback_comment = '".$moneyback_content."' where ordernum = '".$_ordernum."'";
					_MQ_noreturn($que);
				}
				else {
					if($v_cnt==0) {
						$v_cnt++;
						error_frame_loc_msg("_order.list.php?_PVSC=${_PVSC}" , "가상계좌 결제 취소는 환불계좌 정보를 입력하시고 하나씩 취소해야 합니다.");
					} else { exit(); }
				}
			}
		}

		if($ordr[apply_point] == "Y") {
			// - 적용된 포인트, 쿠폰적용 취소 ---
			// 제공변수 : $_ordernum
			include(dirname(__FILE__)."/shop.order.pointdel_pro.php");
			// - 적용된 포인트, 쿠폰적용 취소 ---
		}
		if($ordr[paystatus] == "Y") {
			// 상품 재고 증가 및 판매량 차감
			$_ordernum = $_ordernum;
			include_once(dirname(__FILE__)."/shop.order.salecntdel_pro.php");

			// - 문자발송 ---

			// 계좌 미발급된 가상계좌 주문건은 문자를 발송하지 않는다.
			$sms_check_virtual = true;
			if( $ordr['paymethod'] == 'V') {
				$chk = _MQ_result(" select count(*) from odtOrderOnlinelog where ool_ordernum = '".$_ordernum."' and ool_type = 'R' ");
				if( $chk == 0 ) { $sms_check_virtual = false; }
			}

			$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			$smskbn = "cancel";	// 문자 발송 유형
			if($row_sms[$smskbn][smschk] == "y" && $sms_check_virtual === true) {
				$sms_to		= phone_print($ordr[orderhtel1],$ordr[orderhtel2],$ordr[orderhtel3]);
				$sms_from	= $row_company[tel];

				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				// 치환작업
				$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $_ordernum);
				$sms_msg = $arr_sms_msg['msg'];
				$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

			}
			// - 문자발송 ---
		}

		_MQ_noreturn("update odtOrder set canceled='Y', canceldate='".time()."', cancel_mem_type = '".$_applytype."' where ordernum='{$_ordernum}' ". ( $_applytype == "member" ? " and orderid='".get_userid()."' " : ""  ) . ( $_applytype == "guest" ? " and ordername='".addslashes($_COOKIE["guest_order_name"])."' and  replace(ordernum,'-','') = '".addslashes(rm_str($_COOKIE["guest_order_num"]))."' " : ""  ) );

		// 주문발송 상태 변경
		order_status_update($_ordernum);

		$_trigger = "Y"; // 처리형태
	}

?>