<?PHP

	include "inc.php";

	if( in_array( $_mode , array("add" , "modify" , "reply") ) ){

		// --사전 체크 ---
		$_menu = nullchk($_menu , "게시판 선택하시기 바랍니다." );
		$_writer = nullchk($_writer , "작성자 이름을 입력하시기 바랍니다." );
		$_title = nullchk($_title , "제목을 입력하시기 바랍니다." );
		$_content = nullchk($_content , "내용을 입력하시기 바랍니다." );
		// --사전 체크 ---

		$_file_name = _FilePro( "../upfiles/bbs" , "_file" );
		$_thumb_name = _PhotoPro( "../upfiles/bbs" , "_thumb" );

		$sque = " 
			b_menu		= '". $_menu ."'
			,b_writer	= '". $_writer ."'
			,b_pcode	= '". $_pcode ."'
			,b_sdate	= '". $_sdate ."'
			,b_notice	= '". $_notice."'
			,b_secret	= '". $_secret."'
			,b_edate	= '". $_edate ."'
			,b_title	= '". $_title ."'
			,b_content	= '". $_content ."'
			,b_bestview	= '". $_bestview ."'
			,b_thumb	= '". $_thumb_name ."'
			,b_file		= '". $_file_name ."'
			,b_category	= '".$_category."'
		";
	}


	// 정보 추출 (modify / delete)
	if( $_uid ){
		$r = _MQ(" select * from odtBbs where b_uid='".$_uid."' ");
	}

	// - 모드별 처리 ---
	switch( $_mode ){

		// -- 등록 ---
		case "add":
			$que = " insert odtBbs set ".$sque." ,b_inid = '".$row_admin['id']."' ,b_rdate = now() ";
			_MQ_noreturn($que);
			$_uid = mysql_insert_id();

			// --- shorten url 적용 ---
			switch($_menu){
				case "faq": 
					$_url_name = "/?pn=service.faq&_uid=" . $_uid ; 
				break;
				default: 
					$_url_name = "/?pn=board.view&_menu=".$_menu."&_uid=" . $_uid ;
				break;
			}
			//$_shorten_url = get_shortURL("http://".$_SERVER["HTTP_HOST"] . $_url_name ); 
			// --- shorten url 적용 ---
			
			//$que = " update odtBbs set b_shorten_url='{$_shorten_url}'  where b_uid='".$_uid."' ";
			//_MQ_noreturn($que);

			// 게시물 개수 업데이트
			update_board_post_cnt($_menu);

			error_loc("_bbs.post_mng.form.php?_mode=modify&_uid=".$_uid."&_PVSC=".$_PVSC."");
			break;
		// -- 등록 ---



		// -- 수정 ---
		case "modify":

			$que = " update odtBbs set ".$sque." where b_uid='".$_uid."' ";
			_MQ_noreturn($que);

			error_loc("_bbs.post_mng.form.php?_mode=modify&_uid=".$_uid."&_PVSC=".$_PVSC."");
			break;
		// -- 수정 ---



		// -- 삭제 ---
		case "delete":

			$r = _MQ(" select * from odtBbs where b_uid='".$_uid."' ");
			_FileDel( "../upfiles/bbs" , $r['b_file']);
			_PhotoDel( "../upfiles/bbs" , $r['b_thumb']);
			
			_MQ_noreturn("delete from odtBbs where b_uid='".$_uid."' ");

			// 게시물 개수 업데이트
			update_board_post_cnt($r['b_menu']);

			error_loc("_bbs.post_mng.list.php?".enc('d' , $_PVSC ));
			break;
		// -- 삭제 ---


		// -- 삭제 ---
		case "select_delete":

			foreach($chk_uid as $_uid => $val) {
				if($val == "Y") { 
					$r = _MQ(" select * from odtBbs where b_uid='".$_uid."' ");
					_FileDel( "../upfiles/bbs" , $r['b_file']);
					_PhotoDel( "../upfiles/bbs" , $r['b_thumb']);
					_MQ_noreturn("delete from odtBbs where b_uid='".$_uid."' "); 
				}
			}

			// 게시물 개수 업데이트
			update_board_post_cnt();

			error_loc("_bbs.post_mng.list.php?".enc('d' , $_PVSC ));
			break;
		// -- 삭제 ---


        // --- 댓글 ---
		case "reply":
			$que = " insert odtBbs set ".$sque." , b_inid = '" . $row_admin['id'] . "' , b_depth='2' , b_relation='".$r['b_uid']."'  ,b_rdate = now() ";
			_MQ_noreturn($que);
			$_uid = mysql_insert_id();

			// 게시물 개수 업데이트
			update_board_post_cnt($_menu);

			error_loc("_bbs.post_mng.form.php?_mode=modify&_uid=".$_uid."&_PVSC=".$_PVSC."");
			break;
        // --- 댓글 ---

	}
	// - 모드별 처리 ---
	exit;
?>