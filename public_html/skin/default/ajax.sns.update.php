<?PHP
	header("Content-Type:text/html;charset=utf-8"); 

	include dirname(__FILE__)."/../../include/inc.php";

	$que = "insert into odtSnsLog set
					sl_pcode			=	'".$_GET[pcode]."',
					sl_type				=	'".$_GET[type]."',
					sl_ip				=	'".$_SERVER[REMOTE_ADDR]."',
					sl_rdate			=	now()";

	$res = mysql_query($que);


?>