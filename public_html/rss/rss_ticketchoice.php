<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<products>";
echo "<fileversion>201105311741</fileversion>";
##DATA FORMAT
$dataForm = "
<product>

    <prod_id>{##PPID##}</prod_id>
    <name><![CDATA[{##PNAME##}]]></name>
    <url><![CDATA[{##LINK##}]]></url> 
    <descript><![CDATA[{##PMSG##}]]></descript> 
    <thumbnail_img><![CDATA[{##PIMG##}]]></thumbnail_img>
    <image><![CDATA[{##PIMG##}]]></image>
    <proddetail_img><![CDATA[{##PIMG##}]]></proddetail_img>
    <startdate><![CDATA[{##STT_DATETIME##}]]></startdate> 
    <enddate><![CDATA[{##END_DATETIME##}]]></enddate>
    <ticketstartdate></ticketstartdate>
    <ticketenddate></ticketenddate>
    <price><![CDATA[{##PRICEO##}]]></price> 
    <saleprice><![CDATA[{##PRICES##}]]></saleprice>
    <salerate><![CDATA[{##PRICER##}]]></salerate>
    <mincnt><![CDATA[{##CNTMIN##}]]></mincnt> 
    <maxcnt><![CDATA[{##CNTMAX##}]]></maxcnt> 
    <salecnt><![CDATA[{##CNTSALE##}]]></salecnt> 
    <shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
    <shop_tel><![CDATA[{##SUP_TEL##}]]></shop_tel>
    <shop_address><![CDATA[{##SUP_ADDRESS##}]]></shop_address>
    <region1><![CDATA[{##RSSAREA1##}]]></region1>
    <region2><![CDATA[{##RSSAREA2##}]]></region2>
    <category><![CDATA[{##CATEGORY##}]]></category>
    <lng><![CDATA[]]></lng>
    <lat><![CDATA[]]></lat>
    <mobileurl></mobileurl>
    <mobilesupportpayment>N</mobilesupportpayment> 
</product>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
?>