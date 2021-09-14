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
    <division>{##CATEGORY##}</division> 
    <region>{##RSSAREA1##}</region> 
    <name><![CDATA[{##PNAME##}]]></name>
    <image1>{##PIMG##}</image1> 
    <image2>{##PIMG##}</image2> 
    <image3>{##PIMG##}</image3> 
    <descript>{##PMSG##}</descript> 
    <shopname>{##SUP_NAME##}</shopname>
    <address>{##SUP_ADDRESS##}</address> 
    <price>{##PRICEO##}</price> 
    <saleprice>{##PRICES##}</saleprice> 
    <salerate>{##PRICER##}</salerate> 
    <fullcount>{##CNTMAX##}</fullcount> 
    <mincnt>{##CNTMIN##}</mincnt> 
    <maxcnt>{##BUYLIMIT##}</maxcnt> 
    <salecnt>{##CNTSALE##}</salecnt> 
    <limitdate>{##END_DATETIME##}</limitdate> 
    <lng></lng>
    <lat></lat>
</product>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>