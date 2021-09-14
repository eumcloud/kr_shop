<?php

	// 페이지 표시
	$app_current_link = "/totalAdmin/_product.list.php";
	include dirname(__FILE__)."/wrap.header.php";


	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $k => $v) { if( is_array($v) ){foreach($v as $sk => $sv) { $_PVS .= "&" . $k . "[".$sk."]=$sv"; }}else {$_PVS .= "&$k=$v"; }}
	$_PVSC = enc('e' , $_PVS);




	$arr_cate_1 = $arr_cate_2 = $arr_cate_3 = array();

	// - 1차  카테고리 ---
	$res = _MQ_assoc("select catecode,catename from odtCategory where cHidden='no' and catedepth='1' order by cateidx asc ");
	foreach( $res as $k=>$v ){$arr_cate_1[$v['catecode']] = $v['catename'];}
	// - 1차  카테고리 ---

	// - 2차  카테고리 ---
	if($pass_parent01){
		$res = _MQ_assoc("select catecode,catename from odtCategory where cHidden='no' and catedepth='2' and find_in_set('" . $pass_parent01 . "' , parent_catecode) > 0 order by cateidx asc");
		foreach( $res as $k=>$v ){$arr_cate_2[$v['catecode']] = $v['catename'];}
	}
	// - 2차  카테고리 ---

	// - 3차  카테고리 ---
	if($pass_parent02){
		$res = _MQ_assoc("select catecode,catename from odtCategory where cHidden='no' and catedepth='3' and find_in_set('" . $pass_parent02 . "' , parent_catecode) > 0 order by cateidx asc");
		foreach( $res as $k=>$v ){$arr_cate_3[$v['catecode']] = $v['catename'];}
	}
	// - 3차  카테고리 ---

	// - 공급업체 ---
	$arr_customer = arr_company();

	// 아이콘 정보 배열로 추출
	$product_icon = get_product_icon_info_qry("product_name_small_icon");


	// 상품명 체크
	if($search_type == "open") {$pass_name_tmp = $pass_name ? $pass_name : $pass_name_tmp;}
	else {$pass_name = $pass_name_tmp ? $pass_name_tmp : $pass_name;}


	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_customerCode !="" ) { $s_query .= " AND p.customerCode='".$pass_customerCode."' "; }
	if( $pass_p_view !="" ) { $s_query .= " AND p.p_view='".$pass_p_view."' "; }
	if( $pass_bestview !="" ) { $s_query .= " AND p.bestview='".$pass_bestview."' "; }
	if( $pass_code !="" ) { $s_query .= " AND p.code like '%".stripslashes($pass_code)."%' "; }
	if( $pass_name !="" ) { $s_query .= " AND p.name like '%".stripslashes($pass_name)."%' "; }
	if( $sale_enddate == "N" ) { $s_query .= " AND p.sale_enddate >= CURDATE() AND p.sale_date <= CURDATE() "; }
	if( $sale_enddate == "Y" ) { $s_query .= " AND p.sale_enddate < CURDATE() "; }
	// --- 검색 슬래시 풀기 ---
	$pass_code = stripslashes($pass_code);
	$pass_name = stripslashes($pass_name);
	$pass_name_tmp = stripslashes($pass_name_tmp);

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

	$listmaxcount = 5 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$que = " select count(*) as cnt from odtProduct as p $s_query ";
	$res = _MQ($que);
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$que = " select p.* from odtProduct as p " . $s_query . " ORDER BY p.inputDate desc  limit $count , $listmaxcount  ";
	$res = _MQ_assoc($que);

?>




