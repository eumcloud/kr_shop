<?PHP
	include_once(dirname(__FILE__)."/../../include/inc.php");

	// 옵션/장바구니/비회원 구매를 위한 쿠키 적용
	if(!$_COOKIE["AuthShopCOOKIEID"]) {
		samesiteCookie("AuthShopCOOKIEID", substr(enc('e' , md5(time().rand(0,9999)."hy shop")),0,15) , 0 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
	}

	// 세션 기록 2016-02-02
	if($_GET[pn] == 'shop.order.form' && $_COOKIE["AuthShopCOOKIEID"]) {

		if(substr(phpversion(),0,3) < 5.4) { session_register("order_start"); }
		$_SESSION["order_start"] = $_COOKIE["AuthShopCOOKIEID"];
	}

	$homepage_title = stripslashes($row_company['homepage_title']);
    // 2018-11-12 SSJ :: 페이지별 타이틀 설정 패치 ---{ //
    $site_glbtlt = $homepage_title; // 공통 타이틀 저장
    $title_setup = array();

    // 기본 페이지 처리
    if($pn == 'product.brand_list' && $uid){ // 브랜드상품(브랜드 선택 시)
        $app_pn_title = '/?pn='.$pn.'&uid=';
    }else if($pn){
        $app_pn_title = '/?pn='.$pn;
    }
    $title_setup = _MQ(" select * from odtSiteTitle where sst_page like '%§§". $app_pn_title ."§§%' ");

    // 추가적용 페이지 처리
    if($title_setup['sst_uid'] == ''){
        $title_setup = _MQ(" select * from odtSiteTitle where sst_page like '%§§". $_SERVER['REQUEST_URI'] ."§§%' ");
    }

    // 타이틀 문구 설정
    if($title_setup['sst_title'] <> ''){
        $homepage_title = trim(stripslashes($title_setup['sst_title']));
    }else{
        $homepage_title = $site_glbtlt;
    }

    // 기본 치환자
    $arrTitleReplace = array();
    $arrTitleReplace['{공통타이틀}'] = $site_glbtlt;
    $arrTitleReplace['{사이트명}'] = $row_setup['site_name'];
    $arrTitleReplace['{검색어}'] = trim($keyword);
    // 치환자 초기화
    $arrTitleReplace['{카테고리명}'] = ''; $arrTitleReplace['{상품명}'] = ''; $arrTitleReplace['{게시판명}'] = ''; $arrTitleReplace['{게시물제목}'] = ''; $arrTitleReplace['{기획전명}'] = ''; $arrTitleReplace['{브랜드명}'] = '';
    // 치환자 추출
    if($cuid && preg_match("/{카테고리명}/i" , $homepage_title)) $arrTitleReplace['{카테고리명}'] = ($sub_cuid?$sub_cuid.' > ':'').trim(stripslashes(_MQ_result(" select catename from odtCategory where catecode = '". $cuid ."' ")));
    if($pcode && preg_match("/{상품명}/i" , $homepage_title)) $arrTitleReplace['{상품명}'] = trim(stripslashes(_MQ_result(" select name from odtProduct where code = '". $pcode ."' ")));
    // 게시판명 추출
    if(in_array($pn, array('board.list','board.view','board.form'))){
        if($_menu && preg_match("/{게시판명}/i" , $homepage_title)) $arrTitleReplace['{게시판명}'] = trim(stripslashes(_MQ_result(" select bi_name from odtBbsInfo where bi_uid = '". $_menu ."' ")));
        if($_uid && preg_match("/{게시물제목}/i" , $homepage_title)) $arrTitleReplace['{게시물제목}'] = trim(stripslashes(_MQ_result(" select b_title from odtBbs where b_uid = '". $_uid ."' ")));
    }

    // 치환자 적용
    $homepage_title = str_replace(array_keys($arrTitleReplace),array_values($arrTitleReplace), $homepage_title);
    $homepage_title = $homepage_title ? $homepage_title : $site_glbtlt;
    // }--- 2018-11-12 SSJ :: 페이지별 타이틀 설정 패치

	if($_GET['pcode'] && $_GET['pn'] == "product.view") {

		// 사이트 제목 설정
		if($row_company['homepage_title_product']=='yes' && $pcode) {
			$tmp = _MQ(" select name from odtProduct where code = '".$pcode."' ");
			$homepage_title = $tmp['name']." - ".$homepage_title;
		}

		// - 최근 본 상품 업데이트(쿠키생성)  ---
		if( !$_COOKIE["AuthProductLatest"] ){
			// -- AuthSDProductLatest 없을 경우 적용 ---
			$appAuthSDProductLatest = "";
			for( $i=0; $i<9 ; $i++ ){
				if( rand(1,2) == 1 ) { $appAuthSDProductLatest .= rand(0,9); } // 숫자
				else { $appAuthSDProductLatest .= chr(rand(97,122)); } // 영문
			}
			samesiteCookie("AuthProductLatest", $appAuthSDProductLatest , time()+3600*24*30 , "/" , "." . str_replace("www." , "" , $_SERVER['HTTP_HOST']));
			// -- AuthSDProductLatest 없을 경우 적용 ---
		} else { $appAuthSDProductLatest = $_COOKIE["AuthProductLatest"]; }
		// 최근 본 상품 업데이트 (쿠키생성)

		// - 최근 본 상품 업데이트 ---
		$plr = _MQ(" select count(*) as cnt from odtProductLatest where pl_pcode='" . $pcode . "' and pl_uniqkey='" . $appAuthSDProductLatest . "'  ");
		if( $plr['cnt'] == 0 ) {
			_MQ_noreturn(" insert odtProductLatest set pl_pcode='" . $pcode . "' , pl_uniqkey='" . $appAuthSDProductLatest . "' , pl_rdate=now()  ");
		}
		// - 최근 본 상품 업데이트 ---
	}
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="<?=stripslashes($row_company['metatag'])?>">
	<meta name="keyword" content="<?=stripslashes($row_company['metatag_keyword'])?>">
	<!-- 화면축소/확대방지 -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0, target-densitydpi=medium-dpi" />
	<!-- 전화자동링크방지 -->
	<meta name="format-detection" content="telephone=no" />
	<title><?=$homepage_title?></title>

	<!-- 홈아이콘 -->
	<?$banner_info = info_banner("site_icon_basic",1,"data");?>
	<?php if($banner_info[0]['b_img']) { ?>
	<link rel="apple-touch-icon-precomposed" href="<?=IMG_DIR_BANNER.$banner_info[0]['b_img']?>" />
	<link rel="shortcut icon" href="<?=IMG_DIR_BANNER.$banner_info[0]['b_img']?>" />
	<?php } ?>

	<?
		$sns_url = rewrite_url($_GET['pcode']);
		$sns_fullurl = "http://".$_SERVER['HTTP_HOST']."/".$pcode;
		$sns_url = $sns_url ? $sns_url : $sns_fullurl;
		if($_GET['pcode']) {
			// SNS 공유를 위한 변수 생성
			$sns_result = _MQ(" select * from odtProduct where code = '".$_GET['pcode']."' ");
			$sns_name = $sns_result['name']." ".number_format($sns_result['price'])."원";
			$sns_image = "http://".$_SERVER['HTTP_HOST'].IMG_DIR_PRODUCT.$sns_result['main_img'];
			$sns_desc = trim(str_replace("  "," ",str_replace(":","-",str_replace("\t"," ",str_replace("\r"," ",str_replace("\n"," ",str_replace("'","`",stripslashes(($sns_result['hort_comment']?$sns_result['short_comment']." - ":"") .$row_company['homepage_title']))))))));
		} else {
			$sns_name = $row_company['homepage_title'];
			$sns_image = "http://".$_SERVER['HTTP_HOST'].IMG_DIR_BANNER.$banner_info[0]['b_img'];
			$sns_desc = stripslashes($row_company['metatag']);
		}
	?>

	<?php
		// -- {canonical} 적용 :: inc.header.php PC/모바일 개별적용  -- 2019-11-28 LCY
		$http_mode = $_SERVER['HTTPS'] != '' ? 'https://': $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'https://' : 'http://';
		$canonical_url = $http_mode.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	?>

	<link rel="canonical" href="<?=$canonical_url?>" />
	<meta property="og:url" content="<?=$sns_fullurl?>" />
	<meta property="og:image" content="<?=$sns_image?>" />
	<meta property="og:site_name" content="<?=$row_company['homepage_title']?>" />
	<meta property="fb:app_id" content="<?=$row_setup['Facebook_id']?>" />
	<meta property="og:description" content="<?=$sns_desc?>" />
	<? if($_GET['pcode']) { ?>
	<meta property="og:title" content="<?=$sns_name?>" />
	<meta property="og:type" content="product" />
	<meta property="og:price:amount" content="<?=$sns_result['price']?>" />
	<meta property="og:price:currency" content="KRW" />
	<? } ?>

	<?php echo (str_replace(array('.onedaynet.co.kr', '.gobeyond.co.kr'), '', $_SERVER['HTTP_HOST']) != $_SERVER['HTTP_HOST']?'<meta name="robots" content="noindex">'.PHP_EOL:null); // 원데이넷/상상너머 2차 도메인으로 네이버 검색 노출 차단 ?>

	<!-- 자바스크립트 -->
	<script src="/include/js/jquery-1.11.2.min.js" type="text/javascript"></script>
	<script src="/include/js/jquery.placeholder.js" type="text/javascript"></script>
	<script src="/m/js/script.js?v=<?=$cache_ver?>" type="text/javascript"></script>

	<!-- 디자인css -->
	<link href="/m/css/m.default_setting.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
	<link href="/m/css/m.tplus4.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
	<!-- 공통css -->
	<link href="/m/css/cm_font.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
	<link href="/m/css/cm_design.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
	<link href="/m/css/cm_board.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
	<link href="/m/css/cm_shop.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
	<link href="/m/css/cm_member.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
	<link href="/m/css/cm_mypage.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
	<link href="/m/css/m.part_cancel.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" /><!-- 부분취소css -->
	<link href="/pages/css/editor.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
	<link href="/m/css/m.customize.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
	<link href="/m/css/cm_customize.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />

	<? if($row_setup['view_social_commerce']!='Y') { ?>
	<link href="/m/css/m.switch.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
	<? } ?>

	<!-- jQuery performance boost -->
	<script src="/include/js/jquery/jquery.easing.1.3.js" type="text/javascript"></script>
	<script src="/include/js/TweenMax.min.js"></script>
	<script src="/include/js/jquery.gsap.min.js"></script>
	<script src="/include/js/iscroll.js"></script>
	<script src="/include/js/imagesLoaded.min.js"></script>
	<script src="/include/js/jquery.lazyload/jquery.lazyload.js"></script>
	<? if($pn=='board.form') { ?><script src="/include/js/board.photo.js"></script><? } ?>

	<!-- lightbox -->
	<link href="/include/js/jquery.lightbox.css" rel="stylesheet" type="text/css" />
	<script src="/include/js/jquery.lightbox_me.js" type="text/javascript"></script>

	<!-- bxslide -->
	<link rel="stylesheet" href="/include/js/jquery.bxslider/jquery.bxslider.css" type="text/css" />
	<script src="/include/js/jquery.bxslider/jquery.bxslider.js"></script>

	<!-- validate -->
	<script src="/include/js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
	<script src="/include/js/jquery.validate.js" type="text/javascript"></script>

	<!-- 자바스크립트 공용변수 2015-11-17 -->
	<script>
	var od_url = 'http://<?php echo $_SERVER["HTTP_HOST"]; ?>';
	var rewrite_chk = '<?php echo ($row_setup['rewrite_chk']?$row_setup['rewrite_chk']:'no'); ?>';
	</script>
	<!-- 자바스크립트 공용변수 2015-11-17 -->
</head>
<body onload="<?=$pn=='shop.order.result'&&$row_setup['P_KBN']=='K'?'chk_pay();':''?>">
<iframe name="common_frame" src="about:blank" style="display:none;width:0;height:0;"></iframe>
<script>
$(document).ready(function() {
	var $root = $('html, body');
	$('.scrollto').click(function() {
		var target = $(this).data('scrollto');
		$root.animate({
			scrollTop: $('[name="' + target + '"]').offset().top - 10
		}, 500);
		return false;
	});
	$('.scrollfix').bind('mousewheel DOMMouseScroll',function(e){
		var e0 = e.originalEvent, delta = e0.wheelDelta || -e0.detail;
		this.scrollTop += ( delta < 0 ? 1 : -1 ) * 30;
		e.preventDefault();
	});
});
</script>