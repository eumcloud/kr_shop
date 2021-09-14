<!-- ●●●●● 일반및 공통페이지 레이아웃 상단 -->
<div class="common_pages_top">
	
	<!-- 서브메뉴 열리면 클래스값  -->
	<div class="this_page_name ">
		<? if($pn!='mypage.main') { ?>
		<a href="/m/?pn=<?=$pn=='mypage.order.view'?'mypage.order.list':'mypage.main'?>" class="btn_back" title="뒤로"><span class="shape"></span></a>
		<? } ?>
		<a href="#none" onclick="return false;" class="open_toggle btn_openmenu" title="메뉴열기"><span class="shape"></span></a>
		<span class="txt"><?=$page_title?></span>

		<!-- 클릭하면 나오는 해당페이지의 메뉴 -->
		<div class="open_menu">
			<ul>
				<li><a href="/m/?pn=mypage.order.list" class="menu <?=$pn == "mypage.order.list" || $pn == "mypage.order.view"?"hit":""?>">주문내역</a></li>
				<li><a href="/m/?pn=mypage.wish.list" class="menu <?=$pn == "mypage.wish.list"?"hit":""?>">찜한상품</a></li>
				<li><a href="/m/?pn=mypage.action_point.list" class="menu <?=$pn == "mypage.action_point.list"?"hit":""?>">참여점수</a></li>
				<li><a href="/m/?pn=mypage.point.list" class="menu <?=$pn == "mypage.point.list"?"hit":""?>">적립금</a></li>
				<li><a href="/m/?pn=mypage.coupon.list" class="menu <?=$pn == "mypage.coupon.list"?"hit":""?>">쿠폰함</a></li>
				<li><a href="/m/?pn=mypage.request.list" class="menu <?=$pn == "mypage.request.list"?"hit":""?>">1:1상담내역</a></li>
				<li><a href="/m/?pn=mypage.request.form" class="menu <?=$pn == "mypage.request.form"?"hit":""?>">1:1온라인문의</a></li>
				<li><a href="/m/?pn=mypage.posting.list" class="menu <?=$pn == "mypage.posting.list"?"hit":""?>">상품문의내역</a></li>
				<li><a href="/m/?pn=mypage.return.list" class="menu <?=preg_match("/mypage.return./i",$pn)?"hit":""?>">교환/반품내역</a></li>
				<li><a href="/m/?pn=mypage.modify.form" class="menu <?=$pn == "mypage.modify.form"?"hit":""?>">정보수정</a></li>
				<li><a href="/m/?pn=mypage.leave.form" class="menu <?=$pn == "mypage.leave.form"?"hit":""?>">회원탈퇴</a></li>
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