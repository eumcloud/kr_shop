<?PHP
	include "./inc.php";

	$app_path = "../upfiles";


	// 체크박스 Y/N 값
	$m_status = $m_status!='y'?'n':'y';

	/***** 회원 발송 설정값 입력 *****/ unset($tmp,$s_query,$_file);

		$tmp = _MQ("select * from m_sms_set where smskbn = '".$uid."'");

		$s_query = "
			  smschk		= '".$m_status."'
			, smstext		= '".$m_text."'
			, smstitle		= '".$m_title."'
			, sms_send_type = '".$m_send_type."'
		";


		//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		$kakao_status = ($kakao_status?$kakao_status:'N');
		$s_query .= "
			, kakao_status = '{$kakao_status}'
			, kakao_templet_num = '" . mysql_real_escape_string($kakao_templet_num) . "'
			, kakao_add1 = '" . mysql_real_escape_string($kakao_add1) . "'
			, kakao_add2 = '" . mysql_real_escape_string($kakao_add2) . "'
			, kakao_add3 = '" . mysql_real_escape_string($kakao_add3) . "'
			, kakao_add4 = '" . mysql_real_escape_string($kakao_add4) . "'
			, kakao_add5 = '" . mysql_real_escape_string($kakao_add5) . "'
			, kakao_add6 = '" . mysql_real_escape_string($kakao_add6) . "'
			, kakao_add7 = '" . mysql_real_escape_string($kakao_add7) . "'
			, kakao_add8 = '" . mysql_real_escape_string($kakao_add8) . "'
		";


		if($tmp[smskbn]) {

			if(!$m_file && !$m_file_OLD) { _PhotoDel( $app_path , $tmp[smsfile] ); $_file = ''; }
			else { $_file = _PhotoPro( $app_path , 'm_file' ); }

			$s_query .= " , smsfile = '".$_file."' ";

			_MQ_noreturn(" update m_sms_set set " . $s_query . " where smskbn = '".$uid."' ");

		} else {

			$_file = _PhotoPro( $app_path , 'm_file' );
			$s_query .= " , smsfile = '".$_file."' , smskbn = '".$uid."' ";

			_MQ_noreturn(" insert m_sms_set set " . $s_query);

		}

	error_loc_msg("_config.sms.form.php?_uid=".$uid , "성공적으로 저장되었습니다.");
	exit;
?>