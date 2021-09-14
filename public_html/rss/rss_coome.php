<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<fileversion>201112011500</fileversion>";
echo "<coupon_rss>";
echo "<version>1.0</version>";
echo "<name>$comName</name>";
echo "<url>$comHome</url>";
echo "<products>";

##DATA FORMAT
$dataForm = "
    <product>
      <id>{##PIDN##}</id>
      <title><![CDATA[{##PNAME##}]]></title>
      <description><![CDATA[{##DETAIL##}]]></description>	  
      <url><![CDATA[{##LINK##}]]></url>	  
      <category><![CDATA[{##CATEGORY##}]]></category>
      <sell_start><![CDATA[{##STT_DATETIME##}]]></sell_start>
	  <sell_end><![CDATA[{##END_DATETIME##}]]></sell_end>
      <price>{##PRICEO##}</price>
      <dc_price>{##PRICES##}</dc_price>
      <dc_rate>{##PRICER##}</dc_rate>
      <min_count>{##CNTMIN##}</min_count>
      <max_count>{##CNTMAX##}</max_count>
      <sell_count>{##CNTSALE##}</sell_count>

      <images>
        <image><![CDATA[{##PIMG##}]]></image>
      </images>

      <shops>
        <shop>
          <name><![CDATA[{##SUP_NAME##}]]></name> 
          <tel><![CDATA[{##SUP_TEL##}]]></tel>
          <address><![CDATA[{##SUP_ADDRESS##}]]></address>
          <region><![CDATA[{##RSSAREA2##}]]></region>
        </shop>
      </shops>
    </product>
";

##FORLOOP
RunLoop($dataForm);

##END
echo "</products>";
echo "</coupon_rss>";
?>