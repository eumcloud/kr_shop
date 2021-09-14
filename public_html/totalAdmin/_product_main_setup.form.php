<?PHP

	// 페이지 표시
	$app_current_link = "/totalAdmin/_product_main_setup.list.php?_type=" . ($_REQUEST[_type] ? $_REQUEST[_type] : "hot");

	include_once("inc.header.php");

	$arr_cate_1 = $arr_cate_2 = $arr_cate_3 = array();

	// - 1차  카테고리 ---
    $res = _MQ_assoc("select catecode,catename from odtCategory where cHidden='no' and catedepth='1' order by cateidx asc ");
	foreach( $res as $k=>$v ){
		$arr_cate_1[$v[catecode]] = $v[catename];
	}
	// - 1차  카테고리 ---

	// - 2차  카테고리 ---
	if($pass_parent01){
		$res = _MQ_assoc("select catecode,catename from odtCategory where cHidden='no' and catedepth='2' and find_in_set('" . $pass_parent01 . "' , parent_catecode) > 0 order by cateidx asc");
		foreach( $res as $k=>$v ){
			$arr_cate_2[$v[catecode]] = $v[catename];
		}
	}
	// - 2차  카테고리 ---

	// - 3차  카테고리 ---
	if($pass_parent02){
		$res = _MQ_assoc("select catecode,catename from odtCategory where cHidden='no' and catedepth='3' and find_in_set('" . $pass_parent02 . "' , parent_catecode) > 0 order by cateidx asc");
		foreach( $res as $k=>$v ){
			$arr_cate_3[$v[catecode]] = $v[catename];
		}
	}
	// - 3차  카테고리 ---


	// - 공급업체 ---
	$arr_customer = arr_company();


?>



				<!-- 검색영역 -->
<form name=searchfrm method=post action='<?=$PHP_SELF?>'>
<input type=hidden name="mode" value="search">
<input type=hidden name="_type" value="<?=$_type?>">
				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><col width="200px"/><col width="120px"/><col width="*"/>
						</colgroup>
						<tbody>
							<tr>
								<td class="article" colspan="6" height=30><B>검색 후 추가하고자하는 목록을 추가하시기 바랍니다.</B></td>
							</tr>
							<tr>
								<td class="article">상품분류</td>
								<td class="conts" colspan="5">

									1차분류 : <?=_InputSelect( "pass_parent01" , array_keys($arr_cate_1) , $pass_parent01 , " id=\"pass_parent01\" onchange=\"category_select(1);\" " , array_values($arr_cate_1) , "-1차분류-")?>

									2차분류 : <?=_InputSelect( "pass_parent02" , array_keys($arr_cate_2) , $pass_parent02 , " id=\"pass_parent02\" onchange=\"category_select(2);\" " , array_values($arr_cate_2) , "-2차분류-")?>

									3차분류 : <?=_InputSelect( "cateCode" , array_keys($arr_cate_3) , $cateCode , " id=\"pass_parent03\" " , array_values($arr_cate_3) , "-3차분류-")?>

								</td>
							</tr>
							<tr>
								<td class="article">공급업체</td>
								<td class="conts" colspan="3"><?=_InputSelect( "pass_customerCode" , array_keys($arr_customer) , $pass_customerCode , "" , array_values($arr_customer) , "-공급업체-")?></td>
								<td class="article">상품코드</td>
								<td class="conts"><input type=text name="pass_code" class=input_text value="<?=$pass_code?>"></td>
							</tr>
							<tr>
								<td class="article">상품명</td>
								<td class="conts"><input type=text name="pass_name" class=input_text value="<?=$pass_name?>"></td>
								<td class="article">추천상품</td>
								<td class="conts" ><?=_InputSelect( "pass_bestview" , array("N" , "Y"), $pass_bestview, "" , array("미적용" , "적용") , "-선택-")?></td>
								<td class="article">판매기간</td>
								<td class="conts" ><?=_InputSelect( "sale_enddate" , array("N" , "Y"), $sale_enddate, "" , array("판매중인상품" , "종료상품") , "-선택-")?></td>
							</tr>
						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_product_main_setup.form.php?_type=<?=$_type?>" class="medium gray" title="검색풀기" >검색풀기</a></span>
							<?}?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_product_main_setup.list.php?_type=<?=$_type?>" class="medium gray" title="설정목록보기" >설정목록보기</a></span>
						</div>
					</div>
				</div>
</form>
				<!-- // 검색영역 -->








