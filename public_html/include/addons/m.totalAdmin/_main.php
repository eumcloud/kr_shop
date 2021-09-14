<?php

	include dirname(__FILE__)."/inc.header.php";

?>
<body>
<div class="wrap">
	

	<!-- ● 메인 -->
	<div class="main_section">

		<div class="myinfo">
			
			<!-- 상단영역 -->
			<div class="myinfo">
				<!-- <span class="imgicon">
					<span class="icon1"></span>
					<span class="icon2"></span>
				</span> -->
				<div class="title_en">TOTAL ADMIN</div>
				<dl>					
					<dt><?=$row_setup['site_name']?></dt>
					<dd><a href="http://<?=str_replace("http://" , "" , $row_company['homepage'])?>" target="_blank" class="link"><?=$row_company['homepage']?></a></dd>
				</dl>
			</div>	

			<div class="btn_go_box">
				<ul>
					<li><a href="/" target="_blank" class="btn ic_home">내홈페이지</a></li>
					<li><a href="/totalAdmin/?_pcmode=chk&<?=str_replace('_mobilemode=chk','',$_SERVER['QUERY_STRING'])?>" class="btn ic_pc">PC버전보기</a></li>
					<li><a href="logout.php" class="btn ic_logout">로그아웃</a></li>
				</ul>
			</div>

		</div>

		<!-- 메인에서 메뉴바로가기 1차메뉴 -->
		<div class="admin_menu">
			<ul>
<?php
	// 메뉴별 권한 체크
	$arr_save_link = array();
	$f_link = _MQ_assoc(" SELECT am.m2_link FROM m_menu_set as ms inner join m_adm_menu as am on(ms.m15_code1 = am.m2_code1 and ms.m15_code2 = am.m2_code2)   WHERE ms.m15_id = '" . $row_admin['id'] . "' and ms.m15_vkbn = 'Y' and ms.m15_code2 != '' and am.m2_link in ('". implode("' , '" , $arr_menu_link) ."') order by am.m2_seq asc, ms.m15_code1 asc, ms.m15_code2 asc ");
	foreach($f_link as $k=>$v){ $arr_save_link[$v['m2_link']]++; }
?>
				
				<?if( in_array("/totalAdmin/_product.list.php" , array_keys($arr_save_link)) ) : ?><li><a href="_product.list.php" class="menu"><img src="<?=PATH_MOBILE_TOTALADMIN?>/images/mainmenu1.png" alt="대메뉴아이콘" /><span class="txt">상품관리</span></a></li><?endif;?>

				<?if( in_array("/totalAdmin/_order.list.php" , array_keys($arr_save_link)) ) : ?><li><a href="_order.list.php" class="menu"><img src="<?=PATH_MOBILE_TOTALADMIN?>/images/mainmenu2.png" alt="대메뉴아이콘" /><span class="txt">주문관리</a></span></li><?endif;?>

				<?if( in_array("/totalAdmin/_request.list.php?pass_menu=request" , array_keys($arr_save_link)) ) : ?><li><a href="_request.list.php?pass_menu=request" class="menu"><img src="<?=PATH_MOBILE_TOTALADMIN?>/images/mainmenu3.png" alt="대메뉴아이콘" /><span class="txt">1:1문의</a></span></li><?endif;?>

			</ul>
		</div>
		<!-- / 메인에서 메뉴바로가기 -->


		<!-- 메인간략통계 : 사용안함
		<div class="admin_state">
			<ul>
				<li>
					<div class="title_box"><span class="txt">최근 한달 총매출액</span></div>
					<span class="value"><span class="unit">￦</span>5,694,900</span>
				</li>
				<li>
					<div class="title_box"><span class="txt">최근 일주일 1:1문의</span></div>
					<span class="value">12 <a href="" class="btn_ready">답변대기 0</a></span>
				</li>
				<li>
					<div class="title_box"><span class="txt">등록된 총 상품개수</span></div>
					<span class="value">2,974</span>
				</li>
			</ul>		
		</div>
		-->

		<div class="user_guide_box">
			관리자페이지 모바일 버전은 중요한 기능을 빠르게 관리하기 위한 제한적인 서비스를 제공하고 있습니다. 모든 관리를 위해서는 PC버전을 이용해주세요.
		</div>

	</div>
	<!-- / 메인 -->



	<!-- 푸터 -->
	<div class="footer">
		<div class="copyright">Copyright © <?=substr(rm_str($row_setup['licenseNumber']),0,4) ." ". $row_setup['site_name']?>. All Rights Reserved.</div>
	</div>
	<!-- /푸터 -->
	



</div>
</body>
<?php

	include dirname(__FILE__)."/inc.footer.php";

?>