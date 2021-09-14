<?PHP
	include "inc.php";
 	
 	// LMH004
	$que = "update odtSetup set 
					P_KBN					= '". trim($P_KBN) ."', 
					P_ID					= '" . trim($P_ID) . "', 
					P_SID					= '" . trim($P_SID) ."', 
					P_KEY					= '" . trim($P_KEY) ."', 
					P_PG_ENC_KEY			= '" . trim($P_PG_ENC_KEY) ."',
					P_PG_PRO_TYPE			= '" . trim($P_PG_PRO_TYPE) ."',
					P_MODE					= '" . trim($P_MODE) ."',
					P_PW					= '" . trim($P_PW) ."',
					P_SKBN					= '" . trim($P_SKBN) ."',
					P_V_DATE				= '" . trim($_P_V_DATE) ."',
					P_B_DATE				= '" . trim($_P_B_DATE) ."',
					s_view_escrow_join_info	= '".($s_view_escrow_join_info == "Y" ? "Y" : "N")."',
					auto_cancel				= '".$auto_cancel."'

                    , P_I_TYPE = '".trim($P_I_TYPE)."'
                    , P_SKEY = '".trim($P_SKEY)."'
					, P_SID_SKEY = '".trim($P_SID_SKEY)."'

                    , P_L_TYPE = '".trim($P_L_TYPE)."'

				where serialnum = 1";
	_MQ_noreturn($que);


	// 무통장입금계좌 입력.
	foreach( $bankname as $k=>$v ) {
		// 추가
		if($k == "add" ) {
			if($bankname[$k] && $banknum[$k] && $name[$k]) {
				$sque = "
					insert odtBank set 
						bankname = '". $bankname[$k] ."',
						banknum = '". $banknum[$k] ."',
						name = '". $name[$k] ."'
				";
				_MQ_noreturn($sque);
			}
		}
		else {
			//수정
			if(trim($bankname[$k]) && trim($banknum[$k]) && trim($name[$k])){
				$sque = "
					update odtBank set 
						bankname = '". $bankname[$k] ."',
						banknum = '". $banknum[$k] ."',
						name = '". $name[$k] ."'
					where serialnum = '". $k ."'
				";
			}
			//삭제
			else {
				$sque = "delete from odtBank where serialnum = '". $k ."'";
			}
			_MQ_noreturn($sque);
		}
	}




	error_loc("_config.pg.form.php");
	exit;
?>