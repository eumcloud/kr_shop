<?
	// 로그인 중이면 정보수정으로
	if(is_login()) { error_loc("/m/?pn=mypage.modify.form"); }

	//상단 공통페이지
	$page_title = "아이디찾기";
	include dirname(__FILE__)."/member.header.php";
?>
<div class="common_page ">

	<!-- 탭메뉴 -->
	<div class="cm_tabmenu">
		<div class="tabbox">
			<ul>
				<li class="hit"><a href="/m/?pn=member.find.id.form" class="tab">아이디찾기</a></li>
				<li class=""><a href="/m/?pn=member.find.pw.form" class="tab">비밀번호찾기</a></li>
			</ul>
		</div>
	</div>
	<!-- / 탭메뉴 -->

	<!-- ●●●●●●●●●● 아이디비번찾기 -->
	<div class="cm_member_find">

		<div class="title_img">Find Id</div>

		<!-- 아이디찾기 -->
		<form id="find_id_frm">
			<input type="hidden" name="_mode" value="id"/>
			<div class="form_box">
				<ul>
					<li><input type="text" name="find_id_name" class="input_design" placeholder="사용자 이름"/></li>
					<li><input type="text" name="find_id_tel" class="input_design" placeholder="휴대폰 번호"/></li>
				</ul>
			</div>
			<div class="cm_bottom_button">
				<ul>
					<li><div class="button_pack"><button type="submit" class="btn_lg_white">아이디 찾기</button></div></li>
				</ul>						
			</div>
		</form>

		<!-- 찾으면 레이어로 나타남 -->
		<div class="result_box" id="find_id_layer" style="display:none;">
			<div class="result_txt">
				<dl>
					<dt>조회하신 아이디는<br/><em id="find_id"></em> 입니다.</dt>
					<dd>개인정보 도용에 따른 피해 방지를 위해 <br>일부를 별표(*)로 표시하였습니다.</dd>
				</dl>
			</div>
			<div class="cm_bottom_button">
				<ul>
					<li><span class="button_pack"><a href="#none" onclick="reset_find('find_id_layer');return false;" title="" class="btn_lg_white">다시찾기</a></span></li>
					<li><span class="button_pack"><a href="/m/?pn=member.login.form" title="" class="btn_lg_black">로그인하기</a></span></li>
				</ul>
			</div>
		</div>
		<!-- / 찾으면 레이어로 나타남 -->

		<div class="guide_text">
			<ul>
				<li>위 사항을 입력하면 회원님의 아이디를 알려드리겠습니다.</li>
				<li>가입시 입력하셨던 정보를 모를 경우 고객센터로 문의해주십시오.</li>
			</ul>
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
							$('#find_id_layer').show(); $('#find_id_frm').hide();
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
	<!-- / 아이디비번찾기 -->


</div>
<script>
function reset_find(id) {
	$('#'+id).hide(); $('#find_id_frm').show();
	if(id=='find_id_layer') { $('input[name=find_id_name]').focus(); }
	if(id=='find_pw_layer') { $('input[name=find_pw_name]').focus(); }
}
</script>