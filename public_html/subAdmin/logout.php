<?PHP

	include "../include/inc.php";

	samesiteCookie("auth_comid", "" , -999 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
	samesiteCookie("auth_comid_sess", "" , -999 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));

	error_loc("/totalAdmin/");

?>