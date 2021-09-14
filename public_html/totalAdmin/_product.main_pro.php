<?PHP

	include "inc.php";

	// -- odtSetup 적용 ---
	$que = "
		update odtSetup set 
			s_main_hot_title	= '".$s_main_hot_title."', 
			s_main_new_title	= '".$s_main_new_title."',
			s_main_close_title	= '".$s_main_close_title."', 
			s_main_close_day	= '".rm_str($s_main_close_day)."',
			s_main_close_cnt	= '".rm_str($s_main_close_cnt)."'
		where 
			serialnum = 1
	";
	_MQ_noreturn($que);

	error_loc("_product.main_form.php");
	exit;


?>