<?
	require_once dirname(__FILE__).'/Encryptor.class.php';
	
	$mid = "mnbank001m";	//상점id
    $merchantKey = "zutht7y2mL0DQWk7mkY2Jt+2B7hxqRBtnQ0tK0nl3ZhfztnX5sXSyApEatooQODfz5wNa7DTxzogjWqbxLfa6Q==";	//상점키
   	$amt = "1004";	 //결제금액
	$moid = "mnoid1234567890";
   	
    //$ediDate, $mid, $merchantKey, $amt    
	$encryptor = new Encryptor($merchantKey);

	$encryptData = $encryptor->encData($amt.$mid.$moid);
	$ediDate = $encryptor->getEdiDate();	
    $vbankExpDate = $encryptor->getVBankExpDate();	
        	
	$payActionUrl = "https://webtx.mnbank.co.kr";
	$payLocalUrl = "http://mlrs2a04.cafe24.com";
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" href="css/nyroModal.mnbank.custom.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="js/jquery.nyroModal.mnbank.custom.js"></script>
<script type="text/javascript" src="js/client.mnbank.mnwebtx.js" defer="defer" async="async"></script>
<script type="text/javascript">
	function changeAmt(){
		frm = document.transMgr;
		frm.action = "mainPay.jsp";
		frm.target = "_self";
		$('#transMgr').removeClass("nyroModal");
		frm.submit();
	}

	function resultResponse(param){
		alert(param);
		$('#resultDiv').append(param.resultCd +":"+param.resultMsg+":"+param.tid);
		//다른 페이지로 파라미터 넘기기
		submitParametersToNextPage(param, "./decryptAndDb.php");
	}

	$(function() {
		$('#submitBtn').click(function() {
			if($('input[name=transType]:checked').val()=='1' && $('#payMethod').val()!='CARD' && $('#payMethod').val()!='BANK' && $('#payMethod').val()!='VBANK' ){
				alert("에스크로에서 지원하지 않는 결제수단입니다.");
				return;
			}
			if($("select[name=payMethod]").val() == ""){
				$('#transMgr').submit();
			}else{
				$('#transMgr').submit();
			}
		});
	});
</script>
<title>m&amp;Bank::인터넷결제</title>
</head>
<body>
<form id="transMgr" method="post" action="<?=$payActionUrl ?>/webTxInit" class="nyroModal" target="_blank">
	<div style="width: 550px; border-color: aqua; border: aqua ">
		<p><strong>결제 상점 데모 프로그램</strong></p>
		<p>
		<label>결제수단</label>
		<select name="payMethod" id="payMethod">
			<option value="">[선택]</option>
			<option value="CARD">[신용카드]</option>
			<option value="BANK">[계좌이체]</option>
			<option value="VBANK">[가상계좌]</option>
			<option value="CELLPHONE">[휴대폰결제]</option>
			<option value="CDBILLRG">[신용카드자동결제]</option>
		</select>
		</p>
		<p>
			<label>결제타입</label>
			<label>일반</label><input type="radio" id="transTypeN" name="transType" value="0" checked="checked">
			<label>에스크로</label><input type="radio" id="transTypeE" name="transType" value="1">
		</p>
		<p>
			<label>상품명(*)</label>
			<input type="text" name="goodsName" value="mn_상품명">
		</p>
		<p>
			<label>상품가격(*)</label>
			<input type="text" name="amt" value="<?=$amt?>"> 원
			<input type="button" value="금액 변경" onclick="changeAmt();" />
		</p>
		<p>
			<label>상품주문번호</label>
			<input type="text" name="moid" value="<?=$moid?>">
		</p>
		<p>			
			<label>회원사아이디(*)</label>
			<input type="text" name="mid" value="<?=$mid?>" readonly="readonly">
		</p>
		<p>
			<label>결제결과 전송 URL(*)</label>
 			<input type="text" name="returnUrl" value="<?=$payLocalUrl?>/webtx/iframeResponse.php">
		</p>
		<p>
			<label>회원사고객ID</label>
			<input type="text" name="mallUserId" value="mn_id">
		</p>
		<p>
			<label>구매자명</label>
			<input type="text" name="buyerName" value="mn_구매자명">
		</p>
		<p>
			<label>구매자연락처((-)없이 입력)</label>
			<input type="text" name="buyerTel" value="0212345678">
		</p>
		<p>
			<label>구매자메일주소(*)</label>
			<input type="text" name="buyerEmail" value="aaa@bbb.com">
		</p>
		<p>
			<label>보호자메일주소</label>
			<input type="text" name="parentEmail">
		</p>
		<p>
			<label>배송지주소</label>
			<input type="text" name="buyerAddr" value="경기도 성남시 분당구 대왕판교로 660">
		</p>
		<p>
			<label>우편번호</label>
			<input type="text" name="buyerPostNo" value="463400">
		</p>
		<p>
			<label>Mall IP</label>
			<input type="text" name="mallIp" value="<?=$_SERVER['SERVER_ADDR']?>">
		</p>
		<p>
			<label>상점예비정보</label>
			<input type="text" name="mallReserved" value="MallReserved">
		</p>
		<p>
			<label>가상계좌입금기한</label>
			<input type="text" name="vbankExpDate" value="<?=$vbankExpDate?>">
		</p>
		<p>
			<label>수취인전달메시지</label>
			<input type="text" name="rcvrMsg" value="rcvrMsg">	
		</p>
		<p>
			<label>제공기간</label>
			<input type="text" name="prdtExpDate" value="20131231">	
		</p>
		<p>
			<label>결제결과 페이지 유무</label>
			<input type="text" name="resultYn" value="Y">	
		</p>
	</div>		
	
	<input type="hidden" name="payType" value="1">	
	<input type="hidden" name="ediDate"	value="<?=$ediDate?>">
	<input type="hidden" name="encryptData" value="<?=$encryptData?>">
	<input type="hidden" name="userIp"	value="<?=$_SERVER['REMOTE_ADDR']?>">
	<input type="button" id="submitBtn" value="결제 전송(btn))">
</form>

<div id="resultDiv"></div>
</body>
</html>