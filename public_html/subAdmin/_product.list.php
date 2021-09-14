<?PHP
// 페이지 표시
$app_current_link = "/totalAdmin/_product.list.php";

include_once("inc.header.php");

$arr_cate_1 = $arr_cate_2 = $arr_cate_3 = array();

// - 1차  카테고리 ---
$res = _MQ_assoc("select catecode,catename from odtCategory where cHidden='no' and catedepth='1' order by cateidx asc ");
foreach( $res as $k=>$v ){
	$arr_cate_1[$v['catecode']] = $v['catename'];
}
// - 1차  카테고리 ---

// - 2차  카테고리 ---
if($pass_parent01){
	$res = _MQ_assoc("select catecode,catename from odtCategory where cHidden='no' and catedepth='2' and find_in_set('" . $pass_parent01 . "' , parent_catecode) > 0 order by cateidx asc");
	foreach( $res as $k=>$v ){
		$arr_cate_2[$v['catecode']] = $v['catename'];
	}
}
// - 2차  카테고리 ---

// - 3차  카테고리 ---
if($pass_parent02){
	$res = _MQ_assoc("select catecode,catename from odtCategory where cHidden='no' and catedepth='3' and find_in_set('" . $pass_parent02 . "' , parent_catecode) > 0 order by cateidx asc");
	foreach( $res as $k=>$v ){
		$arr_cate_3[$v['catecode']] = $v['catename'];
	}
}
// - 3차  카테고리 ---


// - 공급업체 ---
$arr_customer = arr_company();

// 아이콘 정보 배열로 추출
$product_icon = get_product_icon_info_qry("product_name_small_icon");

$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $k => $v) { if( is_array($v) ){foreach($v as $sk => $sv) { $_PVS .= "&" . $k . "[".$sk."]=$sv"; }}else {$_PVS .= "&$k=$v"; }}
$_PVSC = enc('e' , $_PVS);



// 검색 체크
$s_query = " where p.customerCode='". $com[id] ."' ";
if( $pass_p_view !="" ) { $s_query .= " AND p.p_view='".$pass_p_view."' "; }
if( $pass_code !="" ) { $s_query .= " AND p.code like '%".$pass_code."%' "; }
if( $pass_name !="" ) { $s_query .= " AND p.name like '%".$pass_name."%' "; }
if( $pass_delivery !="" ) { $s_query .= " AND p.setup_delivery = '".$pass_delivery."' "; }
if( $sale_enddate == "N" ) { $s_query .= " AND p.sale_enddate >= CURDATE() AND p.sale_date <= CURDATE() "; }
if( $sale_enddate == "Y" ) { $s_query .= " AND p.sale_enddate < CURDATE() "; }
if( sizeof($pass_icon) > 0 ) {
	foreach($pass_icon as $k0 => $v0) $s_query_icon[] = " find_in_set('".$v0."',p_icon) ";
	$s_query .= " and (". implode(" or ",$s_query_icon) .") ";
}

// - 카테고리 검색 ---
if($cateCode) { $s_query .= " and (select count(*) from odtProductCategory as pct where pct.pct_pcode=p.code and pct.pct_cuid='".$cateCode."') > 0 ";  }
else if( $pass_parent01 || $pass_parent02 ) {
	$appCateCode = ($pass_parent01 ? $pass_parent01 : $appCateCode);
	$appCateCode = ($pass_parent02 ? $pass_parent02 : $appCateCode); // 2차 카테고리가 있으면 1차카테고리 덮기
	$s_query .= "
		and (
			select count(*)
			from odtProductCategory as pct
			left join odtCategory as c on (c.catecode = pct.pct_cuid)
			where
				pct.pct_pcode=p.code and
				find_in_set('" . $appCateCode . "' , c.parent_catecode)>0
		) > 0
	";
}
// - 카테고리 검색 ---


$listmaxcount = 20 ;
if( !$listpg ) {$listpg = 1 ;}
$count = $listpg * $listmaxcount - $listmaxcount;

$que = " select count(*) as cnt from odtProduct as p $s_query ";
$res = _MQ($que);
$TotalCount = $res[cnt];
$Page = ceil($TotalCount / $listmaxcount);

$que = " select p.* from odtProduct as p " . $s_query . " ORDER BY p.inputDate desc  limit $count , $listmaxcount  ";
$res = _MQ_assoc($que);
?>

