<?PHP

	include "inc.php";

	// 넘겨온 변수
    //$pass_parent01
    //$pass_parent02
	//$pass_parent03_no_required
    //$_idx



    // - 2단 분류 선택시 ---
    if(  $pass_parent02 && $pass_idx == 2 ){
        $que = "select catecode , catename from odtCategory where cHidden='no' and catedepth='3' and find_in_set('${pass_parent02}' , parent_catecode) > 0 order by cateidx asc";
    }
    // - 2단 분류 선택시 ---

    //  - 1단분류 ---
    else if( $pass_parent01 && $pass_idx == 1 ) {
        $que = "select catecode , catename from odtCategory where cHidden='no' and catedepth='2' and find_in_set('${pass_parent01}' , parent_catecode) > 0 order by cateidx asc";
	}
    //  - 1단분류 ---


    $res = mysql_query($que);
	$str = "";
	for( $i=0; $v = mysql_fetch_assoc($res); $i++){

		if($i <> 0) {
			$str .= ' , ';
		}
		$str .= '{"optionValue" : "' . $v[catecode] . '" , "optionDisplay" : "' . trim($v[catename]) . '"}';
		$cnt ++;
	}
	echo '[' . $str . ']';


    exit;
?>