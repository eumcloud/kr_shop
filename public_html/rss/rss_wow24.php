<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<deals>";
echo "<fileversion>201103120037</fileversion>";
##DATA FORMAT
$dataForm = "
<item>
    <image><![CDATA[{##PIMG##}]]></image>
    <name><![CDATA[{##TITLE##}]]></name>
    <url><![CDATA[{##LINK##}]]></url>
    <title><![CDATA[{##PNAME##}]]></title>
    <original><![CDATA[{##PRICEO##}]]></original>
    <price><![CDATA[{##PRICES##}]]></price>
    <discount><![CDATA[{##PRICER##}]]></discount>
    <start_at><![CDATA[{##STT_DATETIMEHM##}]]></start_at>
    <end_at><![CDATA[{##END_DATETIMEHM##}]]></end_at>
    <min_count><![CDATA[{##CNTMIN##}]]></min_count>
    <max_count><![CDATA[{##CNTMAX##}]]></max_count>
    <now_count><![CDATA[{##CNTSALE##}]]></now_count>
    <category><![CDATA[{##CATEGORY##}]]></category>
    <event><![CDATA[]]></event>
    <status><![CDATA[판매]]></status>
    <address><![CDATA[{##SUP_ADDRESS##}]]></address>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</deals>";
?>