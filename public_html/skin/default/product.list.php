<?
	/* ---- 2차/3차 카테고리 ---- */
	include dirname(__FILE__)."/inc.product.menu.php";

	// 카테고리 정보
	$category_info = get_category_info($cuid);
?>

<!-- 서브 카테고리/비주얼 -->
<div class="sub_top_area">
	<div class="layout_fix">

		<? if($category_total_info['depth1_display'] == "지역") { ?>
		<!-- 지역일 경우 -->
		<div class="sub_center">
			<!-- 2차 카테고리 -->
			<div class="list_category">
				<div class="inner">
					<!-- 해당 카테고리일 경우 btn_hit 추가 -->
					<?=$category_2depth_html?>
				</div>
			</div>
			<?=$category_3depth_html?>
		</div>
		<? } else { ?>
		<!-- 지역 외 카테고리 -->
		<div class="sub_ctg_choice">
			<div class="location">
				<span class="txt">홈</span>
				<span class="arrow"><img src="/pages/images/arrow_s.gif" alt="" /></span>
				<span class="txt"><?=$category_total_info['depth1_catename']?></span>
				<span class="arrow"><img src="/pages/images/arrow_s.gif" alt="" /></span>
			</div>
			<div class="select_box">
				<span class="arrow"></span>
				<span class="first"><?=$category_total_info['depth2_catename']?></span>
				<div class="over">
					<?=$category_2depth_html?>
				</div>
			</div>
		</div>
		<!-- 지역 외 카테고리는 3차 카레고리를 아래에서 별도로 출력한다 -->
		<? } ?>
	</div>
</div>


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
$thema			= $_GET['thema'];									// 테마 이름
?>	

<div class="sub_list">

	<!-- 3차 선택 -->
	<div class="sub_category">
		<? if($category_total_info['depth1_display'] == "지역") { // 지역 카테고리일 경우 ?>
		<div class="sub_tit local_tit">
			<span class="txt_l"><?=$_GET['sub_cuid']!=''?$_GET['sub_cuid']:$category_total_info['depth2_catename']?></span>
			<span class="txt"><?=$category_total_info['depth2_catename']?></span>
			<span class="txt"><?=$category_total_info['depth3_catename']?></span>
		</div>
		<? } else { // 일반 카테고리일 경우 ?>
		<div class="sub_tit">
			<span class="txt_l"><?=$category_total_info['depth2_catename']?></span>
		</div>
		<? } ?>

		<!-- 테마/카테고리선택 -->
		<div class="depth3_tab">
			<div class="layout_fix">
				<div class="inner">
					<span class="bt_line"></span>
					<ul>
					<? if($category_total_info['depth1_display'] == "지역") {  $cnt_thema = 1;  ?>
						<li class="<?=!$_GET['thema'] ? "hit" : "";?>"><span class="line"></span><a href="/?pn=<?=$_GET['pn']?>&sub_cuid=<?=$_GET['sub_cuid']?>&cuid=<?=$_GET['cuid']?>" class="btn">전체보기</a></li>
						<?
						$thema_arr = get_category_thema($cuid);
						foreach($thema_arr as $thema_key => $thema_val) {
							$cnt_thema ++;
							echo "<li class='".($_GET['thema'] == $thema_val && $_GET['thema'] ? "hit" : "")."'><span class='line'></span><a href='/?pn=".$_GET['pn']."&sub_cuid=".$_GET['sub_cuid']."&cuid=".$_GET['cuid']."&thema=".$thema_val."' class='btn'>".$thema_val."</a></li>";
							//if($thema_key>0&&($thema_key+1)%($thema_key<5?5:6)==0) { echo "</ul><ul>"; }
							if($cnt_thema>2&&($cnt_thema % 6)==0) { echo "</ul><ul>"; }
						}
						?>
					<? } else {  $cnt_cate = 1;  ?>
						<li class="<?=$category_total_info['depth2_catecode'] == $_GET['cuid'] ? "hit" : ""?>"><span class="line"></span><a href="/?pn=<?=$_GET['pn']?>&cuid=<?=$category_total_info['depth2_catecode']?>" class="btn">전체보기</a></li>
						<?
						$depth3_arr = _MQ_assoc("select * from odtCategory where catedepth=3 and find_in_set('".$category_total_info['depth2_catecode']."',parent_catecode) and cHidden='no' order by cateidx asc");
						foreach($depth3_arr as $depth3_key => $depth3_row) {
							$cnt_cate ++;
							echo "<li class='".($_GET['cuid'] == $depth3_row['catecode'] ? "hit" : "")."'><span class='line'></span><a href='/?pn=".$_GET['pn']."&cuid=".$depth3_row['catecode']."' class='btn'>".$depth3_row['catename']."</a></li>";
							//if($depth3_key>0&&($depth3_key+1)%($depth3_key<5?5:6)==0) { echo "</ul><ul>"; }
							if($cnt_cate>2&&($cnt_cate % 6)==0) { echo "</ul><ul>"; }
						}
						?>
					<? } ?>
					</ul>
				</div>
			</div>
		</div>
	</div>


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

	<div class="item_list_area">
		<div class="layout_fix <?=$display_area?>">
		<? include dirname(__FILE__)."/ajax.product.list.php"; ?>
		</div>
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