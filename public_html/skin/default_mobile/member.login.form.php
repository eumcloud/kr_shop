<?php
if(is_login()) { error_loc("/m/"); }

//상단 공통페이지
$page_title = "로그인";
?>
<div class="common_page">

	<form name="login_frm" id="login_frm" method="post" action="/pages/member.login.pro.php" target="common_frame">
	<input type="hidden" name="_move_path" value="<?=$_SESSION['path']?$_SESSION['path']:$_GET['path']?>"/><? $_SESSION['path'] = ''; ?>
	<input type="hidden" name="_mode" value="login"/>
	
	<!-- ●●●●●●●●●● 로그인 -->
	<div class="cm_member_login">	
			
		<div class="form_box">
			<div class="title_box">Member Login</div>
			<ul>
				<li class="login_id"><input type="text" name="_id" class="input_design" placeholder="아이디" value="<?=$_COOKIE['AuthSDIndividualIDChk']?>"/></li>
				<li class="login_pw"><input type="password" name="_passwd" class="input_design" value="" placeholder="비밀번호"/></li>
			</ul>			
			<input type="submit" name="" class="btn_login" value="LOGIN"/>
			<div class="save_id"><label><input type="checkbox" name="login_id_chk" value="Y" <?=($_COOKIE['AuthSDIndividualIDChk'])?'checked':''?>/>아이디 저장하기</label></div>
		</div>


		<div class="btn_box">
			<ul>
				<li>
					<span class="button_pack"><a href="/m/?pn=member.find.id.form" class="btn_md_white">아이디 찾기</a></span>
					<span class="button_pack"><a href="/m/?pn=member.find.pw.form" class="btn_md_white">비밀번호 찾기</a></span>
				</li>
				<li><span class="button_pack"><a href="/m/?pn=member.join.agree" class="btn_md_black">온라인 회원가입</a></span></li>
			</ul>
		</div>

	</div>	
	<!-- / 로그인 -->

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