<?php

	include dirname(__FILE__)."/inc.header.php";

?>
<body>
<div class="wrap ">


<?php
	// 슬라이드 메뉴 부분
	include dirname(__FILE__)."/_slide.php";
?>

	<iframe name="common_frame" src="about:blank" style="display:none;width:0;height:0;"></iframe>


	<!-- ● 헤더타이틀 영역 -->
	<div class="header">
		<!-- 왼쪽공간 -->
		<span class="left_box">
			<a href="#none" onclick="sitemap_ctl();" title="슬라이드메뉴보기" class="btn_slide"></a>
		</span>
		<!-- 가운데공간 메인으로링크 -->
		<div class="center_box">
			<a href="index.html" class="txt_box"><strong><?=$row_company[name]?></strong></a>
		</div>
		<!-- 오른쪽공간 -->
		<span class="right_box">
			<a href="/" target="_blank" title="내홈페이지" class="btn_myhome"></a>
		</span>
	</div>
	<!-- / 헤더타이틀 영역 -->




<?php

	// 현재 페이지의 위치 추출 - inc.php => $CURR_FILENAME 정보 이용
	$app_current_link = ($app_current_link ? $app_current_link : "/totalAdmin/" . $CURR_FILENAME) ;
	$currleft_r = _MQ(" SELECT * FROM m_adm_menu WHERE m2_link = '". $app_current_link ."' AND m2_code2 != '' ");
	if(sizeof($currleft_r) == 0 ) {
		$currleft_r = _MQ(" SELECT * FROM m_adm_menu WHERE m2_vkbn = 'y' AND m2_code1 = '10' AND m2_code2 != '' ORDER BY m2_seq limit 1 "); // 관리자 메인의 맨 처음 메뉴 불러오기
	}
	// 현재 페이지 명
	if(!$app_current_page_name) $app_current_page_name = $currleft_r["m2_name2"];

?>
	<!-- ●●●●● 레이아웃 상단 -->
	<div class="common_pages_top">

		<div class="this_page_name">
			<a href="#none" onclick="history.go(-1);return false" class="btn_back" title="뒤로"><span class="shape"></span></a>

			<div class="txt"><?=$app_current_page_name?></div>



<? if(preg_match("/_order.list.php/i" , $currleft_r['m2_link'])) : ?>
			<a href="#none" onclick="view_submenu();return false" class="btn_openmenu" title="메뉴열기"><span class="shape"></span></a>
			<div class="open_menu" >
				<ul>
					<!-- 해당메뉴일때 hit -->
					<li><a href="_order.list.php" class="menu <?=($currleft_r['m2_link'] == "/totalAdmin/_order.list.php" ? "hit" : "")?>">주문관리</a></li>
					<li><a href="_order.list.php?style=b" class="menu <?=($currleft_r['m2_link'] == "/totalAdmin/_order.list.php?style=b" ? "hit" : "")?>">무통장주문관리</a></li>
				</ul>
			</div>
<script>
	// --- 슬라이드 서브메뉴 열기 ---
	function view_submenu() {
		patt = /block/g;
		if(patt.test($(".open_menu").css("display"))) {
			$(".open_menu").slideUp(300);
			$('.this_page_name').removeClass('if_open_menu');
		}
		else {
			$(".open_menu").slideDown(300);
			$('.this_page_name').addClass('if_open_menu');
		}
	}
</script>
<?endif;?>


		</div>		

	</div>
	<!-- / 레이아웃 상단 -->