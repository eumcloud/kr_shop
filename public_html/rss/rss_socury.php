<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<items>";
echo "<fileversion>201103120037</fileversion>";
##DATA FORMAT
$dataForm = "
<item>
    <url><![CDATA[{##LINK##}]]></url>
    <category><![CDATA[{##CATEGORY##}]]></category>
    <address><![CDATA[{##SUP_ADDRESS##}]]></address>
    <city><![CDATA[{##RSSAREA1##}]]></city>
    <venue><![CDATA[{##RSSAREA2##}]]></venue>        
    <location><![CDATA[{##SUP_ADDRESS##}]]></location>
    <subject><![CDATA[{##PNAME##}]]></subject>
    <image><![CDATA[{##PIMG##}]]></image>
    <oc>{##PRICEO##}</oc>
    <dc>{##PRICES##}</dc> 
    <dcrate>{##PRICER##}</dcrate>
	<mincnt>{##CNTMIN##}</mincnt>
    <maxcnt>{##CNTMAX##}</maxcnt> 
    <salecnt>{##CNTSALE##}</salecnt>
    <limitdate><![CDATA[{##END_DATETIME##}]]></limitdate>
    <expire><![CDATA[]]></expire>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</items>";
?>