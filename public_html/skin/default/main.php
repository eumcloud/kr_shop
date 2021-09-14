<!-- 메인비주얼 -->
<? $banner_info = info_banner("site_main_big",6,"data"); if(count($banner_info)>0) { ?>
<div class="main_visual">
	<div class="layout_fix">
		<div class="main_bn">
			<? $banner_flag_info = info_banner("site_main_big_flag",1,"data"); if(count($banner_flag_info)>0) { ?>
			<span class="ic_flag"><img src="<?=IMG_DIR_BANNER.$banner_flag_info[0]['b_img']?>" alt="<?=$banner_flag_info[0]['b_title']?>" /></span>
			<? } ?>
			<!-- 배너사이즈 730*403 -->
			<div style="width:730px;height:403px;overflow:hidden;">
				<div id="main_visual_slider" style="z-index:1;">
					<? foreach($banner_info as $k=>$v) { ?>
					<a href="<?=$v['b_link']?>" target="<?=$v['b_target']?>" title="<?=$v['b_title']?>" class="bn"><img src="<?=IMG_DIR_BANNER.$v['b_img']?>" alt="<?=$v['b_title']?>" /></a>
					<? } ?>
				</div>
			</div>
		</div>
		<div class="thumb_bn">
			<ul id="main_visual_pager">
				<? foreach($banner_info as $k=>$v) { ?>
				<li>
					<a href="#none" onclick="return false;" data-slide-index="<?=$k?>" title="<?=$v['b_title']?>" class="upper <?=$k==0?'active':''?>"><img src="/pages/images/blank.gif" alt="" /></a>
					<!-- 111*89 -->
					<div class="thumb"><img src="<?=IMG_DIR_BANNER.$v['b_img2']?>" alt="<?=$v['b_title']?>" /></div>
					<!-- 1줄 제한, 관리자등록 -->
					<div class="tit"><?=cutstr($v['b_title'],12,'')?></div>
				</li>
				<? } ?>
			</ul>
		</div>
	</div>
</div>
<script>
var main_visual_slider = '';
$(window).on('load',function(){
	main_visual_slider = $('#main_visual_slider').bxSlider({
		auto: true, autoHover: true, speed: 450, mode: 'fade',
		easing: 'easeInOutCubic', useCSS: false,
		pager: true, pagerCustom: '#main_visual_pager', controls: false,
		onSlideAfter: function(){ main_visual_slider.startAuto(); }
	});
});
</script>
<? } ?>



