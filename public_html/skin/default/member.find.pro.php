<?PHP

	include_once(dirname(__FILE__)."/../../include/inc.php");

	$_result = 'OK';

	// - 모드별 처리 ---
	switch( $_mode ){

		case "id":
			// -- 회원 확인 ---
			$r = _MQ("select * from odtMember where name='${find_id_name}' and concat(htel1,htel2,htel3)='".str_replace("-","",$find_id_tel)."' ");
			if( sizeof($r) == 0 ) {
				$_txt = "회원 정보를 찾을 수 없습니다. 다시 시도하시기 바랍니다.";
				$_result = 'FAIL';
			}
			else {
				$_id = substr($r[id],0,strlen($r[id])-3)."***";
			}
			echo json_encode(array('result'=>$_result,'id'=>$_id,'text'=>$_txt));
			break;


		case "pw":
			// -- 회원 확인 ---
			$r = _MQ("select * from odtMember where id='${find_pw_id}' and email='{$find_pw_email}' ");
			if( sizeof($r) == 0 ) {
				$_txt = "회원 정보를 찾을 수 없습니다. 다시 시도하시기 바랍니다.";
				$_result = 'FAIL';
			}

			// 임시 비밀번호 발급 및 수정
			$tmp_pw = "";
			for( $i=0; $i<6 ; $i++ ){
				if( rand(1,2) == 1 ) { // 숫자
					$tmp_pw .= rand(0,9);
				}
				else { // 영문
					$tmp_pw .= chr(rand(97,122));
				}
			}
			_MQ_noreturn(" update odtMember set passwd=password('" . $tmp_pw . "') where id='$r[id]'");

			// -- 변수 준비
			$_title = "[".$row_setup['site_name']."] 임시 비밀번호를 보내드립니다.";
			$_title_text = "임시 비밀번호를 보내드립니다.";
			$mailing_url = "http://".$_SERVER[HTTP_HOST];
			$mailling_content = "

			<!-- 메일의 전달사항 -->
			<div style=\"background:#f1f1f1; color:#666; font-family:'나눔고딕','돋움'; font-size:17px; text-align:center; line-height:1.5; padding:30px 20px; letter-spacing:-1px; border-bottom:1px solid #ddd\">
				<b style=\"font-family:'나눔고딕','돋움'; color:#000; font-weight:600\">".$r[name]."님!</b> 요청하신 로그인 정보를 알려드립니다.<br/>
				고객님의 로그인 정보를 알려드립니다. 비밀번호는 암호화 되어있는 관계로<br/>임시비밀번호를 생성해서 보내드리니, 로그인 후에 반드시 변경하길 바랍니다.
			</div>

			<!-- 내용 -->
			<div style=\"margin:40px 50px 50px 50px;\">
				<dl style=\"margin-top:30px\">
					<!-- 내용작은 타이틀 -->
					<dt style=\"font-family:'나눔고딕','돋움'; font-size:17px; font-weight:600; background:transparent url('".$mailing_url."/pages/images/mailing/bullet.png') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666\">로그인 정보 찾기 결과</dt>
					<dd style=\"font-family:'나눔고딕','돋움'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0\">
						아이디 : ".$r[id]."
					</dd>
					<dd style=\"font-family:'나눔고딕','돋움'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0\">
						임시비밀번호 : ".$tmp_pw." (로그인 후 꼭 비밀번호를 변경해주세요.)
					</dd>
				</dl>
			</div>
			";
			/*
			<!-- 메일의 큰제목 -->
			<div style=\"background:transparent url('".$mailing_url."/pages/images/mailing/title_bg.jpg') left top no-repeat; height:130px; text-align:center;\">
				<span style=\"display:inline-block; font-family:'나눔고딕','돋움'; font-size:34px; font-weight:600; color:#fff; letter-spacing:-1px; line-height:120px; background:transparent url('".$mailing_url."/pages/images/mailing/title_icon.png') left center no-repeat; padding-left:70px\">".$_title_text."</span>
			</div>
			*/
			//(substr($r[id],0,strlen($r[id])-3)."***")
			$_content = get_mail_content($_title,$_title_img,$mailling_content);

			// -- 메일 발송
			mailer( $r[email], $_title , $_content );

			echo json_encode(array('result'=>$_result,'pw'=>$r[email],'text'=>$_txt));

			break;

	}
	// - 모드별 처리 ---

	exit;
?>