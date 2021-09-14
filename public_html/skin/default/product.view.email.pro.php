<?
	include_once(dirname(__FILE__)."/../../include/inc.php");

	$que = "insert into odtSnsLog set
			sl_pcode	= '".$_POST['pcode']."',
			sl_type		= '".$_POST['type']."',
			sl_ip		= '".$_SERVER['REMOTE_ADDR']."',
			sl_rdate	= now()
	";

	$res = _MQ_noreturn($que);

	$row_product = get_product_info($_POST['pcode']);

	// - 메일발송 ---
	if( mailCheck($_POST['toMail']) ){
		include_once(dirname(__FILE__)."/mail.contents.sns.php"); // 메일 내용 불러오기 ($mailing_content)
		$_title = stripslashes($_POST['toName']."님! " .$_POST['fromName']."님께서 보내신 추천메일입니다.");
		$_title_content = '';
		$_content = get_mail_content($_title, $_title_content, $mailling_content);
		// SSJ: 2017-09-20 발송메일을 보내시는분 메일로 설정
		$row_company['email'] = $_POST['fromMail'];
		mailer( $_POST['toMail'] , $_title , $_content );
	}
	// - 메일발송 ---

	echo 'OK';
?>