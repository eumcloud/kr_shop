<?

$PG_MODE = $row_setup[P_MODE];

$today=mktime(); 
$today_time = date('YmdHis', $today);

//parameter
$serviceId = $row_setup[P_ID] ;   //테스트서버 : glx_api
$orderDate = $today_time ; //(YYYYMMDDHHMMSS)
$orderId = $ordernum ;
$userId =  $r[orderid]; 
$userName = $r[ordername];
$itemName = $app_product_name;
$itemCode = "ITEM_CODE";
$amount = $r[tPrice];
$userIp = $_SERVER["REMOTE_ADDR"];
$returnUrl = "http://".$_SERVER[HTTP_HOST]."/m/shop.order.result_billgate.pro.php";

if($r[paymethod]=='C') { $_method = 'credit/smartphone'; } 
if($r[paymethod]=='L') { $_method = 'account'; } 
if($r[paymethod]=='V') { $_method = 'vaccount'; } 

if($r[paymethod]!='C') { $returnUrl = "http://".$_SERVER[HTTP_HOST]."/m/shop.order.result_billgate.pro.".$_method.".php"; }

if($r[paymethod]!='V') {
	$temp = $serviceId.$orderId.$amount;
	$cmd = sprintf("%s \"%s\" \"%s\"", $COM_CHECK_SUM, "GEN", $temp);
	$checkSum = exec($cmd) or die("ERROR:899900");
}

if ($checkSum == '8001'||$checkSum == '8003'||$checkSum == '8009'){
	error_alt($checkSum." Error Message : make checksum error! Please contact your system administrator!");
} else {

?>


<script language="JavaScript" charset="euc-kr">
function checkSubmit(){
	var HForm = document.payment;
	//HForm.target = "payment";
	document.charset = "euc-kr";
	
	//테스트 URL 
	HForm.action = "<?=($PG_MODE=='test')?'http://tpay.billgate.net/'.$_method.'/certify.jsp':'https://pay.billgate.net/'.$_method.'/certify.jsp'?>";
	//상용 URL 
	//HForm.action = "https://pay.billgate.net/credit/certify.jsp";

	/*var option ="width=500,height=477,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,left=150,top=150";
	var objPopup = window.open("", "payment", option);

	if(objPopup == null){	//팝업 차단여부 확인
		alert("팝업이 차단되어 있습니다.\n팝업차단을 해제하신 뒤 다시 시도하여 주십시오.");
	}*/

	HForm.submit();
}
</script>

<form name="payment" method="post" charset="euc-kr" accept-charset="EUC-KR">
<input type="hidden" name="SERVICE_ID" value="<?=$serviceId?>">								<!-- 서비스아이디 -->
<input type="hidden" name="AMOUNT" value="<?=$amount?>">									<!-- 결제 금액 -->
<input type="hidden" name="ORDER_ID" class="input" value="<?=$orderId?>">					<!-- 주문번호 -->
<input type="hidden" name="ORDER_DATE" size=20 class="input" value="<?=$orderDate?>">		<!-- 주문일시 -->
<input type="hidden" name="USER_IP" size=20 class="input" value="<?=$userIp?>">				<!-- 고객 IP -->
<input type="hidden" name="ITEM_NAME" size=20 class="input" value="<? echo cutstr($itemName,15); ?>">			<!-- 상품명 -->
<input type="hidden" name="ITEM_CODE" size=20 class="input" value="<?=$itemCode?>">			<!-- 상품코드 -->
<input type="hidden" name="USER_ID" size=20 class="input" value="<?=$userId?>">				<!-- 고객 아이디 -->
<input type="hidden" name="USER_NAME" size=20 class="input" value="<?=$userName?>">			<!-- 고객명 -->
<input type="hidden" name="INSTALLMENT_PERIOD" size=30 class="input" value="0:3:6:9:12">	<!-- 할부개월수 -->
<input type="hidden" name="_paymethod" value="<?=$r[paymethod]?>">
<input type="hidden" name="RETURN_URL" size=50 class="input" value="<?=$returnUrl?>">		<!-- Return Url -->
<input type="hidden" name="CHECK_SUM" class="input" value="<?=$checkSum?>">					<!-- Check Sum -->
</form>


<? } // checksum end ?>