<!-- 고정배너 및 롤링배너 -->
<div class="visual_banner">
	<span class="left_line"></span>
	<span class="bt_line"></span>
	<div class="wrap_box">
		<? $banner_info = info_banner("mobile_main_top_big",1,"data"); if(count($banner_info)>0) { ?>
		<!-- 배경색 관리자 지정변경 -->
		<div class="fixed_bn" style="<?=$banner_info[0]['b_bgcolor']?"background-color:".$banner_info[0]['b_bgcolor'].";":""?>"><span class="lineup"><a href="<?=$banner_info[0]['b_link']?>" target="<?=$banner_info[0]['b_target']?>" class="bn"><!-- 1000*180 --><img src="<?=IMG_DIR_BANNER.$banner_info[0]['b_img']?>" alt="<?=$banner_info[0]['b_title']?>" /></a></span></div>
		<? } ?>

		<!-- 3개 배너 노출, 롤링 -->
		<?
			$banner_info = info_banner("mobile_main_top",12,"data");
			if(count($banner_info)>0) {
		?>
		<div class="rolling_bn">
			<span class="lineup">
			<a class="bn phantom_img" href="<?=$banner_info[0]['b_link']?>" target="<?=$banner_info[0]['b_target']?>" style="margin: 0 auto; text-align: center; float: none;"><img src="<?=IMG_DIR_BANNER.$banner_info[0]['b_img']?>" alt="" style="width:360px;float:none;"/></a>
			<div id="mobile_main_top_slider" style="display:none;">
				<? foreach($banner_info as $k=>$v) { ?>
				<a href="<?=$v['b_link']?>" target="<?=$v['b_target']?>" class="bn"><img src="<?=IMG_DIR_BANNER.$v['b_img']?>" alt="<?=$v['b_title']?>" /></a>
				<? } ?>
			</div>
			</span>
			<!-- 롤링표시, 하나만 등록이 되어있을 경우 display:none -->
			<div class="roll_btn" style="<?=count($banner_info)<=1?'display:none;':''?>">
				<span class="lineup">
					<div id="mobile_main_top_pager">
					<? foreach($banner_info as $k=>$v) { ?>
					<a href="#none" onclick="return false;" data-slide-index="<?=$k?>" class="off <?=$k==0?'active':''?>"></a>
					<? } ?>
					</div>
				</span>
			</div>
		</div>
		<? } ?>
	</div>
