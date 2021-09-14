<?php
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<items>";
echo "<fileversion>201103221533</fileversion>";
##DATA FORMAT
$dataForm = "
<item>
    <url><![CDATA[{##LINK##}]]></url>
    <region>{##RSSAREA1##}</region>
    <category>{##CATEGORY##}</category>
    <image>{##PIMG##}</image>
    <title><![CDATA[[{##RSSAREA1##}]{##PNAME##} \"{##SUP_NAME##}\"]]></title>
    <descript>\"{##SUP_NAME##}\" {##PMSG##}</descript>
    <price>{##PRICEO##}</price>	
    <sale_price>{##PRICES##}</sale_price>
    <sale_rate>{##PRICER##}</sale_rate>
    <min_cnt>{##CNTMIN##}</min_cnt>
    <max_cnt>{##CNTMAX##}</max_cnt>
    <now_cnt>{##CNTSALE##}</now_cnt>
    <address>{##SUP_ADDRESS##}</address>
    <tel>{##SUP_TEL1##}</tel>
    <start_date>{##STT_DATETIME##}</start_date>
    <limit_date>{##END_DATETIME##}</limit_date>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</items>";
?>