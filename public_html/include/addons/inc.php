<?PHP

	if(!headers_sent()){
		session_start();
		header("Content-Type: text/html; charset=UTF-8");
	}

	if( !$_path_str ) {
		if( @file_exists("../include/config_database.php") ) {
			$_path_str = "..";
		}
		else {
			$_path_str = ".";
		}
	}

	// 아이피 차단.
	$arr_deny_ip = array("118.219.234.241" , "118.219.234.108" , "112.158.235.11");
	if( in_array($_SERVER[REMOTE_ADDR] , $arr_deny_ip )) { exit; }

	$_path_str = $_SERVER[DOCUMENT_ROOT]."/include";
	include_once "${_path_str}/config_database.php";
	include_once "${_path_str}/config_connect.php";
	include_once "${_path_str}/lib.func.php";
	include_once "${_path_str}/lib.qry.php";
	include_once "${_path_str}/var.php";

	// 스킨 var 불러오기
	include_once "${_path_str}/../pages/var.php";

	// - 현재 폴더/파일명 확인 ---
	$EX_FILENAME = explode("/" , $_SERVER[SCRIPT_FILENAME]);
	$CURR_FILENAME = $EX_FILENAME[(sizeof($EX_FILENAME)-1)];
	// - 현재 폴더/파일명 확인 ---


	// - 쇼핑몰 기본정보 호출 ---
	$row_setup = info_basic();
	$row_setup[ranDsum] = $row_setup[ranDsum] ? $row_setup[ranDsum] : "tkdtkdsjaj";
	// - 쇼핑몰 기본정보 호출 ---


	// - 회사정보 호출 ---
	$row_company = info_company();
	// - 회사정보 호출 ---

	// - 문자발송정보 호출 ---
	$row_sms = info_sms();
	// - 문자발송정보 호출 ---


	// - 관리자 정보 호출 ---
	if( $_COOKIE['auth_adminid'] ) {
		$row_admin = info_admin($row_setup[ranDsum],$_MaddSum);
	}
	// - 관리자 정보 호출 ---


	// - 입점업체 관리자 정보 호출 ---
	if( $_COOKIE['auth_comid'] ) {
		$com = info_subcompany($row_setup[ranDsum],$_MaddSum);
	}
	// - 입점업체 관리자 정보 호출 ---


	// - 회원정보 호출 ---
	if( $_COOKIE['auth_memberid'] ) {
		$row_member = info_member($row_setup[ranDsum],$addSum);

		// -- 쿠폰 발급 알람 및 만료된 쿠폰 삭제 ---
		if($row_member[id]) {
			$isAlarm = _MQ("select count(*) as cnt from odtCoupon where coID ='".$row_member[id]."' and coUse ='N' and alarm = 'N'");
			if($isAlarm[cnt]) {
				_MQ_noreturn("update odtCoupon set alarm='Y' where coID='".$row_member[id]."'");
				_MQ_noreturn(" update odtCoupon set coUse='E' where coID='".$row_member[id]."' and coUse='N' and coLimit < CURDATE() ");
				error_loc_msg("/?Pid=u03b05" , $row_member[id]."님 쿠폰이 발급되었습니다.");
			}
		}
		// -- 쿠폰 발급 알람 및 만료된 쿠폰 삭제 ---

	}
	// - 회원정보 호출 ---

?>