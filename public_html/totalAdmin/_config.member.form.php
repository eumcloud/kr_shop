<?PHP
	include_once("inc.header.php");

?>

	<form name="memberFrm" id="memberFrm" method="post" action="_config.member.pro.php" ENCTYPE="multipart/form-data">
	<input type="hidden" name="_mode" value="modify">

		<!-- 검색영역 -->
		<!-- 내부 서브타이틀 -->
		<div class="sub_title"><span class="icon"></span><span class="title">회원가입시 본인확인 설정</span></div>
		<!-- // 내부 서브타이틀 -->
		<div class="form_box_area">
			<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<td class="article">본인확인 서비스 사용<span class="ic_ess" title="필수"></span></td>
							<td class="conts"><?php echo _InputRadio( "_join_auth_use" , array('Y','N'), $row_setup['s_join_auth_use']  , "" , array("사용","사용안함") , ""); ?></td>
						</tr>

						<tr class="auth_view">
							<td class="article" style="<?php echo ($row_setup['s_join_auth_use'] <> 'Y' ? 'display:none;' : null); ?>">KCP 회원사 코드</td>
							<td class="conts" style="<?php echo ($row_setup['s_join_auth_use'] <> 'Y' ? 'display:none;' : null); ?>"><input type="text" name="_join_auth_kcb_code" id="_join_auth_kcb_code" class="input_text" style="width:120px;" value='<?php echo $row_setup['s_join_auth_kcb_code']; ?>' />
						</tr>

						<tr class="auth_view">
							<td class="article" style="<?php echo ($row_setup['s_join_auth_use'] <> 'Y' ? 'display:none;' : null); ?>">참고사항</td>
							<td class="conts" style="<?php echo ($row_setup['s_join_auth_use'] <> 'Y' ? 'display:none;' : null); ?>">
								※ 참조 URL : <A HREF="http://www.onedaynet.co.kr/p/add_02_1.html" target="_BLANK" >http://www.onedaynet.co.kr/p/add_02_1.html</A><br>
								※ 참조 URL의 절차에 따라 KCP와 계약체결 후 바로 본인확인 서비스를 이용할 수 있습니다. <br>
								※ 계약 후 KCP 회원사 코드를 입력하여 사용할 수 있습니다.<br><br>

								※ KCP 본인확인 테스트 코드 사용방법<br>
								&nbsp;&nbsp;&nbsp;&nbsp;1. 테스트 사이트 코드는 <strong>S6186</strong>입니다. 회원사 코드에 테스트 사이트 코드를 입력해 주시기 바랍니다.<br>
								&nbsp;&nbsp;&nbsp;&nbsp;2. 테스트 사이트 코드 입력 시 <strong>KT</strong>로만 인증 가능합니다. <br>
								&nbsp;&nbsp;&nbsp;&nbsp;3. 그외 인증정보(성명, 생년월일, 성별, 휴대폰번호)는 임의로 입력가능 합니다.<br>
								&nbsp;&nbsp;&nbsp;&nbsp;4. 인증 문자가 발송되는 대신 <strong>OTP_NO = XXXXXXX</strong>와같은 형식으로 알림창에 인증번호가 노출됩니다. <br>
								&nbsp;&nbsp;&nbsp;&nbsp;5. 알림창에 노출된 인증번호를 입력하면 인증이 완료됩니다.<br>
							</td>
						</tr>

					</tbody>
				</table>
		</div>
		<!-- // 검색영역 -->

		<?php echo _submitBTNsub(); ?>

	</form>

<script>
	/*  메인스타일 ---------- */
	var onoff = function() {
		if($("input[name=_join_auth_use]:checked").val() == "Y") {
			$(".auth_view td").show();
		}
		else {
			$(".auth_view td").hide();
		}
	}
	onoff();
	$("input[name=_join_auth_use]").click(function() {onoff();});
	/*  // 메인스타일 ---------- */

</script>
<?PHP
	include_once("inc.footer.php");
?>