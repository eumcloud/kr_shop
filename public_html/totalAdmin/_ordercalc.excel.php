<?PHP

	include_once("inc.php");

	$pass_company  = $pass_company ? $pass_company : "";
	$pass_sdate = $pass_sdate ? $pass_sdate : date("Y-m-d" , strtotime("-7 day"));
	$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');

    $toDay = date("YmdHis");
	$fileName = "ordercalcexcel";

    ## Exel 파일로 변환 #############################################
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");
	print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">"); 


	// 공급업체 정보 추출
	$arr_customer = arr_company();

    echo "
		<TABLE border=1>
			<TR>
				<th>주문일</th>
				<th>공급업체</th>
				<th>결제액</th>
				<th>판매량</th>
				<th>배송비</th>
				<th>입점업체수수료</th>
				<th>할인액</th>
				<th>수수료</th>
			</TR>
	";

	## 주문 정보 지정
	$s_query = " where op.op_settlementstatus != 'none' and o.paystatus='Y' and o.paystatus2='Y' and o.canceled='N' AND o.orderstatus='Y' ";
	if( $pass_sdate && $pass_edate ) { $s_query .= " AND left(o.orderdate,10) between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 검색기간
	else if( $pass_sdate ) { $s_query .= " AND left(o.orderdate,10) >= '". $pass_sdate ."' "; }
	else if( $pass_edate ) { $s_query .= " AND left(o.orderdate,10) <= '". $pass_edate ."' "; }
	if( $pass_company ) { $s_query .= " AND op.op_partnerCode = '". $pass_company ."' "; }// 공급업체

	$s_query .= " and op.op_cancel = 'N' "; //LMH001

	// 합계 변수 초기화
	$sum_app_cnt = $sum_tPrice = $sum_payPrice = $sum_dPrice = 0;
	$que = "
		select 
			sum((op_pprice + op_poptionprice) * op_cnt ) as tPrice , 
			IF( 
				op.op_comSaleType='공급가' ,  
				sum((op.op_supply_price+op.op_poptionpurprice) * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price) , 
				sum((op.op_pprice+op.op_poptionprice) * op.op_cnt - (op.op_pprice+op.op_poptionprice) * op.op_cnt * op.op_commission/ 100 + op.op_delivery_price + op.op_add_delivery_price)
			) as comPrice,
			sum( op_cnt ) as tCnt , 
			sum( op_usepoint ) as tUsePoint , 
			sum(op_delivery_price + op_add_delivery_price) as dPrice , 
			left(o.orderdate , 10) as sub_orderdate,
			op.op_partnerCode ,
			count(*) as cnt 
		from odtOrderProduct as op 
		left join odtOrder as o on (o.ordernum = op.op_oordernum)
		$s_query 
		group by sub_orderdate , op.op_partnerCode, op.op_comSaleType
		order by sub_orderdate , op.op_partnerCode
	";
	//echo $que;
	$res = _MQ_assoc($que);

	foreach($res as $sk=>$sv){

		$sum_app_cnt += $sv[tCnt];
		$sum_tPrice += $sv[tPrice];
		$sum_dPrice += $sv[dPrice];
		$sum_payPrice += $sv[comPrice];
		$sum_tUsePoint += $sv[tUsePoint];
		$sum_appPrice += $sv[tPrice] + $sv[dPrice] - $sv[comPrice] - $sv[tUsePoint];

		echo "
			<tr height=30>
				<td>" . $sv[sub_orderdate] . "</td>
				<td class='left'>". $arr_customer[$sv[op_partnerCode]] ."</td>
				<td>" . number_format($sv[tPrice]) . "원</td>
				<td>" . number_format($sv[tCnt]) . "</td>
				<td>" . number_format($sv[dPrice]) . "원</td>
				<td>" . number_format($sv[comPrice]) . "원</td>
				<td>" . number_format($sv[tUsePoint]) . "원</td>
				<td>" . number_format( $sv[tPrice] + $sv[dPrice] - $sv[comPrice] - $sv[tUsePoint]) . "원</td>
			</tr>
		";
	}


	echo "
			<tr>
				<th><b>합 계</b></th>
				<th></th>
				<th>" . number_format($sum_tPrice) . "원</th>
				<th>" . number_format($sum_app_cnt) . "</th>
				<th>" . number_format($sum_dPrice) . "원</th>
				<th>" . number_format($sum_payPrice) . "원</th>
				<th>" . number_format($sum_tUsePoint) . "원</th>
				<th>" . number_format( $sum_appPrice) . "원</th>
			</tr>
		</table>
	";

?>