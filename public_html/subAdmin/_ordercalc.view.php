<?PHP

	include_once("inc.header.php");


	if(!$pass_sdate) {$pass_sdate = date("Y-m-d" , strtotime("-7 day"));}
	$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');

		
?>


				<!-- 검색영역 -->
<form name=searchfrm method=post action='<?=$PHP_SELF?>'>
<input type=hidden name=mode value=search>
				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><col width="*"/>
						</colgroup>
						<tbody>
							<tr>
								<td class="article">검색기간</td>
								<td class="conts" >
									<input type=text name="pass_sdate" ID="pass_sdate" class=input_text value="<?=$pass_sdate?>" readonly style="width:100px;">
									~ 
									<input type=text name="pass_edate" ID="pass_edate" class=input_text value="<?=$pass_edate?>" readonly style="width:100px;">
									(주문일 기준)
								</td>
							</tr>

							<tr>
								<td class="conts" colspan="2">
									<?=_DescStr("매출금액뿐 아니라 발생된 업체 정산 금액 등을 기간별로 볼수있는 기능입니다.")?>
									<?=_DescStr("주문에 대한 결제 승인을 포함하지 않으므로 입점업체 관리자페이지의 정보와 다를 수 있습니다. 만약 똑같은 정보를 원하신다면 결제확인에 대해 모두 결제승인 처리하시면 같은 정보를 확인할 수 있습니다.")?>
									<?=_DescStr("배송비는 입점업체 총수수료에 포함되므로 참고하시기 바랍니다.")?>
								</td>
							</tr>
						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == search) {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="전체목록" >전체목록</a></span>
							<?}?>
						</div>
					</div>
				</div>
</form>
				<!-- // 검색영역 -->





				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<!-- 리스트 제어버튼영역 //-->
					<div class="top_btn_area">
						<span class="shop_btn_pack"><a href="#none" onclick="common_frame.location.href=('_ordercalc.excel.php?pass_sdate=<?=$pass_sdate?>&pass_edate=<?=$pass_edate?>&pass_company=<?=$pass_company?>');" class="small white" title="엑셀다운로드" >엑셀다운로드</a></span>
					</div>
					<!-- // 리스트 제어버튼영역 -->

					<table class="list_TB" summary="리스트기본">
						<thead>
							<tr>
								<th scope="col" class="colorset">주문일</th>
								<th scope="col" class="colorset">구매금액</th>
								<th scope="col" class="colorset">판매량</th>
								<th scope="col" class="colorset">배송비</th>
								<th scope="col" class="colorset">입점업체<br>수수료</th>
								<th scope="col" class="colorset">할인액</th>
								<th scope='col' class='colorset'>수수료</th>
							</tr>
						</thead> 
						<tbody>
<?PHP

	## 주문 정보 지정
	$s_query = " where op.op_settlementstatus != 'none' and o.paystatus='Y' and o.paystatus2='Y' and o.canceled='N' AND o.orderstatus='Y' AND op.op_partnerCode = '" . $com[id] . "' ";
	if( $pass_sdate && $pass_edate ) { $s_query .= " AND left(o.orderdate,10) between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 검색기간
	else if( $pass_sdate ) { $s_query .= " AND left(o.orderdate,10) >= '". $pass_sdate ."' "; }
	else if( $pass_edate ) { $s_query .= " AND left(o.orderdate,10) <= '". $pass_edate ."' "; }

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
	if(sizeof($res) == 0) {
		echo "<tr><td colspan=8 height='100'>검색결과가 없습니다.</td></tr>";
	}
	else {
		foreach($res as $sk=>$sv){

			$sum_app_cnt += $sv[tCnt];
			$sum_tPrice += $sv[tPrice];
			$sum_dPrice += $sv[dPrice];
			$sum_payPrice += $sv[comPrice];
			$sum_tUsePoint += $sv[tUsePoint];
			$sum_appPrice += $sv[tPrice] + $sv[dPrice] - $sv[comPrice] - $sv[tUsePoint];

			echo "
				<tr height=30>
					<td>" . $sv[sub_orderdate] . "</td><!-- 주문일 --> 
					<td class='right'>" . number_format($sv[tPrice]) . "원</td><!-- 구매금액 --> 
					<td class='right'>" . number_format($sv[tCnt]) . "</td><!-- 판매량 --> 
					<td class='right'>" . number_format($sv[dPrice]) . "원</td><!-- 배송비 --> 
					<td class='right'>" . number_format($sv[comPrice]) . "원</td><!-- 입점업체 수수료 --> 
					<td class='right'>" . number_format($sv[tUsePoint]) . "원</td><!-- 할인액 -->
					<td class='right'>" . number_format( $sv[tPrice] + $sv[dPrice] - $sv[comPrice] - $sv[tUsePoint]) . "원</td><!-- 수수료 -->
				</tr>
			";
		}
	}


	echo "
		<tr>
			<th><b>합 계</b></th>
			<th class='right'>" . number_format($sum_tPrice) . "원</th><!-- 구매금액 --> 
			<th class='right'>" . number_format($sum_app_cnt) . "</th><!-- 판매량 --> 
			<th class='right'>" . number_format($sum_dPrice) . "원</th><!-- 배송비 --> 
			<th class='right'>" . number_format($sum_payPrice) . "원</th><!-- 입점업체 수수료 --> 
			<th class='right'>" . number_format($sum_tUsePoint) . "원</th><!-- 할인액 -->
			<th class='right'>" . number_format( $sum_appPrice) . "원</th><!-- 수수료 -->
		</tr>
	";

?>
						</tbody> 
					</table>

			</div>



<?PHP
	include_once("inc.footer.php");
?>




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