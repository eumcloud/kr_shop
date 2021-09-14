<?php

	//if(substr(phpversion(),0,3) < 5.4) { session_register("order_start"); }
	//$_SESSION["order_start"] = $_COOKIE["AuthShopCOOKIEID"];

	clean_cart(); // 장바구니 판매불가 상품 삭제

	// 비회원 구매를 위한 쿠키 적용여부 파악
	cookie_chk();

	// LDD019 {
	if($row_setup['none_member_buy'] == 'N' && !is_login()) {
		error_msg("로그인 후 이용 가능합니다.");
	}
	// } LDD019

	// JJC003 묶음배송
	//	$arr_cart ==> 장바구니 정보
	//	$arr_customer ==> 입점업체 정보 저장
	//	$arr_delivery ==> 업체별 배송비 정보
	//	$arr_product_info ==> 장바구니 상품정보
	// order_type_product , order_type_coupon ==> 상품형태(배송, 쿠폰)
	include(dirname(__FILE__)."/shop.cart.inc.php");

	if($order_type_product == "Y" && $order_type_coupon == "Y") { $order_type = "both"; }
	if($order_type_product == "Y" && $order_type_coupon != "Y") { $order_type = "product"; }
	if($order_type_product != "Y" && $order_type_coupon == "Y") { $order_type = "coupon"; }

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

</div>

	<div class="common_page">
	<div class="layout_fix">

		<form name="frm" method="post" action="/pages/shop.order.pro.php">
			<input type="hidden" name="order_type" value="<?=$order_type?>"/>

		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>주문</strong> 상품
			<div class="explain">주문하실 상품을 최종적으로 확인하신 후 결제를 진행해주세요.</div>
		</div>
		<!-- / 단락타이틀 -->

		<div class="cm_shop_cart_list">
		<?php
			// JJC003 묶음배송
			$arr_product_sum = $arr_product = array();
			if( count($arr_cart)==0 ) { // 장바구니 상품 없을때
				error_loc_msg('/?pn=shop.cart.list','주문할 상품이 없습니다. 장바구니로 이동합니다.');
			}

			foreach($arr_cart as $crk=>$crv) {

		?>
				<!-- 입점업체 반복구간 -->
				<!-- 입점업체 (사용안하면 이 div전체 안보이게) -->
				<div class="cm_shop_entered">
				<span class="name">업체배송</span><span class="bar"></span><span class="name"><?=$arr_customer[$crk]['cName']?></span>
				<!-- 배송비정책 -->
				<span class="charge">
					<?=($arr_customer[$crk]['com_delprice_free'] > 0 ? "<b>". number_format($arr_customer[$crk]['com_delprice_free']) ."</b>원 이상 구매시 배송비 무료" : "")?>
				</span>
				</div>

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
					<?php

					unset($del_chk_customer);
					foreach($crv as $k=>$v) {
						// |개별배송패치| - $sum_product_cnt
						unset($option_html , $sum_price , $sum_product_cnt);

						foreach($v as $sk => $sv) {
							/* 옵션 처리 - 일반옵션 */
							if($sv['c_is_addoption'] <> "Y") {
								$option_tmp_name		= !$sv['c_option1'] ? "옵션없음" : $sv['c_option1']." ".$sv['c_option2']." ".$sv['c_option3'];
								$option_tmp_price		= $sv['c_price'] + $sv['c_optionprice'];
								$option_tmp_cnt			= $sv['c_cnt'];
								$option_tmp_sum_price	= $sv['c_cnt'] * ($sv['c_price'] + $sv['c_optionprice']);
								$app_point				= $sv['c_point'];

								// 상품 수량 select 값
								$buy_limit_array = array();
								$buy_max = 200; // 최고 구매갯수 설정
								$buy_limit = $sv['buy_limit'] ? min($sv['c_option1'] ? $sv['oto_cnt'] : $sv['stock'] ,$sv['buy_limit']) : min($sv['c_option1'] ? $sv['oto_cnt'] : $sv['stock'] ,$buy_max); // 구매제한이 없으면 재고만큼만 선택할수 있게 하되 max는 200
								for($i=1;$i<=$buy_limit;$i++) { $buy_limit_array[] = $i; }

								$option_tmp_stock = _InputSelect( "_ccnt[".$sv['c_uid']."]" , $buy_limit_array , $option_tmp_cnt , " id='cart_cnt_".$sv['c_uid']."' onchange='cart_modify(".$sv['c_uid'].")' class='option_select' " , $buy_limit_array , "선택");

								$option_html .="
									<dd class='ess'>
										<div class='option_name'>".$option_tmp_name."</div>
										<div class='counter_box'>
											<span class='option_number'>(<strong>".number_format($option_tmp_cnt)."</strong>개)</span>
											<span class='option_price'><strong>".number_format($option_tmp_price)."</strong>원</span>
										</div>
									</dd>
								";

								//상품수 , 포인트 , 상품금액
								$arr_product["cnt"] += $option_tmp_cnt;//상품수
								$sum_product_cnt += $option_tmp_cnt ;// |개별배송패치| - 상품당 갯수를 가져온다 : 해당 코드가 없을 시 추가
								$arr_product["point"] += $app_point ;//포인트
								$arr_product["sum"] += $option_tmp_sum_price;//상품금액
								$sum_price += $option_tmp_sum_price;//상품금액
							}
							/* 옵션 처리 - 일반옵션 끝 */
							/* 옵션 처리 - 추가옵션 */
							else {
								$option_tmp_name		= !$sv['c_option1'] ? "옵션없음" : $sv['c_option1']." ".$sv['c_option2']." ".$sv['c_option3'];
								$option_tmp_price		= $sv['c_price'] + $sv['c_optionprice'];
								$option_tmp_cnt			= $sv['c_cnt'];
								$option_tmp_sum_price	= $sv['c_cnt'] * ($sv['c_price'] + $sv['c_optionprice']);
								$app_point				= $sv['c_point'];

								// 상품 수량 select 값
								$buy_limit_array = array();
								$buy_max = 200; // 최고 구매갯수 설정
								$buy_limit = min($sv['c_option1'] ? $sv['pao_cnt'] : $sv['stock'] ,$buy_max); // 구매제한이 없으면 재고만큼만 선택할수 있게 하되 max는 200
								for($i=1;$i<=$buy_limit;$i++) { $buy_limit_array[] = $i; }

								$option_tmp_stock = _InputSelect( "_ccnt[".$sv['c_uid']."]" , $buy_limit_array , $option_tmp_cnt , " id='cart_cnt_".$sv['c_uid']."' onchange='cart_modify(".$sv['c_uid'].")' class='option_select' " , $buy_limit_array , "선택");

								$option_html .="
									<dd class=''>
										<div class='option_name'>".$option_tmp_name."</div>
										<div class='counter_box'>
											<span class='option_number'>(<strong>".number_format($option_tmp_cnt)."</strong>개)</span>
											<span class='option_price'><strong>".number_format($option_tmp_price)."</strong>원</span>
										</div>
									</dd>
								";

								//상품수 , 포인트 , 상품금액
								$arr_product["cnt"] += $option_tmp_cnt;//상품수
								$arr_product["point"] += $app_point ;//포인트
								$arr_product["sum"] += $option_tmp_sum_price;//상품금액
								$sum_price += $option_tmp_sum_price;//상품금액
							}
							/* 옵션 처리 - 추가옵션 */
						}


						/* 상품 정보 */
						$pr = $arr_product_info[$k];
						$pro_name	= strip_tags($pr['name']);	// 상품명
						$img_src	= replace_image(IMG_DIR_PRODUCT.app_thumbnail("장바구니", $pr)); // 상품 이미지
						/* 상품 정보 끝 */


						/* 추가배송비개선 - 2017-05-19::SSJ  */
						// 추가배송비 안내 멘트가 나타날 부분
						//$add_delivery_print = ($pr['setup_delivery'] == "Y" ? "<div class='guide_txt add_delivery_string_product'><!-- 추가배송비 안내내용 --></div>" : "");
						/* 추가배송비개선 - 2017-05-19::SSJ  */


						unset($product_coupon_html);
						/* 상품쿠폰 처리 */
						if(trim($pr['coupon_title']) && $pr['coupon_price'] > 0) {
							$product_coupon_html = "
								<div class='item_coupon'>
									<span class='txt_icon'>COUPON</span>
									<label class='one_coupon'><input type='checkbox' class='product_coupon_check' onclick='app_order_price()' name='product_coupon[".$pr['code']."]' value='".floor($sum_price*($pr['coupon_price']/100))."'/> ".stripslashes($pr['coupon_title'])." 적용 (<strong>".$pr['coupon_price']."%</strong> 할인 : <strong>".number_format(floor($sum_price*($pr['coupon_price']/100)))."</strong>원)</label>
								</div>
							";
						}
						/* 상품쿠폰 처리 끝 */

					?>
						<tr>
							<td>
								<!-- 상품사진 -->
								<a href="/?pn=product.view&pcode=<?=$pr['code']?>" target="_blank" title="<?=$pro_name?>" class="thumb"><img src="<?=product_thumb_img( $pr , '장바구니' ,  'data')?>" alt="<?=$pro_name?>" title="" /></a>
								<!-- 상품정보 -->
								<div class="item_name">
									<dl>
										<dt><a href="/?pn=product.view&pcode=<?=$pr['code']?>" target="_blank" title="<?=$pro_name?>"><?=$pro_name?></a></dt>
										<?=$option_html?>
									</dl>
									<?=$product_coupon_html?>
								</div>
							</td>
							<!-- 수량합계금액 -->
							<td class="pointbg"><strong><?=number_format($sum_price)?></strong>원</td>
							<!-- 배송비 -->
							<td class="pointbg">
								<?php

									/* 추가배송비개선 - 2017-05-19::SSJ  */
									// 추가배송비 안내 멘트가 나타날 부분
									$add_delivery_print = "";

									// 배송설정별 추가배송비 적용을위한 클래스지정
									$class_delivery_addprice = "";
									$class_delivery_addprice_print = "";


									$app_delivery = "-";    $delivery_price = 0;
									if($pr['setup_delivery'] == "Y"){
										switch($pr['del_type']){
											case "unit":
												$app_delivery = "<b>" . number_format($pr['del_price']*$sum_product_cnt) . "</b>원<br>개별배송";
												$arr_product["delivery"]+=$pr['del_price'] * $sum_product_cnt; // |개별배송패치| - $sum_product_cnt : 상품갯수를 곱해준다.
												$delivery_price = $pr['del_price'] * $sum_product_cnt; // |개별배송패치|

												// 입점업체의 설정체크
												if($row_setup['s_del_addprice_use']=="Y" && $arr_customer[$crk]['com_del_addprice_use']=="Y" && $arr_customer[$crk]['com_del_addprice_use_unit']=="Y"){
													// 배송설정별 추가배송비 적용을위한 클래스지정
													$class_delivery_addprice = "js_delevery_addprice js_delevery_addprice_unit";
													$class_delivery_addprice_print = "js_delevery_addprice_print js_delevery_addprice_unit_print";
												}

												break;
											case "free": 
												$app_delivery = "무료배송"; 

												// 입점업체의 설정체크
												if($row_setup['s_del_addprice_use']=="Y" && $arr_customer[$crk]['com_del_addprice_use']=="Y" && $arr_customer[$crk]['com_del_addprice_use_free']=="Y"){
													// 배송설정별 추가배송비 적용을위한 클래스지정
													$class_delivery_addprice = "js_delevery_addprice";
													$class_delivery_addprice_print = "js_delevery_addprice_print";
												}

												break;
											case "normal":
												if($del_chk_customer <> $crk) {
													$app_delivery = ($arr_customer[$crk]['app_delivery_price'] <> 0 ? "<b>" . number_format($arr_customer[$crk]['app_delivery_price']) . "</b>원" : "배송비무료") ;
													$arr_product["delivery"]+=$arr_customer[$crk]['app_delivery_price'];
													$delivery_price = $arr_customer[$crk]['app_delivery_price'];
													$del_chk_customer = $crk;

													// 일반배송상품중 무료배송조건충족시
													if($arr_customer[$crk]['app_delivery_price']==0){
														// 입점업체의 설정체크
														if($row_setup['s_del_addprice_use']=="Y" && $arr_customer[$crk]['com_del_addprice_use']=="Y" && $arr_customer[$crk]['com_del_addprice_use_normal']=="Y"){
															// 배송설정별 추가배송비 적용을위한 클래스지정
															$class_delivery_addprice = "js_delevery_addprice";
															$class_delivery_addprice_print = "js_delevery_addprice_print";
														}

													// 일반배송상품
													}else{
														// 입점업체의 설정체크
														if($row_setup['s_del_addprice_use']=="Y" && $arr_customer[$crk]['com_del_addprice_use']=="Y"){
															// 배송설정별 추가배송비 적용을위한 클래스지정
															$class_delivery_addprice = "js_delevery_addprice";
															$class_delivery_addprice_print = "js_delevery_addprice_print";
														}
													}
												}
												break;
										}

										// 배송비정보
										echo "<input type='hidden' name='product_delivery_price[".$pr['code']."]' value='" . $delivery_price . "'/>";
										echo "<input type='hidden' name='product_add_delivery_price[".$pr['code']."]' class='product_add_delivery_price_value ". $class_delivery_addprice ."' value='0' data-pcnt='". $sum_product_cnt ."' />";

										$add_delivery_print = "<div class='guide_txt add_delivery_string_product ". $class_delivery_addprice_print ."' data-pcnt='". $sum_product_cnt ."'><!-- 추가배송비 안내내용 --></div>"; // 추가배송비 안내 멘트가 나타날 부분

									}
									else {
										$app_delivery = "해당없음";
									}
									echo $app_delivery; // 배송비 문구
									echo $add_delivery_print ; // 추가배송비 문구
								?>
							</td>
						</tr>
					<? } ?>
					</tbody>
				</table>
			<? } // 업체별 foreach ?>
		</div> <!-- .cm_shop_cart_list -->


