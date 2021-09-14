<?php

header("Content-Type: text/html; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
echo "<rss version='2.0'>";
echo "<fileversion>201103120037</fileversion>";
echo "<channel>";
##DATA FORMAT
$dataForm = "
<item>
    <guid><![CDATA[{##PID##}]]></guid>
    <link><![CDATA[{##LINK##}]]></link>
    <title><![CDATA[[{##PNAME##}]]></title>
    <subtitle><![CDATA[{##SUBNAME##}]]></subtitle>
    <description><![CDATA[{##PMSG##}]]></description>
    <category><![CDATA[{##CATEGORY##}]]></category>
    <minCnt><![CDATA[{##CNTMIN##}]]></minCnt>
    <maxCnt><![CDATA[{##CNTMAX##}]]></maxCnt>
    <curCnt><![CDATA[{##CNTSALE##}]]></curCnt>
    <pubDate><![CDATA[{##STT_DATETIME##}]]></pubDate>
    <image><![CDATA[{##PIMG##}]]></image>
    <price><![CDATA[{##PRICEO##}]]></price>
    <dcPrice><![CDATA[{##PRICES##}]]></dcPrice>
    <dcRate><![CDATA[{##PRICER##}]]></dcRate>
    <dcInfo></dcInfo>
    <shipFree></shipFree>
    <itemInfo></itemInfo>
    <begin><![CDATA[{##STT_DATETIME##}]]></begin>
    <end><![CDATA[{##END_DATETIME##}]]></end>
    <update><![CDATA[{##STT_DATETIME##}]]></update>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</channel>";
echo "</rss>";
?>