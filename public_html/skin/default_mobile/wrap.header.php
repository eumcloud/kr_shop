<?
	include dirname(__FILE__)."/inc.header.php";

	// 상품상세페이지에서 cuid 가 없다면, 직접 뽑아온다. (top menu 활성화를 위해서.)
	/*if($_GET[pn] == "product.view" && $_GET[pcode] && !$_GET[cuid]) {
		$_GET[cuid] = _MQ_result("select pct_cuid from odtProductCategory where pct_pcode = '".$_GET[pcode]."' limit 1");
	}*/

	// cuid 값이 있으면 1,2,3차 카테고리 정보를 가져온다.
	if(!in_array($pn,array('product.view')) && $_GET['cuid']) { $category_total_info = get_total_category_info($_GET['cuid']); }
	
	// 지역카테고리에서 sub_cuid 가 없을때 강제로 지정한다.
	if($category_total_info['depth1_catecode'] == 1 && !$_GET['sub_cuid']) {
		$_GET['sub_cuid'] = $category_total_info['depth2_subcate_display_choice'];
	}

	// 카트에 담긴 상품수
	$cart_cnt = get_cart_cnt();
?>

<?if($pn == "board.form") { // 게시판 글쓰기 사용 ?>
<!-- TinyMCE -->
<script language="Javascript" src="/include/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
	selector: "textarea[geditor]",
	theme: "modern",
	language : 'ko_KR',
	height: 150,
	force_br_newlines : false,
	force_p_newlines : true,
	convert_newlines_to_brs : false,
	remove_linebreaks : true,
	forced_root_block : 'p', // Needed for 3.x
	relative_urls:false,
	allow_script_urls: true,
	remove_script_host: false,
	convert_urls: false,
	formats: { bold : {inline : 'b' }},
	extended_valid_elements: "@[class|id|width|height|alt|href|style|rel|cellspacing|cellpadding|border|src|name|title|type|onclick|onfocus|onblur|target],b,i,em,strong,a,img,br,h1,h2,h3,h4,h5,h6,div,table,tr,td,s,del,u,p,span,article,section,header,footer,svg,blockquote,hr,ins,ul,dl,object,embed,pre",
	plugins: [
		"jbimages",
		 "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
		 "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
		 "save table directionality emoticons template paste textcolor"
   ],
   content_css: "/pages/css/editor.css",
   body_class: "editor_content",
   menubar : false,
   toolbar1: "alignleft aligncenter alignright | outdent indent | advlist bold italic forecolor backcolor | jbimages | autolink"
 }); 
$(document).ready(function(){ $('form').submit(function(){ tinyMCE.triggerSave(); }); }); // 에디터 작성시 validate 조정
</script>
<? } ?>

<div class="wrap" name="topPosition">

<? include dirname(__FILE__)."/inc.slide.sidebar.php"; ?>

<!-- 상단공통 -->
<div class="header">
	<? $banner_info = info_banner("mobile_site_top_logo",1,"data"); if(count($banner_info)>0) { ?>
	<a href="<?=$banner_info[0]['b_link']?>" target="<?=$banner_info[0]['b_target']?>" class="top_logo"><img src="<?=IMG_DIR_BANNER.$banner_info[0]['b_img']?>" alt="<?=$banner_info[0]['b_title']?>" /></a>
	<? } ?>
	<div class="member_menu">
		<a href="<?=is_login()?"/m/?pn=mypage.main":"/m/?pn=member.login.form&path=".enc('e','pn=mypage.main')?>" class="btn btn_my"><img src="/m/images/ic_my_top.png" alt="마이페이지" /></a>
		<a href="/m/?pn=shop.cart.list" class="btn btn_cart">
			<!-- 담긴 상품 없을 시 display:none --><? if($cart_cnt>0){ ?><span class="cart_num"><span class="num"><?=number_format($cart_cnt)?></span></span><? } ?>
			<img src="/m/images/ic_cart.png" alt="장바구니 보기" />
		</a>
	</div>
</div>

<!-- 검색영역 -->
<div class="search_area">
	<form name="search_form" id="search_form" role="search" action="/m/">
	<input type="hidden" name="pn" value="product.search.list"/>
	<div class="search_box">
		<div class="input_box">
			<input type="search" name="keyword" value="<?=trim(stripslashes($_GET['keyword'] ? htmlspecialchars($_GET['keyword']) : $row_setup['s_search_keyword']))?>" class="input_search" placeholder="검색어를 입력하세요." id="main_search_input"/>
		</div>
		<!-- 검색어 입력시 나타남 --><input type="submit" name="" value="검색" class="btn_search" placeholder=""/>
	</div>
	</form>
</div>
<script>
$(document).ready(function(){
	$('#main_search_input').on('focus',function(){ if( $(this).val() == "<?=$row_setup['s_search_keyword']?>" ) { $(this).val(''); } });
	//$('#main_search_input').on('keyup',function(){ if( $(this).val() != '' ) { $('#search_form .btn_search').show(); } else { $('#search_form .btn_search').hide(); } });
	$('form[name=search_form]').on('submit',function(){
		if( $('#main_search_input').val() == '' ) { alert('검색어를 입력하세요.'); $('#main_search_input').focus(); return false; };
	});
});
</script>

