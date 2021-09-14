<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<mall>";
echo "<fileversion>201103120037</fileversion>";
##DATA FORMAT
$dataForm = "
<item>
    <name><![CDATA[{##PNAME##}]]></name>
    <desc><![CDATA[{##PMSG##}]]></desc>
    <oprice><![CDATA[{##PRICEO##}]]></oprice>
    <dprice><![CDATA[{##PRICES##}]]></dprice>
    <edate><![CDATA[{##END_DATE##}]]></edate>
    <image><![CDATA[{##PIMG##}]]></image>
    <oimage><![CDATA[{##PIMG##}]]></oimage>
    <url><![CDATA[{##LINK##}]]></url>
    <min><![CDATA[{##CNTMIN##}]]></min>
    <max><![CDATA[{##CNTMAX##}]]></max>
    <now><![CDATA[{##CNTSALE##}]]></now>
    <pos><![CDATA[]]></pos>
    <addr><![CDATA[{##SUP_ADDRESS##}]]></addr>
    <exp><![CDATA[{##EXP_DATE##}]]></exp>
    <state>1</state>
    <lat></lat>
    <lng></lng>
    <cate><![CDATA[{##CATEGORY##}]]></cate>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</mall>";
?>