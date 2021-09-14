<?
	include dirname(__FILE__)."/inc.header.php";

	// 상품상세페이지에서 cuid 가 없다면, 직접 뽑아온다. (top menu 활성화를 위해서.)
	/*if($_GET['pn'] == "product.view" && $_GET['pcode'] && !$_GET['cuid']) {
		$_GET['cuid'] = _MQ_result("select pct_cuid from odtProductCategory where pct_pcode = '".$_GET['pcode']."' limit 1");
	}*/

	// cuid 값이 있으면 1,2,3차 카테고리 정보를 가져온다.
	if(!in_array($pn,array('product.view')) && $_GET['cuid']) { $category_total_info = get_total_category_info($_GET['cuid']); }

	// 지역카테고리에서 sub_cuid 가 없을때 강제로 지정한다.
	if($category_total_info['depth1_catecode'] == 1 && !$_GET['sub_cuid']) {
		$_GET['sub_cuid'] = $category_total_info['depth2_subcate_display_choice'];
	}
?>

<?if($pn == "board.form") { // 게시판 글쓰기 사용 ?>
<!-- TinyMCE -->
<script language="Javascript" src="/include/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
	selector: "textarea[geditor]",
	theme: "modern",
	language : 'ko_KR',
	height: 370,
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
		 "save table contextmenu directionality emoticons template paste textcolor imagetools"
   ],
   content_css: "/pages/css/editor.css",
   body_class: "editor_content",
   menubar : false,
   toolbar1: "undo redo | fontsizeselect | advlist bold italic forecolor backcolor | charmap | hr | jbimages | autolink link media",
   toolbar2: "bullist numlist outdent indent | alignleft aligncenter alignright alignjustify | table"
 });
$(document).ready(function(){ $('form').submit(function(){ tinyMCE.triggerSave(); }); }); // 에디터 작성시 validate 조정
</script>
<? } // 게시판 글쓰기 사용 ?>

<div class="wrap" name="topPosition">

<!-- 최상단 배너 -->
<?php
$banner_info = info_banner("site_top_big",1,"data");
if(sizeof($banner_info) > 0 && (!$pn||$pn=='main') && $_COOKIE['AuthPopupClose_topbig']!='Y') {
?>
<div class="top_banner" style="<?=$banner_info[0]['b_bgcolor']?"background-color:".$banner_info[0]['b_bgcolor'].";":""?>">
	<div class="layout_fix">
		<a href="#none" onclick="return false;" class="top_banner_close btn_close"><img src="/pages/images/ic_top_bn_close.gif" alt="닫기" /></a>
		<a href="<?=$banner_info[0]['b_link']?>" title="<?=$banner_info[0]['b_title']?>" target="<?=$banner_info[0]['b_target']?>" class="banner"><img src="<?=IMG_DIR_BANNER.$banner_info[0]['b_img']?>" alt="<?=$banner_info[0]['b_title']?>" /></a>
	</div>
</div>
<script>
// 최상단 배너 닫기
$(document).ready(function(){
	$('.top_banner_close').on('click',function(){
		$.ajax({
			data: {'_mode':'topbig_banner_close'},
			type: 'POST', cache: false,
			url: '/pages/member.login.pro.php',
			success: function(data) {
				$('.top_banner').slideUp(700,'easeInOutCubic');
				$('.fly_left, .fly_right').animate({ 'marginTop': parseInt($('.fly_left').css('marginTop')) - $('.top_banner').outerHeight() + 'px' },700,'easeInOutCubic');
			}
		});
	});
});
</script>
<? } ?>


