<?
	// 로그인 중이면 정보수정으로
	if(is_login()) { error_loc("/m/?pn=mypage.modify.form"); }

	//상단 공통페이지
	$page_title = "비밀번호찾기";
	include dirname(__FILE__)."/member.header.php";
?>
<div class="common_page ">

	<!-- 탭메뉴 -->
	<div class="cm_tabmenu">
		<div class="tabbox">
			<ul>
				<li class=""><a href="/m/?pn=member.find.id.form" class="tab">아이디찾기</a></li>
				<li class="hit"><a href="/m/?pn=member.find.pw.form" class="tab">비밀번호찾기</a></li>
			</ul>
		</div>
	</div>
	<!-- / 탭메뉴 -->

	<!-- ●●●●●●●●●● 아이디비번찾기 -->
	<div class="cm_member_find">

		<div class="title_img">Find Password</div>

		<!-- 비밀번호찾기 -->
		<form id="find_pw_frm">
			<input type="hidden" name="_mode" value="pw"/>
			<div class="form_box">
				<ul>
					<li><input type="text" name="find_pw_id" class="input_design" placeholder="사용자 아이디"/></li>
					<li><input type="email" name="find_pw_email" class="input_design" placeholder="이메일 주소"/></li>
				</ul>
			</div>
			<div class="cm_bottom_button">
				<ul>
					<li><div class="button_pack"><button type="submit" class="btn_lg_white">비밀번호 찾기</button></div></li>
				</ul>						
			</div>
		</form>

		<!-- 찾으면 레이어로 나타남 -->
		<div class="result_box" id="find_pw_layer" style="display:none;">
			<div class="result_txt">
				<dl>
					<dt>고객님의 임시비밀번호를<br/><em id="find_pw"></em> 로 보냈습니다.</dt>
					<dd>보내드린 임시비밀번호로 로그인 후,<br/>정보수정에서 꼭 비밀번호를 수정해주세요.</dd>
				</dl>
			</div>
			<div class="cm_bottom_button">
				<li><span class="button_pack"><a href="#none" onclick="reset_find('find_pw_layer');return false;" title="" class="btn_lg_white">다시찾기</a></span></li>
				<li><span class="button_pack"><a href="/m/?pn=member.login.form" title="" class="btn_lg_black">로그인하기</a></span></li>
			</div>
		</div>
		<!-- / 찾으면 레이어로 나타남 -->

		<div class="guide_text">
			<ul>
				<li>위 사항을 입력하면 회원님의 이메일로 임시비밀번호를 보내드립니다.</li>
				<li>가입시 입력하셨던 정보를 모를 경우 고객센터로 문의해주십시오.</li>
			</ul>
		</div>

		<script>
		$(document).ready(function(){
			$('form#find_pw_frm').on('submit',function(e){ e.preventDefault();
				var data = $(this).serialize();
				if($('input[name=find_pw_id]').val()=='') { alert('사용자 아이디를 입력하세요.'); $('input[name=find_pw_id]').focus(); return false; }
				if($('input[name=find_pw_email]').val()=='') { alert('이메일을 입력하세요.'); $('input[name=find_pw_email]').focus(); return false; }
				$.ajax({
					data: data,
					type: 'POST',
					cache: false,
					dataType: 'JSON',
					url: '/pages/member.find.pro.php',
					success: function(data) {
						if(data['result']=='OK') {
							$('#find_pw').text(data['pw']);
							$('#find_pw_layer').show(); $('#find_pw_frm').hide();
							$('input[name=find_pw_id]').val(''); $('input[name=find_pw_email]').val('');
						} else {
							alert(data['text']);
							$('#find_pw').text('');
							$('input[name=find_pw_id]').focus();
						}
					},
					error:function(request,status,error){
						alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					}
				});
			});
		});
		</script>
		<!-- / 비밀번호찾기 -->

	</div>
	<!-- / 아이디비번찾기 -->


</div>
<script>
function reset_find(id) {
	$('#'+id).hide(); $('#find_pw_frm').show();
	if(id=='find_id_layer') { $('input[name=find_id_name]').focus(); }
	if(id=='find_pw_layer') { $('input[name=find_pw_id]').focus(); }
}
</script>