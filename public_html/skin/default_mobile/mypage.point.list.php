<?
	// 로그인 체크
	member_chk();

	// - 넘길 변수 설정하기 ---
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---	

	$page_title = "적립금";
	include dirname(__FILE__)."/mypage.header.php";
?>

<div class="common_page">
<div class="common_inner common_full">

	<!-- 페이지 통계박스 -->
	<div class="cm_mypage_sumbox">
		<dl>
			<dt>나의 총 적립금 <strong><?=number_format($row_member['point'])?></strong>원</dt>
			<dd><strong><?=number_format($row_setup['paypoint'])?></strong>원 이상 누적되었을 경우 현금처럼 사용할 수 있습니다.</dd>
		</dl>
	</div>
	<!-- / 페이지 통계박스 -->

	<div class="cm_mypage_list list_point">
		<?
		$listmaxcount = 10; // 미입력시 20개 출력
		if( !$listpg ) {$listpg = 1 ;}
		$count = $listpg * $listmaxcount - $listmaxcount;
		$s_query = " and pointID ='".get_userid()."' ";

		$res = _MQ(" select count(*)  as cnt from odtPointLog where 1 ".$s_query."");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);

		$point_assoc = _MQ_assoc("select * from odtPointLog where 1 ".$s_query." order by pointRegidate desc limit ".$count.", ".$listmaxcount."");
		if( count($point_assoc)==0 ) {
		?>
		<!-- 내용없을경우 모두공통 -->
		<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">적립된 내역이 없습니다.</div></div>
		<!-- // 내용없을경우 모두공통 -->
		<? } else { ?>
		<ul>
			<? foreach($point_assoc as $k=>$v) { ?>
			<li>
				<div class="date"><?=date('Y.m.d',strtotime($v['pointRegidate']))?></div>
				<div class="title"><?=$v['pointTitle']?></div>
				<span class="state_icon">
					<span class="texticon_pack">
					<? if($v['pointStatus']=='Y'&&$v['pointPoint']<0) { ?><span class="red">사용완료</span>
					<? } else if($v['pointStatus']=='Y') { ?><span class="blue">지급완료</span>
					<? } else { ?><span class="green">지급예정</span>
					<? } ?>
					</span>
				</span>
				<div class="double_box">
					<div class="price"><?=number_format($v['pointPoint'])?>원</div>
					<!--  해당사항없으면 안나오도록 -->
					<div class="date_soon"><?=$v['pointPoint']>0?"적립예정일 : ".date('Y.m.d',strtotime($v['redRegidate'])):""?></div>		
				</div>
			</li>
			<? } ?>
		</ul>
		<? } ?>

	</div><!-- .cm_mypage_list -->

	<div class="cm_paginate">
		<?=pagelisting_mobile($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
	</div>

	<!-- ●●●●●●●●●● 페이지 이용도움말 -->
	<div class="cm_user_guide">
		<dl>
			<dt>적립금 안내</dt>
			<?if($row_setup['paypoint_join'] > 0) {?>
			<dd><strong>회원가입시</strong> <strong><?=number_format($row_setup['paypoint_join'])?>원</strong>이 <?=$row_setup['paypoint_joindate']>0?"<strong>".$row_setup['paypoint_joindate']."일 후</strong> ":""?>지급됩니다.</dd>
			<?}?>
			<dd><strong>상품구매시</strong> 상품에 지정된 적립률만큼의 적립금이 <?=$row_setup['paypoint_productdate']>0?"<strong>".$row_setup['paypoint_productdate']."일 후</strong> ":""?>지급됩니다.</dd>
			<dd>적립금은 <strong><?=number_format($row_setup['paypoint'])?>원</strong> 이상 누적되었을 경우 현금처럼 사용할 수 있습니다 (단, 한 주문당 최고: <strong><?=number_format($row_setup['paypoint_limit'])?>원</strong>).</dd>
		</dl>
	</div>
	<!-- / 페이지 이용도움말 -->

</div><!-- .common_inner -->
</div><!-- .common_page -->