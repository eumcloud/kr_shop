<?
	// 로그인 체크
	//member_chk();

	// LDD019 {
	if($row_setup['none_member_buy'] == 'N' && !is_login()) {
		error_msg("로그인 후 이용 가능합니다.");
	}
	// } LDD019

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	// 검색정보 쿠키처리 $_PVS에서 제외
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { if(!in_array($key , array("pass_name" , "pass_ordernum"))){ $_PVS .= "&$key=$val"; } }
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기

	// 검색 체크
	$pass_name = $_COOKIE["guest_order_name"];
	$pass_ordernum = $_COOKIE["guest_order_num"];
?>
<div class="common_page common_none">

	<!-- ●●●●●●●●●● 타이틀상단 -->
	<div class="cm_common_top">
		<div class="commom_page_title">
			<span class="icon_img"><img src="/pages/images/cm_images/icon_top_guest.png" alt="" /></span>
			<dl>
				<dt>비회원 주문조회</dt>
				<dd>회원으로 쇼핑몰을 이용하시면 더 많은 혜택을 받으실 수 있습니다.</dd>
			</dl>
		</div>
	</div>
	<!-- / 타이틀상단 -->

</div>

<div class="common_page">
<div class="layout_fix">

	<!-- ●●●●●●●●●● 비회원주문검색 -->
	<div class="cm_guest_order">
		<div class="inner_box">

			<div class="gtxt_box">
				<div class="telnumber"><?=$row_company['tel']?></div>
				비회원 주문 시 입력하신 주문인 이름과 주문 번호를  입력하세요.<br/>
				정보가 기억나지 않으시면 <b>고객센터</b>로 직접 연락주시길 바랍니다.
			</div>

			<div class="search_form">
				<form name="guest_order_frm" id="guest_order_frm" method="post" action="/pages/service.guest.order.pro.php" target="common_frame">
				<input type="hidden" name="_mode" value="guest_search" />
				<div class="input_box">
					<input type="search" name="pass_name" class="input_design input_name" placeholder="주문자 이름" value="<?=$pass_name?$pass_name:''?>"/>
					<input type="search" name="pass_ordernum" class="input_design input_number" placeholder="주문자 주문번호" value="<?=$pass_ordernum?$pass_ordernum:''?>"/>
				</div>
				<input type="submit" name="" class="btn_search" value="비회원 주문내역조회" />
				</form>
			</div>

		</div>
	</div>	
	<!-- / 비회원주문검색 -->

	<? if(!$pass_name || !$pass_ordernum){ ?>
	<!-- 페이지 이용도움말 -->
	<div class="cm_user_guide">
		<dl>
			<dt>비회원 주문조회 안내</dt>
			<dd>비회원으로 상품을 구매하신 경우에만 주문/배송조회가 가능합니다.</dd>
			<dd>비회원 구매 시에는 쇼핑몰의 할인/적립 혜택을 받으실 수 없습니다.</dd>
			<dd>주문정보가 기억나지 않으실 경우, <strong>고객센터</strong>를 통해 문의주세요.</dd>
		</dl>
	</div>
	<!-- / 페이지 이용도움말 -->
	<? } ?>

	<? 
		if($pass_name && $pass_ordernum){ 

			$s_query = " 
				,IF( paymethod =  'V', (SELECT COUNT( * ) FROM odtOrderCardlog WHERE oc_oordernum = ordernum ) , 1) as vbank_finsh
				from odtOrder as o 
				where 
					ordername='".addslashes($pass_name)."' and member_type = 'guest'
					and  replace(ordernum,'-','') = '".addslashes(rm_str($pass_ordernum))."'
					and( (paymethod in ('C','L','G') and paystatus ='Y' and canceled='N') 
					or (paymethod in ('B','V') && orderstep='finish' and canceled='N') ) 
				having  vbank_finsh > 0 ";

			$listmaxcount = 20;
			if( !$listpg ) {$listpg = 1 ;}
			$count = $listpg * $listmaxcount - $listmaxcount;

			$res = _MQ(" select count(*) as cnt ".$s_query." ");
			$TotalCount = $res['cnt'];
			$Page = ceil($TotalCount / $listmaxcount);

			$que = " 
				select 
					o.* ,
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

			$res = _MQ_assoc($que);
	?>
	<!-- 주문내역 리스트  -->
	<div class="cm_order_list">
		<? if( count($res)==0 ) { ?>
		<!-- 내용없을경우 모두공통 -->
		<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">검색된 주문내역이 없습니다.</div></div>
		<!-- // 내용없을경우 모두공통 -->
		<? 
			} else { 
			foreach($res as $k=>$v) {
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
				<a href="/?pn=service.guest.order.view&ordernum=<?=$v['ordernum']?>&_PVSC=<?=$_PVSC?>" class="upper_link"><img src="/pages/images/cm_images/blank.gif" alt="" /></a>
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
						<li><span class="number">주문번호 : <?=$v['ordernum']?></span></li>
						<li><strong><?=$app_product_name?></strong></li>
						<li><span class="price">총 주문금액: <strong><?=number_format($v['tPrice'])?>원</strong></span></li>
						<li><span class="date">주문일: <?=date('Y.m.d',strtotime($v['orderdate']))?></span></li>
						<? if($v['canceled']=='Y') { ?><li><span class="date">취소일: <?=date('Y.m.d',$v['canceldate'])?></span></li><? } ?>
					</ul>
				</div>
				<!-- 주문상태 -->
				<?=$arr_o_status_main[$v['orderstatus_step']]?>
				<span class="btn_box">
					<span class="button_pack"><a href="/?pn=service.guest.order.view&ordernum=<?=$v['ordernum']?>&_PVSC=<?=$_PVSC?>" class="btn_sm_black">주문상세</a></span>
					<? if($v['canceled'] != "Y" && $v['paystatus2'] == "N" && !in_array($v['orderstatus_step'] , array('발송완료' , '발급완료')) ) { if($v['mem_cancelchk'] == "Y") { ?>
					<span class="button_pack"><a href="#none" onclick="order_cancel('<?=$v['ordernum']?>');return false;" class="btn_sm_white">주문취소</a></span>
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
		<? } ?>
	</div>
	<!-- / 주문내역 -->


	<? }} ?>

</div><!-- .layout_fix -->
</div><!-- .common_page -->

<script>
$(document).ready(function(){
	$('input[name=pass_name]').focus();
	$("#guest_order_frm").validate({
		rules: {
			pass_name	: { required: true },
			pass_ordernum	: { required: true }
		},
		messages: {
			pass_name	: { required: "주문자명을 입력하세요." },
			pass_ordernum	: { required: "주문자 주문번호를 입력하세요.." }
		}
	});
});

// 주문취소
function order_cancel(ordernum){
	if( confirm('정말 주문을 취소하시겠습니까?') ) {
		common_frame.location.href=("/pages/service.guest.order.pro.php?_mode=cancel&ordernum=" + ordernum + "&_PVSC=<?=$_PVSC?>");
	}
}
</script>