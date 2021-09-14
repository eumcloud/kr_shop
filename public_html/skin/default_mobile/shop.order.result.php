<?
/*
2015-06-15 패치
==========================================================
- KCP처리 Start
----------------------------------------------------------
*/
include_once(dirname(__FILE__)."/../../include/inc.php");
if($row_setup['P_KBN'] == 'K' && $_POST[ "res_cd"]) {
    include_once(dirname(__FILE__).'/shop.order.result_kcp.pro.php');
    exit;
}
if(!$pn) { error_loc("/m/?pn=shop.order.result"); }
/*
----------------------------------------------------------
- KCP처리 END
==========================================================
*/

// - 주문번호저장정보 추출 ---
session_start();
$ordernum = ($_SESSION["session_ordernum"] ? $_SESSION["session_ordernum"] : $_REQUEST['ordernum']);
// - 주문번호저장정보 추출 ---
// 주문정보 추출
$r = _MQ("select * from odtOrder where ordernum='". $ordernum ."' ");

/* ---------- pg 관련 상단 호출 ---------- */
switch($row_setup['P_KBN']) {
	case "L" :
		require_once(PG_M_DIR."/lgpay/lgdacom/XPayClient.php");
		break;
	case "I" :
		break;
	case "K" :
		break;
	case "A" :
		break;
	case "M" :
		break;
	case "B":
		require_once(PG_DIR."/billgate/config.php");
		break;
}
/* ---------- // pg 관련 상단 호출 ---------- */

// 업체 정보 추출
$cque = "
	select op_partnerCode,cName,id
	from odtOrderProduct as op
	left join odtProduct as p on (p.code=op.op_pcode)
	left join odtMember as m on (m.id=op.op_partnerCode)
	where op.op_oordernum='".$ordernum."'
	group by op_partnerCode
";
$cr = _MQ_assoc($cque);

$page_title = "주문결제";
include dirname(__FILE__)."/shop.header.php";
?>

