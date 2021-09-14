<?
	// 카테고리 정보가 없을 시 2015-11-21
	$is_cuid = get_category_info($cuid);
	if(count($is_cuid)<1) error_msg("카테고리 정보가 없습니다.");

	///// 카테고리 추출
	unset($category_2depth_html,$category_3depth_html,$category_4depth_html);
	if($category_total_info['depth1_display'] == "지역") {

		/*---- 2차 카테고리 ----*/
		$sub_assoc = explode(",",$category_total_info['depth1_lineup']); // 묶음탭
		foreach($sub_assoc as $sub_key => $sub_val) {
			$sub_row = _MQ("select catecode,subcate_main from odtCategory where cHidden='no' and subcate_display_choice = '".$sub_val."' and find_in_set('".$category_total_info['depth1_catecode']."',parent_catecode) > 0 order by subcate_main='Y' desc, cateidx asc limit 1");
			$sub_url = "/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&sub_cuid=".$sub_val."&cuid=".$sub_row['catecode']."";	// 첫번째 카테고리는 메인으로 이동시킨다.
			//$category_2depth_html .= "<a href='".($sub_row['catecode'] ? $sub_url : "javascript:alert(\"카테고리 미지정\")")."' class='".($_GET['sub_cuid'] == $sub_val || ($sub_row['subcate_main'] == "Y" && !$_GET['sub_cuid']) ? "btn_hit" : "")." btn_ctg'>".$sub_val."</a>";
			$category_2depth_html .= "<option value='".($sub_row['catecode']?'Y':'N')."' data-url='".$sub_url."' ".($_GET['sub_cuid'] == $sub_val || ($sub_row['subcate_main'] == "Y" && !$_GET['sub_cuid']) ? "selected" : "").">".$sub_val."</option>";
		}

		/*---- 3차 카테고리 ----*/
		$category_sub_assoc = _MQ_assoc("select * from odtCategory where subcate_display_choice = '".$_GET['sub_cuid']."' and find_in_set('".$category_total_info['depth1_catecode']."',parent_catecode) > 0 and catedepth = 2 and cHidden='no' order by cateidx asc ");
		foreach($category_sub_assoc as $category_sub_key => $category_sub_row) {
			if($category_sub_row['catecode']) {
				/*$category_3depth_html .= "
					<div class='set'>
						<div class='first'><a href='/?pn=product.list&sub_cuid=".$_GET['sub_cuid']."&cuid=".$category_sub_row['catecode']."' class='".($category_sub_row['catecode'] == $category_total_info['depth2_catecode'] ? "btn_hit" : "")." btn'>".$category_sub_row['catename']."</a></div>
						<div class='second'><ul>
					";*/
				$category_3depth_html .= "<option value='Y' data-url='/?pn=product.list&sub_cuid=".$_GET['sub_cuid']."&cuid=".$category_sub_row['catecode']."' ".($category_sub_row['catecode'] == $category_total_info['depth2_catecode'] ? "selected" : "").">".$category_sub_row['catename']."</option>";
				if($category_sub_row['catecode'] == $category_total_info['depth2_catecode'] && $pn <> 'product.main'){ // 2020-05-07 SSJ :: 카테고리 메인일경우 2차까지만 노출
					$category_3depth_assoc = _MQ_assoc("select * from odtCategory where find_in_set('".$category_sub_row['catecode']."',parent_catecode) and catedepth = 3 and cHidden='no' order by cateidx asc ");
					foreach($category_3depth_assoc as $category_3depth_key => $category_3depth_row) {
						/*$category_3depth_html .= "<li><a href='/?pn=product.list&sub_cuid=".$_GET['sub_cuid']."&cuid=".$category_3depth_row['catecode']."' class='".($category_3depth_row['catecode'] == $_GET['cuid'] ? "btn_hit" : "")." btn_sub'>".$category_3depth_row['catename']."<em class='eng'>".number_format($category_3depth_row['c_pro_cnt'])."</em></a><span class='line'></span></li>";*/
						$category_4depth_html .= "<option value='Y' data-url='/?pn=product.list&sub_cuid=".$_GET['sub_cuid']."&cuid=".$category_3depth_row['catecode']."' ".($category_3depth_row['catecode'] == $_GET['cuid'] ? "selected" : "").">".$category_3depth_row['catename']."</option>";
					}
				}
				/*$category_3depth_html .= "</ul></div></div>";*/
			}
		}
		/*if($category_3depth_html) $category_3depth_html = "<span class='list_line'></span><div class='local_ctg'>".$category_3depth_html."</div>";*/
		/*---- // 3차 카테고리 ----*/

	} else if($category_total_info['depth1_display'] == "기획전") {

		$depth2_assoc = _MQ_assoc("select catename,catecode,cateimg from odtCategory where find_in_set(".$category_total_info['depth1_catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by cateidx asc");
		foreach($depth2_assoc as $depth2_key => $depth2_row) {
			$sub_url = "/?pn=product.promotion&cuid=".$depth2_row['catecode'];
			$category_2depth_html .= "<option value='Y' data-url='".$sub_url."' ".($category_total_info['depth2_catecode'] == $depth2_row['catecode'] ? "selected" : "").">".$depth2_row['catename']."</option>";
		}

	} else {

		$depth2_assoc = _MQ_assoc("select catename,catecode,cateimg,subcate_main from odtCategory where find_in_set(".$category_total_info['depth1_catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by cateidx asc");
		foreach($depth2_assoc as $depth2_key => $depth2_row) {
			$sub_url = "/?pn=product.".($depth2_row['subcate_main'] == "Y" ? "main" : "list")."&cuid=".$depth2_row['catecode'];	// 첫번째 카테고리는 메인으로 이동시킨다.
			if($depth2_row['cateimg']) {
				$category_2depth_html .= "<option value='Y' data-url='".$sub_url."' ".($category_total_info['depth2_catecode'] == $depth2_row['catecode'] || ($depth2_row['subcate_main'] == "Y" && !$_GET['cuid']) ? "selected" : "").">".$depth2_row['catename']."</option>";
			} else {
				$category_2depth_html .= "<option value='Y' data-url='".$sub_url."' ".($category_total_info['depth2_catecode'] == $depth2_row['catecode'] || ($depth2_row['subcate_main'] == "Y" && !$_GET['cuid']) ? "selected" : "").">".$depth2_row['catename']."</option>";
			}
		}

	}

	///// 테마 추출
	unset($theme_html);
	if($category_total_info['depth1_display'] == "지역") {
        // 2020-05-07 SSJ :: 카테고리 메인일경우 2차까지만 노출
        if($pn <> 'product.main'){
            $theme_html = "<a href='/?pn=".$_GET['pn']."&sub_cuid=".$_GET['sub_cuid']."&cuid=".$_GET['cuid']."' class='ctg ".(!$_GET['thema']?'active':'')."''>전체보기</a>";
            $thema_arr = get_category_thema($cuid);
            foreach($thema_arr as $thema_key => $thema_val) {
                $theme_html .= "<a href='/?pn=".$_GET['pn']."&sub_cuid=".$_GET['sub_cuid']."&cuid=".$_GET['cuid']."&thema=".$thema_val."' class='ctg ".($_GET['thema'] == $thema_val && $_GET['thema'] ? "active" : "")."'>".$thema_val."</a>";
            }
        }
	} else {
		$category_3depth_html = "<option value='Y' data-url='/?pn=".$_GET['pn']."&cuid=".$category_total_info['depth2_catecode']."' ".($category_total_info['depth2_catecode'] == $_GET['cuid']?'selected':'').">전체보기</option>";
		$depth3_arr = _MQ_assoc("select * from odtCategory where catedepth=3 and find_in_set('".$category_total_info['depth2_catecode']."',parent_catecode) and cHidden='no' order by cateidx asc");
		foreach($depth3_arr as $depth3_key => $depth3_row) {
			$category_3depth_html .= "<option value='Y' data-url='/?pn=".$_GET['pn']."&cuid=".$depth3_row['catecode']."' ".($_GET['cuid'] == $depth3_row['catecode'] ? "selected" : "").">".$depth3_row['catename']."</option>";
		}
	}
?>
<div class="page_top <?=$pn=='product.list'?'sub_list_top':''?>">

	<!-- 지역의 경우 클래스값 추가 ctg_type_local -->
	<div class="sub_ctg <?=($category_total_info['depth1_display'] == "지역" && $pn <> 'product.main')?'ctg_type_local':''?>"><!-- 2020-05-07 SSJ :: 카테고리 메인일경우 2차까지만 노출 -->
		<ul>
			<li>
				<div class="select_box">
					<span class="ic_arrow"></span>
					<select name="category_2depth" class="category_toggle">
						<?=$category_2depth_html?>
					</select>
				</div>
			</li>
			<? if($category_total_info['depth1_display'] != "기획전") { ?>
				<? if($category_3depth_html) { ?>
				<li>
					<div class="select_box">
						<span class="ic_arrow"></span>
						<select name="category_2depth" class="category_toggle">
							<?=$category_3depth_html?>
						</select>
					</div>
				</li>
				<? } ?>
				<? if($category_4depth_html) { ?>
				<li>
					<div class="select_box">
						<span class="ic_arrow"></span>
						<select name="category_2depth" class="category_toggle">
							<?=$category_4depth_html?>
						</select>
					</div>
				</li>
				<? } ?>
			<? } ?>
		</ul>
	</div>

	<? if($theme_html) { ?>
	<!-- (지역의경우) 테마 및 최하위 카테고리 스와이핑  -->
	<div class="ctg_last">
		<div id="product_swiper_menu">
			<div class="product_swiper_wrap">
			<?=$theme_html?>
			</div>
		</div>
	</div>
	<? } ?>

</div>
<script>
$(document).ready(function(){
	$('.category_toggle').on('change',function(){
		var url = $(this).find('option:selected').data('url');
		if( $(this).val() == 'N' ) { alert('카테고리 미지정 오류. 관리자에게 문의하세요.'); return false; }
		else { location.href = url; }
	});
});
var p_scrollWidth = 0, p_scrollIndex = 1, p_wrapper = document.getElementById('product_swiper_menu'), p_myScroll = '';
$(function() {
	$.each($('#product_swiper_menu .ctg'), function(k, v){ p_scrollWidth += $('#product_swiper_menu .ctg').eq(k).outerWidth() + 6; });
	if( $(document).width() < p_scrollWidth ) {
		$('#product_swiper_menu .product_swiper_wrap').css('width', parseInt(p_scrollWidth)+36);
		p_myScroll = new iScroll(p_wrapper,{ hScrollbar: false, vScrollbar: false });
		if(p_scrollIndex > 0) { p_myScroll.scrollToElement(document.querySelector('#product_swiper_menu .ctg.active'), 0); }
	}
});
$(window).on('resize',function(){
	if( $(document).width() < p_scrollWidth ) {
		$('#product_swiper_menu .product_swiper_wrap').css('width', parseInt(p_scrollWidth)+36);
		p_myScroll = new iScroll(p_wrapper,{ hScrollbar: false, vScrollbar: false });
		if(p_scrollIndex > 0) { p_myScroll.scrollToElement(document.querySelector('#product_swiper_menu .ctg.active'), 0); }
	} else { if( p_myScroll ) { setTimeout(function () { p_myScroll.refresh(); }, 0); p_myScroll.destroy(); p_myScroll = null; } }
});
</script>