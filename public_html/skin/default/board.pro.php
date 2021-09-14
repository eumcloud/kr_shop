<?PHP

	include dirname(__FILE__)."/../../include/inc.php";

	if( in_array( $_mode , array("add" , "modify" , "reply") ) ){

        // --- 스팸방지 ---
        if($row_setup['recaptcha_api']&&$row_setup['recaptcha_secret'] && $recaptcha_action_use == 'Y') {
            // 스팸방지
            $secret = $row_setup['recaptcha_secret'];
            $response = $_POST["g-recaptcha-response"];
            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
            $_action_result = json_decode($verify); # -- 스팸체크 결과
            if($_action_result->success==false) error_alt( "스팸방지를 확인인해 주세요.");
        }

		// --사전 체크 ---
		$bbs_menu = nullchk($_menu , "게시판 코드가 입력되지 않았습니다" , "" , "ALT");
		$bbs_title = nullchk($_title , "제목을 입력해주시기 바랍니다." , "" , "ALT");
		$bbs_content = nullchk($_content , "내용을 입력해주시기 바랍니다." , "" , "ALT"); // nullchk - alert 형식으로 return
		// --사전 체크 ---

        // -- 웹 취약점 보완 패치 -- 2019-09-16 {
        $bbs_content = RemoveXSS($bbs_content);
        // -- 웹 취약점 보완 패치 -- 2019-09-16 }

        //이미지업로드처리
        $_img_name1 = _PhotoPro( dirname(__FILE__)."/../../upfiles/bbs" , "_img1" );
        $_img_name2 = _PhotoPro( dirname(__FILE__)."/../../upfiles/bbs" , "_img2" );
        $_file_name = _FilePro( dirname(__FILE__)."/../../upfiles/bbs" , "_file" );
        $_thumb_name = _PhotoPro( dirname(__FILE__)."/../../upfiles/bbs" , "_thumb" );

		$sque = "
			b_menu		= '". $bbs_menu ."'
			,b_writer	= '". $_writer ."'
			,b_notice	= '". $_notice ."'
			,b_secret	= '". $_secret ."'
			,b_title	= '". $bbs_title ."'
			,b_content	= '". $bbs_content ."'
			,b_img1		= '". $_img_name1 ."'
			,b_img2		= '". $_img_name2 ."'
			,b_file		= '". $_file_name ."'
			,b_thumb	= '". $_thumb_name ."'
			,b_pcode	= '". $_pcode ."'
			,b_sdate	= '". $_sdate ."'
			,b_edate	= '". $_edate ."'
		";
	}

	// 선택글 정보 불어오기 (delete / modify 일 경우 적용됨)
	if( $_uid) {
		$r = _MQ("select * from odtBbs where b_uid = '".$_uid."' ");

		/* ------ 권한 체크 --------*/
		$is_auth = is_admin() || $_COOKIE["auth_request_".$r['b_uid']] || (is_login() && get_userid() == $r['b_inid']) ? true : false;

		// 관리자가 아닌상태에서...
		if(!$is_auth && $_mode <> "reply" ) {
			if($r['b_inid'] <> get_userid()) { error_alt("회원님께서 작성하신 글이 아닙니다."); }
			if($r['b_passwd'] <> $_passwd) { error_msg("비밀번호가 일치하지 않습니다."); }
		}
	}

	switch( $_mode ){

		// 등록
		case "add":
			$que = " insert odtBbs set ".$sque."
				,b_inid			= '" . get_userid() . "'
				,b_writer_type	= '". (is_login() ? "member" : "guest") ."'
				,b_passwd = password('". $_passwd ."')
				,b_rdate		= now() ";
			_MQ_noreturn($que);
			$_uid = mysql_insert_id();

			// shorten url 적용
			$_shorten_url = get_shortURL("http://".$_SERVER["HTTP_HOST"]."/?pn=board.view&_uid=" . $_uid );

			$que = " update odtBbs set b_shorten_url='{$_shorten_url}' where b_uid='".$_uid."' ";
			_MQ_noreturn($que);

			// 게시물 갯수 업데이트
			update_board_post_cnt($bbs_menu);

			error_frame_loc_msg("/?pn=board.list&_menu=".$_menu."&" . enc('d' , $_PVSC), "정상적으로 등록하였습니다.") ;
			break;


		// 수정
		case "modify":

			if( !is_login() ) { // 비회원일 경우 비밀번호 체크
				if( trim($_passwd)=='' ) { error_alt("비밀번호를 입력하세요."); }
				$r = _MQ(" select * from odtBbs where b_uid = '".$_uid."' ");
				if( $_passwd <> $r['b_passwd'] ) { error_alt("비밀번호가 틀렸습니다."); }
			}

			$que = " update odtBbs set ".$sque." where b_uid='".$_uid."' ";
			_MQ_noreturn($que);
			error_frame_loc_msg("/?pn=board.list&_menu=".$_menu."&" . enc('d' , $_PVSC), "정상적으로 수정하였습니다.") ;
			break;


		// 삭제
		case "delete":
			$_menu = $r["b_menu"];

			$que = " delete from odtBbs where b_uid='".$_uid."' ";
			_MQ_noreturn($que);

			// 게시물 갯수 업데이트
			update_board_post_cnt($_menu);

            //이미지파일삭제
            _PhotoDel( dirname(__FILE__)."/../../upfiles/bbs" , $r['b_img1']);
            _PhotoDel( dirname(__FILE__)."/../../upfiles/bbs" , $r['b_img2']);
            _FileDel( dirname(__FILE__)."/../../upfiles/bbs" , $r['b_file']);
            _PhotoDel( dirname(__FILE__)."/../../upfiles/bbs" , $r['b_thumb']);

			error_frame_loc_msg("/?pn=board.list&_menu=".$_menu."&" . enc('d' , $_PVSC), "정상적으로 삭제하였습니다.") ;
			break;


        // 댓글
		case "reply":
			$que = " insert odtBbs set ".$sque."
				,b_inid		= '" . get_userid() . "'
				,b_depth	='2'
				,b_relation	='".$r['b_uid']."'
				,b_rdate	= now()
				,b_passwd = password('". $_passwd ."')
			";
			_MQ_noreturn($que);

			// 게시물 갯수 업데이트
			update_board_post_cnt($bbs_menu);

			error_frame_loc_msg("/?pn=board.list&_menu=".$_menu."&" . enc('d' , $_PVSC), "정상적으로 등록하였습니다.") ;
			break;
	}

?>