<!-- 메인/서브메인/기획전을 제외한 상품리스트/상품상세/공통페이지에서는 sub_nav 추가 -->
<div class="nav <?=(!$pn || in_array($pn,array('main','product.main','product.promotion')))?'':'sub_nav'?>">

	<div class="category">
		<div id="swiper_menu">
			<div class="swiper_wrap">
			<?
			$top_menu_assoc = _MQ_assoc("select * from odtCategory where catedepth=1 and cHidden='no' order by cateidx asc ");
			foreach($top_menu_assoc as $top_menu_key => $top_menu_row) {
				/* 1차 카테고리 링크값 구하기 */
				if($top_menu_row['subcate_display'] == "지역") { // 지역일 경우에만 sub_cuid 추가
					$sub_cuid = reset(explode(",",$top_menu_row['lineup'])); // 지역 sub_cuid (묶음탭)
					$sub_row	= _MQ("select catecode,subcate_main from odtCategory where cHidden='no' and parent_catecode='". $top_menu_row['catecode'] ."' and catedepth=2 and subcate_display_choice = '".$sub_cuid."' order by subcate_main='Y' desc, cateidx asc limit 1"); // sub 메뉴에서 첫번째로 보여질 카테고리
					$sub_main_url = "/m/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&sub_cuid=$sub_cuid&cuid=".$sub_row['catecode']; // 링크값
					$sub_main_url = ($sub_row['catecode'] ? $sub_main_url : "javascript:alert('첫 카테고리 연결 미지정')"); // 링크값
				} else if($top_menu_row['subcate_display'] == "기획전") { // 기획전은 메인없이 바로 list 페이지로 이동
					$sub_row = _MQ("select catecode from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by cateidx asc limit 1"); // sub 메뉴에서 첫번째로 보여질 카테고리
					$sub_main_url = "/m/?pn=product.promotion&cuid=".$sub_row['catecode']; // 링크값
				} else {
					$sub_row = _MQ("select catecode,subcate_main from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by subcate_main='Y' desc, cateidx asc limit 1");
					$sub_main_url = "/m/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&cuid=".$sub_row['catecode']; // 링크값
				}
				$sub_main_title = $top_menu_row['catename']; // 1차 카테고리명
				$this_page = ($category_total_info['depth1_catecode'] == $top_menu_row['catecode']) ? true : false;
			?>
				<!-- 해당 카테고리일 때 ctg_hit 추가 -->
				<a href="<?=$sub_main_url?>" class="ctg <?=$this_page?'ctg_hit':''?>"><?=$sub_main_title?><span class="line"></span></a>
			<? } ?>
			</div>
		</div>
	</div>
	<script>
	var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('swiper_menu'), myScroll = '';
	$(function() {
		$.each($('#swiper_menu .ctg'), function(k, v){ scrollWidth += $('#swiper_menu .ctg').eq(k).outerWidth(); });
		if( $(document).width() - $('.btn_slide').width() < scrollWidth ) {
			$('#swiper_menu .swiper_wrap').css('width', parseInt(scrollWidth)+26);
			myScroll = new iScroll(wrapper,{ hScrollbar: false, vScrollbar: false });
			if(scrollIndex > 0 && $('#swiper_menu .ctg_hit').length > 0) { myScroll.scrollToElement(document.querySelector('#swiper_menu .ctg_hit'), 0); }
		}
	});
	$(window).on('resize',function(){
		if( $(document).width() - $('.btn_slide').width() < scrollWidth ) {
			$('#swiper_menu .swiper_wrap').css('width', parseInt(scrollWidth)+26);
			myScroll = new iScroll(wrapper,{ hScrollbar: false, vScrollbar: false });
			if(scrollIndex > 0 && $('#swiper_menu .ctg_hit').length > 0) { myScroll.scrollToElement(document.querySelector('#swiper_menu .ctg_hit'), 0); }
		} else { if( myScroll ) { setTimeout(function () { myScroll.refresh(); }, 0); myScroll.destroy(); myScroll = null; } }
	});
	</script>

	<!-- 반응형일 때만 나타남/서브 리스트에서는 안나타남 -->
	<a href="#none" onclick="return false;" class="open_slide btn_slide"></a>
	<? if(!$pn || $pn=='main') { ?>
	<div class="nav_open">
		<!-- 로그인 후/ LOGOUT, join은 display:none -->
		<div class="btn_area">
			<? if(is_login()){ ?>
			<span class="btn_box"><a href="/pages/member.login.pro.php?_mode=mobile_logout" class="btn_mem">LOGOUT</a></span>
			<? } else { ?>
			<span class="btn_box"><a href="/m/?pn=member.login.form&path=<?=$path?>" class="btn_mem">LOGIN</a></span>
			<span class="btn_box"><a href="/m/?pn=member.join.agree" class="btn_mem">JOIN</a></span>
			<? } ?>
		</div>
		<ul>
			<li><a href="<?=is_login()?"/m/?pn=mypage.main":"/m/?pn=member.login.form&path=".enc('e','pn=mypage.main')?>" class="btn">마이페이지<span class="arrow"></span></a></li>
			<li><a href="<?=is_login()?"/m/?pn=mypage.order.list":"/m/?pn=service.guest.order.list"?>" class="btn"><?=($row_setup['none_member_buy'] == 'Y' && !is_login() ?"비회원주문조회":"주문배송조회")?><span class="arrow"></span></a></li>
			<li><a href="/m/?pn=mypage.request.form" class="btn">1:1 온라인문의<span class="arrow"></span></a></li>
			<li><a href="/m/?pn=board.list&_menu=faq" class="btn">자주묻는질문<span class="arrow"></span></a></li>
			<li><a href="/m/?pn=service.guide" class="btn">이용안내<span class="arrow"></span></a></li>
			<li><a href="/m/?pn=service.page.view&pageid=company" class="btn">회사소개<span class="arrow"></span></a></li>
		</ul>
	</div>
	<? } ?>
</div>