<?PHP

	include "inc.php";

	if( in_array( $_mode , array("add" , "modify") ) ){

		// --사전 체크 ---
		$_uid = nullchk($_uid , "게시판 아이디를 입력하시기 바랍니다." );
		$_name = nullchk($_name , "게시판 이름을 입력하시기 바랍니다." );
		$_file_size_limit = rm_str($_file_size_limit);
		// --사전 체크 ---

		$sque = " 
			bi_name				= '".$_name ."'
			,bi_view			= '".$_view ."'
			,bi_list_type		= '".$_list_type ."'
			,bi_auth_list		= '".$_auth_list."'
			,bi_auth_view		= '".$_auth_view ."'
			,bi_auth_write		= '".$_auth_write ."'
			,bi_auth_reply		= '".$_auth_reply ."'
			,bi_auth_comment	= '".$_auth_comment ."'
			,bi_listmaxcnt		= '".$_listmaxcnt."'
			,bi_newicon_view	= '".$_newicon_view."'
			,bi_comment_use		= '".$_comment_use."'
			,bi_file_upload_use	= '".$_file_upload_use."'
			,bi_file_size_limit	= '".$_file_size_limit."'
			,bi_secret_use		= '".$_secret_use."'
			,bi_html_header		= '".$_html_header."'
			,bi_html_footer		= '".$_html_footer."'
		";
	}


	// 정보 추출 (modify / delete)
	if( $_uid ){
		$r = _MQ(" select * from odtBbsInfo where bi_uid='".$_uid."' ");
	}


	// - 모드별 처리 ---
	switch( $_mode ){

		// -- 등록 ---
		case "add":
			$que = " insert odtBbsInfo set ".$sque.", bi_uid='".$_uid."'";
			_MQ_noreturn($que);

			error_loc("_bbs.board.form.php?_mode=modify&_uid=".$_uid."&_PVSC=".$_PVSC."");
			break;
		// -- 등록 ---


		// -- 수정 ---
		case "modify":

			$que = " update odtBbsInfo set ".$sque." where bi_uid='".$_uid."' ";
			_MQ_noreturn($que);

			error_loc("_bbs.board.form.php?_mode=modify&_uid=".$_uid."&_PVSC=".$_PVSC."");
			break;
		// -- 수정 ---

		// -- 삭제 ---
		case "delete":
			if(preg_match("/^notice$|^event$|^faq$/",$_uid)) {
				error_msg($r[bi_name]." 게시판은 삭제 할 수 없는 게시판입니다.");
			}

			_MQ_noreturn("delete from odtBbsInfo where bi_uid='".$_uid."' ");
			error_loc("_bbs.board.list.php?".enc('d' , $_PVSC ));
			break;
		// -- 삭제 ---

		// -- id 중복체크 
		case "duplication_check":
			$r = _MQ(" select count(*) as cnt from odtBbsInfo where bi_uid='".$_uid."' ");
			if($r[cnt] < 1) { echo "true"; } else { echo "false"; }

			break;
	}
	// - 모드별 처리 ---
	exit;
?>