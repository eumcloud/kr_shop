<?php
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<channel>";
echo "<fileversion>201103221533</fileversion>";
echo "<link>http://$_SERVER[HTTP_HOST]</link>";
echo "<description><![CDATA[]]></description>";
echo "<items>";
##DATA FORMAT
$dataForm = "
<item>

	<target_url><![CDATA[{##LINK##}]]></target_url>
	<image><![CDATA[{##PIMG##}]]></image>
	<subject><![CDATA[{##PNAME##}]]></subject>
	<memo><![CDATA[{##PMSG##}]]></memo>
	<category>{##CATEGORY##}</category>
	<region>{##RSSAREA1##}</region>
	<price>{##PRICES##}</price>
	<ori_price>{##PRICEO##}</ori_price>
	<discount><![CDATA[{##PRICER##}]]></discount>
	<start_date><![CDATA[{##STT_DATETIME##}]]></start_date>
	<end_date><![CDATA[{##END_DATETIME##}]]></end_date>
	<svc_name><![CDATA[{##SUP_NAME##}]]></svc_name>
	<svc_addr>{##SUP_ADDRESS##}</svc_addr>
	<end_yn>N</end_yn>
	<min>{##CNTMIN##}</min>
	<max>{##CNTMAX##}</max>
	<sale>{##CNTSALE##}</sale>
	<expire><![CDATA[{##EXP_DATE2##}]]></expire>
	<holiday_yn></holiday_yn>
	<park_yn></park_yn>

</item>";

##FORLOOP
RunLoop($dataForm);

##END
echo "</items>";
echo "</channel>";
?>