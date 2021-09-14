<?php
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<result>";
echo "<code>0000</code>";
echo "<msg></msg>";
echo "<prods>";

##DATA FORMAT
$dataForm = "
    <prod>
        <prodid><![CDATA[{##PIDN##}]]></prodid>
        <salecnt>{##CNTSALE##}</salecnt>       
    </prod>
";


##FORLOOP
RunLoop($dataForm);

##END
echo "</prods>";
echo "</result>";
?>