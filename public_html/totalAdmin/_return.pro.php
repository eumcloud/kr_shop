<?

	// LMH008

	include_once "inc.php";



	if($ordernum==''||$uid=='') { error_alt("잘못된 접근입니다."); }


	// SMS 보내기
	$ordr = _MQ(" select * from odtRequestReturn as rr left join odtOrder as o on (o.ordernum = rr.rr_ordernum) where rr_uid = '".$uid."' ");
	if( $ordr['orderhtel1'] && $_status <> $ordr['rr_status'] ) {
		/*-------------- 문자 발송 ---------------*/
		$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		$smskbn = "return_re_mem";	// 문자 발송 유형
		if($row_sms[$smskbn][smschk] == "y") {
			$sms_to		= phone_print($ordr[orderhtel1],$ordr[orderhtel2],$ordr[orderhtel3]);
			$sms_from	= $row_company[tel];

			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			// 치환작업
			$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $ordr['ordernum']);
			$sms_msg = $arr_sms_msg['msg'];
			$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

		}
		//onedaynet_sms_multisend($arr_send);
		//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		onedaynet_alimtalk_multisend($arr_send);
		/*-------------- // 문자 발송 ---------------*/
	}


	_MQ_noreturn("
		update odtRequestReturn set
			rr_status = '".$_status."',
			rr_type = '".$_type."',
			rr_reason = '".$_reason."',
			rr_content = '".$_content."',
			rr_admcontent = '".$_admcontent."',
			rr_edate = now()
			where rr_uid = '".$uid."'
		");


	error_frame_loc_msg("_return.list.php?".enc('d',$_PVSC),"정상적으로 수정하였습니다.");


?>