<?PHP
# LDD007
include_once("inc.header.php");

$arr_customer = arr_company();

$settlementstatus = 'ready';
$settlementstatus = $settlementstatus ? $settlementstatus : "ready";


// 검색 체크
$s_query = " where op.op_settlementstatus='". $settlementstatus ."' and o.paystatus='Y' and o.paystatus2='Y' and o.canceled='N' AND o.orderstatus='Y' and op.op_partnerCode='".$com[id]."' ";
//if( $pass_sdate && $pass_edate ) { $s_query .= " AND left(o.orderdate,10) between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 검색기간
if( $pass_sdate && $pass_edate ) { $s_query .= " AND ( left(op.op_expressdate,10) between '". $pass_sdate ."' and '". $pass_edate ."' or left(op.op_coupon_use,10) between '". $pass_sdate ."' and '". $pass_edate ."') "; }// - 검색기간
else if( $pass_sdate ) { $s_query .= " AND left(op.op_expressdate,10) >= '". $pass_sdate ."' "; }
else if( $pass_edate ) { $s_query .= " AND left(op.op_expressdate,10) <= '". $pass_edate ."' "; }

if( $pass_orderproduct_type ) { $s_query .= " AND op.op_orderproduct_type = '". $pass_orderproduct_type ."' "; }// 판매형태
if( $pass_pname ) { $s_query .= " AND op.op_pname like '%". $pass_pname ."%' "; }//상품명
if($pass_paymethod) { $s_query .= " and o.paymethod = '".$pass_paymethod."' "; } // 결제수단

// 현 페이지 주문번호 추출
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
	    ORDER BY op.op_uid DESC
	";
$res = _MQ_assoc($que);
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
					<td class="article">결제수단</td>
					<td class="conts">
						<?=_InputSelect( "pass_paymethod" , array_keys($arr_paymethod_name) , $pass_paymethod , "" , array_values($arr_paymethod_name) , "-결제수단-")?>
					</td>
				</tr>
				<tr>
					<td class="article">판매형태</td>
					<td class="conts"><?=_InputSelect( "pass_orderproduct_type" , array('coupon','product') , $pass_orderproduct_type , " " , array('Coupon Type','Delivery Type') , "-선택-")?></td>
					<td class="article">검색기간</td>
					<td class="conts">
						<input type="text" name="pass_sdate" ID="pass_sdate" class="input_text" value="<?=$pass_sdate?>" readonly style="width:65px;">
						~ 
						<input type="text" name="pass_edate" ID="pass_edate" class="input_text" value="<?=$pass_edate?>" readonly style="width:65px;">
					</td>
				</tr>
			</tbody> 
		</table>
		
		<!-- 버튼영역 -->
		<div class="top_btn_area">
			<div class="btn_line_up_center">
				<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
				<?php if ($mode == 'search') { ?>
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
	<?=_DescStr("(배송)발송완료관리, (쿠폰)발급완료관리 메뉴에서 수동으로 정산대기 처리를 할 수 있습니다.", 'orange')?>
</div>
<!-- } 자동 정산대기 처리 안내 -->


