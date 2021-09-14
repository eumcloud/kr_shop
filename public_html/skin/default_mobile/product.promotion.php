<?
/* ---- 2차/3차 카테고리 추출 ---- */
include dirname(__FILE__)."/inc.product.menu.php";
$category_info = get_category_info($cuid);
?>

<? $banner_info = info_banner($category_total_info[depth2_catecode].",visual",12,"data"); if(count($banner_info)>0) { ?>
<div class="sub_visual">
	<div class="wrap_box">
		<!-- 3개 배너 노출, 롤링 -->
		<div class="promotion_bn">
			<span class="lineup">
				<a href="<?=$banner_info[0]['b_link']?>" target="<?=$banner_info[0]['b_target']?>" class="bn"><img src="<?=IMG_DIR_BANNER.$banner_info[0]['b_img']?>" alt=""/></a>
			</span>
		</div>
	</div>
</div>
<? } ?>

<?
$display_area	= "promotion_product_list_area";						// 노출시킬 class 명
$cuid			= $_GET['cuid'];										// 카테고리
$listmaxcount	= "N";													// 페이지당 노출갯수
$hit_num_use	= "Y";													// 인기순위 아이콘 노출여부(1위~3위)
$list_type		= $category_total_info['depth1_mobile_list_display'];	// 목록 유형
$pagenate_use	= "N";													// 페이징 사용여부
$event_type		= "";													// 이벤트 요소
$order_field	= "pro_idx";											// 정렬 필드명
$order_sort		= "asc";												// 정렬 방식
$thema			= "";													// 테마 이름
?>
<div class="sub_list">

	<!-- 상단타이틀 및 정렬선택 -->
	<div class="list_top">
		<div class="sub_tit"><?=$category_total_info['depth2_catename']?></div>
		<div class="list_arrange">
			<? if($list_type!='type3') { ?>
			<!-- 해당 타입일 때 type_hit 추가 -->
			<!-- 상품리스트가 여행/레저형일 때는 btn_type은 display:none -->
			<!-- 썸네일형 -->
			<a href="#none" onclick="return false;" class="btn_type <?=$list_type=='type1'?'type_hit':''?>" data-type="type1">
				<img src="/m/images/ic_thumb_off.gif" alt="" class="off"/>
				<img src="/m/images/ic_thumb_hit.gif" alt="" class="hit"/>
			</a>
			<!-- 리스트형-->
			<a href="#none" onclick="return false;" class="btn_type <?=$list_type=='type2'?'type_hit':''?>" data-type="type2">
				<img src="/m/images/ic_list_off.gif" alt="" class="off"/>
				<img src="/m/images/ic_list_hit.gif" alt="" class="hit"/>
			</a>
			<? } ?>
			<div class="ar_select">
				<span class="ic_arrow"></span>
				<select name="toggleOption">
					<option value="pro_idx" <?=($order_field == "pro_idx" ? " selected " : null)?>>추천 베스트</option>
					<option value="sale_date" <?=($order_field == "sale_date" ? " selected " : null)?>>신규상품</option>
					<option value="sale_enddate" <?=($order_field == "sale_enddate" ? " selected " : null)?>>매진임박</option>
				</select>
			</div>
		</div>
	</div>

	<div class="<?=$display_area?>"><? include dirname(__FILE__)."/ajax.product.list.php"; ?></div>

</div><!-- .sub_list -->

<script>
var field = '<?=$order_field?>', sort = '<?=$order_sort?>', this_type = '<?=$list_type?>';
$(document).ready(function() {
	$('select[name=toggleOption]').on('change',function(){
		field = $(this).val(), sort = 'asc';
		if( field == 'sale_date' ) { sort = 'desc'; }
		product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>',this_type,'<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>',field,sort,'<?=$search_keyword?>','<?=$com_info[id]?>');
	});
	$('.btn_type').on('click',function(){
		this_type = $(this).data('type');
		$('.btn_type').removeClass('type_hit'); $(this).addClass('type_hit');
		product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>',this_type,'<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>',field,sort,'<?=$search_keyword?>','<?=$com_info[id]?>');
	});
});
</script>