<?PHP

	include_once( dirname(__FILE__) . "/../../include/inc.php");


    // --- 스팸방지 ---
    if($row_setup['recaptcha_api']&&$row_setup['recaptcha_secret'] && $recaptcha_action_use == 'Y') {
        // 스팸방지
        $secret = $row_setup['recaptcha_secret'];
        $response = $_POST["g-recaptcha-response"];
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
        $_action_result = json_decode($verify); # -- 스팸체크 결과
        if($_action_result->success==false) error_alt( "스팸방지를 확인인해 주세요.");
    }


	// --사전 체크 ---
	$r_menu = nullchk(trim($_menu) , "잘못된 코드입니다. 다시 시도하시기 바랍니다." , "" , "ALT");
	$r_comname = nullchk(trim($_comname) , "상호명이나 이름을 입력해주시기 바랍니다." , "" , "ALT");
	$r_tel = nullchk(trim($_tel) , "연락처를 입력해주시기 바랍니다." , "" , "ALT");
	$r_title = nullchk(trim($_title) , "제목을 입력해주시기 바랍니다." , "" , "ALT");
	$r_content = nullchk(trim($_content) , "내용을 입력해주시기 바랍니다." , "" , "ALT"); // nullchk - alert 형식으로 return
	$r_email = $_email;
	// --사전 체크 ---


	// -- 등록한 첨부파일명 ---
	$_file_name = _FilePro( dirname(__FILE__)."/../../upfiles/normal" , "_file" ) ;
	// -- 등록한 첨부파일명 ---

	$que = "
		insert odtRequest set
			r_comname	= '". $r_comname ."'
			,r_tel		= '". $r_tel ."'
			,r_email	= '". $r_email ."'
			,r_title	= '". $r_title ."'
			,r_content	= '". $r_content ."'
			,r_file		= '". $_file_name ."'
			,r_inid		= '" . get_userid() . "'
			,r_status	= '답변대기'
			,r_rdate	= now()
			,r_menu		= '".$r_menu."'
	";
	_MQ_noreturn($que);

	// 문자 발송
	$sms_to = $r_tel;
//	shop_send_sms($sms_to,"request");

	if(is_login()) // 회원이면 나의 주문내역 페이지로 이동
		error_frame_loc_msg("/?pn=service.partner.form","정상적으로 등록하였습니다.\\n\\n빠른 답변드리도록 노력하겠습니다.\\n\\n감사합니다.") ;
	else
		error_frame_reload("정상적으로 등록하였습니다.\\n\\n빠른 답변드리도록 노력하겠습니다.\\n\\n감사합니다.") ;

?>