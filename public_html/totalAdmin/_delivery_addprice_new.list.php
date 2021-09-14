<?PHP

	include_once("inc.header.php");


	$is_valide = _MQ_assoc(" SHOW TABLES LIKE 'odtDeliveryAddpriceNew'; ");
	if(count($is_valide)<1){
			
		echo '
			<style>
				.new_deny_guide {background:#2793a0; color:#fff; margin:20px; margin-bottom:0px; font-size:14px; padding:15px 20px; letter-spacing:-0.5px;}
				.new_deny_guide strong {text-decoration:underline; font-weight:400;}
			</style>

			<div class="new_deny_guide">	<a href="/totalAdmin/_delivery_addprice_new.pro.php?_mode=create_table" style="color:#fff;">※ 추가배송비를 사용하기 위해 <strong>“추가배송비 DB를 추가”</strong>해 주시기 바랍니다. </a> </div>
		';
		
		return;
	}



	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $k => $v) { if( is_array($v) ){foreach($v as $sk => $sv) { $_PVS .= "&" . $k . "[".$sk."]=$sv"; }}else {$_PVS .= "&$k=$v"; }}
	$_PVSC = enc('e' , $_PVS);

	// odtDeliveryAddpriceNew
	// 검색 체크
	$s_query = " where 1 ";
	if($pass_addr) $s_query .= " and da_addr like '%". $pass_addr ."%' ";

	$listmaxcount = 100 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtDeliveryAddpriceNew $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from odtDeliveryAddpriceNew {$s_query} ORDER BY da_addr asc limit $count , $listmaxcount ");
	

