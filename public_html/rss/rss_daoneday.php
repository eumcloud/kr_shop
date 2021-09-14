<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<products>";
echo "<fileversion>201103120037</fileversion>";
##DATA FORMAT
$dataForm = "
<product>
    <url><![CDATA[{##LINK##}]]></url>
    <logourl>{##LOGO##}</logourl> 
    <sitename><![CDATA[{##TITLE##}]]></sitename>
    <division><![CDATA[{##CATEGORY##}]]></division>
    <region><![CDATA[{##RSSAREA1##}]]></region> 
    <pid>{##PID##}</pid> 
    <name><![CDATA[{##PNAME##}]]></name>
    <image1><![CDATA[{##PIMG##}]]></image1>
    <description><![CDATA[{##PMSG##}]]></description>
    <address>{##SUP_ADDRESS##}</address> 
    <price>{##PRICEO##}</price> 
    <saleprice>{##PRICES##}</saleprice> 
    <salerate>{##PRICER##}</salerate> 
    <fullcount>{##CNTMAX##}</fullcount> 
    <mincnt>{##CNTMIN##}</mincnt> 
    <maxcnt>{##CNTMAX##}</maxcnt> 
    <salecnt>{##CNTSALE##}</salecnt> 
    <startdate>{##STT_DATETIME##}</startdate> 
    <limitdate>{##END_DATETIME##}</limitdate> 
    <lng /> 
    <lat /> 
</product>";


##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>