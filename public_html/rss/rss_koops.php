<?php

header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");
include_once("../include/inc.php");

##START
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<channel>";
echo "<fileversion>201201111310</fileversion>";
echo "<siteinfo>";
echo "<name><![CDATA[$comName]]></name>";
echo "<url><![CDATA[$comHome]]></url>";
echo "<logo><![CDATA[$comHome/pages/skin/".$row_setup[P_SKIN]."/img/top_logo.png]]></logo>";
echo "</siteinfo>";

##DATA FORMAT
$dataForm = "
<item>

<idx><![CDATA[{##PID##}]]></idx>
<title><![CDATA[{##PNAME##}]]></title>
<link><![CDATA[{##LINK##}]]></link>
<type><![CDATA[{##PTYPE_M##}]]></type>
<time_start>{##STT_DATETIME##}</time_start>
<time_end>{##END_DATETIME##}</time_end>
<coupon_wdate_start>{##BEG_DATE##}</coupon_wdate_start>
<coupon_wdate_end>{##EXP_DATE##}</coupon_wdate_end>
<price_original>{##PRICEO##}</price_original>
<price_now>{##PRICES##}</price_now>
<sell_count>{##CNTSALE##}</sell_count>
<count_min>{##CNTMAX##}</count_min>
<count_max>{##BUYLIMIT##}</count_max>
<photo1><![CDATA[{##PIMG##}]]></photo1>
<photo2></photo2>
<photo3></photo3>
<photo4></photo4>
<photo5></photo5>
<area><![CDATA[{##RSSAREA1##}]]></area>
<area><![CDATA[{##RSSAREA2##}]]></area>
<category><![CDATA[{##CATEGORY##}]]></category>
<category2></category2>
<shop><![CDATA[{##SUP_NAME##}]]></shop>
<tel><![CDATA[{##SUP_TEL##}]]></tel>
<addr><![CDATA[{##SUP_ADDRESS##}]]></addr>
<latitude>0.0</latitude>
<longitude>0.0</longitude>
<mapurl><![CDATA[{##HOMEPAGE##}]]></mapurl>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
?>