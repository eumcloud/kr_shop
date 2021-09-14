<?php

	clean_cart(); // 장바구니 판매불가 상품 삭제

	// JJC003 묶음배송
	//	$arr_cart ==> 장바구니 정보 
	//	$arr_customer ==> 입점업체 정보 저장
	//	$arr_delivery ==> 업체별 배송비 정보
	//	$arr_product_info ==> 장바구니 상품정보
	include(dirname(__FILE__)."/shop.cart.inc.php");


?>

<div class="common_page common_none">

	<!-- ●●●●●●●●●● 타이틀상단 -->
	<div class="cm_common_top">
		<div class="commom_page_title">
			<span class="icon_img"><img src="/pages/images/cm_images/icon_top_cart.png" alt="" /></span>
			<dl>
				<dt>장바구니</dt>
				<dd>결제하기 전 옵션 및 수량 등을 꼭 확인해주세요.</dd>
			</dl>
		</div>

		<!-- 단계별 페이지가 있을경우 -->
		<div class="progress">
			<span class="box hit"><strong>STEP.1</strong>장바구니</span>
			<span class="box"><strong>STEP.2</strong>주문결제</span>
			<span class="box"><strong>STEP.3</strong>주문완료</span>
		</div>

	</div>
	<!-- / 타이틀상단 -->

</div>

<div class="common_page">
<div class="layout_fix">

	<!-- ●●●●●●●●●● 장바구니리스트  -->
	<div class="cm_shop_cart_list">

		<?
		// JJC003 묶음배송
		$arr_product_sum = $arr_product = array();
		if( count($arr_cart)==0 ) { // 장바구니 상품 없을때
		?>
		<!-- 내용없을경우 모두공통 -->
		<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">장바구니에 담긴 상품이 없습니다.</div></div>
		<!-- // 내용없을경우 모두공통 -->
		<? } else { ?>
			<form name="frm" method="post">
			<input type="hidden" name="mode" value=""/>
			<input type="hidden" name="cuid" value=""/>
			<input type="hidden" name="code" value=""/>
			<input type="hidden" name="allcheck" value="Y"/>
			<?


				foreach($arr_cart as $crk=>$crv) {


			?>
			<!-- 입점업체 묶음 반복구간 -->
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
					<col width="3%"/><col width="*"/><col width="12%"/><col width="13%"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"></th>
						<th scope="col">상품정보</th>
						<th scope="col">상품합계</th>
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
											<span class='option_number'>".$option_tmp_stock."</span>
											<span class='option_price'><strong>".number_format($option_tmp_price)."</strong>원</span>
											<a href='#none' onclick='cart_option_delete(".$sv['c_uid'].");return false;' class='option_delete' title='삭제'><span class='ic_delete'></span></a>
										</div>
									</dd>
								";

								//상품수 , 포인트 , 상품금액
								$arr_product["cnt"] += $option_tmp_cnt;//상품수
								$sum_product_cnt += $option_tmp_cnt ;// |개별배송패치| - 상품갯수를 가져온다 : 해당 코드가 없을 시 추가
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
											<span class='option_number'>".$option_tmp_stock."</span>
											<span class='option_price'><strong>".number_format($option_tmp_price)."</strong>원</span>
											<a href='#none' onclick='cart_option_delete(".$sv['c_uid'].");return false;' class='option_delete' title='삭제'><span class='ic_delete'></span></a>
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

				?>
					<tr>
						<td><label><input type="checkbox" class="cls_code" name="_code[]" value="<?=$pr['code']?>" checked="checked" title="선택" /></label></td>
						<td>
							<!-- 상품사진 -->
							<a href="<?=rewrite_url($pr['code'])?>" class="thumb" title="상품보기" target="_blank"><img src="<?=product_thumb_img( $pr , '장바구니' ,  'data')?>" alt="<?=$pro_name?>"/></a>
							<!-- 상품정보 -->
							<div class="item_name">
								<dl>
									<dt><a href="<?=rewrite_url($pr['code'])?>" title="상품보기" target="_blank"><?=$pro_name?></a></dt>
									<?=$option_html?>
								</dl>
							</div>
						</td>
						<!-- 수량합계금액 -->
						<td class="pointbg"><strong><?=number_format($sum_price)?></strong>원</td>
						<!-- 배송비 -->
						<td class="pointbg">
<?php
	$app_delivery = "-";
	if($pr['setup_delivery'] == "Y"){
		switch($pr['del_type']){
			case "unit":
				$app_delivery = "<b>" . number_format($pr['del_price'] * $sum_product_cnt) . "</b>원<br>개별배송"; 
				$arr_product["delivery"]+= $pr['del_price'] * $sum_product_cnt; // |개별배송패치| - $sum_product_cnt : 상품갯수를 곱해준다.
				$cart_delivery_price = $pr['del_price'] * $sum_product_cnt;// 선택 구매 2015-12-04 LDD // |개별배송패치| - $sum_product_cnt : 상품갯수를 곱해준다.
				break;
			case "free": $app_delivery = "무료배송"; $cart_delivery_price = 0; break;
			case "normal":
				$app_delivery = "-";
				if($del_chk_customer <> $crk) {
					$app_delivery = ($arr_customer[$crk]['app_delivery_price'] <> 0 ? "<b>" . number_format($arr_customer[$crk]['app_delivery_price']) . "</b>원" : "배송비무료") ;
					$arr_product["delivery"]+=$arr_customer[$crk]['app_delivery_price'];
					$del_chk_customer = $crk;
					$cart_delivery_price = $arr_customer[$crk]['app_delivery_price'];// 선택 구매 2015-12-04 LDD
				}
				break;
		}
	}
	else {
		$app_delivery = "해당없음"; 
	}
	echo $app_delivery;
?>
<input type="hidden" name="cart_price_<?=$pr['code']?>" value="<?=($sum_price?$sum_price:0)?>"/>
<input type="hidden" name="cart_delivery_<?=$pr['code']?>" value="<?=($cart_delivery_price?$cart_delivery_price:0)?>"/>

						</td>
					</tr>
				<? } ?>
				</tbody> 
			</table>
			<? } // 업체별 foreach ?>
			</form>

		<!-- 제어버튼 -->
		<div class="ctrl_btn">
			<span class="button_pack"><a href="#none" onclick="selectAll();return false;" class="btn_sm_white">전체상품 선택/해제</a></span>
			<span class="button_pack"><a href="#none" onclick="cart_select_delete();return false;" class="btn_sm_black">선택상품 삭제</a></span>
		</div>

		<? } // 장바구니 상품 있을때 ?>

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
					<span class="price"><strong id="cart_price"><?=number_format($arr_product_sum["sum"])?></strong><em>원</em></span>
				</span>

				<span class="box plus_box">
					<span class="icon"></span>
					<span class="txt">총 배송비</span> 
					<span class="price"><strong id="cart_delivery"><?=number_format($arr_product_sum["delivery"])?></strong><em>원</em></span>
				</span>

				<!-- <span class="box minus_box">
					<span class="icon"></span>
					<span class="txt">총 할인금액</span> 
					<span class="price"><strong>3,000</strong><em>원</em></span>
				</span> -->

				<span class="box equal_box">
					<span class="icon"></span>
					<span class="txt">총 결제예상금액</span> 
					<span class="price"><strong id="cart_total"><?=number_format($arr_product_sum["sum"] + $arr_product_sum["delivery"])?></strong><em>원</em></span>
				</span>

			</span>
		</div>
		<!-- / 계산하기 -->

		<!-- ●●●●●●●●●● 가운데정렬버튼 -->
		<div class="cm_bottom_button">
			<ul>
				<li><span class="button_pack"><a href="/" class="btn_lg_white">쇼핑계속하기<span class="edge"></span></a></span></li>
				<? if( count($arr_cart)>0 ) { if( is_login() ) { ?>
				<li><span class="button_pack"><a href="#none" onclick="cart_submit();return false;" class="btn_lg_color">회원구매하기<span class="edge"></span></a></span></li>
				<? } else { ?>
				<li><span class="button_pack"><a href="#none" onclick="cart_submit();return false;" class="btn_lg_black">비회원구매하기<span class="edge"></span></a></span></li>
				<? }} ?>
			</ul>
		</div>
		<!-- / 가운데정렬버튼 -->


	</div> <!-- .cm_shop_cart_list -->


