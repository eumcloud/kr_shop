<?PHP

	include "../include/inc.php";

	samesiteCookie("auth_adminid", "" , -999 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
	samesiteCookie("auth_adminid_sess", "" , -999 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
	AdminLogout();

	error_loc("/totalAdmin/");

?>