<?
	// 로그인 중이 아니면 로그인 페이지로
	if(!is_login()) { error_loc("/?pn=member.login.form"); }
?>	
<div class="common_page common_only">

	<!-- ●●●●●●●●●● 회원가입 완료안내 -->
	<div class="cm_member_ok_message">

		<img src="/pages/images/cm_images/member_complete.png" alt="회원가입완료" />
		<div class="notice"><b><?=$row_member['name']?></b>님! <b><?=stripslashes($row_setup['site_name'])?></b> 회원가입을 진심으로 환영합니다.</div>
		<div class="txt">사이트 이용에 관한 궁금하신 점은 언제든지 고객센터로 문의주세요.</div>

		<!-- 가운데정렬버튼 -->
		<div class="cm_bottom_button">
			<ul>
				<li><span class="button_pack"><a href="/" title="" class="btn_lg_color">홈으로</a></span></li>
				<li><span class="button_pack"><a href="/?pn=mypage.main" title="" class="btn_lg_black">마이페이지</a></span></li>
				<li><span class="button_pack"><a href="/?pn=service.main" title="" class="btn_lg_white">고객센터</a></span></li>
			</ul>
		</div>
		<!-- s/ 가운데정렬버튼 -->	

	</div>
	<!-- / 회원가입 완료안내 -->
	
</div>