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
<items>
    <product>
    <url><![CDATA[{##LINK##}]]></url>
    <cate><![CDATA[{##CATEGORY##}]]></cate>
    <addr1><![CDATA[{##SUP_ADDRESS##}]]></addr1>
    <addr2><![CDATA[]]></addr2>
    <addr3><![CDATA[]]></addr3>
    <addr4><![CDATA[]]></addr4>
    <pid><![CDATA[{##PID##}}]]></pid>
    <pname><![CDATA[{##PNAME##}]]></pname>
    <event><![CDATA[{##PMSG##}]]></event>
    <igurl><![CDATA[{##PIMG##}]]></igurl>
    <logimg><![CDATA[{##LOGO##}]]></logimg>
    <price><![CDATA[{##PRICEO##}]]></price>
    <dcprice><![CDATA[{##PRICES##}]]></dcprice>
    <dcrate><![CDATA[{##PRICER##}]]></dcrate>
    <mincnt><![CDATA[{##CNTMIN##}]]></mincnt>
    <maxcnt><![CDATA[{##CNTMAX##}]]></maxcnt>
    <salecnt><![CDATA[{##CNTSALE##}]]></salecnt>
    <stdate><![CDATA[{##STT_DATETIME##}]]></stdate>
    <ltdate><![CDATA[{##END_DATETIME##}]]></ltdate>
    <expire><![CDATA[{##EXP_DATETIME##}]]></expire>
    </product>
</items>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</items>";
?>