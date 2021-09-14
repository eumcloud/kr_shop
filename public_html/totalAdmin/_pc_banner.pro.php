<?PHP
	include "inc.php";

	// - 입력수정 사전처리 ---
	if( $_mode <> "delete"  ) {

		// --사전 체크 ---
		$_loc = nullchk($_loc , "배너위치를 선택하세요");
		$_idx = nullchk($_idx , "구좌(순위)를 선택하시기 바랍니다.");
		$_sdate = nullchk($_sdate , "구간시작일을 선택하시기 바랍니다.");
		$_edate = nullchk($_edate , "구간종료일을 선택하시기 바랍니다.");
//		$_link = str_replace("http://" , "" , $_link); // http:// 제거
		// --사전 체크 ---

		// --이미지 처리 ---
		$_imgname = _PhotoPro( "..".IMG_DIR_BANNER , "_img" ) ; // 배너이미지
		// --이미지 처리 ---


		// --query 사전 준비 ---
		$sque = " 
			b_loc		= '".$_loc."'
			,b_img		= '".$_imgname."'
			,b_link		= '".$_link."'
			,b_target	= '".$_target."'
			,b_view		= '".$_view."'
			,b_title	= '".$_title."'
			,b_idx		= '".$_idx."'
			,b_sdate	= '".$_sdate."'
			,b_edate	= '".$_edate."'
		";
		// --query 사전 준비 ---

	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":
			_MQ_noreturn("insert odtBanner set $sque , b_rdate=now(), b_type='product_category' ");
			$_uid = mysql_insert_id();
			error_loc("_pc_banner.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}&_loc={$_loc}");
			break;


		case "modify":
			_MQ_noreturn(" update odtBanner set  $sque where b_uid='${_uid}' ");
			error_loc("_pc_banner.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}&_loc={$_loc}");
			break;


		case "delete":
			// -- 이미지 삭제 ---
			$r = _MQ("select b_img from odtBanner where b_uid='${_uid}' ");
			if( $r[b_img]) {
				_PhotoDel( "..".IMG_DIR_BANNER , $r[b_img] );
			}
			// -- 이미지 삭제 ---

			_MQ_noreturn("delete from odtBanner where b_uid='$_uid' ");
			error_loc( "_pc_banner.list.php?" . enc('d' , $_PVSC) );
			break;
	}
	// - 모드별 처리 ---

?>