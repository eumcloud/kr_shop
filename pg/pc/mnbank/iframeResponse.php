<?PHP
	$statusCl = $_POST['statusCl'];
	$name = "";
	$STATUS_READY = "0";
	$STATUS_CLOSE = "1";
	$STATUS_TRANS = "2";
	$resultStr = "";

	if($STATUS_TRANS==$statusCl){
		$resultStr.="{";
		$init = false;
		while (list($name, $value) = each($_POST)){
			if($init==true){
				$resultStr.=",";
			}else{
				$init = true;
			}
			$resultStr.="\"".$name."\":\"".$value."\"";
		}
		$resultStr.="}";
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>m&amp;Bank::인터넷결제</title>
</head>
<body>
<script type="text/javascript">
	if('<?=$STATUS_READY?>' == '<?=$statusCl?>'){
		parent.parent.displayShow();
	}else if('<?=$STATUS_CLOSE?>' == '<?=$statusCl?>'){
		parent.parent.payWinClose();
	}else if('<?=$STATUS_TRANS?>' == '<?=$statusCl?>'){
		parent.parent.resultResponseIframe(<?=$resultStr ?>);
	}	
</script>
</body>
</html>