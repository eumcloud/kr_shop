<?php
	include_once dirname(__FILE__)."/lib.php";
	header('Content-Type: application/xml; charset=utf-8');

	$arrXmlData = array();
	$sitemapLib = new sitemapLib();

	// header :: default info
	array_push($arrXmlData,"<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
	array_push($arrXmlData,"<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"");
	array_push($arrXmlData,"xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"");
	array_push($arrXmlData,"xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9");
	array_push($arrXmlData,"http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">");


	// main :: main info
	array_push($arrXmlData,"<url>");
	array_push($arrXmlData,"<loc>" . SITE_URL . "</loc>");
	array_push($arrXmlData,"<changefreq>" . FREQUENCY . "</changefreq>");
	array_push($arrXmlData,"</url>");

	// content :: product
	array_push($arrXmlData,$sitemapLib->product());

	// content :: board , post
	array_push($arrXmlData,$sitemapLib->board());

	// content :: page
	array_push($arrXmlData,$sitemapLib->page());


	// 추가페이지가 있다면 정의
	/* ex) lib.php 에 정의
	$arrAddUrl = array(
		"/?pn=product.list&_event=type&typeuid=2"
	);
	*/
	foreacH($arrAddUrl as $v){
		// main :: main info
		array_push($arrXmlData,"<url>");
		array_push($arrXmlData,"<loc>" . $v . "</loc>");
		array_push($arrXmlData,"<changefreq>" . FREQUENCY . "</changefreq>");
		array_push($arrXmlData,"</url>");
	}

	// footer :: end info
	array_push($arrXmlData,"</urlset>");
	echo implode("\n",$arrXmlData);
?>