?>


	<style type="text/css">
		.inner_info {position:relative;}
		.inner_info .inner_title {position:absolute; left:0; top:0; padding:5px 0;}
		.inner_info ul {display:inline-block; padding-left:80px; font-weight:400px;}
		.inner_info ul li {margin-bottom:3px;}

		.hide_section { display:none;}

		.shop_btn_border {display: inline-block; float: left; border: 1px solid #a7a7a7; padding: 5px; background: #efefef;}
	</style>



	<!-- 검색영역 -->
	<div class="sub_title"><span class="icon"></span><span class="title">도서산간지역 검색</span></div>
	<div class="form_box_area">
		<form name="searchfrm" method="get" action='<?=$PHP_SELF?>' autocomplete='off' style="border:0;padding:0;">
			<input type="hidden" name="mode" value="search">
			<table class="form_TB">
				<colgroup>
					<col width="120px"/><col width="*"/>
				</colgroup>
					<tbody>
					<tr>
						<td class="article">주소검색</td>
						<td class="conts"><input type="text" name="pass_addr" class="input_text" value="<?=$pass_addr?>" style="width:300px;"></td>
					</tr>
				</tbody>
			</table>

			<!-- 버튼영역 -->
			<div class="top_btn_area">
				<div class="btn_line_up_center">
					<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
					<?if ($mode == search) {?>
					<span class="shop_btn_pack"><span class="blank_3"></span></span>
					<span class="shop_btn_pack"><a href="_delivery_addprice_new.list.php" class="medium gray" title="목록" >전체목록</a></span>
					<?}?>
					<span class="shop_btn_pack"><span class="blank_3"></span></span>
					<span class="shop_btn_pack"><a href="#none" onclick="trigger_address_frm()" class="medium red" title="도서산간지역추가" >도서산간지역추가</a></span>
					<span class="shop_btn_pack"><span class="blank_3"></span></span>
					<span class="shop_btn_pack"><a href="#none" onclick="trigger_excel_frm()" class="medium white" title="엑셀일괄업로드" >엑셀일괄업로드</a></span>
				</div>
			</div>
		</form>

	</div>
	<!-- // 검색영역 -->


	<div id="ja_address_frm" class="hide_section" style="<?=($addprice?'display:block;':'')?>">
		<div class="sub_title"><span class="icon"></span><span class="title">도서산간지역 추가</span></div>
		<!-- 검색영역 -->
		<div class="form_box_area">
			<input type="hidden" name="" id="default_addprice" value="<?=rm_str($addprice)?>" />
			<table class="form_TB">
				<colgroup>
					<col width="120px"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<td class="article">주소검색</td>
						<td class="conts">
							<span class="shop_btn_pack btn_input_blue"><input type="button" class="input_medium" onclick="new_post_view();return false;" title="" value="주소검색"></span>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack btn_input_gray js_reset_btn" style="display:none;" ><input type="button" class="input_medium" onclick="init_address();return false;" title="" value="초기화"></span>
						</td>
					</tr>
					<tr>
						<td class="article">상세정보</td>
						<td class="conts">
							<div id="js_address_data" data-text="주소검색 버튼을 눌러 추가할 주소를 검색해주세요."></div>
						</td>
					</tr>
					<tr>
						<td class="conts" colspan="2">
							<div class="line">
								<?=_DescStr("<b>[주소검색]</b>버튼을 눌러 추가할 도서산간 지역을 검색해주세요.")?>
								<?=_DescStr("주소를 검색하여 검색된 주소를 선택하면 선택한 주소가 포함된 선택가능한 주소의 목록이표시됩니다.")?>
								<?=_DescStr("검색된 주소의 목록중 추가할 지역을 <b>[선택추가]</b>버튼을 눌러 추가해주세요.")?>
								<?=_DescStr("<b>[수정추가]</b>로 도서산간 지역을 추가시 추가배송비가 적용될 행정구역단위까지 입력해주세요. 배송주소와 지역명이 일치해야 추가배송비가 적용됩니다.")?>
								<?=_DescStr("추가배송비는 반드시 숫자로만 공백없이 입력하셔야 합니다.")?>
								<?=_DescStr("추가배송비가 0원이면 도서산간지역은 추가되지만 추가배송비가 적용되지는 않습니다.")?>
							</div>

							<div class="line">
								<?=_DescStr("step1) <b>\"제주특별자치도 제주시 가령골길 \"</b>을 도서산간지역으로 추가시 <b>[주소검색]</b>버튼을 눌러 <b>\"제주특별자치도 제주시 가령골길\"</b>로 검색 하세요." , "orange")?>
								<?=_DescStr("step2) <b>검색된 주소들중에 \"제주특별자치도 제주시 가령골길 1\"</b>을 선택하세요.  " , "orange")?>
								<?=_DescStr("step3) <b>\"제주특별자치도 제주시\"</b>, <b>\"제주특별자치도 제주시 가령골길\"</b>, <b>\"제주특별자치도 제주시 가령골길 1\"</b>중  <b>\"제주특별자치도 제주시 가령골길\"</b>을 <b>[선택추가]</b> 버튼을 눌러 추가해주세요." , "orange")?>
								<?=_DescStr("지번주소와 도로명주소중 한가지만 선택하시면 됩니다." , "orange")?>
							</div>

							<div class="line">
								<?=_DescStr("<b>\"제주특별자치도 제주시\"</b>와 <b>\"제주특별자치도 제주시 가령골길\"</b> 두 주소가 모두 등록되어 있다면 <b>\"제주특별자치도 제주시 가령골길\"</b>이 먼저 적용됩니다. ")?>
								<?=_DescStr("지번주소와 도로명주소가 모두 등록되어있다면 도로명주소가 먼저 적용됩니다. ")?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>

		</div>
	</div>
	<!-- // 검색영역 -->



	<div id="ja_excel_frm" class="hide_section">
		<div class="sub_title"><span class="icon"></span><span class="title">엑셀일괄업로드</span></div>
		<!-- 검색영역 -->
		<form action="_delivery_addprice_new.pro.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="_mode" value="ins_excel" />
			<div class="form_box_area">
				<table class="form_TB">
					<colgroup>
						<col width="120px"/><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<td class="article">일괄 업로드</td>
							<td class="conts">
								<input type="file" name="excel_file" class="input_text">
							</td>
						</tr>
						<tr>
							<td class="conts" colspan="2">
								<div class="line">
									<?=_DescStr("도서산간 지역은 적용될 행정구역단위까지 입력해주세요. 배송주소와 지역명이 일치해야 추가배송비가 적용됩니다.")?>
									<?=_DescStr("도서산간 지역이 입력되지 않은 행은 도서산간 추가배송비 리스트에 추가되지 않습니다.")?>
									<?=_DescStr("이미 등록된 도서산간 지역은 추가배송비만 업데이트 됩니다. ")?>
									<?=_DescStr("추가배송비는 반드시 숫자로만 공백없이 입력하셔야 합니다.")?>
									<?=_DescStr("추가배송비가 0원이면 도서산간지역은 추가되지만 추가배송비가 적용되지는 않습니다.")?>
								</div>

								<div class="line">
									<?=_DescStr("<b>\"제주특별자치도 제주시\"</b>와 <b>\"제주특별자치도 제주시 가령골길\"</b> 두 주소가 모두 등록되어 있다면 <b>\"제주특별자치도 제주시 가령골길\"</b>이 먼저 적용됩니다. ")?>
									<?=_DescStr("지번주소와 도로명주소가 모두 등록되어있다면 도로명주소가 먼저 적용됩니다. ")?>
								</div>

								<div class="line">
									<?=_DescStr("<b>업로드 용량</b>에 따라 <b>다소시간이 걸릴 수 있습니다.</b>", "orange")?>
									<?=_DescStr("<b>업로드 파일</b>은 <b>최대 ".ini_get("upload_max_filesize")."까지 업로드 가능</b> 합니다.", "orange")?>
									<?=_DescStr("<b>엑셀97~2003 버전 파일만 업로드가 가능합니다. 엑셀 2007이상 버전은(xlsx) 다른 이름저장을 통해 97~2003버전으로 저장하여 등록하세요.</b>", "orange")?>
								</div>
							</td>
						</tr>
					</tbody>
				</table>

		<div class="top_btn_area">
			<div class="btn_line_up_center">
				<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="일괄업로드" value="일괄업로드"></span>
				<span class="shop_btn_pack"><span class="blank_3"></span></span>
				<span class="shop_btn_pack"><a href="/upfiles/normal/delivery_addprice_sample.xls" class="medium white" title="샘플파일다운로드" >샘플파일다운로드</a></span>
			</div>
		</div>

			</div>
		</form>
	</div>
	<!-- // 검색영역 -->


<form name="frm" method="post" action="" onsubmit="return false;">
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">
	<input type="hidden" name="_search_que" value="<?=enc('e',$s_query)?>">
	<div class="content_section_inner">

		<div class="ctl_btn_area">
			<span class="shop_btn_pack"><a href="#none" class="small red" title="선택삭제" onclick="selectDelete()">선택삭제</a></span>
			<span class="shop_btn_pack"><span class="blank_3"></span></span>
			<span class="shop_btn_pack"><a href="#none" class="small gray" title="선택엑셀다운로드" onclick="selectExcel()">선택엑셀다운로드</a></span>
			<span class="shop_btn_pack"><span class="blank_3"></span></span>
			<span class="shop_btn_pack"><a href="#none" class="small white" title="검색엑셀다운로드" onclick="searchExcel()">검색엑셀다운로드</a></span>
		</div>

		  <table class="list_TB">
			<colgroup>
				<col width="60px"/><col width="80px"/><col width="*"/><col width="150px"/><col width="150px"/><col width="90px"/>
			</colgroup>
			<thead>
			<tr>
			  <th class="colorset"><input type="checkbox" name="allchk"></th>
			  <th class="colorset">NO</th>
			  <th class="colorset">주소</th>
			  <th class="colorset">추가배송비</th>
			  <th class="colorset">등록일</th>
			  <th class="colorset"></th>
			</tr>
			</thead>
			<tbody>
				<tr style="background:#eee">
					<td colspan="3" style="text-align:left;">
						<?=_DescStr("추가배송비를 변경할 도서산간지역을 선택후 추가배송비 일괄변경시 선택된 지역의 추가배송비가 일괄적으로 변경됩니다.")?>
					</td>
					<td style="border-left:0">
						<input type="text" name="modify_addprice" id="js_modify_addprice" class="input_text" style="text-align:right; width:80px;" />
					</td>
					<td colspan="2" style="border-left:0">
						<span class="shop_btn_pack"><a href="#none" class="small white" title="선택 추가배송비 일괄변경" onclick="selectModify()">추가배송비 일괄변경</a></span>
					</td>
				</tr>
	<?PHP
		$i = 0;
		foreach($res as $k=>$v) {

			$_num = $TotalCount - $count - $k ;

			echo "
				<tr>
					<td><input type=checkbox name='chk_uid[]' value='".$v['da_uid']."' class=class_uid></td>
					<td>${_num}</td>
					<td style='text-align:left;'>" . $v['da_addr'] . "</td>
					<td>" . number_format($v['da_price'],0). "원</td>
					<td>" . date("Y-m-d", strtotime($v['da_rdate'])) ."</td>
					<td>
						";
			?>
				<span class='shop_btn_pack'>
					<input type=button value='수정' class='small gray' onclick="javascript:window.open('_delivery_addprice_new.form.php?_uid=<?=$v['da_uid']?>','add_delivery_price','width=800,height=300,scrollbars=yes');"></a>
				</span><span class="shop_btn_pack"><span class="blank_3"></span></span>
				<span class='shop_btn_pack'>
					<input type=button value='삭제' class='small red' onclick="del('_delivery_addprice_new.pro.php?_mode=delete&_uid=<?=$v['da_uid']?>&_PVSC=<?=$_PVSC?>');"></a>
				</span>
			<?php
			echo "
					</td>
				</tr>
			";

			$i++;

		}

		if( $i == 0 ) echo "<tr align='center'><td colspan='10' height='200'>등록된 정보가 없습니다.</td></tr>";
	?>
			</tbody>
		</table>

		<div class="list_paginate">
			<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=")?>
		</div>

	</div>
</form>


<!-- 주소검색 다음 API 호출 -->
<?php if($_SERVER['HTTPS']) { ?>
<script src="//spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>
<?php } else { ?>
<script src="//dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<?php } ?>
<script>
// 상세정보영역 초기화
function init_address(){
	$('#js_address_data').html($('#js_address_data').data('text'));
	$('.js_reset_btn').hide();
}
init_address();

// 주소추가
function insert_addprice(_idx){
	var key = $('#key_' + _idx).val();
	var addr = $('#addr_' + _idx).val();
	var addprice = $('#_addprice').val().replace(/[^0-9]/g,'')*1;
//	if(addprice<1){
//		alert("추가배송비를 입력해주시기 바랍니다. ");
//		$('#_addprice').focus();
//		return false;
//	}

	document.location.href = "_delivery_addprice_new.pro.php?_mode=add&key=" + encodeURI(key) + "&addr=" + encodeURI(addr) + "&addprice=" + addprice;
}

// 추가배송비 입력창 숫자만 입력
$(document).delegate("#_addprice", "focusin", function(){
	var _val = $(this).val().replace(/[^0-9]/g,'')*1;
	if(_val==0) _val = '';
	$(this).val(_val);
}).delegate("#_addprice", "focusout", function(){
	var _val = $(this).val().replace(/[^0-9]/g,'')*1+'';
	$(this).val(_val.comma());
});

// 도로명주소 우편번호 열기
function new_post_view(){
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
			// 시도 
			var sido = data.sido;
			// 시군구
			var sigungu = data.sigungu;
			// 읍면
			var bname1 = data.bname1;
			// 동리
			var bname2 = data.bname2;
			// 도로명
			var roadname = data.roadname;
			// 지번주소전체
			var jibunAddress = data.jibunAddress;
			// 도로명주소전체
			var roadAddress = data.roadAddress;

			// 추가배송비 - 기본값설정
			var addprice = $("#default_addprice").val()*1;

			// 추출된데이터전송
			$.ajax({
				url: '_delivery_addprice_new.pro.php',
				data: {'_mode':'ajax_form', 'sido':sido, 'sigungu':sigungu, 'bname1':bname1, 'bname2':bname2, 'roadname':roadname, 'jibunAddress':jibunAddress, 'roadAddress':roadAddress, 'addprice':addprice},
				type: 'post',
				dataType: 'html',
				success: function(data){
					$('#js_address_data').html(data);
					$('.js_reset_btn').show();//초기화버튼
					$('#_addprice').focus();
				}
			});

		}
	}).open();
}


