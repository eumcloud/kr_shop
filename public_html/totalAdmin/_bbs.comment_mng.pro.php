<?PHP

	include "inc.php";

	if( in_array( $_mode , array("add" , "modify") ) ){

		// --사전 체크 ---
		$_writer = nullchk($_writer , "작성자 이름을 입력하시기 바랍니다." );
		$_content = nullchk($_content , "댓글 내용을 입력하시기 바랍니다." );
		// --사전 체크 ---

		$_content = mysql_real_escape_string($_content);

		$sque = " 
				bt_writer = '". $_writer ."'
				,bt_content = '". $_content ."'
		";
	}


	// 정보 추출 (modify / delete)
	if( $_uid ){
		$r = _MQ(" select * from odtBbsComment where bt_uid='{$_uid}' ");
	}



	// - 모드별 처리 ---
	switch( $_mode ){

		// -- 등록 ---
		case "add":
			$que = " insert odtBbsComment set {$sque} ,bt_inid = '".$row_admin[id]."' ,bt_rdate = now() ";
			_MQ_noreturn($que);
			$_uid = mysql_insert_id();

			error_loc("_bbs.comment_mng.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;
		// -- 등록 ---



		// -- 수정 ---
		case "modify":

			$que = " update odtBbsComment set {$sque} where bt_uid='{$_uid}' ";
			_MQ_noreturn($que);

			error_loc("_bbs.comment_mng.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;
		// -- 수정 ---



		// -- 삭제 ---
		case "delete":
			
			_MQ_noreturn("delete from odtBbsComment where bt_uid='{$_uid}' ");
			error_loc("_bbs.comment_mng.list.php?".enc('d' , $_PVSC ));
			break;
		// -- 삭제 ---


		// -- 삭제 ---
		case "select_delete":

			foreach($chk_uid as $_uid => $val) {
				if($val == "Y") _MQ_noreturn("delete from odtBbsComment where bt_uid='{$_uid}' ");
			}

			error_loc("_bbs.comment_mng.list.php?".enc('d' , $_PVSC ));
			break;
		// -- 삭제 ---


	}
	// - 모드별 처리 ---
	exit;
?>