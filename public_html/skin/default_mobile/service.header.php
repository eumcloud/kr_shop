<? $assoc = _MQ_assoc("select * from odtNormalPage where np_view ='Y' order by np_idx asc"); ?>
<!-- ●●●●● 일반및 공통페이지 레이아웃 상단 -->
<div class="common_pages_top">
	
	<!-- 서브메뉴 열리면 클래스값  -->
	<div class="this_page_name ">
		<a href="#none" onclick="history.go(-1);return false;" class="btn_back" title="뒤로"><span class="shape"></span></a>
		<a href="#none" onclick="return false;" class="open_toggle btn_openmenu" title="메뉴열기"><span class="shape"></span></a>
		<span class="txt"><?=$page_title?></span>

		<!-- 클릭하면 나오는 해당페이지의 메뉴 -->
		<div class="open_menu">
			<ul>
				<? foreach($assoc as $key => $row) { ?>
				<li><a href="/?pn=service.page.view&pageid=<?=$row['np_id']?>" class="menu <?=$_GET['pageid']==$row['np_id']?"hit":""?>"><?=$row['np_title']?></a></li>
				<? } ?>
				<li><a href="/?pn=service.agree" class="menu <?=$pn=='service.agree'?"hit":""?>">이용약관</a></li>
				<li><a href="/?pn=service.privacy" class="menu <?=$pn=='service.privacy'?"hit":""?>">개인정보처리방침</a></li>
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