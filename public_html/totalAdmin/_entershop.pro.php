<?PHP

	include "inc.php";

	// - 입력수정 사전처리 ---
	if( in_array($_mode , array("add" , "modify"))) {

		// --사전 체크 ---
		if( $_mode == "add"  ) {
			$id = nullchk(trim($id) , "아이디를 입력하세요");
			$passwd = nullchk($passwd , "비밀번호를 입력하세요");
			$repasswd = nullchk($repasswd , "비번확인을 입력하세요");
		}
		if($passwd <> $repasswd) { 
			error_msg("비밀번호가 다릅니다.");
		}
		$cName = nullchk($cName , "공급업체명을 입력하시기 바랍니다.");
		$address = nullchk($address , "주소를 입력하시기 바랍니다.");
		$email = nullchk($email , "이메일을 입력하시기 바랍니다.");
		$tel = nullchk($tel , "전화번호를 입력하시기 바랍니다.");

		// 좌표처리 LMH003
		if( $address && !$com_mapx && !$com_mapy ) {
			$com_map = get_mapcoordinates($address);
			$ex = explode("," , $com_map);
			$com_mapx = $ex[0];
			$com_mapy = $ex[1];
		}

		$ex_tel = explode("-" , tel_format($tel));
		$tel1 = $ex_tel[0];
		$tel2 = $ex_tel[1];
		$tel3 = $ex_tel[2];
		$ex_htel = explode("-" , tel_format($htel));
		$htel1 = $ex_htel[0];
		$htel2 = $ex_htel[1];
		$htel3 = $ex_htel[2];
		$ex_ofax = explode("-" , tel_format($ofax));
		$ofax1 = $ex_ofax[0];
		$ofax2 = $ex_ofax[1];
		$ofax3 = $ex_ofax[2];
		// --사전 체크 ---

		// --query 사전 준비 ---
		$sque = " 
			bannder			='{$bannder}',
			cName			='{$cName}',
			cNumber			='{$cNumber}',
			ceoName			='{$ceoName}',
			address			='{$address}',
			cItem1			='{$cItem1}',
			cItem2			='{$cItem2}',
			name			='{$name}',
			email			='{$email}',
			tel1			='{$tel1}', tel2='{$tel2}', tel3='{$tel3}',
			htel1			='{$htel1}', htel2='{$htel2}', htel3='{$htel3}',
			ofax1			='{$ofax1}', ofax2='{$ofax2}', ofax3='{$ofax3}',
			homepage		='{$homepage}',
			account_bank	= '". $account_bank ."',
			account_deposit	= '". $account_deposit ."',
			account_name	= '". $account_name ."',
			com_mapx		='{$com_mapx}',
			com_mapy		='{$com_mapy}'
		";

		// JJC003 - 묶음배송 관련
		$sque .= "
			, com_delprice		='" . rm_str($com_delprice) . "'
			, com_delprice_free	='" . rm_str($com_delprice_free) . "'
			, com_del_company	='" . $com_del_company . "'
		";

		// 추가배송비 설정 추가 2017-04-16 :: SSJ
		$sque .= "
			, com_del_addprice_use      ='" . $_del_addprice_use . "'
			, com_del_addprice_use_normal   ='" . $_del_addprice_use_normal . "'
			, com_del_addprice_use_unit ='" . $_del_addprice_use_unit . "'
			, com_del_addprice_use_free ='" . $_del_addprice_use_free . "'
		";

		if($passwd == $repasswd && $passwd) { 
			$srow = _MQ("SELECT password('$passwd') as pw ");
			$passwd = $srow[pw];
			$sque .= "
				,passwd		= '{$passwd}'
				,repasswd	='{$repasswd}'
			";
		}
		// --query 사전 준비 ---
	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":
			_MQ_noreturn("insert odtMember set $sque , userType= 'C', id='" . $id . "', signdate='". time() ."' ");
			$serialnum = mysql_insert_id();
			error_loc("_entershop.form.php?_mode=modify&serialnum=${serialnum}&_PVSC=${_PVSC}");
			break;


		case "modify":
			_MQ_noreturn(" update odtMember set  $sque , modifydate='". time() ."' where serialnum='${serialnum}' ");
			error_loc("_entershop.form.php?_mode=modify&serialnum=${serialnum}&_PVSC=${_PVSC}");
			break;


		case "delete":
			_MQ_noreturn("delete from odtMember where serialnum='$serialnum' ");
			error_loc( "_entershop.list.php?" . enc('d' , $_PVSC) );
			break;
	}
	// - 모드별 처리 ---

?>