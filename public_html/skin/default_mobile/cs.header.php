<!-- ●●●●● 일반및 공통페이지 레이아웃 상단 -->
<div class="common_pages_top">
	
	<!-- 서브메뉴 열리면 클래스값  -->
	<div class="this_page_name ">
		<? if($pn!='service.main') { ?>
		<a href="/m/?pn=<?=in_array($pn,array('board.view','board.form'))?'board.list&_menu='.$_menu:'service.main'?>" class="btn_back" title="뒤로"><span class="shape"></span></a>
		<? } ?>
		<a href="#none" onclick="return false;" class="open_toggle btn_openmenu" title="메뉴열기"><span class="shape"></span></a>
		<span class="txt"><?=$page_title?></span>

		<!-- 클릭하면 나오는 해당페이지의 메뉴 -->
		<div class="open_menu">
			<ul>
				<li><a href="/m/?pn=service.guide" class="menu <?=$_GET['pn'] == "service.guide"?"hit":""?>">이용안내</a></li>
				<li><a href="/m/?pn=service.partner.form" class="menu <?=$_GET['pn'] == "service.partner.form"?"hit":""?>">제휴/광고문의</a></li>
				<?
				$board_assoc = _MQ_assoc("select * from odtBbsInfo where bi_view = 'Y'");
				foreach($board_assoc as $board_key => $board_row) {
				?>	
				<li><a href="/m/?pn=board.list&_menu=<?=$board_row['bi_uid']?>" class="menu <?=$_GET['_menu'] == $board_row['bi_uid']?"hit":""?>"><?=$board_row['bi_name']?></a></li>
				<? } ?>
				<li><a href="/m/?pn=service.return.form" class="menu <?=$_GET['pn'] == "service.return.form"?"hit":""?>">교환/반품신청</a></li><!-- LMH008 -->
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