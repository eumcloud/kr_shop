<?PHP
include_once("inc.header.php");
?>



<form name="frm" method=post action="_config.default.pro.php" ENCTYPE='multipart/form-data'>

<!-- 내부 서브타이틀 -->
<div class="sub_title"><span class="icon"></span><span class="title">사이트설정</span></div>
<!-- // 내부 서브타이틀 -->

<div class="form_box_area">

	<table class="form_TB" summary="검색항목">
		<colgroup>
			<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
		</colgroup>
		<tbody>

			<tr>
				<td class="article">원데이넷 라이센스 정보</td>
				<td class="conts"><B><?=$row_setup[licenseNumber]?></B></td>
			</tr>

			<tr>
				<td class="article">카피라이트정보</td>
				<td class="conts">

					<div class="line">상점홈페이지 : <input type="text" name="homepage" class="input_text" style="width:500px" value='<?=$row_company[homepage] ?>' /><?=_DescStr("고객님의 상점에 대한 도메인을 설정 합니다.")?></div>

					<div class="line">담당휴대폰 : <input type="text" name="htel" class="input_text" style="width:150px" value='<?=$row_company[htel] ?>' /><?=_DescStr("주문접수 등의 SMS 수신을 위한 회사 핸드폰 번호를 설정 합니다.")?></div>

					<div class="line">담당E-mail : <input type="text" name="email" class="input_text" style="width:200px" value='<?=$row_company[email] ?>' /><?=_DescStr("주문내역/회원가입 등 상점에서 대표로 사용할 E-mail을 설정 합니다.")?></div>

					<div class="line">
						<a name="sms_send_tel"></a>
						전화번호 : <input type="text" name="tel" class="input_text" style="width:150px" value="<?=$row_company['tel'] ?>" />
						<?=_DescStr('SMS서비스 사용 시 <b>발신번호로 사용</b>됩니다.')?>
						<?php
						$SMSUser = onedaynet_sms_user();
						if($SMSUser['code'] == 'U04'||$SMSUser['code'] == 'U05'||$SMSUser['code'] == 'U06') {
							$Uniq = uniqid();
							echo '<script>$(document).ready(function () {setInterval("$(\'.blink_text_'.$Uniq.'\').fadeOut().fadeIn();",1000);});</script>';
							echo '<div class="blink_text_'.$Uniq.'">'._DescStr('<b style="font-size:15px; color:#ff0000">'.$SMSUser['data'].'</b>', 'orange').'</div>';
						}
						?>
					</div>

					<div class="line">팩스번호 : <input type="text" name="fax" class="input_text" style="width:150px" value='<?=$row_company[fax] ?>' /></div>

					<div class="line">통신판매신고번호 : <input type="text" name="number2" class="input_text" style="width:200px" value='<?=$row_company[number2] ?>' /><?=_DescStr("고객님 상점에 대한 통신판매업신고번호를 설정 합니다. (예: 마포통신 제0000호)")?></div>

					<div class="line">개인정보관리책임자 : <input type="text" name="name1" class="input_text" style="width:100px" value='<?=$row_company[name1] ?>' /><?=_DescStr("실질적인 상점 운영자 이름을 입력합니다.")?></div>	

					<div class="line">상점주소 : <input type="text" name="address" class="input_text" style="width:500px" value='<?=$row_company[address] ?>' /><?=_DescStr("반품/교환 등에 사용할 고객님의 상점 주소를 설정 합니다.")?></div>

					<div class="line">고객센터 운영시간 : <textarea name="officehour" style="width:500px;height:60px;" class="input_text"><?=$row_company[officehour]?></textarea><?=_DescStr("고객센터 운영시간을 간단히 입력하세요 (예: am09:00 ~ pm06:00 주말 및 공휴일은 휴무입니다.)")?></div>
				</td>
			</tr>

			<tr>
				<td class="article">에스크로 정보</td>
				<td class="conts">
					<div class="line">에스크로 인증 URL : <input type="text" name="escrow_url" class="input_text" style="width:500px" value='<?=$row_company[escrow_url] ?>' /><?=_DescStr("에스크로 사용시 인증 URL를 입력 하시면 됩니다. (에스크로 기능을 사용할 때만 입력 하시면 됩니다.)")?></div>
					<div class="line">에스크로 구매안전 배너 : <?=_PhotoForm( "../upfiles/normal" , "escrow_img"  , $row_company[escrow_img] )?></div>
				</td>
			</tr>

			<tr>
				<td class="article">사업자(세금계산서)정보</td>
				<td class="conts">
					<div class="line">상호명(법인명) : <input type="text" name="name" class="input_text" style="width:200px" value='<?=$row_company[name] ?>' /></div>
					<div class="line">대표자명 : <input type="text" name="ceoname" class="input_text" style="width:100px" value='<?=$row_company[ceoname] ?>' /></div>
					<div class="line">사업자등록번호 : <input type="text" name="number1" class="input_text" style="width:200px" value='<?=$row_company[number1] ?>' /><?=_DescStr("사업자등록번호는 현금영수증 발급 기능에 필수 항목입니다. 반드시 입력하세요.")?></div>
					<div class="line">사업장소재지 : <input type="text" name="taxaddress" class="input_text" style="width:500px" value='<?=$row_company[taxaddress] ?>' /></div>
					<div class="line">업태 : <input type="text" name="taxstatus" class="input_text" style="width:200px" value='<?=$row_company[taxstatus] ?>' /></div>
					<div class="line">종목 : <input type="text" name="taxitem" class="input_text" style="width:200px" value='<?=$row_company[taxitem] ?>' /></div>
				</td>
			</tr>

			<tr>
				<td class="article">관리자 로그인페이지</td>
				<td class="conts">

					<div class="line">고객센터 전화번호 : <input type="text" name="login_page_phone" class="input_text" style="width:200px" value='<?=$row_setup[login_page_phone] ?>' /><?=_DescStr("관리자 로그인페이지에 표시할 고객센터 전화번호를 입력합니다.")?></div>

					<div class="line">고객센터 이메일 : <input type="text" name="login_page_email" class="input_text" style="width:200px" value='<?=$row_setup[login_page_email] ?>' /><?=_DescStr("관리자 로그인페이지에 표시할 고객센터 이메일을 입력합니다.")?></div>

				</td>
			</tr>

		</tbody> 
	</table>
