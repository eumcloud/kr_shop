<?PHP
# LDD010
// 카테고리 정보에 대한 3단 select 배열을 위한 ajax
$app_mode = "popup";
include_once("inc.php");


############### 옵션 SORTing ############### 
// pass_type - U : up , D :down
// pass_depth - 1, 2, 3 
// pass_uid  -옵션 고유번호
if( in_array($pass_type , array("insert")) && $pass_depth && $pass_uid ) {

    // 타겟 데이터 정보 추출
    $target_r = _MQ(" select * from odtProductOption where oto_uid='" . $pass_uid ."' ");

    // 타켓 하위 순위 1 순위씩 밀림
    _MQ_noreturn(" update odtProductOption set oto_sort = oto_sort + 1 where oto_pcode='" . $pass_code . "' and oto_depth='". $pass_depth ."' ". ($target_r['oto_parent'] ? " and oto_parent = '". $target_r['oto_parent'] ."' " : "") . " and oto_sort > '". $target_r['oto_sort'] ."' ");


    switch ($pass_depth) {
        case '1':

            $max_sort = ($target_r['oto_sort'] + 1);

            // 항목추가 - 1차
            _MQ_noreturn("
                insert odtProductOption set
                    oto_pcode='{$pass_code}',
                    oto_poptionname='',
                    oto_depth='1',
                    oto_sort='". $max_sort ."'
            ");
            $uid_1depth = mysql_insert_id();

            // 순번추출 - 2차
            $r2 = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='2' and oto_parent='" . $uid_1depth . "' ");
            $max_sort2 = $r2['max_sort'] + 1;

            // 항목추가 - 2차
            _MQ_noreturn("
                insert odtProductOption set
                    oto_pcode='{$pass_code}',
                    oto_poptionname='',
                    oto_depth='2',
                    oto_parent='{$uid_1depth}',
                    oto_sort='". $max_sort2 ."'
            ");
            $uid_2depth = mysql_insert_id();

            // 순번추출 - 3차
            $r3 = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='3' and find_in_set('" . $uid_2depth . "' , oto_parent) > 0 ");
            $max_sort3 = $r3['max_sort'] + 1;

            // 항목추가 - 3차
            _MQ_noreturn("
                insert odtProductOption set
                    oto_pcode='{$pass_code}',
                    oto_poptionname='',
                    oto_depth='3',
                    oto_parent='{$uid_1depth},{$uid_2depth}',
                    oto_sort='". $max_sort3 ."'
            ");
        break;
        case '2':

            $max_sort = ($target_r['oto_sort'] + 1);

            // 항목추가 - 2차
            mysql_query("
                insert odtProductOption set
                    oto_pcode='{$pass_code}',
                    oto_poptionname='',
                    oto_depth='2',
                    oto_parent='{$target_r['oto_parent']}',
                    oto_sort='". $max_sort ."'
            ");
            $uid_2depth = mysql_insert_id($connect);

            // 순번추출 - 3차
            $r3 = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='3' and find_in_set('" . $uid_2depth . "' , oto_parent) > 0 ");
            $max_sort3 = $r3['max_sort'] + 1;

            // 항목추가 - 3차
            mysql_query("
                insert odtProductOption set
                    oto_pcode='{$pass_code}',
                    oto_poptionname='',
                    oto_depth='3',
                    oto_parent='{$target_r['oto_parent']},{$uid_2depth}',
                    oto_sort='". $max_sort3 ."'
            ");
        break;
        case '3':

            $max_sort = ($target_r['oto_sort'] + 1);

            // 항목추가 - 3차
            mysql_query("
                insert odtProductOption set
                    oto_pcode='{$pass_code}',
                    oto_poptionname='',
                    oto_depth='3',
                    oto_parent='{$target_r['oto_parent']}',
                    oto_sort='". $max_sort ."'
            ");
        break;
    }

    // 항목추가 - 2차
    //_MQ_noreturn(" insert odtProductOption set oto_pcode='" . $pass_code . "', oto_poptionname='', oto_depth='". $pass_depth ."', oto_parent='" . $target_r['oto_parent'] . "', oto_sort='". ($target_r['oto_sort'] + 1) ."' ");
}




############### 옵션 SORTing ############### 
// pass_type - U : up , D :down
// pass_depth - 1, 2, 3 
// pass_uid  -옵션 고유번호
else if( in_array($pass_type , array("U" , "D")) && $pass_depth && $pass_uid ) {

    // 타겟 데이터 정보 추출
    $target_r = _MQ(" select * from odtProductOption where oto_uid='" . $pass_uid ."' ");

    // max sort 추출
    $maxr = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='". $pass_depth ."' ". ($target_r['oto_parent'] ? " and oto_parent = '". $target_r['oto_parent'] ."' " : "") );

    // 0보다 크고 max 보다 적어야 함.
    if( $target_r[oto_sort] > 0 && $target_r['oto_sort'] <= $maxr['max_sort'] ) {

        // 타켓 데이터 정보 변경
        _MQ_noreturn(" update odtProductOption set ". ( $pass_type == "U" ? "oto_sort=oto_sort-1" : "oto_sort=oto_sort+1") ." where oto_uid='" . $pass_uid ."' ");


        // 순위 바꿀 데이터 정보 추출 및 변경
        if( $pass_type == "U" ) {
            $change_r = _MQ(" select * from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='". $pass_depth ."' ". ($target_r['oto_parent'] ? " and oto_parent = '". $target_r['oto_parent'] ."' " : "") . " and oto_uid != '" . $pass_uid ."' and oto_sort < '". $target_r['oto_sort'] ."' order by oto_sort desc , oto_uid desc limit 1 ");
            _MQ_noreturn(" update odtProductOption set oto_sort = oto_sort + 1 where oto_uid='" . $change_r[oto_uid] ."' ");
        }
        else {
            $change_r = _MQ(" select * from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='". $pass_depth ."' ". ($target_r['oto_parent'] ? " and oto_parent = '". $target_r['oto_parent'] ."' " : "") . " and oto_uid != '" . $pass_uid ."' and oto_sort > '". $target_r['oto_sort'] ."' order by oto_sort asc , oto_uid asc limit 1 ");
            _MQ_noreturn(" update odtProductOption set oto_sort = oto_sort - 1 where oto_uid='" . $change_r[oto_uid] ."' ");
        }
    }

}



# 2015-11-18 실시간 저장을 위하여 정보를 한번씩 더 저장 한다.
if( sizeof($oto_info) > 0 ) {

    foreach( $oto_info as $k=>$v ){
        $que = "
            update odtProductOption set 
                oto_poptionname     ='".mysql_real_escape_string(trim($v['oto_poptionname']))."',
                oto_poptionprice    ='".$v['oto_poptionprice']."',
                oto_poptionpurprice ='".$v['oto_poptionpurprice']."',
                oto_cnt             ='".$v['oto_cnt']."',
                oto_view            = '".$v['oto_view']."'
            where oto_uid='{$k}'
        ";
        _MQ_noreturn($que);
    }
}

//echo "<script>alert('저장하였습니다.');parent.category_apply();</script>";
exit;