<!-- 상단 메뉴 -->
<div class="top_menu">
	<div class="layout_fix">
		<!-- 왼쪽 메뉴 -->
		<div class="left_menu">
			<a href="javascript:bookmark('<?=stripslashes($row_company['homepage_title'])?>', 'http://<?=$_SERVER['HTTP_HOST']?>')" class="btn">즐겨찾기</a>
			<span class="line"></span>
			<a href="#none" onclick="popup_subscription();return false;" class="btn">구독하기</a>
		</div>

		<!-- 오른쪽 메뉴 -->
		<div class="right_menu">
			<?
				if(!is_login()) {
					if(!$path && $pn<>"member.login.form"){ $path = enc("e",$_SERVER['QUERY_STRING']); }
			?>
			<a href="/?pn=member.login.form&path=<?=$path?>" class="mu">로그인</a>
			<span class="line"></span>
			<a href="/?pn=member.join.agree" class="mu">회원가입</a>
			<span class="line"></span>
			<? } else { ?>
			<a href="/pages/member.login.pro.php?_mode=logout" class="mu">로그아웃</a>
			<span class="line"></span>
			<? } ?>
			<? if($row_setup['none_member_buy'] == 'Y' && !is_login() ) { ?>
			<a href="/?pn=service.guest.order.list" class="mu">비회원주문조회</a>
			<span class="line"></span>
			<? } ?>
			<a href="/?pn=service.page.view&pageid=mobile" class="mu ic">모바일쇼핑</a>
			<span class="line"></span>
			<!-- 고객센터 -->
			<span class="menu_sub">
				<a href="/?pn=service.main" class="tit"><span class="txt">고객센터</span><span class="tit_ic"></span></a>
				<span class="over">
					<a href="/?pn=service.guide" class="sub_btn">이용안내</a>
					<a href="/?pn=board.list&_menu=faq" class="sub_btn">자주묻는질문</a>
					<a href="/?pn=service.partner.form" class="sub_btn">제휴/광고문의</a>
					<a href="/?pn=board.list&_menu=notice" class="sub_btn">공지사항</a>
					<a href="/?pn=board.list&_menu=event" class="sub_btn">이벤트</a>
				</span>
			</span>
			<? if(is_login()) { ?>
			<span class="line"></span>
			<!-- 마이페이지 -->
			<span class="menu_sub">
				<a href="/?pn=mypage.main" class="tit"><span class="txt">마이페이지</span><span class="tit_ic"></span></a>
				<span class="over">
					<a href="/?pn=mypage.order.list" class="sub_btn">주문내역</a>
					<a href="/?pn=mypage.wish.list" class="sub_btn">찜한상품</a>
					<a href="/?pn=mypage.action_point.list" class="sub_btn">참여점수</a>
					<a href="/?pn=mypage.point.list" class="sub_btn">적립금</a>
					<a href="/?pn=mypage.coupon.list" class="sub_btn">쿠폰함</a>
					<a href="/?pn=mypage.request.list" class="sub_btn">1:1상담내역</a>
					<a href="/?pn=mypage.request.form" class="sub_btn">1:1온라인문의</a>
					<a href="/?pn=mypage.posting.list" class="sub_btn">상품문의내역</a>
					<a href="/?pn=mypage.modify.form" class="sub_btn">정보수정</a>
				</span>
			</span>
			<? } ?>
			<span class="line"></span>
			<!-- 장바구니 -->
			<a href="/?pn=shop.cart.list" class="btn_cart">
				<span class="txt">장바구니</span>
				<span class="cart_num"><span class="num" id="cart_cnt_txt"><?=number_format(get_cart_cnt())?></span><span class="edge"></span></span>
			</a>
		</div>
	</div>
</div>


