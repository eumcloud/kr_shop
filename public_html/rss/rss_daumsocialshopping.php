<?php
header("Content-Type: text/xml; charset=utf-8"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");
include_once("addon_rss.php");

##START
$slntype = get_slntype();
$company = getRow("SELECT * FROM ".$slntype."Company");  //회사기본설정

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<result>";

echo "<partnerid>jaeun2001</partnerid>";
echo "<code>0000</code>";
echo "<prods>";
##DATA FORMAT
$dataForm = "
    <prod>
        <shop>
            <shopkey><![CDATA[{##COMPRI##}]]></shopkey>
            <shopname><![CDATA[{##COMNAME##}]]></shopname>

            <regdttm><![CDATA[{##COMINPUT##}]]></regdttm>
            <upddttm><![CDATA[{##COMMODIFY##}]]></upddttm>

            <csphone><![CDATA[{##COMTEL##}]]></csphone>

            <csmail><![CDATA[{##COMEMAIL##}]]></csmail>
            <homeurl><![CDATA[{##HOMEPAGE##}]]></homeurl>
            <mainphone><![CDATA[{##COMTEL##}]]></mainphone>
            <comsalesnumber><![CDATA[{##COMSALESNUMBER##}]]></comsalesnumber>
            <storeurl></storeurl>
        </shop>
        <stores>
            <store>
                <storekey><![CDATA[{##PPID##}]]></storekey>
                <storename><![CDATA[{##SUP_NAME##}]]></storename>
                <regdttm><![CDATA[{##SUP_SIGNDATE##}]]></regdttm>
                <upddttm><![CDATA[{##SUP_MODIFYDATE##}]]></upddttm>
                <storepositiondesc></storepositiondesc>
                <storephone><![CDATA[{##SUP_TEL##}]]></storephone>
                <parkingyn></parkingyn>
                <parkingdesc></parkingdesc>
                <openyn>Y</openyn>
                <seat>0</seat>
                <zipcode><![CDATA[{##SUP_ZIP##}]]></zipcode>
                <address><![CDATA[{##SUP_ADDRESS##}]]></address>
                <hotspotname><![CDATA[{##RSSAREA2##}]]></hotspotname>
                <hotspotid><![CDATA[{##AID##}]]></hotspotid>
                <mapinfo></mapinfo>
                <businesstime></businesstime>
                <mainyn>Y</mainyn>
                <status>Y</status>
            </store>
        </stores>

            <prodid><![CDATA[{##PIDN##}]]></prodid>
            <produrl><![CDATA[{##LINK##}]]></produrl>
            <prodmobileurl></prodmobileurl>
            <catename><![CDATA[{##CATEGORY##}]]></catename>
            <cateid><![CDATA[{##AID##}]]></cateid>
            <selltype><![CDATA[{##DELCHK##}]]></selltype>
            <buytype><![CDATA[{##NOWCHK##}]]></buytype>
            <prodname><![CDATA[{##PNAME##}]]></prodname>
            <proddesc><![CDATA[{##PMSG##}]]></proddesc>
            <prodimg><![CDATA[{##PIMG##}]]></prodimg>
            <prodmainimg><![CDATA[{##PIMG##}]]></prodmainimg>
            <proddetaildesc><![CDATA[{##DETAIL##}]]></proddetaildesc>

            <usedesc></usedesc>
            <refunddesc></refunddesc>
            <etcdesc></etcdesc>
            <storedesc></storedesc>

            <normalprice>{##PRICEO##}</normalprice>
            <saleprice>{##PRICES##}</saleprice>
            <discrate>{##PRICER##}</discrate>
            <maxcnt>{##CNTMAX##}</maxcnt>
            <mincnt>{##CNTMIN##}</mincnt>
            <limitcnt>{##BUYLIMIT##}</limitcnt>
            <salecnt>{##CNTSALE##}</salecnt>
            <salesdttm>{##daumSTT_DATE##}</salesdttm>
            <saleedttm>{##daumEND_DATE##}</saleedttm>
            <usesdttm>{##daumSTT_DATE##}</usesdttm>
            <useedttm>{##COU_ENDDATE##}</useedttm>


            <regdttm>{##REGDTTM##}</regdttm>
            <upddttm>00000000000000</upddttm>

            <limitadultyn><![CDATA[{##LIMITADULT##}]]></limitadultyn>
            <wholecountryyn><![CDATA[{##AREA_CHK##}]]></wholecountryyn>
            
            <prodstorecnt>1</prodstorecnt>
            <status>Y</status>

        
        
    </prod>
";




##FORLOOP
RunLoop($dataForm);

##END
echo "</prods>";
echo "</result>";
?>