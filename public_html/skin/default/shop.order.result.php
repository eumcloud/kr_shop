<?
// - 주문번호저장정보 추출 ---
$ordernum = $_SESSION["session_ordernum"];//주문번호
// - 주문번호저장정보 추출 ---

/* ---------- pg 관련 상단 호출 ---------- */
switch($row_setup['P_KBN']) {
	case "L" : 
		require_once(PG_DIR."/lgpay/lgdacom/XPayClient.php");
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

// 주문정보 추출
$r = _MQ("select * from odtOrder where ordernum='". $ordernum ."' ");
?>
<div class="common_page common_none">

	<!-- ●●●●●●●●●● 타이틀상단 -->
	<div class="cm_common_top">
		<div class="commom_page_title">
			<span class="icon_img"><img src="/pages/images/cm_images/icon_top_order.png" alt="" /></span>
			<dl>
				<dt>주문결제</dt>
				<dd>최종적으로 주문하실 상품을 확인하고 주문결제를 해주세요.</dd>
			</dl>
		</div>

		<!-- 단계별 페이지가 있을경우 -->
		<div class="progress">
			<span class="box "><strong>STEP.1</strong>장바구니</span>
			<span class="box hit"><strong>STEP.2</strong>주문결제</span>
			<span class="box"><strong>STEP.3</strong>주문완료</span>
		</div>

	</div>
	<!-- / 타이틀상단 -->

	<div class="cm_shop_ok_message" style="padding: 0 30px;">
		<!-- 페이지 이용도움말 -->
		<div class="cm_user_guide">
			<dl>
				<dt>알려드립니다!</dt>
				<dd><strong>본 화면에서는 새로고침(F5)또는 뒤로가기 버튼을 클릭하지 마십시오.</strong></dd>
				<dd>위와같은 동작으로 중복결제가 발생 할 수 있습니다.</dd>
			</dl>
		</div>
		<!-- / 페이지 이용도움말 -->
	</div>


	<div class="common_page">
	<div class="layout_fix">

		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>주문</strong> 상품
			<!-- <div class="explain">주문하실 상품을 최종적으로 확인하신 후 결제를 진행해주세요.</div> -->
		</div>
		<!-- / 단락타이틀 -->
		<div class="cm_shop_cart_list">
			<table summary="상품리스트">
				<colgroup>
					<col width="*"/><col width="12%"/><col width="13%"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col">상품정보</th>
						<th scope="col">상품금액</th>
						<th scope="col">배송비</th>
					</tr>
				</thead> 
				<tbody>
				<?
					// 주문상품 정보 추출
					$cque = "
						select code,name,prolist_img,sum(op_cnt*(op_pprice + op_poptionprice)) as sum_price,cl_title,cl_price,sum(op.op_delivery_price) as op_delivery_price,sum(op.op_add_delivery_price) as op_add_delivery_price,op_orderproduct_type from odtOrderProduct as op 
						inner join odtProduct as p on (p.code=op.op_pcode)
						left join odtOrderCouponLog as cl on (cl.cl_pcode = op.op_pcode and cl.cl_oordernum = op.op_oordernum)
						where op.op_oordernum='".$ordernum."'
						group by op_pcode
					";
					$cr = _MQ_assoc($cque);
					if( count($cr)==0 ) { error_loc_msg('/','필수 정보가 누락되었습니다. 메인페이지로 이동합니다.'); }

					foreach($cr as $k=>$v) {

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
								$add_delivery_print = "<br/>추가배송비<br/>+".number_format($v['op_add_delivery_price'])."원";
							}
						}
						/* 배송비 처리 끝 */

						/* 옵션 처리 */
						unset($option_html);				

						$sque = "
							select * from odtOrderProduct as op 
							inner join odtProduct as p on (p.code=op.op_pcode)
							left join odtOrderSettle as os on (os.os_cpid = p.customerCode and os.os_oordernum = op.op_oordernum)
							where op.op_oordernum='".$ordernum."' and op.op_pcode = '".$v['code']."' 
						";
						//order by op_is_addoption desc,op_option1,op_option2,op_option3
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
										<span class='option_price'><strong>".number_format($option_price)."</strong>원</span>
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
									<span class='txt_icon'>COUPON</span><span class='one_coupon'>".stripslashes($v['cl_title'])." 적용 (<strong>".number_format($v['cl_price'])."</strong>원 할인)</span>
								</div>
							"; 
						}
						/* 상품 정보 끝 */

						// 배송비 정책
						$arr_product["add_delivery"] += $v['op_add_delivery_price'];//개별배송비
						$arr_product["delivery"] += $v['op_delivery_price'];

						// 전체 총계
						foreach($arr_product as $ak=>$av){ $arr_product_sum[$k] += $av; }
				?>
				<tr>
					<td>
						<!-- 상품사진 -->
						<a href="<?=rewrite_url($v['code'])?>" target="_blank" title="<?=$pro_name?>" class="thumb"><img src="<?=product_thumb_img( $v , '장바구니' ,  'data')?>" alt="<?=$pro_name?>" title="<?=$pro_name?>"/></a>
						<!-- 상품정보 -->
						<div class="item_name">
							<dl>
								<dt><a href="<?=rewrite_url($v['code'])?>" target="_blank" title="<?=$pro_name?>"><?=$pro_name?></a></dt>
								<?=$option_html?>
							</dl>
							<?=$product_coupon_html?>
						</div>
					</td>
					<!-- 수량합계금액 -->
					<td class="pointbg"><strong><?=number_format($sum_price)?></strong>원</td>
					<!-- 배송비 -->
					<td class="pointbg">
						<? if( $delivery_price > 0 ) { ?>
							<strong><?=number_format($delivery_price)?></strong>원
						<? } else { ?>
							<?=$delivery_print?>
						<? } ?>
						<?=$add_delivery_print?>
					</td>
				</tr>
				<? } ?>
				</tbody>
			</table>
		</div><!-- .cm_shop_cart_list -->

		<!-- ●●●●●●●●●● 최종계산 -->
		<div class="cm_shop_last_sum">
			<span class="lineup">

				<span class="box normal_box">
					<span class="icon"></span>
					<span class="txt">상품합계금액</span> 
					<span class="price"><strong><?=number_format($r['tPrice']-$r['dPrice']+$r['sPrice'])?></strong><em>원</em></span>
				</span>

				<span class="box plus_box">
					<span class="icon"></span>
					<span class="txt">총 배송비</span> 
					<span class="price"><strong><?=number_format($r['dPrice'])?></strong><em>원</em></span>
				</span>

				<span class="box minus_box">
					<span class="icon"></span>
					<span class="txt">총 할인금액</span> 
					<span class="price"><strong><?=number_format($r['sPrice'])?></strong><em>원</em></span>
				</span>

				<span class="box equal_box">
					<span class="icon"></span>
					<span class="txt">총 결제예상금액</span> 
					<span class="price"><strong><?=number_format($r['tPrice'])?></strong><em>원</em></span>
				</span>

			</span>

			<div class="save_point">본 주문으로 예상되는 적립금은 <strong><?=number_format($r['gGetPrice'])?></strong>원 입니다.</div>
		</div>
		<!-- / 최종계산 -->

		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>주문자</strong> 정보
			<!-- <div class="explain"><img src="images/cm_images/member_form_bullet2.png" alt="필수" />표시된 것은 필수항목입니다.</div> -->
		</div>
		<!-- / 단락타이틀 -->

		<!-- ●●●●●●●●●● 주문자정보 -->
		<div class="cm_order_form">
			<ul>
				<li class="ess double">
					<span class="opt">주문자 이름</span>
					<div class="value"><?=$r['ordername']?></div>
				</li>
				<li class="ess double">
					<span class="opt">주문자 휴대폰</span>
					<div class="value"><?=phone_print($r['orderhtel1'],$r['orderhtel2'],$r['orderhtel3'])?></div>
				</li>
				<li class="ess ">
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
		<div class="cm_order_form">
			<ul>
				<li class="ess double">
					<span class="opt">사용자 이름</span>
					<div class="value"><?=$r['username']?></div>
				</li>
				<li class="ess double">
					<span class="opt">사용자 휴대폰</span>
					<div class="value"><?=phone_print($r['userhtel1'],$r['userhtel2'],$r['userhtel3'])?></div>
				</li>
				<li class="ess ">
					<span class="opt">사용자 이메일</span>
					<div class="value"><?=$r['useremail']?></div>
				</li>
			</ul>
		</div>	
		<? } // 사용자정보 끝 ?>

		<? if($r['order_type'] == "product" || $r['order_type'] == "both") { // 배송상품이 있을경우에만 노출 ?>
		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>받는분</strong> 정보
			<!-- <div class="explain"><img src="images/cm_images/member_form_bullet2.png" alt="필수" />표시된 것은 필수항목입니다.</div> -->
		</div>
		<!-- / 단락타이틀 -->
		<div class="cm_order_form">
			<ul>
				<li class="ess double">
					<span class="opt">받는분 이름</span>
					<div class="value"><?=$r['recname']?></div>
				</li>
				<li class="ess double">
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
		<div class="cm_order_form">
			<ul>
				<li class="ess double">
					<span class="opt">최종 결제금액</span>
					<div class="value"><strong><?=number_format($r['tPrice'])?></strong>원</div>
				</li>
				<li class="ess double">
					<span class="opt">결제수단</span>
					<div class="value"><?=$arr_paymethod_name[$r['paymethod']]?></div>
				</li>
				<?PHP
				// PG사 결제 인증요청 페이지
				switch($row_setup['P_KBN']) {
					case "L" : 
						// -- lg u+ 모듈별 처리
						if($row_setup['P_L_TYPE'] == 'W'){  // 웹표준모듈
							require_once(dirname(__FILE__)."/shop.order.result_lgpay_new.php"); 
							$submit_onclick = "launchCrossPlatform();";
						}else{
							require_once(dirname(__FILE__)."/shop.order.result_lgpay.php"); 
							$submit_onclick = "doPay_ActiveX();";
						}
						

						
						break;
					case "I" : 
                        # -- 이니시스 모듈별 처리 
                        if($row_setup['P_I_TYPE'] == 'W'){ // 웹표준 모듈일경우
                            require_once(dirname(__FILE__)."/shop.order.result_inicis_std.php"); 
                        }else{ // 기본 TX 모듈 일경우
                            require_once(dirname(__FILE__)."/shop.order.result_inicis.php"); 
                        }

                        $submit_onclick = "ini_submit();";
                        break;
					case "K" : 
						require_once(dirname(__FILE__)."/shop.order.result_kcp.php"); 
						$submit_onclick = "onload_pay(document.order_info);";
						break;
					case "A" :
						require_once(dirname(__FILE__)."/shop.order.result_allthegate.php"); 
						$submit_onclick = "Pay(frmAGS_pay);";
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
			</ul>
		</div>

		<!-- ●●●●●●●●●● 가운데정렬버튼 -->
		<div class="cm_bottom_button">
			<ul>
				<li><span class="button_pack"><a href="#none" onclick="<?=$submit_onclick?>return false;" title="" class="btn_lg_color">결제하기</a></span></li>
			</ul>
		</div>
		<!-- // 가운데정렬버튼 -->

	</div><!-- .layout_fix -->
	</div><!-- .common_page -->

</div>

<?
/* ---------- pg 관련 하단 호출 ---------- */
switch($row_setup['P_KBN']) {
	case "L" : 
		?>

<?php 
	// -- 2016-12-14 LCY :: LGU+
	if($row_setup['P_L_TYPE'] == 'D'){  // 기본모듈일 시에만 ?>
<!-- xpay.js는 반드시 body 밑에 두시기 바랍니다.
UTF-8 인코딩 사용 시는 xpay_ub.js 대신 xpay_ub_utf-8.js 을  호출하시기 바랍니다. -->
<script language="javascript" src="<?= $_SERVER['SERVER_PORT']!=443?"http":"https" ?>://xpay.uplus.co.kr<?=($CST_PLATFORM == "test")?($_SERVER['SERVER_PORT']!=443?":7080":":7443"):""?>/xpay/js/xpay_ub_utf-8.js" type="text/javascript" charset="utf-8"></script> 

<?PHP } ?>
		
		<?
		break;
	case "I" : 
		break;
	case "K" : 
		break;
	case "A" :
		break;
	case "M" :
		break;
}
/* ---------- // pg 관련 하단 호출 ---------- */