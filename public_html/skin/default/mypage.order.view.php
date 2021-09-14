<?
	// 로그인 체크
	member_chk();

	$que = " select o.* , oc.oc_tid
						from odtOrder as o
						left join odtOrderCardlog as oc on (oc.oc_oordernum=o.ordernum)
						where o.ordernum='".$ordernum."' and o.orderid='".get_userid()."' ";

	$r = _MQ($que);

	// 포인트, 쿠폰에 대한 할인금액을 뽑도록 수정 2015-11-13 LCY[002]
	    $cque = "
	        select
	            cl_price
	        from odtOrderCouponLog
	        where cl_oordernum='".$ordernum."'
	    ";
	    $cres = _MQ_assoc($cque);
	    $total_cprice=0;
	    foreach($cres as $ck=>$cv){
	        $total_cprice+=$cv['cl_price'];
	    }





	if(!$r['ordernum']) { error_msg("해당 주문이 없습니다."); }

	include dirname(__FILE__)."/mypage.header.php";
?>

<!-- 인쇄용 스타일 -->
<style media="print">
.top_menu, .header, .nav, .fly_left, .fly_right, .topposition, .footer_menu, .footer, .common_page.common_none, .cm_bottom_button, .button_pack,
.thumb, .cm_shop_last_sum {display:none;}
.cm_order_form.cm_shop_last_sum_print { display:block !important; }
.item_name { margin-left: 0 !important; }
th, td, td span, td div, td p, td strong, td a, .cm_shop_title, .texticon_pack.checkicon span, .cm_shop_cart_list strong, .cm_order_form strong,
.cm_order_form .opt, .cm_order_form .value, .cm_order_form .value dt, .cm_order_form .value dd, .order_number { font-size: 10px !important; letter-spacing: -1px; }
.print_col { width: 17%; }
body, .wrap, .layout_fix, .common_page {min-width:0;width:100% !important;}
body, .wrap { padding: 0; margin: 0; background: transparent !important; }
.common_page { border: 0; }
</style>

