<?PHP
	include dirname(__FILE__)."/../../include/inc.php";

	// 받은 변수
	//		- _mode ==> add
	//		- _buid
	//		- bbs_talk_content

	// 게시판 댓글 갯수 업데이트
	function bbs_cnt ( $buid ){
		$r = _MQ("select count(*) as cnt from odtBbsComment where bt_buid='" . $buid . "' ");
		_MQ_noreturn(" update odtBbs set b_talkcnt = '".$r[cnt]."' where b_uid='". $buid ."' ");
	}

	if( in_array($_mode , array("add","delete")) ){
		member_chk();// 로그인 체크는 등록 / 삭제시에만 적용됨
	}

	// 모드별 처리
	switch( $_mode ){

		// - 댓글 등록 ---
		case "add":
			$_buid = nullchk($_buid , "잘못된 접근입니다." , "" , "ALT");
			$bbs_talk_content = nullchk($bbs_talk_content , "댓글을 등록해주시기 바랍니다." , "" , "ALT");
			$que = "
				insert odtBbsComment set
					bt_buid		= '". $_buid ."'
					,bt_rdate	= now()
					,bt_inid	= '".get_userid()."'
					,bt_writer	= '".$row_member['name']."'
					,bt_content	= '".$bbs_talk_content."'
			";
			_MQ_noreturn($que);

			// 게시판 댓글 갯수 업데이트
			bbs_cnt ( $_buid );

			break;
		// - 댓글 등록 ---

		// - 댓글 삭제 ---
		case "delete":
			$uid = nullchk($uid , "잘못된 접근입니다." , "" , "ALT");

			// 등록 댓글 확인
			$r = _MQ(" select bt_buid , bt_inid from odtBbsComment where bt_uid = '".$uid."' ");
			if( $r[bt_inid] <> get_userid() ) { error_alt("본인이 등록하신 댓글이 아닙니다."); }

			$que = " delete from odtBbsComment where bt_uid = '".$uid."' and bt_inid='".get_userid()."' ";
			_MQ_noreturn($que);

			// 게시판 댓글 갯수 업데이트
			bbs_cnt ( $r[bt_buid] );

			break;
		// - 댓글 삭제 ---


		// - 댓글 보기 ---
		case "view":
			$_buid = nullchk($_buid , "잘못된 접근입니다." , "" , "ALT");

			// 댓글 - cnt 추출
			$sr = _MQ(" select count(*) as cnt_bt from odtBbsComment where bt_buid='".$_buid."' ");
			$bbs_talk_cnt = $sr['cnt_bt']; // 댓글 총수

			// - 댓글 목록 ---
			echo "<div class='comment_list'><ul>";

			$btr = _MQ_assoc("  select * from odtBbsComment  where bt_buid='{$_buid}' order by bt_uid desc ");
			foreach( $btr as $k=>$v ){
				$del_button = ($v[bt_inid] == get_userid() && is_login()) ? "<input type='button' onclick='bbs_talk_del(".$_buid." , ".$v[bt_uid].")' class='btn_delete' title='댓글삭제' value=''/>" : "";
				echo "
					<li><span class='name'>".$v[bt_writer]."</span><span class='id'>".$v[bt_inid]."</span><span class='bar'></span><span class='date'>".date('y.m.d H:i',strtotime($v[bt_rdate]))."</span>".$del_button."<div class='conts'>".nl2br(stripslashes(htmlspecialchars($v[bt_content])))."</div></li>
				";
			}
			echo "</ul></div>";
			// - 댓글 목록 ---
			break;
		// - 댓글 보기 ---

	}

	exit;
?>