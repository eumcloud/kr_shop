<?PHP
include_once(dirname(__FILE__)."/../../include/inc.php");

switch($_mode) {
	case "my_local";
		// 기존 데이터 삭제.
		_MQ_noreturn("delete from odtMyLocal where ml_id = '".(is_login() ? get_userid() : $_COOKIE["AuthShopCOOKIEID"])."'");

		// 데이터 입력.
		_MQ_noreturn("insert into odtMyLocal set ml_cuid = '".implode(",",$my_local)."', ml_id='".(is_login() ? get_userid() : $_COOKIE["AuthShopCOOKIEID"])."'");

		error_frame_loc("/?pn=product.list&cuid=" . $cuid);

		break;
	}
?>
