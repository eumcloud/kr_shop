<!-- ●●●●● 일반및 공통페이지 레이아웃 상단 -->
<div class="common_pages_top">
	
	<!-- 서브메뉴 열리면 클래스값  -->
	<div class="this_page_name ">
		<!-- <a href="" class="btn_back" title="뒤로"><span class="shape"></span></a> -->
		<a href="#none" onclick="return false;" class="open_toggle btn_openmenu" title="메뉴열기"><span class="shape"></span></a>
		<span class="txt"><?=$page_title?></span>

		<!-- 클릭하면 나오는 해당페이지의 메뉴 -->
		<div class="open_menu">
			<ul>
				<!-- 해당메뉴일때 hit -->
				<li><a class="menu <?=$pn=='shop.cart.list'?'hit':''?>" href="/m/?pn=shop.cart.list">장바구니<?if($cart_cnt > 0) {?><span class="state">(<?=$cart_cnt?>)</span><?}?></a></li>
				<li><a class="menu <?=preg_match('/shop.order./i',$pn)?'hit':''?>" href="/m/?pn=mypage.order.list">주문결제</a></li>
				<? if($row_setup['none_member_buy'] == 'Y' && !is_login()) { ?><li><a class="menu <?=$pn=='service.guest.order.list'?'hit':''?>" href="/m/?pn=service.guest.order.list">비회원주문조회</a></li><? } ?>
			</ul>
		</div>
	</div>
	
</div>
<!-- / 일반및 공통페이지 레이아웃 상단 -->
<script>
$(document).ready(function(){
	$('.open_toggle').on('click',function(){
		$('.this_page_name').toggleClass('if_open_menu');
		$('.open_menu').slideToggle('fast','easeInOutCubic');
	});
});
</script>