<div class="content_section_inner">
	<form name="OderAllDelete" method="post" target="common_frame" >
		<input type="hidden" name="PageL" value="All">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_seachcnt" value="<?=$TotalCount?>">
		<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">
		<input type="hidden" name="_search_que" value="<?=enc('e',$s_query)?>">
		<input type="hidden" name="settlementstatus" value="<?=$settlementstatus?>">
		<!-- 리스트 제어버튼영역 //-->
		<div class="top_btn_area">
			<span class="shop_btn_pack"><a href="#none" onclick="saveExcel('_order3.excel.php');" id="saveexcel" class="small white" title="엑셀저장" >엑셀저장</a></span>
		</div>

		<table class="list_TB">
			<thead>
				<tr>
					<th scope="col" class="colorset">NO</th>
					<th scope="col" class="colorset">
						<input type="checkbox" name="allchk" onclick="selectAll();" value="Y">
					</th>
					<th scope="col" class="colorset price_title"><b>주문일</b></th>
					<th scope="col" class="colorset price_title"><b>상품명</b></th>
					<th scope="col" class="colorset price_title"><b>구매합계<br>(상품가*판매량)</b></th>
					<th scope="col" class="colorset price_title"><b>배송비</b></th>
					<th scope="col" class="colorset price_title"><b>수수료</b></th>
					<th scope="col" class="colorset price_title"><b>할인액</b></th>
					<th scope="col" class="colorset price_title"><b>수수료</b></th>
					<th scope="col" class="colorset price_title"><b>상세보기</b></th>
				</tr>
			</thead>
			<tbody>
				<?php if(sizeof($res) <= 0) { echo "<tr><td colspan='10' height='100' style='text-align:center;'><font color='darkorange'>정산내역이 없습니다.</font></td></tr>"; } ?>
				<?php
				// 총합계
				$TotalSumPrice = 0; // 총 구매합계
				$TotalDeliveryPrice = 0; // 총 배송비
				$TotalComPrice = 0; // 총 업체수수료
				$TotalUsePoint = 0; // 총 할인액
				$TotalDiscount = 0; // 총 수수료
				$TotalCount = 0; // 개수
				foreach($res as $k=>$v) {

					// -- 순번 ---
					$_num = sizeof($res)-$k;

					// 가격계산
					$sum_price = ($v['op_pprice'] + $v['op_poptionprice']) * $v['op_cnt'];

					// 총합계 적용
					$TotalSumPrice += $sum_price; // 총 구매합계
					$TotalDeliveryPrice += $v['op_delivery_price'] + $v['op_add_delivery_price']; // 총 배송비
					$TotalComPrice += $v['comPrice']; // 총 업체수수료
					$TotalUsePoint += $v['op_usepoint']; // 총 할인액
					$TotalDiscount += $sum_price + $v['op_delivery_price'] + $v['op_add_delivery_price'] - $v['comPrice'] - $v['op_usepoint']; // 총 수수료
					$TotalCount += 1; // 총 개수

					// -- 상세보기 버튼 ---
					$_btn = "
						<div class='btn_line_up_center'>
							<span class='shop_btn_pack'><input type=button value='상세보기' class='input_small blue' onclick='window.open(\"_order.view.php?_mode=modify&ordernum=" . $v[ordernum] . "&_PVSC=" . $_PVSC . "\");'></span>
						</div>
					";
				?>
				<tr>
					<td><?php echo $_num; ?></td>
					<td>
						<input type="checkbox" name="OpUid[]" value="<?php echo $v['op_uid']; ?>" class="class_uid">
						<input type="hidden" name="settle_data[<?php echo $v['op_uid']; ?>][partnerCode]" value="<?php echo $v['op_partnerCode']; ?>" placeholder="업체코드">
						<input type="hidden" name="settle_data[<?php echo $v['op_uid']; ?>][price]" value="<?php echo $sum_price; ?>" placeholder="구매합계">
						<input type="hidden" name="settle_data[<?php echo $v['op_uid']; ?>][delivery_price]" value="<?php echo $v['op_delivery_price'] + $v['op_add_delivery_price']; ?>" placeholder="배송비">
						<input type="hidden" name="settle_data[<?php echo $v['op_uid']; ?>][com_price]" value="<?php echo $v['comPrice']; ?>" placeholder="업체수수료">
						<input type="hidden" name="settle_data[<?php echo $v['op_uid']; ?>][usepoint]" value="<?php echo $v['op_usepoint']; ?>" placeholder="할인액">
						<input type="hidden" name="settle_data[<?php echo $v['op_uid']; ?>][discount]" value="<?php echo $sum_price + $v['op_delivery_price'] + $v['op_add_delivery_price'] - $v['comPrice'] - $v['op_usepoint']; ?>" placeholder="수수료">
					</td>
					<td>
						<?php
						echo (
							$v['op_orderproduct_type'] == 'product'?
							($v['op_expressdate'] == '0000-00-00 00:00:00'?'-':date('Y.m.d', strtotime($v['op_expressdate'])))
							:
							($v['op_coupon_use'] == '0000-00-00 00:00:00'?'-':date('Y.m.d', strtotime($v['op_coupon_use'])))
							);
						?>
					</td>
					<td>
						<?php echo stripslashes($v['op_pname']); ?><br>
						<?php echo $v['ordername']; ?>(
						<a href="./_order.view.php?_mode=modify&ordernum=<?php echo $v['op_oordernum']; ?>" target="_blank"><?php echo $v['op_oordernum']; ?></a>)
					</td>
					<td>
						<!-- 구매합계 -->
						<?php echo number_format( $sum_price); ?> 원
					</td>
					<td>
						<!-- 배송비 -->
						<?php echo number_format($v['op_delivery_price'] + $v['op_add_delivery_price'], 'Y'); ?> 원
					</td>
					<td>
						<!-- 업체수수료 -->
						<?php echo number_format($v['comPrice']); ?> 원
					</td>
					<td>
						<!-- 할인액 -->
						<?php echo number_format($v['op_usepoint']); ?> 원
					</td>
					<td>
						<!-- 수수료 -->
						<?php echo number_format( $sum_price + $v['op_delivery_price'] + $v['op_add_delivery_price'] - $v['comPrice'] - $v['op_usepoint']); ?> 원
					</td>
					<td>
						<!-- 상세보기 -->
						<?php echo $_btn; ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="10" class="new_order_data_sum">
						
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
<link rel='stylesheet' href='../include/js/jquery/jquery.ui.all.css' type=text/css>
<script src="../include/js/jquery/jquery.ui.core.js"></script>
<script src="../include/js/jquery/jquery.ui.widget.js"></script>
<script src="../include/js/jquery/jquery.ui.datepicker.js"></script>
<script src="../include/js/jquery/jquery.ui.datepicker-ko.js"></script>
<script>
    $(function() {
        $("#pass_sdate").datepicker({changeMonth: true, changeYear: true });
        $("#pass_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#pass_sdate").datepicker( "option",$.datepicker.regional["ko"] );

        $("#pass_edate").datepicker({changeMonth: true, changeYear: true });
        $("#pass_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#pass_edate").datepicker( "option",$.datepicker.regional["ko"] );
    });
</script>

<?PHP include_once("inc.footer.php"); ?>