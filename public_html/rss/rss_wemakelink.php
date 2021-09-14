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

    <code><![CDATA[{##PPID##}]]></code>
    <url><![CDATA[{##LINK##}]]></url>
    <mobile_url></mobile_url>
    <region>{##RSSAREA1##}</region>
    <region2>{##RSSAREA2##}</region2>
    <image><![CDATA[{##PIMG##}]]></image>
    <title><![CDATA[{##PNAME##}]]></title>
    <descript><![CDATA[{##PMSG##}]]></descript>
    <category>{##CATEGORY##}</category>
    <price>{##PRICEO##}</price>
    <sale_price>{##PRICES##}</sale_price>
    <sale_rate>{##PRICER##}</sale_rate>
    <min_cnt>{##CNTMIN##}</min_cnt>
    <max_cnt>{##CNTMAX##}</max_cnt>
    <now_cnt>{##CNTSALE##}</now_cnt>
    <shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
    <shop_tel>{##SUP_TEL##}</shop_tel>
    <shop_addr>{##SUP_ADDRESS##}</shop_addr>
    <blog_keyword></blog_keyword>
    <lat></lat>
    <lng></lng>
    <start_date>{##STT_DATETIME##}</start_date>
    <limit_date>{##END_DATETIME##}</limit_date>
    <coupon_start>{##BEG_DATE2##}</coupon_start>
    <coupon_limit>{##EXP_DATE2##}</coupon_limit>

</item>";


##FORLOOP
RunLoop($dataForm);

##END
echo "</items>";
?>