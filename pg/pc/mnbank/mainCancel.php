<?
	require_once dirname(__FILE__).'/Encryptor.class.php';
	
	$mid = "mnbank001m";	//상점id
    $merchantKey = "zutht7y2mL0DQWk7mkY2Jt+2B7hxqRBtnQ0tK0nl3ZhfztnX5sXSyApEatooQODfz5wNa7DTxzogjWqbxLfa6Q==";	//상점키
   	$cancelAmt = "1004";	 //결제금액
	$moid = "mnoid1234567890";
   	
    //$ediDate, $mid, $merchantKey, $amt    
	$encryptor = new Encryptor($merchantKey);

	$encryptData = $encryptor->encData($cancelAmt.$mid.$moid);
	$ediDate = $encryptor->getEdiDate();	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>m&Bank::인터넷결제</title>
<script language="javascript">
<!--
function goCancelCard() {
	var formNm = document.tranMgr;
	
	// tid validation
	if(formNm.tid.value == "") {
		alert("tid를 확인하세요.");
		return false;
	} else if(formNm.tid.value.length > 30 || formNm.tid.value.length < 30) {
		alert("tid 길이를 확인하세요.");
		return false;
	}
	// 취소금액
	if(formNm.cancelAmt.value == "") {
		alert("금액을 입력하세요.");
		return false;
	} else if(formNm.cancelAmt.value.length > 12 ) {
		alert("금액 입력 길이 초과.");
		return false;
	}
	var PartialValue = "";
	// 부분취소여부 체크 - 신용카드, 계좌이체 부분취소 가능
	for(var idx = 0 ; idx < formNm.partialCancelCode.length ; idx++){
		if(formNm.partialCancelCode[idx].checked){
			PartialValue = formNm.partialCancelCode[idx].value;
			break;
		}
	}
	
	if(PartialValue == '1'){
		if(formNm.tid.value.substring(10,12) != '01' &&  formNm.tid.value.substring(10,12) != '02' &&  formNm.tid.value.substring(10,12) != '03'){
			alert("신용카드결제, 계좌이체, 가상계좌만 부분취소/부분환불이 가능합니다");
			return false;
		}
	}
	formNm.submit();
	return true;
}

-->
</script>
</head>
<body onbeforeunload="" oncontextmenu='return false' ondragstart='return false'>
<form name="tranMgr" method="post" action="https://webtx.mnbank.co.kr/payCancel">
	<img src="http://pg.mnbank.co.kr/images/bar02.gif" width="213" height="37">
	<div id="content_right" style="width:490px; height:150px;">
	  <div id="content_right_top">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="http://pg.mnbank.co.kr/images/icon_b.gif" align="absmiddle"><strong>취소정보를 확인하십시오.</strong></td>
      </tr>
    </table>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr> 
			  <td style="padding:2px;"><table width="100%" border="0" cellspacing="1">
					<tr> 
					<td >mid</td>
					<td ><input name="mid" maxlength="30" size="30" value="<?=$mid?>"></td>
				  </tr>
				  <tr> 
					<td >tid</td>
					<td ><input name="tid" maxlength="30" size="30" value=""></td>
				  </tr>
				  <tr> 
					<td >moid</td>
					<td ><input name="tid" maxlength="30" size="30" value="<?=moid?>"></td>
				  </tr>
				  <tr> 
					<td height="1" colspan="2" bgcolor="#cccccc"></td>
				  </tr>
				  <tr> 
					<td >취소패스워드</td>
					<td >
					<input type="password" name="cancelPw" size="20" value=""> * 데모시 미입력
					</td>
				  </tr>				  
				  <tr> 
					<td height="1" colspan="2" bgcolor="#cccccc"></td>
				  </tr>
				  <tr> 
					<td >취소금액</td>
					<td ><input name="cancelAmt" size="20" value="<?=$cancelAmt?>"></td>
				  </tr>
				  <tr> 
					<td height="1" colspan="2" bgcolor="#cccccc"></td>
				  </tr>
				  <tr> 
					<td >취소사유</td>
					<td ><input name="cancelMsg" size="20" value="고객요청"></td>
				  </tr>
				  <tr> 
					<td height="1" colspan="2" bgcolor="#cccccc"></td>
				  </tr>
				  <tr> 
					<td >부분취소 여부</td>
					<td>
						<input type="radio" name="partialCancelCode" value="0" checked="checked"/>전체취소
						<input type="radio" name="partialCancelCode" value="1"/>부분취소
					</td>
				  </tr>
				  <tr>
				  	<td>returnUrl</td>
				  	<td><input type="text" name="returnUrl" value="https://webtx.mnbank.co.kr/sampleJSP/cancelReturnUrl.jsp"/></td>
				  </tr>
				  <tr>
				  	<td>encryptData</td>
				  	<td>
				  		<input type="text" name="encryptData" value="<?=$encryptData?>" />
				  	</td>
				  </tr>
				  <tr>
				  	<td>ediDate</td>
				  	<td>
				  		<input type="text" name="ediDate" value="<?=$ediDate?>" />
				  	</td>
				  </tr>
				</table></td>
			</tr>
		  </table>
		  <br/>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>
        	<table border="0" align="center" cellpadding="0" cellspacing="0">
          	<tr>
							<td><img src="http://pg.mnbank.co.kr/images/btn_ok.gif" width="64" height="24" border="0" onClick="return goCancelCard();"></td>
           		<td width="20"></td>
            	<td><img src="http://pg.mnbank.co.kr/images/btn_reset.gif" onClick="javascript:tranMgr.reset()" width="64" height="24" border="0"></td>
							<td width="10"></td>			
          	</tr>
        	</table>
        </td>
      </tr>
    	</table>
		</div>
	</div>
<input type="hidden" name="cc_ip" size="20" value="<?=$_SERVER['REMOTE_ADDR']?>">
</form>

</body>
</html>