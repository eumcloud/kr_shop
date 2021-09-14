<?php
header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<olcoo_sync>";
echo "<fileversion>201103120037</fileversion>";
echo "<doc_ver>3</doc_ver>";
echo "<name><![CDATA[$company[homepage_title]]]></name>";
echo "<url><![CDATA[http://$_SERVER[HTTP_HOST]]]></url>";
echo "<deals>";

##DATA FORMAT
$dataForm = "
<deal>
    <sync_id><![CDATA[{##PPID##}]]></sync_id>
    <start_at><![CDATA[{##STT_DATETIME##}]]></start_at>
    <end_at><![CDATA[{##END_DATETIME##}]]></end_at>
    <deal_end_at><![CDATA[{##EXP_DATE##}]]></deal_end_at>
    <url><![CDATA[{##LINK##}]]></url>
    <mobile_url><![CDATA[]]></mobile_url>
    <price>{##PRICEO##}</price> 
    <max_count><![CDATA[{##CNTMAX##}]]></max_count>
    <sale_count><![CDATA[{##CNTSALE##}]]></sale_count>
    <off_price>{##PRICES##}</off_price> 
    <title><![CDATA[{##PNAME##}]]></title>
    <subtitle><![CDATA[]]></subtitle>
    <seller><![CDATA[{##SUP_NAME##}]]></seller>
    <seller_add><![CDATA[{##SUP_ADDRESS##}]]></seller_add>
    <area><![CDATA[{##RSSAREA1##}]]></area>
    <area_desc><![CDATA[{##RSSAREA2##}]]></area_desc>
    <category><![CDATA[{##CATEGORY##}]]></category>
    <images>
        <image><![CDATA[{##PIMG##}]]></image>
    </images>
</deal>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</deals>";
echo "</olcoo_sync>";
?>