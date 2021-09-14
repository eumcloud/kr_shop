<?
	// 로그인 체크
	member_chk();

	$page_title = "회원탈퇴";
	include dirname(__FILE__)."/mypage.header.php";
?>
<div class="common_page">
<div class="common_inner common_full">

	<!-- ●●●●●●●●●● 회원탈퇴 -->
	<div class="cm_mypage_leave">

		<form name="leave_frm" id="leave_frm" action="/pages/member.join.pro.php" method="post" target="common_frame">
		<input type="hidden" name="_mode" value="delete"/>
		<input type="hidden" name="realCheck" value="1"/>
		<div class="form_box">
			<div class="title_img">Member Leave</div>
			<div class="sub_txt">그동안 저희 서비스를 이용하여 주셔서 대단히 감사합니다.<br/>
				더욱더 개선하여 좋은 서비스와 품질로 보답하겠습니다.<br/>
				<strong>아이디와 비밀번호를 확인하고 회원탈퇴 버튼을 누르면 탈퇴가 완료됩니다.</strong>
			</div>
			<ul>
				<li class="login_id"><input type="text" name="leave_id" id="leave_id" class="input_design" placeholder="아이디" value="<?=get_userid()?>" readonly/></li>
				<li class="login_pw"><input type="password" name="fakepw" value="" style="display:none;"/><input type="password" name="leave_pw" id="leave_pw" class="input_design" placeholder="비밀번호 입력" value=""/></li>
			</ul>
		</div>
		<div class="cm_bottom_button">
			<ul>
				<li><div class="button_pack"><button type="button" onclick="leave_submit();" class="btn_lg_black">회원탈퇴</button></div></li>
			</ul>						
		</div>
		</form>

	</div>
	<!-- / 회원탈퇴 -->

	<!-- 페이지 이용도움말 -->
	<div class="cm_user_guide">
		<dl>
			<dt>회원탈퇴 주의사항</dt>
			<dd>탈퇴후에는 같은 아이디로 재가입 할 수 없습니다.</dd>
			<dd>서비스를 탈퇴하시면 서비스 활동이 불가능하며 이용시 발생한 포인트 및 쿠폰등은 복원되지 않습니다.</dd>
			<dd>서비스 탈퇴 후에는 그 동안 이용하셨던 모든 내역의 조회, 상담 및 서비스 등을 이용할 수 없습니다.</dd>
			<dd>탈퇴 즉시 개인정보는 삭제되며, 어떠한 방법으로도 복원할 수 없습니다.</dd>
			<dd>전자상거래 서비스 등의 거래내역은 전자상거래등에서의 소비자보호에 관한 법률에 의거하여 보관됩니다.</dd>
		</dl>
	</div>
	<!-- / 페이지 이용도움말 -->

</div><!-- .common_inner -->
</div><!-- .common_page -->

<script>
$(document).ready(function(){
	$('input[name=leave_pw]').focus();
	$("#leave_frm").validate({
		rules: { leave_pw: { required: true } },
		messages: { leave_pw: { required: "비밀번호를 입력하세요." } }
	});
});
function leave_submit() {
	if( confirm('정말 탈퇴하시겠습니까? 한번 탈퇴하면 되돌릴 수 없습니다.') ) {
		$("#leave_frm").submit();
	}
}
</script>