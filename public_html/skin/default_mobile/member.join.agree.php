<?

	// 로그인 중이면 정보수정으로.
	if(is_login()) {
		error_loc("/m/?pn=mypage.modify.form");
	}

    //if($row_setup[s_join_auth_use] == "Y") { // 본인인증 활성화시
    //  $next_pn = "member.join.auth";
    //} else {
    //  $next_pn = "member.join.form";
    //}
    $next_pn = "member.join.form";

	// 정책설정 정보 추출 2017-09-13 SSJ
	$row_policy = _MQ_assoc("select * from odtPolicy where 1 order by po_uid asc ");
	$arr_policy = array();
	foreach($row_policy as $k=>$v){
		$arr_policy[$v['po_name']][] = $v;
		$arr_policy[$v['po_name'] . '_use'] = $v['po_use'];
	}

	//상단 공통페이지
	$page_title = "회원가입";
	include dirname(__FILE__)."/member.header.php";

?>

<div class="common_page">
	<div class="common_inner common_full">

		<form name="joinAgree" id="joinAgree" action="/m/" method="get">
		<input type="hidden" name="pn" value="<?=$next_pn?>"/>

		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_member_title">
			<strong>이용약관</strong> 동의
			<div class="explain">다음 약관을 읽고 동의하시면 체크해주십시오.</div>
		</div>
		<!-- / 단락타이틀 -->

		<!-- ●●●●●●●●●● 약관동의 -->
		<div class="cm_member_agree">
			<div class="text_box"><textarea cols="" rows="10" class="textarea_design" readonly><?=stripslashes($row_company[guideinfo])?></textarea></div>
			<div class="agree_check">
				<label><input type="checkbox" name="join_agree" value="yes"/>이용약관을 읽고 이에 동의합니다.</label>
				<!-- 새창링크 --><a href="/m/?pn=service.agree" target="_blank" class="btn_view_all"><span class="lineup">전문보기</span></a>
			</div>
		</div>
		<!-- / 약관동의 -->


		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_member_title">
			<strong>개인정보수집 및 이용</strong> 동의
			<div class="explain">다음 개인정보취급방침을 읽고 동의하시면 체크해주십시오.</div>
		</div>
		<!-- / 단락타이틀 -->


		<!-- ●●●●●●●●●● 약관동의 -->
		<div class="cm_member_agree">
			<div class="text_box"><textarea cols="" rows="" class="textarea_design" readonly><?=stripslashes($row_company[privacyinfo])?></textarea></textarea></div>

			<!-- 개인정보수집 관련 추가 -->
	        <div class="cm_agree_add_info">
	            <table>
	                <colgroup>
	                    <col width="5%"/>
	                    <col width="10%"/>
	                    <col width="20%"/>
	                    <col width="*"/>
	                </colgroup>
	                <thead>
	                    <tr>
	                        <th scope="col" colspan="2">구분</th>
	                        <th scope="col">이용 목적</th>
	                        <th scope="col">수집 항목</th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <tr>
	                        <td class="fc_hit" rowspan="2">회원가입</td>
	                        <td>필수</td>
	                        <td>서비스 이용 및 상담 </td>
	                        <td>아이디, 비밀번호, 이름, 휴대폰 번호, 이메일 주소</td>
	                    </tr>
	                    <tr>
	                        <td>선택</td>
	                        <td>상품 배송</td>
	                        <td>지번주소, 구 우편번호, 도로명 주소, 새 우편번호, 배송 시 유의사항</td>
	                    </tr>
	                </tbody>
	                <thead>
	                    <tr>
	                        <th scope="col" colspan="4">보존 및 파기</th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <tr>
	                        <td colspan="4">회원 탈퇴 이후 부정 이용을 방지하기 위해 1년간 보존</td>
	                    </tr>
	                </tbody>
	                <thead>
	            </table>
	        </div>
	        <!-- / 개인정보수집 관련 추가 -->

			<div class="agree_check">
				<label><input type="checkbox" name="join_privacy" value="yes" />개인정보수집 및 이용에 동의합니다.</label>
				<!-- 새창링크 --><a href="/m/?pn=service.privacy" target="_blank" class="btn_view_all"><span class="lineup">전문보기</span></a>
			</div>
		</div>
		<!-- / 약관동의 -->


		<?php
			// SSJ: 2017-09-20 선택적 개인정보수집 및 이용약관 동의 추가
			if($arr_policy['optional_privacyinfo_use'] == 'Y'){
		?>
				<!-- ●●●●●●●●●● 단락타이틀 -->
				<div class="cm_member_title">
					[선택] <strong>개인정보수집 및 이용</strong> 동의
					<div class="explain">선택 약관에 동의하지 않으셔도 계속 가입을 진행하실 수 있습니다.</div>
				</div>
				<!-- / 단락타이틀 -->
				<?php
					foreach($arr_policy['optional_privacyinfo'] as $k=>$v){
				?>
						<style>

						</style>
						<!-- ●●●●●●●●●● 약관동의 -->
						<div class="cm_member_agree">
							<div class="text_title"><?php echo stripslashes($v['po_title']); ?></div>
							<div class="text_box"><textarea cols="" rows="" readonly class="textarea_design scrollfix"><?php echo stripslashes($v['po_content']); ?></textarea></div>

							<div class="agree_check">
								<label><input type="checkbox" name="join_optional_privacy[]" value="<?php echo $v['po_uid']; ?>" /> 위 방침을 읽고 동의합니다.</label>
							</div>
						</div>
						<!-- / 약관동의 -->
				<?php } ?>
		<?php } ?>

		<?php
			// SSJ: 2017-09-20 선택적 개인정보 처리ㆍ위탁 동의 추가
			if($arr_policy['optional_consign_use'] == 'Y'){
		?>
				<!-- ●●●●●●●●●● 단락타이틀 -->
				<div class="cm_member_title">
					[선택] <strong>개인정보 처리ㆍ위탁</strong> 동의
					<div class="explain">선택 약관에 동의하지 않으셔도 계속 가입을 진행하실 수 있습니다.</div>
				</div>
				<!-- / 단락타이틀 -->
				<?php
					foreach($arr_policy['optional_consign'] as $k=>$v){
				?>
						<style>

						</style>
						<!-- ●●●●●●●●●● 약관동의 -->
						<div class="cm_member_agree">
							<div class="text_title"><?php echo stripslashes($v['po_title']); ?></div>
							<div class="text_box"><textarea cols="" rows="" readonly class="textarea_design scrollfix"><?php echo stripslashes($v['po_content']); ?></textarea></div>

							<div class="agree_check">
								<label><input type="checkbox" name="join_optional_privacy[]" value="<?php echo $v['po_uid']; ?>" /> 위 방침을 읽고 동의합니다.</label>
							</div>
						</div>
						<!-- / 약관동의 -->
				<?php } ?>
		<?php } ?>

		<?php
			// SSJ: 2017-09-20 선택적 개인정보수집 및 이용 동의 추가
			if($arr_policy['optional_thirdinfo_use'] == 'Y'){
		?>
				<!-- ●●●●●●●●●● 단락타이틀 -->
				<div class="cm_member_title">
					[선택] <strong>개인정보 제3자 제공</strong> 동의
					<div class="explain">선택 약관에 동의하지 않으셔도 계속 가입을 진행하실 수 있습니다.</div>
				</div>
				<!-- / 단락타이틀 -->
				<?php
					foreach($arr_policy['optional_thirdinfo'] as $k=>$v){
				?>
						<style>

						</style>
						<!-- ●●●●●●●●●● 약관동의 -->
						<div class="cm_member_agree">
							<div class="text_title"><?php echo stripslashes($v['po_title']); ?></div>
							<div class="text_box"><textarea cols="" rows="" readonly class="textarea_design scrollfix"><?php echo stripslashes($v['po_content']); ?></textarea></div>

							<div class="agree_check">
								<label><input type="checkbox" name="join_optional_privacy[]" value="<?php echo $v['po_uid']; ?>" /> 위 방침을 읽고 동의합니다.</label>
							</div>
						</div>
						<!-- / 약관동의 -->
				<?php } ?>
		<?php } ?>

		<div class="cm_auth_tip"><span class="tx">현재 <u>14세 미만의 회원은 가입이 제한</u>되어 있으며, <br>사이트 이용에 제약이 있을 수 있습니다.</span></div>

		<!-- 가운데정렬버튼 -->
		<div class="cm_bottom_button">
			<ul>
				<li><span class="button_pack"><a href="#none" onclick="history.go(-1);return false;" title="" class="btn_lg_black">이전페이지</a></span></li>
				<li><span class="button_pack"><a href="#none" onclick="agree_submit();return false;" title="" class="btn_lg_color">동의합니다</a></span></li>
			</ul>
		</div>
		<!-- / 가운데정렬버튼 -->

		</form>

	</div><!-- .common_inner -->
</div><!-- .common_page -->


<script>
function agree_submit() {
	$("#joinAgree").submit();
}
$(document).ready(function(){
	$("#joinAgree").validate({
		rules: {
			join_agree: { required : true },
			join_privacy: { required : true }
		},
		messages: {
			join_agree: { required: "이용약관에 동의해주시기 바랍니다." },
			join_privacy: { required: "개인정보수집 및 이용에 동의해주시기 바랍니다." }
		}
	});
});
</script>