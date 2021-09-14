<!-- 왼쪽에서나오는슬라이드 사이트맵 -->
<div class="slide_bg" style="display:none;"><a href="#none" onclick="return false;" class="close_slide btn_close"><img src="/m/images/slide_close.png" alt="닫기" /></a></div>
<div class="slide close_slide slide_sitemap" style="display:none">
	<div class="wrap_box">

		<div class="top_my">

			<? if(is_login()) { ?>
			<!-- 로그인 후 -->
			<div class="top after">
				<!-- 마이페이지 -->
				<a href="/m/?pn=mypage.main" class="btn_mypage">Mypage</a>
				<!-- 찜상품 -->
				<a href="/m/?pn=mypage.wish.list" class="wish">
					<span class="ic_wish"><img src="/m/images/ic_wish.png" alt="찜상품" /></span>
					<span class="wish_cnt"><?=number_format(_MQ_result("select count(*) from odtProductWish where pw_inid = '".get_userid()."'"))?></span>
				</a>
				<!-- 로그아웃 -->
				<a href="/m/member.login.pro.php?_mode=logout" class="bnt_logout">Logout</a>
			</div>

			<!-- 내 쇼핑정보 -->
			<div class="infobox">
				<!-- 로그인 후 -->
				<div class="myinfo">
					<a href="/m/?pn=mypage.order.list" title="진행주문">
						<span class="txt">진행주문</span>
						<span class="my"><strong><?=number_format(get_order_status_cnt('결제대기')+get_order_status_cnt('결제확인')+get_order_status_cnt('발송대기'))?></strong>건</span>
					</a>
					<a href="/m/?pn=mypage.point.list" title="나의 포인트">
						<span class="txt">나의포인트</span>
						<span class="my"><strong><?=number_format($row_member['point'])?></strong>원</span>
					</a>
					<? $is_usecoupon = _MQ("select count(*) as cnt from odtCoupon where coID ='".get_userid()."' and coUse='N'"); ?>
					<a href="/m/?pn=mypage.coupon.list" title="나의 쿠폰">
						<span class="txt">나의쿠폰</span>
						<span class="my"><strong><?=number_format($is_usecoupon['cnt'])?></strong>장</span>
					</a>
				</div>
			</div>
			<!-- 로그인 후 끝 -->
			<? } else { ?>
			<!-- 로그인 전 -->
			<div class="top before">
				<span class="lineup">
					<? if(!$path && $pn<>"member.login.form"){ $path = enc("e",$_SERVER['QUERY_STRING']); } ?>
					<a href="/m/?pn=member.login.form&path=<?=$path?>" title="로그인">Login</a>
					<a href="/m/?pn=member.join.agree" title="회원가입">Join</a>
				</span>
			</div>
			<!-- 내 쇼핑정보 -->
			<div class="infobox"><span class="before_txt">로그인을 하시면 쇼핑정보를 볼 수 있습니다</span></div>
			<!-- 로그인 전 끝 -->
			<? } ?>
		</div>

		<!-- 전체카테고리 -->
		<div class="slide_menu">
		<?
		$top_menu_assoc = _MQ_assoc("select * from odtCategory where catedepth=1 and cHidden='no' order by cateidx asc limit 5");
		foreach($top_menu_assoc as $top_menu_key => $top_menu_row) {
			/* 1차 카테고리 링크값 구하기 */
			$special_page = false;
			if($top_menu_row['subcate_display'] == "지역") { // 지역일 경우에만 sub_cuid 추가
				$sub_cuid = reset(explode(",",$top_menu_row['lineup'])); // 지역 sub_cuid (묶음탭)
				//$sub_row = _MQ("select catecode,subcate_main from odtCategory where cHidden='no' and subcate_display_choice = '".$sub_cuid."' order by subcate_main='Y' desc, cateidx asc limit 1"); // sub 메뉴에서 첫번째로 보여질 카테고리
				// 2020-07-09 SSJ :: 1차 카테고리 제한 추가
				$sub_row = _MQ("select catecode,subcate_main from odtCategory where cHidden='no' and subcate_display_choice = '".$sub_cuid."' and parent_catecode = '". $top_menu_row['catecode'] ."' order by subcate_main='Y' desc, cateidx asc limit 1"); // sub 메뉴에서 첫번째로 보여질 카테고리
				$sub_main_url = "/m/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&sub_cuid=$sub_cuid&cuid=".$sub_row['catecode']; // 링크값
				$sub_main_url = ($sub_row['catecode'] ? $sub_main_url : "javascript:alert('첫 카테고리 연결 미지정')"); // 링크값
			} else if($top_menu_row['subcate_display'] == "기획전") { // 기획전은 메인없이 바로 list 페이지로 이동
				$sub_row = _MQ("select catecode from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by cateidx asc limit 1"); // sub 메뉴에서 첫번째로 보여질 카테고리
				$sub_special_url = "/m/?pn=product.promotion&cuid=".$sub_row['catecode']; // 링크값
				$special_page = true;
			} else {
				$sub_row = _MQ("select catecode,subcate_main from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by subcate_main='Y' desc, cateidx asc limit 1");
				$sub_main_url = "/m/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&cuid=".$sub_row['catecode']; // 링크값
			}
			$sub_main_title = $top_menu_row['catename']; // 1차 카테고리명

			/* 2차 카테고리 메뉴 생성 */
			unset($sub_category_html); $sub_category_html = "<div class='sub sub_category' style='display:none;' id='sub_".$top_menu_row['catecode']."'>";
			if($top_menu_row['subcate_display'] == "지역") { // 지역 스타일은 카테고리 정보의 lineup 출력
				$sub_assoc = explode(",",$top_menu_row['lineup']); // 묶음탭
				foreach($sub_assoc as $sub_key => $sub_val) {
					$sub_row = _MQ("select catecode,subcate_main from odtCategory where cHidden='no' and subcate_display_choice = '".$sub_val."' and find_in_set('".$top_menu_row['catecode']."',parent_catecode) > 0 order by subcate_main='Y' desc, cateidx asc limit 1");
					$sub_url = "/m/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&sub_cuid=".$sub_val."&cuid=".$sub_row['catecode']."";
					$sub_category_html .= "<a href='".($sub_row['catecode'] ? $sub_url : "javascript:alert(\"카테고리 미지정\")")."'>".$sub_val."</a>";
				}
			} else if($top_menu_row['subcate_display'] == "기획전") {
				$sub_assoc = _MQ_assoc("select catename,catecode,cateimg,subcate_main from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) > 0 and catedepth=2 and cHidden='no' order by cateidx asc"); // 상품 리스트의 탭을 위하여 2차 카테고리 추출
				foreach($sub_assoc as $sub_key => $sub_row) {
					$sub_url = "/m/?pn=product.promotion&cuid=".$sub_row['catecode']; // 첫번째 카테고리는 메인으로 이동
					$sub_category_html .= "<a href='".$sub_url."'>".$sub_row['catename']."</a>";
				}
			} else { // 쇼핑 스타일은 2차 카테고리 출력
				$sub_assoc = _MQ_assoc("select catename,catecode,cateimg,subcate_main from odtCategory where find_in_set(".$top_menu_row['catecode'].",parent_catecode) > 0 and catedepth=2 and cHidden='no' order by cateidx asc"); // 상품 리스트의 탭을 위하여 2차 카테고리 추출
				foreach($sub_assoc as $sub_key => $sub_row) {
					$sub_url = "/m/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&cuid=".$sub_row['catecode']; // 첫번째 카테고리는 메인으로 이동
					$sub_category_html .= "<a href='".$sub_url."'>".$sub_row['catename']."</a>";
				}
			} $sub_category_html .= "</div>";

			$this_page = ($category_total_info['depth1_catecode'] == $top_menu_row['catecode']) ? true : false;
		?>
			<a href="#none" onclick="return false;" class="sub_toggle" data-catecode="<?=$top_menu_row['catecode']?>"><?=$sub_main_title?><span class="s_close"></span></a>
			<?=$sub_category_html?>
		<? } ?>
		<a href="#none" onclick="return false;" class="sub_toggle" data-catecode="service">고객센터<span class="s_close"></span></a>
		<div class="sub sub_category" style="display:none;" id="sub_service">
			<a href="/m/?pn=service.main">고객센터 메인</a>
			<? if($row_setup['none_member_buy'] == 'Y' && !is_login()) { ?><a href="/m/?pn=service.guest.order.list">비회원주문조회</a><? } ?>
			<a href="/m/?pn=service.guide">이용안내</a>
			<a href="/m/?pn=service.partner.form">제휴/광고문의</a>
			<?
			$board_assoc = _MQ_assoc("select * from odtBbsInfo where bi_view = 'Y'");
			foreach($board_assoc as $board_key => $board_row) {
			?>
			<a href="/m/?pn=board.list&_menu=<?=$board_row['bi_uid']?>"><?=$board_row['bi_name']?></a>
			<? } ?>
			<a href="/m/?pn=service.return.form">교환/반품신청</a>
		</div>
		</div><!-- .slide_menu -->
		<div style="height:60px;"></div>
	</div><!-- .wrap_box -->
