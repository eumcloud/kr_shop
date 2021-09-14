<?

	if(is_login()) { error_loc("/"); }

	$msr = _MQ("select * from odtMemberSleep where id='". $_id ."' ");
	if(!$msr['id']) { error_loc_msg("/" , "잘못된 접근입니다."); }


?>	
<div class="common_page common_only">

<form name="login_frm" id="login_frm" method="post" action="/pages/member.sleep.pro.php" target="common_frame">
<input type="hidden" name="_mode" value="send">
<input type="hidden" name="_id" value="<?=$_id?>">

	<!-- ●●●●●●●●●● 휴면계정 -->
	<div class="cm_member_sleep">			
		<div class="inner_box">
			
			<span class="top_ic"></span>
			<div class="title_box">휴면 회원 인증</div>

			<div class="guide_txt">
				회원님은 현재 장기 미사용 계정으로 휴면전환된 상태입니다.<br/>
				휴면 상태를 풀기 위해서는 <strong>이메일 인증절차</strong>를 거쳐야 합니다.<br/>
				아래 버튼을 클릭하여 인증을 진행하시기 바랍니다.
			</div>
			
			<div class="btn_box">
				<a href="#none" onclick="document.login_frm.submit();" class="btn_email_ok"><span class="txt">휴면회원 인증 진행</span></a>
			</div>

		</div>
	</div>	
	<!-- / 휴면계정 -->

</form>

</div>

<script>
$(document).ready(function(){

	$("#login_frm").validate({
		rules: {
			_id: { required: function() { $("input[name=_id]").val() == "아이디" ? $("input[name=_id]").val("") : ""; return true; } },
			_passwd: { required: function() { $("input[name=_passwd]").val() == "비밀번호" ? $("input[name=_passwd]").val("") : ""; return true; }  }
		},
		messages: {
			_id: { required: "아이디를 입력하세요."},
			_passwd: { required: "비밀번호를 입력하세요."}
		}
	});

});
</script>