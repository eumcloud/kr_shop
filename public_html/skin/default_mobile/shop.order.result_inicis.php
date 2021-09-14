<?

$row_setup[P_SID] = $row_setup[P_SID]?$row_setup[P_SID]:$row_setup[P_ID];
$_pg_mid = $r[paymethod]=='V'?$row_setup[P_SID]:$row_setup[P_ID];

?>
<script language="javascript">	
function call_pay_form()
{
    var order_form = document.ini;
	var paymethod = "";
	//var wallet = window.open("", "BTPG_WALLET");

		switch("<?=$r[paymethod]?>") {        
        case "C":   //신용카드
            paymethod="wcard";
            break;
//        case "H":   //휴대폰
//            paymethod="mobile";
//            break;
        case "V":   //가상계좌
            paymethod="vbank";
            break;
//        case "M":   //문화상품권
//            paymethod="culture";
//            break;
//        case "P":   //해피머니
//            paymethod="hpmn";
//            break;
    }	
/*
	if (wallet == null) 
	{
		if ((webbrowser.indexOf("Windows NT 5.1")!=-1) && (webbrowser.indexOf("SV1")!=-1)) 
		{    // Windows XP Service Pack 2
			alert("팝업이 차단되었습니다. 브라우저의 상단 노란색 [알림 표시줄]을 클릭하신 후 팝업창 허용을 선택해주세요.");
		} 
		else 
		{
			alert("팝업이 차단되었습니다.");
		}
		return false;
	}
*/	
	//order_form.target = "BTPG_WALLET";  //새창을띄운다.
  document.charset="euc-kr";
	order_form.action = "https://mobile.inicis.com/smart/" + paymethod + "/";
	order_form.submit();

}
</script>

<!-- 스크립트-->
<form id="form1" name="ini" method="post" action="" encoding="euc-kr" accept-charset="EUC-KR" >

<input type="hidden" name="P_OID" id="textfield2" value="<?=$ordernum?>"/>
<input type="hidden" name="P_GOODS" id="textfield3"  value="<?=$app_product_name?>" />
<input type="hidden" name="P_AMT" value="<?=$r[tPrice]?>" id="textfield4" title="가격"/>
<input type="hidden" name="P_UNAME" value="<?=$r[ordername]?>" id="textfield5" title="구매자명"/>
<input type="hidden" name="P_MNAME" value="<?=$row_setup[site_name]?>" id="textfield6" title="상점명"/>
<input type="hidden" name="P_MOBILE" id="textfield7" value="<?=$r[orderhtel1].'-'.$r[orderhtel2].'-'.$r[orderhtel3]?>"/>
<input type="hidden" name="P_EMAIL"  id="textfield8" value="<?=$r[orderemail]?>" />
<input type="hidden" name="P_VBANK_DT" id="textfield9" value="<?=date('Ymd', time() + ($row_setup[P_V_DATE] * 86400))?>"/>

<input type="hidden" name="P_RESERVED" value="<? if($r['paymethod'] == 'C') { ?>twotrs_isp=Y&block_isp=Y&twotrs_isp_noti=N<? } ?><? if($r['tPrice']>299999) { ?>&ismart_use_sign=Y<? } ?>">

<input type="hidden" name="P_MID" value="<?=$_pg_mid?>"> 
<input type="hidden" name="P_NEXT_URL" value="http://<?=$_SERVER[HTTP_HOST]?>/m/shop.order.result_inicis.pro.php">
<input type="hidden" name="P_NOTI_URL" value="http://<?=$_SERVER[HTTP_HOST]?>/m/shop.order.result_inicis_paying.php"><!-- isp 전용 -->
<input type="hidden" name="P_HPP_METHOD" value="1">
<input type="hidden" name="P_RETURN_URL" value="http://<?=$_SERVER[HTTP_HOST]?>/m/shop.order.result_inicis_isp.php?ordernum=<?=$ordernum?>" title="결과화면url"><!-- isp 전용 -->

 </form>