<div class="common_page">
<div class="layout_fix">

	<!-- ●●●●●●●●●● 주문상세 주문번호출력 -->
	<div class="cm_order_number">
		<span class="lineup">
			<span class="order_number">주문번호 : <strong><?=$r['ordernum']?></strong></span>
			<!--  버튼있을때 -->
			<!-- <span class="btn_box">
				<? if($r['canceled'] == "N" && $r['paystatus'] == "Y") { if($r['mem_cancelchk'] == "Y") { ?>
				<span class="button_pack"><a href="#none" onclick="order_cancel('<?=$r['ordernum']?>');return false;" class="btn_md_white">주문취소</a></span>
				<? } else { ?>
				<span class="button_pack"><a href="#none" onclick="alert('주문취소가 불가능한 상태입니다. 고객센터(<?=$row_company[tel]?>)로 문의하세요.');return false;" class="btn_md_white">주문취소</a></span>
				<? }} ?>
			</span> -->
		</span>
	</div>


	<!-- ●●●●●●●●●● 단락타이틀 -->
	<div class="cm_shop_title">
		<strong>주문</strong> 상품
		<!-- <div class="explain">주문하실 상품을 최종적으로 확인하신 후 결제를 진행해주세요.</div> -->
	</div>
	<!-- / 단락타이틀 -->
	<div class="cm_shop_cart_list">
		<table summary="상품리스트">
			<colgroup>
				<col width="*"/><col width="12%"/><col width="13%"/><col class="print_col" width="13%"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">상품정보</th>
					<th scope="col">상품금액</th>
					<th scope="col">배송비</th>
					<th scope="col">진행상태</th>
				</tr>
			</thead>
			<tbody>
			<?

				$arr_opuid = array();// JJC : 교환/반품 : 2018-07-09

				// 주문상품 정보 추출
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
				$cr = _MQ_assoc($cque);
				if( count($cr)==0 ) { error_loc_msg('/?pn=mypage.order.list','필수 정보가 누락되었습니다. 목록으로 이동합니다.'); }

				foreach($cr as $k=>$v) {

					/* 옵션 처리 */
					unset($option_html , $delivery_print , $delivery_price , $add_delivery_print);

					$sque = "
						select * from odtOrderProduct as op
						inner join odtProduct as p on (p.code=op.op_pcode)
						left join odtOrderSettle as os on (os.os_cpid = p.customerCode and os.os_oordernum = op.op_oordernum)
						left join odtRequestReturn as rr on (op.op_uid = SUBSTRING_INDEX(rr.rr_opuid , ',',-1) and rr.rr_ordernum = op.op_oordernum)
						where op.op_oordernum='".$ordernum."' and op.op_pcode = '".$v['code']."' order by op_uid asc, op_is_addoption desc
					";
					//order by op_is_addoption desc,op_option1,op_option2,op_option3
					$sr = _MQ_assoc($sque);
					foreach($sr as $sk => $sv) {

						$arr_opuid[$sv['op_uid']] ++;// JJC : 교환/반품 : 2018-07-09

						/* 배송비 처리 */
						if( $chk_pcode <> $sv['op_pcode'] ) {
							if($sv['op_orderproduct_type'] != "product") {	// 배송적용 상품이 아니면
								$delivery_print = "해당없음";
								$delivery_price = 0;
								$add_delivery_print = "";
							} else {
								// 배송정보
								$delivery_price = $sv['op_delivery_price'];
								$delivery_print = "-";
								// 추가배송비 여부
								if($sv['op_add_delivery_price']) {
									$add_delivery_print = "<br/>추가배송비<br/>+".number_format($sv['op_add_delivery_price'])."원";
								}
							}
							$chk_pcode = $sv['op_pcode'];
						}
						/* 배송비 처리 끝 */

						/* 쿠폰 처리 */
						unset($expire);
						if($sv['expire']) { $expire = "<div class='thisis_due'>유효기간 :  ".date('Y-m-d',strtotime($sv['expire']))." 까지 </div>"; }
						unset($coupon_html,$coupon_html_body,$use_cnt,$notuse_cnt);
						if($sv['op_orderproduct_type'] == "coupon") {
							$coupon_assoc = _MQ_assoc("select * from odtOrderProductCoupon where opc_opuid = '".$sv['op_uid']."'");
							if(sizeof($coupon_assoc) < 1) { $coupon_html_body = "<div class='thisis_txt'>결제가 확인되면 쿠폰이 발급됩니다.</div>"; }
							foreach($coupon_assoc as $coupon_key => $coupon_row) {
								if($sv['op_cancel']=='N' && $v['canceled']=='N') { //LMH001
									if($coupon_row['opc_status'] == "대기") {
										$notuse_cnt++;
										$coupon_html_body .="
											<div class='coupon_number'>
												<span class='texticon_pack'><span class='orange'>미사용</span></span>
												".$coupon_row['opc_expressnum']."
												<span class='button_pack'>
													<a href='/pages/mypage.order.pro.php?_mode=coupon_sms_resend&opcuid=".$coupon_row['opc_uid']."' target='common_frame' class='btn_sm_black'>문자발송</a>
												</span>
											</div>
										";
									} else if($coupon_row['opc_status'] == "사용") {
										$coupon_html_body .="
											<div class='coupon_number'>
												<span class='texticon_pack'><span class='light'>사용완료</span></span>
												".$coupon_row['opc_expressnum']."
											</div>
										";
										$use_cnt++;
									} else if($coupon_row['opc_status'] == "취소") {
										$coupon_html_body .="
											<div class='coupon_number'>
												<span class='texticon_pack'><span class='dark'>취소</span></span>
												".$coupon_row['opc_expressnum']."
											</div>
										";
									} else { error_msg("잘못된 접근입니다. 다시 시도하세요. 계속 문제가 발생하면 관리자에게 문의하세요."); }
								} else { //LMH001
									$coupon_html_body .="
										<div class='coupon_number'>
											<span class='texticon_pack'><span class='dark'>취소</span></span>
											".$coupon_row['opc_expressnum']."
										</div>
									";
								}
							}
							$coupon_html .="<dd class='thisis_coupon'>".$expire.$coupon_html_body."</dd>";
						}
						/* 쿠폰 처리 끝 */

						/* 배송상태 처리 */
						unset($status_print,$delivery_btn_print);
						if($sv['op_delivstatus'] == "N") {
							$status_print = $arr_o_status[$v['orderstatus_step']];
						} else {
							if($sv['op_orderproduct_type']=='coupon'){
								/*if($notuse_cnt>0) { $status_print .= "<span class='orange'>미사용(".number_format($notuse_cnt)."개)</span>"; }
								if($use_cnt>0) { $status_print .= "<span class='light'>사용(".number_format($use_cnt)."개)</span>"; }*/
								$status_print .= $arr_o_status['발급완료'];
							} else {
								$status_print .= $arr_o_status['발송완료'];
								$delivery_btn_print .= "
								<span class='option_cancel'><span class='button_pack'><a href='".$arr_delivery_company[$sv['op_expressname']].rm_str($sv['op_expressnum'])."' target='_blank' class='btn_sm_white'>배송조회</a></span></span>
								";
							}
						}
						/* 배송상태 처리 끝 */

						// 부분취소 버튼 LMH001
						unset($app_btn_cancel,$app_cancel_class);
						if($r['paystatus']=='Y' && $sv['op_is_addoption']!='Y' && $sv['op_settlementstatus']=='none') {
							switch($sv['op_cancel']) {
								case 'Y': // 취소완료
								$app_btn_cancel = "<span class='option_cancel'><span class='button_pack'><a href='#none' onclick=\"return false;\" data-ordernum='".$r['ordernum']."' data-opuid='".$sv['op_uid']."' class='btn_sm_white product_cancel_view'>취소내역</a></span></span>";
								$app_status_cancel = "<span class='light'>취소완료</span>";
								$app_cancel_class = "if_option_cancel";
								break;
								case 'R': // 취소진행
								$app_btn_cancel = "<span class='option_cancel'><span class='button_pack'><a href='#none' onclick=\"return false;\" data-ordernum='".$r['ordernum']."' data-opuid='".$sv['op_uid']."' class='btn_sm_white product_cancel_view'>취소진행중</a></span></span>";
								$app_status_cancel = "<span class='light'>취소진행중</span>";
								$app_cancel_class = "if_option_cancel";
								break;
								default:
								if($r['canceled']=='N'  && $r['paystatus2'] == "N") {
									$app_btn_cancel = "<span class='option_cancel'><span class='button_pack'><a href='#none' onclick=\"return false;\" data-ordernum='".$r['ordernum']."' data-opuid='".$sv['op_uid']."' class='btn_sm_white product_cancel'>주문취소</a></span></span>";
									$app_status_cancel = "";
									$app_cancel_class = "if_option_cancel";
								}
								break;
							}
							// 주문취소 버튼 자체를 숨기고자 할 경우 아래 주석 해제
							/*if( $sv[op_orderproduct_type] == "coupon" ) {
								$coupon_chk = _MQ("select count(*) as cnt from odtOrderProductCoupon where opc_opuid = '".$sv['op_uid']."' and opc_status = '사용' ");
								if( $coupon_chk[cnt] > 0 ) { $app_status_cancel = ''; $app_btn_cancel = ''; }
							}*/
						}
						$app_btn_cancel = $delivery_btn_print ? $delivery_btn_print : $app_btn_cancel;
						$app_cancel_class = $delivery_btn_print  ? "if_option_cancel"  : $app_cancel_class;// 2016-10-14 ::: 수정

						$option_name		= !$sv['op_option1'] ? "옵션없음" : $sv['op_option1']." ".$sv['op_option2']." ".$sv['op_option3'];
						$option_price		= $sv['op_pprice'] + $sv['op_poptionprice'];
						$option_cnt			= $sv['op_cnt'];
						$option_sum_price	= $sv['op_cnt'] * ($sv['op_pprice'] + $sv['op_poptionprice']);
						$option_html .="
							<dd class='".($sv['op_is_addoption']<>"Y"?"ess":"")." ".$app_cancel_class."'>
								".$app_btn_cancel."
								<div class='option_name'>".$option_name."</div>
								<div class='counter_box'>
									<span class='option_number'>(<strong>".number_format($option_cnt)."</strong>개)</span>
									<span class='option_price'><strong>".number_format($option_price)."</strong>원</span>
								</div>
							</dd>
							".$coupon_html."
						";
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
				<td class="pointbg">
					<span class="texticon_pack checkicon"><?=$status_print?></span>
					<?//$delivery_btn_print?>
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
				<span class="txt">총 결제금액</span>
				<span class="price"><strong><?=number_format($r['tPrice'])?></strong><em>원</em></span>
			</span>

		</span>

		<div class="save_point">본 주문으로 발생한 적립금은 <strong><?=number_format($r['gGetPrice'])?></strong>원 입니다.</div>
	</div>
	<div class="cm_order_form cm_shop_last_sum_print" style="display:none;">
		<ul>
			<li class="ess double">
				<span class="opt">상품합계금액</span>
				<div class="value"><strong><?=number_format($r['tPrice']-$r['dPrice']+$r['sPrice'])?></strong>원</div>
			</li>
			<li class="ess double">
				<span class="opt">총 배송비</span>
				<div class="value"><strong><?=number_format($r['dPrice'])?></strong>원</div>
			</li>
			<li class="ess double">
				<span class="opt">총 할인금액</span>
				<div class="value"><strong><?=number_format($r['sPrice'])?></strong>원</div>
			</li>
			<li class="ess double">
				<span class="opt">예상 적립금</span>
				<div class="value"><strong><?=number_format($r['gGetPrice'])?></strong>원</div>
			</li>
			<li class="ess">
				<span class="opt">총 결제금액</span>
				<div class="value"><strong><?=number_format($r['tPrice'])?></strong>원</div>
			</li>
		</ul>
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
		<strong>받는분(배송)</strong> 정보
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
			<? if($r[sPrice] > 0) { //쿠폰, 포인트 할인금액 2015-11-13 LCY002 ?>
			<li class="ess">
				<span class="opt">상세 할인금액</span>
				<div class="value">
					<!-- 상세할인금액계산 2015-11-18 -->
					<div class="benefit_sum">
						<dl>
							<dd>쿠폰 : <strong><?=!empty($total_cprice)?number_format($total_cprice):"0"?></strong>원<span class="shape"></span></dd>
							<dd>포인트 : <strong><?=!empty($r['gPrice'])?number_format($r['gPrice']):"0"?></strong>원<span class="shape"></span></dd>
							<dd>프로모션코드 : <strong><?=!empty($r['o_promotion_price'])?number_format($r['o_promotion_price']):"0"?></strong>원<span class="shape"></span></dd>
							<dt>총 할인액 : <strong><?=number_format($r[sPrice])?></strong>원<span class="shape"></span></dt>
						</dl>
					</div>
					<!-- / 상세할인금액계산 -->
				</div>
			</li>
	 		<? } ?>
            <?
                if( $r['paymethod'] == "V" ) {
                    $ol = _MQ("select * from odtOrderOnlinelog where ool_ordernum = '$ordernum' and ool_type='R' order by ool_uid desc limit 1");
                    $r['tPrice'] = $r['tPrice'] + $ol['ool_escrow_fee']; // 총액 수수료더하기
                }
            ?>
			<li class="ess double">
				<span class="opt">최종 결제금액</span>
				<div class="value"><strong><?=number_format($r['tPrice'])?></strong>원<?=($ol['ool_escrow_fee']>0 ? " (수수료 " . number_format($ol['ool_escrow_fee']) . "원 포함)" : null)?></div>
			</li>
			<li class="ess double">
				<span class="opt">결제수단 선택</span>
				<div class="value"><?=$arr_paymethod_name[$r['paymethod']]?> <? if($r[paystatus]=='Y' && $r[paymethod]=='C') { echo link_credit_receipt($ordernum,'[영수증출력]'); } ?></div>
			</li>
			<?
				if( $r['paymethod'] == "V" ) {
			?>
			<li class="ess">
				<span class="opt">입금은행</span>
				<div class="value"><?=$ol['ool_account_num']?> (<?=$ol['ool_bank_name']?>)</div>
			</li>
			<li class="ess double">
				<span class="opt ">입금자명</span>
				<div class="value"><?=$ol['ool_deposit_name']?> <?=$r['taxorder']=="Y"?"(현금영수증 발행을 신청하였습니다)":""?></div>
			</li>
			<? } ?>
			<? if($r['paymethod'] == "B") { ?>
			<li class="ess">
				<span class="opt">입금은행</span>
				<div class="value"><?=$r['paybankname']?></div>
			</li>
			<li class="ess double">
				<span class="opt ">입금예정일</span>
				<div class="value"><?=$r['paydatey']."-".$r['paydatem']."-".$r['paydated']?></div>
			</li>
			<li class="ess double">
				<span class="opt">입금자명</span>
				<div class="value"><?=$r['payname']?> <?=$r['taxorder']=="Y"?"(현금영수증 발행을 신청하였습니다)":""?></div>
			</li>
			<? } ?>
		</ul>
	</div>

	<!-- ●●●●●●●●●● 가운데정렬버튼 -->
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><a href="/?<?=$_PVSC?enc('d',$_PVSC):"pn=mypage.order.list"?>" title="" class="btn_lg_white">목록으로</a></span></li>
			<li><span class="button_pack"><a href="#none" onclick="window.print();return false;" title="" class="btn_lg_black">인쇄하기</a></span></li>
			<?
				// JJC : 교환/반품 : 2018-07-09
				/* 교환/반품가능여부체크 : 취소되지않음 && (발송완료상품중 교환/반품신청이 없는 상품수 > 0) */

				$arr_rropuid = array();
				$rr_que = "select rr_opuid from odtRequestReturn where rr_ordernum = '". $ordernum ."' ";
				$rr_res = _MQ_assoc($rr_que);
				foreach($rr_res as $rr_k => $rr_v){
					$ex = array_filter(explode("," , $rr_v['rr_opuid']));
					$arr_rropuid = array_merge($arr_rropuid , $ex);
				}

				$arr_diff = array_diff(array_keys($arr_opuid) , $arr_rropuid);

				if($r['canceled'] == 'N' && sizeof($arr_diff) > 0  ){

			?>
			<li><span class="button_pack"><a href="/?pn=service.return.form&ordernum=<?=$r['ordernum']?>" title="" class="btn_lg_black">교환/반품신청</a></span></li>
			<?
				}
			?>
		</ul>
	</div>
	<!-- // 가운데정렬버튼 -->

</div><!-- .layout_fix -->
</div><!-- .common_page -->

<script>
// 주문취소
function order_cancel(ordernum){
	if( confirm('정말 주문을 취소하시겠습니까?') ) {
		common_frame.location.href=("/pages/mypage.order.pro.php?_mode=cancel&ordernum=" + ordernum + "&_PVSC=<?=$_PVSC?>");
	}
}
$(document).ready(function(){
	$('#cash_issue').on('click',function(e){ // 현금영수증 신청 버튼
		e.preventDefault();
		if (confirm('<?=$r[ordername]?>님 <?=$r[orderhtel1]?>-<?=$r[orderhtel2]?>-<?=$r[orderhtel3]?> 번호로 현금영수증 발행을 신청합니다.')) {
			$.ajax({
				data: {'ordernum':'<?=$ordernum?>'},
				type: 'POST',
				cache: false,
				url: '/pages/totalCashReceipt.ajax.php',
				success: function(data) {
					if(data=='AUTH'){ // 작업에 성공했다면 진행 - AUTH = 현금영수증 발행, OK = 현금영수증 신청 완료
						$('#cash_status').text('현금영수증이 발행되었습니다.');
					} else if(data=='OK') {
						$('#cash_status').text('현금영수증 발행이 신청되었습니다.');
					} else { // 아니라면 오류 메세지
						alert('현금영수증 발행 신청에 실패했습니다.');
					}
				},
				error:function(request,status,error){
			alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		   }
			});
		} else {
			return false;
		}
	});
});
</script>



<!-- ●●●●●●●●●● 부분취소신청 (티플형) LMH001 -->
<div class="cm_ly_pop_tp" id="product_cancel_pop" style="display:none;width:500px;">

	<!--  레이어팝업 공통타이틀 영역 -->
	<div class="title_box">부분취소/환불 신청<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>

	<!-- 하얀색박스공간 -->
	<div class="inner_box">

		<!-- 설명글 -->
		<div class="top_txt">
			부분 취소하실 상품을 꼭 다시한번 확인하시고,<br/>
			다음 정보를 입력해주시면 관리자의 확인 후 처리됩니다.
		</div>

		<!-- 상품정보 -->
		<div class="this_item">
			<div class="thumb"><a href="#none" onclick="return false;"><img class="product_thumb" src="" alt="" /></a></div>
			<div class="info">
				<div class="info_title">부분취소 신청하실 상품정보</div>
				<dl>
					<dt class="product_name"></dt>
					<dd class="product_option"></dd>
				</dl>
				<div class="info_price">


					<? // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC ?>
					<span class="txt">
						상품금액 : <strong class="product_price">0</strong> ,
						배송비용 : <strong class="delivery_price">0</strong> ,
						할인비용 : <strong class="discount_price">0</strong><? // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC ?>
					</span>
					<span class="txt">
						환불금액 : <strong class="return_price">0</strong>
					</span>
					<? // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC ?>

				</div>
				<?
					$coup_log_cnt = _MQ_result("select count(*) as cnt from odtOrderCouponLog where cl_type = 'member' and cl_oordernum='".$r[ordernum]."'");
					if($coup_log_cnt>0) {
				?>
				<div class="info_price">사용한 쿠폰은 모든 상품이 취소될 때 함께 취소되며 다음 주문 시 재사용하실 수 있습니다.</div>
				<? } ?>
			</div>
		</div>
		<!-- / 상품정보 -->

		<form name="product_cancel">
		<input type="hidden" name="mode" value="cancel"/><input type="hidden" name="ordernum" value=""/><input type="hidden" name="op_uid" value=""/><input type="hidden" name="cancel_mem_type" value="member"/>
        <!-- 폼들어가는곳 -->
        <div class="form_box">
            <ul>
                <li>
                    <span class="opt">환불수단</span>
                    <div class="value">
                        <?php if(in_array($row_setup['P_KBN'],array('I','A','K','L','B','D'))) { ?>
                            <?php if( !in_array($r['paymethod'],array('C','L','G')) ) { ?>
                                <label class="save_check"><input type="radio" name="cancel_type" class="cancel_type_pg" checked value="pg"/>직접 환불</label>&nbsp;&nbsp;&nbsp;
                            <?php }else{ ?>
                                <label class="save_check"><input type="radio" name="cancel_type" class="cancel_type_pg" checked value="pg"/>PG사 결제 취소</label>&nbsp;&nbsp;&nbsp;
                            <?php } ?>
                        <?php } ?>
                        <label class="save_check"><input type="radio" name="cancel_type" class="cancel_type_point" value="point"/>적립금 환불</label>
                    </div>
                </li>
                <?php if( !in_array($r['paymethod'],array('C','L','G')) ) { ?>
                <li class='view_pg'><?php // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC ?>
                    <span class="opt">환불계좌</span>
                    <div class="value">
                        <input type="text" name="cancel_bank_name" class="input_design icon_name" value="<?php echo $_member['cancel_bank_name']; ?>" placeholder="예금주" style="width:140px;box-shadow:0;border:0;"/>
                        <select name="cancel_bank" class="select_design" style="width:140px; margin-left:5px">
                            <?php foreach($ksnet_bank as $kk=>$vv) { ?>
                            <option value="<?php echo $kk; ?>" <?php echo ($_member['cancel_bank']==$kk?'selected':''); ?>><?php echo $vv; ?></option>
                            <?php } ?>
                        </select>
                        <input type="text" name="cancel_bank_account" class="input_design icon_bank" value="<?php echo $_member['cancel_bank_account']; ?>" placeholder="계좌번호" style="box-shadow:0;border:0;"/>
                        <!-- <label class="save_check"><input type="checkbox" name="save_myinfo" value="Y"/>나의 정보에 함께 저장하기</label> -->
                    </div>
                </li>
                <?php } ?>
                <li>
                    <span class="opt">전달내용</span>
                    <div class="value">
                        <textarea name="cancel_msg" rows="3" cols="" class="textarea_design" placeholder="관리자에게 전달할 내용이 있다면 입력하세요." ></textarea>
                    </div>
                </li>
            </ul>
        </div>
        <!-- / 폼들어가는곳 -->

		<!-- 레이어팝업 버튼공간 -->
		<div class="cm_bottom_button">
			<ul>
				<li><span class="button_pack"><button type="submit" class="btn_md_color">취소신청</button></span></li>
				<li><span class="button_pack"><a href="#none" onclick="return false;" title="" class="close btn_md_black">닫기</a></span></li>
			</ul>
		</div>
		<!-- / 레이어팝업 버튼공간 -->
		</form>

	</div>
	<!-- / 하얀색박스공간 -->

</div>
<!-- ●●●●●●●●●● 부분취소신청 (티플형) -->
<div class="cm_ly_pop_tp" id="product_cancel_view_pop" style="display:none;width:500px;">

	<!--  레이어팝업 공통타이틀 영역 -->
	<div class="title_box">부분취소/환불 신청 내역<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>

	<!-- 하얀색박스공간 -->
	<div class="inner_box">

		<!-- 설명글 -->
		<div class="top_txt">
			<span style="font-size:inherit; color:inherit; font-weight:inherit;" class="cancel_date"></span>에 부분취소 요청하신 내역입니다.
		</div>

		<!-- 상품정보 -->
		<div class="this_item">
			<div class="thumb"><a href="#none" onclick="return false;"><img class="product_thumb" src="" alt="" /></a></div>
			<div class="info">
				<div class="info_title">부분취소 신청하신 상품정보</div>
				<dl>
					<dt class="product_name"></dt>
					<dd class="product_option"></dd>
				</dl>
				<div class="info_price">
					<? // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC ?>
					<span class="txt">
						상품금액 : <strong class="product_price">0</strong> ,
						배송비용 : <strong class="delivery_price">0</strong> ,
						할인비용 : <strong class="discount_price">0</strong><? // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC ?>
					</span>
					<span class="txt">
						환불금액 : <strong class="return_price">0</strong>
					</span>
					<? // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC ?>
				</div>
			</div>
		</div>
		<!-- / 상품정보 -->

		<div class="form_box">
			<ul>
				<li>
					<span class="opt">환불수단</span>
					<div class="value">
                        <?php if(in_array($row_setup['P_KBN'],array('I','A','K','L','B'))) { ?>
                            <?php if( !in_array($r['paymethod'],array('C','L','G')) ) { ?>
                                <label class="save_check"><input type="radio" name="cancel_type_val" disabled class="cancel_type_pg" checked value="pg"/>직접 환불</label>&nbsp;&nbsp;&nbsp;
                            <?php }else{ ?>
                                <label class="save_check"><input type="radio" name="cancel_type_val" disabled class="cancel_type_pg" checked value="pg"/>PG사 결제 취소</label>&nbsp;&nbsp;&nbsp;
                            <?php } ?>
                        <?php } ?>
						<label class="save_check"><input type="radio" name="cancel_type_val" disabled class="cancel_type_point"/>적립금 환불</label>
					</div>
				</li>
				<?php if( !in_array($r['paymethod'],array('C','L','G')) ) { ?>
				<li class='cancel_bank_wrap'>
					<span class="opt">환불계좌</span>
					<div class="value">
						<input type="text" name="cancel_bank_name" class="input_design icon_name" value="" readonly placeholder="예금주" style="width:140px;"/>
						<select name="cancel_bank" readonly class="select_design" style="width:140px; margin-left:5px">
							<option value="" selected></option>
						</select>
						<input type="text" name="cancel_bank_account" class="input_design icon_bank" value="" readonly placeholder="계좌번호" />
					</div>
				</li>
				<? } ?>
				<li>
					<span class="opt">전달내용</span>
					<div class="value">
						<textarea name="cancel_msg" rows="3" cols="" class="textarea_design" readonly placeholder="관리자에게 전달하신 내용이 없습니다." ></textarea>
					</div>
				</li>
			</ul>
		</div>
		<!-- / 폼들어가는곳 -->

		<!-- 레이어팝업 버튼공간 -->
		<div class="cm_bottom_button">
		    <ul>
		        <li><span class="button_pack"><a href="#none" onclick="return false;" title="" class="close btn_md_white">닫기</a></span></li>
			</ul>
		</div>
		<!-- / 레이어팝업 버튼공간 -->

	</div>
	<!-- / 하얀색박스공간 -->

</div>
<script>
$(document).ready(function(){
	$('input[name=cancel_type]').on('change',function(){
		var type = $(this).val();
		if( type=='pg' ) { $('.view_pg').show(); } else { $('.view_pg').hide(); }
	});
	$('.product_cancel').on('click',function(){
		var ordernum = $(this).data('ordernum'), op_uid = $(this).data('opuid'), $product_pop = $('#product_cancel_pop'), $product_form = $('form[name=product_cancel]');
		$.ajax({
			data: {'ordernum': ordernum, 'op_uid': op_uid, 'mode': 'product'},
			type: 'POST',
			cache: false,
			url: '/pages/mypage.order.view.ajax.php',
			dataType: 'JSON',
			success: function(data) {
				if(data['result']=='OK'){
					$product_pop.find('.product_thumb').attr('src',data['data']['image']);
					$product_pop.find('.product_name').text(data['data']['name']);

					$product_pop.find('.product_price').text(data['data']['price']);
					$product_pop.find('.delivery_price').text(data['data']['delivery']);//배송비용
					$product_pop.find('.discount_price').text(data['data']['discount']);//할인비용 // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
					$product_pop.find('.return_price').text(data['data']['return']);//환불금액

					if(data['data']['option']) {
						$product_pop.find('.product_option').text('옵션: ' + data['data']['option']);
						if(data['data']['addoption']) {
							$product_pop.find('.product_option').append('<br/>추가옵션: '+data['data']['addoption']);
						}
					} else { $product_pop.find('.product_option').text(''); }
					$product_form.find('input[name=ordernum]').val(ordernum);
					$product_form.find('input[name=op_uid]').val(op_uid);
					if(data['data']['pg_check']=='N') {
						$('input[name=cancel_type].cancel_type_pg').parent().hide();
						$('input[name=cancel_type].cancel_type_pg').prop('disabled',true);
						$('input[name=cancel_type].cancel_type_point').prop('checked',true).trigger('change');
					}
					$('#product_cancel_pop').lightbox_me({
						centered: true, closeEsc: false, overlaySpeed: 0, lightboxSpeed: 0,
						onLoad: function() { },
						onClose: function(){
							$product_form.find('input[name=ordernum]').val('');
							$product_form.find('input[name=op_uid]').val('');
						}
					});
				} else {
					alert(data['result_text']);
				}
			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	});

	$('form[name=product_cancel]').on('submit',function(e){ e.preventDefault();

		<?// 2016-11-30 ::: 사전체크 ::: JJC ?>
		var app_cancel_type = $("form[name=product_cancel] input[name=cancel_type]").filter(function() {if (this.checked) return this;}).val(); // 선택한 환불수단
		app_cancel_type = app_cancel_type == undefined ? '' : app_cancel_type;// - undefined 초기화
		if( app_cancel_type == '' ){ alert('환불수단을 선택해주시기 바랍니다.'); return false; }

		<? if( !in_array($v[o_paymethod],array('card','point')) ) { ?>
			if( $('form[name=product_cancel] input[name=cancel_bank_name]').val() == '' && ( $('input[name=cancel_type]:checked').val() != 'card' && $('input[name=cancel_type]:checked').val() != 'point' )){ alert('예금주를 입력해주시기 바랍니다.'); return false; }
			if( $('form[name=product_cancel]  select[name=cancel_bank]').val() == ''  && ( $('input[name=cancel_type]:checked').val() != 'card' && $('input[name=cancel_type]:checked').val() != 'point' ) ){ alert('은행을 선택해주시기 바랍니다.'); return false; }
			if( $('form[name=product_cancel]  input[name=cancel_bank_account]').val() == ''  && ( $('input[name=cancel_type]:checked').val() != 'card' && $('input[name=cancel_type]:checked').val() != 'point' ) ){ alert('계좌번호를 입력해주시기 바랍니다.'); return false; }
		<? } ?>
		<?// 2016-11-30 ::: 사전체크 ::: JJC ?>

		if(confirm("정말 주문을 취소하시겠습니까?")===true) {
			var data = $(this).serialize();
			$.ajax({
				data: data,
				type: 'POST',
				cache: false,
				url: '/pages/mypage.order.view.ajax.php',
				success: function(data) {
					if(data=='OK') {
						alert('성공적으로 취소요청 되었습니다.'); location.reload(); return false;
					} else {
						alert(data);
					}
				},
				error:function(request,status,error){
					alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}
	});
});
</script>
<script>
$(document).ready(function(){
	$('.product_cancel_view').on('click',function(){
		var ordernum = $(this).data('ordernum'), op_uid = $(this).data('opuid'), $product_pop = $('#product_cancel_view_pop');
		$.ajax({
			data: {'ordernum': ordernum, 'op_uid': op_uid, 'mode': 'view'},
			type: 'POST',
			cache: false,
			url: '/pages/mypage.order.view.ajax.php',
			dataType: 'JSON',
			success: function(data) {
				if(data['result']=='OK'){
					$product_pop.find('.product_thumb').attr('src',data['data']['image']);
					$product_pop.find('.product_name').text(data['data']['name']);
					$product_pop.find('.product_price').text(data['data']['price']);

					$product_pop.find('.delivery_price').text(data['data']['delivery']);//배송비용
					$product_pop.find('.discount_price').text(data['data']['discount']);//할인비용 // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
					$product_pop.find('.return_price').text(data['data']['return']);//환불금액

					$product_pop.find('.cancel_date').text(data['data']['date']);
					$product_pop.find('select[name=cancel_bank] option').text(data['data']['bank']);
					$product_pop.find('input[name=cancel_bank_account]').val(data['data']['bank_account']);
					$product_pop.find('input[name=cancel_bank_name]').val(data['data']['bank_name']);
					$product_pop.find('textarea[name=cancel_msg]').val(data['data']['msg']);
					$product_pop.find('input[name=cancel_type_val].cancel_type_'+data['data']['cancel_type']).prop('checked',true);
					if(data['data']['cancel_type']!='pg') {
						$product_pop.find('.cancel_bank_wrap').hide();
					}
					if(data['data']['option']) {
						$product_pop.find('.product_option').text('옵션: ' + data['data']['option']);
						if(data['data']['addoption']) {
							$product_pop.find('.product_option').append('<br/>추가옵션: '+data['data']['addoption']);
						}
					} else { $product_pop.find('.product_option').text(''); }
					$('#product_cancel_view_pop').lightbox_me({
						centered: true, closeEsc: false,
						onLoad: function() { },
						onClose: function(){ }
					});
				} else {
					alert(data['result_text']);
				}
			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	});

});
</script>
<!-- / 부분취소신청 -->