<?PHP

	// 페이지 표시
	$app_current_link = "/totalAdmin/_product_main_setup.list.php?_type=" . ($_REQUEST[_type] ? $_REQUEST[_type] : "hot");

	include_once("inc.header.php");

	$_type = $_type ? $_type : "hot";
?>

				<!-- 버튼영역 -->
				<div class="form_box_area">
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="button" class="input_medium" title="메인상품 추가하기" value="메인상품 추가하기" onclick='location.href=("_product_main_setup.form.php?_type=<?=$_type?>");'></span>
						</div>
					</div>
				</div>
				<!-- 버튼영역 -->

<form name=frm method=post action="_product_main_setup.pro.php">
<input type=hidden name=_mode value=''>
<input type=hidden name="_type" value="<?=$_type?>">

				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<!-- 리스트 제어버튼영역 //-->
					<div class="ctl_btn_area">
						<span class="shop_btn_pack"><a href="#none" onclick="select_send('modify');" class="small gray" title="선택상품순위수정" >선택상품순위수정</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="#none" onclick="select_send('delete');" class="small gray" title="선택상품삭제" >선택상품삭제</a></span>
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
	$que = " 
		select 
			pms.* , p.*
		from odtProductMainSetup as pms 
		inner join odtProduct as p on (pms.pms_pcode=p.code)
		where
			pms.pms_type='". $_type ."'
		ORDER BY pms_idx asc, inputDate desc 
	";
	$res = _MQ_assoc($que);
	$app_total = sizeof($res); // 전체검색개수
	if($app_total == 0 ) echo "<tr><td colspan=10 height='40'>등록된 상품이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_mod = "<span class='shop_btn_pack'><input type=button value='수정' class='input_small blue' onclick='location.href=(\"_product.form.php?_mode=modify&code=" . $v[code] . "&_PVSC=" . $_PVSC . "\");'></span>";
		$_del = "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='del(\"_product.pro.php?_mode=delete&code=" . $v[code] . "&_PVSC=" . $_PVSC . "\");'></span>";

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
				<td><input type=text name='chk_idx[".$v[code]."]' value='". $v[pms_idx] ."' class=input_text style='width:30px;' ></td>
				<td>".${_num}."</td>
				<td style='text-align:left ; padding-left:5px;'><img src='". replace_image('/upfiles/product/'.($v['prolist_img'] ? $v['prolist_img'] : $v['main_img'] )) ."' style='width:150px;'></td>
				<td style='text-align:left ; padding-left:5px;'>
					[카테고리]<br>". $app_cate2 ."<br>
					[상품코드] <B>" . $v[code] . "</B><br><br>
					[상품명] <B>". $v[name] ."</B>
				</td>
				<td><strike>". number_format($v[price_org]) ."원</strike><br>". number_format($v[price]) ."원</td>
				<td>". ( $v[sale_type]=='A' ? '상시판매' : date("y.m.d" , strtotime($v[sale_date])) ."<br>". date("y.m.d" , strtotime($v[sale_enddate])) )."</td>
			</tr>
		";
	}
?>
						</tbody> 
					</table>
			</div>
</form>


<script>

	// - 선택적용 ---
	 function select_send(_mode) {
		 if($('.class_pcode').is(":checked")){
			$("input[name=_mode]").val(_mode);
			if(_mode == "delete") {
				if( confirm("정말 삭제하시겠습니까?") ){
					document.frm.submit();
				}
			}
			else {
				document.frm.submit();
			}
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



<?PHP
	include_once("inc.footer.php");
?>
