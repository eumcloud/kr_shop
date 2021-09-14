<?
$page_title = "개인정보처리방침";
include dirname(__FILE__)."/service.header.php";
?>
<div class="common_page">
<div class="common_inner common_full">

	<div class="editor">
		<?=stripslashes(($row_company['privacyinfo_html_m']?$row_company['privacyinfo_html_m']:$row_company['privacyinfo_html']))?>
	</div>

</div><!-- .common_inner -->
</div><!-- .common_page -->