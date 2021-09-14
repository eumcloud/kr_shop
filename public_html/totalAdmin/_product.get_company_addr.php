<?PHP

	include_once("inc.php");

	$r = _MQ(" SELECT * FROM odtMember where userType = 'C' and id !='onedaynet' and id='" . $id . "' ");

	// LMH003
	echo json_encode(array('juso'=>htmlspecialchars($r[address]),'mapx'=>htmlspecialchars($r[com_mapx]),'mapy'=>htmlspecialchars($r[com_mapy])));

?>