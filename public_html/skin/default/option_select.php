<?PHP

	include_once(dirname(__FILE__)."/../../include/inc.php");

//    $code = $_POST[code];
//    $depth = $_POST[depth];
    $depth_next = $depth + 1;
//    $uid = $_POST[uid];
    if($uid == "undefined") {
        $uid = "";
    }
//    $uid1 = $_POST[uid1];
    if($uid1 == "undefined") {
        $uid1 = "";
    }

    if( !$code || !$depth ) {
        exit;
    }


    $str_option = "";






	// 대표상품 display
    $que = "select * from odtProduct where code = '".$code."' ";
    $r = _MQ($que);

	// 옵션타이틀
	$arr_option_title = array(
		"1" => trim($r[option1_title]),
		"2" => trim($r[option2_title]),
		"3" => trim($r[option3_title])
	);



    // 재고없이 옵션만 추출
    if( ( str_replace("depth","",$r[option_type_chk]) - $depth ) > 1 ) {
        // 옵션정보 불러오기
        $sque = "
            SELECT * FROM odtProductOption 
            WHERE 
                oto_pcode='" . $code . "' 
                and oto_depth= " . $depth_next . "
                and find_in_set( " . $uid ." , oto_parent) > 0
                and oto_view = 'Y'
            order by oto_sort asc, oto_uid asc
        ";
        $sres = _MQ_assoc($sque);
		foreach( $sres as $k=>$sr ){
            $str_option .= "<option value='".$sr[oto_uid]."'>".$sr[oto_poptionname]."</option>";
        }

        echo "<select name=_option_select" . $depth_next . " ID='option_select" . $depth_next . "_id' class='add_option' onchange=\"option_select(" . $depth_next . ",'".$code."')\"><option value=''>".($arr_option_title[$depth_next] ? $arr_option_title[$depth_next] : $depth_next."차옵션을 선택하세요") ."</option>" . $str_option . "</select>";

    }



    // 재고 추출
    else {

        if( $r[option_type_chk] == "1depth" ) {
            // 옵션정보 불러오기
            $sque = "
                SELECT * FROM odtProductOption 
                WHERE 
                    oto_pcode='" . $code . "'
                    and oto_view = 'Y'
                order by oto_sort asc, oto_uid asc 
            ";
        }
        else {
            // 옵션정보 불러오기
            $sque = "
                SELECT * FROM odtProductOption 
                WHERE 
                    oto_pcode='" . $code . "' 
                    and oto_depth= " . $depth_next . "
                    and find_in_set( " . $uid ." , oto_parent) > 0
                    and oto_view = 'Y'
                order by oto_sort asc, oto_uid asc
            ";
        }
        $sres = _MQ_assoc($sque);
		foreach( $sres as $k=>$sr ){
            $str_option .= "<option value='".$sr[oto_uid]."'>".$sr[oto_poptionname]." (잔여:".  ($sr[oto_cnt] > 0 ? number_format($sr[oto_cnt])  : "품절") .") / " . ($sr[oto_poptionprice] < 0 ? "" : "+") . number_format($sr[oto_poptionprice]) . "원</option>";
        }
        echo "<select name=_option_select" . $depth_next . " ID='option_select" . $depth_next . "_id' class='add_option' onchange=\"option_select_add('".$r[code]."')\"><option value=''>".($arr_option_title[$depth_next] ? $arr_option_title[$depth_next] : "상세옵션을 선택하세요")."</option>" . $str_option . "</select>";
    }

    exit;

?>
