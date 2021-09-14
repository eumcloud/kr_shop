<?

include_once("inc.php");

//-----------------------------------------------------------------------------
// 상위로 순서변경
//-----------------------------------------------------------------------------
if ("view_up" == $status) {

    // 정보 불러오기
    $Result  = _MQ(" SELECT cateidx , catedepth , parent_catecode FROM odtCategory WHERE serialnum = '$serialnum' ");
    $cateidx = $Result[cateidx];
    $catedepth = $Result[catedepth];
    $parent_catecode = $Result[parent_catecode];

    // 최소 순위  찾기 //////////////////////////////////////////
    $Result  = _MQ(" SELECT ifnull(MIN(cateidx),0) as min_cateidx FROM odtCategory WHERE parent_catecode='$parent_catecode' ");
    $mincateidx = $Result[min_cateidx];

    if ($mincateidx == $cateidx) {
		error_alt("더 이상 상위로 변경할 수 없습니다.");
    }
    else {

        // 바로 한단계위 데이터와 cateidx 값 바꿈
        $sr = _MQ("select cateidx , serialnum from odtCategory WHERE parent_catecode='$parent_catecode' and cateidx < '$cateidx' order by cateidx desc limit 1");

        _MQ_noreturn(" UPDATE odtCategory SET cateidx = $cateidx WHERE serialnum='$sr[serialnum]'");

        // 순서값 제거 - 자신의 순서값
        _MQ_noreturn(" UPDATE odtCategory SET cateidx = $sr[cateidx] WHERE serialnum = '$serialnum' ");

    }

    //echo "<script>parent.${framename}.location.reload(true);</script>";
    exit;
}

//-----------------------------------------------------------------------------
// 하위로 순서변경
//-----------------------------------------------------------------------------
if ("view_down" == $status) {

    // 정보 불러오기
    $Result  = _MQ(" SELECT cateidx , catedepth , parent_catecode FROM odtCategory WHERE serialnum = '$serialnum' ");
    $cateidx = $Result[cateidx];
    $catedepth = $Result[catedepth];
    $parent_catecode = $Result[parent_catecode];

    // 최소 순위  찾기 //////////////////////////////////////////
    $Result  = _MQ(" SELECT ifnull(MAX(cateidx),0) as max_cateidx FROM odtCategory WHERE parent_catecode='$parent_catecode' ");
    $maxcateidx = $Result[max_cateidx];

    if ($maxcateidxmaxcateidx == $cateidx) {
		error_alt("더 이상 하위로 변경할 수 없습니다.");
    }
    else {

        // 바로 한단계 아래 데이터와 cateidx 값 바꿈
        $sr = _MQ("select cateidx , serialnum from odtCategory WHERE parent_catecode='$parent_catecode' and cateidx > '$cateidx' order by cateidx asc limit 1");

        _MQ_noreturn(" UPDATE odtCategory SET cateidx = $cateidx WHERE serialnum='$sr[serialnum]'");

        // 순서값 제거 - 자신의 순서값
        _MQ_noreturn(" UPDATE odtCategory SET cateidx = $sr[cateidx] WHERE serialnum = '$serialnum' ");

    }

    //echo "<script>parent.${framename}.location.reload(true);</script>";
    exit;

}


?>