</div>
<!-- 검색영역 -->


<!-- 내부 서브타이틀 -->
<div class="sub_title"><span class="icon"></span><span class="title">기본설정</span></div>
<!-- // 내부 서브타이틀 -->

<div class="form_box_area">

	<table class="form_TB" summary="검색항목">
		<colgroup>
			<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
		</colgroup>
		<tbody>

			<tr>
				<td class="article">사이트명</td>
				<td class="conts"><input type="text" name="site_name" class="input_text" style="width:300px" value='<?=$row_setup[site_name] ?>' /></td>
			</tr>

			<tr>
				<td class="article">사용자 쿠폰문자발송 횟수</td>
				<td class="conts"><input type="text" name="smsMaxCount" class="input_text" style="width:20px" value='<?=$row_setup[smsMaxCount]?>' />회</td>
			</tr>
			<?php // LDD019 ?>
			<tr>
				<td class="article">비회원구매</td>
				<td class="conts">
					<?=_InputRadio("none_member_buy", array('Y','N'), $row_setup['none_member_buy']?$row_setup['none_member_buy']:'Y', '', array('적용','미적용'), '')?>
				</td>
			</tr>
			<?php // LDD019 ?>
			<tr>
				<td class="article">ReWrite 적용</td>
				<td class="conts">
					<?=_InputRadio( "rewrite_chk" , array('yes','no') , $row_setup[rewrite_chk] ? $row_setup[rewrite_chk] : "no" , "" , array('적용','미적용') , "")?>
					<?=_DescStr("rewrite 적용 시 상품당 고유 주소값을 가지게 됩니다.")?>
					<?=_DescStr("예) http://" . $_SERVER["HTTP_HOST"] . "/S2170789")?>
				</td>
			</tr>

			<tr>
				<td class="article">유효기간 알림설정</td>
				<td class="conts">
					<?=_InputRadio( "auto_endalim" , array('yes','no') , $row_setup[auto_endalim] ? $row_setup[auto_endalim] : "no" , "" , array('적용','미적용') , "")?>
					<?=_DescStr("유효기간 알림설정을 할 경우 유효기간이 15일 이내이며, 쿠폰을 사용하지 않았을 경우 팝업창으로 안내합니다.")?>
				</td>
			</tr>
			<tr>
				<td class="article">소셜커머스 기능 보이기</td>
				<td class="conts">
					<?=_InputRadio( "view_social_commerce" , array('Y','N') , $row_setup[view_social_commerce] ? $row_setup[view_social_commerce] : "Y" , "" , array('보이기','감추기') , "")?>
					<?=_DescStr("소셜커머스 기능을 감추면 남은시간, 마감임박 상품, 쿠폰상품 등이 노출되지 않습니다.")?>
				</td>
			</tr>

			<tr>
				<td class="article">휴면계정전환일수</td>
				<td class="conts">
					<input type="text" name="member_sleep_period" class="input_text" style="width:20px" value='<?=$row_setup[member_sleep_period]?>' />개월
					<?=_DescStr("개월단위로 지정한 휴면계정전환일수 보다 넘어간 만큼 로그인하지 않은 회원은 1일 1회 체크하여 휴면전환됩니다.")?>
				</td>
			</tr>
				<!-- 비밀번호 변경일수 관리자 지정 추가 lcy-->
			 <tr>
                <td class="article">비밀번호 갱신 안내주기</td>
                <td class="conts">
                    <input type="text" name="member_cpw_period" class="input_text" style="width:25px" value='<?=$row_setup[member_cpw_period]?>' />개월
                    <?=_DescStr("개월단위로 적어주면 회원의 비밀번호 변경날로 부터 지정한 날이 지났경우 비밀번호변경 안내 페이지가 노출됩니다.")?>
                </td>
	         </tr>
		</tbody> 
	</table>