<!-- 검색영역 -->
<form name="searchfrm" method="post" action="<?=$PHP_SELF?>">
	<input type="hidden" name="mode" value="search">
	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="120px"/><col width="200px"/><col width="120px"/><col width="200px"/><col width="120px"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">상품명</td>
					<td class="conts"><input type="text" name="pass_name" class="input_text" value="<?=$pass_name?>"></td>
					<td class="article">상품코드</td>
					<td class="conts"><input type="text" name="pass_code" class="input_text" value="<?=$pass_code?>"></td>
					<td class="article">판매여부</td>
					<td class="conts" ><?=_InputSelect("sale_enddate", array("N" , "Y"), $sale_enddate, "", array("판매중인상품" , "종료상품"), "-선택-")?></td>
				</tr>
				<tr>
					<td class="article">상품타입</td>
					<td class="conts">
						<?=_InputSelect("pass_delivery", array("N" , "Y"), $pass_delivery, "", array("쿠폰상품" , "배송상품"), "-선택-")?>
					</td>
					<td class="article">노출여부</td>
					<td class="conts"colspan="3"><?=_InputSelect("pass_p_view", array("N" , "Y"), $pass_p_view, "", array("숨김" , "노출"), "-선택-")?></td>
				</tr>
				<tr>
					<td class="article">상품분류</td>
					<td class="conts" colspan="5">
						1차분류 : <?=_InputSelect("pass_parent01", array_keys($arr_cate_1), $pass_parent01, " id=\"pass_parent01\" onchange=\"category_select(1);\" ", array_values($arr_cate_1), "-1차분류-")?>
						2차분류 : <?=_InputSelect("pass_parent02", array_keys($arr_cate_2), $pass_parent02, " id=\"pass_parent02\" onchange=\"category_select(2);\" ", array_values($arr_cate_2), "-2차분류-")?>
						3차분류 : <?=_InputSelect("cateCode", array_keys($arr_cate_3), $cateCode, " id=\"pass_parent03\" ", array_values($arr_cate_3), "-3차분류-")?>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- 버튼영역 -->
		<div class="top_btn_area">
			<div class="btn_line_up_center">
				<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
				<?php if ($mode == 'search') { ?>
				<span class="shop_btn_pack"><span class="blank_3"></span></span>
				<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="전체목록" >전체목록</a></span>
				<?php } ?>
				<span class="shop_btn_pack"><span class="blank_3"></span></span>
				<span class="shop_btn_pack"><a href="_product.form.php?_mode=add" class="medium red" title="상품등록" >상품등록</a></span>
				<span class="shop_btn_pack"><span class="blank_3"></span></span>
				<span class="shop_btn_pack"><a href="#none" onclick="$('.excel_upload_field').toggle();" class="medium white" title="일괄업로드">일괄업로드</a></span>
			</div>
		</div>
	</div>
</form>
<!-- // 검색영역 -->


<!-- 상품일괄업로드 # LDD014 {-->
<form action="_product.upload.php" method="post" enctype="multipart/form-data">
	<div class="form_box_area excel_upload_field" style="display:none;">
		<table class="form_TB">
			<colgroup>
				<col width="120">
				<col width="*">
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
						<?=_DescStr("상품 <b>등록</b>시 상품분류(카테고리)는 1개만 지정 가능하며 이미 등록되어있지 않은 분류(카테고리)는 제외 됩니다.")?>
						<?=_DescStr("상품 <b>수정</b>시 상품분류(카테고리) 설정은 무시됩니다.")?>
						<?=_DescStr("상품 분류 추가/변경은 <b>업로드 수정/확인</b>에서 가능합니다. ")?>
						<?=_DescStr("<b>업로드 파일</b>은 <b>최대 ".ini_get("upload_max_filesize")."까지 업로드 가능</b> 합니다.")?>
						<?=_DescStr("<b>업로드 용량</b>에 따라 <b>다소시간이 걸릴 수 있습니다.</b>")?>
						<?=_DescStr("엑셀내용중 <b>엔터</b>는 <b>생략</b> 하시고 입력 바랍니다.")?>
						<?=_DescStr("<b>상품 사용 정보</b>, <b>업체 이용 정보</b>, <b>상품상세설명</b>, <b>상품 상세설명 (모바일)</b>, <b>주문확인서 주의사항</b>의 내용은 엔터를 제외 하고 <b>HTML로 입력</b> 바랍니다.  ")?>
						<?=_DescStr("상품이미지가 <b>외부이미지</b>일 경우 <b>http://</b>부터 시작하도록 입력 바랍니다.")?>
						<?=_DescStr("상품이미지가 <b>내부이미지</b>일 경우 <b>./upfiles/product/ 폴더에 사전 업로드</b>를 하시고 엑셀에는 <b>파일명과 확장자만 입력</b> 바랍니다.")?>
						<?=_DescStr("엑셀내용중 금액 또는 수수료의 <b>%</b>, <b>콤마(,)</b>, <b>원</b> 등을 <b>기호를 생략</b> 하세요.")?>
						<?=_DescStr("일괄업로드는 \"<b>파일업로드</b>\" - \"<b>업로드 수정/확인</b>\" - \"<b>등록처리</b>\" 단계를 거쳐 처리됩니다.")?>
						<?=_DescStr("<b>엑셀97~2003 버전 파일만 업로드가 가능합니다. 엑셀 2007이상 버전은(xlsx) 다른 이름저장을 통해 97~2003버전으로 저장하여 등록하세요.</b>", "orange")?>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="top_btn_area">
			<div class="btn_line_up_center">
				<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="일괄업로드" value="일괄업로드"></span>
				<span class="shop_btn_pack"><span class="blank_3"></span></span>
				<span class="shop_btn_pack"><a href="_product.download.php?_search_que=<?=onedaynet_encode(enc('e',$s_query))?>" class="medium white" title="엑셀다운로드" >엑셀다운로드</a></span>
			</div>
		</div>
	</div>
