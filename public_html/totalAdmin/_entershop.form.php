<?PHP
// 메뉴 지정 변수
$app_current_link = "/totalAdmin/_entershop.list.php";

include_once("inc.header.php");

if($_mode == "modify") {

	// 아이디로 접근시 시리얼 넘버로 전환 LDD001 {
	if($customerCode) {

		$serialnum = _MQ(" SELECT `serialnum` as `num` FROM odtMember WHERE id='" . $customerCode . "' and userType = 'C' ");
		error_loc('_entershop.form.php?_mode=modify&serialnum='.$serialnum['num'].'&_PVSC=');
	}
	// } LDD001 END

    $row = _MQ(" SELECT * FROM odtMember WHERE serialnum='" . $serialnum . "' ");
}
?>


<form name=frm method=post action=_entershop.pro.php enctype='multipart/form-data' >
<input type=hidden name=_mode value='<?=($_mode ? $_mode : "add")?>'>
<input type=hidden name=serialnum value='<?=$serialnum?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">


					<!-- 내부 서브타이틀 -->
					<div class="sub_title"><span class="icon"></span><span class="title">입점업체정보</span></div>
					<!-- // 내부 서브타이틀 -->

					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article">공급업체 아이디<?=($_mode == "modify" ? "" : "<span class='ic_ess' title='필수'></span>")?></td>
										<td class="conts">
											<?=( $_mode == "modify" ? "<B>" . $row[id] . "</B>" : "<input type=text name='id' value='' size=20 class='input_text' onblur='search(this)'> <span id='searchinnerHTML'></span>")?>
										</td>
									</tr>
									<tr>
										<td class="article">비밀번호<?=($_mode == "modify" ? "" : "<span class='ic_ess' title='필수'></span>")?></td>
										<td class="conts">
											<input type=password name="passwd" value='' size=20 class=input_text>
											<?=($_mode == "modify" ? _DescStr("변경할 경우에만 입력하세요.") : "")?>
										</td>
									</tr>
									<tr>
										<td class="article">비번확인<?=($_mode == "modify" ? "" : "<span class='ic_ess' title='필수'></span>")?></td>
										<td class="conts">
											<input type=password name="repasswd" value='' size=20 class=input_text>
											<?=_DescStr("다시 한번 입력하세요.")?>
										</td>
									</tr>
									<tr>
										<td class="article">밴더사명</td>
										<td class="conts"><input type=text name="bannder" value='<?=$row[bannder]?>' size=30 class=input_text></td>
									</tr>
									<tr>
										<td class="article">공급업체명<span class="ic_ess" title="필수"></span></td>
										<td class="conts"><input type=text name="cName" value='<?=$row[cName]?>' size=30 class=input_text></td>
									</tr>
									<tr>
										<td class="article">사업자번호 (주민번호)</td>
										<td class="conts"><input type=text name="cNumber" value='<?=$row[cNumber]?>' size=30 class=input_text></td>
									</tr>
									<tr>
										<td class="article">대표자</td>
										<td class="conts"><input type=text name="ceoName" value='<?=$row[ceoName]?>' size=30 class=input_text></td>
									</tr>
									<tr>
										<td class="article">업태</td>
										<td class="conts"><input type=text name="cItem1" value='<?=$row[cItem1]?>' size=50 class=input_text></td>
									</tr>
									<tr>
										<td class="article">업종</td>
										<td class="conts"><input type=text name="cItem2" value='<?=$row[cItem2]?>' size=50 class=input_text></td>
									</tr>
								</tbody> 
							</table>
				
					</div>

					<!-- 내부 서브타이틀 --><!-- LMH003 -->
					<div class="sub_title"><span class="icon"></span><span class="title">지도 및 주소 설정</span></div>
					<!-- // 내부 서브타이틀 -->

					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
							<colgroup>
								<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
							</colgroup>
							<tbody>
							<tr>
								<td class="article">지도 및 주소<span class="ic_ess" title="필수"></span></td>
								<td class="conts">
								<? if($row[com_mapx] && $row[com_mapy]) { $coordinate = $row[com_mapx].", ".$row[com_mapy]; ?>
								<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
								<script type="text/javascript">
									function initialize() {

										var latlng = new google.maps.LatLng(<?=$coordinate?>);
										var myOptions = {
											zoom: 18,
											center: latlng,
											disableDefaultUI: false,
											scrollwheel: false,
											mapTypeId: google.maps.MapTypeId.ROADMAP
										};

										var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

										var marker_0 = new google.maps.Marker({
											draggable: true,
											position: new google.maps.LatLng(<?=$coordinate?>),
											map : map
										});

										google.maps.event.addListener(marker_0, 'dragend', function (event) {
											document.getElementById("com_mapx").value = this.getPosition().lat();
											document.getElementById("com_mapy").value = this.getPosition().lng();
										});

									}
									jQuery(document).ready(function($) {
										initialize();
									});	
								</script>
								<div id="map_canvas" style="width:600px;height:338px;margin-bottom:5px;"></div>
								<? } ?>

									주소 : <input type=text name="address" value='<?=$row[address]?>' size=85 class=input_text>
									<div style="margin: 5px 0;">
									X좌표 : <input type="text" name="com_mapx" id="com_mapx" class="input_text" size="25" value="<?=$row[com_mapx]?>">&nbsp;&nbsp;&nbsp;				
									Y좌표 : <input type="text" name="com_mapy" id="com_mapy" class="input_text" size="25" value="<?=$row[com_mapy]?>">
									</div>
									<?=_DescStr("주소를 등록하시고 저장을 하시면 지도를 확인할 수 있습니다 (사용자페이지 업체정보 아래 표시됨).")?>
									<?=_DescStr("주소 등록시 좌표는 자동으로 등록되며, 지도위치를 변경하시려면 X, Y 좌표를 삭제하신 후 주소 변경 후 수정하시면 됩니다.")?>
									<?=_DescStr("주소 등록시 주변 경관을 설명하는 문구(OO주유소 근처, 교차로 부근 등)를 입력할 경우 좌표 검색이 되지 않을 수 있으니 주의하시기 바랍니다.")?>
									<?=_DescStr("좌표를 입력하면 지도가 표시됩니다. 세밀한 조정을 원하시면 빨간 마커를 드래그하여 원하는 위치에 놓으시면 됩니다.")?>
								</td>
							</tr>
							</tbody>
						</table>
					</div>

					<!-- 내부 서브타이틀 -->
					<div class="sub_title"><span class="icon"></span><span class="title">담당자정보</span></div>
					<!-- // 내부 서브타이틀 -->

					<div class="form_box_area">
							<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article">담당자명</td>
										<td class="conts"><input type=text name="name" value='<?=$row[name]?>' size=30 class=input_text></td>
									</tr>
									<tr>
										<td class="article">E-mail<span class="ic_ess" title="필수"></span></td>
										<td class="conts"><input type=text name="email" value='<?=$row[email]?>' size=30 class=input_text></td>
									</tr>
									<tr>
										<td class="article">전화번호<span class="ic_ess" title="필수"></span></td>
										<? $app_tel = $row[tel1]?tel_format($row[tel1].$row[tel2].$row[tel3]):'';?>
										<td class="conts"><input type=text name="tel" value='<?=$app_tel?>' size=20 class=input_text><?=_DescStr("하이푼(-)을 포함하시기 바랍니다.")?></td>
									</tr>
									<tr>
										<td class="article">휴대폰번호</td>
										<? $app_htel = $row[htel1]?tel_format($row[htel1].$row[htel2].$row[htel3]):'';?>
										<td class="conts"><input type=text name="htel" value='<?=$app_htel?>' size=20 class=input_text><?=_DescStr("하이푼(-)을 포함하시기 바랍니다.")?></td>
									</tr>
									<tr>
										<td class="article">팩스번호</td>
										<? $app_ofax = $row[ofax1]?tel_format($row[ofax1].$row[ofax2].$row[ofax3]):'';?>
										<td class="conts"><input type=text name="ofax" value='<?=$app_ofax?>' size=20 class=input_text><?=_DescStr("하이푼(-)을 포함하시기 바랍니다.")?></td>
									</tr>
									<tr>
										<td class="article">홈페이지</td>
										<td class="conts"><input type=text name="homepage" value='<?=$row[homepage]?>' size=60 class=input_text></td>
									</tr>
								</tbody> 
							</table>
				
					</div>


