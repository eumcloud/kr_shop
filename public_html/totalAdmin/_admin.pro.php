<?PHP

	include "inc.php";

	// - 입력수정 사전처리 ---
	if( in_array($_mode , array("add" , "modify"))) {

		// --사전 체크 ---
		$id = nullchk($id , "쇼핑몰관리자아이디를 입력하세요");
		if( $_mode == "add"  ) {
			$passwd = nullchk($passwd , "비밀번호를 입력하세요");
			$repasswd = nullchk($repasswd , "비번확인을 입력하세요");
		}
		if($passwd <> $repasswd) { 
			error_msg("비밀번호가 다릅니다.");
		}
		$name = nullchk($name , "쇼핑몰관리자명을 입력하세요");
		$htel = nullchk($htel , "휴대폰번호를 입력하세요");
		$email = nullchk($email , "이메일을 입력하세요");
		// --사전 체크 ---

		// --query 사전 준비 ---
		$sque = " 
			 id='{$id}'
			,name='{$name}'
			,htel='{$htel}'
			,email='{$email}'
		";
		if($passwd == $repasswd && $passwd) { 
			$sque .= "
				,passwd = password('$passwd')
				,repasswd='{$repasswd}'
			";
		}
		// --query 사전 준비 ---
	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":

			_MQ_noreturn("insert odtAdmin set $sque , inputDate='". time() ."' ");
			$serialnum = mysql_insert_id();

			// 기본메뉴 권한 추가 LDD009
			_MQ_noreturn("insert into m_menu_set set m15_id = '{$id}', m15_code1 = '01', m15_vkbn = 'Y', m15_skbn = 'Y', m15_dkbn = 'Y', m15_udate = now(), m15_uid = '{$row_admin['id']}' ");
			_MQ_noreturn("insert into m_menu_set set m15_id = '{$id}', m15_code1 = '01', m15_code2 = '01', m15_vkbn = 'Y', m15_skbn = 'Y', m15_dkbn = 'Y', m15_udate = now(), m15_uid = '{$row_admin['id']}' ");

			error_loc("_admin.form.php?_mode=modify&serialnum=${serialnum}&_PVSC=${_PVSC}");
			break;


		case "modify":
			_MQ_noreturn(" update odtAdmin set  $sque , modifyDate='". time() ."' where serialnum='${serialnum}' ");

			//_MQ_noreturn(" update m_menu_set set m15_id = '".$id."' where m15_id = '".$curr_id."' ");
			//_MQ_noreturn(" update m_menu_set set m15_uid = '".$id."' where m15_uid = '".$curr_id."' ");

			error_loc("_admin.form.php?_mode=modify&serialnum=${serialnum}&_PVSC=${_PVSC}");
			break;


		case "delete":

			// 메뉴권한 삭제 LDD009
			$FindAdmin = _MQ(" select `id` from odtAdmin where serialnum='{$serialnum}' ");
			_MQ_noreturn("delete from m_menu_set where m15_id = '{$FindAdmin['id']}' ");

			_MQ_noreturn("delete from odtAdmin where serialnum='$serialnum' ");
			error_loc( "_admin.list.php?" . enc('d' , $_PVSC) );
			break;
	}
	// - 모드별 처리 ---

?>