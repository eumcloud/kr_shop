<?

	include_once dirname(__FILE__)."/../../include/inc.php";

	if($row_setup['recaptcha_api']&&$row_setup['recaptcha_secret']) {
		// 스팸방지
		require_once(dirname(__FILE_).'/../../include/recaptcha/recaptchalib.php');
		$privatekey = $row_setup['recaptcha_secret'];
		$resp = recaptcha_check_answer ($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
		if (!$resp->is_valid) {
		    error_alt("스팸방지 문자를 정확히 입력하세요. (ERROR: ".$resp->error.")");
		}
	}

	if(!$_ordernum) { error_alt("주문번호를 입력하세요"); } else { $ordernum = $_ordernum; }
	if(count($_opuid)==0) { error_alt($arr_return_type[$_type]."하실 상품을 한개 이상 선택하세요."); }

	if( is_login() ) { $s_query = " and o.orderid = '".get_userid()."' and o.member_type = 'member' "; }
	else { $s_query = " and o.member_type = 'guest' "; }

	$ordr = _MQ_assoc("
		select * from odtOrderProduct as op
		left join odtOrder as o on (op.op_oordernum = o.ordernum)
		left join odtProduct as p on (op.op_pcode = p.code)
		where 1 ".$s_query."
		and op.op_oordernum = '".$ordernum."' and op.op_cancel = 'N' and o.canceled = 'N' and o.order_type in ('both','product') and op.op_is_addoption = 'N'
		and op.op_delivstatus = 'Y'
		group by op.op_pcode order by op_uid asc
		");

	if( count($ordr) == 0 ) { error_alt("잘못된 주문번호 이거나 교환/반품할 수 있는 상품이 없습니다.");	}

	$_opuids = implode(',',$_opuid);
	$_content = strip_tags($_content);

	// 중복 신청 체크
	$_dup_chk = _MQ_result(" select count(*) from odtRequestReturn where rr_opuid = '".$_opuids."' ");
	if( $_dup_chk > 0 ) { error_alt("같은 주문상품을 중복하여 교환/반품 신청할 수 없습니다."); }

	_MQ_noreturn("
		insert odtRequestReturn set
			rr_ordernum = '".$_ordernum."',
			rr_opuid = '".$_opuids."',
			rr_content = '".$_content."',
			rr_type = '".$_type."',
			rr_reason = '".$_reason."',
			rr_status = 'R',
			rr_rdate = now(),
			rr_member = '".get_userid()."'
	");

	// SMS 보내기
	if( $ordr[0]['orderhtel1'] ) {
		/*-------------- 문자 발송 ---------------*/
		$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		$smskbn = "return_mem";	// 문자 발송 유형
		if($row_sms[$smskbn][smschk] == "y") {
			$sms_to		= phone_print($ordr[0][orderhtel1],$ordr[0][orderhtel2],$ordr[0][orderhtel3]);
			$sms_from	= $row_company[tel];

			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			// 치환작업
			$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $ordr[0]['ordernum']);
			$sms_msg = $arr_sms_msg['msg'];
			$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

		}

		$smskbn = "return_adm";	// 문자 발송 유형
		if($row_sms[$smskbn][smschk] == "y") {
			$sms_to		= $row_company[htel];
			$sms_from	= $row_company[tel];

			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			// 치환작업
			$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $ordr[0]['ordernum']);
			$sms_msg = $arr_sms_msg['msg'];
			$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

		}
		//onedaynet_sms_multisend($arr_send);
		//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		onedaynet_alimtalk_multisend($arr_send);
		/*-------------- // 문자 발송 ---------------*/
	}

	error_frame_loc_msg(($_PVSC?enc('d' , $_PVSC):"/?pn=".$pn),"정상적으로 신청되었습니다.");

?>