<?//JJC003 - 묶음배송 관련?>
					<div class="sub_title"><span class="icon"></span><span class="title">배송설정</span></div>
					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
							<colgroup>
								<col width="200px"/>
								<col width="*"/>
							</colgroup>
							<tbody>
								<tr>
									<td class="article">기본배송비</td>
									<td class="conts"><input type="text" name="com_delprice" class="input_text number_style" style="width:60px" value='<?=($_mode =='modify' ? $row['com_delprice'] : $row_setup['s_delprice'])?>' />원
									<?=_DescStr("무료배송은 0을 입력하세요.")?>
									</td>
								</tr>
								<tr>
									<td class="article">무료배송비</td>
									<td class="conts"><input type="text" name="com_delprice_free" class="input_text number_style" style="width:60px" value='<?=($_mode =='modify' ? $row['com_delprice_free'] : $row_setup['s_delprice_free'])?>' />원
									<?=_DescStr("0 입력 시 무료배송비가 적용되지 않고, 항상 배송비가 적용됩니다.")?>
									</td>
								</tr>
								<tr>
									<td class="article">지정택배사</td>
									<td class="conts">
									<?=_InputSelect( "com_del_company" , array_keys($arr_delivery_company), ($row['com_del_company'] ? $row['com_del_company'] : $row_setup['s_del_company']) , "" , "" , "") ?>
									</td>
								</tr>

								<?php
									// 추가배송비 설정 추가 2017-05-19 :: SSJ { 
									// 최초 등록시 운영업체 설정 불러옴
									$row['com_del_addprice_use'] = $row['com_del_addprice_use'] ? $row['com_del_addprice_use'] : $row_setup['s_del_addprice_use'];
									$row['com_del_addprice_use_normal'] = $row['com_del_addprice_use_normal'] ? $row['com_del_addprice_use_normal'] : $row_setup['s_del_addprice_use_normal'];
									$row['com_del_addprice_use_unit'] = $row['com_del_addprice_use_unit'] ? $row['com_del_addprice_use_unit'] : $row_setup['s_del_addprice_use_unit'];
									$row['com_del_addprice_use_free'] = $row['com_del_addprice_use_free'] ? $row['com_del_addprice_use_free'] : $row_setup['s_del_addprice_use_free'];
								?>
								<tr>
									<td class="article">추가배송비 설정<span class="ic_ess" title="필수"></span></td>
									<td class="conts">
										<?=_InputRadio("_del_addprice_use", array('Y', 'N'), $row['com_del_addprice_use']? $row['com_del_addprice_use']:'N', ' class="del_addprice_use"', array('사용함','사용안함'), "")?>
										<?=_DescStr("'사용함' 설정시 도서산간 추가배송비 설정에따라 추가배송비가 적용됩니다.")?>
										<?php if($row_setup['s_del_addprice_use']<>"Y"){ ?>
											<?=_DescStr("운영업체의 추가배송비 설정이 '사용안함'으로 설정되었습니다. 입점업체의 설정은 적용되지 않습니다.", "orange")?>
										<?php } ?>

										<div class="line del_addprice_detail" style="<?php echo ($row['com_del_addprice_use']<>"Y"?"display:none;":""); ?>">
											* 일반배송상품에 추가배송비를 적용합니다.(필수적용)<br>
											* 일반배송상품을 무료배송비이상 구매하여 무료배송이 되었을때 추가배송비를 
											(
												<label><input type="radio" name="_del_addprice_use_normal" value="Y" <?php echo ($row['com_del_addprice_use_normal']=="Y"?" checked ":"");?>>적용합니다.</label>
												<label><input type="radio" name="_del_addprice_use_normal" value="N" <?php echo ($row['com_del_addprice_use_normal']<>"Y"?" checked ":"");?>>적용하지 않습니다.</label>
											)
										</div>
										<div class="line del_addprice_detail" style="<?php echo ($row['com_del_addprice_use']<>"Y"?"display:none;":""); ?>">
											* 개별배송상품에 추가배송비를 
											(
												<label><input type="radio" name="_del_addprice_use_unit" value="Y" <?php echo ($row['com_del_addprice_use_unit']=="Y"?" checked ":"");?>>적용합니다.</label>
												<label><input type="radio" name="_del_addprice_use_unit" value="N" <?php echo ($row['com_del_addprice_use_unit']<>"Y"?" checked ":"");?>>적용하지 않습니다.</label>
											)
										</div>
										<div class="line del_addprice_detail" style="<?php echo ($row['com_del_addprice_use']<>"Y"?"display:none;":""); ?>">
											* 무료배송상품에 추가배송비를 
											(
												<label><input type="radio" name="_del_addprice_use_free" value="Y" <?php echo ($row['com_del_addprice_use_free']=="Y"?" checked ":"");?>>적용합니다.</label>
												<label><input type="radio" name="_del_addprice_use_free" value="N" <?php echo ($row['com_del_addprice_use_free']<>"Y"?" checked ":"");?>>적용하지 않습니다.</label>
											)
										</div>

										<script>
											$(function() {
												$('.del_addprice_use').on('click', function() {

													var Value = $(this).val();
													if(Value == 'Y') $('.del_addprice_detail').show();
													else $('.del_addprice_detail').hide();
												});
											})
										</script>
									</td>
								</tr>
								<?php // } 추가배송비 설정 추가 2017-05-19 :: SSJ ?>

								<tr>
									<td class="conts" colspan=2>
										<?=_DescStr("배송상품을 판매하는 업체에서만 입력하시면 됩니다.")?>
										<?=_DescStr("입점업체의 배송정책이 없을 경우 기본배송설정으로 적용됩니다.")?>
									</td>
								</tr>
							</tbody> 
						</table>
					</div>
