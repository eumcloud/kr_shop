<?php
/*
비밀번호찾기 Mail Content
*/
$mailling_content = '
<div style="margin:40px 50px 50px 50px;">
	<dl style="margin-top:30px">
		<!-- 내용작은 타이틀 -->
		<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/include/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">회원가입 정보</dt>
		<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
			아이디 : '.$r['id'].'
		</dd>
		<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
			임시비밀번호 : '.$new_passwd.'
		</dd>
	</dl>
</div>
';