<div class="common_page">
	<div class="common_inner common_full">

		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>주문</strong> 상품
			<!-- <div class="explain">주문하실 상품을 최종적으로 확인하신 후 결제를 진행해주세요.</div> -->
		</div>
		<!-- / 단락타이틀 -->

		<div class="cm_shop_cart_list">

			<?
			if( count($cr)==0 ) { error_loc_msg('/m/','잘못된 접근입니다. 메인페이지로 이동합니다.'); }
			$arr_product_sum = array();

			foreach($cr as $crk=>$crv) {
				$cque = "
					select
						code,name,prolist_img,sum(op_cnt*(op_pprice + op_poptionprice)) as sum_price,cl_title,cl_price,orderstatus_step,sum(op.op_delivery_price) as op_delivery_price,sum(op.op_add_delivery_price) as op_add_delivery_price,op_orderproduct_type,op_pname,canceled
					from odtOrder as o
					inner join odtOrderProduct as op on (o.ordernum = op.op_oordernum)
					inner join odtProduct as p on (p.code=op.op_pcode)
					left join odtOrderCouponLog as cl on (cl.cl_pcode = op.op_pcode and cl.cl_oordernum = op.op_oordernum)
					where op.op_oordernum='".$ordernum."'
					group by op_pcode
				";
				$crow = _MQ_assoc($cque);
			?>
			<!-- 입점업체 묶음 반복구간 -->
			<!-- 입점업체 (사용안하면 이 div전체 안보이게) -->
			<div class="cm_shop_entered">
				<span class="txt">업체배송</span>
				<div class="shopname"><?=$crv['cName']?></div>
				<!-- 배송비정책 -->
				<!-- <div class="charge"><b>30,000</b>원 이상 구매시 배송비 무료</div> -->
			</div>
			<div class="cart_item_list if_nocart">
				<ul>
				<?
				foreach($crow as $k=>$v) {

					/* 배송비 처리 */
					if($v['op_orderproduct_type'] != "product") {	// 배송적용 상품이 아니면
						$delivery_print = "해당없음";
						$delivery_price = 0;
						$add_delivery_print = "";
					} else {
						// 배송정보
						$delivery_price = $v['op_delivery_price'];
						$delivery_print = "-";
						// 추가배송비 여부
						unset($add_delivery_print);
						if($v['op_add_delivery_price']) {
							$add_delivery_print = "<div class='guide_txt'>추가배송비+".number_format($v['op_add_delivery_price'])."원</div>";
						}
					}
					/* 배송비 처리 끝 */

					/* 옵션 처리 */
					unset($option_html);

					$sque = "
						select * from odtOrderProduct as op
						inner join odtProduct as p on (p.code=op.op_pcode)
						left join odtOrderSettle as os on (os.os_cpid = p.customerCode and os.os_oordernum = op.op_oordernum)
						where op.op_oordernum='".$ordernum."' and op.op_pcode = '".$v['code']."' order by op_is_addoption desc,op_option1,op_option2,op_option3
					";

					$sr = _MQ_assoc($sque);
					foreach($sr as $sk => $sv) {
						/*------- 상품명 (결제시 상품명으로 사용됨) ------*/
						if(!$app_product_name)  {
							$app_product_name_tmp = $sv['op_pname'];
							$app_product_name = $sv['op_pname'];
						} else {
							$app_product_cnt++;
							$app_product_name = $app_product_name_tmp ." 외 ".$app_product_cnt."건";
						}
						$option_total_cnt ++; // 015-03-26 추가
						/*------- // 상품명 (결제시 상품명으로 사용됨) ------*/
						$option_name		= !$sv['op_option1'] ? "옵션없음" : $sv['op_option1']." ".$sv['op_option2']." ".$sv['op_option3'];
						$option_price		= $sv['op_pprice'] + $sv['op_poptionprice'];
						$option_cnt			= $sv['op_cnt'];
						$option_sum_price	= $sv['op_cnt'] * ($sv['op_pprice'] + $sv['op_poptionprice']);
						$option_html .="
							<dd class='".($sv['op_is_addoption']<>"Y"?"ess":"")."'>
								<div class='option_name'>".$option_name."</div>
								<div class='counter_box'>
									<span class='option_number'>(<strong>".number_format($option_cnt)."</strong>개)</span>
									<span class='counter_right'>
										<span class='option_price'><strong>".number_format($option_price)."</strong>원</span>
									</span>
								</div>
							</dd>
						";
						$good_info[] = array('ordr_numb'=>$ordernum,'good_name'=>$sv[name],'good_cntx'=>$sv[op_cnt],'good_amtx'=>($sv[op_pprice] + $sv[op_poptionprice]));
					}
					/* 옵션 처리 끝 */

					/* 상품 정보 */
					$pro_name	= strip_tags($v['name']);	// 상품명
					$img_src	= replace_image(IMG_DIR_PRODUCT.app_thumbnail("장바구니",$v)); // 상품 이미지
					$sum_price	= $v['sum_price']; // 옵션가격 합계

					// 상품 할인 쿠폰 기능
					unset($product_coupon_html);
					if($v['cl_title']) {
						$product_coupon_html = "
							<div class='item_coupon'>
								<div>
									<span class='white_box'>
										<span class='coupon_ti'>적용쿠폰</span>
										<span class='coupon_name'>".stripslashes($v['cl_title'])."</span>
										<span class='color_box'>
											<span class='edge1'></span><span class='edge2'></span>
											<span class='coupon_discount'>할인</span>
											<span class='coupon_price'>".number_format( $v['cl_price'] )."원</span>
										</span>
									</span>
								</div>
							</div>
						";
					}
					/* 상품 정보 끝 */
				?>
				<li>
					<!-- 상품이름과 사진 -->
					<div class="item_info">
						<a href="<?=rewrite_url($v['code'])?>" target="_blank" class="upper_link"></a>
						<!-- 상품사진 -->
						<div class="thumb"><img src="<?=product_thumb_img( $v , '장바구니' ,  'data')?>" alt="<?=$pro_name?>"/></div>
						<div class="name"><?=$pro_name?></div>
					</div>
					<!-- 상품정보 -->
					<div class="item_name">
						<dl>
						<?=$option_html?>
						</dl>
					</div>
					<!-- 상품가격(배송비) -->
					<div class="item_charge">
						<dl>
							<dd>
								<span class="opt">상품합계</span>
								<div class="value"><strong><?=number_format($sum_price)?></strong>원</div>
							</dd>
							<dd>
								<span class="opt">배송비</span>
								<div class="value">
									<? if( $delivery_price > 0 ) { ?>
										<strong><?=number_format($delivery_price)?></strong>원
									<? } else { ?>
										<?=$delivery_print?>
									<? } ?>
									<?=$add_delivery_print?>
								</div>
							</dd>
						</dl>
					</div>
					<!-- / 상품가격(배송비) -->
					<?=$product_coupon_html?>
				</li>
				<? } // 상품 loop 끝 ?>
				</ul>
			</div><!-- .cart_item_list -->

			<? } // 업체 loop 끝 ?>

		</div><!-- .cm_shop_cart_list -->

		<!-- ●●●●●●●●●● 최종계산 -->
		<div class="cm_shop_last_sum">

			<span class="box normal_box">
				<span class="icon"><span class="shape"></span></span>
				<span class="txt">상품합계금액</span>
				<span class="price"><strong><?=number_format($r['tPrice']-$r['dPrice']+$r['sPrice'])?></strong><em>원</em></span>
			</span>

			<span class="box plus_box">
				<span class="icon"><span class="shape"></span></span>
				<span class="txt">총 배송비</span>
				<span class="price"><strong><?=number_format($r['dPrice'])?></strong><em>원</em></span>
			</span>

			<span class="box minus_box">
				<span class="icon"><span class="shape"></span></span>
				<span class="txt">총 할인금액</span>
				<span class="price"><strong><?=number_format($r['sPrice'])?></strong><em>원</em></span>
			</span>

			<span class="box equal_box">
				<span class="icon"><span class="shape"></span></span>
				<span class="txt">총 결제예상금액</span>
				<span class="price"><strong><?=number_format($r['tPrice'])?></strong><em>원</em></span>
			</span>

			<? if( is_login() ) { ?>
			<div class="save_point">본 주문으로 예상되는 적립금: <strong><?=number_format($r['gGetPrice'])?></strong>원</div>
			<? } ?>
		</div>
		<!-- / 최종계산 -->

		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>주문자</strong> 정보
			<!-- <div class="explain"><img src="images/cm_images/member_form_bullet2.png" alt="필수" />표시된 것은 필수항목입니다.</div> -->
		</div>
		<!-- / 단락타이틀 -->

		<!-- ●●●●●●●●●● 주문자정보 -->
		<div class="cm_order_form if_noinput">
			<ul>
				<li class="ess">
					<span class="opt">주문자 이름</span>
					<div class="value"><?=$r['ordername']?></div>
				</li>
				<li class="ess">
					<span class="opt">주문자 휴대폰</span>
					<div class="value"><?=phone_print($r['orderhtel1'],$r['orderhtel2'],$r['orderhtel3'])?></div>
				</li>
				<li class="ess">
					<span class="opt">주문자 이메일</span>
					<div class="value"><?=$r['orderemail']?></div>
				</li>
			</ul>
		</div>

		<? if($r['order_type'] == "coupon" || $r['order_type'] == "both") { // 쿠폰상품이 있을경우에만 노출 ?>
		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>사용자</strong> 정보
			<!-- <div class="explain"><img src="images/cm_images/member_form_bullet2.png" alt="필수" />표시된 것은 필수항목입니다.</div> -->
		</div>
		<!-- / 단락타이틀 -->
		<div class="cm_order_form if_noinput">
			<ul>
				<li class="ess">
					<span class="opt">사용자 이름</span>
					<div class="value"><?=$r['username']?></div>
				</li>
				<li class="ess">
					<span class="opt">사용자 휴대폰</span>
					<div class="value"><?=phone_print($r['userhtel1'],$r['userhtel2'],$r['userhtel3'])?></div>
				</li>
				<li class="ess">
					<span class="opt">사용자 이메일</span>
					<div class="value"><?=$r['useremail']?></div>
				</li>
			</ul>
		</div>
		<? } // 사용자정보 끝 ?>

		<? if($r['order_type'] == "product" || $r['order_type'] == "both") { // 배송상품이 있을경우에만 노출 ?>
		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>받는분(배송)</strong> 정보
			<!-- <div class="explain"><img src="images/cm_images/member_form_bullet2.png" alt="필수" />표시된 것은 필수항목입니다.</div> -->
		</div>
		<!-- / 단락타이틀 -->
		<div class="cm_order_form if_noinput">
			<ul>
				<li class="ess">
					<span class="opt">받는분 이름</span>
					<div class="value"><?=$r['recname']?></div>
				</li>
				<li class="ess">
					<span class="opt">받는분 휴대폰</span>
					<div class="value"><?=phone_print($r['rechtel1'],$r['rechtel2'],$r['rechtel3'])?></div>
				</li>
				<li class="ess">
					<span class="opt">받는분 주소</span>
					<div class="value">
						<div class="text_multi">
							<dl>
								<dt>(<?=$r['reczip1']."-".$r['reczip2']?>) <?=$r['recaddress']?> <?=$r['recaddress1']?></dt>
								<dd>도로명주소 : <?=$r['recaddress_doro']?></dd>
								<dd>새 우편번호: <?=$r['reczonecode']?></dd>
							</dl>
						</div>
					</div>
				</li>
				<?php
				// LDD018
				if($r['delivery_date'] != '0000-00-00') {
				?>
				<li>
					<span class="opt">배송일 지정</span>
					<div class="value"><?php echo $r['delivery_date']; ?></div>
				</li>
				<?php } ?>
				<li class="">
					<span class="opt">배송시 유의사항</span>
					<div class="value"><?=nl2br(stripslashes($r['comment']))?></div>
				</li>
			</ul>
		</div>
		<? } // 배송지정보 끝 ?>

		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>결제</strong> 정보
			<!-- <div class="explain"><img src="images/cm_images/member_form_bullet2.png" alt="필수" />표시된 것은 필수항목입니다.</div> -->
		</div>
		<!-- / 단락타이틀 -->
		<div class="cm_order_form if_noinput">
			<ul>
				<li class="ess">
					<span class="opt">최종 결제금액</span>
					<div class="value"><strong><?=number_format($r['tPrice'])?></strong>원</div>
				</li>
				<li class="ess">
					<span class="opt">결제수단</span>
					<div class="value"><?=$arr_paymethod_name[$r['paymethod']]?></div>
				</li>
			</ul>
		</div>

		<!-- ●●●●●●●●●● 가운데정렬버튼 -->
		<?
		// PG사 결제 인증요청 페이지
		switch($row_setup['P_KBN']) {
			case "L" :
					// -- lg u+ 모듈별 처리
					if($row_setup['P_L_TYPE'] == 'W'){  // 웹표준모듈
						require_once(dirname(__FILE__)."/shop.order.result_lgpay_new.php");
					}else{
						require_once(dirname(__FILE__)."/shop.order.result_lgpay.php");
					}

				$submit_onclick = "launchCrossPlatform();";
				break;
			case "I" :
				require_once(dirname(__FILE__)."/shop.order.result_inicis.php");
				$submit_onclick = "call_pay_form();";
				break;
			case "K" :
				require_once(dirname(__FILE__)."/shop.order.result_kcp.php");
				$submit_onclick = "kcp_AJAX();";
				break;
			case "A" :
				require_once(dirname(__FILE__)."/shop.order.result_allthegate.php");
				$submit_onclick = "PayStart();";
				break;
			case "M" :
				require_once(dirname(__FILE__)."/shop.order.result_mnbank.php");
				$submit_onclick = "mn_submit();";
				break;
			case "B" :
				require_once(dirname(__FILE__)."/shop.order.result_billgate.php");
				$submit_onclick = "checkSubmit();";
				break;
		    case "D" :
		        require_once(dirname(__FILE__)."/shop.order.result_daupay.php");
		        $submit_onclick = "fnSubmit();";
		        break;
		}
		?>
		<div class="cm_bottom_button">
			<ul>
				<li><span class="button_pack"><a href="#none" onclick="<?=$submit_onclick?>return false;" title="" class="btn_lg_color">결제하기<span class="edge"></span></a></span></li>
			</ul>
		</div>
		<!-- // 가운데정렬버튼 -->

	</div><!-- .common_inner -->
</div><!-- .common_page -->