<? $banner_info = info_banner("site_main_md",40,"data"); if(count($banner_info)>0) { ?>
<!-- MD'S PICK -->
<!-- 상품 이미지, 타이틀, 설명 각각 따로 관리자 등록 -->
<div class="main_center_bn">
	<div class="layout_fix">
		<!-- 타이틀 -->
		<div class="box_title"><img src="/pages/images/main_tit01.gif" alt="md's pick" /></div>

		<!-- 롤링버튼, 등록된 배너가 4개 이하일 경우 롤링버튼 안나타남 -->
		<? if( count($banner_info)>4 ) { ?>
		<div class="roll_btn">
			<div id="site_main_md_pager">
			<? foreach($banner_info as $k=>$v) { ?>
			<a href="#none" onclick="return false;" data-slide-index="<?=$k?>" class="btn <?=$k==0?'active':''?>" title="<?=$v['b_title']?>">
				<img src="/pages/images/roll_hit.png" alt="" class="img_hit" />
				<img src="/pages/images/roll_off.png" alt="" class="img_off" />
			</a>
			<? } ?>
			</div>
		</div>
		<? } ?>

		<!-- 배너등록박스 -->
		<div class="banner_box">
			<div id="site_main_md_slider">
			<? foreach($banner_info as $k=>$v) { ?>
			<!-- 배너하나 -->
			<div class="box">
				<a href="<?=$v['b_link']?>" target="<?=$v['b_target']?>" class="alink"><img src="/pages/images/blank.gif" alt="" /></a>
				<div class="title_area">
					<div class="lineup">
						<!-- 타이틀 한줄 제한 -->
						<? if( $v['b_title'] ) { ?>
							<div class="tit"><?=cutstr($v['b_title'],15,'')?></div>
							<span class="under_line"></span>
						<? } ?>
						<!-- 배너설명 글 -->
						<? if( $v['b_content'] ) { ?>
							<div class="txt"><?=nl2br(stripslashes($v['b_content']))?></div>
						<? } ?>
					</div>
				</div>
				<div class="img_box"><img src="<?=IMG_DIR_BANNER.$v['b_img']?>" alt="<?=$v['b_title']?>" /></div>
			</div>
			<? } ?>

			<? if( count($banner_info)<4 ) { for($i=0;$i<4-count($banner_info);$i++) { ?>
			<!-- 배너하나 -->
			<div class="box">
				<a href="#none" onclick="return false;" class="alink"><img src="/pages/images/blank.gif" alt="" /></a>
				<div class="title_area"><div class="lineup"></div></div>
			</div>
			<? }} ?>
			</div>
		</div>
	</div>
</div>
<script>
var site_main_md = '';
$(window).on('load',function(){
	site_main_md = $('#site_main_md_slider').bxSlider({
		<? if( count($banner_info)>4 ) { ?>
		auto: true, autoHover: true, speed: 500,
		slideSelector: '.box', easing: 'easeInOutCubic', useCSS: false,
		slideMargin: 1, slideWidth: 249, responsive: false,
		minSlides: 4, maxSlides: 4, moveSlides: 1,
		pager: true, pagerCustom: '#site_main_md_pager',
		controls: false,
		onSlideAfter: function(){ site_main_md.startAuto(); }
		<? } else { ?>
		auto: false, slideSelector: '.box', slideMargin: 1, slideWidth: 249, responsive: false,
		minSlides: 4, maxSlides: 4, pager: false, controls: false
		<? } ?>
	});
});
</script>
<? } ?>


