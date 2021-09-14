<?PHP
# LDD007
include_once("inc.php");

$toDay = date("YmdHis");
$fileName = "order4excel";

## Exel 파일로 변환 #############################################
//header("Content-Type: text/html; charset=UTF-8");
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");
print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");


if( $_mode == 'search_excel' ) {
	$res = _MQ_assoc(" select * from odtOrderSettleComplete ".(enc('d',$_search_que))." order by s_uid desc ");
} else {
	if( sizeof($OpUid) == 0 ){
		error_msg("항목을 선택하시기 바랍니다.");
	}
	$res = _MQ_assoc(" select * from odtOrderSettleComplete where s_uid in ('". implode("','" , $OpUid) ."') and s_partnerCode='".$com[id]."' order by s_uid desc ");
}

echo '
	<table border=1>
		<tr>
			<th scope="col" class="colorset">정산일</th>
			<th scope="col" class="colorset">총금액</th>
			<th scope="col" class="colorset">정산수량</th>
			<th scope="col" class="colorset">배송비</th>
			<th scope="col" class="colorset">입점업체 정산금액</th>
			<th scope="col" class="colorset">할인액</th>
			<th scope="col" class="colorset">수수료</th>
		</tr>
';
foreach($res as $k=>$v){

	echo "
		<tr>
			<td>".$v['s_date']."</td>
			<td>".number_format($v['s_price'])."원</td>
			<td>".number_format($v['s_count'])."건</td>
			<td>".number_format($v['s_delivery_price'])."원</td>
			<td>".number_format($v['s_com_price'])."원</td>
			<td>".number_format($v['s_usepoint'])."원</td>
			<td>".number_format($v['s_discount'])."원</td>
		</tr>
	";	
}

echo "</table>";