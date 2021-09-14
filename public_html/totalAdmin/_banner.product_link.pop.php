<?PHP

	$app_mode = "popup";
	include_once("inc.header.php");

		if ( !$_COOKIE["auth_adminid"] ) {
		error_loc("/");
	}

	// 넘겨저온 변수
	//		code (필수)
	//		relation_procode (선택) - 입력예: 상품코드1/상품코드2/상품코드3 (상품코드의 구분을 / 로 하시기 바랍니다.)

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


?>




<form name="relationForm" method="get" action="<?=$PHP_SELF?>">
<input type="hidden" name="formname" value="<?=$formname?>">
<input type="hidden" name="relation_procode" value="<?=$relation_procode?>">
<input type="hidden" name="code" value="<?=$code?>">
<input type="hidden" name="mode" value="search">
				<!-- 검색영역 -->
				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<tbody> 
							<tr>
								<td class="conts" colspan="5">
									1차분류 : <?=_InputSelect( "pass_parent01" , array_keys($arr_cate_1) , $pass_parent01 , " id=\"pass_parent01\" onchange=\"category_select(1);\" " , array_values($arr_cate_1) , "-1차분류-")?>
									2차분류 : <?=_InputSelect( "pass_parent02" , array_keys($arr_cate_2) , $pass_parent02 , " id=\"pass_parent02\" onchange=\"category_select(2);\" " , array_values($arr_cate_2) , "-2차분류-")?>
									3차분류 : <?=_InputSelect( "cateCode" , array_keys($arr_cate_3) , $cateCode , " id=\"pass_parent03\" " , array_values($arr_cate_3) , "-3차분류-")?>
								</td>
							</tr>
						</tbody> 
					</table>					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>?code=<?=$code?>&relation_procode=<?=$relation_procode?>" class="medium gray" title="목록" >검색풀기</a></span>
							<?}?>							
						</div>
					</div>
				</div>
				<!-- // 검색영역 -->
</form>



				<!-- 리스트영역 -->
				<div class="content_section_inner">
					<table class="list_TB" summary="리스트기본">
						<colgroup>
								<col width="60px"/><col width="60px"/><col width="120px"/><col width="*"/><col width="150px"/><col width="150px"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset"></th>
								<th scope="col" class="colorset">이미지</th>
								<th scope="col" class="colorset">상품이름</th>
								<th scope="col" class="colorset">상품코드</th>								
								<th scope="col" class="colorset">판매가격</th>
							</tr>
						</thead> 
						<tbody> 
<?PHP

	if( !$cateCode && !$pass_parent02 && !$pass_parent01 ) {
		if($relation_procode) {
			$no_content = NULL; if($relation_procode) { $s_query_add = " where p.code = '".$relation_procode."' "; }
		} else { $no_content = "<tr><td colspan=6 height='80'>해당분류를 선택하시기 바랍니다.</td></tr>"; }
	} else { $s_query_add = ''; }
	if($no_content) {
		echo $no_content;
	} else {

		$s_query = " where 1 "; 
		if($code) { $s_query .= " and p.code != '" . $code ."' "; }

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
		if($s_query_add) { $s_query = $s_query_add; }
		$que = " 
			select 
				p.* 
			from odtProduct as p
			" . $s_query . "
			ORDER BY p.inputDate desc 
		";
		$res = _MQ_assoc($que);
		$TotalCount = sizeof($res) ;
		foreach($res as $k=>$v) {
			$_num = $TotalCount - $k ;
			echo "
								<tr class='selectToggle' style='cursor:pointer;'>
									<td>".${_num}."</td>
									<td><input id='isRelation' type='radio' name='ProCode' value='" . $v[code] . "' " . ($v[code] == $relation_procode ? "checked" : "") . "></td>
									<td style='text-align:center;'><img src='". replace_image('/upfiles/product/'.($v[prolist_img] ? $v[prolist_img] : $v[main_img])) ."' style='width:100px;'></td>
									<td style='text-align:left ; padding-left:5px;'><B>". $v[name] ."</B></td>
									<td>" . $v[code] . "</td>
									<td>". number_format($v[price]) ."원</td>
								</tr>
			";
		}
	}
?>
					</tbody> 
				</table>
			</div>

			<!-- 버튼영역 -->
			<div class="top_btn_area">
				<div class="btn_line_up_center">
					<span class="shop_btn_pack btn_input_blue"><input type="button" class="input_medium" title="선택적용" value="선택적용" onclick="putValue();"></span>
					<span class="shop_btn_pack"><span class="blank_3"></span></span>
					<span class="shop_btn_pack btn_input_blue"><input type="button" class="input_medium" title="창닫기" value="창닫기" onclick="window.close();"></span>
				</div>
			</div>
			<br><br><br><br>








<SCRIPT LANGUAGE="JavaScript">
	function selectAll() {
        if( $("input[name=allcheck]").is(":checked") == false ) { 
            $("input[name='ProCode[]']").attr("checked","");
        }
        else {
            $("input[name='ProCode[]']").attr("checked","checked");
        }
	}
    function putValue() {
        var app_str = "";
        $("input[name='ProCode']:checked").each(function(){
            app_str = $(this).val();
        });
        opener.document.frm._link.value = "/?pn=product.view&pcode="+app_str;
        close();
    }
	$('.selectToggle').on('click',function(){
		$(this).find('td input:radio').prop('checked', true);
	});
</SCRIPT>
<script language="JavaScript" src="_product.js"></script>