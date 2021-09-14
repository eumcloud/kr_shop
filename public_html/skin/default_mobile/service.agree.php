<?
$page_title = "이용약관";
include dirname(__FILE__)."/service.header.php";
?>
<div class="common_page">
<div class="common_inner common_full">

	<div class="editor">
		<?=stripslashes(($row_company['guideinfo_html_m']?$row_company['guideinfo_html_m']:$row_company['guideinfo_html']))?>
	</div>

</div><!-- .common_inner -->
</div><!-- .common_page -->