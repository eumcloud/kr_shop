<?php
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<coupon_feed>";
echo "<doc_ver>1</doc_ver>";
echo "<fileversion>20110801</fileversion>";
echo "<name>$company[homepage_title]</name>";
echo "<link>http://$_SERVER[HTTP_HOST]</link>";
echo "<logo_image></logo_image>";
##DATA FORMAT
$dataForm = "
<deals>
    <deal>        
        <meta_id><![CDATA[{##PID##}]]></meta_id>
        <start_at>{##STT_DATETIME##}</start_at>
        <end_at>{##END_DATETIME##}</end_at>
        <title><![CDATA[{##PNAME##}]]></title>
        <description><![CDATA[{##PMSG##}]]></description>
        <url><![CDATA[{##LINK##}]]></url>
        <support_mobile_transaction>Y</support_mobile_transaction>
        <original>{##PRICEO##}</original>
        <discount>{##PRICER##}</discount>
	    <price>{##PRICES##}</price>
        <max_count>{##CNTMAX##}</max_count>
        <min_count>{##CNTMIN##}</min_count>
        <now_count>{##CNTSALE##}</now_count>
        <status>진행중</status>
        <shops>
            <shop>
                <shop_name><![CDATA[{##SUP_NAME##}]]></shop_name>
                <shop_tel><![CDATA[{##SUP_TEL##}]]></shop_tel>
                <shop_address>{##SUP_ADDRESS##}</shop_address>
            </shop>
        </shops>
        <images>
            <image><![CDATA[{##PIMG##}]]></image>
        </images>
    </deal>
</deals>";


##FORLOOP
RunLoop($dataForm);

##END
echo "</coupon_feed>";
?>