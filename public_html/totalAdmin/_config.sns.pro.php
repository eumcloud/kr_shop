<?PHP
	include "inc.php";



	$sque = " update odtSetup set 
							Facebook_id = '". $Facebook_id ."',
							Facebook_pw = '". $Facebook_pw ."',
							twitter_key = '". $twitter_key ."',
							twitter_secret = '". $twitter_secret ."',
							kakao_api = '".$kakao_api."',
							s_google_key = '". implode("§" , array_filter($_google_key)) ."'
							, recaptcha_api = '".$recaptcha_api."'
							, recaptcha_secret = '".$recaptcha_secret."'
						where
						serialnum = 1";
	_MQ_noreturn($sque);

	error_frame_reload("수정되었습니다");


?>