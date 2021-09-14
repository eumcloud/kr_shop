<?PHP

	include "inc.php";

	if( in_array($_mode , array("add" , "modify"))){

		// --사전 체크 ---
		$_appId = $row_admin[id];// 고정
		//$_menuIdx = nullchk($_menuIdx , "순위를 입력하시기 바랍니다.");
		if(!$_menuIdx) { $_menuIdx = 99; }
		$_menuName = nullchk($_menuName , "메뉴명을 입력하시기 바랍니다.");
		$_menuLink = nullchk($_menuLink , "메뉴링크를 입력하시기 바랍니다.");
		// --사전 체크 ---

		// --query 사전 준비 ---
		$sque = " 
			 fm_appId = '". $_appId ."'
			,fm_menuName = '". $_menuName ."'
			,fm_menuLink = '". $_menuLink ."'
			,fm_menuIdx = '". $_menuIdx ."'
		";
		// --query 사전 준비 ---

	}

	switch($_mode){
		// - 추가 ---
		case "add":
			_MQ_noreturn("insert odtFavmenu set $sque , fm_rdate=now()");
			error_loc("_favmenu.form.php");
			break;

		// - 수정 ---
		case "modify":
			_MQ_noreturn("update odtFavmenu set $sque where fm_uid='".$_uid."' ");
			error_loc("_favmenu.form.php");
			break;

		// - 삭제 ---
		case "delete":
			_MQ_noreturn("delete from odtFavmenu where fm_uid='".$_uid."' ");
			error_loc("_favmenu.form.php");
			break;
	}

?>