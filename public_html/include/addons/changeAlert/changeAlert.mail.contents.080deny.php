<?php

	// 파일명 : changeAlert.mail.contents.080deny.php
	// 080수신거부시 광고성 정보 수신거부 처리 - changeAlert 
	// 자체로 메일발송함.
	// $hp 변수를 이용하여 회원정보 추출
	// $_trigger = "OK" 여야 진행가능

	include_once("inc.php");

	if( $hp && $_trigger == "OK" ) {

		// 회원정보 추출
		$row_member = _MQ_assoc(" select * from odtMember where concat(htel1 ,  htel2 , htel3) = '". rm_str($hp) ."' ");
		foreach($row_member as $k=>$v){

			// - 메일발송 ---
			$email = $v['email'];

			$mailling_content = '
				<div style="margin:40px 50px 50px 50px;">
					<dl style="margin-top:30px">
						<!-- 내용작은 타이틀 -->
						<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/include/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">광고성 정보 수신동의 상태</dt>
						<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
							SMS : 수신거부
						</dd>
						<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
							설정변경일 : '. substr($v['m_opt_date'] , 0 , 10).'
						</dd>
					</dl>
				</div>
			';

			$_title = "[".$row_setup[site_name]."] 080수신거부로 수신동의 상태가 변경되었습니다.";
			$_title_content = '
				<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">' . $v['name'] .'님!</strong><br />
				080수신거부를 통하여 광고성 정보 수신동의 상태가 변경되었음을 알려드립니다.<br />
				변경된 수신정보상태는 아래와 같습니다.
			';
			$_content = get_mail_content($_title, $_title_content, $mailling_content);
			
			mailer( $email , $_title , $_content );
			// - 메일발송 ---

		}

	}

?>