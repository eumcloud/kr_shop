<?
$side_margin_top = $side_margin_top ? $side_margin_top : "320";
?>
<!-- 왼쪽 배너 -->
<? $banner_info = info_banner("site_left",50,"data"); if(count($banner_info)>0) { ?>
<div class="fly_left" style="margin-top:<?=$side_margin_top?>px;">
	<div class="top">쇼핑몰 이용안내 <span class="arrow"></span></div>
	<div class="banner">
		<? foreach($banner_info as $k=>$v) { ?>
		<a href="<?=$v[b_link]?>" target="<?=$v[b_target]?>" class="bn">
			<img src="<?=IMG_DIR_BANNER.$v[b_img]?>" alt="<?=$v[b_title]?>" />
		</a>
		<? } ?>
	</div>
</div>
<? } ?>
