<?
	// 로그인 중이면 정보수정으로
	if(is_login()) { error_loc("/?pn=mypage.modify.form"); }

	//// 본인인증 화면으로 이동
	//if($row_setup['s_join_auth_use'] == "Y" && !$_POST['result_cd']) {
	//	error_loc("/?pn=member.join.auth");
	//}
	//
	//// 본인인증 관련 처리
	//if($row_setup[s_join_auth_use] == "Y") {
	//
	//	// 본인인증여부 확인
	//	if($_POST[result_cd] != "B000") error_msg("본인인증이 되지 않았습니다.");
	//
	//	// 본인인증을 통과했다면, 회원 기본정보가 넘어온다
	//	$auth_name = $_POST[name];
	//	$auth_htel1 = substr(rm_str($_POST[tel_no]),0,3);
	//	$auth_htel2 = substr(rm_str($_POST[tel_no]),3,strlen(rm_str($_POST[tel_no]))-7);
	//	$auth_htel3 = substr(rm_str($_POST[tel_no]),strlen(rm_str($_POST[tel_no]))-4,4);
	//	$auth_sex	= $_POST[gender] == "1" ? "M" : "F";
	//	$auth_birth = substr($_POST[birthday],0,4) ."-". substr($_POST[birthday],4,2) ."-". substr($_POST[birthday],6,2);
	//
	//}
?>

<div class="common_page common_none">

	<!-- ●●●●●●●●●● 타이틀상단 -->
	<div class="cm_common_top">
		<div class="commom_page_title">
			<span class="icon_img"><img src="/pages/images/cm_images/icon_top_form.png" alt="" /></span>
			<dl>
				<dt>회원 정보입력</dt>
				<dd>입력하신 정보는 마이페이지에서 확인 및 수정 가능합니다.</dd>
			</dl>
		</div>

		<!-- 단계별 페이지가 있을경우 (본인인증이 체크되면 클래스값 if_auth) -->
        <div class="progress">
            <!-- 본인인증없을때 -->
            <div class="default">
                <span class="box"><strong>STEP.1</strong>약관동의</span>
                <span class="box hit"><strong>STEP.2</strong>정보입력</span>
                <span class="box"><strong>STEP.3</strong>가입완료</span>
            </div>
            <!-- 본인인증있을때 -->
            <div class="auth">
                <span class="box"><strong>STEP.1</strong>약관동의</span>
                <span class="box"><strong>STEP.2</strong>본인인증</span>
                <span class="box hit"><strong>STEP.3</strong>정보입력</span>
                <span class="box"><strong>STEP.4</strong>가입완료</span>
            </div>
        </div>

	</div>
	<!-- / 타이틀상단 -->

</div>


