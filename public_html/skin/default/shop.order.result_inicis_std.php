<?
	# 카드결제에 필요한 셋팅
    /**************************
     * 1. 라이브러리 인클루드 *
     **************************/
    require_once(PG_DIR."/inicis/libs/INIStdPayUtil.php");
    require_once(PG_DIR."/inicis/libs/sha256.inc.php");

		$inipay = new INIStdPayUtil();

	if( $r[paymethod] == 'V'){
		 $_pg_mid = trim($row_setup[P_SID]) && trim($row_setup[P_SID_SKEY]) ? $row_setup[P_SID] : $row_setup[P_ID];
		 $_pg_skey = trim($row_setup[P_SID]) && trim($row_setup[P_SID_SKEY]) ? $row_setup[P_SID_SKEY] : trim($row_setup[P_SKEY]);
	}else{
		 $_pg_mid = trim($row_setup[P_ID]);
		 $_pg_skey = trim($row_setup[P_SKEY]);
	}

//    $row_setup[P_SID] = $row_setup[P_SID] && $row_setup[P_SID_SKEY]?$row_setup[P_SID]:$row_setup[P_ID]; // 키값을 가져온다.
//    $_pg_mid = $r[paymethod]=='V'?$row_setup[P_SID]:$row_setup[P_ID]; // 에스크로/일반
//
//    $row_setup[P_SID_SKEY] = trim($row_setup[P_SID_SKEY])?trim($row_setup[P_SID_SKEY]):trim($row_setup[P_SKEY]);
//    $_pg_skey = $r[paymethod] == 'V' ? trim($row_setup[P_SID_SKEY]): trim($row_setup[P_SKEY]); // 사인키


		$timestamp = $inipay->getTimestamp();   // util에 의해서 자동생성
		$ordernum = $ordernum; // 가맹점 주문번호(가맹점에서 직접 설정)
		$price = $r[tPrice];        // 상품가격(특수기호 제외, 가맹점에서 직접 설정)

		$mKey = hash("sha256", $_pg_skey);

		$params = array(
		    "oid" => $ordernum,
		    "price" => $price,
		    "timestamp" => $timestamp
		);

		$sign = $inipay->makeSignature($params);


		// PG사에 맞게 변수 설정

		if($r[paymethod] == "C") $gopaymethod = "Card";
		if($r[paymethod] == "L") $gopaymethod = "DirectBank";
		if($r[paymethod] == "V") $gopaymethod = "VBank";

		$siteDomain = "http://".$_SERVER['HTTP_HOST']; //가맹점 도메인 입력
?>


	<form name="ini" id="ini_form" method="post" target="common_frame" >
	<input type="hidden" name="gopaymethod"		value="<?=$gopaymethod?>">
	<input type="hidden" name="paymethod"		value="<?=$gopaymethod?>">
	<input type="hidden" name="goodname"		value="<?=$app_product_name?>">
	<input type="hidden" name="buyername"		value="<?=$r[ordername]?>">
	<input type="hidden" name="buyeremail"		value="<?=$r[orderemail]?>">
	<input type="hidden" name="buyertel"		value="<?=phone_print($r[orderhtel1],$r[orderhtel2],$r[orderhtel3])?>"	>

	<input type="hidden" name="mid"				value="<?=$_pg_mid?>"    >
	<input type="hidden" name="price"			value="<?=$r[tPrice]?>"	>
	<input type="hidden"   name="timestamp" value="<?php echo $timestamp ?>" >
	<input type="hidden"  name="mKey" value="<?php echo $mKey ?>" >
	<input type="hidden" name="oid" size=40		value="<?=$ordernum?>">
	<input type="hidden" name="signature" 	value="<?=$sign?>">
	<input type="hidden" name="returnUrl" value="<?php echo $siteDomain ?>/pages/shop.order.result_inicis_std.pro.php" > <?php // 결과를 return 해줄 페이지?>
	<input type="hidden" name="popupUrl" value="<?php echo $siteDomain ?>/pages/shop.order.result_inicis_std_popup.php" > <?php // 결제모듈이 노출되는 팝업 이벤트창 ?>
	<input type="hidden" name="closeUrl" value="<?php echo $siteDomain ?>/pages/shop.order.result_inicis_std_close.php" > <?php // 닫기 이벤트창?>
	<input type="hidden" name="quotabase" value="2:3:4:5:6:9:12" ><!-- 2020-01-30 SSJ :: 할부개월 추가 -->

<?
if($r[paymethod] == "V") { $_virtual_due_date = date('Ymd', time() + ($row_setup[P_V_DATE] * 86400));
?>
	<input type="hidden" name="acceptmethod"	value="HPP(2):OCB:va_receipt:Vbank(<?=$_virtual_due_date?>):useescrow">
<?
} else {
?>
	<input type="hidden" name="acceptmethod"	value="HPP(2):Card(0):OCB:VBank:DirectBank:receipt:cardpoint<?=$gopaymethod=='DirectBank'?':useescrow':''?>">
<?
}
?>

	<?/* 기타설정 */?>
	<input type=hidden name=currency size=20 value="WON">
	<input type=hidden name=version value=1.0>

	</form>

<?php if($_pg_mid == "INIpayTest") { ?>
<script language="javascript" type="text/javascript" src="https://stgstdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>
<?php }else{?>
<script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>
<?php }?>
<script language="JavaScript" type="text/JavaScript">

	function ini_submit()
	{
		// MakePayMessage()를 호출함으로써 플러그인이 화면에 나타나며, Hidden Field
		// 에 값들이 채워지게 됩니다. 일반적인 경우, 플러그인은 결제처리를 직접하는 것이
		// 아니라, 중요한 정보를 암호화 하여 Hidden Field의 값들을 채우고 종료하며,
		// 다음 페이지인 INIsecureresult.php로 데이터가 포스트 되어 결제 처리됨을 유의하시기 바랍니다.



		if(document.ini.goodname.value == "")  // 필수항목 체크 (상품명, 상품가격, 구매자명, 구매자 이메일주소, 구매자 전화번호)
		{
			alert("상품명이 빠졌습니다. 필수항목입니다.");
			return false;
		}
		else if(document.ini.buyername.value == "")
		{
			alert("구매자명이 빠졌습니다. 필수항목입니다.");
			return false;
		}
		else if(document.ini.buyeremail.value == "")
		{
			alert("구매자 이메일주소가 빠졌습니다. 필수항목입니다.");
			return false;
		}
		else if(document.ini.buyertel.value == "")
		{
			alert("구매자 전화번호가 빠졌습니다. 필수항목입니다.");
			return false;
		}
		else
		{

						 INIStdPay.pay('ini_form');

		}


	}


//-->
</script>