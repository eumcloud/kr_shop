<?
	require_once dirname(__FILE__).'/Encryptor.class.php';
	
	//webTx���� ���� �������
	$payMethod = $_POST['payMethod'];
	$mid = $_POST['mid'];
	$tid = $_POST['tid'];
	$mallUserId = $_POST['mallUserId'];
	$amt = $_POST['amt'];
	$buyerName = $_POST['buyerName'];
	$buyerTel = $_POST['buyerTel'];
	$buyerEmail = $_POST['buyerEmail'];
	$mallReserved = $_POST['mallReserved'];
	$goodsName = $_POST['goodsName'];
	$moid = $_POST['moid'];
	$authDate = $_POST['authDate'];
	$authCode = $_POST['authCode'];
	$fnCd = $_POST['fnCd'];
	$fnName = $_POST['fnName'];
	$resultCd = $_POST['resultCd'];
	$resultMsg = $_POST['resultMsg'];
	$errorCd = $_POST['errorCd'];
	$errorMsg = $_POST['errorMsg'];
	$vbankNum = $_POST['vbankNum'];
	$vbankExpDate = $_POST['vbankExpDate'];
	$ediDate = $_POST['ediDate'];
	
	//ȸ���� DB�� ����Ǿ��ִ� ��
	$amtDb = "";//�ݾ�
	$moidDb = "";//moid
	$mKey = "";//����Ű
	
	$encryptor = new Encryptor($mKey, $ediDate);
	$decAmt = $encryptor->decData($amt);
	$decMoid = $encryptor->decData($moid);
	
	if( $decAmt!=$amtDb || $decMoid!=$moidDb ){
		echo "������ �����͸� �����Դϴ�.";
	}else{
		//������� ���� ���� �˸�
		ResultConfirm::send($tid, "000");
		//DBó��
	}
?>