<?php
	// 전체 총계
	foreach($arr_product as $ak=>$av){ $arr_product_sum[$ak] += $av; }
?>
		<!-- ●●●●●●●●●● 계산하기 -->
		<div class="cm_shop_cart_sum">
			<span class="lineup">

				<span class="box normal_box">
					<span class="icon"></span>
					<span class="txt">상품합계금액</span>
					<span class="price"><strong><?=number_format($arr_product_sum["sum"])?></strong><em>원</em></span>
				</span>

				<span class="box plus_box">
					<span class="icon"></span>
					<span class="txt">총 배송비</span>
					<span class="price"><strong><?=number_format($arr_product_sum["delivery"])?></strong><em>원</em></span>
				</span>

				<span class="box plus_box add_delivery_info" style="display:none;">
					<span class="icon"></span>
					<span class="txt">추가 배송비</span>
					<span class="price"><strong id="delivery_price_smallsum">0</strong><em>원</em></span>
				</span>

				<!-- <span class="box minus_box">
					<span class="icon"></span>
					<span class="txt">총 할인금액</span>
					<span class="price"><strong>3,000</strong><em>원</em></span>
				</span> -->

				<span class="box equal_box">
					<span class="icon"></span>
					<span class="txt">총 결제예상금액</span>
					<span class="price"><strong id="ID_total_price_smallsum"><?=number_format($arr_product_sum["sum"] + $arr_product_sum["delivery"])?></strong><em>원</em></span>
				</span>

			</span>
		</div>
		<!-- / 계산하기 -->


		<input type="hidden" name="use_promotion_price" value="0"/>
		<?
		// 프로모션코드 적용 LMH005
		$_promotion_cnt = _MQ_result(" select count(*) from odtPromotionCode where pr_use = 'Y' and pr_expire_date >= CURDATE() ");
		if( $_promotion_cnt>0 ) {
		?>
		<div class="cm_code_box">
			<span class="code_txt">프로모션코드 <span class="promotion_text font-inherit"></span></span>
			<span class="code_form">
				<input type="text" name="promotion_code" value=""/>
				<span class="button_pack"><a href="#none" onclick="return false;" class="do_promotion_apply btn_md_black">적용하기</a></span>
				<span class="button_pack"><a href="#none" onclick="return false;" class="do_promotion_reset btn_md_white">적용취소</a></span>
			</span>
		</div>
		<script>
		$(document).ready(function(){
			$('input[name=promotion_code]').on('keypress',function(e){ if( e.which == 13 ){ e.preventDefault(); alert('우측 적용하기 버튼을 눌러주세요.'); } });
			$('.do_promotion_apply').on('click',function(){
				if( $('input[name=promotion_code]').val() == '' ) { alert('프로모션코드를 입력하세요.'); }
				else {
					$.ajax({
						data: {'mode':'promotion_code','promotion_code':$('input[name=promotion_code]').val()},
						type: 'POST',
						cache: false,
						url: '/pages/shop.cart.pro.php',
						dataType: 'JSON',
						success: function(data) {
							if(data['code']=='OK') {
								var use_promotion_price = data['result']['type']=='P' ? Math.floor(<?=$arr_product_sum["sum"]?>*data['result']['amount']/100) : data['result']['amount'];
								$('.promotion_text').text('('+String(data['result']['amount']).comma()+(data['result']['type']=='P'?'%':'원')+' 할인)');
								$('input[name=use_promotion_price]').val( use_promotion_price*1 );
								app_order_price();
							} else { alert(data['text']); $('.promotion_text').text(''); }
						},
						error:function(request,status,error){
							alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
						}
					});
				}
			});
			$('.do_promotion_reset').on('click',function(){
				$('input[name=use_promotion_price]').val(0); $('input[name=promotion_code]').val(''); app_order_price();
				$('.promotion_text').text('');
			});
		});
		</script>
		<? } // 프로모션코드 적용 LMH005 ?>

		<? if( !is_login() ) { ?>
		<input type="hidden" name="_use_point" value="0"/> <!-- LMH005 -->
		<? // SSJ: 2017-09-20 비회원주문시 이용약관 추가 ?>
		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>비회원 주문</strong> 이용약관 동의
			<div class="explain">비회원으로 주문하실 경우 다음 이용약관에 동의해 주셔야 합니다.</div>
		</div>
		<!-- / 단락타이틀 -->
		<!-- ●●●●●●●●●● 비회원일경우 약관동의하기 필요하면 사용 -->
		<div class="cm_order_agree">
			<textarea cols="" rows="" readonly class="scrollfix"><?=stripslashes($row_company['guideinfo'])?></textarea>
			<label><input type="checkbox" name="order_guideinfo" id="order_guideinfoorder_guideinfo" class="" value="Y"/> 위 방침을 읽고 동의합니다.</label>
		</div>
		<!-- / 동의하기 -->
		<? // SSJ: 2017-09-20 비회원주문시 이용약관 추가 ?>

		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>비회원 주문</strong> 개인정보수집 및 이용 동의
			<div class="explain">비회원으로 주문하실 경우 다음 개인정보수집 및 이용에 동의해 주셔야 합니다.</div>
		</div>
		<!-- / 단락타이틀 -->
		<!-- ●●●●●●●●●● 비회원일경우 약관동의하기 필요하면 사용 -->
		<div class="cm_order_agree">
			<textarea cols="" rows="" readonly class="scrollfix"><?=stripslashes($row_company['guestinfo'])?></textarea>
			<label><input type="checkbox" name="order_agree" id="order_agree" class="" value="Y"/> 위 방침을 읽고 동의합니다.</label>
		</div>
		<!-- / 동의하기 -->
		<? } ?>

		<? if( is_login() ) { ?>
		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>할인</strong> 및 <strong>최종결제금액</strong> 확인
			<div class="explain">사용가능한 쿠폰과 포인트를 확인하시고 혜택을 받으세요.</div>
		</div>
		<!-- / 단락타이틀 -->

		<!-- ●●●●●●●●●● 쿠폰 포인트 선택 -->
		<div class="cm_order_benefit">

			<!-- 쿠폰선택 -->
			<div class="coupon_box">
				<span class="title_box">쿠폰선택</span>
				<?
					$arr_coupon_member = array();
					$sres = _MQ_assoc(" select * from odtCoupon where coID='".get_userid()."' and coUse='N' ");
					if( count($sres)==0 ) {
				?>
					<!-- 내용없을경우 모두공통 -->
					<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">사용가능한 쿠폰이 없습니다.</div></div>
					<!-- // 내용없을경우 모두공통 -->
				<?
					} else {
						echo "<ul>";
						foreach( $sres as $k=>$v ){
							$delivery_sum_tmp = $arr_product_sum["delivery"] + $arr_product_sum["add_delivery"];
							if($v['coType'] == "무료배송쿠폰" &&  $v['coPrice'] > $delivery_sum_tmp)  { //무료배송 쿠폰일경우에는 쿠폰 금액을 배송비보다 크지 않도록 조절
								echo "
									<li>
										<div class='coupon_name'>".stripslashes($v['coName'])."</div>
										<span class='coupon_price'><strong>".number_format($v['coPrice'])."</strong>원 (".number_format($delivery_sum_tmp)."원 사용 가능)</span>
										<span class='coupon_ctrl'><label><input type='radio' name='use_coupon_member' class='use_coupon_member' value='".$v['coNo']."' />쿠폰적용</label></span>
										<span style='display:none;' ID='use_coupon_member_".$v['coNo']."'>".$v['coPrice']."</span>
									</li>
								";
							} else {
								echo "
									<li>
										<div class='coupon_name'>".stripslashes($v['coName'])."</div>
										<span class='coupon_price'><strong>".number_format($v['coPrice'])."</strong>원</span>
										<span class='coupon_ctrl'><label><input type='radio' name='use_coupon_member' class='use_coupon_member' value='".$v['coNo']."' />쿠폰적용</label></span>
										<span style='display:none;' ID='use_coupon_member_".$v['coNo']."'>".$v['coPrice']."</span>
									</li>
								";
							}
						}
						echo "</ul>";
					} // 쿠폰이 있을 경우
				?>
			</div> <!-- .coupon_box -->

			<!-- 포인트적용 -->
			<?
			/*$unable_point = _MQ("select ifnull(sum(gPrice),0) as sum from odtOrder where orderid = '".get_userid()."' and (paymethod = 'V' or paymethod = 'B') and paystatus != 'Y' and canceled == 'N' ");
			$able_point = $row_member['point'] - $unable_point['sum'];*/
			$able_point = $row_member['point'];
			if( $able_point < $row_setup['paypoint'] ) {
				$point_info_msg = "<dd>※ 적립금은 <strong>".number_format($row_setup['paypoint'])."</strong>원 이상부터 사용할 수 있습니다.</dd>";
			} else {
				if($row_setup['paypoint_limit']>0){
					$point_info_msg = "<dd>※ 적립금은 한번 주문 시 <strong>".number_format($row_setup['paypoint_limit'])."원</strong> 까지 사용할 수 있습니다.</dd>";
				}
			}
			if($unable_point['sum']>0){
				$point_info_msg .= "<dd>※ 결제대기중인 <strong>".number_format($unable_point['sum'])."</strong>원을 제외한 적립금입니다.</dd>";
			}
			?>
			<div class="point_box">
				<span class="title_box">적립금적용</span>
				<ul>
					<li>
						<div class="mypoint">
							<dl>
								<dt>현재 나의 적립금 : <strong><?=number_format($able_point)?></strong>원</dt>
								<?=$point_info_msg?>
							</dl>
						</div>

						<span class="apply_point">
							<!-- 천단위 콤마 찍기 -->
							<input type="text" name="_use_point" onkeypress="Only_Numeric();" class="number_style" value="0" <?=$able_point < $row_setup['paypoint'] ? "disabled" : "";?> />
							<span class="button_pack">
								<a href="#none" onclick="return false;" class="do_point_apply btn_md_black">적립금 적용하기</a>
								<a href="#none" onclick="return false;" class="do_point_reset btn_md_white">적용취소</a>
							</span>
						</span>
					</li>
				</ul>
				<script>
				$(document).ready(function(){
					$('input[name=_use_point]').on('focus',function(){ if( $(this).val()==0 ){ $(this).val(''); } });
					$('input[name=_use_point]').on('blur',function(){ if( $(this).val()=='' ){ $(this).val(0); } });
					$('.do_point_apply').on('click',function(){
						if( $('input[name=_use_point]').val() == '' || $('input[name=_use_point]').val().replace(/,/g,'')*1 == 0 ) { alert('포인트를 입력하세요.'); }
						else { sale_submit(); }
					});
					$('.do_point_reset').on('click',function(){ $('input[name=_use_point]').val(0); sale_submit(); });
				});
				</script>
			</div><!-- .point_box -->

		</div> <!-- .cm_order_benefit -->
		<? } ?>


		<!-- ●●●●●●●●●● 최종계산 -->
		<div class="cm_shop_last_sum">
			<span class="lineup">

				<span class="box normal_box">
					<span class="icon"></span>
					<span class="txt">상품합계금액</span>
					<span class="price"><strong><?=number_format($arr_product_sum["sum"])?></strong><em>원</em></span>
				</span>

				<span class="box plus_box">
					<span class="icon"></span>
					<span class="txt">총 배송비</span>
					<span class="price"><strong id="ID_total_delivery_price"><?=number_format($arr_product_sum["delivery"]+$arr_product_sum["add_delivery"])?></strong><em>원</em></span>
				</span>

				<span class="box minus_box">
					<span class="icon"></span>
					<span class="txt">총 할인금액</span>
					<span class="price"><strong id="ID_sale_point">0</strong><em>원</em></span>
				</span>

				<span class="box equal_box">
					<span class="icon"></span>
					<span class="txt">총 결제예상금액</span>
					<span class="price"><strong id="ID_total_price"><?=number_format($arr_product_sum["sum"] + $arr_product_sum["delivery"] + $arr_product_sum["add_delivery"])?></strong><em>원</em></span>
				</span>

			</span>
			<? if( is_login() ) { ?>
			<div class="save_point">본 주문으로 예상되는 적립금은 <strong><?=number_format($arr_product_sum["point"])?></strong>원 입니다.</div>
			<? } ?>
		</div>
		<!-- / 최종계산 -->

		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>주문자</strong> 정보
			<div class="explain"><img src="/pages/images/cm_images/member_form_bullet2.png" alt="필수" />표시된 것은 필수항목입니다.</div>
		</div>
		<!-- / 단락타이틀 -->

		<!-- ●●●●●●●●●● 주문자정보 -->
		<div class="cm_order_form">
			<input type="hidden" name="_ozip1" value="<?=$row_member['zip1']?>"/>
			<input type="hidden" name="_ozip2" value="<?=$row_member['zip2']?>"/>
			<input type="hidden" name="_oaddress" value="<?=$row_member['address']?>"/>
			<input type="hidden" name="_oaddress1" value="<?=$row_member['address1']?>"/>
			<input type="hidden" name="_oaddress_doro" value="<?=$row_member['address_doro']?>"/>
			<input type="hidden" name="_ozonecode" value="<?=$row_member['zonecode']?>"/>
			<? if( is_login() ) { ?>
			<input type="hidden" name="_oname" value="<?=$row_member['name']?>"/>
			<input type="hidden" name="_ohtel" value="<?=phone_print($row_member['htel1'],$row_member['htel2'],$row_member['htel3'])?>"/>
			<input type="hidden" name="_oemail" value="<?=trim($row_member['email'])?>"/>
			<ul>
				<!-- 필수항목 클래스값 추가 -->
				<li class="ess double">
					<span class="opt">주문자 이름</span>
					<div class="value"><input type="text" name="" class="input_design" placeholder="주문자 이름" value="<?=$row_member['name']?>" readonly/></div>
				</li>
				<li class="ess double">
					<span class="opt">주문자 휴대폰</span>
					<div class="value"><input type="text" name="" class="input_design" placeholder="주문자 휴대폰번호" value="<?=phone_print($row_member['htel1'],$row_member['htel2'],$row_member['htel3'])?>" readonly/></div>
				</li>
				<li class="ess ">
					<span class="opt">주문자 이메일</span>
					<div class="value"><input type="text" name="" class="input_design" placeholder="주문자 이메일주소 (아이디@주소)" style="width:39%" value="<?=$row_member['email']?>" readonly/></div>
				</li>
			</ul>
			<? } else { ?>
			<ul>
				<!-- 필수항목 클래스값 추가 -->
				<li class="ess double">
					<span class="opt">주문자 이름</span>
					<div class="value"><input type="text" name="_oname" class="input_design" placeholder="주문자 이름" /></div>
				</li>
				<li class="ess double">
					<span class="opt">주문자 휴대폰</span>
					<div class="value"><input type="text" name="_ohtel" class="input_design" placeholder="주문자 휴대폰번호" /></div>
				</li>
				<li class="ess ">
					<span class="opt">주문자 이메일</span>
					<div class="value"><input type="text" name="_oemail" class="input_design" placeholder="주문자 이메일주소 (아이디@주소)" style="width:39%"/></div>
				</li>
			</ul>
			<? } ?>
		</div>

		<? if($order_type == "coupon" || $order_type == "both") { // 쿠폰상품이 있을경우에만 노출 ?>
		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>사용자</strong> 정보
			<div class="explain"><img src="/pages/images/cm_images/member_form_bullet2.png" alt="필수" />표시된 것은 필수항목입니다.</div>
		</div>
		<!-- / 단락타이틀 -->
		<div class="cm_order_form">
			<ul>
				<!-- 필수항목 클래스값 추가 -->
				<li class="ess">
					<span class="opt">사용자 선택</span>
					<div class="value this_area_open">
						<label><input type="radio" name="_user_type" value="order" <?=is_login()?'checked':''?>/> 구매자 본인 사용</label>
						<label><input type="radio" name="_user_type" value="other"/> 다른 사용자</label>
					</div>
				</li>
				<li class="ess double">
					<span class="opt">사용자 이름</span>
					<div class="value"><input type="text" name="_uname" class="input_design" placeholder="사용자 이름" /></div>
				</li>
				<li class="ess double">
					<span class="opt">사용자 휴대폰</span>
					<div class="value"><input type="text" name="_uhtel" class="input_design" placeholder="사용자 휴대폰번호" /></div>
				</li>
				<li class="ess ">
					<span class="opt">사용자 이메일</span>
					<div class="value"><input type="text" name="_uemail" class="input_design" placeholder="사용자 이메일주소 (아이디@주소)" style="width:39%"/></div>
				</li>
			</ul>
		</div>
		<script>
		$(document).ready(function() {
			// 구매자 본인 정보를 자동입력한다.
			$("input[name=_uname]").val($("input[name=_oname]").val());
			$("input[name=_uhtel]").val($("input[name=_ohtel]").val());
			$("input[name=_uemail]").val($("input[name=_oemail]").val());
			$("input[name=_user_type]").on('click',function() {
				if($(this).val() == "order") {
					$("input[name=_uname]").val($("input[name=_oname]").val());
					$("input[name=_uhtel]").val($("input[name=_ohtel]").val());
					$("input[name=_uemail]").val($("input[name=_oemail]").val());
				} else {
					$("input[name=_uname]").val("");
					$("input[name=_uhtel]").val("");
					$("input[name=_uemail]").val("");
				}
			});
		});
		</script>
		<? } // 사용자정보 끝 ?>

		<? if($order_type == "product" || $order_type == "both") { // 배송상품이 있을경우에만 노출 ?>
		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>받는분(배송)</strong> 정보
			<div class="explain"><img src="/pages/images/cm_images/member_form_bullet2.png" alt="필수" />표시된 것은 필수항목입니다.</div>
		</div>
		<!-- / 단락타이틀 -->
		<div class="cm_order_form">
			<ul>
				<!-- 클래스값 추가/// ess:필수요소, 두칸으로 쓸 경우:double -->
				<li class="ess">
					<span class="opt">배송지 선택</span>
					<div class="value this_area_open">
						<label><input type="radio" name="_rtype" value="equal" id="_rtype_equal" <?=is_login()?'checked':''?>/>기본주소 (주문자 정보와 동일)</label>
						<label><input type="radio" name="_rtype" value="new" id="_rtype_new" />새로운 주소</label>
						<?
						// 과거 배송지 조회
						$arr_old_order = array();
						$sores = is_login() === true ? _MQ_assoc("select reczonecode,ordernum,recname,recemail,rechtel1,rechtel2,rechtel3,reczip1,reczip2,recaddress,recaddress1,recaddress_doro,left(orderdate,10) as ordate from odtOrder where orderid='".get_userid()."' and paystatus='Y' and order_type!='coupon' group by concat(recname,recaddress,recaddress1) order by orderdate desc") : array();
						if( count($sores) > 0 ) {
						?>
						<!-- 이전주소 선택할 경우 -->
						<label class="before_address"><input type="radio" name="_rtype" value="old" id="_rtype_old" />이전사용 주소선택
							<!-- 이전주소목록 선택하면 나옴, 선택을 해야 닫힘 -->
							<span class="open_box before_address_pop" style="display:none;">
							<? foreach($sores as $srk=>$srv) { ?>
								<span class="data before_address_apply"
									data-rname="<?=$srv['recname']?>"
									data-rhtel="<?=phone_print($srv['rechtel1'],$srv['rechtel2'],$srv['rechtel3'])?>"
									data-remail="<?=$srv['recemail']?>"
									data-rzip1="<?=$srv['reczip1']?>"
									data-rzip2="<?=$srv['reczip2']?>"
									data-raddress="<?=$srv['recaddress']?>"
									data-raddress1="<?=$srv['recaddress1']?>"
									data-raddress_doro="<?=$srv['recaddress_doro']?>"
									data-rzonecode="<?=$srv['reczonecode']?>"
								>
									<strong><?=$srv['recname']?></strong> [<?=$srv['recaddress'].' '.$srv['recaddress1']?>]
									<span class="button_pack">
										<a href="#none" onclick="return false;" class="btn_sm_white">주소선택</a>
									</span>
								</span>
							<? } ?>
							</span>
						</label>
						<!-- / 이전주소 선택할 경우 -->
						<script>
						$(document).ready(function(){
							$('.before_address').on('mouseenter',function(){ $('.before_address_pop').show(); });
							$('.before_address_pop').on('mouseleave',function(){ $('.before_address_pop').hide(); });
							$('.before_address_apply').on('click',function(){
								$('#_rtype_old').prop('checked',true); $('.before_address_pop').hide();
								$('input[name=_rname]').val($(this).data('rname'));
								$('input[name=_rhtel]').val($(this).data('rhtel'));
								$('input[name=_remail]').val($(this).data('remail'));
								$('input[name=_rzip1]').val($(this).data('rzip1'));
								$('input[name=_rzip2]').val($(this).data('rzip2'));
								$('input[name=_raddress]').val($(this).data('raddress'));
								$('input[name=_raddress1]').val($(this).data('raddress1'));
								$('input[name=_raddress_doro]').val($(this).data('raddress_doro'));
								$('input[name=_rzonecode]').val($(this).data('rzonecode'));
								add_delivery();
							});
						});
						</script>
						<? } ?>
					</div>
				</li>
				<?php
				// LDD018
				if($row_setup['reserv_del_use'] == 'Y') {
				?>
				<li class="">
					<span class="opt">배송일</span>
					<div class="value">
						<label><input type="checkbox" class="delivery_date_use" value="Y"> 요청일 지정</label>
						<input type="text" name="delivery_date" class="input_design" id="delivery_date" style="width:100px" disabled readonly>
					</div>
					<script>
						$(function() {

							$('.delivery_date_use').on('click', function() {

								var ck = $(this).is(':checked');
								if(ck === true) {

									$('#delivery_date').attr('disabled', false);
								}
								else {

									$('#delivery_date').attr('disabled', true);
									$('#delivery_date').val('');
								}
							});
							$("#delivery_date").datepicker({ // LMH004
								changeMonth: true, changeYear: true, minDate: 0, minDate: '+<?=$row_setup['reserv_del_term_min']?>d'<?php echo ($row_setup['reserv_del_term_max'] > 0?", maxDate: '+ ".$row_setup['reserv_del_term_max']."d'":null); ?>
							});
							$("#delivery_date").datepicker( "option", "dateFormat", "yy-mm-dd" );
							$("#delivery_date").datepicker( "option",$.datepicker.regional["ko"] );
						});
					</script>
				</li>
				<?php } ?>
				<li class="ess double">
					<span class="opt">받는분 이름</span>
					<div class="value"><input type="text" name="_rname" class="input_design" placeholder="받는분 이름" /></div>
				</li>
				<li class="ess double">
					<span class="opt">받는분 휴대폰</span>
					<div class="value"><input type="text" name="_rhtel" class="input_design" placeholder="받는분 휴대폰번호" /></div>
				</li>
				<li class="ess ">
					<span class="opt">받는분 이메일</span>
					<div class="value"><input type="text" name="_remail" class="input_design" placeholder="받는분 이메일주소 (아이디@주소)" style="width:39%"/></div>
				</li>
				<li class="ess ">
					<span class="opt">받는분 주소</span>
					<div class="value">
						<input type="text" name="_rzip1" id="_post1" readonly class="input_design" style="width:80px" /><span class="dash"></span>
						<input type="text" name="_rzip2" id="_post2" readonly class="input_design" style="width:80px" />
						<span class="button_pack"><a href="#none" onclick="new_post_view();return false;" title="" class="btn_md_black">주소찾기</a></span>
						<div class="input_double">
							<div class="input_wrap"><div><input type="text" name="_raddress" id="_addr1" readonly class="input_design" placeholder="기본주소"/></div></div>
							<div class="input_wrap"><div><input type="text" name="_raddress1" id="_addr2" class="input_design" placeholder="나머지 주소" /></div></div>
							<div class="tip_txt">
								<dl>
									<dt ID="add_delivery_string"><!-- 도서산간 추가배송비 메세지 출력 위치 --></dt>
								</dl>
							</div>
						</div>
					</div>
				</li>
				<li class="">
					<span class="opt">도로명 주소</span>
					<div class="value"><input type="text" name="_raddress_doro" id="_addr_doro" readonly class="input_design" placeholder="도로명 주소" />
						<div class="tip_txt">
							<dl>
								<dd>주소찾기를 통해 자동으로 입력됩니다.</dd>
							</dl>
						</div>
					</div>
				</li>
				<li class="">
					<span class="opt">새 우편번호</span>
					<div class="value"><input type="text" name="_rzonecode" id="_zonecode" readonly class="input_design" placeholder="국가기초구역번호" />
						<div class="tip_txt">
							<dl>
								<dd>주소찾기를 통해 자동으로 입력됩니다.</dd>
							</dl>
						</div>
					</div>
				</li>
				<li class="">
					<span class="opt">배송시 유의사항</span>
					<div class="value">
						<textarea cols="" rows="" name="_content" class="textarea_design"></textarea>
						<div class="tip_txt">
							<dl>
								<dd>부재시 연락처나 배송시 요청사항을 적어주세요 (200자 이내).</dd>
								<dd>개별 주문 시 요구사항은 반영하기가 어려우니 양해바랍니다.</dd>
								<dt>한번의 주문으로 다수의 배송지로 배송은 어렵습니다.</dt>
							</dl>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<script>
		$(document).ready(function(){
			$("input[name=_raddress1]").on('focus',function(){ add_delivery(); });

			// 구매자 정보를 미리 입력한다.
			$("input[name=_rname]").val($("input[name=_oname]").val());//주문자명->수령인명
			$("input[name=_rhtel]").val($("input[name=_ohtel]").val());//주문자휴대폰->수령인휴대폰
			$("input[name=_remail]").val($("input[name=_oemail]").val());//주문자휴대폰->수령인휴대폰
			$("input[name=_rzip1]").val($("input[name=_ozip1]").val());//주문자휴대폰->우편번호
			$("input[name=_rzip2]").val($("input[name=_ozip2]").val());//주문자휴대폰->우편번호
			$("input[name=_raddress]").val($("input[name=_oaddress]").val());//주문자휴대폰->주소1
			$("input[name=_raddress1]").val($("input[name=_oaddress1]").val());//주문자휴대폰->주소2
			$("input[name=_raddress_doro]").val($("input[name=_oaddress_doro]").val());//주문자휴대폰->주소2
			$("input[name=_rzonecode]").val($("input[name=_ozonecode]").val());//주문자주소->국가기초구역번호

			// - 배송지정보 radio 클릭 적용 ---
			$("input[name=_rtype]").on('click',function(e) {
				var _app_rtype = $("input[name=_rtype]").filter(function() {if (this.checked) return this;}).val();//체크값 확인
				switch(_app_rtype){
					// -- 주문정보와 동일 ---
					case "equal":
						$("input[name=_rname]").val($("input[name=_oname]").val());//주문자명->수령인명
						$("input[name=_rhtel]").val($("input[name=_ohtel]").val());//주문자휴대폰->수령인휴대폰
						$("input[name=_remail]").val($("input[name=_oemail]").val());//주문자휴대폰->수령인휴대폰
						$("input[name=_rzip1]").val($("input[name=_ozip1]").val());//주문자휴대폰->우편번호
						$("input[name=_rzip2]").val($("input[name=_ozip2]").val());//주문자휴대폰->우편번호
						$("input[name=_raddress]").val($("input[name=_oaddress]").val());//주문자휴대폰->주소1
						$("input[name=_raddress1]").val($("input[name=_oaddress1]").val());//주문자휴대폰->주소2
						$("input[name=_raddress_doro]").val($("input[name=_oaddress_doro]").val());//주문자휴대폰->주소2
						$("input[name=_rzonecode]").val($("input[name=_ozonecode]").val());//주문자주소->국가기초구역번호
						break;
					// -- 새로운 주소 ---
					case "new":
						$("input[name=_rname]").val("");
						$("input[name=_rhtel]").val("");
						$("input[name=_remail]").val("");
						$("input[name=_rzip1]").val("");
						$("input[name=_rzip2]").val("");
						$("input[name=_raddress]").val("");
						$("input[name=_raddress1]").val("");
						$("input[name=_raddress_doro]").val("");
						$("input[name=_rzonecode]").val("");
						break;
					// -- 과거배송지 ---
					case "old":
						e.preventDefault();
						return false;
						break;
				}
				add_delivery(); // 추가배송비 적용
			});
			// - 배송지정보 클릭시 적용 ---
		});
		</script>
		<? include_once dirname(__FILE__)."/../../newpost/newpost.search.php"; } // 배송지정보 끝 ?>

		<!-- ●●●●●●●●●● 단락타이틀 -->
		<div class="cm_shop_title">
			<strong>결제</strong> 정보
			<div class="explain">결제방법을 선택하시고 결제를 진행해주세요.</div>
		</div>
		<!-- / 단락타이틀 -->
		<div class="cm_order_form cm_order_last_step">
			<div class="thisis_price">
				<div class="upper_border line1"></div>
				<div class="upper_border line2"></div>
				<div class="upper_border line3"></div>
				<div class="upper_border line4"></div>
				<dl>
					<dt>최종결제 금액확인</dt>
					<dd><span class="lineup"><span class="unit_front">￦</span><strong id="ID_total_price2"><?=number_format($arr_product_sum["sum"] + $arr_product_sum["delivery"] + $arr_product_sum["add_delivery"])?></strong><span class="unit">원</span></span></dd>
				</dl>
			</div>
			<ul>
				<!-- <li class="ess">
					<span class="opt">최종 결제금액</span>
					<div class="value"><span class="sum_price">66,000</span>원</div>
				</li> -->
				<li class="ess">
					<span class="opt">결제수단 선택</span>
					<div class="value payway normal">
						<label class="use_card"><span class="lineup"><input type="radio" name="_paymethod" value="card" id="_paymethod_card" checked/>신용카드</span></label>
						<label class="use_real"><span class="lineup"><input type="radio" name="_paymethod" value="iche" id="_paymethod_iche"/>실시간 계좌이체</span></label>
						<label class="use_vert"><span class="lineup"><input type="radio" name="_paymethod" value="virtual" id="_paymethod_virtual"/>가상계좌</span></label>
						<label class="use_bank"><span class="lineup"><input type="radio" name="_paymethod" value="online" id="_paymethod_online"/>무통장 입금</span></label>
					</div>
					<div class="value payway allpoint" style="display:none;">
						<label class="use_point"><span class="lineup"><input type="radio" name="_paymethod" value="point" id="_paymethod_point"/>전액 적립금 결제</span></label>
					</div>
				</li>
				<li class="ess ID_paymethod_online" style="display:none;">
					<span class="opt">입금은행 선택</span>
					<div class="value">
						<?
						$arr_bank = array();
						$ex = _MQ_assoc("select * from odtBank order by inputdate asc");
						foreach( $ex as $k=>$v ){ $app_str = "[$v[bankname]] $v[banknum], $v[name]"; $arr_bank[$k] = $app_str; }
						echo _InputSelect("_bank",array_values($arr_bank),""," class='select_design' style='width:70%' ","","- 계좌 선택 -");
						?>
					</div>
				</li>
				<li class="ess ID_paymethod_online" style="display:none;">
					<span class="opt">입금예정일</span>
					<div class="value">
						<input type="text" name="paydate" id="paydate" class="input_design" value="<?=date('Y-m-d')?>" readonly style="width:150px" />
						<div class="tip_txt"><!-- LMH004 -->
							<dl>
								<dd>주문완료 후 <?=number_format($row_setup['P_B_DATE'])?>일 이내에 입금하지 않으시면 자동으로 주문이 취소됩니다.</dd>
							</dl>
						</div>
					</div>
				</li>
				<li class="ess ID_paymethod_online" style="display:none;">
					<span class="opt">입금자명</span>
					<div class="value">
						<input type="text" name="_deposit" class="input_design" style="width:150px" value="<?=$row_member['name']?>" />
						<label><input type="checkbox" name="_get_tax" value="Y"/>현금영수증 발행신청</label>
					</div>
				</li>
				<!-- <li class="ess ID_paymethod_online ID_paymethod_virtual">
					<span class="opt">현금영수증</span>
					<div class="value">
						<label><input type="checkbox" name="_get_tax" value="Y"/>현금영수증 발행을 신청합니다</label>
					</div>
				</li> -->
			</ul>
		</div>

		<div class="cm_order_form">
			<ul>
				<li class="ess">
					<span class="opt">구매확인</span>
					<div class="value">
						<label>
							<input type="checkbox" name="_yes_buy" value="Y">
							<font color="#FF0000">구매하실 상품의 상품명, 발행일등의 상품정보 및 가격을 확인하였으며, 이에 동의합니다.</font>(전자상거래법 제8조 제2항)
						</label>
					</div>
				</li>
			</ul>
		</div>

		<!-- ●●●●●●●●●● 페이지 이용도움말 -->
		<div class="cm_user_guide">
			<dl>
				<dt>주문 시 유의사항을 알려드립니다.</dt>
				<dd>메인상품중에 주문하실 상품과 수량을 확인하시고 상품금액과 배송비를 확인해주세요.</dd>
				<dd>1회 주문 시 <strong>상품별 최대 구매 가능한 수량에 제한</strong>이 있으며 또한 <strong>반복 주문할 수 있는 횟수도 제한</strong>이 있습니다.</dd>
				<dd>서브상품(옵션상품)이 있는경우, 메인상품 주문자에 한해 주문 가능하며 서브상품만 따로 주문하실 수 없습니다.</dd>
				<dd>주문고객정보와 배송정보를 정확히 기입해주십시오. (회원정보를 수정해 놓으면 편리하게 이용하실 수 있습니다)</dd>
				<dd>결제방법은 <strong>카드결제, 실시간계좌이체, 가상계좌, 무통장입금, 전액적립금결제</strong>가 있습니다.</dd>
				<dd>품절된 상품은 주문하실 수 없습니다.</dd>
			</dl>
		</div>
		<!-- / 페이지 이용도움말 -->

		<!-- ●●●●●●●●●● 가운데정렬버튼 -->
		<div class="cm_bottom_button">
			<ul>
				<li><span class="button_pack"><a href="#none" onclick="if(confirm('작성중인 주문정보가 있습니다.\n이전페이지로 이동하시겠습니까?')){location.href=('/?pn=shop.cart.list');}return false;" title="" class="btn_lg_white">이전단계</a></span></li>
				<li><span class="button_pack"><a href="#none" onclick="order_submit();return false;" title="" class="btn_lg_color">결제하기</a></span></li>
			</ul>
		</div>
		<!-- // 가운데정렬버튼 -->


		<input type="hidden" name="price_sum" value="<?=$arr_product_sum['sum']?>"/><!-- 구매총액 -->
		<input type="hidden" name="price_total" value="<?=($arr_product_sum['sum'] + $arr_product_sum['delivery']+$arr_product_sum['add_delivery'])?>"/><!-- 총결제액 -->
		<input type="hidden" name="price_delivery" value="<?=$arr_product_sum['delivery']+$arr_product_sum['add_delivery']?>"/><!-- 배송비(추가배송비 포함) -->
		<input type="hidden" name="price_add_delivery" value="<?=$arr_product_sum['add_delivery']?>"/><!-- 추가배송비 -->
		<input type="hidden" name="app_point" value="<?=ceil($arr_product_sum['point'])?>"/><!-- 제공해야할 포인트 -->
		<input type="hidden" name="able_point" value="<?=$able_point?>"/><!-- 사용가능포인트 -->
		<input type="hidden" name="use_coupon_price_member"/><!-- 사용한 사용자쿠폰금액-->
		<input type="hidden" name="use_coupon_price_product"/><!-- 사용한 상품쿠폰금액-->
		<input type="hidden" name="price_total_backup" value="<?=($arr_product_sum['sum'] + $arr_product_sum['delivery']+$arr_product_sum['add_delivery'])?>"/><!-- 총결제액 - 백업용(도서산간-배송비제외) -->
		<input type="hidden" name="price_delivery_backup" value="<?=$arr_product_sum['delivery']+$arr_product_sum['add_delivery']?>"/><!-- 배송비(추가배송비 포함) - 백업용(도서산간-배송비제외) -->

		</form>

	</div> <!-- .layout_fix -->
	</div> <!-- .common_page -->


