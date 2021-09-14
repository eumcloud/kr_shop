<?PHP
	include_once("inc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="kr" lang="kr" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>입점관리자 페이지</title>
	<link href="/pages/css/cm_font.css?v=<?=$cache_ver?>" rel="stylesheet" type="text/css" />
    <link href="./css/adm_style.css?c=<?=time()?>" rel="stylesheet" type="text/css" /> 

    <!-- 홈아이콘 -->
    <?$banner_info = info_banner("site_icon_basic",1,"data");?>
    <?php if($banner_info[0][b_img]) { ?>
    <link rel="apple-touch-icon-precomposed" href="<?=IMG_DIR_BANNER.$banner_info[0][b_img]?>" />
    <link rel="shortcut icon" href="<?=IMG_DIR_BANNER.$banner_info[0][b_img]?>" />
    <?php } ?>

	<SCRIPT src="/include/js/jquery-1.11.2.min.js"></SCRIPT>
	<SCRIPT src="/include/js/jquery.placeholder.js"></SCRIPT>
	<SCRIPT src="/include/js/jquery-migrate-1.2.1.min.js"></SCRIPT>
	<SCRIPT src="/include/js/jquery.validate.js"></SCRIPT>

	<SCRIPT src="/include/js/jquery.lightbox_me.js"></SCRIPT>

	<SCRIPT src="../include/js/default.js"></SCRIPT>

	<!-- ToolTip 적용 LDD003 { -->
	<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
	<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
	<!-- } ToolTip 적용 LDD003 -->

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
	    forced_root_block : 'p', // Needed for 3.x
	    allow_script_urls: true,
		document_base_url : "/",
		relative_urls:false,
		remove_script_host: true,
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

		$('input[type=file]').on('change',function(){
			var input = this;
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('img#img_'+input.name).attr('src',e.target.result).show();
				}
				reader.readAsDataURL(input.files[0]);
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
			<span class="en">Admin</span>
		</a>
		<!-- <div class="title"><img src="./images/title.png" alt="타이틀" title="" /></div> -->
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

	<!-- 가운데 (2단메뉴 펼침:id="container" 2단메뉴 닫음: id="container_hide") -->
	<!-- 컨텐츠 -->
	<div class="container" ID="depth2_leftmenu">

		<!-- 1단메뉴 -->
		<div class="aside_first">
		<?
		foreach($arrMenuTotalAdminVar as $key => $sub_array) {
			if(!$sub_array[first]) continue;
			$menu_url = $sub_array[menu_url] . (strstr($sub_array[menu_url],"?") ? "&" : "?") ."menu_idx=".$key;
			$app_current_menu_name = ($sub_array[menu_name1]==$arrMenuTotalAdminVar[$_COOKIE[menu_idx]][menu_name1] ? $sub_array[menu_name1] : $app_current_menu_name);
		?>
			<a href="<?=$menu_url?>" class="menu_<?=$arrMenuTotalAdmin_imgkey[$sub_array['menu_name1']]?> <?=$sub_array[menu_name1]==$arrMenuTotalAdminVar[$_COOKIE[menu_idx]][menu_name1] ? "hit" : NULL;?>" title="<?=$sub_array[menu_name1]?>"></a>
		<?
		}
		?>
		</div>
		<!-- // 1단메뉴 -->


		<!-- 2단메뉴 -->
		<div class="aside_second">
	<?PHP
	foreach($arrMenuTotalAdminVar as $key => $sub_array ){
		if($sub_array[menu_name1] != $arrMenuTotalAdminVar[$_COOKIE[menu_idx]][menu_name1]) continue;
		$menu_url = $sub_array[menu_url] . (strstr($sub_array[menu_url],"?") ? "&" : "?") ."menu_idx=".$key;
		$app_current_page_name = ($sub_array[menu_name2]==$arrMenuTotalAdminVar[$_COOKIE[menu_idx]][menu_name2] ? $sub_array[menu_name2] : $app_current_page_name);
	?>
		<a href='<?=$menu_url?>' class='menu<?=$sub_array[menu_name2]==$arrMenuTotalAdminVar[$_COOKIE[menu_idx]][menu_name2] ? "_on" : NULL;?>' title='<?=$sub_array[menu_name2]?>'><?=$sub_array[menu_name2]?></a>
	<?
	}
	?>	

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