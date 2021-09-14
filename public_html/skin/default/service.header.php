<? $assoc = _MQ_assoc("select * from odtNormalPage where np_view ='Y' order by np_idx asc"); ?>
<div class="common_page common_none">

	<!-- ●●●●●●●●●● 타이틀상단 -->
	<div class="cm_common_top">
		<div class="commom_page_title">
			<span class="icon_img"><img src="/pages/images/cm_images/icon_top_company.png" alt="" /></span>
			<dl>
				<dt><a href="/?pn=service.page.view&pageid=<?=$assoc[0]['np_id']?>">회사소개</a></dt>
				<dd>저희 쇼핑몰을 방문해 주셔서 진심으로 감사드립니다.</dd>
			</dl>
		</div>
	</div>
	<!-- / 타이틀상단 -->

	<!-- ●●●●●●●●●● 페이지메뉴 -->
	<div class="cm_common_col_nav">
		<ul>
			<? foreach($assoc as $key => $row) { ?>
			<li><a href="/?pn=service.page.view&pageid=<?=$row['np_id']?>" class="tab <?=$_GET['pageid']==$row['np_id']?"hit":""?>"><?=$row['np_title']?></a></li>
			<? } ?>
			<li><a href="/?pn=service.agree" class="tab <?=$pn=='service.agree'?'hit':''?>">이용약관</a></li>
			<li><a href="/?pn=service.privacy" class="tab <?=$pn=='service.privacy'?'hit':''?>">개인정보처리방침</a></li>
		</ul>
	</div>
	<!-- / 페이지메뉴 -->

</div>