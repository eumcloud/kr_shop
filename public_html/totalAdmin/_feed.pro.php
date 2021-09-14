<?PHP
	include "inc.php";


	// - 모드별 처리 ---
	switch( $_mode ){

		case "delete":

			$ft_idx = nullchk($ft_idx , "코드를 입력하시기 바랍니다.");

			// 상품정보 삭제
			_MQ_noreturn("delete from feedTable where ft_idx='{$ft_idx}' ");

			error_loc("_feed.list.php?".enc('d' , $_PVSC));
			break;

		case "update" :


			$ft_idx = nullchk($ft_idx , "코드를 입력하시기 바랍니다.");

			// 상품정보 삭제
			_MQ_noreturn("update feedTable set ft_emailsend = if(ft_emailsend = 'Y' , 'N' , 'Y') where ft_idx='{$ft_idx}' ");

			error_loc("_feed.list.php?".enc('d' , $_PVSC));
			break;


		// 일괄삭제
		case "mass_delete":

			$s_query = " where ft_idx in ('".implode("','" , array_keys($chk_pcode))."') ";

			// 상품삭제
			_MQ_noreturn("delete from feedTable {$s_query} ");

			error_loc("_feed.list.php?".enc('d' , $_PVSC));
			break;


	}
	// - 모드별 처리 ---

	exit;
?>