<?PHP
	include "./inc.php";

	// - 입력수정 사전처리 ---
	if( $_mode <> "delete"  ) {

		// --사전 체크 ---
		$_type = nullchk($_type , "아이콘유형를 선택하세요");
		$_idx = nullchk($_idx , "순위를 입력하시기 바랍니다.");
		// --사전 체크 ---

		// --이미지 처리 ---
		$_imgname = _PhotoPro( "../upfiles/icon" , "_img" ) ; // 아이콘이미지
		// --이미지 처리 ---


		// --query 사전 준비 ---
		$sque = " 
			 pi_type='{$_type}'
			,pi_img = '{$_imgname}'
			,pi_title='{$_title}'
			,pi_idx = '".$_idx."'
		";
		// --query 사전 준비 ---

	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":
			_MQ_noreturn("insert odtProductIcon set $sque , pi_rdate=now()");
			$_uid = mysql_insert_id();
			error_loc("_product_icon.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}&_type={$_type}");
			break;


		case "modify":
			_MQ_noreturn(" update odtProductIcon set  $sque where pi_uid='${_uid}' ");
			error_loc("_product_icon.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}&_type={$_type}");
			break;


		case "delete":
			// -- 이미지 삭제 ---
			$r = _MQ("select pi_img from odtProductIcon where pi_uid='${_uid}' ");
			if( $r[pi_img]) {
				_PhotoDel( "../upfiles/icon" , $r[pi_img] );
			}
			// -- 이미지 삭제 ---

			_MQ_noreturn("delete from odtProductIcon where pi_uid='$_uid' ");
			error_loc( "_product_icon.list.php?" . enc('d' , $_PVSC) );
			break;
	}
	// - 모드별 처리 ---

?>