<?
/* ---- 2차 카테고리 ---- */
include dirname(__FILE__)."/inc.product.menu.php";

$display_area	= "product_list_area";													// 노출시킬 class 명
$cuid			= $_GET['cuid'];														// 카테고리
$listmaxcount	= "N";																	// 페이지당 노출갯수
$hit_num_use	= "Y";																	// 인기순위 아이콘 노출여부(1위~3위)
$list_type		= $category_total_info['depth1_mobile_list_display'];					// 목록 유형
$pagenate_use	= "N";																	// 페이징 사용여부
$event_type		= $category_total_info['depth1_display'] == "지역" ? "my_local" : NULL;	// 이벤트 요소
$order_field	= "pro_idx";															// 정렬 필드명
$order_sort		= "asc";																// 정렬 방식
$thema			= $_GET['thema'];														// 테마 이름

$main_list_title = $category_total_info['depth1_display'] == "지역" && $_GET['sub_cuid'] ? $_GET['sub_cuid'] : $category_total_info['depth2_catename'];
?>
<div class="sub_list">

	<!-- 상단타이틀 및 정렬선택 -->
	<div class="list_top">
		<div class="sub_tit"><?=$main_list_title?></div>
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
		if( field == 'sale_date' ) { field = 'inputDate'; sort = 'desc';}
		product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>',this_type,'<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>',field,sort,'<?=$search_keyword?>');
	});
	$('.btn_type').on('click',function(){
		this_type = $(this).data('type');
		$('.btn_type').removeClass('type_hit'); $(this).addClass('type_hit');
		product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>',this_type,'<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','<?=$event_type?>',field,sort,'<?=$search_keyword?>');
	});
});
</script>