<?
$pageid = $_GET['pageid'];

$page_row = _MQ("select * from odtNormalPage where np_id ='".$pageid."'");

// 노출 비공개 페이지인지 체크
if($page_row['np_view'] == "N" && !is_admin()) error_msg("비공개 페이지 입니다.");

include dirname(__FILE__)."/service.header.php";
?>
<div class="common_page">

	<div class="editor">
		<?=stripslashes($page_row['np_content'])?>
	</div>

</div>	