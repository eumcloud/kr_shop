<div class="common_page common_none">

	<!-- ●●●●●●●●●● 타이틀상단 -->
	<div class="cm_common_top">
		<div class="commom_page_title">
			<span class="icon_img"><img src="/pages/images/cm_images/icon_top_my.png" alt="" /></span>
			<dl>
				<dt><a href="/?pn=mypage.main">마이페이지</a></dt>
				<dd>나의 쇼핑정보 및 사이트 이용정보를 관리할 수 있습니다.</dd>
			</dl>
		</div>

	</div>
	<!-- / 타이틀상단 -->


	<!-- ●●●●●●●●●● 페이지메뉴 -->
	<div class="cm_common_col_nav">
		<ul>
			<li><a href="/?pn=mypage.order.list" class="tab <?=$pn == "mypage.order.list" || $pn == "mypage.order.view"?"hit":""?>">주문내역</a></li>
			<li><a href="/?pn=mypage.wish.list" class="tab <?=$pn == "mypage.wish.list"?"hit":""?>">찜한상품</a></li>
			<li><a href="/?pn=mypage.action_point.list" class="tab <?=$pn == "mypage.action_point.list"?"hit":""?>">참여점수</a></li>
			<li><a href="/?pn=mypage.point.list" class="tab <?=$pn == "mypage.point.list"?"hit":""?>">적립금</a></li>
			<li><a href="/?pn=mypage.coupon.list" class="tab <?=$pn == "mypage.coupon.list"?"hit":""?>">쿠폰함</a></li>
			<li><a href="/?pn=mypage.request.list" class="tab <?=$pn == "mypage.request.list"?"hit":""?>">1:1상담내역</a></li>
			<li><a href="/?pn=mypage.request.form" class="tab <?=$pn == "mypage.request.form"?"hit":""?>">1:1온라인문의</a></li>
			<li><a href="/?pn=mypage.posting.list" class="tab <?=$pn == "mypage.posting.list"?"hit":""?>">상품문의내역</a></li>
			<li><a href="/?pn=mypage.return.list" class="tab <?=preg_match("/mypage.return./i",$pn)?"hit":""?>">교환/반품내역</a></li>
			<li><a href="/?pn=mypage.modify.form" class="tab <?=$pn == "mypage.modify.form"?"hit":""?>">정보수정</a></li>
			<li><a href="/?pn=mypage.leave.form" class="tab <?=$pn == "mypage.leave.form"?"hit":""?>">회원탈퇴</a></li>
		</ul>
	</div>
	<!-- / 페이지메뉴 -->

</div>