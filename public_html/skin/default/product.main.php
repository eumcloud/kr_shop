<?

	/* ---- 2차/3차 카테고리 추출 ---- */
	include dirname(__FILE__)."/inc.product.menu.php";

	// 메인화면 타이틀 네임
	$main_list_title = $category_total_info['depth1_display'] == "지역" ? $_GET['sub_cuid'] : $category_total_info['depth2_catename'];
?>	


<!-- 서브 카테고리/비주얼 -->
<div class="sub_top_area">
	<div class="layout_fix">
		<div class="center">
			<!-- 2차 카테고리 -->
			<div class="category">
				<div class="inner">
					<?=$category_2depth_html?>					
				</div>
			</div>

			<!-- 서브비주얼 -->
			<div class="sub_visual">
				<div id="product_main_slider" style="height:370px;overflow:hidden;z-index:1;">
			<?
				$banner_info = info_banner($category_total_info['depth1_catecode'].",big",4,"data"); $banner_class = ''; $banner_cutstr = 22;
				if( count($banner_info)<4 ) { $banner_class = "triple_title"; $banner_cutstr = 28; } 
				if( count($banner_info)<3 ) { $banner_class = "wide_title"; $banner_cutstr = 30; } 
				if( count($banner_info)<2 ) { $banner_class = "single_title"; $banner_cutstr = 120; }
				
				if( count($banner_info)>0 ) {
					foreach($banner_info as $k=>$v) {
			?>
				<!-- 배너사이즈 810*370 -->
				<a href="<?=$v['b_link']?>" target="<?=$v['b_target']?>" class="banner"><img src="<?=IMG_DIR_BANNER.$v['b_img']?>" alt="<?=$v['b_title']?>" /></a>
			<? } ?>
				</div>
				<div class="bn_title <?=$banner_class?>">
					<div id="product_main_pager">
					<!-- 해당 배너일 경우 bn_hit 추가 -->
					<!-- 관리자가 텍스트 등록 -->
					<? foreach($banner_info as $k=>$v) { ?>
					<a href="#none" onclick="return false;" data-slide-index="<?=$k?>" class="bn_link <?=$k==0?'active':''?>" title="<?=$v['b_title']?>" style="z-index:2;">
						<span class="arrow"></span>
						<!-- 최대 2줄까지 노출 -->
						<span class="txt"><?=cutstr($v['b_title'],$banner_cutstr)?></span>
					</a>
					<? } ?>
					</div>
				</div>
				<script>
				var product_main_slider = '';
				$(window).on('load',function(){
					product_main_slider = $('#product_main_slider').bxSlider({
						<? if( count($banner_info)>1 ) { ?>
						auto: true, autoHover: true, speed: 300, mode: 'fade',
						slideSelector: '', easing: 'easeInOutCubic', useCSS: false,
						slideMargin: 0, slideWidth: 810, controls: false,
						pager: true, pagerCustom: '#product_main_pager',
						onSlideAfter: function(){ product_main_slider.startAuto(); }
						<? } else { ?>
						auto: false, useCSS: false, slideWidth: 810, pager: true, pagerCustom: '#product_main_pager', controls: false
						<? } ?>
					});
				});
				</script>
			<? } ?>
			</div>
		</div>
	</div>
</div>



<!-- 상품리스트 -->
<?
$display_area	= "product_list_area";								// 노출시킬 class 명
$cuid			= $_GET['cuid'];									// 카테고리
$listmaxcount	= "N";												// 페이지당 노출갯수
$hit_num_use	= "Y";												// 인기순위 아이콘 노출여부(1위~3위)
$list_type		= $category_total_info['depth1_pc_list_display'];	// 목록 유형
$pagenate_use	= "N";												// 페이징 사용여부
$event_type		= "";												// 이벤트 요소
$order_field	= "pro_idx";										// 정렬 필드명
$order_sort		= "asc";											// 정렬 방식
$thema			= "";												// 테마 이름
?>
<div class="sub_list">
	<!-- 서브 탭 -->
	<div class="sub_tab">
		<!-- 해당 탭일 경우 btn_hit 추가 -->
		<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>','pro_idx','asc');return false;" class="product_list_tab btn_tab btn_hit">
			<span class="dot d_left"></span><span class="dot d_right"></span>추천 베스트
		</a>
		<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>','inputDate','desc');return false;" class="product_list_tab btn_tab">
			<span class="dot d_left"></span><span class="dot d_right"></span>신규상품
		</a>
		<? if($row_setup[view_social_commerce]=='Y') { ?>
		<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>','sale_enddate','asc');return false;" class="product_list_tab btn_tab">
			<span class="dot d_left"></span><span class="dot d_right"></span>매진임박
		</a>
		<? } ?>
		<!-- 개발 가능한지 문의 후 작업 -->
		<!-- <a href="" class="btn_tab">
			<span class="dot d_left"></span>
			<span class="dot d_right"></span>
			바로사용
		</a> -->
	</div>
	<script>
	$(document).ready(function(){
		$(".product_list_tab").click(function() {
			$(".product_list_tab").removeClass("btn_hit");
			$(this).addClass("btn_hit");
		});
	});
	</script>

	<div class="item_list_area">
		<div class="layout_fix <?=$display_area?>">
			<? include dirname(__FILE__)."/ajax.product.list.php"; ?>
		</div>
	</div>
</div>
