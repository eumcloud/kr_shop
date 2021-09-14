<?PHP

	include "inc.php";



	// -사전 체크 ---
	$_type = nullchk($_type , "잘못된 접근입니다.");
	if(sizeof($chk_pcode) == 0 ) {
		error_msg("잘못된 접근입니다.");
	}
	// -사전 체크 ---


	switch($_mode){



		// - 메인상품설정 추가 ---
		case "add":
			foreach($chk_pcode as $k=>$v){
				if($v){
					$que = "
						insert odtProductMainSetup set
							pms_type = '" . $_type . "',
							pms_pcode = '" . $v . "',
							pms_idx = '" . $chk_idx[$v] . "',
							pms_rdate = now()
					";
					//echo $que . "<hr>";
					_MQ_noreturn($que);
				}
			}
			break;
		// - 메인상품설정 추가 ---




		// - 메인상품설정 수정 ---
		case "modify":
			foreach($chk_pcode as $k=>$v){
				if($v){
					$que = "
						update odtProductMainSetup set
							pms_idx = '" . $chk_idx[$v] . "'
						where 
							pms_type = '" . $_type . "' and
							pms_pcode = '" . $v . "'
					";
					_MQ_noreturn($que);
				}
			}
			break;
		// - 메인상품설정 수정 ---





		// - 메인상품설정 삭제 ---
		case "delete":
			$que = " delete from odtProductMainSetup where pms_type = '" . $_type . "' and pms_pcode in ( '" . implode("' , '" , array_values($chk_pcode)) . "' ) ";
			_MQ_noreturn($que);
			break;
		// - 메인상품설정 삭제 ---



	}

	error_loc("_product_main_setup.list.php?_type=" . $_type );

?>