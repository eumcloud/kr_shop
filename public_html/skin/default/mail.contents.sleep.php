<?php
/*
휴먼계정 전환 안내 Mail Content
*/
$mailling_content = '
<div style="margin:40px 50px 50px 50px;">
	<dl style="margin-top:30px">
		<!-- 내용작은 타이틀 -->
		<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/include/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">회원정보</dt>
		<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
			고객명 : '.$_name.'
		</dd>
		<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
			최근접속일 : '.$_recentdate.'
		</dd>
		<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding-top:10px; font-weight:600; color:#444; margin:0;">※ 휴면계정 전환 후 휴면을 푸시려면, 로그인 후 별도의 인증과정을 거치시기 바랍니다.</dd>
	</dl>
</div>
';