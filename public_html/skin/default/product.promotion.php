<? 
	include dirname(__FILE__)."/inc.product.menu.php"; 
	$category_info = get_category_info($cuid);
?>
<!-- 상품리스트 -->
<div class="sub_list promotion">
	<!-- 3차 선택 -->
	<div class="sub_category">

		<!-- 테마/카테고리선택 -->
		<div class="depth3_tab">
			<div class="layout_fix">
				<div class="inner">
					<span class="bt_line"></span>
					<?=$category_2depth_html?>
				</div>
			</div>
		</div>
	</div>

	<!-- 기획전 배너 -->
	<?
		$banner_info = info_banner($category_total_info['depth2_catecode'].",visual",1,"data");
		if( count($banner_info)>0 ) {
	?>
	<div class="promotion_bn">
		<div class="layout_fix">
			<a href="<?=$banner_info[0]['b_link']?>" target="<?=$banner_info[0]['b_target']?>" title="<?=$banner_info[0]['b_title']?>" class="bn_area"><img src="<?=IMG_DIR_BANNER.$banner_info[0]['b_img']?>" alt="<?=$banner_info[0]['b_title']?>"></a>
		</div>
	</div>
	<? } ?>

	<?
	$display_area	= "product_list_area";	// 노출시킬 class 명
	$cuid			= $_GET['cuid'];			// 카테고리
	$listmaxcount	= "N";					// 페이지당 노출갯수
	$hit_num_use	= "Y";					// 인기순위 아이콘 노출여부(1위~3위)
	$list_type		= "type1";				// 목록 유형
	$pagenate_use	= "N";					// 페이징 사용여부
	$event_type		= "";					// 이벤트 요소
	$order_field	= "pro_idx";			// 정렬 필드명
	$order_sort		= "asc";				// 정렬 방식
	$thema			= '';					// 테마 이름
	?>

	<!-- 서브 탭 -->
	<div class="sub_tab">
		<!-- 해당 탭일 경우 btn_hit 추가 -->
		<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>','pro_idx','asc');return false;" class="product_list_tab btn_tab btn_hit">
			<span class="dot d_left"></span><span class="dot d_right"></span>추천 베스트
		</a>
		<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>','sale_date','desc');return false;" class="product_list_tab btn_tab">
			<span class="dot d_left"></span><span class="dot d_right"></span>신규상품
		</a>
		<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>','sale_enddate','asc');return false;" class="product_list_tab btn_tab">
			<span class="dot d_left"></span><span class="dot d_right"></span>마감임박
		</a>
		<!-- 개발 가능한지 문의 후 작업 -->
		<!-- <a href="" class="btn_tab">
			<span class="dot d_left"></span>
			<span class="dot d_right"></span>
			바로사용
		</a> -->
	</div>

	<div class="item_list_area">
		<div class="layout_fix <?=$display_area?>">
			<? include dirname(__FILE__)."/ajax.product.list.php"; ?>
		</div>
	</div>
	<script>
	$(document).ready(function(){
		$(".product_list_tab").on('click',function() {
			$(".product_list_tab").removeClass("btn_hit");
			$(this).addClass("btn_hit");
		});
	});
	</script>
</div>