<form name="searchfrm" method="post" action="<?=$PHP_SELF?>">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="search_type" value="close">
	<!-- 상단에 들어가는 검색등 공간 검색닫기를 누르면  if_closed 처음설정을 닫혀있도록 해도 좋을듯.. -->
	<div class="page_top_area if_closed">

		<div class="title_box"><span class="txt">SEARCH</span>
			<div class="before_search">
				<button type="submit" class="btn_search"></button>
				<input type="search" name="pass_name_tmp" value="<?=$pass_name_tmp?>" class="input_design" placeholder="상품명 검색">
			</div>
			<a href="#none" onclick="view_search(); return false;" class="btn_ctrl btn_close" title="검색닫기">상세검색닫기<span class="shape"></span></a>
			<a href="#none" onclick="view_search(); return false;" class="btn_ctrl btn_open" title="검색열기">상세검색열기<span class="shape"></span></a>
		</div>

		<!-- ●●●●● 검색폼 -->
		<div class="cm_search_form">
			<ul>
				<li class="">
					<span class="opt">노출여부</span>
					<div class="value"><?=_InputRadio_totaladmin( "pass_p_view" , array("N" , "Y") , $pass_p_view , "" , array("숨김" , "노출") , "")?></div>
				</li>
				<li class="ess">
					<span class="opt">공급업체</span>
					<div class="value">
						<div class="select">
							<span class="shape"></span>
							<?=_InputSelect("pass_customerCode", array_keys($arr_customer), $pass_customerCode, "", array_values($arr_customer), "-공급업체-")?>
						</div>
					</div>
				</li>
				<li class="">
					<span class="opt">상품명</span>
					<div class="value"><input type="text" name="pass_name" value="<?=$pass_name?>" class="input_design" placeholder="상품명을 입력하세요." /></div>
				</li>
				<li class="">
					<span class="opt">상품코드</span>
					<div class="value"><input type="text" name="pass_code" value="<?=$pass_code?>" class="input_design" placeholder="상품코드를 입력하세요." /></div>
				</li>
				<li class="">
					<span class="opt">판매여부</span>
					<div class="value"><?=_InputRadio_totaladmin( "sale_enddate" , array("N" , "Y") , $sale_enddate , "" , array("판매중인상품" , "종료상품") , "")?></div>
				</li>

				<li class="ess">
					<span class="opt">상품분류</span>
					<div class="value">
						<div class="select">
							<span class="shape"></span>
							<?=_InputSelect("pass_parent01", array_keys($arr_cate_1), $pass_parent01, " id=\"pass_parent01\" onchange=\"category_select(1);\" ", array_values($arr_cate_1), "-1차분류-")?>
						</div>
						<div class="select">
							<span class="shape"></span>
							<?=_InputSelect("pass_parent02", array_keys($arr_cate_2), $pass_parent02, " id=\"pass_parent02\" onchange=\"category_select(2);\" ", array_values($arr_cate_2), "-2차분류-")?>
						</div>
						<div class="select">
							<span class="shape"></span>
							<?=_InputSelect("cateCode", array_keys($arr_cate_3), $cateCode, " id=\"pass_parent03\" ", array_values($arr_cate_3), "-3차분류-")?>
						</div>
					</div>
				</li>
			</ul>

			<!-- ●●●●● 도움말 공간 dt는 주황색 dd는 파란색 -->
			<div class="guide_box">
				<dl>
					<dt>주문정보를 삭제할 경우 상품 재고량과 회원이 사용한 적립금이 환원되지 않습니다.</dt>
					<dd>상품의 재고량과 회원이 사용한 적립금이 환원되기를 바란다면 반드시 주문취소로 처리 하셨다가 삭제하시기 바랍니다.</dd>
					<dd>회원주문인 경우 주문번호가 볼드체(굵은글씨)로 표시 됩니다.</dd>
					<dd>주문내역에 대한 엑셀파일은 검색조건에 맞는 내역만 저장됩니다.</dd>
				</dl>
			</div>
			<!-- 도움말 공간 -->


			<!-- ●●●●● 가운데정렬버튼 -->
			<div class="cm_bottom_button">
				<ul>
					<li><span class="button_pack"><input type="submit" class="btn_md_blue" value="검색하기"></span></li>
					<?if($mode == "search") :?><li><span class="button_pack"><a href="_product.list.php" class="btn_md_black">전체목록</a></span></li><?endif;?>
				</ul>
			</div>
			<!-- / 가운데정렬버튼 -->
		</div>
	</div>
	<!-- / 상단에 들어가는 검색등 공간 -->
</form>







<form name="frm" method="post" action="_product.pro.php">
<input type="hidden" name="_mode" value="">
<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">

<?
	if(sizeof($res) == 0 ) :
		echo "<div class='cm_no_conts'><div class='no_icon'></div><div class='gtxt'>등록된 내용이 없습니다.</div></div>"; 
	else :