<!-- 로고탑 -->
<div class="header">
	<div class="layout_fix">
		<!-- 로고 -->
		<? $banner_info = info_banner("site_top_logo",1,"data"); ?>
		<a href="<?=$banner_info[0]['b_link']?>" target="<?=$banner_info[0]['b_target']?>" title="<?=$banner_info[0]['b_title']?>" class="logo_top"><img src="<?=IMG_DIR_BANNER.$banner_info[0]['b_img']?>" alt="<?=$banner_info[0]['b_title']?>" /></a>

		<!-- 검색박스 -->
		<?
			//$arrSearchKeyword = explode("," , $row_setup['s_recommend_keyword']); shuffle($arrSearchKeyword);
			$search_keyword = $_GET['keyword'];
		?>
		<div class="search_area">
			<div class="search_box">
				<form name="search_form" role="search" action="/">
					<input type="hidden" name="pn" value="product.search.list"/>
					<input type="search" name="keyword" value="<?=trim(stripslashes($_GET['keyword'] ? htmlspecialchars($_GET['keyword']) : $row_setup['s_search_keyword']))?>" class="search_inner" id="main_search_input" placeholder="검색어를 입력하거나 키워드를 클릭해주세요." />
					<span class="btn_search_box"><input type="submit" name="" value="" class="btn_search"><span class="edge"></span></span>
				</form>
				<script>
				$(document).ready(function(){
					$('#main_search_input').on('focus',function(){ if( $(this).val() == "<?=$row_setup['s_search_keyword']?>" ) { $(this).val(''); } });
					$('form[name=search_form]').on('submit',function(){
						if( $('#main_search_input').val() == '' ) { alert('검색어를 입력하세요.'); $('#main_search_input').focus(); return false; };
					});
				});
				</script>
			</div>

			<!-- 추천검색어 -->
			<div class="open_keyowrd">
				<div class="titlt_box">추천 검색 키워드</div>
				<div class="keyword_box">
					<?
					$arrSearchKeyword = explode("," , $row_setup[s_recommend_keyword]);
					foreach($arrSearchKeyword as $tmp_key => $tmp_val) {
						if($tmp_key % 10 ==0 && $tmp_key) 	// 4개씩 노출.
							echo "</div><div class='keyword' style='display:none'>";
						else
							if($tmp_key) echo "";

						echo "<a href='?pn=product.search.list&keyword=".$tmp_val."' class='link'>".$tmp_val."</a>";
					}
					?>
				</div>
			</div>
		</div>

		<!-- 등록배너 -->
		<? $banner_info = info_banner("site_top_right",50,"data"); ?>
		<div class="banner_box">
			<div class="btn_roll">
				<a href="#none" onclick="return false;" id="site_top_right_prev" class="btn prev"></a>
				<a href="#none" onclick="return false;" id="site_top_right_next" class="btn next"></a>
			</div>
			<div class="img_box">
				<div id="site_top_right_slider" style="width:225px;overflow:hidden;">
				<? foreach($banner_info as $k=>$v) { ?>
				<a href="<?=$v['b_link']?>" target="<?=$v['b_target']?>" title="<?=$v['b_title']?>"><img src="<?=IMG_DIR_BANNER.$v['b_img']?>" alt="<?=$v['b_title']?>"/></a>
				<? } ?>
				</div>
			</div>
		</div>
		<script>
		var site_top_right_slider = '';
		$(window).on('load',function(){
			site_top_right_slider = $('#site_top_right_slider').bxSlider({
				mode: 'vertical', auto: true, autoHover: true, speed: 500,
				easing: 'easeInOutCubic', useCSS: false, controls: false, pager: false, slideWidth: 225,
				onSlideAfter: function(){ site_top_right_slider.startAuto(); }
			});
		});
		$(document).ready(function(){
			$('#site_top_right_prev').on('click',function(){ site_top_right_slider.goToPrevSlide(); });
			$('#site_top_right_next').on('click',function(){ site_top_right_slider.goToNextSlide(); });
		});
		</script>
	</div>
</div>


