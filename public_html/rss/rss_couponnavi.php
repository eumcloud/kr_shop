<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<coupon_feed>";
echo "<fileversion>201112301520</fileversion>";


echo "<company>";
echo "<title><![CDATA[$comName]]></title>";
echo "<link><![CDATA[$comHome]]></link>";
echo "<logo><![CDATA[$comHome/pages/skin/V2/image_shane/th_bottom_logo.png]]></logo>";
echo "<tel><![CDATA[[$comTel]]></tel>";
echo "</company>";

##DATA FORMAT
$dataForm = "
<item>

<idx><![CDATA[{##CATEGORY##}]]></idx>
<title><![CDATA[{##PNAME##}]]></title>
<link><![CDATA[{##LINK##}]]></link>
<time_start>{##STT_DATETIME##}</time_start>
<time_end>{##END_DATETIME##}</time_end>
<price_original>{##PRICEO##}</price_original>
<price_now>{##PRICES##}</price_now>
<sell_count>{##CNTSALE##}</sell_count>
<count_min>{##CNTMAX##}</count_min>
<count_max>{##BUYLIMIT##}</count_max>
<photo1><![CDATA[{##PIMG##}]]></photo1>
<photo2></photo2>
<area><![CDATA[[{##RSSAREA1##}]]></area>
<category><![CDATA[[{##CATEGORY##}]]></category>
<shop><![CDATA[{##SUP_NAME##}]]></shop>
<tel><![CDATA[{##SUP_TEL##}]]></tel>
<addr><![CDATA[{##SUP_ADDRESS##}]]></addr>
<desc_text><![CDATA[{##PMSG##}]]></desc_text>
<mobile_link><![CDATA[{##MLINK##}]]></mobile_link>
<mobile_paysupport>N</mobile_paysupport>

</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</coupon_feed>";
?>