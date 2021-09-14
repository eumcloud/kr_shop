<?PHP
	include "./inc.php";
?>
<SCRIPT src="/include/js/jquery-1.7.1.min.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
	$(document).ready(function() {
		opener.$("input[name=_link]").val('/?pn=product.view&pcode=<?=$Prop_code?>');
		close();
	});
</SCRIPT>