</div>
<? if(count($banner_info)>1) { ?>
<script>
var main_top_slider = '', wWidth = $(window).width(), sMargin = 0, sWidth = 300, bxWidth = $(window).width();
$(window).on('load',function(){

	$('#mobile_main_top_slider').imagesLoaded().done(function(){

		$('.visual_banner .phantom_img').remove();
		$('#mobile_main_top_slider').show();

		if ( wWidth > 499 ) { sWidth = 410;
			sMargin = Math.round( sWidth - ( Math.floor( ( parseInt($('.visual_banner .wrap_box').innerWidth()) + sWidth  ) / 2 ) ) ) * -1;
			//sMargin = Math.round( sWidth - ( Math.floor( ( parseInt($('.visual_banner .wrap_box').innerWidth()) - sWidth - 20 ) / 2 ) ) ) * -1;
			main_top_slider = $('#mobile_main_top_slider').bxSlider({
				auto: true, autoHover: true, controls: false, pagerCustom: '#mobile_main_top_pager', useCSS: false,
				minSlides: 1, maxSlides: 3, moveSlides: 1, slideWidth: sWidth, slideSelector: '.bn', slideMargin : 10,
				onSlideAfter: function($slideElement, oldIndex, newIndex){ main_top_slider.startAuto(); },
				onSliderLoad: function(currentIndex){
					$('#mobile_main_top_slider .bn').each(function(){ bxWidth += parseInt($(this).outerWidth()); });
					$('.visual_banner .rolling_bn .bx-wrapper').css({ 'width': (bxWidth>wWidth)?bxWidth:wWidth, 'maxWidth': (bxWidth>wWidth)?bxWidth:wWidth });
				}
			});
			$('#mobile_main_top_slider').css({ 'marginLeft': sMargin });
		} else {
			sMargin = Math.round( sWidth - ( Math.floor( ( wWidth + sWidth  ) / 2 ) ) ) * -1;
			//sMargin = Math.round( sWidth - ( Math.floor( ( wWidth - sWidth - 20 ) / 2 ) ) ) * -1;
			main_top_slider = $('#mobile_main_top_slider').bxSlider({
				auto: true, autoHover: true, controls: false, pagerCustom: '#mobile_main_top_pager', useCSS: false,
				minSlides: 1, maxSlides: 3, moveSlides: 1, slideWidth: sWidth, slideSelector: '.bn', slideMargin : 10,
				onSlideAfter: function($slideElement, oldIndex, newIndex){ main_top_slider.startAuto(); },
				onSliderLoad: function(currentIndex){
					$('#mobile_main_top_slider .bn').each(function(){ bxWidth += parseInt($(this).outerWidth()); });
					$('.visual_banner .rolling_bn .bx-wrapper').css({ 'width': (bxWidth>wWidth)?bxWidth:wWidth, 'maxWidth': (bxWidth>wWidth)?bxWidth:wWidth });
				}
			});
			$('#mobile_main_top_slider').css({ 'marginLeft': sMargin });
		}

	});

});
$(window).on('resize',function(){
	if( wWidth != $(window).width() ) {
		wWidth = $(window).width(); main_top_slider.destroySlider();
		if ( wWidth > 499 ) { sWidth = 410;
			//sMargin = Math.round( sWidth - ( Math.floor( ( parseInt($('.visual_banner .wrap_box').innerWidth()) - sWidth - 20 ) / 2 ) ) ) * -1;
			sMargin = Math.round( sWidth - ( Math.floor( ( parseInt($('.visual_banner .wrap_box').innerWidth()) + sWidth ) / 2 ) ) ) * -1;
			main_top_slider = $('#mobile_main_top_slider').bxSlider({
				auto: true, autoHover: true, controls: false, pagerCustom: '#mobile_main_top_pager', useCSS: false,
				minSlides: 1, maxSlides: 3, moveSlides: 1, slideWidth: sWidth, slideSelector: '.bn', slideMargin : 10,
				onSlideAfter: function($slideElement, oldIndex, newIndex){ main_top_slider.startAuto(); },
				onSliderLoad: function(currentIndex){
					$('#mobile_main_top_slider .bn').each(function(){ bxWidth += parseInt($(this).outerWidth()); });
					$('.visual_banner .rolling_bn .bx-wrapper').css({ 'width': (bxWidth>wWidth)?bxWidth:wWidth, 'maxWidth': (bxWidth>wWidth)?bxWidth:wWidth });
				}
			});
			$('#mobile_main_top_slider').css({ 'marginLeft': sMargin });
		} else {
			sWidth = 300;
			//sMargin = Math.round( sWidth - ( Math.floor( ( wWidth - sWidth - 20 ) / 2 ) ) ) * -1;
			sMargin = Math.round( sWidth - ( Math.floor( ( wWidth + sWidth ) / 2 ) ) ) * -1;
			main_top_slider = $('#mobile_main_top_slider').bxSlider({
				auto: true, autoHover: true, controls: false, pagerCustom: '#mobile_main_top_pager', useCSS: false,
				minSlides: 1, maxSlides: 3, moveSlides: 1, slideWidth: sWidth, slideSelector: '.bn', slideMargin : 10,
				onSlideAfter: function($slideElement, oldIndex, newIndex){ main_top_slider.startAuto(); },
				onSliderLoad: function(currentIndex){
					$('#mobile_main_top_slider .bn').each(function(){ bxWidth += parseInt($(this).outerWidth()); });
					$('.visual_banner .rolling_bn .bx-wrapper').css({ 'width': (bxWidth>wWidth)?bxWidth:wWidth, 'maxWidth': (bxWidth>wWidth)?bxWidth:wWidth });
				}
			});
			$('#mobile_main_top_slider').css({ 'marginLeft': sMargin });
		}
	}
});
</script>
<? } ?>