<script src="/include/js/jquery/jquery.formatCurrency-1.4.0.min.js"></script>
<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
$(function() {
	$( "#paydate" ).datepicker({ // LMH004
		changeMonth: true, changeYear: true, minDate: 0, maxDate: '+<?=$row_setup[P_B_DATE]?>d'
	});
	$( "#paydate" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
	$( "#paydate" ).datepicker( "option",$.datepicker.regional["ko"] );
});

// - 결제를 위한 폼 전송 ---
function order_submit() {
	// 실결제금액 1000원 이상 체크
	var app_price_total = $("input[name='price_total']").val()*1;
	if( app_price_total < 1000 && app_price_total != 0 ){ alert("실제 결제금액은 1,000원 이상이어야 합니다."); }
	else{$("form[name=frm]").submit();}
}
// - 결제를 위한 폼 전송 ---

// - 결제금액 확인 및 적용 ---
function app_order_price() {

	// 적용할 쿠폰금액 체크
	var _app_coupon_uid = _app_coupon_member_price = _app_coupon_product_price = coupon_product_price = 0;
	// 프로모션코드 체크 LMH005
	var _app_promotion_price = $('input[name=use_promotion_price]').val()*1;

	// 사용자 쿠폰
	ind_coupon_cnt = $(".use_coupon_member").length;
	for(i=0;i<ind_coupon_cnt;i++) {
		if( $(".use_coupon_member").eq(i).attr("checked") == "checked" ) {
			_app_coupon_uid = $(".use_coupon_member").eq(i).val();
		}
	}
	_app_coupon_member_price = $("#use_coupon_member_" + _app_coupon_uid).text()*1;//사용자 쿠폰

	// 상품쿠폰
	product_coupon_cnt = $(".product_coupon_check").length;
	for(i=0;i<product_coupon_cnt;i++) {
		if( $(".product_coupon_check").eq(i).attr("checked") == "checked" ) {
			coupon_product_price += $(".product_coupon_check").eq(i).val()*1;
		}
	}
	_app_coupon_product_price = coupon_product_price; //상품쿠폰

	// 쿠폰 할인 총액
	_app_coupon_total_price = _app_coupon_product_price*1 + _app_coupon_member_price*1;
	// 쿠폰 할인 총액 (프로모션코드 추가) LMH005
	_app_coupon_total_price = _app_coupon_total_price*1 + _app_promotion_price;

	// 총 결제액 = 구매총액 + 배송비 - 사용포인트
	var _price_total = $("input[name=price_sum]").val()*1 + $("input[name=price_delivery]").val()*1 - $("input[name=_use_point]").val().replace(/,/g,'')*1 - _app_coupon_member_price*1 - _app_coupon_product_price*1;
	// 총 결제액 (프로모션코드 추가) LMH005
	_price_total = _price_total - _app_promotion_price;

	// 총 결제액이 0보다 작을경우....
	if(_price_total < 0) {
		alert("할인금액이 총 결제 금액을 초과하였습니다.");

		// 사용한 포인트가 있으면 포인트를 초기화.
		$("input[name=_use_point]").val(0);

		// 사용쿠폰이 있다면 클릭 해제
		$(".use_coupon_member").attr("checked",false);

		// 프로모션코드 초기화 LMH005
		$('input[name=promotion_code]').val(''); $('input[name=use_promotion_price]').val(0); $('.promotion_text').text('');

		// 함수 재실행.
		app_order_price();
		return;
	}

	// 총할인금액.
	_total_sale_point = $("input[name=_use_point]").val().replace(/,/g,'')*1 + _app_coupon_total_price*1;
	$("#ID_use_point").html($("input[name=_use_point]").val().replace(/,/g,'')*1).formatCurrency({ symbol: '', roundToDecimalPlace: 0 }); // 포인트 사용금액 - 합계표
	$("#ID_use_coupon").html(_app_coupon_total_price*1).formatCurrency({ symbol: '', roundToDecimalPlace: 0 }); // 쿠폰 총 할인금액 - 합계표
	$("#ID_total_price , #ID_total_price2").html(_price_total*1).formatCurrency({ symbol: '', roundToDecimalPlace: 0 }); // 총결제액적용 - 합계표
	$("#ID_sale_point").html(_total_sale_point).formatCurrency({ symbol: '', roundToDecimalPlace: 0 }); // 총할인금액.
	$("input[name=price_total]").val(_price_total); // 총결제액적용 - input
	$("input[name=use_coupon_price_member]").val(_app_coupon_member_price); // 사용한 보너스쿠폰금액 - input
	$("input[name=use_coupon_price_product]").val(_app_coupon_product_price); // 사용한 상품쿠폰금액 - input

	app_order_all_point();	// 전액 적립금 결제인지 체크하여 처리.
}
// - 결제금액 확인 및 적용 ---

// - 전액 적립금 결제 (굳이 적립금 결제가 아니더라도, 할인 쿠폰통해 총결제금액이 0이 된 주문도 함께 체크하여 처리한다)
function app_order_all_point(){

	// 총결제금액이 0원이고, 할인금액이 존재한다면 전액 적립금결제로 보고 처리 LMH005
	if(($("input[name=price_total]").val()*1 == 0) && ( ($("input[name=_use_point]").val().replace(/,/g,'')*1 + $("input[name=use_coupon_price_member]").val()*1 + $("input[name=use_coupon_price_product]").val()*1 + $("input[name=use_promotion_price]").val().replace(/,/g,'')*1 ) > 0)) {
		$("#normal_paystatus").hide();
		$("#point_paystatus").show();
		$("#_paymethod_point").prop("checked",true);
		$('.payway.normal').hide(); $('.payway.allpoint').show();
	} else {
		$("#normal_paystatus").show();
		$("#point_paystatus").hide();
		$("#_paymethod_card").prop("checked",true);
		$('.payway.normal').show(); $('.payway.allpoint').hide();
	}
	$('.ID_paymethod_online').hide();

}
// - 전액 적립금 결제

// - 포인트 입력 시 사전 체크 및 적용 ---
function sale_submit() {

	<?if($row_setup[paypoint_limit] > 0) {?>
	if($("input[name=_use_point]").val().replace(/,/g,'')*1 > <?=$row_setup[paypoint_limit]?> * 1 ) {
		alert("적립금은 한번 주문 시 <?=number_format($row_setup[paypoint_limit])?>원까지 사용가능합니다.");
		$("input[name=_use_point]").val(0);
	}
	<?}?>

	if($("input[name=_use_point]").val().replace(/,/g,'')*1 > $("input[name=able_point]").val() * 1 ) {
		alert("보유포인트 보다 큰  포인트를 입력하실 수 없습니다.");
		$("input[name=_use_point]").val(0);
	}

	app_order_price();
};
// - 포인트 입력 시 사전 체크 및 적용 ---

/* 추가배송비개선 - 2017-05-19::SSJ  */
// - 추가 배송비 적용비 체크 ---
function add_delivery(){

    var app_addr = $("#_addr1").val(); // 지번주소
    if(app_addr == undefined) app_addr = "";
    var app_addr2 = $("#_addr2").val(); // 상세주소
    if(app_addr2 == undefined) app_addr2 = "";
    var app_addr_doro = $("#_addr_doro").val(); // 도로명주소
    if(app_addr_doro == undefined) app_addr_doro = "";

    // - 초기화 ---
    $("input[name=price_delivery]").val( $("input[name=price_delivery_backup]").val() );// 총배송비 초기화
    $("input[name=price_total]").val( $("input[name=price_total_backup]").val() );// 총결제액 초기화

    $.ajax({
        url: "/pages/ajax.delivery.addprice_new.php",
        cache: false,
        type: "POST",
		async: false,
        data: "app_addr=" + app_addr + "&app_addr2=" + app_addr2 + "&app_addr_doro="+app_addr_doro,
        success: function(data){

            // 추가배송비 적용
            $(".js_delevery_addprice").val(data);
            // 개별배송 추가배송비 수정
            $(".js_delevery_addprice_unit").each(function(){
                var _pcnt = $(this).data("pcnt");
                $(this).val(data*_pcnt);
            });

            // 추가배송비 합계적용
            var app_add_delivery_price = 0; // 합계 추가배송비
            $(".js_delevery_addprice").each(function(){
                app_add_delivery_price += $(this).val()*1;
            });

            if(app_add_delivery_price > 0 ) {
                $(".add_delivery_info").show(); // 추가배송 안내 부분 보임
                $("#add_delivery_string").html("도서 산간 지역에 대한 추가 배송비 " + app_add_delivery_price.toString().comma() + "원이 적용되었습니다." ); // 문구추가
                $(".js_delevery_addprice_print").html("<span class='shop_state_pack'><span class='orange'>추가배송비</span></span><span class='shop_state_pack'><span class='orange'>+" + data.toString().comma() + "원</span></span>");
                // 개별배송 추가배송비 수정
                $(".js_delevery_addprice_unit_print").each(function(){
                    var _pcnt = $(this).data("pcnt");
                    $(this).html("<span class='shop_state_pack'><span class='orange'>추가배송비</span></span><span class='shop_state_pack'><span class='orange'>+" + (data * _pcnt).toString().comma() + "원</span></span>");
                });
            } else {
                $(".add_delivery_info").hide(); // 추가배송 안내 부분 숨김
                $("#add_delivery_string").html(""); // 문구초기화
                $(".js_delevery_addprice_print").html(""); // 문구초기화
            }

            $("input[name=price_add_delivery]").val( parseInt(app_add_delivery_price) );// 추가 배송비
            $("input[name=price_delivery]").val( parseInt($("input[name=price_delivery_backup]").val()) + parseInt(app_add_delivery_price) );// 총배송비에 추가 배송비를 합산
            $("input[name=price_total]").val( parseInt($("input[name=price_total_backup]").val()) + parseInt(app_add_delivery_price) );// 총결제액에 추가배송비 합산

            app_order_price();
            $("#delivery_price_smallsum").html(app_add_delivery_price.toString().comma()); // 배송비 가격 적용
            $("#ID_total_delivery_price").html($("input[name=price_delivery]").val().toString().comma()); // 배송비 가격 적용
            $("#ID_total_price_smallsum").html($("input[name=price_total]").val().toString().comma()); // 총 상품금액
        }
    });

}
// - 추가 배송비 적용비 체크 ---
/* 추가배송비개선 - 2017-05-19::SSJ  */

var use_coupon_member = '';
$(document).ready(function(){
	add_delivery();

	// - 사용자쿠폰 선택 처리 ---
	$('.use_coupon_member').on('click',function(){
		if( use_coupon_member == $(this).val() ) {
			$(this).prop('checked',false); use_coupon_member = ''; sale_submit();
		} else {
			$(this).prop('checked',true); use_coupon_member = $(this).val(); sale_submit();
		}
	});
	// - 사용자쿠폰 선택 처리 ---

	// - 결제방식 radio 클릭 적용 ---
	$("input[name=_paymethod]").on('click',function() {
		var _app_paymethod = $("input[name=_paymethod]").filter(function() {if (this.checked) return this;}).val(); //체크값 확인
		if( _app_paymethod == "online" ) { $(".ID_paymethod_online").show(); } // 무통장입금테이블 노출
		else {
			$(".ID_paymethod_online").hide(); // 무통장입금테이블 숨김
			<? if(in_array($row_setup['P_KBN'],array('D'))) { ?>
			if(_app_paymethod == "virtual") { $(".ID_paymethod_virtual").show(); } // 현금영수증 발행 테이블 노출
			<? } ?>
		}
	});
	// - 결제방식 radio 클릭 적용 ---

	// - 주문서 validate ---
	$("form[name=frm]").validate({
		ignore: "input[type=text]:hidden",
		rules: {
			// --사전 체크 :: 주문자정보 ---
			<? if(!is_login()){ ?>order_guideinfo: { required: true },<? } ?>
			<? if(!is_login()){ ?>order_agree: { required: true },<? } ?>
			_oname: { required: true },
			_ohtel: { required: true },
			_oemail: { required: true , email:true},
			// --사전 체크 :: 사용자정보 ---
			_uname: { required: true },
			_uhtel: { required: true },
			_uemail: { required: true , email:true},
			// --사전 체크 :: 배송지정보 ---
			_rname: { required: true },
			_rhtel: { required: true },
			_rzip1: { required: false },
			_rzip2: { required: false },
			_raddress: { required: true },
			_raddress1: { required: true },
			// --사전 체크 :: 결제입력정보 ---
			_bank:{required: function() { return ($("input[name=_paymethod]").filter(function() {if (this.checked) return this;}).val() == "online" ? true : false );}},
			_deposit:{required: function() { return ($("input[name=_paymethod]").filter(function() {if (this.checked) return this;}).val() == "online" ? true : false );} },
			_yes_buy: { required:true }

		},
		messages: {
			// --사전 체크 :: 주문자정보 ---
			<? if(!is_login()){ ?>order_guideinfo: { required: "이용약관에 동의해주시기 바랍니다." },<? } ?>
			<? if(!is_login()){ ?>order_agree: { required: "개인정보수집 및 이용에 동의해주시기 바랍니다." },<? } ?>
			_oname: { required: "주문자 이름을 입력해주시기 바랍니다."},
			_ohtel: { required: "휴대폰번호을 입력해주시기 바랍니다." },
			_oemail: { required: "이메일을 입력해주시기 바랍니다." , email:"이메일이 바르지 않습니다."},
			// --사전 체크 :: 사용자정보 ---
			_uname: { required: "사용자 이름을 입력해주시기 바랍니다."},
			_uhtel: { required: "사용자 휴대폰번호을 입력해주시기 바랍니다." },
			_uemail: { required: "이메일을 입력해주시기 바랍니다." , email:"이메일이 바르지 않습니다."},
			// --사전 체크 :: 배송지정보 ---
			_rname: { required: "받는분 이름을 입력해주시기 바랍니다."},
			_rhtel: { required: "받는분 휴대폰번호를 입력해주시기 바랍니다." },
			_rzip1: { required: "우편번호를 입력해주시기 바랍니다."},
			_rzip2: { required: "우편번호를 입력해주시기 바랍니다."},
			_raddress: { required: "주소를 입력해주시기 바랍니다."},
			_raddress1: { required: "상세주소를 입력해주시기 바랍니다."},
			// --사전 체크 :: 결제입력정보 ---
			_paymethod: { required: "결제방식을 선택해주시기 바랍니다." },
			_bank:{ required: "무통장 계좌정보를 선택해주시기 바랍니다." },
			_deposit:{ required: "무통장 입금자명을 입력해주시기 바랍니다." },
			_yes_buy: { required:"구매내용을 확인후 구매내용 확인 동의를 해주시기 바랍니다." }
		}
	});
	// - 주문서 validate ---
});
</script>