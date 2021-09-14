<?PHP
	include "inc.php";

	// - 입력수정 사전처리 ---
	if( $_mode <> "delete"  ) {
		// --사전 체크 ---
		//$da_post1 = nullchk($da_post1 , "우편번호 앞자리를 입력하여 주십시요.");
		//$da_post2 = nullchk($da_post2 , "우편번호 뒷자리를 입력하여 주십시요.");
		$da_addr = nullchk($da_addr , "주소를 입력하여 주십시요.");
		$da_price = nullchk($da_price , "추가금액을 입력하여 주십시요.");

		if($da_post1=="" && $da_zone==""){ error_msg("우편번호가 입력되지 않았습니다."); }
		$da_post = implode("-",array($da_post1 , $da_post2));
		// --사전 체크 ---



		// --query 사전 준비 ---
		$sque = " 
			 da_post = '".$da_post."'
			,da_addr = '".$da_addr."'
			,da_price = '".$da_price."'
			,da_zone = '".$da_zone."'
		";
		// --query 사전 준비 ---
	}
	// - 입력수정 사전처리 ---
	
	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":
			_MQ_noreturn("insert odtDeliveryAddprice set $sque , da_rdate=now()");
			$_uid = mysql_insert_id();

			error_loc_msg("_delivery_addprice.form.php?_mode=add&_PVSC=${_PVSC}","추가되었습니다");
			break;


		case "modify":
			_MQ_noreturn(" update odtDeliveryAddprice set $sque where da_uid='${_uid}' ");
			error_loc_msg("_delivery_addprice.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}","수정되었습니다");
			break;


		case "delete":
			_MQ_noreturn("delete from odtDeliveryAddprice where da_uid='$_uid' ");
			error_loc_msg( "_delivery_addprice.list.php?" . enc('d' , $_PVSC) ,"삭제되었습니다");
			break;
	}
	// - 모드별 처리 ---

?>