<? $banner_info = info_banner("site_main_md",40,"data"); if(count($banner_info)>0) { ?>
<!-- md's pick 배너 / 반응형일 때 4개까지 보임 -->
<div class="md_banner">
	<div class="main_tit"><strong>MD'S</strong> PICK</div>
	<div class="wrap_box">
		<span id="site_main_md_prev" style="float:left;"></span>
		<span id="site_main_md_next" style="float:right;"></span>
		<div class="bn_box">
			<div id="site_main_md_slider" style="display:none;">
			<? foreach($banner_info as $k=>$v) { ?>
			<div class="box">
				<a href="<?=$v['b_link']?>" target="<?=$v['b_target']?>" class="alink"></a>
				<div class="title_area">
					<div class="lineup">
						<? if( $v['b_title'] ) { ?>
						<!-- 타이틀 한줄 제한 -->
						<div class="tit"><?=$v['b_title']?></div>
						<span class="under_line"></span>
						<? } ?>
						<? if( $v['b_content'] ) { ?>
						<!-- 배너설명 글 최대 2줄까지 제한 -->
						<div class="txt"><?=$v['b_content']?></div>
						<? } ?>
					</div>
				</div>
				<div class="img_box"><img src="<?=IMG_DIR_BANNER.$v['b_img']?>" alt="<?=$v['b_title']?>"></div>
			</div>
			<? } ?>
			</div>
		</div>

	</div>
</div>
<script>
var site_main_md = '', wWidth_md = $(window).width(), minSlide = 2, maxSlide = 2, bxWidth_md = 0;
$(window).on('load',function(){
	if( wWidth_md > 499 ) { minSlide = 4; maxSlide = 4; }
	else { minSlide = 2; maxSlide = 2; }
	$('#site_main_md_slider').imagesLoaded().done(function(){
		$('#site_main_md_slider').show();
		site_main_md = $('#site_main_md_slider').bxSlider({
			auto: true, autoHover: false, speed: 500,
			slideSelector: '.box', easing: null, useCSS: false,
			slideWidth: Math.ceil($('.md_banner .wrap_box').innerWidth()/2), minSlides: minSlide, maxSlides: maxSlide, moveSlides: 1,
			pager: false, controls: true,
			nextText: '<a href="#none" onclick="return false;" class="btn next"></a>',
			nextSelector: '#site_main_md_next',
			prevText: '<a href="#none" onclick="return false;" class="btn prev"></a>',
			prevSelector: '#site_main_md_prev',
			onSlideAfter: function(){ site_main_md.startAuto(); },
			onSliderLoad: function(currentIndex){
				$('#site_main_md_slider .box').each(function(){ bxWidth_md += parseInt($(this).outerWidth()); });
				$('.visual_banner .rolling_bn .bx-wrapper').css({ 'width': (bxWidth_md>wWidth_md)?bxWidth_md:wWidth_md, 'maxWidth': (bxWidth_md>wWidth_md)?bxWidth_md:wWidth_md });
			}
		});
		//$('#site_main_md_slider').css({ 'marginLeft': -1 });
	});
});
$(window).on('resize',function(){
	if( wWidth_md != $(window).width() ) {
		wWidth_md = $(window).width(); site_main_md.destroySlider();
		if( wWidth_md > 499 ) { minSlide = 4; maxSlide = 4; }
		else { minSlide = 2; maxSlide = 2; }
		site_main_md = $('#site_main_md_slider').bxSlider({
			auto: true, autoHover: false, speed: 500,
			slideSelector: '.box', easing: null, useCSS: false,
			slideWidth: Math.ceil($('.md_banner .wrap_box').innerWidth()/2), minSlides: minSlide, maxSlides: maxSlide, moveSlides: 1,
			pager: false, controls: true,
			nextText: '<a href="#none" onclick="return false;" class="btn next"></a>',
			nextSelector: '#site_main_md_next',
			prevText: '<a href="#none" onclick="return false;" class="btn prev"></a>',
			prevSelector: '#site_main_md_prev',
			onSlideAfter: function(){ site_main_md.startAuto(); },
			onSliderLoad: function(currentIndex){
				$('#site_main_md_slider .box').each(function(){ bxWidth_md += parseInt($(this).outerWidth()); });
				$('.visual_banner .rolling_bn .bx-wrapper').css({ 'width': (bxWidth_md>wWidth_md)?bxWidth_md:wWidth_md, 'maxWidth': (bxWidth_md>wWidth_md)?bxWidth_md:wWidth_md });
			}
		});
		//$('#site_main_md_slider').css({ 'marginLeft': -1 });
	}
});
</script>
<? } ?>


