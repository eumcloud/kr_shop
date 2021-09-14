<?
	// 로그인 중이면 정보수정으로
	if(is_login()) { error_loc("/m/?pn=mypage.modify.form"); }
?>
<div class="common_page ">

	<!-- 탭메뉴 -->
	<div class="cm_tabmenu">
		<div class="tabbox">
			<ul>
				<li class="hit"><a href="" class="tab">아이디찾기</a></li>
				<li class=""><a href="" class="tab">비밀번호찾기</a></li>
			</ul>
		</div>
	</div>
	<!-- / 탭메뉴 -->

	<!-- ●●●●●●●●●● 아이디비번찾기 -->
	<div class="cm_member_find">

		<div class="left_box">

			<!-- 아이디찾기 -->
			<div class="form">
				<div class="title_img">Find Id <b>아이디찾기</b></div>
				
				<!-- 찾으면 레이어로 나타남 -->
				<div class="result_box" id="find_id_layer" style="display:none;">
					<div class="result_txt">조회하신 아이디는<br/><em id="find_id"></em> 입니다.</div>
					개인정보 도용에 따른 피해 방지를 위해 <br>일부를 별표(*)로 표시하였습니다.
					<div class="cm_bottom_button">
						<ul>
							<li><span class="button_pack"><a href="#none" onclick="reset_find('find_id_layer');return false;" title="" class="btn_md_white">다시찾기</a></span></li>
							<li><span class="button_pack"><a href="/?pn=member.login.form" title="" class="btn_md_black">로그인하기</a></span></li>
						</ul>
					</div>
				</div>
				<!-- / 찾으면 레이어로 나타남 -->
				<form id="find_id_frm">
				<input type="hidden" name="_mode" value="id"/>
				<ul>
					<li><input type="text" name="find_id_name" class="input_design" placeholder="사용자 이름"/></li>
					<li><input type="tel" name="find_id_tel" pattern="\d*" class="input_design" placeholder="휴대폰 번호"/></li>
				</ul>
				<button type="submit" name="" class="btn_ok"/>Find</button>
				</form>

				<div class="guide_text">
					<ul>
						<li>위 사항을 입력하면 회원님의 아이디를 알려드리겠습니다.</li>
						<li>가입시 입력하셨던 정보를 모를 경우 고객센터로 문의해주십시오.</li>
					</ul>
				</div>

			</div>
			<script>
			$(document).ready(function(){
				$('form#find_id_frm').on('submit',function(e){ e.preventDefault();
					var data = $(this).serialize();
					if($('input[name=find_id_name]').val()=='') { alert('사용자 이름을 입력하세요.'); $('input[name=find_id_name]').focus(); return false; }
					if($('input[name=find_id_tel]').val()=='') { alert('휴대폰 번호를 입력하세요.'); $('input[name=find_id_tel]').focus(); return false; }
					$.ajax({
						data: data,
						type: 'POST',
						cache: false,
						dataType: 'JSON',
						url: '/pages/member.find.pro.php',
						success: function(data) {
							if(data['result']=='OK') {
								$('#find_id').text(data['id']);
								$('#find_id_layer').show();
								$('input[name=find_id_name]').val(''); $('input[name=find_id_tel]').val('');
							} else {
								alert(data['text']);
								$('#find_id').text('');
								$('input[name=find_id_name]').focus();
							}
						},
						error:function(request,status,error){
							alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
						}
					});
				});
			});
			</script>
			<!-- / 아이디찾기 -->
		
		</div>
		<div class="right_box">
			
			<!-- 비밀번호찾기 -->
			<div class="form">
				<div class="title_img">Find Password <b>비밀번호찾기</b></div>
				
				<!-- 찾으면 레이어로 나타남 -->
				<div class="result_box" id="find_pw_layer" style="display:none;">
					<div class="result_txt">고객님의 임시비밀번호를<br/><em id="find_pw"></em> 로 보냈습니다.</div>
					보내드린 임시비밀번호로 로그인 후,<br/>정보수정에서 꼭 비밀번호를 수정해주세요.
					<div class="cm_bottom_button">
						<ul>
							<li><span class="button_pack"><a href="#none" onclick="reset_find('find_pw_layer');return false;" title="" class="btn_md_white">다시찾기</a></span></li>
							<li><span class="button_pack"><a href="/?pn=member.login.form" title="" class="btn_md_black">로그인하기</a></span></li>
						</ul>
					</div>
				</div>
				<!-- / 찾으면 레이어로 나타남 -->

				<form id="find_pw_frm">
				<input type="hidden" name="_mode" value="pw"/>
				<ul>
					<li><input type="text" name="find_pw_id" class="input_design" placeholder="사용자 아이디"/></li>
					<li><input type="email" name="find_pw_email" class="input_design" placeholder="이메일 주소"/></li>
				</ul>			
				<button type="submit" name="" class="btn_ok"/>Find</button>
				</form>

				<div class="guide_text">
					<ul>
						<li>위 사항을 입력하면 회원님의 이메일로 임시비밀번호를 보내드립니다.</li>
						<li>가입시 입력하셨던 정보를 모를 경우 고객센터로 문의해주십시오.</li>
					</ul>
				</div>

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
								$('#find_pw_layer').show();
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

	</div>
	<!-- / 아이디비번찾기 -->


</div>
<script>
function reset_find(id) {
	$('#'+id).hide();
	if(id=='find_id_layer') { $('input[name=find_id_name]').focus(); }
	if(id=='find_pw_layer') { $('input[name=find_pw_id]').focus(); }
}
</script>