<div class="common_page">
	<div class="layout_fix">

	<form name="join_frm" id="join_frm" method="post" action="/pages/member.join.pro.php" target="common_frame" autocomplete="off">
    <input type="hidden" name="_mode" value="join">
    <input type="hidden" name="realCheck" value="1">
    <input type="hidden" name="idCheck1" value="">
    <input type="hidden" name="authtype" value="<?=$authtype?>">
    <input type="hidden" name="_ordr_idxx" value=""><!-- 2018-10-04 SSJ :: 본인인증 사용 시 -->
	<?php
		// SSJ: 2017-09-20 선택적동의항목 추가
		if(sizeof($join_optional_privacy) > 0){
			foreach($join_optional_privacy as $k=>$v){
				echo '<input type="hidden" name="join_optional_privacy[]" value="'. $v .'" />';
			}
		}
	?>

		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_member_title">
			<strong>기본정보</strong> 입력
			<div class="explain"><img src="/pages/images/cm_images/member_form_bullet2.png" alt="필수" /> 표시된 것은 필수 항목입니다.</div>
		</div>
		<!-- / 단락타이틀 -->

		<!-- ●●●●●●●●●● 회원기본정보 -->
		<div class="cm_member_form">
			<ul>
				<!-- 클래스값 추가/// ess:필수요소, 두칸으로 쓸 경우 : double -->
				<li class="ess">
					<span class="opt">아이디</span>
					<div class="value">
						<input type="text" name="_id" id="_id" class="input_design" style="width:305px" onchange="this.form.idCheck1.value=''" maxlength="12"/>
						<span class="button_pack"><a href="#none" onclick="idCheck()" class="btn_md_white">아이디 중복체크<span class="edge"></span></a></span>
						<div class="tip_txt">
							<dl>
								<dt>아이디는 한번 가입한 이후에는 변경이 불가능합니다.</dt>
								<dd>영문, 숫자로 4자~12자 이내로 입력해주세요.</dd>
							</dl>
						</div>
					</div>
				</li>
				<li class="ess double">
					<span class="opt">비밀번호</span>
					<div class="value">
						<input type="password" name="_passwd" id="_passwd" class="input_design" />
						<div class="tip_txt">
							<dl>
								<dd>영문, 숫자로 6자이상 입력해주세요.</dd>
							</dl>
						</div>
					</div>
				</li>
				<li class="ess double">
					<span class="opt">비밀번호 확인</span>
					<div class="value"><input type="password" type="password" name="_repasswd" class="input_design" />
						<div class="tip_txt">
							<dl>
								<dd>동일하게 다시 한번 입력해주세요.</dd>
							</dl>
						</div>
					</div>
				</li>
				<li class="ess double">
                    <span class="opt">이름</span>
                    <div class="value">
                        <?php if($row_setup['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
                            <input type="text" name="_name" class="input_design js_auth_before auth_name" value="" readonly/>
                        <?php } else { ?>
                            <input type="text" name="_name" class="input_design" value=""/>
                        <?php } ?>
                        <div class="tip_txt">
                            <dl>
                                <dd>실명 한글 이름을 입력해주세요.</dd>
                            </dl>
                        </div>
                    </div>
                </li>
				<li class="double">
                    <span class="opt">생년월일</span>
                    <div class="value">
                        <?php if($row_setup['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
                            <input type="text" class="input_design if_date js_auth_before auth_birth" placeholder="(예:2015-05-01)" style="width:130px; margin-right:10px;" name="_birth" id=""  readonly/>
                        <?php } else { ?>
                            <input type="text" class="input_design" placeholder="(예:2015-05-01)" style="width:130px; margin-right:10px;" name="_birth" id="frm_request_birth"  readonly/>
                        <?php } ?>

                        <label><input type="radio" name="_sex" value="M"<?php echo ($row_setup['s_join_auth_use'] == 'Y'?' onclick="return false;" class="js_auth_before auth_sex"':' class="radio"'); ?> checked/><span class="txt">남성</span></label>
                        <label><input type="radio" name="_sex" value="F"<?php echo ($row_setup['s_join_auth_use'] == 'Y'?' onclick="return false;" class="js_auth_before auth_sex"':' class="radio"'); ?>/><span class="txt">여성</span></label>

                        <div class="tip_txt">
                            <dl>
                                <dd>생년월일을 입력해 주세요. </dd>
                            </dl>
                        </div>
                    </div>
                </li>
				<li class="ess double">
                    <span class="opt">휴대폰 번호</span>
                    <div class="value">
                        <?php if($row_setup['s_join_auth_use'] == 'Y'){ ?>
                            <input type="text" name="_htel" class="input_design auth_phone js_auth_before" value="" readonly style="width:168px;"/>
                            <span class="button_pack"><a href="#none" onclick="auth_type_check();return false;" class="btn_md_white">휴대폰 본인인증<span class="edge"></span></a></span>
                        <?php }else{ ?>
                            <input type="text" name="_htel" class="input_design" value=""/>
                        <?php } ?>

                        <div class="tip_txt">
                            <dl>
                                <dt>주문등과 관련된 중요한 문자가 발송됩니다.</dt>
                                <dd>수신가능한 휴대폰 번호를 입력해주세요.</dd>
                            </dl>
                        </div>
                    </div>
                </li>
				<li class="double">
					<span class="opt">SMS 수신</span>
					<div class="value">
						<label><input type="radio" name="_sms" value="Y" checked />수신</label>
						<label><input type="radio" name="_sms" value="N" />수신거부</label>
						<div class="tip_txt">
							<dl>
								<dt>광고성정보, 이벤트 문자 수신여부 </dt>
								<dd>비정기적으로 문자 서비스를 제공합니다.</dd>
							</dl>
						</div>
					</div>
				</li>
				<li class="ess double">
					<span class="opt">이메일 주소</span>
					<div class="value">
						<input type="text" name="_email" class="input_design" placeholder="아이디@주소" />
						<div class="tip_txt">
							<dl>
								<dt>주문등과 관련된 중요한 메일이 발송됩니다.</dt>
								<dd>수신가능한 메일주소를 입력해주세요.</dd>
							</dl>
						</div>
					</div>
				</li>

				<li class="double">
					<span class="opt">이메일 수신</span>
					<div class="value">
						<label><input type="radio" name="_mailling" value="Y" checked/>수신</label>
						<label><input type="radio" name="_mailling" value="N"/>수신거부</label>
						<div class="tip_txt">
							<dl>
								<dt>광고성정보, 이벤트 메일 수신여부 </dt>
								<dd>비정기적으로 메일링 서비스를 제공합니다.</dd>
							</dl>
						</div>
					</div>
				</li>

				<li>
					<span class="opt">주소</span>
					<div class="value">
						<input type="text" name="_zip1" id="_post1" class="input_design" style="width:80px" readonly/><span class="dash"></span>
						<input type="text" name="_zip2" id="_post2" class="input_design" style="width:80px" readonly/>
						<span class="button_pack"><a href="#none" onclick="new_post_view();return false;" class="btn_md_white">주소찾기<span class="edge"></span></a></span>
						<div class="input_double">
							<div class="input_wrap"><div><input type="text" name="_address" id="_addr1" class="input_design" placeholder="기본주소" readonly/></div></div>
							<div class="input_wrap"><div><input type="text" name="_address1" id="_addr2" class="input_design" placeholder="나머지 주소" /></div></div>
						</div>
						<div class="tip_txt">
							<dl>
								<dd>미리 입력해 두시면 쇼핑 시 편리합니다.</dd>
							</dl>
						</div>
					</div>
				</li>
				<li class="">
					<span class="opt">도로명 주소</span>
					<div class="value"><input type="text" name="_address_doro" id="_addr_doro" class="input_design" placeholder="도로명 주소" readonly />
						<div class="tip_txt">
							<dl>
								<dd>주소찾기를 통해 자동으로 입력됩니다.</dd>
							</dl>
						</div>
					</div>
				</li>
				<li class="double">
					<span class="opt">새 우편번호</span>
					<div class="value"><input type="text" name="_zonecode" id="_zonecode" class="input_design" placeholder="국가기초구역번호" readonly />
						<div class="tip_txt">
							<dl>
								<dd>주소찾기를 통해 자동으로 입력됩니다.</dd>
							</dl>
						</div>
					</div>
				</li>
				<li class="double">
					<span class="opt">전화번호</span>
					<div class="value">
						<input type="text" name="_tel" class="input_design" value=""/>
						<div class="tip_txt">
							<dl>
								<dd>휴대폰 이외 유선전화가 필요한 경우 입력해주세요.</dd>
							</dl>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<!-- / 회원기본정보 -->

		<!-- 가운데정렬버튼 -->
		<div class="cm_bottom_button">
			<span class="lineup">
				<span class="button_pack"><a onclick="return confirm('회원가입을 취소하고 메인으로 이동합니다. 계속하시겠습니까?');" href="/" title="" class="btn_lg_black">회원가입 취소<span class="edge"></span></a></span>
				<span class="button_pack"><a href="#none" title="" onclick="join_submit();return false;" class="btn_lg_color">회원가입 완료<span class="edge"></span></a></span>
			</span>
		</div>
		<!-- / 가운데정렬버튼 -->

	</form>

	</div>
</div>



<? // -- 아이디 체크 전용 폼 ( 보안서버 적용을 위한 조치 ) # SSL 페이지 반별후 자동이동 ?>
<form name="idCheck_form" id="idCheck_form" method="post" action="/pages/member.id.check.php" target="common_frame" >
<input type="hidden" name="_id" id="idCheck_id" value="">
</form>
<script>
	function idCheck(){
		if($("#_id").val() == "") { alert('아이디를 먼저 입력하세요.'); $("#_id").focus(); return false; }
		$("#idCheck_id").val($("#_id").val());
		$("#idCheck_form").submit();
	}
</script>



<!-- 생년월일 달력적용 -->
<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>

<script>
$(function() {
	$( "#frm_request_birth" ).datepicker({
		changeMonth: true, changeYear: true, yearRange: "-100:+0"
	});
	$( "#frm_request_birth" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
	$( "#frm_request_birth" ).datepicker( "option",$.datepicker.regional["ko"] );
});
$(document).ready(function(){

	$("#join_frm").validate({
		rules: {
			idCheck1	: { required: true },
			_id			: { required: true, minlength: 4, maxlength:12},
			_passwd		: { required: true, minlength: 6 },
			_repasswd	: { required: true, equalTo: "#_passwd"},
			_name		: { required: true },
			_htel		: { required: true },
			_email		: { required: true, email: true }
		},
		messages: {
			idCheck1	: { required: "아이디 중복체크를 해주시기 바랍니다." },
			_id			: { required: "아이디를 입력하세요." ,minlength: "아이디는 최소 4글자이상입니다." ,maxlength: "아이디는 최대 12글자이하입니다."},
			_passwd 	: { required: "비밀번호를 입력하세요.",minlength: "비밀번호는 최소 6글자이상입니다." },
			_repasswd 	: { required: "비밀번호를 입력하세요.", equalTo: "비밀번호가 같지 않습니다."},
			_name		: { required: "이름을 입력하세요." },
			_htel		: { required: "핸드폰번호를 입력하세요." },
			_email		: { required: "이메일을 입력하세요." , email: "이메일 형식이 올바르지 않습니다."}
		}
        ,submitHandler: function(form){
            // do other things for a valid form
            if(typeof kcp_submit == 'function') if(!kcp_submit()) return false;
            form.submit();
        }
	});

});

function join_submit() { $("#join_frm").submit(); }
</script>

<?PHP
    // 주소찾기 - 우편번호찾기 박스
    include_once $_SERVER[DOCUMENT_ROOT]."/newpost/newpost.search.php";
    // 본인인증 사용을 하는경우에만 적용
    if($row_setup['s_join_auth_use'] == 'Y') {
        $_ATUH_TYPE_ = 'join';
        include_once(dirname(__file__).'/member.join.auth.php'); // 2018-10-04 SSJ :: KCP 휴대폰 본인인증 추가
    }
?>