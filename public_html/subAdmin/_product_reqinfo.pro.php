<?PHP


	include "inc.php"; // 기본 정보 include



	// 사전처리
	if( in_array($_mode , array("add" , "modify"))){

		// --사전 체크 ---
		$_key = nullchk($_key , "항목명을 입력하시기 바랍니다.");
//		$_value = nullchk($_value , "항목내용을 입력하시기 바랍니다.");
		// --사전 체크 ---

		// --query 사전 준비 ---
		$sque = " 
			pri_pcode = '". $pass_code ."' ,
			pri_key = '". $_key ."' ,
			pri_value = '". $_value ."' 
		";
		// --query 사전 준비 ---

	}



	switch($_mode){


		// - 추가 ---
		case "add":
			_MQ_noreturn("insert odtProductReqInfo set $sque , pri_rdate=now()"); // DB 처리
			error_loc("_product_reqinfo.popup.php?pass_code=" . $pass_code); //페이지 이동
			break;



		// - 수정 ---
		case "modify":

			foreach($_key as $k=>$v) {
				$sque = " 
					pri_pcode = '". $pass_code ."' ,
					pri_key = '". $_key[$k] ."' ,
					pri_value = '". $_value[$k] ."' 
				";
				_MQ_noreturn("update odtProductReqInfo set $sque where pri_uid='".$k."' ");// DB 처리
			}
			error_loc("_product_reqinfo.popup.php?pass_code=" . $pass_code); //페이지 이동
			break;



		// - 삭제 ---
		case "delete":
			_MQ_noreturn("delete from odtProductReqInfo where pri_uid='".$_uid."' ");// DB 처리
			error_loc("_product_reqinfo.popup.php?pass_code=" . $pass_code); //페이지 이동
			break;

	}

?>