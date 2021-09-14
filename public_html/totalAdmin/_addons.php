<?PHP

	// 메뉴 지정 변수
	$pass_menu = $_REQUEST['pass_menu'] ? $_REQUEST['pass_menu'] : "080deny/_receipt.form";
	$app_current_link = "/totalAdmin/_addons.php";
	include_once("inc.header.php");

	$_addons_menu = array(
		"080수신거부설정" => "080deny/_receipt.form", 
		"080수신거부기록관리" =>"080deny/_member_080deny.list",
		"수신동의 발송관리 (매2년)" =>"2yearOpt/_2year_opt.form",
		"이메일수신거부 문구설정" =>"emailCnf/_email_config.form",
		"스팸방지 DB패치" =>"action/_action.form",

	);
?>	

<!-- ◆◆◆◆◆◆ SMS 및 이메일 스팸관련 정보통신망법에 따른 추가작업 ◆◆◆◆◆ -->
<link href="/include/addons/action/tab_style.css" rel="stylesheet" type="text/css" />
<!-- 법령안내 -->
<div class="new_deny_guide">
	※ 본 페이지는 <strong>“불법 스팸 방지를 위한 정보통신망법”</strong> 개정에 의해 웹사이트 운영자를 위해 추가된 관리 페이지입니다. 
</div>

<!-- 내부페이지 탭메뉴 -->
<div class="new_deny_tab">
	<div class="tab_box">
		<ul>
		<?php foreach($_addons_menu as $k=>$v){ ?> 
			<li class="<?=$v==$pass_menu ? "hit":""?>">
				<a href="_addons.php?pass_menu=<?=$v?>" class="tab"><?=$k?></a>
			</li>
		<?php } ?>
		</ul>
	</div>
</div>
<!-- / 내부페이지 탭메뉴 -->
<!-- / ◆◆◆◆◆◆ SMS 및 이메일 스팸관련 정보통신망법에 따른 추가작업 ◆◆◆◆◆ -->


<?
	# 메뉴형태 => 디렉토리경로/파일명 
	if($pass_menu) {
		include_once("../include/addons/" . $pass_menu . ".php");
	}

	include_once("inc.footer.php"); 

?>