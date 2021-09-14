<?PHP
# LDD007
// 페이지 표시
$app_current_link = "/totalAdmin/_order3.list.php";
include_once("inc.header.php");

$settlementstatus = 'ready';
$settlementstatus = $settlementstatus ? $settlementstatus : "ready";


// 검색 체크
$s_query = " where op.op_settlementstatus='". $settlementstatus ."' and o.paystatus='Y' and o.paystatus2='Y' and o.canceled='N' AND o.orderstatus='Y' ";
//if( $pass_sdate && $pass_edate ) { $s_query .= " AND left(o.orderdate,10) between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 검색기간
if( $pass_sdate && $pass_edate ) { $s_query .= " AND ( left(op.op_expressdate,10) between '". $pass_sdate ."' and '". $pass_edate ."' or left(op.op_coupon_use,10) between '". $pass_sdate ."' and '". $pass_edate ."') "; }// - 검색기간
else if( $pass_sdate ) { $s_query .= " AND left(op.op_expressdate,10) >= '". $pass_sdate ."' "; }
else if( $pass_edate ) { $s_query .= " AND left(op.op_expressdate,10) <= '". $pass_edate ."' "; }

if( $pass_orderproduct_type ) { $s_query .= " AND op.op_orderproduct_type = '". $pass_orderproduct_type ."' "; }// 판매형태
if( $pass_company ) { $s_query .= " AND op.op_partnerCode = '". $pass_company ."' "; }// 공급업체
if( $pass_pname ) { $s_query .= " AND op.op_pname like '%". $pass_pname ."%' "; }//상품명
if($pass_paymethod) { $s_query .= " and o.paymethod = '".$pass_paymethod."' "; } // 결제수단

// 현 페이지 주문번호 추출
$queP = "
    SELECT 
		op.*, o.* ,
		IF( 
			op.op_comSaleType='공급가' ,  
			((op.op_supply_price+op.op_poptionpurprice) * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price) , 
			((op.op_pprice+op.op_poptionprice) * op.op_cnt - (op.op_pprice+op.op_poptionprice) * op.op_cnt * op.op_commission/ 100 + op.op_delivery_price + op.op_add_delivery_price)
		) as comPrice
	FROM odtOrderProduct as op 
    left join odtOrder as o on ( o.ordernum=op.op_oordernum )
    $s_query 
	group by op_partnerCode
    ORDER BY op_partnerCode asc
