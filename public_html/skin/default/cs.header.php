<div class="common_page common_none">

	<!-- ●●●●●●●●●● 공통상단 -->
	<div class="cm_common_top">
		<div class="commom_page_title">
			<span class="icon_img"><img src="/pages/images/cm_images/icon_top_service.png" alt="" /></span>
			<dl>
				<dt><a href="/?pn=service.main">고객센터</a></dt>
				<dd>사이트를 이용하시는데 궁금한점이 있다면 언제나 문의 바랍니다.</dd>
			</dl>
		</div>
	</div>
	<!-- / 공통상단 -->

	
	<!-- ●●●●●●●●●● 페이지메뉴 -->
	<div class="cm_common_col_nav">
		<ul>
			<li><a href="/?pn=service.guide" class="tab <?=$_GET['pn'] == "service.guide"?"hit":""?>">이용안내</a></li>
			<li><a href="/?pn=service.partner.form" class="tab <?=$_GET['pn'] == "service.partner.form"?"hit":""?>">제휴/광고문의</a></li>
			<?
			$board_assoc = _MQ_assoc("select * from odtBbsInfo where bi_view = 'Y'");
			foreach($board_assoc as $board_key => $board_row) {
			?>	
			<li><a href="/?pn=board.list&_menu=<?=$board_row['bi_uid']?>" class="tab <?=$_GET['_menu'] == $board_row['bi_uid']?"hit":""?>"><?=$board_row['bi_name']?></a></li>
			<? } ?>
			<li><a href="/?pn=service.return.form" class="tab <?=$_GET['pn'] == "service.return.form"?"hit":""?>">교환/반품신청</a></li><!-- LMH008 -->
		</ul>
	</div>
	<!-- / 페이지메뉴 -->

</div>
