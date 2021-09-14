<?PHP
	include_once("inc.header.php");

?>

				<form name="memberFrm" id="memberFrm" method=post action=_config.member.pro.php ENCTYPE='multipart/form-data' target="common_frame">

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
										<td class="conts"><?=_InputRadio( "_join_auth_use" , array('Y','N'), $row_setup[s_join_auth_use]  , "" , array("사용","사용안함") , "")?></td>
									</tr>

									<tr class="auth_view">
										<td class="article">서비스 모드<span class="ic_ess" title="필수"></span></td>
										<td class="conts"><?=_InputRadio( "_join_auth_type" , array('service','test'), $row_setup[s_join_auth_type]  , "" , array("서비스 모드","테스트 모드") , "")?></td>
									</tr>

									<tr class="auth_view">
										<td class="article">KCB 회원사 코드</td>
										<td class="conts"><input type="text" name="_join_auth_kcb_code" id="_join_auth_kcb_code" class="input_text" style="width:120px;" value='<?=$row_setup[s_join_auth_kcb_code]?>' />
									</tr>

									<tr class="auth_view">
										<td class="article">참고사항</td>
										<td class="conts">
											※ 참조 URL : <A HREF="http://www.onedaynet.co.kr/201308/addService_03_01_nameKCB.html" target="_BLANK" >http://www.onedaynet.co.kr/201308/addService_03_01_nameKCB.html</A><br>
											※ 참조 URL의 절차에 따라 KCB와 계약체결 후 바로 본인확인 서비스를 이용할 수 있습니다. <br>
											※ 계약 후 KCB 회원사 코드를 입력하여 사용할 수 있습니다.
										</td>
									</tr>

								</tbody> 
							</table>
					</div>
					<!-- // 검색영역 -->



<?=_submitBTNsub()?>

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