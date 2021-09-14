<?PHP

	// 080 수신거부 설정 적용
	include_once("inc.php");

	$que = "
		update odtSetup set 
			s_deny_tel = '". $_deny_tel ."', 
			s_deny_use = '". $_deny_use ."'
		where 
			serialnum = 1
	"; 
	_MQ_noreturn($que);

	error_loc("/totalAdmin/_addons.php?pass_menu=080deny/_receipt.form");

?>