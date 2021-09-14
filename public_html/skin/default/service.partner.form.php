<?
	// - 넘길 변수 설정하기 ---
	$_PVSC = enc('e' , $_SERVER['QUERY_STRING']);
	$_SESSION['path'] = $_PVSC;
	// - 넘길 변수 설정하기 ---

	include dirname(__FILE__)."/cs.header.php";
?>
<div class="common_page">
<div class="layout_fix">

	<!-- 글쓰기 -->
	<form name="frm_request" id="frm_request" method=post action="/pages/service.partner.pro.php" enctype="multipart/form-data" target="common_frame"  >
	<input type="hidden" name="_menu" value="partner">
	<div class="cm_board_form">
		<ul>
			<li class="ess">
				<span class="opt">이름/상호명</span>
				<div class="value"><input type="text" name="_comname" class="input_design" placeholder="이름/상호명을 입력해주세요." value="<?=$row_member['name']?>"/></div>
			</li>
			<li class="ess">
				<span class="opt">연락처</span>
				<div class="value"><input type="text" name="_tel" class="input_design" placeholder="연락처를 입력해주세요." value="<?=$row_member['htel1']?phone_print($row_member['htel1'],$row_member['htel2'],$row_member['htel3']):''?>"/></div>
			</li>
			<li class="ess">
				<span class="opt">이메일</span>
				<div class="value"><input type="text" name="_email" class="input_design" placeholder="이메일을 입력해주세요." value="<?=$row_member['email']?>"/></div>
			</li>
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
			<li class="">
				<span class="opt">첨부파일</span>
				<div class="value">
					<div class="input_file_box">
						<input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled placeholder="파일을 선택해주세요."/>
						<div class="fileDiv">
							<input type="button" class="buttonImg" value="파일찾기" />
							<input type="file" name="_file" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value"  />
						</div>
					</div>
					<div class="tip_txt">
						<dl>
							<dd>ZIP파일만 등록 가능합니다 (최대 2MB). 용량이 클 때에는 파일만 대용량이메일로 보내주시기 바랍니다.</dd>
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

	<? if( !is_login() ) { ?>
	<!-- 비회원일경우 약관동의하기 필요하면 사용 -->
	<div class="cm_step_agree">
		<textarea cols="" rows="" name="" readonly><?=stripslashes($row_company['partner_agree'])?></textarea>

		<!-- 개인정보수집 관련 추가 -->
	    <div class="cm_agree_add_info">
	        <table>
	            <colgroup>
	                <col width="15%"/>
	                <col width="10%"/>
	                <col width="18%"/>
	                <col width="*"/>
	                <col width="30%"/>
	            </colgroup>
	            <thead>
	                <tr>
	                    <th scope="col" colspan="2">구분</th>
	                    <th scope="col">이용 목적</th>
	                    <th scope="col">수집 항목</th>
	                    <th scope="col">보존 및 파기</th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td class="fc_hit">광고/제휴문의</td>
	                    <td>필수</td>
	                    <td>광고/제휴문의 및 상담</td>
	                    <td>이름/상호명, 연락처, 이메일 주소</td>
	                    <td>문의 및 상담 처리에 필요한 기간 동안 보존</td>
	                </tr>
	            </tbody>
	        </table>
	    </div>
	    <!-- / 개인정보수집 관련 추가 -->

		<label><input type="checkbox" name="order_agree" id="order_agree" class="" value="Y" /> 위 "개인정보수집 및 이용안내"를 읽고 동의합니다.</label>
	</div>
	<!-- / 동의하기 -->
	<? } ?>

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
			<? if(!is_login()){ ?>order_agree: { required: true },<? } ?>
			_comname: { required: true, minlength: 2 },
			_tel: { required: true, minlength: 7 },
			_title: { required: true, minlength: 2 },
			_content: { required: true, minlength: 2 },
			_email: { required: true, email: true }
		},
		messages: {
			<? if(!is_login()){ ?>order_agree: { required: "개인정보 수집방침에 동의해주시기 바랍니다." },<? } ?>
			_comname: { required: "상호명이나 이름을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." },
			_tel: { required: "연락처를 입력하세요", minlength: "7글자 이상 등록하셔야 합니다." },
			_title: { required: "제목을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." },
			_content: { required: "내용을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." },
			_email: { required: "이메일을 입력하세요", email: "올바른 이메일 주소를 입력하세요." }
		}
	});

});

function request_submit() {
	$("#frm_request").submit();
}
</script>