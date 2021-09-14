<?PHP

	// ![LCY] 2020-08-25 :: 크롬 쿠키공유(IFRAME 간 세션 공유) 문제 서포트
	if(!function_exists('samesiteCookie')) {
		function samesiteCookie($name = '', $value =''  , $expires = '' , $path = '', $domain = '', $secure = false, $httponly = false)
		{	
			if($name != ''){
				// 기본 쿠키 방식
				setcookie($name, $value , $expires , $path, $domain, $secure , $httponly);

				// 추가 쿠키 방식 PHP VER >= 7.2
				/*
					$options = array(
						'expires' => $expires,
						'path' => $path,
						'domain' => $domain,
						'secure' => $secure,     // or false
						'httponly' => $httponly,    // or false
						'samesite' => 'None' // None || Lax  || Strict					
					);
					setcookie($name, $value, $options);
				*/
			}
			
			$res = @session_start();
			// IE 브라우저 또는 엣지브라우저 일때는 secure; SameSite=None 을 설정하지 않습니다.
			if( preg_match('/Edge/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || preg_match('~Trident/7.0(; Touch)?; rv:11.0~',$_SERVER['HTTP_USER_AGENT']) ){
				return $res;
			}
			
			$httpsChk = false;
			if(isset($_SERVER['HTTPS'])) {
				if(strtolower($_SERVER['HTTPS']) == 'on') $httpsChk = true;
				else if($_SERVER['HTTPS'] == '1') $httpsChk = true;
			}

			if($httpsChk !== true){ return $res; }
			$headers = headers_list();
			krsort($headers);

			foreach ($headers as $header) {
				//if (!preg_match('~^Set-Cookie: PHPSESSID=~', $header)) continue;
				if (preg_match('~^Set-Cookie: PHPSESSID=~', $header)) {  $_SESSION['SESS_SAME_SITE_COOKIE'] = true;  }
				if (preg_match('/(SameSite=None)/', $header)) continue;
				if (!preg_match('~^Set-Cookie:~', $header)) continue;
				if (preg_match('/=deleted;/i', $header)) continue;
				$header = preg_replace('~; secure(; HttpOnly)?$~', '', $header) . '; secure; SameSite=None';
				header($header, false);
				//break;
			}
			
			// 세션쿠키에 same site 적용이 안됬을 경우 다시한번 생성해 준다.
			if( $_SESSION['SESS_SAME_SITE_COOKIE'] !== true){
				$res = session_regenerate_id();
				samesiteCookie();
			}

			return $res;
		}
	}
	samesiteCookie(); // PHPSESSID 을 위한 처리

	header("Content-Type: text/html; charset=UTF-8");

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

	// 2020-03-09 SSJ :: 장비교체에 따른 소스수정
	if( !(IS_ARRAY($HTTP_POST_VARS) && sizeof($HTTP_POST_VARS)) ){$HTTP_POST_VARS = array();}
	if( !(IS_ARRAY($HTTP_GET_VARS) && sizeof($HTTP_GET_VARS)) ){$HTTP_GET_VARS = array();}
	if( !(IS_ARRAY($HTTP_ENV_VARS) && sizeof($HTTP_ENV_VARS)) ){$HTTP_ENV_VARS = array();}

	$_path_str = dirname(__FILE__);
	include "${_path_str}/config_database.php";
	include "${_path_str}/config_connect.php";

    // -- 웹 취약점 보완 패치 -- 2019-09-16 {
    if( function_exists('escape_string') == false){
        function escape_string($value) {
                if(is_array($value)) return array_map('escape_string', $value);
                else return (isset($value)?addslashes(stripslashes($value)):null);
        }
    }
    // -- 웹 취약점 보완 패치 -- 2019-09-16 }

	include "${_path_str}/var.php";

	// 스킨 var 불러오기
	include "${_path_str}/../pages/var.php";
	include "${_path_str}/../m/var.php";

	include "${_path_str}/lib.func.php";
	include "${_path_str}/lib.qry.php";


	## ***  오류가 날 경우 아래 주석을 풀어 조절하시면 됩니다.
	## *** error_reporting( E_ALL & ~( E_NOTICE | E_USER_NOTICE | E_WARNING | E_COMPILE_WARNING | E_CORE_WARNING | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED ) );
	error_reporting( E_ALL & ~( E_NOTICE | E_USER_NOTICE | E_WARNING | E_COMPILE_WARNING | E_CORE_WARNING | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED ) );


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
	if( $_COOKIE['auth_memberid']) {
		$row_member = info_member($row_setup[ranDsum],$addSum);
	}
	if( $_COOKIE['auth_memberid'] && !$_COOKIE['auth_adminid']) {

		// -- 쿠폰 발급 알람 및 만료된 쿠폰 삭제 ---
		if($row_member[id] && (!preg_match("/totalAdmin/i",$_SERVER["PHP_SELF"]))) { // 2016-11-08 관리자페이지에서 알람끄기 추가 SSJ
			$isAlarm = _MQ("select count(*) as cnt from odtCoupon where coID ='".$row_member[id]."' and coUse ='N' and alarm = 'N'");
			if($isAlarm[cnt]) {
				_MQ_noreturn("update odtCoupon set alarm='Y' where coID='".$row_member[id]."'");
				_MQ_noreturn(" update odtCoupon set coUse='E' where coID='".$row_member[id]."' and coUse='N' and coLimit < CURDATE() ");
				error_loc_msg("/?pn=mypage.coupon.list" , $row_member[id]."님 쿠폰이 발급되었습니다.");
			}
		}
		// -- 쿠폰 발급 알람 및 만료된 쿠폰 삭제 ---

	}
	// - 회원정보 호출 ---

	// 2018-11-12 SSJ :: 메일링URL 설정
	$mailing_url = 'http://'.$_SERVER['SERVER_NAME'];

    // -- 웹 취약점 보완 패치 -- 2019-09-16 {
    if(is_login() === true && !preg_match('/totalAdmin/i', $_SERVER['REQUEST_URI']) && !preg_match('/subAdmin/i', $_SERVER['REQUEST_URI']) && function_exists('UserLoginCheck') == true ) UserLoginCheck(); // 로그인 세션 체크
    // -- 웹 취약점 보완 패치 -- 2019-09-16 }

?>
