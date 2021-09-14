<?
include_once(dirname(__FILE__)."/../../include/inc.php");

$view_cnt = 4; // 몇개씩 볼것인가.
$late_list = get_latest_list();

?>
			
<div class="top_title">TODAY VIEW</div>
<div class="view_box">
	<div class="view_inner">
<?if(sizeof($late_list)<1) {?>
	<div class="view_none">
		<span class="lineup">
			<span class="img"><img src="/pages/images/none_view.png" alt="최근 본 상품이 없습니다" /></span>
			<span class="txt">최근 본 상품이<br/>없습니다.</span>
		</span>
	</div>
<?} else { 

	$total_page = (sizeof($late_list) == $view_cnt)?1:intval(sizeof($late_list) % $view_cnt)==0?intval(sizeof($late_list) / $view_cnt):intval(sizeof($late_list) / $view_cnt)+1;

	foreach($late_list as $k => $v) { 

		// 페이지정보
		$view_page = (intval($idx / $view_cnt)+1);
		$idx++;
		if($view_page>1) { // 2페이지 부터는 숨기고 다음/이전 버튼을 노출한다.
			$hide = " style='display:none' ";
		}

		// 상품정보
		$late_name = cutstr($v['name'],28,"...");
		$late_price = number_format($v['price']);
		$late_img = IMG_DIR_PRODUCT.app_thumbnail( "최근본상품" , $v );
		$late_url = rewrite_url($v['code']);
		if(!file_exists("..".IMG_DIR_PRODUCT.$late_img)&&!$v['prolist_img']){ $late_img = ""; }
		else { $late_img = replace_image($late_img); }
	?>
	<div class="box page<?=$view_page?>" <?=$hide?> >
		<!-- 상품링크 -->
		<a href="<?=$late_url?>" class="item_link" title="<?=$v['name']?>"><img src="/pages/images/blank.gif" alt="" /></a>			
		<a href="#none" onclick="late_delete(<?=$v['pl_uid']?>);return false;" class="btn_close"><img src="/pages/images/ic_del.gif" alt="삭제" /></a>
		<div class="over">
			<div class="info">
				<div class="item_name"><?=$late_name?></div>
				<div class="price"><?=$late_price?></div>
			</div>
		</div>
		<div class="thumb"><? if($late_img) { ?><img src="<?=product_thumb_img( $v , '최근본상품' ,  'data')?>" alt="<?=$v['name']?>" /><? } ?></div>
	</div>
	<? } // end foreach	?>
<? } ?>
	</div>
	<!-- 페이지 네이트 -->
	<div class="btn_nate">
		<span class="lineup">
			<a href="#none" onclick="late_page_move('prev');return false;" class="nate btn_prev">
				<img src="/pages/images/btn_prev.gif" alt="이전" class="off" />
				<img src="/pages/images/btn_prev_hit.gif" alt="이전" class="hit" />
			</a>
			<div class="number"><strong class="page_now">1</strong>/<?=$total_page?$total_page:1?></div>
			<a href="#none" onclick="late_page_move('next');return false;" class="nate btn_next">
				<img src="/pages/images/btn_next.gif" alt="다음" class="off" />
				<img src="/pages/images/btn_next_hit.gif" alt="다음" class="hit" />
			</a>
		</span>
	</div>
</div>


<script>
$(document).ready(function() {
	$("#latest_total_count").html(<?=sizeof($late_list)?>);
})
</script>