<!-- 카테고리 -->
<div class="nav">
	<div class="layout_fix">

		<!-- 전체카테고리보기 -->
		<div class="btn_sitemap">
			<div class="btn_all">
				<a href="#none" onclick="return false;" class="alink"><img src="/pages/images/blank.gif" alt="" /></a>
				<span class="ic">
					<img src="/pages/images/ic_all_ctg.gif" alt="전체카테고리" class="off" />
					<img src="/pages/images/ic_all_ctg_over.gif" alt="전체카테고리" class="over" />
				</span>
				<span class="txt">전체카테고리</span>
			</div>

			<!-- 전체 카테고리 -->
			<div class="sitemap">
				<a href="#none" onclick="return false;" class="btn_close close_sitemap"><img src="/pages/images/btn_all_close.gif" alt="닫기" /></a>
				<script>
					$(document).ready(function(){
						$('.btn_sitemap').on('mouseenter',function(){ $(this).addClass('hover'); });
						$('.btn_sitemap').on('mouseleave',function(){ $(this).removeClass('hover'); });
						$('.close_sitemap').on('click',function(){ $('.btn_sitemap').removeClass('hover'); })
					});
				</script>

				<!-- 카테고리 박스 -->
				<table summary="" class="inner_box">
					<colgroup><col width="188"/><col width="*"/></colgroup>
					<tbody>
					<?
						$top_menu_assoc = _MQ_assoc("select * from odtCategory where catedepth=1 and cHidden='no' order by cateidx asc ");
						foreach($top_menu_assoc as $top_menu_key => $top_menu_row) {
							/* 1차 카테고리 링크값 구하기 */
							if($top_menu_row['subcate_display'] == "지역") { // 지역일 경우에만 sub_cuid 추가
								$sub_cuid = reset(explode(",",$top_menu_row['lineup'])); // 지역 sub_cuid (묶음탭)
								$sub_row = _MQ("select catecode,subcate_main from odtCategory where cHidden='no' and parent_catecode='". $top_menu_row['catecode'] ."' and catedepth=2 and subcate_display_choice = '".$sub_cuid."' order by subcate_main='Y' desc, cateidx asc limit 1"); // sub 메뉴에서 첫번째로 보여질 카테고리
								$sub_main_url = "/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&sub_cuid=$sub_cuid&cuid=".$sub_row['catecode']; // 링크값
								$sub_main_url = ($sub_row['catecode'] ? $sub_main_url : "javascript:alert('첫 카테고리 연결 미지정')"); // 링크값
							} else if($top_menu_row['subcate_display'] == "기획전") { // 기획전은 메인없이 바로 list 페이지로 이동
								$sub_row = _MQ("select catecode from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by cateidx asc limit 1"); // sub 메뉴에서 첫번째로 보여질 카테고리
								$sub_main_url = "/?pn=product.promotion&cuid=".$sub_row['catecode']; // 링크값
							} else {
								$sub_row = _MQ("select catecode,subcate_main from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by subcate_main='Y' desc, cateidx asc limit 1");
								$sub_main_url = "/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&cuid=".$sub_row['catecode']; // 링크값
							}
							$sub_main_title = $top_menu_row['catename']; // 1차 카테고리명

							/* 2차 카테고리 메뉴 생성 */
							unset($sub_category_html);
							if($top_menu_row['subcate_display'] == "지역") { // 지역 스타일은 카테고리 정보의 lineup 출력
								$sub_assoc = explode(",",$top_menu_row['lineup']); // 묶음탭
								foreach($sub_assoc as $sub_key => $sub_val) {
									$sub_row = _MQ("select catecode,subcate_main from odtCategory where cHidden='no' and subcate_display_choice = '".$sub_val."' and find_in_set('".$top_menu_row['catecode']."',parent_catecode) > 0 order by subcate_main='Y' desc, cateidx asc limit 1");
									$sub_url = "/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&sub_cuid=".$sub_val."&cuid=".$sub_row['catecode']."";
									$sub_category_html .= "<span class='line'></span><a class='btn' href='".($sub_row['catecode'] ? $sub_url : "javascript:alert(\"카테고리 미지정\")")."'>".$sub_val."</a>";
								}
							} else if($top_menu_row['subcate_display'] == "기획전") {
								$sub_assoc = _MQ_assoc("select catename,catecode,cateimg,subcate_main from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) > 0 and catedepth=2 and cHidden='no' order by cateidx asc"); // 상품 리스트의 탭을 위하여 2차 카테고리 추출
								foreach($sub_assoc as $sub_key => $sub_row) {
									$sub_url = "/?pn=product.promotion&cuid=".$sub_row['catecode']; // 첫번째 카테고리는 메인으로 이동
									$sub_category_html .=  "<span class='line'></span><a href='".$sub_url."' class='btn'>".$sub_row['catename']."</a>";
								}
							} else { // 쇼핑 스타일은 2차 카테고리 출력
								$sub_assoc = _MQ_assoc("select catename,catecode,cateimg,subcate_main from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) > 0 and catedepth=2 and cHidden='no' order by cateidx asc"); // 상품 리스트의 탭을 위하여 2차 카테고리 추출
								foreach($sub_assoc as $sub_key => $sub_row) {
									$sub_url = "/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&cuid=".$sub_row['catecode']; // 첫번째 카테고리는 메인으로 이동
									$sub_category_html .= "<span class='line'></span><a href='".$sub_url."' class='btn'>".$sub_row['catename']."</a>";
								}
							}
					?>
						<tr>
							<td class="main_ctg">
								<a href="<?=$sub_main_url?>" class="ctg_tit"><?=$sub_main_title?></a>
							</td>
							<td class="sub_ctg">
								<span class="w_line"></span>
								<?=$sub_category_html?>
							</td>
						</tr>
					<? } ?>
					</tbody>
				</table>
			</div>
		</div>

		<? // 카테고리 메인메뉴 출력
			$top_menu_assoc = _MQ_assoc("select * from odtCategory where catedepth=1 and cHidden='no' order by cateidx asc limit 5");
			foreach($top_menu_assoc as $top_menu_key => $top_menu_row) {
				/* 1차 카테고리 링크값 구하기 */
				$special_page = false;
				if($top_menu_row['subcate_display'] == "지역") { // 지역일 경우에만 sub_cuid 추가
					$sub_cuid = reset(explode(",",$top_menu_row['lineup'])); // 지역 sub_cuid (묶음탭)
					//$sub_row = _MQ("select catecode,subcate_main from odtCategory where cHidden='no' and subcate_display_choice = '".$sub_cuid."' order by subcate_main='Y' desc, cateidx asc limit 1"); // sub 메뉴에서 첫번째로 보여질 카테고리
					$sub_row = _MQ("select catecode,subcate_main from odtCategory where cHidden='no' and parent_catecode='". $top_menu_row['catecode'] ."' and catedepth=2 and subcate_display_choice = '".$sub_cuid."' order by subcate_main='Y' desc, cateidx asc limit 1"); // sub 메뉴에서 첫번째로 보여질 카테고리
					$sub_main_url = "/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&sub_cuid=$sub_cuid&cuid=".$sub_row['catecode']; // 링크값
					$sub_main_url = ($sub_row['catecode'] ? $sub_main_url : "javascript:alert('첫 카테고리 연결 미지정')"); // 링크값
				} else if($top_menu_row['subcate_display'] == "기획전") { // 기획전은 메인없이 바로 list 페이지로 이동
					$sub_row = _MQ("select catecode from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by cateidx asc limit 1"); // sub 메뉴에서 첫번째로 보여질 카테고리
					$sub_special_url = "/?pn=product.promotion&cuid=".$sub_row['catecode']; // 링크값
					$special_page = true;
				} else {
					$sub_row = _MQ("select catecode,subcate_main from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by subcate_main='Y' desc, cateidx asc limit 1");
					$sub_main_url = "/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&cuid=".$sub_row['catecode']; // 링크값
				}
				$sub_main_title = $top_menu_row['catename']; // 1차 카테고리명

				/* 2차 카테고리 메뉴 생성 */
				unset($sub_category_html); $sub_category_html = "<div class='line'>";
				if($top_menu_row['subcate_display'] == "지역") { // 지역 스타일은 카테고리 정보의 lineup 출력
					$sub_assoc = explode(",",$top_menu_row['lineup']); // 묶음탭
					foreach($sub_assoc as $sub_key => $sub_val) {
						$sub_row = _MQ("select catecode,subcate_main from odtCategory where cHidden='no' and subcate_display_choice = '".$sub_val."' and find_in_set('".$top_menu_row['catecode']."',parent_catecode) > 0 order by subcate_main='Y' desc, cateidx asc limit 1");
						$sub_url = "/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&sub_cuid=".$sub_val."&cuid=".$sub_row['catecode']."";
						$sub_category_html .= "<a class='btn' href='".($sub_row['catecode'] ? $sub_url : "javascript:alert(\"카테고리 미지정\")")."'>".$sub_val."</a>";
						if($sub_key>0&&(($sub_key+1)%2)==0) { $sub_category_html .= "</div><div class='line'>"; }
					}
				} else if($top_menu_row['subcate_display'] == "기획전") {
				} else { // 쇼핑 스타일은 2차 카테고리 출력
					$sub_assoc = _MQ_assoc("select catename,catecode,cateimg,subcate_main from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) > 0 and catedepth=2 and cHidden='no' order by cateidx asc"); // 상품 리스트의 탭을 위하여 2차 카테고리 추출
					foreach($sub_assoc as $sub_key => $sub_row) {
						$sub_url = "/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&cuid=".$sub_row['catecode']; // 첫번째 카테고리는 메인으로 이동
						$sub_category_html .= "<a href='".$sub_url."' class='btn'>".$sub_row['catename']."</a>";
						if($sub_key>0&&(($sub_key+1)%2)==0) { $sub_category_html .= "</div><div class='line'>"; }
					}
				} $sub_category_html .= "</div>";

				$this_page = ($pn!='product.search.list'&&$category_total_info['depth1_catecode'] == $top_menu_row['catecode']) ? true : false;
		?>

			<? if(!$special_page) { ?>
			<!-- 카테고리하나/해당 페이지일 때 depth_hit 추가-->
			<div class="depth1 <?=$this_page?'depth_hit':''?>">
				<div class="first">
					<a href="<?=$sub_main_url?>" class="tit"><?=$sub_main_title?></a>
					<span class="sub_nav_bg"><span class="line left"></span><span class="line right"></span></span>
				</div>

				<!-- 2차 카테고리 -->
				<? if(!$this_page) { ?>
				<div class="depth2">
					<div class="inner_box">
						<?=$sub_category_html?>
					</div>
					<!-- 관리자등록배너 : 285*123 -->
					<!-- 미등록시 display:none -->
					<? $banner_info = info_banner($top_menu_row['catecode'].",menu",1,"data"); if(count($banner_info)>0) { ?>
					<div class="banner_box">
						<a href="<?=$banner_info[0]['b_link']?>" target="<?=$banner_info[0]['b_target']?>" title="<?=$banner_info[0]['b_title']?>" class="bn"><img src="<?=IMG_DIR_BANNER.$banner_info[0]['b_img']?>" alt="<?=$banner_info[0]['b_title']?>" /></a>
					</div>
					<? } ?>
				</div>
				<? } ?>
			</div>
			<? } else { ?>
			<!-- 카테고리하나/해당 페이지일 때 depth_hit 추가-->
			<div class="depth1 special <?=$this_page?'depth_hit':''?>">
				<div class="first">
					<a href="<?=$sub_special_url?>" class="tit"><?=$sub_main_title?></a>
					<span class="sub_nav_bg"><span class="line left"></span><span class="line right"></span></span>
				</div>
			</div>
			<? } ?>
		<? } ?>
	</div>
