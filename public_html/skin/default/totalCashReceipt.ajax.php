<?
	header('Content-Type: text/html; charset=utf8');
	session_start();
	if( !$_path_str ) {
		if( @file_exists("../include/inc.php") ) {
			$_path_str = "..";
		}
		else {
			$_path_str = "../..";
		}
	}
	include_once($_path_str."/include/inc.php");

	include 'totalCashReceipt.php';

	echo $return;

?>