</form>
<!--} 상품일괄업로드 # LDD014 -->





	<!-- 리스트영역 -->
	<div class="content_section_inner">

		<table class="list_TB" summary="리스트기본">
			<thead>
				<tr>
					<th scope="col" class="colorset">NO</th>
					<th scope="col" class="colorset">이미지</th>
					<th scope="col" class="colorset">상품정보</th>
					<th scope="col" class="colorset">정상가<br>판매가</th>
					<th scope="col" class="colorset">판매일<br>종료일</th>
					<th scope="col" class="colorset">관리</th>
				</tr>
			</thead>
			<tbody>
				<?PHP
				if(sizeof($res) == 0 ) echo "<tr><td colspan=8 height='100'>등록된 상품이 없습니다.</td></tr>";
				foreach($res as $k=>$v) {

					$_link_out = "<span class='shop_btn_pack'><a type=button class='small white' href='/?pn=product.view&pcode=" . $v['code'] . "' target='_blank'>미리보기</a></span>";
					$_mod = "<span class='shop_btn_pack'><input type=button value='수정' class='input_small blue' onclick='location.href=(\"_product.form.php?_mode=modify&code=" . $v['code'] . "&_PVSC=" . $_PVSC . "\");'></span>";
					$_del = "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='del(\"_product.pro.php?_mode=delete&code=" . $v['code'] . "&_PVSC=" . $_PVSC . "\");'></span>";

					$_num = $TotalCount - $count - $k ;

					// -- 카테고리 정보 ---
					$app_cate2 = "";
					$sque = "
						select
							ct3.catename as ct3_name , ct2.catename as ct2_name , ct1.catename as ct1_name
						from odtProductCategory as pct
						left join odtCategory as ct3 on (ct3.catecode = pct.pct_cuid and ct3.catedepth=3)
						left join odtCategory as ct2 on (substring_index(ct3.parent_catecode , ',' ,-1) = ct2.catecode and ct2.catedepth=2)
						left join odtCategory as ct1 on (substring_index(ct3.parent_catecode , ',' ,1) = ct1.catecode and ct1.catedepth=1)
						where
							pct.pct_pcode='". $v['code'] ."'
							order by pct.pct_uid asc
					";
					$sr = _MQ_assoc($sque);
					foreach( $sr as $sk=>$sv ){
						$app_cate2 .= $sv['ct1_name'] ." &gt; ". $sv['ct2_name'] ." &gt; ". $sv['ct3_name'] ."<br>";
					}
					// -- 카테고리 정보 ---

					echo "
						<tr>
							<td>".${_num}."</td>
							<td><img src='". replace_image('/upfiles/product/'.app_thumbnail( "장바구니" , $v )) ."' style='width:100px;'></td>
							<td style='text-align:left ; padding-left:5px;'>
								[카테고리]<br>". $app_cate2 ."<br>
								[상품코드] <B>" . $v['code'] . "</B><br><br>
								[상품명] <B>". $v['name'] ."</B>
							</td>
							<td><strike>". number_format($v['price_org']) ."원</strike><br>". number_format($v['price']) ."원</td>
							<td>".( $v['sale_type'] == "A" ? "<strong>상시판매</strong>" : date("y.m.d" , strtotime($v['sale_date'])) ."<br>". date("y.m.d" , strtotime($v['sale_enddate'])) )."</td>
							<td>
								<div class='btn_line_up_center'>
									". $_mod."
									<span class='shop_btn_pack'><span class='blank_3'></span></span>
									". $_del."
								</div>
								<br><div class='btn_line_up_center'>
									". $_link_out ."
								</div>
							</td>
						</tr>
					";
				}
				?>
			</tbody>
		</table>


		<!-- 페이지네이트 -->
		<div class="list_paginate">
			<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=")?>
		</div>
		<!-- // 페이지네이트 -->

	</div>

<script language="JavaScript" src="_product.js"></script>
<?PHP include_once("inc.footer.php"); ?>