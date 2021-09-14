<?PHP
	include "inc.php";

	// - 입력수정 사전처리 ---
	if( in_array( $_mode , array("add" , "modify") ) ) {

		// --사전 체크 ---
		$ttContent = nullchk($ttContent , "댓글 내용을 입력해주시기 바랍니다.");
		// --사전 체크 ---

		// --query 사전 준비 ---
		$sque = "
			ttContent='". $ttContent ."',
			ttID='". $ttID ."',
			ttName = '".$ttName."'
		";
		// --query 사전 준비 ---

	}
	// - 입력수정 사전처리 ---


	if( $ttNo) {
		$que = " select *  from odtTt where ttNo='".$ttNo."' ";
		$r = _MQ($que);
	}


	// - 모드별 처리 ---
	switch( $_mode ){

		// 추가
		case "add":
			$que = " insert odtTt set " . $sque . " ,ttProCode = '".$r[ttProCode]."', ttIsReply='1' , ttSNo='". $r[ttNo] ."', ttRegidate=now() ";
			_MQ_noreturn($que);

			// 회원에게 문자 발송
			$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			$smskbn = "talk_re";	// 문자 발송 유형
			if($row_sms[$smskbn][smschk] == "y") {
				$tmp = _MQ(" select * from odtMember where id = '".$r[ttID]."' and userType = 'B' ");
				if( $tmp[htel1] ) {

					$sms_to		= phone_print($tt_info[htel1],$tt_info[htel2],$tt_info[htel3]);
					$sms_from	= $row_company[tel];

					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
					// 치환작업
					$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'],'', array("{{회원명}}"=>$tmp['name']));
					$sms_msg = $arr_sms_msg['msg'];
					$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

					//onedaynet_sms_multisend($arr_send);
					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
					onedaynet_alimtalk_multisend($arr_send);
				}
			}

			error_loc("_product_talk.list.php?" . enc('d' , $_PVSC));
			break;

		// 수정
		case "modify":
			$que = " update odtTt set $sque where ttNo='{$ttNo}' ";
			_MQ_noreturn($que);
			error_loc("_product_talk.form.php?_mode=${_mode}&ttNo=${ttNo}&_PVSC=${_PVSC}");
			break;


		// 삭제
		case "delete":
			_MQ_noreturn("delete from odtTt where ttNo='{$ttNo}' || ttSNo='{$ttNo}' ");
			error_loc("_product_talk.list.php?".enc('d' , $_PVSC));
			break;

	}
	// - 모드별 처리 ---

	exit;
?>