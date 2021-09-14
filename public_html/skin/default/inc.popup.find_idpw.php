<div class="cm_ly_pop_tp find_idpw" style="width:500px;display:none">

	

	<form name="find_idpw_frm" id="find_idpw_frm" method="post" action="/pages/member.login.pro.php" target="common_frame">
	<input type="hidden" name="_mode" value="find_idpw">			
	<input type="hidden" name="Form" value="MemSearchForm">
	<input type="hidden" name="ipinuser" value="0">
	<input type="hidden" name="kcb_dupinfo" value="0">	
	

	<!--  레이어팝업 공통타이틀 영역 -->
	<div class="title_box">아이디/비번찾기<a href="" class="btn_close close" title="닫기"></a></div>
	
	<!-- 하얀색박스공간 -->
	<div class="inner_box">
		
		<!-- 설명글 -->
		<div class="top_txt">
			아래 사항을 입력하시면 회원님의 이메일로 <br/>아이디와 임시 비밀번호를 보내드립니다.<br/>
			<strong>임시 비밀번호는 로그인 후에 꼭 변경하시길 바랍니다.</strong>
		</div>	
		
		<!-- 폼들어가는곳 -->
		<div class="form_box">
			<ul>
				<li>
					<span class="opt">이름</span>
					<div class="value"><input type="text" name="_name" class="input_design icon_name" placeholder="가입시 입력한 이름" /></div>
				</li>
				<li>
					<span class="opt">생년월일</span>
					<div class="value"><input type="text" name="_birth" id="_birth" readonly style="cursor:pointer" class="input_design icon_date" value="" placeholder="가입시 입력한 생일 (예:2015-05-01)" /></div>
				</li>
				<li>
					<span class="opt">이메일주소</span>
					<div class="value"><input type="text" name="_email" class="input_design icon_email" placeholder="가입시 입력한 이메일주소 (아이디@주소)" /></div>
				</li>
			</ul>
		</div>
		<!-- / 폼들어가는곳 -->
		
		<!-- 레이어팝업 버튼공간 -->
		<div class="button_pack">
			<span class="lineup">
				<a href="#none" title="" onclick="find_idpw_submit()" class="btn_md_black">확인<span class="edge"></span></a>
				<a href="" title="" class="btn_md_white close">닫기<span class="edge"></span></a>
			</span>
		</div>
		<!-- /레이어팝업 버튼공간 -->

	</div>
	<!-- / 하얀색박스공간 -->
	</form>

</div>

<!-- 생년월일 달력적용 -->
<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>

<script>
	$(function() {
        $( "#_birth" ).datepicker({changeMonth: true,changeYear: true, yearRange: "-100:+0" , defaultDate: "1980-04-01"});
        $( "#_birth" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
        $( "#_birth" ).datepicker( "option",$.datepicker.regional["kr"] );
    });

	$(document).ready(function(){

		$("#find_idpw_frm").validate({
			rules: {
				_name: { required: true },
				_birth: { required: true },
				_email: { required: true, email : true}
			},
			messages: {
				_name: { required: "이름을 입력하세요."},
				_birth: { required: "생년월일을 입력하세요."},
				_email: { required: "이메일주소를 입력하세요.", email : "이메일 주소가 올바르지 않습니다."}
			}
		});

	});
	function find_idpw_submit() {

		$("#find_idpw_frm").submit();

	}
	function popup_find_idpw() {
		$('.find_idpw').lightbox_me({
			centered: true, 
			closeEsc: false,
			onLoad: function() { 
			}
		});
	}
</script>
<!-- 메일링구독 -->