</div> <!-- .layout_fix -->
</div> <!-- .common_page -->



<script>

////////////////////////////// 선택 구매 2015-12-04 LDD {
$(document).ready(function(){
	$('.cls_code').on('change',function(){ get_cart_price(); });
});

// 카트 총 결제금액 계산
function get_cart_price(){
	var cart_price = 0, cart_delivery = 0, cart_total = 0;
	$('.cls_code:checked').each(function(){
		cart_price += $('input[name=cart_price_'+$(this).val()+']').val()*1;
		cart_delivery += $('input[name=cart_delivery_'+$(this).val()+']').val()*1;
	});
	cart_total = cart_price + cart_delivery;

	$('#cart_price').text(String(cart_price).comma());
	$('#cart_delivery').text(String(cart_delivery).comma());
	$('#cart_total').text(String(cart_total).comma());
}

// 카트 -> 주문서작성
function cart_submit() {
	$("input[name=mode]").val("select_buy");
	document.frm.action = "/pages/shop.cart.pro.php";
	document.frm.submit();
}
////////////////////////////// 선택 구매 2015-12-04 LDD }

	// - 개별상품(옵션) 삭제 ---
	function cart_option_delete(cuid) {
		if(confirm('정말 삭제하시겠습니까?')){
			$("input[name=mode]").val("select_option_onlydelete");
			$("input[name=cuid]").val(cuid);
			document.frm.action = "/pages/shop.cart.pro.php";
			document.frm.submit();
		}
	}
	// - 개별상품(옵션) 삭제 ---

	// - 선택상품(옵션) 삭제 ---
	function cart_delete(code) {
		if(confirm('정말 삭제하시겠습니까?')){
			$("input[name=mode]").val("select_onlydelete");
			$("input[name=code]").val(code);
			document.frm.action = "/pages/shop.cart.pro.php";
			document.frm.submit();
		}
	}
	// - 선택상품(옵션) 삭제 ---

	// - 선택상품 삭제 ---
	function cart_select_delete() {
		if($(".cls_code:checkbox:checked").length == 0 ) {
			alert("1개 이상 선택해주시기 바랍니다.");
		}
		else {
			$("input[name=mode]").val("select_delete");
			document.frm.action = "/pages/shop.cart.pro.php";
			document.frm.submit();
		}
	}
	// - 선택상품 삭제 ---


	// - 선택상품 수량변경 ---
	function cart_modify(cuid) {
		if($("#cart_cnt_"+cuid).val() < 1) {
			alert("수량을 선택해주세요.");
			return false;
		}

		$("input[name=mode]").val("select_modify");
		$("input[name=cuid]").val(cuid);
		document.frm.action = "/pages/shop.cart.pro.php";
		document.frm.submit();
	}
	// - 선택상품 수량변경 ---

	// - 전체선택 / 반전 ---
	function selectAll() {
		if( $("input[name=allcheck]").val() == 'Y' ) {
			$(".cls_code").attr("checked",false);
			$("input[name=allcheck]").val('N');
		} else {
			$(".cls_code").attr("checked",true);
			$("input[name=allcheck]").val('Y');
		}
	}
</script>