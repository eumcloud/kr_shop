<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정

$basicsetup = getRow("SELECT * FROM ".$slntype."Setup WHERE serialnum='1'");
$uid = explode("-",$basicsetup[licenseNumber]);
$uid = "O".$uid[1];

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<products>";
echo "<fileversion>201103120037</fileversion>";
##DATA FORMAT
$dataForm = "
<product>
    <meta_id>$uid</meta_id>
    <name><![CDATA[[{##PNAME##}]]></name>
    <url><![CDATA[{##LINK##}]]></url>
    <kind><![CDATA[{##CATEGORY##}]]></kind>
    <region><![CDATA[{##RSSAREA1##}]]></region>
    <image><![CDATA[{##PIMG##}]]></image>
    <original>{##PRICEO##}</original>
    <price>{##PRICES##}</price>
    <min_count>{##CNTMIN##}</min_count>
    <max_count>{##CNTMAX##}</max_count>
    <now_count>{##CNTSALE##}</now_count>
    <sold>0</sold>
    <end_at>{##END_DATETIME##}</end_at>
    <addr>{##SUP_ADDRESS##}</addr>
    <lati></lati>
    <long></long>
    <blog></blog>
</product>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>