<?PHP
	include "inc.php";
	
	$sque = " update odtSetup set 
						s_join_auth_use = '".$_join_auth_use."',
						s_join_auth_kcb_code = '".$_join_auth_kcb_code."',
						s_join_auth_type = '".$_join_auth_type."'
					where
						serialnum = 1";

	_MQ_noreturn($sque);

	error_frame_reload("수정되었습니다");

	exit;
?>