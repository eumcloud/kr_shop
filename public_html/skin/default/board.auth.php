<?PHP
	include dirname(__FILE__)."/../../include/inc.php";

	// 글 번호나 비밀번호 없을시
	if(!$_uid || !$passwd) error_alt("잘못된 접근입니다. 다시 시도하세요");


	$r = _MQ("select * from odtBbs where b_uid = '".$_uid."'");
	$re = _MQ("select * from odtBbs where b_uid='".$r[b_relation]."'");

	$tmpr = _MQ("select password('". $passwd ."') as pw ");
	$app_password = $tmpr['pw'];

	$is_auth = $r[b_passwd] == $app_password && $passwd ? true : false;
	if(!$is_auth) { $is_auth = $re[b_passwd] == $app_password && $passwd ? true : false; }


	if(!$is_auth) 
		error_alt("비밀번호가 맞지 않습니다.");
	else 
		samesiteCookie("auth_request_".($r[b_relation]>0?$r[b_relation]:$_uid),time(),"0","/");
	

	switch($_mode) {
		case "view" :
			error_frame_loc("/?pn=board.view&_menu=".$r[b_menu]."&_uid=".$_uid."&_PVSC=".$_PVSC);
			break;
		case "delete" :
			error_loc("/pages/board.pro.php?_mode=delete&_uid=".$_uid."&_PVSC=".$_PVSC);
			break;
	}
?>
