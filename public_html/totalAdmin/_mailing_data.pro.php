<?PHP
	include "inc.php";


	// - 입력수정 사전처리 ---
	if( in_array( $_mode , array("add" , "modify") ) ) {

		// --사전 체크 ---
		$_title = nullchk($_title , "메일링 제목을 입력하시기 바랍니다.");
		$_content = nullchk($_content , "메일링 내용을 입력하시기 바랍니다.");
		// --사전 체크 ---

        // --query 사전 준비 ---
        $sque = "
             md_title = '". $_title ."'
            ,md_content = '". $_content ."'
            ,md_adchk = '".$_adchk."'
        ";
        // --query 사전 준비 ---       

	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":
			$que = " insert odtMailingData set $sque , md_rdate = now() ";
			_MQ_noreturn($que);
			$_uid = mysql_insert_id();
			error_loc("_mailing_data.form.php?_mode=modify&_uid=". $_uid . "&_PVSC=${_PVSC}");
			break;



		case "modify":
			$que = " update odtMailingData set $sque where md_uid='{$_uid}' ";
			_MQ_noreturn($que);
			error_loc("_mailing_data.form.php?_mode=${_mode}&_uid=". $_uid . "&_PVSC=${_PVSC}");
			break;



		case "delete":
			_MQ_noreturn("delete from odtMailingData where md_uid='{$_uid}' ");
			error_loc("_mailing_data.list.php?".enc('d' , $_PVSC ));
			break;

	}
	// - 모드별 처리 ---

?>