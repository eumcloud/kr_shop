<?PHP

	include_once( dirname(__FILE__)."/inc.php");

	samesiteCookie("auth_adminid", "" , -999 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
	samesiteCookie("auth_adminid_sess", "" , -999 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));

	error_loc("index.html");

?>