</div>
<!-- 검색영역 -->

<!-- 내부 서브타이틀 -->
<div class="sub_title"><span class="icon"></span><span class="title">적립금설정</span></div>
<!-- // 내부 서브타이틀 -->

<div class="form_box_area">

	<table class="form_TB" summary="검색항목">
		<colgroup>
			<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
		</colgroup>
		<tbody>

			<tr>
				<td class="article">적립금 설정</td>
				<td class="conts">
					<div class="line">* 적립금은 <input class="input_text number_style" type=text name=paypoint style="width:60px" value='<?=$row_setup[paypoint]?>'>원부터 주문 시 현금처럼 사용가능합니다.</div>
					<div class="line">* 한 주문당 <input class="input_text number_style" type=text name=paypoint_limit style="width:60px" value='<?=$row_setup[paypoint_limit]?>'>원까지 사용할 수 있습니다. (0은 사용제한 없음)</div>
				</td>
			</tr>
			<tr>
				<td class="article">적립금 지급 설정</td>
				<td class="conts">
					<div class="line">* 회원가입시 <input class="input_text number_style" type=text name=paypoint_join style="width:50px"  value='<?=$row_setup[paypoint_join]?>'>원을 <input class="input_text number_style" type=text name=paypoint_joindate style="width:30px"  value='<?=$row_setup[paypoint_joindate]?>'>일후 적립 (0은 즉시 적립)</div>
					<div class="line">* 상품구매시 상품에 지정된 적립률(%)만큼의 적립금을 <input class="input_text number_style" type=text name=paypoint_productdate size=3 value='<?=$row_setup[paypoint_productdate]?>'>일후 적립 (0은 즉시 적립)</div>
				</td>
			</tr>
			<tr>
				<td class="article">액션포인트 지급 설정</td>
				<td class="conts">
					<div class="line">* 회원가입시 <input class="input_text number_style" type=text name="_action_join" style="width:50px"  value='<?=$row_setup[s_action_join]?>'>포인트를 적립합니다.</div>
					<div class="line">* 매일 첫 로그인시 <input class="input_text number_style" type=text name="_action_login" style="width:50px"  value='<?=$row_setup[s_action_login]?>'>포인트를 적립합니다.</div>
					<div class="line">* 상품토크 작성시 <input class="input_text number_style" type=text name="_action_talk" style="width:50px"  value='<?=$row_setup[s_action_talk]?>'>포인트를 적립합니다.</div>
					<div class="line">* 주문결제시 <input class="input_text number_style" type=text name="_action_order" style="width:50px"  value='<?=$row_setup[s_action_order]?>'>포인트를 적립합니다.</div>
				</td>
			</tr>
		</tbody> 
	</table>

