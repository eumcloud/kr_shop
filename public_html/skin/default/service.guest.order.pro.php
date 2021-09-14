<?PHP

	include_once(dirname(__FILE__)."/../../include/inc.php");

	// 비회원주문 검색조건
	if($_mode <> "guest_search" && $_mode <> "clear_value"){
		$pass_name = $_COOKIE["guest_order_name"];
		$pass_ordernum = $_COOKIE["guest_order_num"];
		if(!$pass_name || !$pass_ordernum){
			error_alt("잘못된 접근입니다.");
		}
	}

	switch( $_mode ){

		// - 주문취소 ---
		case "cancel":

			$r = _MQ("
				select o.* , oc.oc_tid
				from odtOrder as o
				left join odtOrderCardlog as oc on (oc.oc_oordernum=o.ordernum)
				where 
					o.ordernum='".$ordernum."'  
					and o.canceled !='Y' 
					and o.paystatus2 = 'N' 
					and orderstatus_step not in ('발송완료' , '발급완료')
					and ordername='".addslashes($pass_name)."' 
					and member_type = 'guest'
					and  replace(ordernum,'-','') = '".addslashes(rm_str($pass_ordernum))."'
			");

			if(sizeof($r) ==0 ){
				error_alt("주문정보를 찾을 수 없습니다.");
			}

			$_canceldate = date('Ymd',time()); // 취소일자

			// 배송된 상품은 취소할수 없다.
			if( !in_array($r[orderstatus_step] , array("결제대기","결제확인")) ){
				error_alt("상품이 배송된 주문의 취소는 고객센터에 문의해주세요.");
			}

			// 결제된 티켓상품은 티켓이 바로 발송되므로 취소할수 없다.
			$is_coupon = _MQ_result("SELECT count(*) as cnt
									from odtOrder as o
									left join odtOrderProduct as op on (o.ordernum = op.op_oordernum)
									where
									ordernum='".$ordernum."'
									and op_orderproduct_type='coupon'
									and paystatus='Y'
									");
			if($is_coupon) 	error_alt("쿠폰이 발행된 상품의 취소는 고객센터에 문의해주세요.");


			// - 취소처리 ---
			$_ordernum = $ordernum;
			$_applytype = "guest";// 실행타입 : admin , member ::: member 일 경우 반드시 get_userid() 있어야 함

			// return 변수 1 : $_trigger = "Y"; // 처리형태 : Y(성공) , N(실패)
			// return 변수 2 : $arr_send ::: 문자메시지
			require_once($_SERVER["DOCUMENT_ROOT"] . "/pages/pg.cancle_total.php");
			//if(sizeof($arr_send) > 0 ){ onedaynet_sms_multisend($arr_send); }
			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			if(sizeof($arr_send) > 0 ){ onedaynet_alimtalk_multisend($arr_send); }

			if($_trigger == "Y"){ error_frame_loc_msg("/?pn=service.guest.order.list&" . enc('d' , $_PVSC) , "주문을 취소하였습니다."); }
			else { error_frame_loc_msg("/?pn=service.guest.order.list&" . enc('d' , $_PVSC) , "결제 취소요청이 실패하였습니다."); }
			// - 취소처리 ---


			break;
		// - 주문취소 ---

		// - 배송완료 ---
		case "complete":

			$que = " select op.*, p.partnerCode from odtOrder as o
								inner join odtOrderProduct as op on (o.ordernum = op.op_oordernum)
								inner join odtProduct as p on (p.code = op.op_pcode)
								where
								o.ordernum='{$ordernum}' and
								o.canceled ='N' and
								o.o_paystatus ='Y' and
								and o.ordername='".addslashes($pass_name)."' and member_type = 'guest'
								and  replace(ordernum,'-','') = '".addslashes(rm_str($pass_ordernum))."'
								op.op_delivstatus = 'N' and p.partnerCode = '".$partnerCode."'";

			$r = _MQ_assoc($que);
			if(sizeof($r) ==0 ){
				error_alt("주문정보를 찾을 수 없습니다.");
			}

			unset($os_product_sell_price,$os_oordernum,$os_cpid);
			foreach( $r as $k=>$v ){
				_MQ_noreturn("update odtOrderProduct set op_delivstatus = 'Y', op_completedate = now() where op_uid = '".$v[op_uid]."'");
				// 정산을 위한 변수 처리.
				$os_product_sell_price += ($v[op_price] * $v[op_cnt]);
				$os_oordernum			= $v[op_oordernum];
				$os_cpid				= $v[partnerCode];

			}

			error_frame_loc_msg("/?pn=service.guest.order.list&" . enc('d' , $_PVSC) , "배송완료 처리하였습니다.");
			break;
		// - 구매완료 ---

		// - 컴플레인 ---
		case "complain":

			if(!$opuid) error_msg("잘못된 접근입니다.");

			$que = "update odtOrderProduct set op_complain = '교환/반품신청' , op_complain_date = now() , op_complain_comment = '".$complain_content."' where op_uid = '".$opuid."'";

			_MQ_noreturn($que);

			error_frame_reload("접수되었습니다.");
			break;
		// - 컴플레인 ---
		// - 쿠폰 문자 재발송 ---
		case "coupon_sms_resend":

			if(!$opcuid) error_alt("잘못된 접근입니다.");

			// 주문상품과 쿠폰정보
			$osr = _MQ("select op_pname,op_option1,op_option2,op_option3, opc_expressnum, ordernum , orderhtel1, orderhtel2 ,orderhtel3
						from odtOrderProductCoupon as opc
						inner join odtOrderProduct as op on (op.op_uid = opc.opc_opuid)
						inner join odtOrder as o on (op.op_oordernum = o.ordernum)
						where opc_uid = '".$opcuid."'");


			// 쿠폰번호 sms 발송.
			$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			$smskbn = "coupon";	// 문자 발송 유형
			$sms_to		= phone_print($osr[orderhtel1],$osr[orderhtel2],$osr[orderhtel3]);
			$sms_from	= $row_company[tel];


			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			// 치환작업
			$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $osr['ordernum'], array(
				'{{쿠폰번호}}' => $osr['opc_expressnum']
			));
			$sms_msg = $arr_sms_msg['msg'];
			$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----


			//onedaynet_sms_multisend($arr_send);
			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			onedaynet_alimtalk_multisend($arr_send);


			error_alt("쿠폰번호가 재발송 되었습니다.");
			break;
		// - 쿠폰 문자 재발송 ---

		// - 비회원주문내역조회 ---
		case "guest_search":
			// 검색조건

			if($pass_name == "주문자이름") $pass_name = "";
			if($pass_ordernum == "주문자 주문번호" ) $pass_ordernum = "";

			// 검색조건 쿠키저장
			samesiteCookie("guest_order_name",$pass_name,0,"/");
			samesiteCookie("guest_order_num",$pass_ordernum,0,"/");

			error_frame_loc("/?pn=service.guest.order.list");

			break;
		// - 비회원주문내역조회 ---

		// - 검색정보 초기화 ---
		case "clear_value":
			// 검색조건 쿠키삭제
			samesiteCookie("guest_order_name","",0,"/");
			samesiteCookie("guest_order_num","",0,"/");

			error_frame_loc("/?pn=service.guest.order.list");

			break;
		// - 검색정보 초기화 ---

		default :
			error_msg('오류');
	}


?>