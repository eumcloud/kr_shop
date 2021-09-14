<?PHP
# LDD014
# Error Reproting level modify
error_reporting(E_ALL ^ E_NOTICE);

// 페이지 표시
$app_current_link = "/totalAdmin/_product.list.php";
$app_current_page_name = '업로드 수정/확인'; // 2015-09-30 추가
include_once("inc.header.php");

# Excel Class Load
require_once($_SERVER['DOCUMENT_ROOT']."/include/reader.php");

# 첨부파일 확인
if($_FILES['excel_file']['size'] <= 0) error_loc_msg("_product.list.php", "첨부파일이 없습니다.");


# 첨부파일 확장자 검사
$ext = '';
$ext = substr(strrchr($_FILES['excel_file']['name'],"."),1); //확장자앞 .을 제거하기 위하여 substr()함수를 이용
$ext = strtolower($ext); //확장자를 소문자로 변환
if($ext != 'xls') error_loc_msg("_product.list.php", "xls 파일만 업로드 가능합니다.".$ext);

# Excel Load
$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('utf-8');
$data->read($_FILES['excel_file']['tmp_name']);
//die(ViewArr($data->sheets[0]['cells'])); // 출력해보기

# 입점업체 정보 호출
$arr_customer = arr_company();

# 담당 MD정보 호출
$arr_mdlist = arr_mdlist();


// -- 1차 카테고리 배열 적용 ---
$arr_parent01 = array();
$cres = _MQ_assoc("select catecode , catename from odtCategory where cHidden='no' and catedepth='1' order by cateidx asc ");
foreach( $cres as $k=>$v ){
	$arr_cate01[$v[catecode]] = $v[catename];
}
// -- 1차 카테고리 배열 적용 ---

// 상품 코드를 미리 가져와서 배열로 넣는다 -- LCY
$pcode_que = _MQ_assoc("select code from odtProduct");
$pcode_arr = array();
foreach($pcode_que as $pk=>$pv){
	$pcode_arr[]=$pv['code'];
}
// 상품 코드를 미리 가져와서 배열로 넣는다  -- LCY

