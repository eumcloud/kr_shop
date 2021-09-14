<?PHP
	include "inc.php";
	
	if($_mail_checking != 1) {
		error_alt("원데이넷 아이디 확인을 통해 인증하시기 바랍니다.");
		exit;
	}

	$sque = " update odtSetup set 
						amail_id = '".$amail_id."',
						amail_pw = '".$amail_pw."',
						amail_chk = '".$amail_chk."'
						where
						serialnum = 1";
	_MQ_noreturn($sque);

	error_frame_reload("수정되었습니다");

?>