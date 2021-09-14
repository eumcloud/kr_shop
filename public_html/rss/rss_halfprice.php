<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정

echo "<?xml version=\"1.0\" encoding=\"utf8\" ?>";
echo "<coupon_feed>";
echo "<doc_ver>1</doc_ver>";
echo "<fileversion>201103120037</fileversion>";
echo "<name><![CDATA[$company[homepage_title]]]></name>";
echo "<url><![CDATA[http://$_SERVER[HTTP_HOST]]]></url>";
echo "<logo_image />";
echo "<deals>";

##DATA FORMAT
$dataForm = "
<deal>
    <meta_id><![CDATA[{##PID##}]]></meta_id>
    <title><![CDATA[{##PNAME##}]]></title>
    <description><![CDATA[{##PMSG##}]]></description>
    <original>{##PRICEO##}</original>
    <price>{##PRICES##}</price>
    <discount>{##PRICER##}</discount>
    <min_count>{##CNTMIN##}</min_count>
    <max_count>{##CNTMAX##}</max_count>
    <now_count>{##CNTSALE##}</now_count>
    <end_at><![CDATA[{##END_DATETIME##}]]></end_at>
    <image><![CDATA[{##PIMG##}]]></image>
    <url><![CDATA[{##LINK##}]]></url>
</deal>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</deals>";
echo "</coupon_feed>";
?>