<?
	require_once dirname(__FILE__).'/Encryptor.class.php';
	
	$mid = "mnbank001m";	//����id
    $merchantKey = "zutht7y2mL0DQWk7mkY2Jt+2B7hxqRBtnQ0tK0nl3ZhfztnX5sXSyApEatooQODfz5wNa7DTxzogjWqbxLfa6Q==";	//����Ű
   	$amt = "1004";	 //�����ݾ�
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
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<link rel="apple-touch-icon" href=""/>
<link rel="apple-touch-startup-image" href="" />
<style type="text/css">

	body{
		font-family:�������, ����, Tahoma, Geneva, sans-serif;
		font-size:13px;
		padding:0;
		margin:0;
		background: #F0F1F3;
		background-repeat: repeat-x;	
		width:99%;
		
		a{text-decoration:none;}
		a:link		{color:#000; cursor:pointer;} 
		a:visited 	{color:#000; cursor:pointer;} 
		a:hover		{color:#c8c8c8; cursor:pointer;} 
		a:active	{color:#000; cursor:pointer;}
	}
	
	.TitleBar{font-size:17px; color:#000; font-weight:bold;}
	
	@-webkit-keyframes zoom {
	 from {
	   opacity: 0.1;
	   font-size: 100%;
	 }
	 to {
	   opacity: 1.0;
	   font-size: 130%;
	 }
	}
	
	.selectBar{
		padding:6px 3px;
	}
	.selectBar label{
		font-size:14px;
		padding:3px;
		color:#374c83;
		font-weight:bold;
	}
	.selectBar span{
		font-size:14px;
		padding:3px;
	}
	.selectBar input{
		font-family: �������, Tahoma, Geneva, sans-serif;
		font-size:14px;
		padding:6px;
		margin:0px;
		text-align:left;
		border:1px solid #ccc;
		/* �׵θ� �ó�� ���� */
		border-radius:5px;
		-webkit-border-radius:5px;
		-moz-border-radius:5px;
		-o-border-radius:5px;
		background:#EEEEEE;
		/* �׸��� */
		box-shadow:inset 0 0 5px #ccc; 
		-moz-box-shadow:inset 0 0 5px #ccc; 
		-webkit-box-shadow:inset 0 0 5px #ccc;
	}
	.selectBar .listInput{
		width:90px;
		text-align:left;
	}
	
	.selectBar .largeInput{
		width:95%;
		text-align:left;
	}	
	
	.selectBar div {
		float:right;
		padding:0;
		margin:0;
	}
	
	.selectBar select{
		width:140px; 
		font-size:15px;
	}
	
	.selectList ul{
		list-style:none;
		margin:5px; 
		padding:0; 
	}
</style>

<script type="text/javascript">
	function changeAmt(){
		frm = document.transMgr;
		frm.action = "mainPay.jsp";
		frm.target = "_self";
		frm.submit();
	}

	function submitForm(){
			
		frm = document.transMgr;
		
		if(frm.transType[1].checked){
			
			if(frm.payMethod.value != "CARD" && frm.payMethod.value != "BANK" && frm.payMethod.value != "VBANK"){
				alert("����ũ�ο��� �������� �ʴ� ���������Դϴ�.");
				return;
			}else{
				frm.action = "<?=payActionUrl ?>/webTxInit";
				frm.submit();
			}
			
		}else{
			frm.action = "<?=payActionUrl ?>/webTxInit";
			frm.submit();
		}
	}

</script>
<title>m&amp;Bank::���ͳݰ���</title>
</head>
<body>
<form id="transMgr" name="transMgr" method="post">
	<p class="TitleBar">���� ���� ���� ���α׷�</p>
	<hr>
	<div class="selectList">
		<ul>
			<li class="selectBar">
				<label for="">��������</label>
				<select name="payMethod" id="payMethod">
					<option value="">[����]</option>
					<option value="CARD">[�ſ�ī��]</option>
					<option value="BANK">[������ü]</option>
					<option value="VBANK">[�������]</option>
					<option value="CELLPHONE">[�޴�������]</option>
				</select>
				<input type="button" id="submitBtn" value="���� ����" onclick="submitForm();">
			</li>
		</ul>
		<ul>
			<li class="selectBar">
				<label for="">����Ÿ��(*)</label>
				<label>�Ϲ�</label><input type="radio" id="transTypeN" name="transType" value="0" checked="checked">
				<label>����ũ��</label><input type="radio" id="transTypeE" name="transType" value="1">				
			</li>
		</ul>
		<ul>
			<li class="selectBar">
				<label for="">��ǰ��(*)</label><br>
				<input type="text" name="goodsName" value="mn_��ǰ��">
			</li>
		</ul>
		<ul>
			<li class="selectBar">
				<label for="">��ǰ����(*)</label><br>
				<input type="tel" pattern="[0-9]*" name="amt" value="<?=amt?>"> ��
				<!-- <input type="button" value="�ݾ� ����" onclick="changeAmt();" /> -->
			</li>
		</ul>
		<ul>
			<li class="selectBar">
				<label for="">��ǰ�ֹ���ȣ(*)</label><br>
				<input type="text" name="moid" value="<?=moid?>">
			</li>
		</ul>
		<ul>
			<li class="selectBar">
				<label for="">ȸ�����ID</label><br>
				<input type="text" name="mallUserId" value="mn_id">
			</li>
		</ul>
		<ul>
			<li class="selectBar">
				<label for="">�����ڸ�(*)</label><br>
				<input type="text" name="buyerName" value="mn_�����ڸ�">
			</li>
		</ul>	
		<ul>
			<li class="selectBar">
				<label for="">�����ڿ���ó((-)���� �Է�)</label><br>
				<input type="tel" pattern="[0-9]*" maxlength="11" name="buyerTel" value="0212345678">
			</li>
		</ul>
		<ul>
			<li class="selectBar">
				<label for="">�����ڸ����ּ�(*)</label><br>
				<input type="text" name="buyerEmail" value="test@test.com">
			</li>
		</ul>
		<ul>
			<li class="selectBar">
				<label for="">�����Ⱓ</label><br>
				<input type="text" name="prdtExpDate" value="2013�� 12�� 31�� ����">
			</li>
		</ul>
		<hr>
		<ul>
			<li class="selectBar">
				<label for="">ȸ������̵�(*)</label><br>
				<input type="text" name="mid" value="<?=mid ?>" readonly="readonly">
			</li>
		</ul>		
		<ul>
			<li class="selectBar">
				<label for="">������� ���� URL(*)</label><br>
 				<input type="text" name="returnUrl" class="largeInput" value="<?=payLocalUrl?>/returnPay.jsp" readonly="readonly">
			</li>
		</ul>
		<ul>
			<li class="selectBar">
				<label for="">������� URL(*)</label><br>
 				<input type="text" name="cancelUrl" class="largeInput" value="<?=payLocalUrl?>/mainPay.jsp" readonly="readonly">
			</li>
		</ul>
		<ul>
			<li class="selectBar">
				<label for="">��������Աݱ���(*)</label><br>
				<input type="text" name="vbankExpDate" value="<?=vbankExpDate?>" readonly="readonly">
			</li>
		</ul>
		<ul>
			<li class="selectBar">
				<label for="">���ӹ��(*)</label><br>
				<select name="connType" id="connType">
					<option value="0">Web(M-browser)</option>
					<option value="1">App(BaroBaro)</option>
					<option value="2">App(WebViewController)</option>
				</select>
			</li>
		</ul>
		<ul>
			<li class="selectBar">
				<label for="">�� ��Ű��</label><br>
				<input type="text" name="appPrefix" value="ibWebTest">
			</li>
		</ul>
	</div>

	<input type="hidden" name="payType" value="1"><!-- �������� -->
	<input type="hidden" name="ediDate"	value="<?=ediDate?>"><!-- ������ -->
	<input type="hidden" name="encryptData" value="<?=encryptData?>"><!-- ��ȣȭ ���� ������ -->
	<input type="hidden" name="userIp"	value="<?=request.getRemoteAddr()?>"><!-- User IP Address -->
	<input type="hidden" name="browserType" id="browserType" value="SPG"><!-- SmartPhone Payment Gateway -->
	<input type="hidden" name="mallReserved" value="MallReserved">
</form>
</body>
</html>