<?PHP

	// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
	@ini_set("precision", "20");


	include_once "../include/inc.php";
	AdminLoginCheck();

	if($app_mode <> "popup") {
		$_Mstring_join = $row_admin['serialnum'].$row_setup['ranDsum'].$_MaddSum;
		if ( !$_COOKIE["auth_adminid"] ||  $_COOKIE['auth_adminid_sess'] <> md5($_Mstring_join) ) {
			samesiteCookie("auth_adminid", "" , -999 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
			samesiteCookie("auth_adminid_sess", "" , -999 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
			error_loc("/totalAdmin/");
		}
	}

	// - 넘길 변수 설정하기 ---
	if(preg_match("/.list.php/i" , $CURR_FILENAME)){
		$_PVS = ""; $ARR_PVS = array(); // 링크 넘김 변수		
		foreach(array_filter($_GET) as $key => $val) { $ARR_PVS[$key] = $val; } // GET먼저 중복걸러내기
		foreach(array_filter($_POST) as $key => $val) { $ARR_PVS[$key] = $val; } // POST나중 중복걸러내기
		foreach( $ARR_PVS as $key => $val) { $_PVS .= "&$key=$val"; }
		$_PVSC = enc('e' , $_PVS);
	}
	// - 넘길 변수 설정하기 ---

?>