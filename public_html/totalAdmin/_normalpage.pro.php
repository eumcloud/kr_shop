<?PHP
include_once("inc.php");


// - 입력수정 사전처리 ---
if(in_array($_mode , array("add" , "modify"))) {

	// --사전 체크 ---
	$_id = nullchk($_id, "페이지아이디를 입력하시기 바랍니다.");
	$_view = nullchk($_view, "페이지 노출을 선택하시기 바랍니다.");
	$_idx = nullchk($_idx, "페이지 순위를 입력하시기 바랍니다.");
	$_title = nullchk($_title, "페이지명을 입력하시기 바랍니다.");
	$_content = nullchk($_content, "페이지 내용을 입력하시기 바랍니다.");
	// --사전 체크 ---

	// --query 사전 준비 ---
	$sque = " 
		np_view = '" . $_view . "',
		np_id = '" . $_id . "',
		np_idx = '" . $_idx . "',
		np_title = '" . $_title . "',
		np_content = '" . $_content . "',
		np_content_m = '" . $_content_m . "'
	"; // LDD005 수정
	// --query 사전 준비 ---

}
// - 입력수정 사전처리 ---



// - 모드별 처리 ---
switch( $_mode ){

	case "add":
		_MQ_noreturn("insert odtNormalPage set {$sque} , np_rdate=now()");
		$_uid = mysql_insert_id();
		error_loc("_normalpage.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
		break;


	case "modify":
		_MQ_noreturn(" update odtNormalPage set {$sque} where np_uid='${_uid}' ");
		error_loc("_normalpage.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
		break;


	case "delete":
		_MQ_noreturn("delete from odtNormalPage where np_uid='{$_uid}' ");
		error_loc("_normalpage.list.php?".enc('d' , $_PVSC));
		break;


	// - 페이지 아이디 체크 ---
	case "idchk":
		$r = _MQ("select count(*) as cnt from odtNormalPage where np_id='". $_id ."' ");
		if($r[cnt] > 0 ) {

			echo "no";//중복 아이디 있음 - 사용불가
		}
		else {

			echo "yes";//중복 아이디 없음 - 사용가능
		}
		exit;
		break;
	// - 페이지 아이디 체크 ---

}
// - 모드별 처리 ---