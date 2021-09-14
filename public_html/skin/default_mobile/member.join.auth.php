<?

	// 로그인 중이면 정보수정으로
	if(is_login()) { error_loc("/m/?pn=mypage.modify.form"); }

	// 본인인증이 활성화 안됬다면, 바로 회원가입 창으로 이동
	if($row_setup[s_join_auth_use] != "Y") { error_loc("/m/?pn=member.join.form"); }

?>	
<div class="common_page">
	<div class="common_inner common_full">

		<form name="joinAgree" id="joinAgree" action="/m/member.join.auth.step2.php" method="get">
		<input type="hidden" name="in_tp_bit" id="in_tp_bit" value="8"/>
		<!-- ●●●●●●●●●● 정보입력폼 -->
		<div class="cm_member_form">
			<ul>
				<li class="ess">
					<span class="opt">휴대폰 통신사</span>
					<div class="value ">
						<label class="label_full"><input type="radio" name="tel_com_cd" value="01" class="tel_com_cd" checked>SKT</label>
						<label class="label_full"><input type="radio" name="tel_com_cd" value="02" class="tel_com_cd">KT</label>
						<label class="label_full"><input type="radio" name="tel_com_cd" value="03" class="tel_com_cd">LGU+</label>
						<label class="label_full"><input type="radio" name="tel_com_cd" value="04" class="tel_com_cd">알뜰폰(SKT)</label>
						<label class="label_full"><input type="radio" name="tel_com_cd" value="05" class="tel_com_cd">알뜰폰(KT)</label>
						<label class="label_full"><input type="radio" name="tel_com_cd" value="06" class="tel_com_cd">알뜰폰(LGU+)</label>
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
						<input type="tel" pattern="\d*" name="tel_no" id="tel_no" class="input_design" placeholder="본인명의의 휴대폰번호 입력"/>
						<span class="button_pack"><a href="#none" onclick="jsSubmit();return false;" title="" class="btn_md_white">휴대폰 본인인증</a></span>
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

		<form name="kcbResultForm" id="kcbResultForm" method="post" action="/?pn=member.join.form" onsubmit="return kcb_submit(this)">

			<input type="hidden" name="idcf_mbr_com_cd" 		value=""/>
			<input type="hidden" name="hs_cert_svc_tx_seqno" 	value=""/>
			<input type="hidden" name="hs_cert_rqst_caus_cd" 	value=""/>
			<input type="hidden" name="result_cd" 				value=""/>
			<input type="hidden" name="result_msg" 				value=""/>
			<input type="hidden" name="cert_dt_tm" 				value=""/>
			<input type="hidden" name="di" 						value=""/>
			<input type="hidden" name="ci" 						value=""/>
			<input type="hidden" name="name" 					value=""/>
			<input type="hidden" name="birthday" 				value=""/>
			<input type="hidden" name="gender" 					value=""/>
			<input type="hidden" name="nation" 					value=""/>
			<input type="hidden" name="tel_com_cd" 				value=""/>
			<input type="hidden" name="tel_no" 					value=""/>
			<input type="hidden" name="return_msg"              value=""/>
			<?php
				// SSJ: 2017-09-20 선택적동의항목 추가
				if(sizeof($join_optional_privacy) > 0){
					foreach($join_optional_privacy as $k=>$v){
						echo '<input type="hidden" name="join_optional_privacy[]" value="'. $v .'" />';
					}
				}
			?>

			<div class="cm_bottom_button">
				<ul>
					<li><span class="button_pack"><a href="#none" onclick="history.go(-1);return false;" title="" class="btn_lg_black">이전 페이지</a></span></li>
					<li><span class="button_pack"><a href="#none" onclick="auth_submit();return false;" title="" class="btn_lg_color">다음 단계</a></span></li>
				</ul>
			</div>

		</form>

				
	</div>
</div>



<script>
function auth_submit() {
	$("#kcbResultForm").submit();
}

function kcb_submit(frm) {
	if(!frm.result_cd.value) { alert("본인 인증후 회원가입이 가능합니다."); return false; } 
	else if(frm.result_cd.value != "B000") { alert("본인 인증에 실패하였습니다. 사유 : "+frm.result_msg.value); return false; }
	return true;
}

function jsSubmit(){	
	if($("#tel_no").val() == "(-)하이픈없이 입력") { $("#tel_no").val(""); }
	if ($("#tel_no").val() == "") { alert("휴대폰번호를 입력해주세요"); return; }
	window.open('/m/member.join.auth.step2.php?in_tp_bit='+$("#in_tp_bit").val()+'&tel_com_cd='+$(".tel_com_cd:checked").val()+'&tel_no='+$("#tel_no").val(),'','');
}
</script>