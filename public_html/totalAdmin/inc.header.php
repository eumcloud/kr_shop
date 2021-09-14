<?PHP
	include_once("inc.php");

	// 현재 파일에대한 권한여부 체크
	$app_current_link = ($app_current_link ? $app_current_link : "/totalAdmin/" . $CURR_FILENAME) ;
	$menu_chk = _MQ(" SELECT count(*) as cnt FROM m_menu_set as ms inner join m_adm_menu as am on(ms.m15_code1 = am.m2_code1 and ms.m15_code2 = am.m2_code2)   WHERE ms.m15_id = '" . $row_admin[id] . "' and am.m2_link = '".$app_current_link."' and ms.m15_vkbn = 'N' ");
	if($menu_chk[cnt]>0){
		error_msg("해당 페이지에 권한이 없습니다.");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="kr" lang="kr" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>통합관리자 페이지</title>

	<!-- 홈아이콘 -->
	<?$banner_info = info_banner("site_icon_basic",1,"data");?>
	<?php if($banner_info[0][b_img]) { ?>
	<link rel="apple-touch-icon-precomposed" href="<?=IMG_DIR_BANNER.$banner_info[0][b_img]?>" />
	<link rel="shortcut icon" href="<?=IMG_DIR_BANNER.$banner_info[0][b_img]?>" />
	<?php } ?>

	<link href="./css/adm_style.css" rel="stylesheet" type="text/css" />
	<link href="./css/sms_style.css" rel="stylesheet" type="text/css" />
	<link href="/pages/css/cm_font.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
	<link href="/pages/css/cm_design.css" rel="stylesheet" type="text/css" />
	<link href="/pages/css/part_cancel.css" rel="stylesheet" type="text/css" />

	<SCRIPT src="/include/js/jquery-1.11.2.min.js"></SCRIPT>
	<SCRIPT src="/include/js/jquery.placeholder.js"></SCRIPT>
	<SCRIPT src="/include/js/jquery-migrate-1.2.1.min.js"></SCRIPT>
	<SCRIPT src="/include/js/jquery.validate.js"></SCRIPT>

	<SCRIPT src="/include/js/jquery.lightbox_me.js"></SCRIPT>

	
	<!-- ToolTip 적용 LDD003 { -->
	<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
	<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
	<!-- } ToolTip 적용 LDD003 -->

	<SCRIPT src="/include/js/default.js"></SCRIPT>
	<style>
		tr.tr_check, tr.tr_radio { cursor: pointer; }
	</style>
</head>
<body<?php echo ($app_mode != 'popup'?' style="min-width: 1260px;"':null); ?>>

	<!-- 공통 frame -->
	<iframe name="common_frame" width=0 height=0 frameborder=0 style="display:none;"></iframe>


	<!-- TinyMCE -->
	<script language="Javascript" src="../include/tinymce/tinymce.min.js"></script>
	<script>
	tinymce.init({
	    selector: "textarea[geditor]",
	    theme: "modern",
	    language : 'ko_KR',
	    height: 370,
	    force_br_newlines : false,
	    force_p_newlines : true,
	    convert_newlines_to_brs : false,
	    remove_linebreaks : true,
		forced_root_block : '<?=($app_current_link <> "/totalAdmin/_addons.php?pass_menu=2yearOpt/_2year_opt.form" ? "p" : "")?>', // Needed for 3.x
            <?php // 매2년마다 수신동의 발송관리 에서 에디터 문제 처리 ?>
	    allow_script_urls: true,
	    <? if($app_current_link == '/totalAdmin/_mailing_data.list.php') { ?>
		document_base_url : "http://<?=$_SERVER[HTTP_HOST]?>/",
		relative_urls:false,
	    remove_script_host: false,
		<? } else { ?>
		document_base_url : "/",
		relative_urls:false,
		remove_script_host: true,
		<? } ?>
	    //convert_urls: false,
	    formats: { bold : {inline : 'b' }},
	    extended_valid_elements: "@[class|id|width|height|alt|href|style|rel|cellspacing|cellpadding|border|src|name|title|type|onclick|onfocus|onblur|target],b,i,em,strong,a,img,br,h1,h2,h3,h4,h5,h6,div,table,tr,td,s,del,u,p,span,article,section,header,footer,svg,blockquote,hr,ins,ul,dl,object,embed,pre",
	    plugins: [
	        "jbimages",
	         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
	         "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
	         "save table contextmenu directionality emoticons template paste textcolor imagetools"
	   ],
	   content_css: "/pages/css/editor.css",
	   body_class: "editor_content",
	   menubar : false,
	   toolbar1: "undo redo | fontsizeselect | advlist bold italic forecolor backcolor | charmap | hr | jbimages | autolink link media | preview | code",
	   toolbar2: "bullist numlist outdent indent | alignleft aligncenter alignright alignjustify | table"
	 }); 
	</script>
	<SCRIPT LANGUAGE="JavaScript">
	$(document).ready(function(){

		// ToolTip 적용 LDD003
		$('.content_section').tooltip({ show: null, hide: null });

		// 에디터 작성시 validate 조정
		$('form').submit(function(){ tinyMCE.triggerSave(); });

		// tr 클릭시 체크박스 활성화
		$('tr.tr_check').on('click',function() {
			if (event.target.type !== 'checkbox' && event.target.type !== 'text') {
				$(':checkbox', this).trigger('click');
			}
		});
		$('tr.tr_radio').on('click',function() {
			if (event.target.type !== 'radio' && event.target.type !== 'text') {
				$(':radio', this).trigger('click');
			}
		});

		$('body').delegate('input[type=file]','change',function(){
			var input = this;
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('img#img_'+input.name).attr('src',e.target.result).show();
				}
				reader.readAsDataURL(input.files[0]);
			} else {
				$('img#img_'+input.name).attr('src','').hide();
			}
		});
	});
	</SCRIPT>



