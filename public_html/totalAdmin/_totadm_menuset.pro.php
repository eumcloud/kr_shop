<?PHP

	include "inc.php";


	foreach($m2_seq as $kk=>$vv) {

	    $que = " SELECT * FROM m_menu_set WHERE m15_id = '" . $m2_id . "' AND m15_code1 = '" . $m2_code1[$vv] . "' AND m15_code2 = '" . $m2_code2[$vv] . "'   ";
	    $r = _MQ($que);
	    $RecCnt = sizeof($r);
	    if ($RecCnt > 0 ) {
			$Query  = " UPDATE m_menu_set SET m15_vkbn = '" . $_status[$vv] . "', m15_udate = sysdate(), m15_uid = '".$_COOKIE["AuthAdmin"]."' WHERE m15_id = '" . $m2_id . "' AND m15_code1 = '" . $m2_code1[$vv] . "' AND m15_code2 = '" . $m2_code2[$vv] . "'   ";
			_MQ_noreturn($Query);
		}


	    else {
	        $Query  = " INSERT INTO m_menu_set (m15_id, m15_code1, m15_code2, m15_vkbn, m15_udate, m15_uid) VALUES ('" . $m2_id . "', '" . $m2_code1[$vv] . "', '" . $m2_code2[$vv] . "', '" . $_status[$vv] . "', sysdate(), '". $row_admin[id] ."')  ";
			_MQ_noreturn($Query);
	    }

	}

error_alt("수정하였습니다.");

?>