// 도서산간지역추가 폼 열기/닫기
function trigger_address_frm(){
	var trigger = $("#ja_address_frm").css('display') == 'none' ? true : false;

	$(".hide_section").hide();
	if(trigger){
		$("#ja_address_frm").show();
	}
}


// 엑셀업로드 폼 열기/닫기
function trigger_excel_frm(){
	var trigger = $("#ja_excel_frm").css('display') == 'none' ? true : false;

	$(".hide_section").hide();
	if(trigger){
		$("#ja_excel_frm").show();
	}
}


// - 전체선택 / 해제
$(document).ready(function() {
	$("input[name=allchk]").click(function (){
		if($(this).is(':checked')){
			$('.class_uid').attr('checked',true);
		}
		else {
			$('.class_uid').attr('checked',false);
		}
	});
});


// 선택순위수정
 function selectDelete() {
	 if($('.class_uid').is(":checked")){
		 if(confirm("선택된 "+$('.class_uid:checked').length+"개의 도서산간지역을 삭제하시겠습니까?")){
			$("form[name=frm]").attr("action" , "_delivery_addprice_new.pro.php");
			$("input[name=_mode]").val('mass_delete');
			document.frm.submit();
		 }
	 }
	 else {
		 alert('1개 이상 선택하시기 바랍니다.');
	 }
 }


