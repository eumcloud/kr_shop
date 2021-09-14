<?PHP

	include_once(dirname(__FILE__)."/../../include/inc.php");

	if($_post <> "-" ) {
		$r = _MQ(" select da_price from odtDeliveryAddprice where da_post = '".$_post."' ");
	} else if ($_zone <> "") {
		$r = _MQ(" select da_price from odtDeliveryAddprice where da_zone = '".$_zone."' ");
	}

	if(empty($r)) {
		echo "0";
	}
	else {
		echo $r['da_price'];
	}

	exit;
?>