?>
<script>
/*
//ctrl+N , ctrl+R , F5 차단
function doNotReload(){

	if((event.ctrlKey == true && (event.keyCode == 78 || event.keyCode == 82)) || (event.keyCode == 116) ) {

		alert('해당페이지에서 새로고침을 할 수 없습니다.');
		event.keyCode = 0;
		event.cancelBubble = true;
		event.returnValue = false;
	}
}
document.onkeydown = doNotReload;
*/
</script>
<form action="_product.upload.pro.php" method="post">
	<div class="form_box_area">
		<!-- 가이드 {-->
		<div>
			<table class="form_TB">
				<tbody>
					<tr>
						<td style="padding-bottom:20px">
							<?=_DescStr("<b>처리 수</b>에 따라 <b>다소시간이 걸릴 수 있습니다.</b>")?>
							<?=_DescStr("해당 페이지에서 <b>등록처리</b>버튼을 눌러 저장 하지 않으면 등록되지 않습니다.")?>
							<?=_DescStr("해당 페이지에서 <b>새로고침</b>을 할 경우 문제가 생길 수 있습니다.")?>
							<?=_DescStr("<b>수정</b>되는 상품의 분류(카테고리) 엑셀 데이터는 무시됩니다.")?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<!--} 가이드 -->

		<!-- 엑셀내용 {-->
		<div style="overflow-x:scroll; width:100%; height:500px;" class="new_product_upload">

			<table class="form_TB" style="width:7400px">
				<thead>
					<tr>
						<th class="article" style="width:60px">등록구분</th>
						<th class="article" style="width:100px">상품코드</th>
						<th class="article" style="width:100px">상품명<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:400px">카테고리<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:190px">상품공급업체 아이디<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:190px">담당MD 이름<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:100px">판매설정<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:105px">판매시작일(상시의 경우 제외)<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:105px">판매종료일(상시의 경우 제외)<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:80px">업체정산형태<br>(공급가/수수료)<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:100px">매입가격(공급가격)<br>수수료<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:140px">상품구분<br>(배송상품/쿠폰상품)<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:120px">배송구분<br>(일반/개별/무료)<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:90px">개별배송비<br>(개별배송일 경우 필수)<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:90px">정상가격</th>
						<th class="article" style="width:90px">판매가격<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:70px">할인률<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:325px">상품 상세설명<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:325px">상품 상세설명<br>(모바일)</th>
						<th class="article" style="width:325px">주문확인서 주의사항<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:160px">상품이미지<br>(메인: 480 x 490)<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:160px">상품이미지<br>(정사각형: 330 x 330)<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:160px">상품이미지<br>(직사각형: 489 x 330)<span class="ic_ess" title="필수"></span></th>
						<th class="article" style="width:80px">상품노출</th>
						<th class="article" style="width:65px">노출순서</th>
						<th class="article" style="width:90px">추천상품</th>
						<th class="article" style="width:160px">상품쿠폰 쿠폰명</th>
						<th class="article" style="width:70px">상품쿠폰<br>할인률</th>
						<th class="article" style="width:160px">1차 옵션 타이틀</th>
						<th class="article" style="width:160px">2차 옵션 타이틀</th>
						<th class="article" style="width:160px">3차 옵션 타이틀</th>
						<th class="article" style="width:100px">중복구매<br>불가여부</th>
						<th class="article" style="width:70px">적립금</th>
						<th class="article" style="width:70px">재고량</th>
						<th class="article" style="width:70px">1회 최대 구매량</th>
						<th class="article" style="width:70px">현 판매량</th>
						<th class="article" style="width:70px">관련상품<br>지정방식</th>
						<th class="article" style="width:190px">수동 관련상품(상품코드별 구분은 /)</th>
						<th class="article" style="width:325px">간략 상세정보</th>
						<th class="article" style="width:325px">상품 사용 정보</th>
						<th class="article" style="width:325px">업체 이용 정보</th>
						<th class="article" style="width:160px">지도주소<br>(공급업체 주소를 사용할 경우 "공급업체"를 기입)</th>
						<th class="article" style="width:105px">쿠폰사용만료일</th>
						<th class="article" style="width:60px">관리</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($data->sheets[0]['cells'] as $key=>$val) {

						if($key == 1) continue; // 제목라인 제외
						if($val[1]) {

							$code = $val[1];
							$ActionType = 'm';
						}
						else {

							$acc_cnt = 1000; // 과부하를 위해 1000 까지만 LCY 이게 없을 경우 내부적으로 오류발생시 무한로딩에 빠진다.
							for($i=0;$i<$acc_cnt;$i++){
								## 코드 생성.
								$random = rand(10000,99999);
								$sumTme = (time(Y)+time(m)+time(d)+time(H)+time(i)+time(s)+19)*997;
								$sumTempLength = strlen($sumTme);
								$checkSum = substr($sumTme,$sumTempLength-2,2);
								$code = "S".$checkSum.$random;
								if(in_array($code,$pcode_arr) == false){  // 카테고리 중복 검색
									$pcode_arr[] = $code;
									break;
								}else{
									continue;
								}
							}

							$ActionType = 'a';
						}

						# 날짜 치환
						$sale_date_exp = explode('/', $val[9]);
						$sale_enddate_exp = explode('/', $val[10]);
						$expire_exp = explode('/', $val[44]);
						if($sale_date_exp[1]) $sale_date = $sale_date_exp[2].'-'.$sale_date_exp[1].'-'.$sale_date_exp[0];
						else $sale_date = date('Y-m-d', strtotime($sale_date_exp[0]));
						if($sale_enddate_exp[1]) $sale_enddate = $sale_enddate_exp[2].'-'.$sale_enddate_exp[1].'-'.$sale_enddate_exp[0];
						else $sale_enddate = date('Y-m-d', strtotime($sale_enddate_exp[0]));
						if($expire_exp[1]) $expire = $expire_exp[2].'-'.$expire_exp[1].'-'.$expire_exp[0];
						else $expire = date('Y-m-d', strtotime($expire_exp[0]));
					?>
					<tr>
						<td class="conts" style="width:60px">
							<input type="hidden" name="code[]" value="<?php echo $code; ?>">
							<input type="hidden" name="mode[<?php echo $code; ?>]" value="<?php echo $ActionType; ?>">
							<?php if($ActionType == 'm') { ?>
							<span class="shop_state_pack"><span class="blue">수정</span></span>
							<?php } else { ?>
							<span class="shop_state_pack"><span class="red">추가</span></span>
							<?php } ?>
						</td>
						<td class="conts" style="width:100px">
							<?php echo $code; ?>
						</td>
						<td class="conts" style="width:100px">
							<input type="text" name="name[<?php echo $code; ?>]" class="input_text" value="<?php echo $val[2]; ?>" required>
						</td>
						<td class="conts" style="width:700px">
							<?php
							// 추가 상품이라면 카테고리를 추가 해준다.
							if($val[3] && $val[4] && $val[5]) {

								$cateLoad = _MQ("
												select
													c3.catecode as cc3,
													c2.catecode as cc2,
													c1.catecode as cc1
												from
													odtCategory as c3 left join
													odtCategory as c2 on (substring_index(c3.parent_catecode , ',' ,-1) = c2.catecode and c2.catedepth = 2 and c2.catename = '{$val[4]}' )  left join
													odtCategory as c1 on (substring_index(c3.parent_catecode , ',' ,1) = c1.catecode and c1.catedepth = 1 and c1.catename = '{$val[3]}')
												where
													c3.catename = '{$val[5]}'
													AND c3.catedepth =3
													AND c2.catename =  '".$val[4]."'
													AND c2.catedepth =2
													AND c1.catename =  '".$val[3]."'
													AND c1.catedepth =1
											");
							}
							$uniqid = uniqid();

							if($ActionType == 'm') $cateLoadOld = _MQ(" select * from odtProductCategory where pct_pcode = '{$code}' order by pct_uid asc ");
							else $cateLoadOld['pct_cuid'] = '';
							?>
							<input type="hidden" name="catecode_old[<?php echo $code; ?>]" value="<?php echo $cateLoadOld['pct_cuid']; ?>">
							<input type="hidden" name="catecode[<?php echo $code; ?>]" class="catecode_<?php echo $uniqid; ?>" value="<?php echo $cateLoad['cc3']; ?>">
							<?=_InputSelect( "pass_cate01_".$uniqid, array_keys($arr_cate01) , ($cateLoad['cc1']?$cateLoad['cc1']:''), "onchange=\"category_select_upload(1, '{$uniqid}', '".($cateLoad['cc2']?$cateLoad['cc2']:'')."');\" " , array_values($arr_cate01) , "-선택-") ?>&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;
							<?=_InputSelect( "pass_cate02_".$uniqid, array() , "", "onchange=\"category_select_upload(2, '{$uniqid}', '".($cateLoad['cc3']?$cateLoad['cc3']:'')."');\" " , array() , "-선택-") ?>&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;
							<?=_InputSelect( "pass_cate03_".$uniqid, array() , "", " onchange=\"$('.catecode_{$uniqid}').val($(this).find('option:selected').val());\" " , array() , "-선택-") ?>
							<script>
							$(function() {

								autoload_category('<?php echo $uniqid; ?>', '<?php echo ($cateLoad['cc1']?$cateLoad['cc1']:''); ?>', '<?php echo ($cateLoad['cc2']?$cateLoad['cc2']:''); ?>', '<?php echo ($cateLoad['cc3']?$cateLoad['cc3']:''); ?>');
							});
							</script>
						</td>
						<td class="conts" style="width:190px">
							<?PHP
							// - 공급업체 ---
							echo _InputSelect("customerCode[{$code}]", array_keys($arr_customer), $val[6], "", array_values($arr_customer), "-공급업체-");
							?>
						</td>
						<td class="conts" style="width:190px">
							<?php
							echo _InputSelect("md_name[{$code}]" , $arr_mdlist , $val[7], '' , "", "-담당MD-");
							?>
						</td>
						<td class="conts" style="width:190px;">
							<label><input type="radio" name="sale_type[<?php echo $code; ?>]" value="A"<?php echo (!preg_match("/기간/i" , $val[8])?' checked':''); ?>> 상시판매</label><br>
							<label><input type="radio" name="sale_type[<?php echo $code; ?>]" value="T"<?php echo (preg_match("/기간/i" , $val[8])?' checked':''); ?>> 기간판매</label>
						</td>
						<td class="conts" style="width:105px">
							<input type="text" name="sale_date[<?php echo $code; ?>]" class="input_text" size="11" value="<?php echo $sale_date; ?>">
						</td>
						<td class="conts" style="width:105px">
							<input type="text" name="sale_enddate[<?php echo $code; ?>]" class="input_text" size="11" value="<?php echo $sale_enddate; ?>">
						</td>
						<td class="conts" style="width:80px">
							<label><input type="radio" name="comSaleType[<?php echo $code; ?>]" value="공급가"<?php echo ($val[11]=='공급가' || !$val[11]?' checked':''); ?>> 공급가</label><br>
							<label><input type="radio" name="comSaleType[<?php echo $code; ?>]" value="수수료"<?php echo ($val[11]=='수수료'?' checked':''); ?>> 수수료</label>
						</td>
						<td class="conts" style="width:100px">
							<?php if($val[11]=='공급가' || !$val[11]) { ?>
							<input type="text" name="purPrice[<?php echo $code; ?>]" class="input_text" size="8" value="<?php echo $val[12]; ?>" required>
							<input type="hidden" name="commission[<?php echo $code; ?>]" class="input_text" size="8" value="0">
							<?php } else { ?>
							<input type="hidden" name="purPrice[<?php echo $code; ?>]" class="input_text" size="8" value="0">
							<input type="text" name="commission[<?php echo $code; ?>]" class="input_text" size="8" value="<?php echo $val[12]; ?>" required>
							<?php } ?>
						</td>
						<td class="conts" style="width:140px">
							<label><input type="radio" name="setup_delivery[<?php echo $code; ?>]" value="Y"<?php echo ($val[13] == '배송상품' || !$val[13]?' checked':''); ?>> 배송상품</label><br>
							<label><input type="radio" name="setup_delivery[<?php echo $code; ?>]" value="N"<?php echo ($val[13] == '쿠폰상품'?' checked':''); ?>> 쿠폰상품</label>
						</td>
						<td class="conts" style="width:120px">
							<label><input type="radio" name="del_type[<?php echo $code; ?>]" value="normal"<?php echo ($val[14] == '일반' || !$val[14]?' checked':''); ?>> 일반</label><br>
							<label><input type="radio" name="del_type[<?php echo $code; ?>]" value="unit"<?php echo ($val[14] == '개별'?' checked':''); ?>> 개별</label><br>
							<label><input type="radio" name="del_type[<?php echo $code; ?>]" value="free"<?php echo ($val[14] == '무료'?' checked':''); ?>> 무료</label>
						</td>
						<td class="conts" style="width:90px">
							<input type="text" name="del_price[<?php echo $code; ?>]" class="input_text" size="8" value="<?php echo ($val[15]?$val[15]:0); ?>" required> 원
						</td>
						<td class="conts" style="width:90px">
							<input type="text" name="price_org[<?php echo $code; ?>]" class="input_text" size="8" value="<?php echo ($val[16]?$val[16]:0); ?>"> 원
						</td>
						<td class="conts" style="width:90px">
							<input type="text" name="price[<?php echo $code; ?>]" class="input_text" size="8" value="<?php echo ($val[17]?$val[17]:0); ?>" required> 원
						</td>
						<td class="conts" style="width:70px">
							<input type="text" name="price_per[<?php echo $code; ?>]" class="input_text" size="3" value="<?php echo ($val[18]?$val[18]:0); ?>" required> %
						</td>
						<td class="conts" style="width:325px">
							<textarea rows="10" style="width:300px" name="comment2[<?php echo $code; ?>]" required><?php echo $val[19]; ?></textarea>
						</td>
						<td class="conts" style="width:325px">
							<textarea rows="10" style="width:300px" name="comment2_m[<?php echo $code; ?>]"><?php echo $val[20]; ?></textarea>
						</td>
						<td class="conts" style="width:325px">
							<textarea rows="10" style="width:300px" name="comment3[<?php echo $code; ?>]" required><?php echo $val[21]; ?></textarea>
						</td>
						<td class="conts" style="width:160px">
							<input type="text" name="main_img[<?php echo $code; ?>]" class="input_text" value="<?php echo $val[22]; ?>" required>
						</td>
						<td class="conts" style="width:160px">
							<input type="text" name="prolist_img[<?php echo $code; ?>]" class="input_text" value="<?php echo $val[23]; ?>" required>
						</td>
						<td class="conts" style="width:160px">
							<input type="text" name="prolist_img2[<?php echo $code; ?>]" class="input_text" value="<?php echo $val[24]; ?>" required>
						</td>
						<td class="conts" style="width:80px">
							<label><input type="radio" name="p_view[<?php echo $code; ?>]" value="Y"<?php echo ($val[25] == 'Y' || !$val[25]?' checked':''); ?>> 노출</label><br>
							<label><input type="radio" name="p_view[<?php echo $code; ?>]" value="N"<?php echo ($val[25] == 'N'?' checked':''); ?>> 비노출</label>
						</td>
						<td class="conts" style="width:65px">
							<input type="text" name="pro_idx[<?php echo $code; ?>]" class="input_text" size="4" value="<?php echo $val[26]; ?>">
						</td>
						<td class="conts" style="width:90px">
							<label><input type="radio" name="bestview[<?php echo $code; ?>]" value="Y"<?php echo ($val[27] == 'Y' || !$val[27]?' checked':''); ?>> 추천</label><br>
							<label><input type="radio" name="bestview[<?php echo $code; ?>]" value="N"<?php echo ($val[27] == 'N'?' checked':''); ?>> 추천안함</label>
						</td>
						<td class="conts" style="width:160px">
							<input type="text" name="coupon_title[<?php echo $code; ?>]" class="input_text" value="<?php echo $val[28]; ?>">
						</td>
						<td class="conts" style="width:70px">
							<input type="text" name="coupon_price[<?php echo $code; ?>]" class="input_text" size="3" value="<?php echo $val[29]; ?>"> %
						</td>
						<td class="conts" style="width:160px">
							<input type="text" name="option1_title[<?php echo $code; ?>]" class="input_text" value="<?php echo $val[30]; ?>">
						</td>
						<td class="conts" style="width:160px">
							<input type="text" name="option2_title[<?php echo $code; ?>]" class="input_text" value="<?php echo $val[31]; ?>">
						</td>
						<td class="conts" style="width:160px">
							<input type="text" name="option3_title[<?php echo $code; ?>]" class="input_text" value="<?php echo $val[32]; ?>">
						</td>
						<td class="conts" style="width:100px">
							<input type="hidden" name="guestDisabled" value="1">
							<label><input type="radio" name="ipDistinct[<?php echo $code; ?>]" value="Y"<?php echo ($val[33] == 'Y' || !$val[33]?' checked':''); ?>> 허용</label><br>
							<label><input type="radio" name="ipDistinct[<?php echo $code; ?>]" value="N"<?php echo ($val[33] == 'N'?' checked':''); ?>> 비허용</label>
						</td>
						<td class="conts" style="width:70px">
							<input type="text" name="point[<?php echo $code; ?>]" class="input_text" size="3" value="<?php echo $val[34]; ?>"> %
						</td>
						<td class="conts" style="width:70px">
							<input type="text" name="stock[<?php echo $code; ?>]" class="input_text" size="5" value="<?php echo $val[35]; ?>">
						</td>
						<td class="conts" style="width:70px">
							<input type="text" name="buy_limit[<?php echo $code; ?>]" class="input_text" size="5" value="<?php echo $val[36]; ?>">
						</td>
						<td class="conts" style="width:70px">
							<input type="text" name="saleCnt[<?php echo $code; ?>]" class="input_text" size="5" value="<?php echo $val[37]; ?>">
						</td>
						<td class="conts" style="width:70px">
							<label><input type="radio" name="relation_auto[<?php echo $code; ?>]" value="N"<?php echo ($val[38] == '수동' || !$val[38]?' checked':''); ?>> 수동</label><br>
							<label><input type="radio" name="relation_auto[<?php echo $code; ?>]" value="Y"<?php echo ($val[38] == '자동'?' checked':''); ?>> 자동</label>
						</td>
						<td class="conts" style="width:190px">
							<input type="text" name="p_relation[<?php echo $code; ?>]" class="input_text" value="<?php echo $val[39]; ?>">
						</td>
						<td class="conts" style="width:325px">
							<textarea rows="10" style="width:300px" name="short_comment[<?php echo $code; ?>]"><?php echo $val[40]; ?></textarea>
						</td>
						<td class="conts" style="width:325px">
							<textarea rows="10" style="width:300px" name="comment_proinfo[<?php echo $code; ?>]"><?php echo $val[41]; ?></textarea>
						</td>
						<td class="conts" style="width:325px">
							<textarea rows="10" style="width:300px" name="comment_useinfo[<?php echo $code; ?>]"><?php echo $val[42]; ?></textarea>
						</td>
						<td class="conts" style="width:160px">
							<input type="text" name="com_juso[<?php echo $code; ?>]" class="input_text" value="<?php echo $val[43]; ?>">
						</td>
						<td class="conts" style="width:105px">
							<input type="text" name="expire[<?php echo $code; ?>]" class="input_text" size="11" value="<?php echo $expire; ?>">
						</td>
						<td class="conts" style="width:60px">
							<div class="btn_line_up_center"><span class="shop_btn_pack"><input type="button" value="삭제" class="input_small gray" onclick="$(this).closest('tr').remove();"></span></div>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<!--} 엑셀내용 -->

		<!-- 버튼 {-->
		<div class="top_btn_area">
			<div class="btn_line_up_center">
				<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_large" title="등록처리" value="등록처리"></span>
				<span class="shop_btn_pack"><span class="blank_3"></span></span>
				<span class="shop_btn_pack"><a href="_product.list.php" class="large gray" title="취소" >취소</a></span>
			</div>
		</div>
		<!--} 버튼 -->
	</div>
</form>


<link rel="stylesheet" href="/include/js/jquery/jqueryui/jquery-ui.min.css" type="text/css">
<script language="JavaScript" src="_product.js"></script>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
function category_select_upload(_idx, uniqid, val) {

    $.ajax({
        url: "../include/categorysearch.pro.php",
		cache: false,
		dataType: "json",
		type: "POST",
        data: "pass_parent03_no_required=<?=$pass_cate03_no_required?>&pass_parent01=" + $("[name=pass_cate01_"+uniqid+"]").val() + "&pass_parent02=" + $("[name=pass_cate02_"+uniqid+"]").val()+"&pass_idx=" + _idx,
        success: function(data){
            if(_idx == 2) {

				$("select[name=pass_cate03_"+uniqid+"]").find("option").remove().end().append('<option value="">-선택-</option>');
				var option_str = '';
				for (var i = 0; i < data.length; i++) {
					option_str += '<option value="' + data[i].optionValue + '" '+(data[i].optionValue == val?' selected':'')+'>' + data[i].optionDisplay + '</option>';
				}
				$("select[name=pass_cate03_"+uniqid+"]").append(option_str);
			}
			else if(_idx == 1) {

				$("select[name=pass_cate02_"+uniqid+"]").find("option").remove().end().append('<option value="">-선택-</option>');
				var option_str = '';
				for (var i = 0; i < data.length; i++) {
					option_str += '<option value="' + data[i].optionValue + '"  '+(data[i].optionValue == val?' selected':'')+'>' + data[i].optionDisplay + '</option>';
				}
				$("select[name=pass_cate02_"+uniqid+"]").append(option_str);
				$("select[name=pass_cate03_"+uniqid+"]").find("option").remove().end().append('<option value="">-선택-</option>');
			}
        }
	});
}

function autoload_category(uniqid, val1, val2, val3) {

	if(val2) {

		$.ajax({
	        url: "../include/categorysearch.pro.php",
			cache: false,
			dataType: "json",
			type: "POST",
	        data: "pass_parent03_no_required=<?=$pass_cate03_no_required?>&pass_parent01=" + val1 + "&pass_parent02=" + val2 + "&pass_idx=1",
	        success: function(data) {

				$("select[name=pass_cate02_"+uniqid+"]").find("option").remove().end().append('<option value="">-선택-</option>');
				var option_str = '';
				for (var i = 0; i < data.length; i++) {
					option_str += '<option value="' + data[i].optionValue + '"  '+(data[i].optionValue == val2?' selected':'')+'>' + data[i].optionDisplay + '</option>';
				}
				$("select[name=pass_cate02_"+uniqid+"]").append(option_str);
				$("select[name=pass_cate03_"+uniqid+"]").find("option").remove().end().append('<option value="">-선택-</option>');

				if(val3) {

					$.ajax({
						url: "../include/categorysearch.pro.php",
						cache: false,
						dataType: "json",
						type: "POST",
						data: "pass_parent03_no_required=<?=$pass_cate03_no_required?>&pass_parent01=" + val1 + "&pass_parent02=" + val2 + "&pass_idx=2",
						success: function(data) {

							$("select[name=pass_cate03_"+uniqid+"]").find("option").remove().end().append('<option value="">-선택-</option>');
							var option_str = '';
							for (var i = 0; i < data.length; i++) {
								option_str += '<option value="' + data[i].optionValue + '" '+(data[i].optionValue == val3?' selected':'')+'>' + data[i].optionDisplay + '</option>';
							}
							$("select[name=pass_cate03_"+uniqid+"]").append(option_str);
						}
					});
				}


	        } // success 끝
		}); // ajax 끝
	}

}

$(function() {

    $("input:text[name^=sale_date]").datepicker({changeMonth: true, changeYear: true });
    $("input:text[name^=sale_date]").datepicker( "option", "dateFormat", "yy-mm-dd" );
    $("input:text[name^=sale_date]").datepicker( "option",$.datepicker.regional["ko"] );

    $("input:text[name^=sale_enddate]").datepicker({changeMonth: true, changeYear: true });
    $("input:text[name^=sale_enddate]").datepicker( "option", "dateFormat", "yy-mm-dd" );
    $("input:text[name^=sale_enddate]").datepicker( "option",$.datepicker.regional["ko"] );
});
</script>
<?PHP include_once("inc.footer.php"); ?>