</div>


<?
// 사이트 배너 margin-top
$side_margin_top = $_GET['pn'] == "main"||!$_GET['pn'] ? "294" : "294";
$latest_scroll_top = $side_margin_top;	// 이높이가 되면 스크롤을 따라 움직인다.
if(in_array($_GET['pn'],array('product.main'))) {
	$side_margin_top = "215"; $latest_scroll_top = "215";
}
if(in_array($_GET['pn'],array('product.list'))) {
	if($category_total_info['depth1_display'] == "지역") {
		$side_margin_top = "215"; $latest_scroll_top = "215";
	} else {
		$side_margin_top = "332"; $latest_scroll_top = "332";
	}
}
if(in_array($_GET['pn'],array('product.view'))) {
	$side_margin_top = "215"; $latest_scroll_top = "215";
}
if(in_array($_GET['pn'],array('product.todayclose'))) {
	$side_margin_top = "465"; $latest_scroll_top = "465";
}
if(in_array($_GET['pn'],array('product.promotion'))) {
	$side_margin_top = "215"; $latest_scroll_top = "215";
}
if( preg_match("/mypage./i",$pn) || preg_match("/service./i",$pn) || preg_match("/board./i",$pn) ) {
	$side_margin_top = "326"; $latest_scroll_top = "326";
}
if($_COOKIE['AuthPopupClose_topbig']=='Y' && ($_GET['pn']=='main' || !$_GET['pn'])) {
	$side_margin_top = $side_margin_top - 80; $latest_scroll_top = $latest_scroll_top - 80;
}

