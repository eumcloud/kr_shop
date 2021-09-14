<?php


	// filename : inc.2year_opt.php
	// inc.daily.update.php에 include 되어 1일 1회 실행됨.


	// 2016-05-18 ::: 매 2년마다 수신동의 설정 ----- 수신동의한지 2년이 넘은 회원 체크하여 - odt2yearOptLog 에 데이터 등록
	include_once(dirname(__FILE__).'/inc.php');


	// --- 매2년마다 수신동의 발송적용일 경우에만 적용 ---
	if($row_setup['s_2year_opt_use'] == "Y") {

		// JJC : 수정 : 2021-05-17

		// 3년 지난 경우 삭제
		_MQ_noreturn(" DELETE FROM odt2yearOptLog WHERE DATE_ADD(ol_rdate , interval + 3 year) <= CURDATE() ");

		// 중복 삭제 - 최대값 제외 삭제
		$que = " SELECT ol_mid , COUNT(*) AS cnt , MAX(ol_uid) AS ol_uid FROM odt2yearOptLog GROUP BY ol_mid HAVING cnt > 1 ";
		$res = _MQ_assoc($que);
		foreach( $res as $k=>$v) { _MQ_noreturn(" DELETE FROM odt2yearOptLog WHERE ol_mid = '". $v['ol_mid'] ."' AND ol_uid != '". $v['ol_uid'] ."' "); }

		//	$mr_sms_row = _MQ_assoc("
		//		SELECT 
		//			m.id , m.sms , m.mailling
		//		FROM odtMember as m
		//		LEFT JOIN odt2yearOptLog as ol on (ol.ol_mid = m.id AND DATE_ADD(ol_rdate , interval + 2 year) <= CURDATE() )
		//		WHERE 
		//			ol.ol_uid is null and
		//			m.userType='B' and 
		//			(sms = 'Y' or mailling = 'Y' ) and 
		//			DATE_ADD(m_opt_date , interval + 2 year) <= CURDATE()
		//	"); // 수신동의 2년 지난 - 회원 추출
		$mr_sms_row = _MQ_assoc("
			SELECT 
				m.id , m.sms , m.mailling
			FROM odtMember as m
			LEFT JOIN odt2yearOptLog as ol on (ol.ol_mid = m.id AND DATE_ADD(ol_rdate , interval + 2 year) >= CURDATE() )
			WHERE 
				ol.ol_uid is null and
				m.userType='B' and 
				m.name NOT IN ('탈퇴한회원','휴면회원') AND
				(sms = 'Y' or mailling = 'Y' ) and 
				DATE_ADD(m_opt_date , interval + 2 year) <= CURDATE()
		"); // 수신동의 2년 지난 - 회원 추출
		// JJC : 수정 : 2021-05-17
		foreach($mr_sms_row as $mr_sms_k => $mr_sms_v){
			if( $mr_sms_v['mailling'] == "Y" && $mr_sms_v['sms'] == "Y" ) {$_type = "both";}
			else if( $mr_sms_v['mailling'] == "Y" ) {$_type = "email";}
			else if( $mr_sms_v['sms'] == "Y" ) {$_type = "sms";}
			$sque = " insert odt2yearOptLog set  ol_mid = '". $mr_sms_v['id'] ."', ol_type = '". $_type ."', ol_rdate = now() ";
			_MQ_noreturn($sque);
		}

	}
	// --- 매2년마다 수신동의 발송적용일 경우에만 적용 ---

?>