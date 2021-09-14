<?php

header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<items ver=\"1.0\">";
echo "<fileversion>201103151809</fileversion>";
##DATA FORMAT
$dataForm = "
<item>

    <url><![CDATA[{##LINK##}]]></url>
    <title><![CDATA[[{##PNAME##}]]></title>
    <desc><![CDATA[[{##PMSG##}]]></desc>
    <product><![CDATA[[]]></product>
    <address><![CDATA[{##SUP_ADDRESS##}]]></address>
    <oprice><![CDATA[{##PRICEO##}]]></oprice>
    <dprice><![CDATA[{##PRICES##}]]></dprice>
    <dcrate><![CDATA[{##PRICER##}]]></dcrate>
    <mincnt><![CDATA[{##CNTMIN##}]]></mincnt>
    <maxcnt><![CDATA[{##CNTMAX##}]]></maxcnt>
    <nowcnt><![CDATA[{##CNTSALE##}]]></nowcnt>
    <start_date><![CDATA[{##STT_DATETIME##}]]></start_date>
    <end_date><![CDATA[{##END_DATETIME##}]]></end_date>
    <expire_date><![CDATA[{##EXP_DATETIME##}]]></expire_date>
    <photo><![CDATA[{##PIMG##}]]></photo>
</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</items>";
?>