if( !preg_match("/member.login./i",$pn) && !preg_match("/member.join./i",$pn) && !preg_match("/member.find./i",$pn) ) {
	include "inc.fly_left.php"; // 좌측 사이드바
	include "inc.fly_right.php"; // 우측 사이드바
}
?>
<script>
// 좌우측 사이드바 동적 위치 설정
var is_local_category = <?=$category_total_info['depth1_display']=="지역"&&$pn!='product.main'?"true":"false"?>;
var is_search_list = <?=preg_match('/product.search./i',$_GET['pn'])?"true":"false"?>;
$(document).ready(function(){ reposition_fly_sidebars(); });
function reposition_fly_sidebars(){
	setTimeout(function(){
		var topBannerHeight = $('.top_banner').outerHeight(true,true)*1 + $('.header').outerHeight(true,true)*1 + $('.nav').outerHeight(true,true)*1 + $('.sub_tit').outerHeight(true,true)*1;
		if( is_local_category ) { topBannerHeight = topBannerHeight + $('.sub_top_area').outerHeight(true,true)*1 + 6; }
		if( is_search_list ) { topBannerHeight = topBannerHeight + $('.cm_comb_search .search_form').outerHeight(true,true)*1 - 29; }
		$('.fly_left, .fly_right').css({'marginTop':topBannerHeight + 70});
	},100);
}
</script>
<div id="hiddenCheckHeight">