</div><!-- .slide_sitemap -->
<!-- / 왼쪽에서나오는슬라이드 사이트맵 -->


<script src="/m/js/jquery.scrollTo.js"></script>
<script>
var slide_speed = 500, scrolltop = $(window).scrollTop();
$(document).ready(function(){

	$('.sub_toggle').on('click',function(){
		$this = $(this);
		var catecode = $this.data('catecode');
		if( $('#sub_'+catecode).is(':visible') ) {
			$this.find('.s_open').removeClass('s_open').addClass('s_close');
			$('#sub_'+catecode).hide(); $('.slide .wrap_box').scrollTo('body',300);
		} else {
			$this.find('.s_close').removeClass('s_close').addClass('s_open');
			$('#sub_'+catecode).show(); $('.slide .wrap_box').scrollTo($this,300);
		}
	});

	resize_slide();
	$('.slide').css({ 'left':'-100%' }).hide();
	$('.open_slide').on('click',function(){ scrolltop = $(window).scrollTop();
		$('body, html').css({'overflow':'hidden','height':window.innerHeight,'position':'fixed','width':window.innerWidth}).scrollTop(scrolltop);
		$('.slide_bg').fadeIn(slide_speed,'easeInOutCubic'); upper_footer_toggle('open');
		$('.slide').show().animate({ 'left':'0' },slide_speed,'easeInOutCubic');
	});
	$('.close_slide').on('click',function(e){ e.stopPropagation();
		$('.slide').animate({ 'left':'-100%' },slide_speed,'easeInOutCubic',function(){ $(this).hide(); });
		$('.slide_bg').fadeOut(slide_speed,'easeInOutCubic'); upper_footer_toggle('close');
		$('body, html').attr('style','');
		$(window).scrollTop(scrolltop);
	});
	$('.close_slide .wrap_box').on('click',function(e){ e.stopPropagation(); });
});
$(window).on('resize',function(){ setTimeout(resize_slide(),50); });
$(window).on('scroll',function(){ upper_footer_toggle(); });
$(window).bind( 'orientationchange', function(e){ setTimeout(resize_slide(),50); });

function resize_slide(){
	$('.slide, .slide_bg').height( window.innerHeight );
	if( $('.slide').is(':visible') ) { $('body, html').css({'height':window.innerHeight,'width':window.innerWidth}); }
	upper_footer_toggle();
}
function upper_footer_toggle(mode){
	if(mode=='open') { $('.upper_footer').hide(); }
	else if(mode=='close') { $('.upper_footer').show(); }
	else {
		if( $(window).height() < 400 && $(window).width() > $(window).height() ) { $('.upper_footer').hide(); }
		else {
			var st = $(window).scrollTop();
			if( st < 50 ) { $('.upper_footer').hide(); } else { $('.upper_footer').show(); }
		}
	}
}
</script>