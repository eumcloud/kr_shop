<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<coupon_feed>";
echo "<fileversion>201103120037</fileversion>";
echo "<doc_ver>1</doc_ver>";
echo "<name><![CDATA[{##COMNAME##}]]></name>";
echo "<url><![CDATA[{##HOMEPAGE##}]]></url>";
echo "<logo_image><![CDATA[{##LOGO##}]]></logo_image>";
echo "<deals>";
##DATA FORMAT
$dataForm = "
<deal>
    <meta_id><![CDATA[{##LINK##}]]></meta_id>
    <start_at>{##STT_DATETIME##}</start_at>
    <end_at>{##END_DATETIME##}</end_at>
    <coupon_start_at>{##BEG_DATETIME##}</coupon_start_at>
    <coupon_end_at>{##EXP_DATETIME##}</coupon_end_at>
    <title><![CDATA[{##PNAME##}]]></title>
    <description><![CDATA[{##DETAIL##}]]></description>

    <url><![CDATA[{##LINK##}]]></url>
    <mobile_url><![CDATA[{##MLINK##}]]></mobile_url>

    <support_mobile_transaction>Y</support_mobile_transaction>
    <original>{##PRICEO##}</original>
    <discount>{##PRICER##}</discount>
    <price>{##PRICES##}</price>
    <max_count>{##CNTMIN##}</max_count>
    <min_count>0</min_count>

    <now_count>{##CNTSALE##}</now_count>
    <shops>
        <shop>
            <shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
            <shop_tel><![CDATA[{##SUP_TEL##}]]></shop_tel>
            <shop_address><![CDATA[{##SUP_ADDRESS##}]]></shop_address>
        </shop>
    </shops>
    <images>
        <image><![CDATA[{##PIMG##}]]></image>
    </images>
</deal>
";

##FORLOOP
RunLoop($dataForm);

##END
echo "</deals>";
echo "</coupon_feed>";
?>