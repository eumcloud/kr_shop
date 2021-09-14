<?
	// - 넘길 변수 설정하기 ---
	$_PVSC = enc('e' , $_SERVER['QUERY_STRING']);
	$_SESSION['path'] = $_PVSC;
	// - 넘길 변수 설정하기 ---

	// 로그인 체크
	member_chk();

	include dirname(__FILE__)."/mypage.header.php";
?>
<div class="common_page">
<div class="layout_fix">

	<!-- 글쓰기 -->
	<form name="frm_request" id="frm_request" method=post action="/pages/mypage.request.pro.php" enctype="multipart/form-data" target="common_frame"  >
	<input type="hidden" name="_menu" value="request">
	<div class="cm_board_form">
		<ul>
			<li class="ess">
				<span class="opt">문의제목</span>
				<div class="value"><input type="text" name="_title" class="input_design" placeholder="제목을 입력해주세요." /></div>
			</li>
			<!-- 2칸으로 쓰고싶을때 클래스값 double -->
			<!-- 에디터 들어갈 자리 -->
			<li class="ess">
				<span class="opt">문의내용</span>
				<div class="value"><!-- 에디터 혹은 --><textarea cols="" rows="" name="_content" class="textarea_design"></textarea>
					<div class="tip_txt">
						<dl>
							<dt>글 등록 시 주민번호, 계좌번호와 같은 개인정보 입력은 삼가 주시기 바랍니다.</dt>
						</dl>
					</div>
				</div>
			</li>
            <? if($row_setup['recaptcha_api']&&$row_setup['recaptcha_secret'] && ( preg_match("/MSIE 8.0*/", $userAgent) == false && preg_match("/MSIE 9.0*/", $userAgent) == false   )   ) { ?>
            <li class="ess">
                <span class="opt">스팸방지</span>
                <div class="value">
                    <!-- 스팸방지 들어감 -->
                    <script src="//www.google.com/recaptcha/api.js"></script>
                    <div class="g-recaptcha" data-sitekey="<?php echo $row_setup['recaptcha_api']; ?>"></div>
                    <input type="hidden" name="recaptcha_action_use" value="Y" />
                </div>
            </li>
            <? } ?>
		</ul>
	</div>

	<!-- 가운데정렬버튼 -->
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><a href="#none" onclick="request_submit();return false;" class="btn_lg_color">작성완료<span class="edge"></span></a></span></li>
		</ul>
	</div>
	<!-- / 가운데정렬버튼 -->
	</form>
	<!-- // 글쓰기 -->


</div><!-- .layout_fix -->
</div><!-- .common_page -->


<script LANGUAGE="JavaScript">
$(document).ready(function(){

	$("#frm_request").validate({
		ignore: "input[type=text]:hidden",
		rules: {
			_title: { required: true, minlength: 2 },
			_content: { required: true, minlength: 2 }
		},
		messages: {
			_title: { required: "제목을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." },
			_content: { required: "내용을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." }
		}
	});

});

function request_submit() {
	$("#frm_request").submit();
}
</script>