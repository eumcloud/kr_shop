<?
	if(!is_login()) { error_loc("/m/?pn=member.login.form&path=" . enc("e",$_SERVER['QUERY_STRING'])); }

	$page_title = "마이페이지";
	include dirname(__FILE__)."/mypage.header.php";
?>

<div class="common_page">
<div class="common_inner common_full">

	<div class="cm_mypage_main">

		<!-- 바로가기 -->
		<div class="quick_btn">
			<div class="title_box">
				<span class="txt"><strong>My Shopping</strong>최근 3달 간 나의 쇼핑정보</span>
			</div>
			<ul>
				<li>
					<span class="img_box"><img src="/m/images/cm_images/mypage_main_btn1.png" alt="" /></span>
					결제대기
					<span class="number state_ready"><?=number_format(get_order_status_cnt('결제대기'))?></span>
				</li>
				<li>
					<span class="img_box"><img src="/m/images/cm_images/mypage_main_btn2.png" alt="" /></span>
					결제확인
					<span class="number state_pay"><?=number_format(get_order_status_cnt('결제확인'))?></span>
				</li>
				<li>
					<span class="img_box"><img src="/m/images/cm_images/mypage_main_btn4.png" alt="" /></span>
					발송대기상품
					<span class="number state_deliver"><?=number_format(get_order_status_cnt('발송대기'))?></span>
				</li>
				<li>
					<span class="img_box"><img src="/m/images/cm_images/mypage_main_btn3.png" alt="" /></span>
					발송완료상품
					<span class="number state_ok"><?=number_format(get_order_status_cnt('발송완료'))?></span>
				</li>
				<li>
					<span class="img_box"><img src="/m/images/cm_images/mypage_main_btn5.png" alt="" /></span>
					주문취소
					<span class="number state_cancel"><?=number_format(get_order_status_cnt('주문취소'))?></span>
				</li>
			</ul>
		</div>
		<!-- / 바로가기 -->

		<!-- 포인트/쿠폰 통계 -->
		<div class="my_stats">
			<ul>
				<li>
					<div class="inner_box">
						<span class="opt">나의 적립금</span>
						<span class="lineup">
							<span class="value"><?=number_format($row_member['point'])?></span><span class="unit">원</span>
						</span>
					</div>
					<span class="button_pack"><a href="/m/?pn=mypage.point.list" class="btn_md_white">적립금 내역보기</a></span>
				</li>
				<li>
					<? $is_usecoupon = _MQ("select count(*) as cnt from odtCoupon where coID ='".get_userid()."' and coUse='N'"); ?>
					<div class="inner_box">
						<span class="opt">나의 쿠폰함</span>
						<span class="lineup">
							<span class="value"><?=number_format($is_usecoupon['cnt'])?></span><span class="unit">장</span>
						</span>
					</div>
					<span class="button_pack"><a href="/m/?pn=mypage.coupon.list" class="btn_md_white">쿠폰 내역보기</a></span>
				</li>
			</ul>
		</div>
		<!-- / 포인트/쿠폰 통계 -->

		<!-- 주문내역 리스트 3개까지  -->
		<div class="cm_order_list">
			<!-- 메인그룹타이틀 -->
			<div class="group_title">최근주문내역<a href="/m/?pn=mypage.order.list" class="btn_all">전체보기</a></div>
			<?
				// JJC : 2020-01-31 : 속도개선
				$ordr = _MQ_assoc(" select * from odtOrder as o
					where
					orderid='".get_userid()."' and
					IF( paymethod in ('B','V') , orderstep='finish' , paystatus ='Y') and
					IF(paymethod = 'V' , ( SELECT count(*) FROM odtOrderCardlog where oc_oordernum = o.ordernum ) > 0 , 1)
					order by orderdate desc limit 3 ");
//				$ordr = _MQ_assoc(" select * from odtOrder as o
//					left join ( SELECT oc.oc_oordernum FROM odtOrderCardlog as oc group by oc.oc_oordernum ) as chk_oc on (chk_oc.oc_oordernum = o.ordernum)
//					where
//					orderid='".get_userid()."' and
//					(
//						(paymethod in ('C','L','G', 'K') and paystatus ='Y') or 
//						(paymethod in ('B','V') and orderstep='finish') 
//					) and
//					(
//						(paymethod = 'V' and chk_oc.oc_oordernum !='') or
//						(paymethod != 'V')
//					)
//					order by orderdate desc limit 3 ");
				if( count($ordr)==0 ) {
			?>
				<!-- 내용없을경우 모두공통 -->
				<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">최근 주문내역이 없습니다.</div></div>
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
					<a href="/m/?pn=mypage.order.view&ordernum=<?=$v['ordernum']?>" class="upper_link"></a>
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
							<li><span class="number"><?=$v['ordernum']?></span></li>
							<li><div class="name"><?=$app_product_name?></div></li>
						</ul>
						<ul>
							<li><span class="price">총 주문금액: <strong><?=number_format($v['tPrice'])?>원</strong></span></li>
							<li><span class="date">주문일: <?=date('Y.m.d',strtotime($v['orderdate']))?></span></li>
						</ul>
					</div>
					<!-- 주문상태 -->
					<?=$arr_o_status_main[$v['orderstatus_step']]?>
				</dd>
				<dt>
					<div class="btn_box">
						<ul>
							<li><span class="button_pack"><a href="/m/?pn=mypage.order.view&ordernum=<?=$v['ordernum']?>" class="btn_md_black">주문상세</a></span></li>
							<? if($v['canceled'] != "Y" && $v['paystatus2'] == "N" && !in_array($v['orderstatus_step'] , array('발송완료' , '발급완료')) ) { if($v['mem_cancelchk'] == "Y") { ?>
							<li><span class="button_pack"><a href="#none" onclick="order_cancel('<?=$v['ordernum']?>');return false;" class="btn_md_white">주문취소</a></span></li>
							<? }} ?>
							<!-- <li><span class="button_pack"><a href="" class="btn_sm_white">교환문의</a></span></li> -->
						</ul>
					</div>
				</dt>
			</dl>
			<? if( count($app_product_list)>1 ) { ?>
			<script>
			$(window).on('load',function(){
				$('.mypage_main_product_slider_<?=$k?>').bxSlider({
					auto: true, autoHover: false, speed: 700, mode: 'vertical',
					slideSelector: '', easing: 'easeInOutCubic', useCSS: false,
					slideMargin: 0, slideWidth: 0, minSlides: 1, maxSlides: 1,
					pager: false, controls: false
				});
			});
			</script>
			<? } ?>
			<? }} ?>
		</div><!-- .cm_order_list -->

		<!-- 최근게시물 (모두 한줄제한)  -->
		<div class="recent_board">
			<ul>
				<li>

					<!-- 찜한상품 -->
					<div class="board_wish">
						<!-- 메인그룹타이틀 -->
						<div class="group_title">찜한상품<a href="/m/?pn=mypage.wish.list" class="btn_all">전체보기</a></div>
						<?
							$pwque = "
								select
									pw.*, p.* ,
									(select count(*) from odtProductWish as pw2 where pw2.pw_pcode=pw.pw_pcode) as cnt_product_wish
								from odtProductWish as pw
								left join odtProduct as p on ( p.code=pw.pw_pcode )
								where pw.pw_inid='". get_userid() ."'
								order by pw_uid desc limit 0, 6
							";
							$pwr = _MQ_assoc($pwque);
							if( count($pwr)==0 ) {
						?>
						<!-- 내용없을경우 모두공통 -->
						<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">찜한 상품이 없습니다.</div></div>
						<!-- // 내용없을경우 모두공통 -->
						<? } else { ?>
						<!-- 4개까지 없으면 div전체 안보임 -->
						<div class="wish_box">
							<ul>
							<? foreach($pwr as $k=>$v) { ?>
							<li>
								<div class="item_box">
									<a href="<?=rewrite_url($v['code'])?>" class="upper_link" title="<?=$v['name']?>"></a>
									<span class="item_thumb"><? if($v['prolist_img']){ ?><img src="<?=replace_image(IMG_DIR_PRODUCT.app_thumbnail('장바구니',$v))?>" alt="<?=$v['name']?>" title="" /><? } ?></span>
									<dl>
										<dt><?=$v['name']?></dt>
										<dd><?=number_format($v['price'])?>원</dd>
									</dl>
								</div>
							</li>
							<? } ?>
							</ul>
						</div>
						<? } ?>
					</div>

				</li>
				<li>

					<!-- 1:1온라인문의 -->
					<div class="board_inquiry">
						<!-- 메인그룹타이틀 -->
						<div class="group_title">문의내역<a href="/m/?pn=mypage.posting.list" class="btn_all">전체보기</a></div>
						<?
							$post_assoc = _MQ_assoc("select * from odtTt where ttID ='".get_userid()."' and ttIsReply != '1' order by ttRegidate desc limit 0, 5 ");
							if( count($post_assoc)==0 ) {
						?>
						<!-- 내용없을경우 모두공통 -->
						<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
						<!-- // 내용없을경우 모두공통 -->
						<? } else { ?>
						<!-- 5개까지 : 글이 없으면 div전체 안보임 -->
						<div class="list_box">
							<dl>
							<? foreach($post_assoc as $k=>$v) { $reply_r = _MQ_result("select count(*) from odtTt where ttIsReply='1' and ttSNo = '".$v['ttNo']."'"); ?>
								<dd>
									<a href="/m/?pn=mypage.posting.list&_uid=<?=$v['ttNo']?>" class="upper_link" title="<?=strip_tags(nl2br($v['ttContent']))?>"></a>
									<div class="title"><span class="date"><?=date('y-m-d',strtotime($v['ttRegidate']))?></span><?=strip_tags(nl2br($v['ttContent']))?></div>
									<span class="texticon_pack">
										<? if($reply_r>0) { ?><span class="dark">답변완료</span>
										<? } else { ?><span class="light">답변대기</span>
										<? } ?>
									</span>
								</dd>
							<? } ?>
							</dl>
						</div>
						<? } ?>
					</div>

				</li>
			</ul>
		</div>
		<!-- / 최근게시물 -->

	</div><!-- .cm_mypage_main -->

</div><!-- .common_inner -->
</div><!-- .common_page -->

<script>
// 주문취소
function order_cancel(ordernum){
	if( confirm('정말 주문을 취소하시겠습니까?') ) {
		common_frame.location.href=("/pages/mypage.order.pro.php?_mode=cancel&ordernum=" + ordernum + "&_PVSC=<?=$_PVSC?>");
	}
}
</script>