?>
	<!-- 리스트 제어영역 -->
	<div class="top_ctrl_area">
		<label class="allcheck" title="모두선택"><input type="checkbox" name="allchk" /></label>
		<span class="ctrl_button">
			<span class="button_pack"><a href="#none" onclick="selectSortModify();" class="btn_sm_white">선택순위수정</a></span>
		</span>
	</div>
	<!-- / 리스트 제어영역 -->
<? endif;?>


	<!--  ●●●●● 내용들어가는 공간 -->
	<div class="container">

		<!-- ●●●●● 데이터리스트 -->
		<div class="data_list">
<?php

	foreach($res as $k=>$v) {

		$_link_out = "<span class='button_pack'><a href='/?pn=product.view&pcode=" . $v['code'] . "' target='_blank' class='btn_sm_white'>미리보기</a></span>";
		$_mod = "<span class='button_pack'><a href='_product.form.php?_mode=modify&code=" . $v['code'] . "&_PVSC=" . $_PVSC . "' class='btn_sm_blue'>수정</a></span>";
		$_del = "<span class='button_pack'><a href='#none' onclick='del(\"_product.pro.php?_mode=delete&code=" . $v['code'] . "&_PVSC=" . $_PVSC . "\");' class='btn_sm_black'>삭제</a></span>";

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
			$app_cate2 .= $sv['ct1_name'] ." &gt; ". $sv['ct2_name'] ." &gt; ". $sv['ct3_name'] ."<br/>";
		}
		// -- 카테고리 정보 ---

		echo "
			<dl>
				<dd>
					<div class='first_box'>
						<label class='check'><input type='checkbox' name='chk_pcode[]' value='".$v['code']."' class=class_pcode /></label>
						<span class='number'>no.". $_num ."</span>
						<span class='view_rank'>
							<span class='txt'>". ($v['p_view'] == "Y" ? "노출" : "<FONT COLOR='red'>숨김</FONT>") ."</span>
							<span class='input_box'><input type='tel' name='chk_idx[".$v['code']."]' value='".$v['pro_idx']."' class='input_design'  /></span>
						</span>
					</div>
					<!-- 상품정보 -->
					<div class='item_info'>
						<span class='thumb'><img src='". replace_image('/upfiles/product/'.app_thumbnail( "장바구니" , $v )) ."' alt='' /></span>
						<div class='name'>[상품명] ". $v['name'] ."</div>
						<div class='code'>[상품코드] " . $v['code'] . "</div>
						<div class='ctg'>[카테고리] ". $app_cate2 ."</div>
						<div class='price'>
							<span class='before'>정상가 : <span class='value'>". number_format($v['price_org']) ."원</span></span>
							<span class='after'>판매가 : <span class='value'>". number_format($v['price']) ."원</span></span>
						</div>
						<div class='due'>
							".( $v['sale_type'] == "A" ? "<span class='open'>상시판매</span>" : "<span class='open'>판매일 : <span class='value'>". date("y.m.d" , strtotime($v['sale_date'])) ."</span></span><span class='close'>종료일 : <span class='value'>". date("y.m.d" , strtotime($v['sale_enddate'])) ."</span></span>" )."
						</div>
					</div>
				</dd>
				<dt>
					<div class='btn_box'>
						<ul>
							<li>" . $_mod . "</li>
							<li>" . $_del . "</li>
							<li>" . $_link_out . "</li>
						</ul>
					</div>
				</dt>
			</dl>
		";
	}
?>
		</div>
	</div>
	<!-- / 내용들어가는 공간 -->
</form>



	<?=pagelisting_mobile_totaladmin($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>



<?php

	include dirname(__FILE__)."/wrap.footer.php";

?>



<script language="JavaScript" src="./js/_product.js"></script>
<SCRIPT>
	// 선택순위수정
	function selectSortModify() {
		if($('.class_pcode').is(":checked")){
			$("form[name=frm]").attr("action" , "_product.pro.php");
			$("input[name=_mode]").val('mass_sort');
			document.frm.submit();
		}
		else {alert('1개 이상 선택하시기 바랍니다.');}
	}
	// - 전체선택 / 해제
	$(document).ready(function() {
		$("input[name=allchk]").click(function (){
			if($(this).is(':checked')){$('.class_pcode').attr('checked',true);}
			else {$('.class_pcode').attr('checked',false);}
		});
	});
</SCRIPT>