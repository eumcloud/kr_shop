<?php
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<DCYA>";
echo "<fileversion>201103221533</fileversion>";
echo "<SITE_INFO>";
echo "<SITE_NAME><![CDATA[$company[homepage_title]]]></SITE_NAME>";
echo "<SITE_URL><![CDATA[http://$_SERVER[HTTP_HOST]]]></SITE_URL>";
echo "<DEAL_CNT></DEAL_CNT>";
echo "<NOW_TIME>".date("Y-m-d H:i:s")."</NOW_TIME>";
echo "</SITE_INFO>";
##DATA FORMAT
$dataForm = "
<DEAL_INFO>
		<DEAL_IDX>{##PID##}</DEAL_IDX>
		<DEAL_URL><![CDATA[{##LINK##}]]></DEAL_URL>
		<DEAL_TITLE>{##PNAME##}</DEAL_TITLE>
		<DEAL_DESCRIPTION><![CDATA[{##PMSG##}]]></DEAL_DESCRIPTION>
		<DEAL_CATEGORI>{##CATEGORY##}</DEAL_CATEGORI>
		<DEAL_ADD1>{##SUP_ADDRESS##}</DEAL_ADD1>
		<DEAL_ADD2>{##SUP_ADDRESS1##}</DEAL_ADD2>
		<DEAL_IMGURL>{##PIMG##}</DEAL_IMGURL>
		<DEAL_ORGPRICE>{##PRICEO##}</DEAL_ORGPRICE>
		<DEAL_DCPRICE>{##PRICES##}</DEAL_DCPRICE>
		<DEAL_DCRATE>{##PRICER##}</DEAL_DCRATE>
		<DEAL_MINCNT>{##CNTMIN##}</DEAL_MINCNT>
		<DEAL_MAXCNT>{##CNTMAX##}</DEAL_MAXCNT>
		<DEAL_BUYCNT>{##CNTSALE##}</DEAL_BUYCNT>
		<DEAL_STATE>1</DEAL_STATE>
		<DEAL_STIME>{##STT_DATE##}</DEAL_STIME>
		<DEAL_ETIME>{##END_DATE##}</DEAL_ETIME>
</DEAL_INFO>";


##FORLOOP
RunLoop($dataForm);

##END
echo "</DCYA>";
?>