<div id="wrap">



<?PHP
	if($app_mode <> "popup") {
?>


	<!-- 헤더 -->
	<div id="header">
		<a href="index.html" class="logo">
			<span class="name"><?=$row_setup[site_name]?></span>
			<span class="en">Total Admin</span>
		</a>
		<div class="title"><img src="./images/title.png" alt="타이틀" title="" /></div>
		<div class="today">
			<span class="year"><?=date("Y")?></span>
			<span class="month"><?=date("m")?></span>
			<span class="day"><?=date("d")?></span>
		</div>
		<div class="btn">
			<a href="../" class="btn_home" title="내홈페이지" target="_blank"></a>
			<a href="logout.php" class="btn_logout" title="로그아웃"></a>
		</div>
	</div>
	<!-- // 헤더 -->


	<!-- 가운데 (2단메뉴 펼침:id="container" 2단메뉴 닫음: id="container_hide") -->
	<!-- 컨텐츠 -->
	<div class="container" ID="depth2_leftmenu">

		<!-- 1단메뉴 -->
		<div class="aside_first">
<?PHP

	// 관리자 메뉴정보 불러오기
	$arr_header_memu = array();
    $hdres = _MQ_assoc(" SELECT * FROM m_menu_set WHERE m15_id = '" . $row_admin[id] . "' ");
	foreach( $hdres as $k=>$v ){
		$arr_header_memu[$v[m15_code1]][$v[m15_code2]] = $v[m15_vkbn];
	}

	// 현재 페이지의 위치 추출 - inc.php => $CURR_FILENAME 정보 이용
	$app_current_link = ($app_current_link ? $app_current_link : "/totalAdmin/" . $CURR_FILENAME) ;
	$currleft_r = _MQ(" SELECT * FROM m_adm_menu WHERE m2_link = '". $app_current_link ."' AND m2_code2 != '' ");
	if(sizeof($currleft_r) == 0 ) {
		$currleft_r = _MQ(" SELECT * FROM m_adm_menu WHERE m2_vkbn = 'y' AND m2_code1 = '10' AND m2_code2 != '' ORDER BY m2_seq limit 1 "); // 관리자 메인의 맨 처음 메뉴 불러오기
	}
	// 현재 페이지 명
	if(!$app_current_page_name) $app_current_page_name = $currleft_r["m2_name2"];
	// 현재 메뉴명
	$app_current_menu_name = "";

	// 1차 메뉴 추출 2015-01-23 hit되는 클래스 방식 변경 _on 으로 할 경우 너무 많은 css가 필요해서 hit 로 추가 //////////////////////////////////////////////////////////////
	$left_que  = " SELECT * FROM m_adm_menu WHERE m2_code1 != '' AND m2_code2 = '' ORDER BY m2_seq asc  ";
	$left_res = _MQ_assoc($left_que);
	foreach( $left_res as $k=>$v ) {
		$app_current_menu_name = ( !$app_current_menu_name && $v[m2_code1] == $currleft_r[m2_code1] ? $v[m2_name1] : $app_current_menu_name );
		echo "<a href='". $v[m2_link] ."' class='" . ( $v[m2_code1] == $currleft_r[m2_code1] ? "menu_". ($v[m2_code1]*1-1) ." hit" : "menu_" . ($v[m2_code1]*1-1) ) . "' title='". $v[m2_name1] ."' style='display:". ($v[m2_vkbn] == 'y' &&  $arr_header_memu[$v[m2_code1]][$v[m2_code2]] == "Y" ? "block" : "none") ."'></a>";
	}
?>
		</div>
		<!-- // 1단메뉴 -->


		<!-- 2단메뉴 -->
		<div class="aside_second">
<?PHP
	// 2차 메뉴 추출 //////////////////////////////////////////////////////
	$leftsub_que  = " SELECT * FROM m_adm_menu WHERE m2_vkbn = 'y' AND m2_code1 = '".$currleft_r[m2_code1]."' AND m2_code2 != '' ORDER BY m2_seq asc  ";
	$leftsub_res = _MQ_assoc($leftsub_que);
	foreach( $leftsub_res as $k=>$v ) {
		if($v[m2_code1] && $v[m2_code2]){
			echo "<a href='". $v[m2_link] ."' class='". ( $v[m2_code2]== $currleft_r[m2_code2] ? "menu_on" : "menu" ) ."' title='". $v["m2_name2"] ."' style='display:". ( $arr_header_memu[$v[m2_code1]][$v[m2_code2]] == "Y" ? "block" : "none") ."'>". $v["m2_name2"] ."</a>";
		}
	}

?>




			<!-- 자주사용하는메뉴 -->
			<div class="favorite_menu">
				<a class="title" alt="즐겨찾는메뉴설정" title="즐겨찾는메뉴설정" href="_favmenu.form.php" /></a>
				<div class="box">
<?PHP
	// - 데이터가 있는 경우 처리 ---
	$que = "select * from odtFavmenu where fm_appId='$row_admin[id]' order by fm_menuIdx asc , fm_uid asc "; 
	// 전체관리자 - 고정
	$res = _MQ_assoc($que);
	foreach($res as $k=>$v){
		echo "<a href='".$v[fm_menuLink]."' class='fmenu' title=''>".$v[fm_menuName]."</a>";
	}
	// - 데이터가 있는 경우 처리 ---
?>
				</div>
			</div>
			<!-- // 자주사용하는메뉴 -->



		</div> 
		<!-- // 2단메뉴 -->

<?PHP
	}
?>

		<!-- 내용 -->
		<div class="content_section">
			<div class="content_section_fix">


<? IF($app_current_page_name) { ?>
				<div class="open_close"><span class="btn_close" ID="open_close_btn_close" title="메뉴닫기"></span><span class="btn_open" ID="open_close_btn_open" title="메뉴열기"></span></div>

				<!-- 페이지타이틀 -->
				<div class="title_area">
					<span class="icon"></span>
					<span class="title"><?=$app_current_page_name?></span>
					<span class="location">홈 &gt; <?=$app_current_menu_name?> &gt; <?=$app_current_page_name?></span>
				</div>
				<!-- // 페이지타이틀 -->
<? } ?>