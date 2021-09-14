<?PHP
	include "inc.php";


	// - 모드별 처리 ---
	switch( $_mode ){


		case "delete":

			$_uid = nullchk($_uid , "잘못된 접근입니다.");

			// 상품정보 삭제
			_MQ_noreturn("delete from odtMember080Deny where md_uid='{$_uid}' ");

			error_loc("/totalAdmin/_addons.php?pass_menu=080deny/_member_080deny.list&".enc('d' , $_PVSC));
			break;


		// 일괄삭제
		case "mass_delete":

			$s_query = " where md_uid in ('".implode("','" , array_keys($chk_pcode))."') ";

			// 상품삭제
			_MQ_noreturn("delete from odtMember080Deny {$s_query} ");

			error_loc("/totalAdmin/_addons.php?pass_menu=080deny/_member_080deny.list&".enc('d' , $_PVSC));
			break;


	}
	// - 모드별 처리 ---

	exit;

?>