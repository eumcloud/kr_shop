<?PHP
# LDD007
include_once("inc.php");

$toDay = date("YmdHis");
$fileName = "order3excel";

## Exel 파일로 변환 #############################################
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");

if( sizeof($OpUid) == 0 ){
	error_msg("주문항목을 선택하시기 바랍니다.");
}

echo "
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<TABLE border=1>
		<TR>
			<th>발송일</th>
			<th>상품명</th>
			<th>주문번호</th>
			<th>구매합계(상품가*판매량)</th>
			<th>배송비</th>
			<th>업체수수료</th>
			<th>수수료</th>
		</TR>
";

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
    where op_partnerCode='".$com[id]."' and op.op_uid in ('". implode("','" , $OpUid) ."')
";
$res = _MQ_assoc($que);
foreach($res as $sk=>$sv){

	$sum_price = ($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt];

	echo "
		<tr>
			<td >" . ($sv['op_orderproduct_type'] == 'product'?date('Y.m.d', strtotime($sv['op_expressdate'])):date('Y.m.d', strtotime($sv['op_coupon_use']))) . "</td>
			<td>". stripslashes($sv[op_pname]) ."</td>
			<td>". $sv[op_oordernum] ."</td>
			<td class='right'>" . number_format( $sum_price) . "원</td><!-- 구매합계 -->
			<td class='right'>" . number_format($sv[op_delivery_price] + $sv[op_add_delivery_price]) . "원</td><!-- 배송비 -->
			<td class='right'>" . number_format($sv[comPrice]) . "원</td><!-- 업체수수료 -->
			<td class='right'>" . number_format( $sum_price + $sv[op_delivery_price] + $sv[op_add_delivery_price] - $sv[comPrice] - $sv[op_usepoint]) . "원</td><!-- 수수료 -->
		</tr>
	";	
}

echo "</table>";