<!-- 카테고리별 추천상품 -->
<div class="main_ctg_item">
	<div class="layout_fix">
		<div class="box_title"><img src="/pages/images/main_tit02.gif" alt="카테고리 추천상품" /></div>
		
		<div class="top_area">
			<!-- 탭영역 -->
			<div class="tab_area">
				<?
					$top_menu_assoc = _MQ_assoc("select * from odtCategory where catedepth=1 and cHidden='no' and subcate_display != '기획전' order by cateidx asc limit 5");
					foreach($top_menu_assoc as $top_menu_key => $top_menu_row) {
						if($top_menu_row['subcate_display'] == "지역") { // 지역일 경우에만 sub_cuid 추가
							$sub_cuid = reset(explode(",",$top_menu_row['lineup'])); // 지역 sub_cuid (묶음탭)
							$sub_row = _MQ("select catecode,subcate_main  from odtCategory where cHidden='no' and  subcate_display_choice = '".$sub_cuid."' order by subcate_main='Y' desc, cateidx asc limit 1"); // sub 메뉴에서 첫번째로 보여질 카테고리
							$sub_main_url = "/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&sub_cuid=$sub_cuid&cuid=".$sub_row['catecode']; // 링크값
							$sub_main_url = ($sub_row['catecode'] ? $sub_main_url : "javascript:alert('첫 카테고리 연결 미지정')"); // 링크값
						} else {
							$sub_row = _MQ("select catecode,subcate_main from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by subcate_main='Y' desc,  cateidx asc limit 1");
							$sub_main_url = "/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&cuid=".$sub_row['catecode']; // 링크값
						}
						$init_link = $top_menu_key==0 ? $sub_main_url : $init_link;
				?>
				<!--  해당 카테고리 일때 btn_hit 추가 -->
				<a href="#none" onclick="return false;" data-cuid="<?=$top_menu_row['catecode']?>" data-cname="<?=$top_menu_row['catename']?>" data-url="<?=$sub_main_url?>" class="main_ctg_tab btn_tab <?=$top_menu_key==0?'btn_hit':''?>"><?=$top_menu_row['catename']?> <span class="line"></span></a>
				<? } ?>
			</div>

			<!-- 더보기 버튼 -->
			<a href="<?=$init_link?>" class="btn_more_view" id="main_ctg_link">
				<strong class="ctg_name" id="main_ctg_title"><?=$top_menu_assoc[0]['catename']?>상품</strong> 전체보기
			</a>
		</div>

		<!-- 상품박스 -->
		<div class="list_box">
			<div class="item_list" id="main_ctg_list"></div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){
	$('.main_ctg_tab').on('click',function(){
		var cuid = $(this).data('cuid'), cname = $(this).data('cname'), url = $(this).data('url');
		$('.main_ctg_tab').removeClass('btn_hit'); $(this).addClass('btn_hit');
		$('#main_ctg_title').text(cname+'상품'); $('#main_ctg_link').attr('href',url);
		load_main_best_product(cuid);
	});
	load_main_best_product("<?=$top_menu_assoc[0]['catecode']?>");
	function load_main_best_product(cuid) {
		$.ajax({
			data: {cuid:cuid},
			type: 'POST',
			cache: false,
			url: '/pages/ajax.main.best_product.list.php',
			success: function(data) {
				$('#main_ctg_list').html(data);
			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
});
</script>


<?
$cuid			= "";							// 카테고리
$display_area	= "main_product_list_area"; 	// 노출시킬 class 명
$listmaxcount	= "N";							// 페이지당 노출갯수
$list_type		= "type1";						// 목록 유형
$pagenate_use	= "N";							// 페이징 사용여부
$hit_num_use	= "N";							// 인기순위 아이콘 노출여부
$event_type		= "best_product"; 				// 이벤트요소
$order_field	= "";							// 정렬 필드명
$order_sort		= "";							// 정렬 방식
$thema			= "";							// 테마 이름
?>
<!-- 상품나열 -->
<div class="main_list">
	<!-- 상품리스트구분 탭 -->
	<div class="main_tab">
		<span class="lineup">
			<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','new_product','<?=$order_field?>','<?=$order_sort?>');return false;" class="btn main_product_list_tab"><?=$row_setup['s_main_new_title']?></a>
			<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','best_product','<?=$order_field?>','<?=$order_sort?>');return false;" class="btn btn_hit main_product_list_tab"><?=$row_setup['s_main_hot_title']?></a>
			<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','soldout_soon_product','<?=$order_field?>','<?=$order_sort?>');return false;" class="btn main_product_list_tab"><?=$row_setup['s_main_close_title']?></a>
		</span>
	</div>
	<!-- // 상품리스트구분 탭 -->
	
	<!-- 상품리스트 -->
	<div class="item_list_area">
		<div class="layout_fix">
			<div class="<?=$display_area?>">
				<? include dirname(__FILE__)."/ajax.product.list.php"; ?>
			</div>
		</div>
	</div>

</div>

<script>
$(document).ready(function(){
	$(".main_product_list_tab").on('click',function() {
		$(".main_product_list_tab").removeClass('btn_hit');
		$(this).addClass('btn_hit');
	});
});
</script>


<!-- 하단 등록배너 -->
<?
	$banner_info = info_banner("site_main_footer",60,"data");
	if(sizeof($banner_info)>0) {
?>
<div class="main_bottom">
	<div class="layout_fix">
		<!-- 사이즈 321*116 -->
		<? foreach($banner_info as $k=>$v) { ?>
		<a href="<?=$v[b_link]?>" target="<?=$v[b_target]?>" class="bn" title="<?=$v[b_title]?>"><img src="<?=IMG_DIR_BANNER.$v[b_img]?>" alt="<?=$v[b_title]?>" /></a>
		<? } ?>
	</div>
</div>
<? } ?>

<?
include dirname(__FILE__)."/inc.popup.php";			// 팝업
?>