<!-- 카테고리 추천상품 -->
<?
	$sub_main_array = array();
	$top_menu_assoc = _MQ_assoc("select * from odtCategory where catedepth=1 and cHidden='no' and subcate_display != '기획전' order by cateidx asc limit 5");
	foreach($top_menu_assoc as $top_menu_key => $top_menu_row) {
		if($top_menu_row['subcate_display'] == "지역") { // 지역일 경우에만 sub_cuid 추가
			$sub_cuid = reset(explode(",",$top_menu_row['lineup'])); // 지역 sub_cuid (묶음탭)
			$sub_row = _MQ("select catecode,subcate_main  from odtCategory where cHidden='no' and  subcate_display_choice = '".$sub_cuid."' order by subcate_main='Y' desc, cateidx asc limit 1"); // sub 메뉴에서 첫번째로 보여질 카테고리
			$sub_main_url = "/m/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&sub_cuid=$sub_cuid&cuid=".$sub_row['catecode']; // 링크값
			$sub_main_url = ($sub_row['catecode'] ? $sub_main_url : "javascript:alert('첫 카테고리 연결 미지정')"); // 링크값
			$sub_main_array[] = array('url'=>$sub_main_url,'title'=>$top_menu_row['catename'],'cuid'=>$top_menu_row['catecode']);
		} else {
			$sub_row = _MQ("select catecode,subcate_main from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by subcate_main='Y' desc,  cateidx asc limit 1");
			$sub_main_url = "/m/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&cuid=".$sub_row['catecode']; // 링크값
			$sub_main_array[] = array('url'=>$sub_main_url,'title'=>$top_menu_row['catename'],'cuid'=>$top_menu_row['catecode']);
		}
		$init_link = $top_menu_key==0 ? $sub_main_url : $init_link;
		$init_title = $top_menu_key==0 ? $top_menu_row['catename'] : $init_title;
	}
?>
<div class="main_category">
	<div class="main_tit"><strong>카테고리</strong> 추천상품</div>

	<a href="<?=$init_link?>" class="btn_more" id="main_ctg_link"><span class="ctg" id="main_ctg_title"><?=$init_title?></span> 전체보기</a>

	<!-- 선택탭 -->
	<div class="tab_box">
		<div class="a_box">
			<? foreach($sub_main_array as $k=>$v) { ?>
			<!-- 해당 탭일 경우 btn_hit 추가 -->
			<a href="#none" onclick="return false;" data-cuid="<?=$v['cuid']?>" data-cname="<?=$v['title']?>" data-url="<?=$v['url']?>" class="btn <?=$k==0?'btn_hit':''?> main_ctg_tab"><?=$v['title']?><span class="line"></span></a>
			<? } ?>
		</div>
	</div>

	<!-- 썸네일형 리스트 -->
	<div class="list_thumb">
		<ul id="main_ctg_list"></ul>
	</div>
</div>
<script>
$(document).ready(function(){
	$('.main_ctg_tab').on('click',function(){
		var cuid = $(this).data('cuid'), cname = $(this).data('cname'), url = $(this).data('url');
		$('.main_ctg_tab').removeClass('btn_hit'); $(this).addClass('btn_hit');
		$('#main_ctg_title').text(cname); $('#main_ctg_link').attr('href',url);
		load_main_best_product(cuid);
	});
	load_main_best_product("<?=$sub_main_array[0]['cuid']?>");
	function load_main_best_product(cuid) {
		$.ajax({
			data: {cuid:cuid},
			type: 'POST',
			cache: false,
			url: '/m/ajax.main.best_product.list.php',
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


<!-- 상품 노출 -->
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
<div class="main_item_list">
	<!-- 분류탭-->
	<div class="main_tab">
		<!-- 해당탭일 때 btn_hit 추가 -->
		<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','new_product','<?=$order_field?>','<?=$order_sort?>');return false;" class="main_product_list_tab btn"><?=$row_setup['s_main_new_title']?></a>
		<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','best_product','<?=$order_field?>','<?=$order_sort?>');return false;" class="main_product_list_tab btn btn_hit"><?=$row_setup['s_main_hot_title']?></a>
		<a href="#none" onclick="product_list_view('<?=$display_area?>','<?=$cuid?>','<?=$listmaxcount?>','<?=$list_type?>','<?=$pagenate_use?>','<?=$hit_num_use?>','<?=$thema?>','soldout_soon_product','<?=$order_field?>','<?=$order_sort?>');return false;" class="main_product_list_tab btn"><?=$row_setup['s_main_close_title']?></a>
	</div>

	<div class="<?=$display_area?>"><? include dirname(__FILE__)."/ajax.product.list.php"; ?></div>

	<script>
	$(document).ready(function(){
		$(".main_product_list_tab").on('click',function() {
			$(".main_product_list_tab").removeClass('btn_hit');
			$(this).addClass('btn_hit');
		});
	});
	</script>
</div><!-- .main_item_list -->


<?
include dirname(__FILE__)."/inc.popup.php";
?>