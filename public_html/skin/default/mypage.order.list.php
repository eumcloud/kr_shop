<?
	// 로그인 체크
	member_chk();

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기

	// 검색 체크
	$s_query = " 
		from odtOrder as o 
		where 
			orderid='".get_userid()."' and
			IF( paymethod in ('B','V') , orderstep='finish' , paystatus ='Y') and
			IF(paymethod = 'V' , ( SELECT count(*) FROM odtOrderCardlog where oc_oordernum = o.ordernum ) > 0 , 1)
	";

	include dirname(__FILE__)."/mypage.header.php";
?>
<div class="common_page">
<div class="layout_fix">

	<!-- ●●●●●●●●●● 페이지 통계박스 -->
	<?
	$order_info = _MQ("
		SELECT ifnull(sum((p.price_org+op.op_poptionprice) * op.op_cnt),0) as sum_org , ifnull(sum((op.op_pprice + op.op_poptionprice) * op.op_cnt),0) as sum_price 
		FROM odtOrderProduct as op 
		inner join odtProduct as p on ( p.code=op.op_pcode )
		inner join odtOrder as o on ( o.ordernum=op.op_oordernum )
		where o.orderid = '".get_userid()."' and o.canceled ='N'  and (o.paystatus='Y' || (o.paymethod in ('B','E') && o.orderstep='finish' ))
		");
	$save_price = $order_info['sum_org'] - $order_info['sum_price'];
	?>
	<div class="cm_mypage_sumbox">
		<dl>
			<dt>총 절약금액 <strong><?=number_format($save_price)?></strong>원</dt>
			<dd>절약금액(<?=number_format($save_price)?>원) = 정상가격(<?=number_format($order_info[sum_org])?>원) - 할인구매가격(<?=number_format($order_info[sum_price])?>원)</dd>
		</dl>
	</div>
	<!-- / 페이지 통계박스 -->

	<!-- ●●●●●●●●●● 주문내역조회 -->
	<div class="cm_order_search">

		<?
			$_period = $_period==''&&$_period_s==''&&$_period_e=='' ? 'all' : $_period;
			if( $_period=='all' ) { 
				$_period_s = _MQ_result("select count(*) ".$s_query)>0 ? date('Y-m-d',strtotime(_MQ_result("select orderdate ".$s_query." order by orderdate asc limit 1"))) : date('Y-m-d'); 
				$_period_e = _MQ_result("select count(*) ".$s_query)>0 ? date('Y-m-d',strtotime(_MQ_result("select orderdate ".$s_query." order by orderdate desc limit 1"))) : date('Y-m-d');
			}
			if( $_period=='today' ) { $_period_s = date('Y-m-d'); $_period_e = $_period_s; } // 오늘
			if( $_period=='week' ) { $_period_s = date('Y-m-d',strtotime("- 1 week")); $_period_e = date('Y-m-d'); } // 1주일
			if( $_period=='month' ) { $_period_s = date('Y-m-d',strtotime("- 1 month")); $_period_e = date('Y-m-d'); } // 1개월
			if( $_period=='three' ) { $_period_s = date('Y-m-d',strtotime("- 3 month")); $_period_e = date('Y-m-d'); } // 3개월
		?>

		<!-- 기간검색 -->
		<div class="period">
			<a href="/?pn=<?=$pn?>&_mode=<?=$_mode?>&_period=all" class="btn <?=$_period=='all'?'hit':''?>">전체</a>
			<a href="/?pn=<?=$pn?>&_mode=<?=$_mode?>&_period=today" class="btn <?=$_period=='today'?'hit':''?>">오늘</a>
			<a href="/?pn=<?=$pn?>&_mode=<?=$_mode?>&_period=week" class="btn <?=$_period=='week'?'hit':''?>">1주일</a>
			<a href="/?pn=<?=$pn?>&_mode=<?=$_mode?>&_period=month" class="btn <?=$_period=='month'?'hit':''?>">1개월</a>
			<a href="/?pn=<?=$pn?>&_mode=<?=$_mode?>&_period=three" class="btn <?=$_period=='three'?'hit':''?>">3개월</a>
		</div>

		<!-- 직접입력검색 -->
		<div class="detail">
			<form name="form_period">
			<input type="hidden" name="pn" value="<?=$pn?>"/><input type="hidden" name="_mode" value="<?=$_mode?>"/>
			<input type="text" name="_period_s" id="s_date" readonly value="<?=$_period_s?>" class="input_date" style="position:relative;z-index:10;"/>
			<span class="dash">~</span>
			<input type="text" name="_period_e" id="e_date" readonly value="<?=$_period_e?>" class="input_date" style="position:relative;z-index:10;"/>
			<span class="button_pack"><a href="#none" onclick="return false;" class="submit_period btn_md_black">조회하기</a></span>
			</form>
		</div>
		<script>
		$(document).ready(function(){
			$('.submit_period').on('click',function(){
				var s_date = new Date($('input[name=_period_s]').val()), e_date = new Date($('input[name=_period_e]').val());
				if( $('input[name=_period_s]').val()=='' || $('input[name=_period_e]').val()=='' ) { alert('검색하실 날짜를 입력하세요.'); return false; }
				if( e_date < s_date ) { alert('시작일이 종료일보다 클 수 없습니다.'); return false; }
				$('form[name=form_period]').submit();
			});
		});
		</script>

	</div>
	<!-- / 주문내역조회 -->

	<!-- ●●●●●●●●●● 카테고리있을경우 게시판메뉴 -->
	<?
	if( $_period_s != '' ) { $s_query .= " and left(orderdate,10) >= '".$_period_s."' "; }
	if( $_period_e != '' ) { $s_query .= " and left(orderdate,10) <= '".$_period_e."' "; }
	$all_cnt = _MQ_result(" select count(*) ".$s_query."");
	$on_cnt = _MQ_result(" select count(*) ".$s_query." and canceled='N' and orderstatus='Y' and orderstatus_step!='발송완료' and orderstatus_step!='발급완료' ");
	$cancel_cnt = _MQ_result(" select count(*) ".$s_query." and canceled='Y' ");
	$done_cnt = _MQ_result(" select count(*) ".$s_query." and orderstatus='Y' and (orderstatus_step='발송완료' or orderstatus_step='발급완료') ");
	?>
	<div class="cm_mypage_tab">
		<a href="/?pn=<?=$pn?>&_mode=all&_period=<?=$_period?>&_period_s=<?=$_period_s?>&_period_e=<?=$_period_e?>" class="<?=$_mode=='all'?'hit':''?>">전체주문 (<?=number_format($all_cnt)?>)</a>
		<a href="/?pn=<?=$pn?>" class="<?=!$_mode?'hit':''?>">진행중인주문 (<?=number_format($on_cnt)?>)</a>
		<a href="/?pn=<?=$pn?>&_mode=done&_period=<?=$_period?>&_period_s=<?=$_period_s?>&_period_e=<?=$_period_e?>" class="<?=$_mode=='done'?'hit':''?>">완료된주문 (<?=number_format($done_cnt)?>)</a>
		<a href="/?pn=<?=$pn?>&_mode=cancel&_period=<?=$_period?>&_period_s=<?=$_period_s?>&_period_e=<?=$_period_e?>" class="<?=$_mode=='cancel'?'hit':''?>">취소된주문 (<?=number_format($cancel_cnt)?>)</a>
	</div>
	<!-- //카테고리있을경우 게시판메뉴 -->

	<!-- 주문내역 리스트 3개까지  -->
	<div class="cm_order_list">
		<?
			if( $_mode == 'all' ) { $s_query .= ""; }
			if( $_mode == '' ) { $s_query .= " and canceled='N' and orderstatus='Y' and orderstatus_step!='발송완료' and orderstatus_step!='발급완료' "; }
			if( $_mode == 'cancel' ) { $s_query .= " and canceled='Y' "; }
			if( $_mode == 'done' ) { $s_query .= " and orderstatus='Y' and (orderstatus_step='발송완료' or orderstatus_step='발급완료') "; }
			if( $_period_s != '' ) { $s_query .= " and left(orderdate,10) >= '".$_period_s."' "; }
			if( $_period_e != '' ) { $s_query .= " and left(orderdate,10) <= '".$_period_e."' "; }

			$listmaxcount = 20 ;
			if( !$listpg ) {$listpg = 1 ;}
			$count = $listpg * $listmaxcount - $listmaxcount;

			$res = _MQ(" select count(*) as cnt ".$s_query." ");
			$TotalCount = $res['cnt'];
			$Page = ceil($TotalCount / $listmaxcount);

			$que = " 
				select 
					o.*,
					(select count(*) from odtOrderProduct as op where op.op_oordernum=o.ordernum) as op_cnt,
					(
						select
							p.name
						from odtOrderProduct as op 
						inner join odtProduct  as p on ( p.code=op.op_pcode ) 
						where op.op_oordernum=o.ordernum order by op.op_uid asc limit 1
					) as name
				".$s_query."
				ORDER BY orderdate desc limit ".$count.", ".$listmaxcount."
			";
			$ordr = _MQ_assoc($que);
			if( count($ordr)==0 ) {
		?>
			<!-- 내용없을경우 모두공통 -->
			<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">주문내역이 없습니다.</div></div>
			<!-- // 내용없을경우 모두공통 -->
		<?
			} else {
			foreach($ordr as $k=>$v) {
				$app_product_list = array();
				$app_product_list = _MQ_assoc(" 
					select p.name, p.prolist_img, op.*, sum( op_cnt * (op_pprice + op_poptionprice) ) as op_tPrice
					from odtOrderProduct as op 
					left join odtProduct as p on (p.code=op.op_pcode) where op_oordernum = '".$v['ordernum']."' group by op_pcode order by op_uid asc
				");
				$app_product_name = $app_product_list[0]['name'];
				if( count($app_product_list)>1 ) { $app_product_name .= " 외 ".(count($app_product_list)-1)."개 "; }
		?>
		<dl>
			<dd>
				<a href="/?pn=mypage.order.view&ordernum=<?=$v['ordernum']?>&_PVSC=<?=$_PVSC?>" class="upper_link"><img src="/pages/images/cm_images/blank.gif" alt="" /></a>
				<!-- 상품정보 -->
				<span class="item_thumb">
					<div class="mypage_main_product_slider_<?=$k?>">
						<? foreach($app_product_list as $apk=>$apv) { ?>
						<img src="<?=product_thumb_img( $apv , '장바구니' ,  'data')?>" alt="<?=$apv['name']?>"/>
						<? } ?>
					</div>
				</span>
				<div class="item_info">
					<ul>
						<li><span class="number">주문번호: <?=$v['ordernum']?></span></li>
						<li><strong><?=$app_product_name?></strong></li>
						<li><span class="price">총 주문금액: <strong><?=number_format($v['tPrice'])?>원</strong></span></li>
						<li><span class="date">주문일: <?=date('Y.m.d',strtotime($v['orderdate']))?></span></li>
						<? if($v['canceled']=='Y') { ?><li><span class="date">취소일: <?=date('Y.m.d',$v['canceldate'])?></span></li><? } ?>
					</ul>
				</div>
				<!-- 주문상태 -->
				<?=$arr_o_status_main[$v['orderstatus_step']]?>
				<span class="btn_box">
					<span class="button_pack"><a href="/?pn=mypage.order.view&ordernum=<?=$v['ordernum']?>&_PVSC=<?=$_PVSC?>" class="btn_sm_black">주문상세</a></span>
					<? if($v['canceled'] != "Y" && $v['paystatus2'] == "N" && !in_array($v['orderstatus_step'] , array('발송완료' , '발급완료')) ) { if($v['mem_cancelchk'] == "Y") { ?>
						<?
							$cancel_chk = _MQ_result(" select count(*) from odtOrderProduct where op_oordernum = '".$v['ordernum']."' and op_cancel != 'N' ");
							if( $cancel_chk > 0 ) { 
						?>
						<span class="button_pack"><a href="#none" onclick="alert('취소/반품/교환 요청중인 상품이 있습니다. 개별상품 단위로 요청해주시기 바랍니다.');return false;" class="btn_sm_white">주문취소</a></span>
						<? } else { ?>
						<span class="button_pack"><a href="#none" onclick="order_cancel('<?=$v['ordernum']?>');return false;" class="btn_sm_white">주문취소</a></span>
						<? } ?>
					<? } else { ?>
					<span class="button_pack"><a href="#none" onclick="alert('주문취소가 불가능한 상태입니다. 고객센터(<?=$row_company[tel]?>)로 문의하세요.');return false;" class="btn_sm_white">주문취소</a></span>
					<? }} ?>
					<!-- <span class="button_pack"><a href="" class="btn_sm_white">교환문의</a></span> -->
				</span>
			</dd>
		</dl>
		<? if( count($app_product_list)>1 ) { ?>
		<script>
		$(window).on('load',function(){
			$('.mypage_main_product_slider_<?=$k?>').bxSlider({
				auto: true, autoHover: false, speed: 700, mode: 'fade',
				slideSelector: '', easing: 'easeInOutCubic', useCSS: false,
				slideMargin: 0, slideWidth: 0, minSlides: 1, maxSlides: 1,
				pager: false, controls: false
			});
		});
		</script>
		<? } ?>
		<? }} ?>
	</div><!-- .cm_order_list -->

	<!-- 페이지네이트 -->
	<div class="cm_paginate">	
		<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
	</div>
	<!-- // 페이지네이트 -->


</div><!-- .layout_fix -->
</div><!-- .common_page -->

<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
$(function() {
	$( "#s_date" ).datepicker({ changeMonth: true, changeYear: true });
	$( "#s_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
	$( "#s_date" ).datepicker( "option",$.datepicker.regional["ko"] );
	$( "#e_date" ).datepicker({ changeMonth: true, changeYear: true });
	$( "#e_date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
	$( "#e_date" ).datepicker( "option",$.datepicker.regional["ko"] );
});

// 주문취소
function order_cancel(ordernum){
	if( confirm('정말 주문을 취소하시겠습니까?') ) {
		common_frame.location.href=("/pages/mypage.order.pro.php?_mode=cancel&ordernum=" + ordernum + "&_PVSC=<?=$_PVSC?>");
	}
}
</script>