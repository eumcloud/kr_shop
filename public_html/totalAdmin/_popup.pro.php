<?PHP
	include "inc.php";

	// - 입력수정 사전처리 ---
	if( $_mode <> "delete"  ) {

		// --사전 체크 ---
		$_idx = nullchk($_idx , "순위를 선택하시기 바랍니다.");
		$_sdate = nullchk($_sdate , "시작일을 선택하시기 바랍니다.");
		$_edate = nullchk($_edate , "종료일을 선택하시기 바랍니다.");
		//$_link = str_replace("http://" , "" , $_link); // http:// 제거
		// --사전 체크 ---

		// --이미지 처리 ---
		$_imgname = _PhotoPro( "..".IMG_DIR_BANNER , "_img" ) ; // 이미지
		// --이미지 처리 ---


		// --query 사전 준비 ---
		$sque = " 
			 p_img = '{$_imgname}'
			,p_link='{$_link}'
			,p_target='{$_target}'
			,p_view='{$_view}'
			,p_title='{$_title}'
			,p_idx = '".$_idx."'
			,p_left = '".$_left."'
			,p_top = '".$_top."'
			,p_sdate = '".$_sdate."'
			,p_edate = '".$_edate."'
		";
		// --query 사전 준비 ---

	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":
			_MQ_noreturn("insert odtPopup set $sque , p_rdate=now()");
			$_uid = mysql_insert_id();
			error_loc("_popup.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;


		case "modify":
			_MQ_noreturn(" update odtPopup set  $sque where p_uid='${_uid}' ");
			error_loc("_popup.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;


		case "delete":
			// -- 이미지 삭제 ---
			$r = _MQ("select p_img from odtPopup where p_uid='${_uid}' ");
			if( $r[p_img]) {
				_PhotoDel( "..".IMG_DIR_BANNER , $r[p_img] );
			}
			// -- 이미지 삭제 ---

			_MQ_noreturn("delete from odtPopup where p_uid='$_uid' ");
			error_loc( "_popup.list.php?" . enc('d' , $_PVSC) );
			break;
	}
	// - 모드별 처리 ---

?>