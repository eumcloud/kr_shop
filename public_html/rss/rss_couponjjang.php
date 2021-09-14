<?php

header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<couponjjang>";
echo "<fileversion>201107112050</fileversion>";
echo "<datas>";

##DATA FORMAT
$dataForm = "
<data>
	<meta_id><![CDATA[{##PPID##}]]></meta_id>
	<title><![CDATA[{##PNAME##}]]></title>
	<description><![CDATA[{##PMSG##}]]></description>
	<sale_start><![CDATA[{##END_DATETIME##}]]></sale_start>
	<sale_end><![CDATA[{##END_DATETIME##}]]></sale_end>
	<url><![CDATA[{##LINK##}]]></url>
	<original_price>{##PRICEO##}</original_price>
	<discount_price>{##PRICES##}</discount_price>
	<discount_rate>{##PRICER##}</discount_rate>
	<max_count>{##CNTMAX##}</max_count>
	<min_count>{##CNTMIN##}</min_count>		
	<current_count>{##CNTSALE##}</current_count>
	<main_image><![CDATA[{##PIMG##}]]></main_image>
	<category><![CDATA[{##CATEGORY##}]]></category>      
	<main_area><![CDATA[{##RSSAREA1##}]]></main_area>
	<shops>
		<shop>
			<s_name><![CDATA[{##SUP_NAME##}]]></s_name>
			<s_tel><![CDATA[{##SUP_TEL1##}-{##SUP_TEL2##}-{##SUP_TEL3##}]]></s_tel>
			<s_address><![CDATA[{##SUP_ADDRESS##}]]></s_address>
		</shop>
	</shops>
</data>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</datas>";
echo "</couponjjang>";
?>