";
$resP = _MQ_assoc($queP);
?>
<style>
.sub_total_price { background-color: #FFFBE6; }
.total_price { background-color: #FFEFEF; }
.price_title { background-color: #DFE8E8 }
</style>

<!-- 검색영역 -->
<form name="searchfrm" method="post" action="<?=$PHP_SELF?>">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="settlementstatus" value="<?=$settlementstatus?>">

	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="100px"/><col width="230px"/><col width="100px"/><col width="200px"/><col width="100px"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">상품명</td>
					<td class="conts"><input type="text" name="pass_pname" class="input_text" value="<?=$pass_pname?>"></td>
					<td class="article">공급업체</td>
					<td class="conts" >
						<?PHP
						$arr_customer = arr_company();
						$arr_customer2 = arr_company2();
						echo _InputSelect( "pass_company" , array_keys($arr_customer) , $pass_company , "" , array_values($arr_customer) , "-공급업체-");
						?>
					</td>
				</tr>
				<tr>
					<td class="article">판매형태</td>
					<td class="conts"><?=_InputSelect("pass_orderproduct_type", array('coupon','product'), $pass_orderproduct_type, "", array('Coupon Type','Delivery Type') , "-선택-")?></td>
					<td class="article">검색기간</td>
					<td class="conts">
						<input type="text" name="pass_sdate" ID="pass_sdate" class="input_text" value="<?=$pass_sdate?>" readonly style="width:65px;">
						~ 
						<input type="text" name="pass_edate" ID="pass_edate" class="input_text" value="<?=$pass_edate?>" readonly style="width:65px;">
					</td>
				</tr>
				<tr>
					<td class="article">결제수단</td>
					<td class="conts" colspan="3">
						<?=_InputSelect("pass_paymethod", array_keys($arr_paymethod_name), $pass_paymethod, "", array_values($arr_paymethod_name), "-결제수단-")?>
					</td>
				</tr>
			</tbody> 
		</table>
		
		<!-- 버튼영역 -->
		<div class="top_btn_area">
			<div class="btn_line_up_center">
				<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
				<?php if ($mode == 'search') {?>
				<span class="shop_btn_pack"><span class="blank_3"></span></span>
				<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>?settlementstatus=<?=$settlementstatus?>" class="medium gray" title="전체목록" >전체목록</a></span>
				<?php } ?>
			</div>
		</div>
	</div>
</form>
<!-- // 검색영역 -->

<!-- 자동 정산대기 처리 안내 { -->
<div class="form_box_area">
	<?=_DescStr("자동 정산대기 처리 안내")?>
	<table class="list_TB">
		<thead>
			<tr>
				<th>분류</th>
				<?php foreach($arr_paymethod_name as $k=>$v) { echo '<th>'.$v.'</th>'; } ?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>배송상품 <small>(배송완료 기준)</small></td>
				<?php foreach($arr_paymethod_name as $k=>$v) { echo '<td>'.$row_setup['s_product_auto_'.$k].'일</td>'; } ?>
			</tr>
			<tr>
				<td>쿠폰상품 <small>(쿠폰사용 기준)</small></td>
				<?php foreach($arr_paymethod_name as $k=>$v) { echo '<td>'.$row_setup['s_coupon_auto_'.$k].'일</td>'; } ?>
			</tr>
		</tbody>
	</table>
	<?=_DescStr("(배송)발송완료관리,(쿠폰)발급완료관리 메뉴에서 수동으로 정산대기 처리를 할 수도 있습니다.", 'orange')?>
</div>
<!-- } 자동 정산대기 처리 안내 -->


<div class="content_section_inner">
	<form name="OderAllDelete" method="post" target="<?php echo ($c?'':'common_frame'); ?>" >
		<input type="hidden" name="PageL" value="All">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_seachcnt" value="<?=$TotalCount?>">
		<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">
		<input type="hidden" name="_search_que" value="<?=enc('e',$s_query)?>">
		<input type="hidden" name="settlementstatus" value="<?=$settlementstatus?>">
		<input type="hidden" name="view_mode" value="<?php echo ($c?'view':'down'); ?>" />
		<!-- 리스트 제어버튼영역 //-->
		<div class="top_btn_area">
			<?php if( $settlementstatus == "ready" ) : // 정산대기 ?>
			<span class="shop_btn_pack"><a href="#none" onclick="settlement_status('complete');" class="small white" title="선택정산완료처리" >선택정산완료처리</a></span>
			<span class="shop_btn_pack"><span class="blank_3"></span></span>
			<?php endif;?>
			<span class="shop_btn_pack"><a href="#none" onclick="saveExcel('_order3.excel.php');" id="saveexcel" class="small white" title="엑셀저장" >엑셀저장</a></span>
		</div>
		<!-- // 리스트 제어버튼영역 -->
		<table class="list_TB">
			<colgroup>
				<col width="80px">
				<col width="180px">
				<col width="*">
			</colgroup>
			<thead>
				<tr>
					<th scope="col" class="colorset">NO</th>
					<th scope="col" class="colorset">입점업체</th>
					<th scope="col" class="colorset" align="left">
						<div>
							<div style="float:left; text-align:center; width:40px">
								<input type="checkbox" name="allchk" onclick="selectAll();" value="Y">
							</div>
							<div style="float:left; text-align: center; width:90%">정산정보</div>
							<div style="clear: both"></div>
						</div>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if(sizeof($resP) <= 0) { echo "<tr><td colspan='3' height='100' style='text-align:center;'><font color='darkorange'>정산내역이 없습니다.</font></td></tr>"; } ?>
				<?php
				// 총합계
				$TotalSumPrice = 0; // 총 구매합계
				$TotalDeliveryPrice = 0; // 총 배송비
				$TotalComPrice = 0; // 총 업체수수료
				$TotalUsePoint = 0; // 총 할인액
				$TotalDiscount = 0; // 총 수수료
				$TotalCount = 0; // 개수
				foreach($resP as $k=>$v) {

					// 서브합계
					$SubSumPrice = 0; // 총 구매합계
					$SubDeliveryPrice = 0; // 총 배송비
					$SubComPrice = 0; // 총 업체수수료
					$SubUsePoint = 0; // 총 할인액
					$SubDiscount = 0; // 총 수수료
					$SubCount = 0;

					// -- 순번 ---
					$_num = sizeof($resP)-$k;

					$que = "
					    SELECT 
							op.*, o.* ,
							IF( 
								op.op_comSaleType='공급가' ,  
								((op.op_supply_price+op.op_poptionpurprice) * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price) , 
								((op.op_pprice+op.op_poptionprice) * op.op_cnt - (op.op_pprice+op.op_poptionprice) * op.op_cnt * op.op_commission/ 100 + op.op_delivery_price + op.op_add_delivery_price)
							) as comPrice
						FROM odtOrderProduct as op 
					    left join odtOrder as o on ( o.ordernum=op.op_oordernum )
					    $s_query
					    and op.op_partnerCode = '{$v['op_partnerCode']}'
					    ORDER BY op.op_uid DESC
					";
					$res = _MQ_assoc($que);
				?>
				<tr>
					<td><?php echo $_num; ?></td>
					<td>
						<div class="new_partner_name"><?php echo $arr_customer2[$v['op_partnerCode']]; ?></div>
						<div class="new_partner_id">(ID : <?php echo $v['op_partnerCode']; ?>)</div>
						<div class="new_order_data_ctrl">
							<span class="lineup"><span class="shop_btn_pack"><a href="#none" class="medium white open_detail_view" title="입접업체의 정산정보 합계만 보여줍니다" >정산정보 간략보기</a></span></span>
						</div>
					</td>
					<!-- 위에서 버튼 클릭하면 클래스값 추가 해서 tbody 긴 정보 간력하게열고닫기 if_order3_closed -->
					<td class="new_order_area">

						<!-- 2015-09-24 정산용 내부테이블추가 -->	
						<div class="new_order_data">
							<table summary="정산용테이블">
								<colgroup>
									<col width="50px"/><col width="90px"/><col width="*"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="10%"/><col width="90px"/>
								</colgroup>
								<thead>
									<tr>
										<td scope="col"><input type="checkbox" class="com_checked class_uid" data-com="com_<?php echo $v['op_partnerCode']; ?>" /></td>
										<td scope="col">발송일</td>
										<td scope="col">상품명</td>
										<td scope="col">구매합계<br>(상품가*판매량)</td>
										<td scope="col">배송비</td>
										<td scope="col">업체수수료</td>
										<td scope="col">할인액</td>
										<td scope="col">수수료</td>
										<td scope="col">상세보기</td>
									</tr>
								</thead> 
								<tbody>
									<?php
									foreach($res as $sk=>$sv) {

										$sum_price = ($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt];

										// -- 상세보기 버튼 ---
										$_btn = "
											<div class='btn_line_up_center'>
												<span class='shop_btn_pack'><input type=button value='상세보기' class='input_small blue' onclick='window.open(\"_order.form.php?_mode=modify&ordernum=" . $sv[ordernum] . "&_PVSC=" . $_PVSC . "\");'></span>
											</div>
										";

										// 총합계 적용
										$TotalSumPrice += $sum_price; // 총 구매합계
										$TotalDeliveryPrice += $sv['op_delivery_price'] + $sv['op_add_delivery_price']; // 총 배송비
										$TotalComPrice += $sv['comPrice']; // 총 업체수수료
										$TotalUsePoint += $sv['op_usepoint']; // 총 할인액
										$TotalDiscount += $sum_price + $sv['op_delivery_price'] + $sv['op_add_delivery_price'] - $sv['comPrice'] - $sv['op_usepoint']; // 총 수수료
										$TotalCount += 1; // 총 개수

										// 서브합계 적용
										$SubSumPrice += $sum_price; // 총 구매합계
										$SubDeliveryPrice += $sv['op_delivery_price'] + $sv['op_add_delivery_price']; // 총 배송비
										$SubComPrice += $sv['comPrice']; // 총 업체수수료
										$SubUsePoint += $sv['op_usepoint']; // 총 할인액
										$SubDiscount += $sum_price + $sv['op_delivery_price'] + $sv['op_add_delivery_price'] - $sv['comPrice'] - $sv['op_usepoint']; // 총 수수료
										$SubCount += 1; // 총 개수
									?>
									<tr>
										<td>
											<input type="checkbox" name="OpUid[]" data-com="com_<?php echo $v['op_partnerCode']; ?>" value="<?php echo $sv['op_uid']; ?>" class="class_uid">
											<input type="hidden" name="settle_data[<?php echo $sv['op_uid']; ?>][partnerCode]" value="<?php echo $sv['op_partnerCode']; ?>" placeholder="업체코드">
											<input type="hidden" name="settle_data[<?php echo $sv['op_uid']; ?>][price]" value="<?php echo $sum_price; ?>" placeholder="구매합계">
											<input type="hidden" name="settle_data[<?php echo $sv['op_uid']; ?>][delivery_price]" value="<?php echo $sv['op_delivery_price'] + $sv['op_add_delivery_price']; ?>" placeholder="배송비">
											<input type="hidden" name="settle_data[<?php echo $sv['op_uid']; ?>][com_price]" value="<?php echo $sv['comPrice']; ?>" placeholder="업체수수료">
											<!-- <input type="hidden" name="settle_data[<?php echo $sv['op_uid']; ?>][usepoint]" value="<?php echo $sv['op_usepoint']; ?>" placeholder="할인액"> -->
											<input type="hidden" name="settle_data[<?php echo $sv['op_uid']; ?>][discount]" value="<?php echo $sum_price + $sv['op_delivery_price'] + $sv['op_add_delivery_price'] - $sv['comPrice'] - $sv['op_usepoint']; ?>" placeholder="수수료">
										</td>
										<td>
											<?php
											echo (
												$sv['op_orderproduct_type'] == 'product'?
												($sv['op_expressdate'] == '0000-00-00 00:00:00'?'-':date('Y.m.d', strtotime($sv['op_expressdate'])))
												:
												($sv['op_coupon_use'] == '0000-00-00 00:00:00'?'-':date('Y.m.d', strtotime($sv['op_coupon_use'])))
												);
											?>
										</td>
										<td>
											<div class="item_name"><?php echo stripslashes($sv['op_pname']); ?></div>
											<div class="user_num"><?php echo $sv['ordername']; ?> <strong>(<a href="./_order.form.php?_mode=modify&ordernum=<?php echo $sv['op_oordernum']; ?>" target="_blank"><?php echo $sv['op_oordernum']; ?></a>)</strong></div>
										</td>
										<td class="price_box">
											<strong class="price_sum"><?php echo number_format( $sum_price); ?></strong>원
										</td>
										<td class="price_box">
											<strong><?php echo number_format($sv['op_delivery_price'] + $sv['op_add_delivery_price']); ?></strong>원
										</td>
										<td class="price_box">
											<strong class="price_commiss"><?php echo number_format($sv['comPrice']); ?></strong>원
										</td>
										<td class="price_box">
											<strong><?php echo number_format($sv['op_usepoint']); ?></strong>원
										</td>
										<td class="price_box">
											<strong class="price_charge"><?php echo number_format( $sum_price + $sv['op_delivery_price'] + $sv['op_add_delivery_price'] - $sv['comPrice'] - $sv['op_usepoint']); ?></strong>원
										</td>
										<td>
											<span class="lineup"><span class="shop_btn_pack btn_input_blue"><input type="button" onclick="window.open('_order.form.php?_mode=modify&ordernum=<?php echo $sv[ordernum]; ?>&_PVSC=<?php echo $_PVSC; ?>');" class="input_small" value="상세보기" /></span></span>
										</td>
									</tr>
									<?php } ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="3"><strong>입점업체합계</strong></td>
										<td class="price_box"><div class="hide_txt txt_lin2">구매합계<br/>(상품가*판매량)</div><strong><?php echo number_format($SubSumPrice); ?></strong>원</td>
										<td class="price_box"><div class="hide_txt">배송비</div><strong><?php echo number_format($SubDeliveryPrice); ?></strong>원</td>
										<td class="price_box"><div class="hide_txt">업체수수료</div><strong><?php echo number_format($SubComPrice); ?></strong>원</td>
										<td class="price_box"><div class="hide_txt">할인액</div><strong><?php echo number_format($SubUsePoint); ?></strong>원</td>
										<td class="price_box"><div class="hide_txt">수수료</div><strong><?php echo number_format($SubDiscount); ?></strong>원</td>
										<td><div class="hide_txt">정산대기수량</div><?php echo $SubCount; ?>개</td>
									</tr>
								</tfoot> 
							</table>
						</div>
						<!-- 2015-09-24 정산용 내부테이블추가 -->	
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3" class="new_order_data_sum">
						
						<div class="inner_sum_box">
							<ul>
								<li class="txt">정산 총 합계</li>
								<li><span class="sum">구매합계</span><span class="value"><?php echo number_format($TotalSumPrice); ?> 원</span></li>
								<li><span class="sum">배송비</span><span class="value"><?php echo number_format($TotalDeliveryPrice); ?> 원</span></li>
								<li><span class="sum">업체수수료</span><span class="value"><?php echo number_format($TotalComPrice); ?> 원</span></li>
								<li><span class="sum">할인액</span><span class="value"><?php echo number_format($TotalUsePoint); ?> 원</span></li>
								<li><span class="sum">수수료</span><span class="value"><?php echo number_format($TotalDiscount); ?> 원</span></li>
							</ul>
						</div>
					
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>



<script type="text/javascript" src="_order2.list.js" ></script>

<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
// 입점내부 전체선택
$(document).delegate('.com_checked', 'click', function() {

	var com = $(this).data('com');
	var ck = $(this).is(':checked');
	$('.class_uid[data-com='+com+']').attr('checked', ck);
});

$(function() {
    $("#pass_sdate").datepicker({changeMonth: true, changeYear: true });
    $("#pass_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
    $("#pass_sdate").datepicker( "option",$.datepicker.regional["ko"] );

    $("#pass_edate").datepicker({changeMonth: true, changeYear: true });
    $("#pass_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
    $("#pass_edate").datepicker( "option",$.datepicker.regional["ko"] );

	$('.open_detail_view').on('click', function(e) {

		e.preventDefault();
		var Target = $(this).closest('tr').find('td.new_order_area');
		var status = Target.hasClass('if_order3_closed');

		if(status === false) {

			$(this).attr('title', '정산정보를 모두 펼쳐서 보여줍니다');
			$(this).html('정산정보 펼쳐보기');
			Target.addClass('if_order3_closed');
			$('.ui-tooltip-content').html('정산정보를 모두 펼쳐서 보여줍니다');
		}
		else {

			$(this).attr('title', '입접업체의 정산정보 합계만 보여줍니다');
			$(this).html('정산정보 간략보기');
			Target.removeClass('if_order3_closed');
			$('.ui-tooltip-content').html('입접업체의 정산정보 합계만 보여줍니다');
		}
	});
})
</script>

<?PHP include_once("inc.footer.php"); ?>