</div>
<!-- 검색영역 -->

<!-- 내부 서브타이틀 -->
<div class="sub_title"><span class="icon"></span><span class="title">사이트 메타테그 설정</span></div>
<!-- // 내부 서브타이틀 -->
<!-- <div style=" margin-left:20px; margin-bottom:-15px; margin-top:15px;overflow:hidden; font-size:15px;font-weight:bold">◆ 사이트 메타테그 설정</div> -->
<div class="form_box_area">

	<table class="form_TB" summary="검색항목">
		<colgroup>
			<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
		</colgroup>
		<tbody>

			<tr>
				<td class="article">title ~ /title</td>
				<td class="conts">

					<div class="line">상점 타이틀(제목) : <input type="text" name="homepage_title" class="input_text" style="width:500px" value='<?=$row_company[homepage_title] ?>' /><?=_DescStr("브라우저 상단에 표시되어질 상점의 타이틀을 설정 합니다.")?></div>

					<div class="line"><input type=checkbox name="homepage_title_product" value='yes' <?=($row_company[homepage_title_product]=="yes") ? "checked":"";?>>상품페이지 노출시 타이틀을 상품명으로 적용<?=_DescStr("선택할 경우 상품페이지 노출시 선택한 상품명이 타이틀로 나오게 됩니다.")?></div>

				</td>
			</tr>

			<tr>
				<td class="article">메타태그 - Description</td>
				<td class="conts">
					<textarea name="metatag" class="input_text" style="width:90%;height:60px;" ><?=stripslashes($row_company[metatag])?></textarea>
					<?=_DescStr("<b>사이트를 설명하는 문구를 설정 합니다.(\"는 제외 하고 입력바랍니다.)</b>")?>
					<?=_DescStr("예시 : 원데이넷 소셜커머스 티켓몰 Ver5 솔루션")?>
				</td>
			</tr>

			<tr>
				<td class="article">메타태그 - Keywords</td>
				<td class="conts">
					<textarea name="metatag_keyword" class="input_text" style="width:90%;height:60px;" ><?=stripslashes($row_company[metatag_keyword])?></textarea>
					<?=_DescStr("<b>사이트를 대표하는 키워드를 설정 합니다.(\"는 제외 하고 입력바랍니다.)</b>")?>
					<?=_DescStr("예시 : 원데이넷, 소셜커머스,솔루션, 상상너머,티켓몰")?>
				</td>
			</tr>

			<tr>
				<td class="article">검색란 입력어</td>
				<td class="conts"><input type="text" name="s_search_keyword" class="input_text" style="width:400px" value='<?=$row_setup[s_search_keyword] ?>' /><?=_DescStr("검색 입력란 안에 노출되는 키워드 입니다.")?></td>
			</tr>

			<tr>
				<td class="article">추천 검색어</td>
				<td class="conts">
					<input type="text" name="s_recommend_keyword" class="input_text" style="width:600px" value='<?=$row_setup[s_recommend_keyword] ?>' />
					<?=_DescStr("검색 입력란 안에 노출되는 키워드 입니다.")?>
					<?=_DescStr("검색어는 콤마(,)로 구분하시기 바랍니다.")?>
				</td>
			</tr>

		</tbody> 
	</table>
</div>
<!-- // 검색영역 -->

<?=_submitBTNsub()?>

</form>

<?PHP
	include_once("inc.footer.php");
?>