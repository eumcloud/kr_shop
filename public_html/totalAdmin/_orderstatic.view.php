<?PHP
include_once("inc.header.php");

// 넘길 변수 설정하기
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// 넘길 변수 설정하기


// 기본 날짜 조건 생성
if(!$pass_sdate) {$pass_sdate = date("Y-m-d" , strtotime("-7 day"));}
if(!$pass_edate) {$pass_edate = date("Y-m-d");}
if(!$pass_paystatus) {$pass_paystatus = "Y";}


// 검색 체크
$s_query = " from odtOrder where canceled='N' AND orderstatus='Y' ";
if($pass_sdate !="") { $s_query .= " and left(orderdate,10) >='${pass_sdate}' "; }
if($pass_edate !="") { $s_query .= " and left(orderdate,10) <='${pass_edate}' "; }
if($pass_paymethod !="") { $s_query .= " and paymethod='${pass_paymethod}' "; }
if($pass_paystatus !="") { $s_query .= " and paystatus='${pass_paystatus}' "; }
if($pass_status !="") { $s_query .= " and orderstatus_step='${pass_status}' "; } 
if($pass_mobile_order !="") {  $s_query .= " and mobile = '".$pass_mobile_order."' "; } // 구매기기 LDD002
else { $s_query .= " and orderstatus_step not in ('결제대기','결제실패')"; }

$que = " 
	select 
		left(orderdate,10) as orderdate_10,
		sum(tPrice) as sum_tPrice,
		sum(dPrice) as sum_dPrice,
		sum(sPrice) as sum_sPrice,
		sum(gGetPrice) as sum_gGetPrice
	$s_query 
	group by orderdate_10
	order by orderdate_10 asc
";
$arr_price = array();
$res = _MQ_assoc($que);
?>


<!-- 검색영역 -->
<div class="form_box_area">
	<form name="searchfrm" method="post" action="<?=$_SERVER["PHP_SELF"]?>" autocomplete="off" >
		<input type="hidden" name="mode" value="search">

		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="120px"/><col width="300px"/><col width="120px"/><col width="*"/>
			</colgroup>
			<tbody> 
				<tr>
					<td class="article">주문일</td>
					<td class="conts"><input type="text" name="pass_sdate" id="pass_sdate" value='<?=$pass_sdate?>' class="input_text" style="width:100px" />
					~
					<input type="text" name="pass_edate" id="pass_edate" value='<?=$pass_edate?>' class="input_text" style="width:100px" /></td>
					<td class="article">결제수단</td>
					<td class="conts"><?=_InputSelect( "pass_paymethod" , array_keys($arr_paymethod_name), $pass_paymethod , "" , array_values($arr_paymethod_name) , '') ?></td>
				</tr>
				<tr>
					<td class="article">결제상태</td>
					<td class="conts"><?=_InputSelect( "pass_paystatus" , array('Y','N'), $pass_paystatus , "" , array('결제확인','결제대기') , '') ?></td>
					<td class="article">주문상태</td>
					<td class="conts"><?=_InputSelect( "pass_status" , array_keys($arr_o_status) , $pass_status , "" , array_keys($arr_o_status) , '') ?></td>
				</tr>
				<?// LDD002 { ?>
				<tr>
					<td class="article">주문기기</td>
					<td class="conts" colspan="3">
						<?=_InputSelect( "pass_mobile_order" , array("Y" , "N") , $pass_mobile_order , "" , array("모바일구매" , "PC구매") , "-구매기기-")?>
					</td>
				</tr>
				<?// } LDD002 ?>
			</tbody> 
		</table>

		<!-- 버튼영역 -->
		<div class="top_btn_area">
			<div class="btn_line_up_center">
				<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
				<?if ($mode == "search") {?>
				<span class="shop_btn_pack"><span class="blank_3"></span></span>
				<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="전체목록">전체목록</a></span>
				<?}?>
			</div>
		</div>
	</form>
</div>
<!-- // 검색영역 -->

<!-- 리스트영역 -->
<div class="content_section_inner">
	<table class="list_TB" summary="리스트기본">
		<thead>
			<tr>
				<th scope="col" class="colorset">NO</th>
				<th scope="col" class="colorset">주문일</th>
				<th scope="col" class="colorset">결제가</th>
				<th scope="col" class="colorset">배송비</th>
				<th scope="col" class="colorset">할인적용액</th>
				<th scope="col" class="colorset">포인트제공액</th>
			</tr>
		</thead> 
		<tbody> 
			<?PHP	
			foreach($res as $k=>$v){
				$_num = $k + 1 ;
				echo "
									<tr height=30>
										<td>" . $_num . "</td>
										<td>" . $v['orderdate_10'] . "</td>
										<td>" . number_format($v['sum_tPrice']) . "원</td>
										<td>" . number_format($v['sum_dPrice']) . "원</td>
										<td>" . number_format($v['sum_sPrice']) . "원</td>
										<td>" . number_format($v['sum_gGetPrice']) . "원</td>
									</tr>
				";
				foreach($v as $sk=>$sv){
					$arr_price[$sk] += $sv;
				}
			}
			?>
			<tr>
				<th colspan=2 align='center'>소계</th>
				<th><?=number_format($arr_price['sum_tPrice'])?>원</th>
				<th><?=number_format($arr_price['sum_dPrice'])?>원</th>
				<th><?=number_format($arr_price['sum_sPrice'])?>원</th>
				<th><?=number_format($arr_price['sum_gGetPrice'])?>원</th>
			</tr>
		</tbody> 
	</table>
</div>
<br><br><br>





<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type="text/css">
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
	$(document).ready(function() {
		$(function() {
			$("#pass_sdate").datepicker({changeMonth: true,changeYear: true});
			$("#pass_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
			$("#pass_sdate").datepicker( "option",$.datepicker.regional["ko"] );

			$("#pass_edate").datepicker({changeMonth: true,changeYear: true});
			$("#pass_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
			$("#pass_edate").datepicker( "option",$.datepicker.regional["ko"] );
		});
	});
</script>

<?PHP include_once("inc.footer.php"); ?>