<?//JJC003 - 묶음배송 관련?>


					<div class="sub_title"><span class="icon"></span><span class="title">정산 계좌정보</span></div>
					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
							<colgroup>
								<col width="200px"/><col width="*"/>
							</colgroup>
							<tbody>
								<tr>
									<td class="article">은행명</td>
									<td class="conts"><input type=text name="account_bank" value='<?=$row[account_bank]?>' size=20 class=input_text></td>
								</tr>
								<tr>
									<td class="article">계좌번호</td>
									<td class="conts"><input type=text name="account_deposit" value='<?=$row[account_deposit]?>' size=40 class=input_text></td>
								</tr>
								<tr>
									<td class="article">예금주</td>
									<td class="conts"><input type=text name="account_name" value='<?=$row[account_name]?>' size=20 class=input_text></td>
								</tr>
							</tbody>
						</table>
					</div>


					<?=_submitBTN("_entershop.list.php")?>


</form>



<?PHP
	include_once("inc.footer.php");
?>



<SCRIPT LANGUAGE="JavaScript">
	function search(obj) {
		common_frame.location.href= "_entershop.ajax.php?id="+obj.value;
	}
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript">

    $(document).ready(function(){
		// -  validate --- 
        $("form[name=frm]").validate({
			ignore: "input[type=text]:hidden",
            rules: {
<?if( $_mode == "modify" ) {?>
                repasswd: { equalTo: "input[name=passwd]" },// 비번확인
<?}else {?>
                id: { required: true },// 아이디
                passwd: { required: true },// 비밀번호
                repasswd: { required: true , equalTo: "input[name=passwd]" },// 비번확인
<?}?>
				cName: { required: true },// 공급업체명
				address: { required: true },// 주소
				email: { required: true , email:true},//이메일
				tel: { required: true },//전화번호
				//htel: { phone:true},//휴대폰번호
				//ofax: { phone:true}//팩스
            },
            messages: {
<?if( $_mode == "modify" ) {?>
                repasswd: { equalTo: "비밀번호가 다릅니다." },
<?}else {?>
                id: { required: "아이디를 입력하시기 바랍니다." },// 비밀번호
                passwd: { required: "비밀번호를 입력하시기 바랍니다." },// 비밀번호
                repasswd: { required: "비번확인을 입력하시기 바랍니다." , equalTo: "비밀번호가 다릅니다." },// 비번확인
<?}?>
				cName: { required: "공급업체명을 입력하시기 바랍니다." },// 공급업체명
				address: { required: "주소를 입력하시기 바랍니다." },// 주소
				email: { required: "이메일을 입력하시기 바랍니다." , email:"이메일이 올바르지 않습니다."},//이메일
				tel: { required: "전화번호를 입력하시기 바랍니다."},//전화번호
				//htel: { phone:"휴대폰번호가 올바르지 않습니다."},//휴대폰번호
				//ofax: { phone:"팩스가 올바르지 않습니다."}//팩스
            }
        });
		// - validate --- 
	});

</SCRIPT>
