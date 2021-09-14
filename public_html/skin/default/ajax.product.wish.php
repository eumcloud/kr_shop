<?

	include_once dirname(__FILE__)."/../../include/inc.php";

	switch($mode) {

		case "add":

			if( !$code ) {
				echo "잘못된 접근입니다."; exit;
			}

			// 상품 중복 체크
			$ir = _MQ("select count(*) as cnt from odtProductWish where pw_inid='" . get_userid() . "' and pw_pcode='". $code ."'");
			if( $ir['cnt'] > 0 ) {
				echo "이미 찜한 상품입니다."; exit;
			}

			$que = "
				insert odtProductWish set
					  pw_pcode='". $code ."'
					, pw_inid='". get_userid() ."'
					, pw_rdate=now()
			";
			_MQ_noreturn($que);

			$r = _MQ("select count(*) as cnt from odtProductWish where pw_inid='" . get_userid() . "'");
			echo $r['cnt'];

		break;

		case "delete":

			if( !$code ) {
				echo "잘못된 접근입니다."; exit;
			}

			$que = " delete from odtProductWish where pw_inid='" . get_userid() . "' and pw_pcode='". $code ."' ";
			_MQ_noreturn($que);

			$r = _MQ("select count(*) as cnt from odtProductWish where pw_inid='" . get_userid() . "'");
			echo $r['cnt'];

		break;


	}

?>