<?php
	// 080 수신거부 설정 적용
?>
<form name='frm' method='post' action="/include/addons/080deny/_receipt.pro.php">

	<div class="form_box_area">

		<table class="form_TB" summary="검색항목">
				<colgroup>
					<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
				</colgroup>
				<tbody>

					<tr>
						<td class="article">신청전화번호</td>
						<td class="conts">
							<input type="text" name="_deny_tel" class="input_text" style="width:150px" value='<?=$row_setup['s_deny_tel'] ?>' />
							<?=_DescStr("biz080.com에 신청하신 080 번호를 입력하시기 바랍니다.")?>
						</td>
					</tr>

					<tr>
						<td class="article">080 수신거부 사용여부</td>
						<td class="conts">
							<?=_InputRadio("_deny_use", array('Y','N'), $row_setup['s_deny_use']?$row_setup['s_deny_use']:'N', '', array('사용','미사용'), '')?>
						</td>
					</tr>

				</tbody> 
			</table>

			<?=_DescStr("biz080.com 사이트 신청 이후 아래 URL 주소를 <u>help@biz080.com</u> 로 보내주시기 바랍니다.")?>
			<?=_DescStr("080 수신거부 연동 URL : <strong>http://". $_SERVER["HTTP_HOST"] ."/include/addons/080deny/deny.php</strong>")?>
			<?=_DescStr("080 수신거부 기록은 <strong>회원관리 &gt; 080 수신거부 기록</strong>에 저장됩니다." , "orange")?>

	</div>
	<!-- 검색영역 -->

	<?=_submitBTNsub()?>

</form>



	<!-- 내부 서브타이틀 -->
	<div class="sub_title"><span class="icon"></span><span class="title">080 수신거부 서비스소개</span></div>

	<!-- // 내부 서브타이틀 -->
	<div class="form_box_area"><img src="/include/addons/080deny/images/info1.jpg" alt="080 수신거부 서비스소개"></div>
	<!-- 검색영역 -->

	<!-- 내부 서브타이틀 -->
	<div class="sub_title"><span class="icon"></span><span class="title">080 수신거부 이용절차</span></div>
	<!-- // 내부 서브타이틀 -->
	<div class="form_box_area">
		<img src="/include/addons/080deny/images/info2.jpg" alt="080 수신거부 이용절차" usemap="#imgmap">
		<map id="imgmap" name="imgmap">
			<area shape="rect" alt="신청서 작성 예시" title="신청서 작성 예시" coords="541,398,642,417" href="http://biz080.com/request_write_sample.php" target="_blank" />
			<area shape="rect" alt="080 가입신청서" title="080 가입신청서" coords="469,420,644,468" href="http://biz080.com/download/080_sms_request_form.zip" target="_blank" />
		</map>
	</div>
	<!-- 검색영역 -->