// 선택 추가배송비 일괄변경
 function selectModify() {
	 if($('.class_uid').is(":checked")){
		var _price = $('#js_modify_addprice').val().replace(/[^0-9]/g,'')*1;
		$('#js_modify_addprice').val(_price);

//		if(_price <1){
//			alert("변경할 추가배송비를 입력해 주시기 바랍니다.");
//			$('#js_modify_addprice').focus();
//			return false;
//		}

		_price = _price + '';
		 if(confirm("선택된 "+$('.class_uid:checked').length+"개의 도서산간지역의 추가배송비를 "+_price.comma()+"원으로 일괄 변경하시겠습니까?")){
			$("form[name=frm]").attr("action" , "_delivery_addprice_new.pro.php");
			$("input[name=_mode]").val('mass_modify');
			document.frm.submit();
		 }
	 }
	 else {
		 alert('1개 이상 선택하시기 바랍니다.');
	 }
 }


// 선택엑셀다운로드
 function selectExcel() {
	 if($('.class_uid').is(":checked")){
		$("form[name=frm]").attr("action" , "_delivery_addprice_new.pro.php");
		$("input[name=_mode]").val('select_excel');
		document.frm.submit();
		$("form[name=frm]").attr("action" , "_delivery_addprice_new.pro.php");
		$("input[name=_mode]").val('');
	 }
	 else {
		 alert('1개 이상 선택하시기 바랍니다.');
	 }
 }


// 검색엑셀다운로드
 function searchExcel() {
	$("form[name=frm]").attr("action" , "_delivery_addprice_new.pro.php");
	$("input[name=_mode]").val('search_excel');
	document.frm.submit();
	$("form[name=frm]").attr("action" , "_delivery_addprice_new.pro.php");
	$("input[name=_mode]").val('');
 }
</script>



<?PHP
	include_once("inc.footer.php");
?>