<?

	include_once(dirname(__FILE__)."/../../include/inc.php");

	// delete
	$que = " delete from odtProductLatest where pl_uid='".$uid."'	";
	_MQ_noreturn($que);

	echo "<SCRIPT>parent.latest_view()</SCRIPT>";
	exit;

?>