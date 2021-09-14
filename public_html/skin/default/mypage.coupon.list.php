<?
	// 로그인 체크
	member_chk();

	$listmaxcount = 20; // 미입력시 20개 출력.
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$s_query = " and coID ='".get_userid()."' ";

	// 사용가능한 쿠폰 갯수
	$is_usecoupon = _MQ(" select count(*)  as cnt from odtCoupon where 1 ".$s_query." and coUse='N'");

	$res = _MQ(" select count(*)  as cnt from odtCoupon where 1 ".$s_query." ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$coupon_assoc = _MQ_assoc("select * from odtCoupon where 1 ".$s_query." order by coRegidate desc limit ".$count.", ".$listmaxcount." ");

	// - 넘길 변수 설정하기 ---
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---

	include dirname(__FILE__)."/mypage.header.php";
?>	
<div class="common_page">
<div class="layout_fix">

	<!-- 페이지 통계박스 -->
	<div class="cm_mypage_sumbox">
		<dl>
			<dt>사용가능한 쿠폰 <strong><?=number_format(_MQ_result(" select count(*) from odtCoupon where coID = '".get_userid()."' and coUse='N' "))?></strong>장</dt>
			<dd>할인쿠폰은 조건에 따라 주문 시 바로 사용가능합니다.</dd>
		</dl>
	</div>
	<!-- / 페이지 통계박스 -->

	<div class="cm_mypage_list list_coupon">
	<? if( count($coupon_assoc)==0 ) { ?>
		<!-- 내용없을경우 모두공통 -->
		<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<!-- // 내용없을경우 모두공통 -->
	<? } else { ?>
		<table summary="쿠폰목록">
			<colgroup>
				<col width="100px"/><col width="*"/><col width="100px"/><col width="200px"/><col width="140px"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">지급일</th>
					<th scope="col">쿠폰명</th>
					<th scope="col">할인금액</th>
					<th scope="col">사용가능기간</th>
					<th scope="col">쿠폰상태</th>
				</tr>
			</thead> 
			<tbody>
			<? 
				foreach($coupon_assoc as $k=>$v) { 
					if($v['coUse'] == "N"){ $coupon_status = "<span class='green'>사용가능</span>"; }
					elseif($v['coUse'] == "E"){ $coupon_status = "<span class='dark'>기간만료</span>"; }
					else{ $coupon_status = "<span class='light'>사용완료</span>"; }
			?>
				<tr>
					<td class="date"><?=date('Y.m.d',strtotime($v['coRegidate']))?></td>
					<td class="title"><?=$v['coName']?></td>
					<td class="price"><?=number_format($v['coPrice'])?>원</td>
					<td class=""><?=date('Y.m.d',strtotime($v['coRegidate']))?>~<?=date('Y.m.d',strtotime($v['coLimit']))?></td>
					<td class=""><span class="texticon_pack checkicon"><?=$coupon_status?></span></td>
				</tr>
			<? } ?>
			</tbody>
		</table>
	<? } ?>
	</div><!-- .cm_mypage_list -->

	<!-- 페이지네이트 -->
	<div class="cm_paginate">	
		<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
	</div>
	<!-- // 페이지네이트 -->

	<!-- 페이지도움말 -->
	<div class="cm_user_guide">
		<dl>
			<dt>할인쿠폰 안내</dt>
			<dd>할인쿠폰은 회원님들께 발급되며 주문 시 사용하실 수 있습니다.</dd>
			<dd>이곳에서 발급받으신 <strong>쿠폰의 금액, 내역, 기간을 확인</strong>하실 수 있습니다.</dd>
			<dd>쿠폰을 클릭하면 해당 금액만큼 상품가격이 할인되므로 저렴한 가격에 상품을 구매하실 수 있습니다.</dd>
			<dd><strong>쿠폰은 중복으로 사용이 불가능</strong>합니다.</dd>
		</dl>
	</div>

</div><!-- .layout_fix -->
</div><!-- .common_page -->