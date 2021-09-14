<?php
header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<shop>";
echo "<fileversion>201103120037</fileversion>";
##DATA FORMAT
$dataForm = "
<item>

   <shopName>{##TITLE##}</shopName>
   <area>{##RSSAREA1##}</area>
   <kind>{##CATEGORY##}</kind>
   <subject><![CDATA[{##PNAME##}]]></subject>
   <content><![CDATA[{##PMSG##}]]></content>
   <address>{##SUP_ADDRESS##}</address>
   <closeTime><![CDATA[{##END_DATETIME##}]]></closeTime>
   <url><![CDATA[{##LINK##}]]></url>
   <originPrice>{##PRICEO##}</originPrice>
   <salePrice>{##PRICES##}</salePrice>
   <saleRate>{##PRICER##}</saleRate>
   <saled>{##CNTSALE##}</saled>
   <saleYn>Y</saleYn>
   <thumbnail>{##PIMG##}</thumbnail>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</shop>";
?>