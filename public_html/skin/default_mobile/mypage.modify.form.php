<?
	// 로그인 상태가 아니면 로그인페이지로
	if(!is_login()) { error_loc("/m/?pn=member.login.form&path=" . enc("e",$_SERVER['QUERY_STRING'])); }

	$MPoint			= number_format($row_member['point']);
	$MSignDate		= date("Y년 m월 d일 H시 i분 s초", $row_member['signdate']);
	$MModifyDate	= date("Y년 m월 d일 H시 i분 s초", $row_member['modifydate']);
	$MRecentDate	= date("Y년 m월 d일 H시 i분 s초", $row_member['recentdate']);
	$birth 			= $row_member['birthy']."-".$row_member['birthm']."-".$row_member['birthd'];

	$page_title = "정보수정";
	include dirname(__FILE__)."/mypage.header.php";
?>
<div class="common_page">
<div class="common_inner common_full post_hide_section">

	<!-- ●●●●●●●●●● 단락타이틀 -->
	<div class="cm_member_title">
		<strong>기본정보</strong> 입력
	</div>
	<!-- / 단락타이틀 -->

	<!-- ●●●●●●●●●● 회원기본정보 -->
	<form name="join_frm" id="join_frm" method="post" action="/pages/member.join.pro.php" target="common_frame">
    <input type="hidden" name="realCheck" value="1">
    <input type="hidden" name="nickCheck1" value="1">
    <input type="hidden" name="_mode" value="modify">
    <input type="hidden" name="_id" value="<?=get_userid()?>">
    <input type="hidden" name="_ordr_idxx" value=""><!-- 2018-10-04 SSJ :: 본인인증 사용 시 -->
	<div class="cm_member_form">
		<ul>
			<!-- 클래스값 추가/// ess:필수요소 -->
			<li class="ess">
				<span class="opt">아이디</span>
				<div class="value"><?=get_userid()?></div>
			</li>
			<li class="ess">
				<span class="opt">보안</span>
				<div class="value">

					<div class="tip_txt">
						<dl>
							<dd><strong>+  비밀번호 변경일 : <?=rm_str($row_member[cpasswd])>0?date('Y-m-d',$row_member[cpasswd]):date('Y-m-d',$row_member[signdate])?></strong></dd>
						</dl>

						<dl>
							<dd><strong>+ 변경알림 갱신일 : <?=date('Y-m-d',$row_member[cpasswd_ck])?></strong></dd>
						</dl>
					</div>

				</div>
			</li>
			<li class="ess">
				<span class="opt">비밀번호</span>
				<div class="value">
					<input type="password" name="_passwd" id="_passwd" class="input_design" value="" placeholder="비밀번호 입력"/>
					<div class="tip_txt">
						<dl>
							<dd>수정을 원할 경우에만 입력해주세요 (영문, 숫자 6자이상).</dd>
						</dl>
					</div>
				</div>
			</li>
			<li class="ess">
				<span class="opt">비밀번호 확인</span>
				<div class="value"><input type="password" name="_repasswd" id="_repasswd" class="input_design" value="" placeholder="다시한번 입력"/>
					<div class="tip_txt">
						<dl>
							<dd>동일하게 다시 한번 입력해주세요.</dd>
						</dl>
					</div>
				</div>
			</li>
			<li class="ess">
                <span class="opt">이름</span>
                <div class="value">
                    <?php if($row_setup['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
                        <input type="text" name="_name" class="input_design js_auth_before auth_name" value="<?=$row_member['name']?>" readonly/>
                    <?php } else { ?>
                        <input type="text" name="_name" class="input_design" value="<?=$row_member['name']?>" readonly/>
                    <?php } ?>
                </div>
            </li>
			<li class="">
                <span class="opt">생년월일</span>
                <div class="value">
                    <?php if($row_setup['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
                        <input type="text" id="" readonly name="_birth" class="input_design if_date js_auth_before auth_birth" placeholder="(예:2015-05-01)" value="<?=$birth?>"/>
                    <?php } else { ?>
                        <input type="text" id="frm_request_birth" readonly name="_birth" class="input_design" placeholder="(예:2015-05-01)" value="<?=$birth?>"/>
                    <?php } ?>
                    <label><input type="radio" name="_sex" value="M" <?=$row_member['sex']=='M'?'checked':''?><?php echo (isset($row_member['sex']) && $row_member['sex'] != ''?' onclick="return false;"':null); ?> class="js_auth_before auth_sex"/>남</label>
                    <label><input type="radio" name="_sex" value="F" <?=$row_member['sex']=='F'?'checked':''?><?php echo (isset($row_member['sex']) && $row_member['sex'] != ''?' onclick="return false;"':null); ?> class="js_auth_before auth_sex"/>여</label>
                    <div class="tip_txt">
                        <dl>
                            <dd>본인의 생년월일을 입력해주세요.</dd>
                        </dl>
                    </div>
                </div>
            </li>
			<li class="ess">
                <span class="opt">휴대폰 번호</span>
                <div class="value">
                    <?php if($row_setup['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
                        <input type="tel" pattern="\d*" name="_htel" class="input_design js_auth_before auth_phone" maxlength="13" value="<?=phone_print($row_member['htel1'],$row_member['htel2'],$row_member['htel3'])?>" placeholder="숫자만 입력하세요" readonly/>
                        <span class="button_pack"><a href="#none" onclick="auth_type_check(); return false;" class="btn_md_white">휴대폰 본인인증</a></span>
                    <?php } else { ?>
                        <input type="tel" pattern="\d*" name="_htel" class="input_design" maxlength="13" value="<?=phone_print($row_member['htel1'],$row_member['htel2'],$row_member['htel3'])?>" placeholder="숫자만 입력하세요"/>
                    <?php } ?>
                    <div class="tip_txt">
                        <dl>
                            <dt>주문등과 관련된 중요한 문자가 발송됩니다.</dt>
                            <dd>수신가능한 휴대폰 번호를 입력해주세요.</dd>
                        </dl>
                    </div>
                </div>
            </li>
			<li class="">
				<span class="opt">SMS 수신</span>
				<div class="value">
					<label><input type="radio" name="_sms" class="radio" value="Y" <?=$row_member['sms']=='Y'?'checked':''?>/>수신</label>
					<label><input type="radio" name="_sms" class="radio" value="N" <?=$row_member['sms']=='N'?'checked':''?>/>수신거부</label>
					<div class="tip_txt">
						<dl>
							<dt>광고성정보, 이벤트 문자 수신여부 </dt>
							<dd>비정기적으로 문자 서비스를 제공합니다.</dd>
						</dl>
					</div>
				</div>
			</li>
			<li class="ess">
				<span class="opt">이메일 주소</span>
				<div class="value">
					<input type="email" name="_email" class="input_design" placeholder="(아이디@주소)" value="<?=$row_member[email]?>"/>
					<div class="tip_txt">
						<dl>
							<dt>주문등과 관련된 중요한 메일이 발송됩니다.</dt>
							<dd>수신가능한 메일주소를 입력해주세요.</dd>
						</dl>
					</div>
				</div>
			</li>

			<li class="">
				<span class="opt">이메일 수신</span>
				<div class="value">
					<label><input type="radio" name="_mailling" class="radio" value="Y" <?=$row_member['mailling']=='Y'?'checked':''?>/>수신</label>
					<label><input type="radio" name="_mailling" class="radio" value="N" <?=$row_member['mailling']=='N'?'checked':''?>/>수신거부</label>
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
					<input type="text" name="_zip1" id="_post1" class="input_design" style="width:80px" readonly value="<?=$row_member['zip1']?>"/><span class="dash"></span>
					<input type="text" name="_zip2" id="_post2" class="input_design" style="width:80px" readonly value="<?=$row_member['zip2']?>"/>
					<span class="button_pack"><a href="#none" onclick="post_popup_show();return false;" title="" class="btn_md_white">주소찾기</a></span>
					<input type="text" name="_address" id="_addr1" class="input_design" placeholder="기본주소" readonly value="<?=$row_member['address']?>"/>
					<input type="text" name="_address1" id="_addr2" class="input_design" placeholder="나머지 주소" value="<?=$row_member['address1']?>"/>
					<!-- <div class="tip_txt">
						<dl>
							<dd>미리 입력해 두시면 쇼핑 시 편리합니다.</dd>
						</dl>
					</div> -->
				</div>
			</li>
			<li class="">
				<span class="opt">도로명 주소</span>
				<div class="value"><input type="text" name="_address_doro" id="_addr_doro" class="input_design" placeholder="도로명 주소" value="<?=$row_member['address_doro']?>" readonly/>
					<div class="tip_txt">
						<dl>
							<dd>주소찾기를 통해 자동으로 입력됩니다.</dd>
						</dl>
					</div>
				</div>
			</li>
			<li class="">
				<span class="opt">새 우편번호</span>
				<div class="value"><input type="text" name="_zonecode" id="_zonecode" class="input_design" placeholder="국가기초구역번호" value="<?=$row_member['zonecode']?>" readonly/>
					<div class="tip_txt">
						<dl>
							<dd>주소찾기를 통해 자동으로 입력됩니다.</dd>
						</dl>
					</div>
				</div>
			</li>
			<li class="">
				<span class="opt">전화번호</span>
				<div class="value">
				<input type="tel" name="_tel" pattern="\d*" class="input_design" maxlength="13" value="<?=phone_print($row_member['tel1'],$row_member['tel2'],$row_member['tel3'])?>" placeholder="숫자만 입력하세요"/>
					<div class="tip_txt">
						<dl>
							<dd>휴대폰 이외 유선전화가 필요한 경우 입력해주세요.</dd>
						</dl>
					</div>
				</div>
			</li>
			<!-- LMH001 <li>
				<span class="opt">환불계좌</span>
				<div class="value">
					<select name="cancel_bank" class="select_design" style="">
						<option value="">- 은행 선택 -</option>
						<? foreach($ksnet_bank as $kk=>$vv) { ?>
						<option value="<?=$kk?>" <?=$row_member['cancel_bank']==$kk?'selected':''?>><?=$vv?></option>
						<? } ?>
					</select>
					<div class="input_double">
						<div class="input_wrap"><div>
							<input type="text" name="cancel_bank_account" class="input_design" value="<?=$row_member['cancel_bank_account']?>" placeholder="계좌번호"/>
						</div></div>
						<div class="input_wrap"><div>
							<input type="text" name="cancel_bank_name" class="input_design" value="<?=$row_member['cancel_bank_name']?>" placeholder="예금주"/>
						</div></div>
					</div>
					<div class="tip_txt">
						<dl>
							<dd>카드결제 취소를 제외한 주문을 취소할때 위 계좌로 환불해 드립니다.</dd>
						</dl>
					</div>
				</div>
			</li> -->
		</ul>
	</div><!-- .cm_member_form -->
	<!-- / 회원기본정보 -->

	<?php
		// 정책설정 정보 추출 2017-09-20 SSJ
		$row_policy = _MQ_assoc("select * from odtPolicy where 1 order by po_uid asc ");
		$arr_policy = array();
		foreach($row_policy as $k=>$v){
			$arr_policy[$v['po_name']][] = $v;
			$arr_policy[$v['po_name'] . '_use'] = $v['po_use'];
		}

		// 선택동의 내역 추출
		$ex_agree_privacy = explode(",", $indr['member_agree_privacy']);
	?>


	<?php
		// SSJ: 2017-09-20 선택적 개인정보수집 및 이용 동의 추가
		if($arr_policy['optional_privacyinfo_use'] == 'Y'){
	?>
			<!-- ●●●●●●●●●● 단락타이틀 -->
			<div class="cm_member_title">
				[선택] <strong>개인정보수집 및 이용</strong> 동의
			</div>
			<!-- / 단락타이틀 -->
			<?php
				foreach($arr_policy['optional_privacyinfo'] as $k=>$v){
			?>
					<style>

					</style>
					<!-- ●●●●●●●●●● 약관동의 -->
					<div class="cm_member_agree no_bg">
						<div class="text_title"><?php echo stripslashes($v['po_title']); ?></div>
						<div class="text_box"><textarea cols="" rows="" readonly class="textarea_design scrollfix"><?php echo stripslashes($v['po_content']); ?></textarea></div>

						<div class="agree_check text_box">
							<label><input type="checkbox" name="join_optional_privacy[]" value="<?php echo $v['po_uid']; ?>" <?php echo (array_search($v['po_uid'], $ex_agree_privacy) !== false ? ' checked ' : null); ?> /> 위 방침을 읽고 동의합니다.</label>
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
			</div>
			<!-- / 단락타이틀 -->
			<?php
				foreach($arr_policy['optional_consign'] as $k=>$v){
			?>
					<style>

					</style>
					<!-- ●●●●●●●●●● 약관동의 -->
					<div class="cm_member_agree no_bg">
						<div class="text_title"><?php echo stripslashes($v['po_title']); ?></div>
						<div class="text_box"><textarea cols="" rows="" readonly class="textarea_design scrollfix"><?php echo stripslashes($v['po_content']); ?></textarea></div>

						<div class="agree_check text_box">
							<label><input type="checkbox" name="join_optional_privacy[]" value="<?php echo $v['po_uid']; ?>" <?php echo (array_search($v['po_uid'], $ex_agree_privacy) !== false ? ' checked ' : null); ?> /> 위 방침을 읽고 동의합니다.</label>
						</div>
					</div>
					<!-- / 약관동의 -->
			<?php } ?>
	<?php } ?>

	<?php
		// SSJ: 2017-09-20 선택적 개인정보수집 및 이용약관 동의 추가
		if($arr_policy['optional_thirdinfo_use'] == 'Y'){
	?>
			<!-- ●●●●●●●●●● 단락타이틀 -->
			<div class="cm_member_title">
				[선택] <strong>개인정보 제3자 제공</strong> 동의
			</div>
			<!-- / 단락타이틀 -->
			<?php
				foreach($arr_policy['optional_thirdinfo'] as $k=>$v){
			?>
					<style>

					</style>
					<!-- ●●●●●●●●●● 약관동의 -->
					<div class="cm_member_agree no_bg">
						<div class="text_title"><?php echo stripslashes($v['po_title']); ?></div>
						<div class="text_box"><textarea cols="" rows="" readonly class="textarea_design scrollfix"><?php echo stripslashes($v['po_content']); ?></textarea></div>

						<div class="agree_check text_box">
							<label><input type="checkbox" name="join_optional_privacy[]" value="<?php echo $v['po_uid']; ?>" <?php echo (array_search($v['po_uid'], $ex_agree_privacy) !== false ? ' checked ' : null); ?> /> 위 방침을 읽고 동의합니다.</label>
						</div>
					</div>
					<!-- / 약관동의 -->
			<?php } ?>
	<?php } ?>


	<!-- 가운데정렬버튼 -->
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><a href="/m/?pn=mypage.main" title="" class="btn_lg_black">마이페이지메인</a></span></li>
			<li><span class="button_pack"><a href="#none" onclick="join_submit();return false;" title="" class="btn_lg_color">정보수정완료</a></span></li>
		</ul>
	</div>
	<!-- / 가운데정렬버튼 -->
	</form>

</div><!-- .common_inner -->
</div><!-- .common_page -->

<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>

<script>
$(function() {
	$( "#frm_request_birth" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: "-100:+0"
	});
	$( "#frm_request_birth" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
	$( "#frm_request_birth" ).datepicker( "option",$.datepicker.regional["ko"] );
});
function join_submit() {
	$("#join_frm").submit();
}
$(document).ready(function(){
    $("#join_frm").validate({
        rules: {
            _passwd     : { required: ($("input[name=_passwd]").val()!="" ? true : false), minlength: 6 },
            _repasswd   : { equalTo: "#_passwd"},
            _name   : { required: true },
            _date       : { required: true },
            _htel       : { required: true },
            _email      : { required: true }
        },
        messages: {
            _passwd     : { required: "비밀번호를 입력하세요.",minlength: "비밀번호는 최소 6글자이상입니다." },
            _repasswd   : { equalTo: "비밀번호가 같지 않습니다."},
            _name   : { required: "이름을 입력하세요." },
            _date       : { required: "생년월일을 입력하세요." },
            _htel       : { required: "핸드폰번호를 입력하세요." },
            _email      : { required: "이메일을 입력하세요." }
        }
        ,submitHandler: function(form){
            // 정보 수정페이지 회원정보 변경체크 -- 본인인증 적용 체크
            if(
                '<?php echo phone_print($row_member["htel1"],$row_member["htel2"],$row_member["htel3"]); ?>' != $('input[name=_htel]').val()
                ||
                '<?php echo $row_member["name"]; ?>' != $('input[name=_name]').val()
                ||
                '<?php echo $birth; ?>' != $('input[name=_birth]').val()
                ||
                '<?php echo $row_member["sex"]; ?>' != $('input[name=_sex]:checked').val()
            )
            {
                // do other things for a valid form
                if(typeof kcp_submit == 'function') if(!kcp_submit()) return false;
            }
            form.submit();
        }
    });
});
</script>
<?php
    include_once $_SERVER[DOCUMENT_ROOT]."/newpost/newpost.search_m.php"; // 주소찾기 - 우편번호찾기 박스
    if($row_setup['s_join_auth_use'] == 'Y') {
        $_ATUH_TYPE_ = 'modify';
        include_once(dirname(__file__).'/../../pages/member.join.auth.php'); // 2018-10-04 SSJ :: KCP 휴대폰 본인인증 추가
    }
?>