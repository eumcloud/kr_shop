<?php

	include_once( dirname(__FILE__)."/inc.php");

	// 로그인 여부 체크
	if ( !$_COOKIE["auth_adminid"] && $_login_trigger <> "N" ) {
		error_loc("./");
	}

	// 현재 파일에대한 권한여부 체크
	$app_current_link = ($app_current_link ? $app_current_link : "/totalAdmin/" . $CURR_FILENAME) ;
	if(in_array($app_current_link , $arr_menu_link)) { // 있는 페이지에서만 검사함.
		$menu_chk = _MQ(" SELECT count(*) as cnt FROM m_menu_set as ms inner join m_adm_menu as am on(ms.m15_code1 = am.m2_code1 and ms.m15_code2 = am.m2_code2)   WHERE ms.m15_id = '" . $row_admin[id] . "' and am.m2_link = '".$app_current_link."' and ms.m15_vkbn = 'N' ");
		if($menu_chk[cnt]>0){ error_msg("해당 페이지에 권한이 없습니다."); }
	}
?>
<!DOCTYPE HTML>
<head>
	<title>모바일 관리자페이지</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- 화면축소/확대방지 -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0, target-densitydpi=medium-dpi" />

	<!-- 모바일에서 숫자 전화자동링크방지 -->
	<meta name="format-detection" content="telephone=no" />

	<!-- 홈아이콘 -->
	<?$banner_info = info_banner("site_icon_basic",1,"data");?>
	<?php if($banner_info[0][b_img]) { ?>
	<link rel="apple-touch-icon-precomposed" href="<?=IMG_DIR_BANNER.$banner_info[0][b_img]?>" />
	<link rel="shortcut icon" href="<?=IMG_DIR_BANNER.$banner_info[0][b_img]?>" />
	<?php } ?>

	<link href="<?=PATH_MOBILE_TOTALADMIN?>/css/m.default_setting.css" rel="stylesheet" type="text/css" />
	<link href="<?=PATH_MOBILE_TOTALADMIN?>/css/m.totalAdmin.css" rel="stylesheet" type="text/css" />

	<!-- 공통css -->	
	<link href="<?=PATH_MOBILE_TOTALADMIN?>/css/cm_font.css" rel="stylesheet" type="text/css" />
	<link href="<?=PATH_MOBILE_TOTALADMIN?>/css/cm_design.css" rel="stylesheet" type="text/css" />


	<!-- 자바스크립트 -->
	<script src="/include/js/jquery-1.11.2.min.js" type="text/javascript"></script>
	<script src="/include/js/jquery.placeholder.js" type="text/javascript"></script>

	<!-- jQuery performance boost -->
	<script src="/include/js/jquery/jquery.easing.1.3.js" type="text/javascript"></script>
	<script src="/include/js/TweenMax.min.js"></script>
	<script src="/include/js/jquery.gsap.min.js"></script>

	<!-- validate -->
	<script src="/include/js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
	<script src="/include/js/jquery.validate.js" type="text/javascript"></script>

	<!-- default js -->
	<script src="./js/default.js" type="text/javascript"></script>

</head>