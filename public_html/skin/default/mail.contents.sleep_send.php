<?php
/*
휴먼계정 복구(인증) Mail Content
*/
$mailling_content = '
<div style="margin:40px 50px 50px 50px;">
	<dl style="margin-top:30px">
		<!-- 내용작은 타이틀 -->
		<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/include/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">인증URL</dt>
		<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
			사용자 : '.$_name.'
		</dd>
		<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
			아이디 : '.$_id.'
		</dd>
		<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
			인증URL : <a href="'.$_AUTH_URL.'" target="_blank">'.$_AUTH_URL.'</a>
		</dd>
	</dl>
</div>
';