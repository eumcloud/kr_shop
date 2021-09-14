<?
	// 로그인 중이면 정보수정으로
	if(is_login()) { error_loc("/?pn=mypage.modify.form"); }

	// 본인인증이 활성화 안됬다면, 바로 회원가입 창으로 이동
	if($row_setup[s_join_auth_use] != "Y") { error_loc("/?pn=member.join.form"); }
?>	

<div class="common_page common_none">

	<!-- ●●●●●●●●●● 타이틀상단 -->
	<div class="cm_common_top">
		<div class="commom_page_title">
			<span class="icon_img"><img src="/pages/images/cm_images/icon_top_auth.png" alt="" /></span>
			<dl>
				<dt>본인인증</dt>
				<dd>본 사이트 이용을 위해서 본인인증 절차를 거쳐야 합니다.</dd>
			</dl>
		</div>

		<!-- 단계별 페이지가 있을경우 (본인인증이 체크되면 클래스값 if_auth) -->
		<div class="progress if_auth">
			<!-- 본인인증없을때 -->
			<div class="default">
				<span class="box"><strong>STEP.1</strong>약관동의</span>
				<span class="box"><strong>STEP.2</strong>정보입력</span>
				<span class="box"><strong>STEP.3</strong>가입완료</span>
			</div>
			<!-- 본인인증있을때 -->
			<div class="auth">
				<span class="box"><strong>STEP.1</strong>약관동의</span>
				<span class="box hit"><strong>STEP.2</strong>본인인증</span>
				<span class="box"><strong>STEP.3</strong>정보입력</span>
				<span class="box"><strong>STEP.4</strong>가입완료</span>
			</div>
		</div>

	</div>
	<!-- / 타이틀상단 -->

</div>

<div class="common_page">
	<div class="layout_fix">

		<form name='joinAuth' id='joinAuth' action='/pages/member.join.auth.step2.php' method='post'>
		<input type='hidden' name='in_tp_bit' value='8'>
		
		<!-- ●●●●●●●●●● 정보입력폼 -->
		<div class="cm_member_form">
			<ul>
				<li class="ess">
					<span class="opt">휴대폰 통신사</span>
					<div class="value">
						<?=_InputRadio( "tel_com_cd" ,  array('01','02','03','04','05','06'), "01"  , ", style='padding:0 5px' " , array("SKT","KT","LGU","알뜰폰(SKT)","알뜰폰(KT)","알뜰폰(LGU+)") , " ")?>
						<!-- <label class="label_sp"><input type="radio" name="" />SKT</label>
						<label class="label_sp"><input type="radio" name="" />KT</label>
						<label class="label_sp"><input type="radio" name="" />LGU+</label> -->
						<div class="tip_txt">
							<dl>
								<dt>알뜰폰의 경우 아래의 정보를 참고하세요.</dt>
								<dd><strong>알뜰폰(SKT망) </strong> : KCT(Tplus), KD링크, 이마트, 아이즈비전, 유니컴즈, SK텔링크, 큰사람컴퓨터, 스마텔, 에스원, 씨엔커뮤니케이션</dd>
								<dd><strong>알뜰폰(KT망) </strong>: CJ헬로비전, KT 파워텔, 홈플러스, 씨엔커뮤니케이션, 에넥스텔레콤, 에스원, 위너스텔, 에이씨앤코리아, 세종텔레콤, KT텔레캅, 프리텔레콤, 에버그린모바일, 착한통신, kt M모바일, 앤텔레콤, 에스원(안심폰), 아이즈비전, 제이씨티, 머천드코리아, 장성모바일, 유니컴즈</dd>
								<dd><strong>알뜰폰(LGU+망) </strong>: (주)미디어로그, (주)스페이스네트, 머천드코리아, (주)엠티티텔레콤, 홈플러스㈜, (주)알뜰폰, 이마트, 서경방송, 울산방송, 푸른방송, 남인천방송, 금강방송, 제주방송</dd>
							</dl>
						</div>
					</div>
				</li>
				<li class="ess">
					<span class="opt">휴대폰 번호</span>
					<div class="value">
						<input type="text" name="tel_no" class="input_design" placeholder="본인명의의 휴대폰번호 입력" style="width:230px;" />
						<span class="button_pack"><a href="#none" onclick='jsSubmit()' title="" class="btn_md_white">휴대폰 본인인증<span class="edge"></span></a></span>
						<div class="tip_txt">
							<dl>
								<dd>본인명의의 휴대폰 번호를 하이픈(-)없이 입력하고 인증버튼을 클릭해주세요.</dd>
							</dl>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<!-- / 정보입력폼 -->

		</form>

		<form name="kcbResultForm" method="post" action="/?pn=member.join.form">
		<input type="hidden" name="idcf_mbr_com_cd" 		value="" 	/>
		<input type="hidden" name="hs_cert_svc_tx_seqno" 	value=""	/>
		<input type="hidden" name="hs_cert_rqst_caus_cd" 	value="" 	/>
		<input type="hidden" name="result_cd" 				value="" 	/>
		<input type="hidden" name="result_msg" 				value="" 	/>
		<input type="hidden" name="cert_dt_tm" 				value="" 	/>
		<input type="hidden" name="di" 						value="" 	/>
		<input type="hidden" name="ci" 						value="" 	/>
		<input type="hidden" name="name" 					value="" 	/>
		<input type="hidden" name="birthday" 				value="" 	/>
		<input type="hidden" name="gender" 					value="" 	/>
		<input type="hidden" name="nation" 					value="" 	/>
		<input type="hidden" name="tel_com_cd" 				value="" 	/>
		<input type="hidden" name="tel_no" 					value="" 	/>
		<input type="hidden" name="return_msg"              value=""    />
		<?php
			// SSJ: 2017-09-20 선택적동의항목 추가
			if(sizeof($join_optional_privacy) > 0){
				foreach($join_optional_privacy as $k=>$v){
					echo '<input type="hidden" name="join_optional_privacy[]" value="'. $v .'" />';
				}
			}
		?>


		<!-- ●●●●●●●●●● 가운데정렬버튼 -->
		<div class="cm_bottom_button">
			<span class="lineup">
				<span class="button_pack"><a href="#none" onclick="history.go(-1);return false;" class="btn_lg_black">이전 페이지<span class="edge"></span></a></span>
				<span class="button_pack"><a href="#none" onclick="auth_submit();return false;" class="btn_lg_color">다음 단계<span class="edge"></span></a></span>
			</span>
		</div>
		<!-- / 가운데정렬버튼 -->	

		</form>

	</div>
</div>

<script>
function jsSubmit(){	
	var form1 = document.joinAuth, isChecked = false, inTpBit = '';
	for(i=0; i<form1.in_tp_bit.length; i++){ if(form1.in_tp_bit[i].checked){ inTpBit = form1.in_tp_bit[i].value; isChecked = true; break; }	}
	if (form1.tel_no.value == '') { alert('휴대폰번호를 입력해주세요'); form1.tel_no.focus(); return; }
	window.open('', 'auth_popup', 'width=430,height=590,scrollbar=yes');
	form1.target = 'auth_popup'; form1.submit();
}
function auth_submit() {
	frm = document.kcbResultForm;
	if(!frm.result_cd.value) { alert("본인 인증후 회원가입이 가능합니다."); return false; } 
	else if(frm.result_cd.value != "B000") { alert("본인 인증에 실패하였습니다. 사유 : "+frm.result_msg.value); return false; }
	frm.submit();
}
</script>