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
				<?if(is_login()) {?>
				<li><a class="menu" href="/m/member.login.pro.php?_mode=logout">로그아웃</a></li>
				<li><a class="menu <?=$pn=='mypage.modify.form'?'hit':''?>" href="/m/?pn=mypage.modify.form">정보수정</a></li>
				<?}else{?>
				<li><a class="menu <?=$pn=='member.login.form'?'hit':''?>" href="/m/?pn=member.login.form">로그인</a></li>
				<li><a class="menu <?=$pn=='member.find.id.form'?'hit':''?>" href="/m/?pn=member.find.id.form">아이디찾기</a></li>
				<li><a class="menu <?=$pn=='member.find.pw.form'?'hit':''?>" href="/m/?pn=member.find.pw.form">비밀번호찾기</a></li>
				<li><a class="menu <?=preg_match('/member.join./i',$pn)?'hit':''?>" href="/m/?pn=member.join.agree">회원가입</a></li>
				<?}?>
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