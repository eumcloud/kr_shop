<?php
header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<coupon_feed>";
echo "<fileversion>201103140041</fileversion>";
echo "<name><![CDATA[$company[homepage_title]]]></name>";
echo "<url><![CDATA[http://$_SERVER[HTTP_HOST]]]></url>";
echo "<logo_image />";
echo "<deals>";

##DATA FORMAT
$dataForm = "
<deal>
    <meta_id><![CDATA[{##PPID##}]]></meta_id>
    <title><![CDATA[{##PNAME##}]]></title>
    <description><![CDATA[{##PMSG##}]]></description>
    <start_at><![CDATA[{##STT_DATETIME##}]]></start_at>
    <end_at><![CDATA[{##END_DATETIME##}]]></end_at>
    <coupon_start_at><![CDATA[{##BEG_DATE##}]]></coupon_start_at>
    <coupon_end_at><![CDATA[{##EXP_DATE##}]]></coupon_end_at>
    <url><![CDATA[{##LINK##}]]></url>
    <mobile_url><![CDATA[]]></mobile_url>
    <original>{##PRICEO##}</original>
    <discount>{##PRICER##}</discount>
    <price>{##PRICES##}</price>
    <min_count>{##CNTMIN##}</min_count>
    <max_count>{##CNTMAX##}</max_count>
    <now_count>{##CNTSALE##}</now_count>
    <category><![CDATA[{##CATEGORY##}]]></category>
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
</deal>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</deals>";
echo "</coupon_feed>";
?>