<form name=frm method=post action="_product_main_setup.pro.php">
<input type=hidden name="_mode" value="add">
<input type=hidden name="_type" value="<?=$_type?>">

				<!-- 리스트영역 -->
				<div class="content_section_inner">


					<!-- 리스트 제어버튼영역 //-->
					<div class="ctl_btn_area">
						<span class="shop_btn_pack"><a href="#none" onclick="select_send();" class="small red" title="선택상품추가" >선택상품추가</a></span>
					</div>
					<!-- // 리스트 제어버튼영역 -->

					<table class="list_TB" summary="리스트기본">
						<thead>
							<tr>
								<th scope="col" class="colorset"><input type="checkbox" name="allchk"></th>
								<th scope="col" class="colorset">노출순위</th>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">이미지</th>
								<th scope="col" class="colorset">상품정보</th>
								<th scope="col" class="colorset">정상가<br>판매가</th>
								<th scope="col" class="colorset">판매일<br>종료일</th>								
							</tr>
						</thead> 
						<tbody> 
<?PHP

	if ($mode == "search") {

		// 검색 체크
		$s_query = " where pms.pms_uid is null ";
		if( $pass_customerCode !="" ) { $s_query .= " AND p.customerCode='".$pass_customerCode."' "; }
		if( $pass_bestview !="" ) { $s_query .= " AND p.bestview='".$pass_bestview."' "; }
		if( $pass_code !="" ) { $s_query .= " AND p.code like '%".$pass_code."%' "; }
		if( $pass_name !="" ) { $s_query .= " AND p.name like '%".$pass_name."%' "; }
		if( $sale_enddate == "N" ) { $s_query .= "  AND ( ( p.sale_enddate >= CURDATE() AND p.sale_date <= CURDATE() AND sale_type = 'T' ) OR p.sale_type = 'A' ) "; }
		if( $sale_enddate == "Y" ) { $s_query .= " AND p.sale_enddate < CURDATE() AND sale_type = 'T' "; }


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


		$que = " 
			select 
				p.* 
			from odtProduct as p
			left join odtProductMainSetup as pms on (pms.pms_pcode=p.code and pms.pms_type='". $_type ."' )
			" . $s_query . "
			ORDER BY p.inputDate desc 
		";
		$res = _MQ_assoc($que);
		$app_total = sizeof($res); // 전체검색개수
		if($app_total == 0 ) echo "<tr><td colspan=7 height='40'>등록된 상품이 없습니다.</td></tr>";
		foreach($res as $k=>$v) {

			$_num = $app_total - $k ;

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
					pct.pct_pcode='". $v[code] ."'
					order by pct.pct_uid asc
			";
			$sr = _MQ_assoc($sque);
			foreach( $sr as $sk=>$sv ){
				$app_cate2 .= $sv[ct1_name] ." &gt; ". $sv[ct2_name] ." &gt; ". $sv[ct3_name] ."<br>";
			}
			// -- 카테고리 정보 ---

			echo "
				<tr>
					<td><input type=checkbox name='chk_pcode[]' value='".$v[code]."' class=class_pcode></td>
					<td><input type=text name='chk_idx[".$v[code]."]' value='". ($k+1) ."' class=input_text style='width:30px;' ></td>
					<td>".${_num}."</td>
					<td style='text-align:left ; padding-left:5px;'><img src='". replace_image('/upfiles/product/'.($v['prolist_img'] ? $v['prolist_img'] : $v['main_img'] )) ."' style='width:150px;'></td>
					<td style='text-align:left ; padding-left:5px;'>
						[카테고리]<br>". $app_cate2 ."<br>
						[상품코드] <B>" . $v[code] . "</B><br><br>
						[상품명] <B>". $v[name] ."</B>
					</td>
					<td><strike>". number_format($v[price_org]) ."원</strike><br>". number_format($v[price]) ."원</td>
					<td>".( $v['sale_type'] == "A" ? "<strong>상시판매</strong>" : date("y.m.d" , strtotime($v['sale_date'])) ."<br>". date("y.m.d" , strtotime($v['sale_enddate'])) )."</td>
				</tr>
			";
		}
	}
	else {
		echo "
			<tr>
				<td style='padding:30px; text-align:center;' colspan=7>검색 결과가 없습니다.</td>
			</tr>
		";
	}
?>
						</tbody> 
					</table>

			</div>


<?PHP
	include_once("inc.footer.php");
?>





<script>
	// - 선택적용 ---
	 function select_send() {
		 if($('.class_pcode').is(":checked")){
			 document.frm.submit();
		 }
		 else {
			 alert('1건 이상 선택하시기 바랍니다.');
		 }
	 }
	// - 선택적용 ---


	// - 전체선택 / 해제
	$(document).ready(function() {
		$("input[name=allchk]").click(function (){
			if($(this).is(':checked')){
				$('.class_pcode').attr('checked',true);
			}
			else {
				$('.class_pcode').attr('checked',false);
			}
		});
	});
</script>
<script language="JavaScript" src="_product.js"></script>