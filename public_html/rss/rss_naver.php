<?php

include "./addon_rss.php";
header('Content-Type: text/xml; charset=UTF-8'); // xm 해더 선언 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");

##START
echo "<?xml version='1.0' encoding='UTF-8'?>";
echo "<rss version='2.0' xmlns:dc='http://purl.org/dc/elements/1.1/'>";

echo   "<channel>";
echo   "<title>[".$row_setup['site_name']."] RSS </title>";
echo   "<link>http://".$_SERVER['HTTP_HOST']."/rss/rss_naver.php</link>";		
echo  "<description>".$row_setup['login_page_email']."</description>";
echo  "<pubDate>".date('r',time())."</pubDate>";
echo  "<generator>Onedaynet</generator>";
echo  "<managingEditor>".$row_setup['site_name']."</managingEditor>";

$dataForm = "";

$dataForm .= "<item>";			 	
$dataForm .= "<title><![CDATA[{##PNAME##}]]></title>";	
$dataForm .= "<link><![CDATA[{##LINK##}]]></link>";
$dataForm .= "<description><![CDATA[{##DETAIL##}]]></description>";
$dataForm .= "<category><![CDATA[{##CATEGORY_01##}]]></category>";
$dataForm .= "<category><![CDATA[{##CATEGORY_02##}]]></category>";
$dataForm .= "<category><![CDATA[{##CATEGORY_03##}]]></category>";
$dataForm .= "<guid><![CDATA[{##LINK##}]]></guid>";
$dataForm .="<pubDate>{##STT_DATETIME##}</pubDate>";
$dataForm .= "</item>";	


##FORLOOP
RunLoop($dataForm);

##END
echo  '</channel>';
echo  '</rss>';
?>