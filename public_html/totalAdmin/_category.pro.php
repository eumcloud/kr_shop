<?PHP

	include_once("inc.php");


	echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="kr" lang="kr" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>통합관리자 페이지</title>
	<link href="./css/adm_style.css" rel="stylesheet" type="text/css" />
	<!--<SCRIPT src="/include/js/jquery-1.7.1.min.js"></SCRIPT>-->
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<SCRIPT src="/include/js/jquery/jquery.validate.js"></SCRIPT>
	<SCRIPT src="/include/js/default.js"></SCRIPT>
	<SCRIPT src="/include/js/smart_script.js"></SCRIPT>
    <script type="text/javascript" src="_category.js"></script>
	<style>body {margin:0; padding:0; background:#fff; min-width:10px; height:90%;}</style>
</head>
<body>
	<input type="hidden" name="check_trigger" value="1"/>
		<!-- 내용 -->
		<div class="content_section">
			<div class="content_section_fix">
	';



//-----------------------------------------------------------------------------
// 1차 카테고리
//-----------------------------------------------------------------------------
if ("1" == $depth) {

    echo "
	<div class='content_section_inner'>
		<table class='list_TB' summary='리스트기본'>
			<colgroup><col width='*'/><col width='120px'/></colgroup>
			<tbody>
	";

    $res = _MQ_assoc(" select * from odtCategory where catedepth=1 order by cateidx asc , catecode asc");
    foreach($res as $k=>$r){

        echo "
			<tr>
				<td 
					onClick=\"
						parent.list2.location.href='_category.pro.php?depth=2&catecode=".$r[catecode]."'; 
						parent.list3.location.href='_category.pro.php?depth=3&catecode=0';
						parent.list4.location.href='_category.pro.php?depth=4&catecode=0';
						parent.PUBLIC_FORM.chk_list2.value='".$r[catecode]."';
						parent.PUBLIC_FORM.chk_list3.value='';
						parent.PUBLIC_FORM.chk_list4.value='';
						\$('.app_tr').css('background','#fff');
						\$(this).css('background','#cfcfcf');
					\"
					class='app_tr'
					style='cursor:pointer;text-align:left;'
				>" . $r[catename]."</td>
				<td>
					<div class='btn_line_up_center'>
						<span class='shop_btn_pack'><input type='button' name='' class='input_small gray f_vup' value='△' data-serialnum='".$r[serialnum]."' data-framename='list1' onClick='return false' alt='상위로 이동'></span>
						<span class='shop_btn_pack'><span class='blank_1'></span></span>
						<span class='shop_btn_pack'><input type='button' name='' class='input_small gray f_vdown' value='▽' data-serialnum='".$r[serialnum]."' data-framename='list1' onClick='return false' alt='하위로 이동'></span>
						<span class='shop_btn_pack'><span class='blank_1'></span></span>
						<span class='shop_btn_pack'><input type='button' name='' class='input_small blue' value='수정' onClick=\"f_add('1', '$r[serialnum]', 'list1');\"></span>
					</div>
				</td>
			</tr>
		";
    }
    echo "</tbody></table></div>";
}

//-----------------------------------------------------------------------------
// 2차 코드목록
//-----------------------------------------------------------------------------
if ("2" == $depth) {
    $catecode = trim($catecode);

    echo "
	<div class='content_section_inner'>
		<table class='list_TB' summary='리스트기본'>
			<colgroup><col width='*'/><col width='120px'/></colgroup>
			<tbody>
	";

    $res = _MQ_assoc(" select * from odtCategory where find_in_set('$catecode',parent_catecode) > 0 and catedepth=2 order by cateidx asc , catecode asc");
    foreach($res as $k=>$r){

        echo "
			<tr>
				<td
					onClick=\"
						parent.list3.location.href='_category.pro.php?depth=3&catecode=$r[catecode]';
						parent.list4.location.href='_category.pro.php?depth=4&catecode=0';
						parent.PUBLIC_FORM.chk_list3.value='$r[catecode]';
						parent.PUBLIC_FORM.chk_list4.value='';
						\$('.app_tr').css('background','#fff');
						\$(this).css('background','#cfcfcf');
					\"
					class='app_tr'
					style='cursor:pointer;text-align:left;'
				>" . $r[catename] . "</td>
				<td>
					<div class='btn_line_up_center'>
						<span class='shop_btn_pack'><input type='button' name='' class='input_small gray f_vup' value='△' data-serialnum='".$r[serialnum]."' data-framename='list2' onClick='return false' alt='상위로 이동'></span>
						<span class='shop_btn_pack'><span class='blank_1'></span></span>
						<span class='shop_btn_pack'><input type='button' name='' class='input_small gray f_vdown' value='▽' data-serialnum='".$r[serialnum]."' data-framename='list2' onClick='return false' alt='하위로 이동'></span>
						<span class='shop_btn_pack'><span class='blank_1'></span></span>
						<span class='shop_btn_pack'><input type='button' name='' class='input_small blue' value='수정' onClick=\"f_add('2', '$r[serialnum]', 'list2');\"></span>
					</div>
				</td>
			</tr>
		";
    }

    echo "</tbody></table></div>";
}


//-----------------------------------------------------------------------------
// 3차 코드목록
//-----------------------------------------------------------------------------
if ("3" == $depth) {
    $catecode = trim($catecode);

    if($catecode <> 0) {

		echo "
		<div class='content_section_inner'>
			<table class='list_TB' summary='리스트기본'>
				<colgroup><col width='*'/><col width='120px'/></colgroup>
				<tbody>
		";

        $res = _MQ_assoc(" select * from odtCategory where find_in_set('$catecode',parent_catecode) > 0 and catedepth=3 order by cateidx asc , catecode asc");
		foreach($res as $k=>$r){

            echo "
				<tr>
					<td style='text-align:left;'>" . $r[catename] . "</td>
					<td>
						<div class='btn_line_up_center'>
							<span class='shop_btn_pack'><input type='button' name='' class='input_small gray f_vup' value='△' data-serialnum='".$r[serialnum]."' data-framename='list3' onClick='return false' alt='상위로 이동'></span>
							<span class='shop_btn_pack'><span class='blank_1'></span></span>
							<span class='shop_btn_pack'><input type='button' name='' class='input_small gray f_vdown' value='▽' data-serialnum='".$r[serialnum]."' data-framename='list3' onClick='return false' alt='하위로 이동'></span>
							<span class='shop_btn_pack'><span class='blank_1'></span></span>
							<span class='shop_btn_pack'><input type='button' name='' class='input_small blue' value='수정' onClick=\"f_add('3', '$r[serialnum]', 'list3');\"></span>
						</div>
					</td>
				</tr>
            ";
        }
        echo "</tbody></table></div>";
    }
}

?>
<script>
	$('.f_vup').on('click',function(){
		var serialnum = $(this).data('serialnum'), framename = $(this).data('framename');
		$.post( "/totalAdmin/_category.ajax.php", { serialnum: serialnum, framename: framename, status: 'view_up' } ); 
		var thisRow = $(this).closest('tr');
		var prevRow = thisRow.prev();
		if (prevRow.length) { prevRow.before(thisRow); } else { alert('더이상 상위로 이동할 수 없습니다.'); }
	});
	$('.f_vdown').on('click',function(){
		var serialnum = $(this).data('serialnum'), framename = $(this).data('framename');
		$.post( "/totalAdmin/_category.ajax.php", { serialnum: serialnum, framename: framename, status: 'view_down' } ); 
		var thisRow = $(this).closest('tr');
		var nextRow = thisRow.next();
		if (nextRow.length) { nextRow.after(thisRow); } else { alert('더이상 하위로 이동할 수 없습니다.'); }
	});
</script>
<?

// 순서변경 기능 _category.ajax.php 로 이동 // 2014-09-26




//-----------------------------------------------------------------------------
// 메뉴 추가
//-----------------------------------------------------------------------------
if ("menu_add" == $status) {

    $catedepth = trim($catedepth);
    $parent_catecode = trim($parent_catecode);

	$subMode = $serialnum ? "edt" : "ins";
	if($subMode == "edt") {
		$row = _MQ("select * from odtCategory where serialnum = '".$serialnum ."'");
	}
    else {
		$sr = _MQ("select ifnull(max(catecode),0) as max_catecode from odtCategory");
		$row[catecode] = $sr[max_catecode] * 1 + 1;

        // 순위정하기
        if($parent_catecode) {
            $sque = " where find_in_set('${parent_catecode}',parent_catecode) > 0 and catedepth='${catedepth}' ";
        }
        else{
            $sque = " where catedepth='${catedepth}' ";
        }

		$s2r = _MQ("select ifnull(max(cateidx),0) as max_cateidx from odtCategory $sque ");
        $row[cateidx] = $s2r[max_cateidx] + 1;

	}


    echo "
<form name='PUBLIC_FORM' method='post' action='$PHP_SELF'  enctype='multipart/form-data'>
<input type='hidden' name='status' value='menu_add_tran' />
<input type='hidden' name='catedepth'    value='$catedepth' />
<input type='hidden' name='catecode'    value='$row[catecode]' />
<input type='hidden' name='cateidx'    value='$row[cateidx]' />
<input type='hidden' name='subMode'    value='$subMode' />
<input type='hidden' name='serialnum'    value='$_GET[serialnum]' />
<input type='hidden' name='framename'    value='$_GET[framename]' />

	<!-- 검색영역 -->
	<div class='form_box_area'>

		<table class='form_TB' summary='검색항목'>
			<colgroup><col width='100px'/><col width='*'/></colgroup>
			<tbody> 
    ";

    if( $catedepth > 1 ) {

        // 부모 카테고리 불러오기
        $sque = " select * from odtCategory where catecode='${parent_catecode}' and catedepth='".($catedepth - 1)."'  ";
        $sr=_MQ($sque);
		$ex = explode("," , $sr[parent_catecode]);
		$ex[] = $sr[catecode];
		$app_parent_catecode = implode("," , array_filter(array_unique($ex)));

        echo "
			<tr>
				<td class='article'>부모카테고리</td>
				<td class='conts'>$sr[catename]<input type='hidden' name='parent_catecode' value='{$app_parent_catecode}' /></td>
			</tr>
        ";
    }
    else {
        //최상위의 경우 parent_catecode는 0
        echo "<input type='hidden' name='parent_catecode' value='0' />";
    }



    if($row[cHidden] == "no") {
        $cHidden_select1 = "selected";
        $cHidden_select2 = "";
    }
    else {
        $cHidden_select1 = "";
        $cHidden_select2 = "selected";
    }

    echo "
		<tr>
			<td class='article'>카테고리명</td>
			<td class='conts'><input type='text' name='catename' class='input_text' style='width:150px;' value=\"" . $row[catename] . "\" /></td>
		</tr>
		<tr>
			<td class='article'>영문 카테고리명<br>(모바일전용)</td>
			<td class='conts'><input type='text' name='catename_eng' class='input_text' style='width:150px;' value=\"" . $row[catename_eng] . "\" /></td>
		</tr>		
		<tr>
			<td class='article'>노출여부</td>
			<td class='conts'>" . _InputSelect( "cHidden" , array("no" , "yes"), $row[cHidden] , "" , array("노출" , "숨김") , "") . "</td>
		</tr>
    ";

	/*
		. 1차카테고리
			- 타이틀이미지 : 미선택 , 선택 , 마우스오버 (cateimg_none , cateimg , cateimg_over)
			- display 형태 : 지역 , 쇼핑 , 여행/레져, 기획전 (subcate_display)
			- 1차 카테고리 탭 : display 형태가 지역일경우 콤마(,)구분 테마형태로 저장 (lineup)
		. 2차카테고리
			- 카테고리 메인 적용 여부 표시 (subcate_main)
			- 2차카테고리 탭 : 콤마(,)구분 테마형태로 저장 (lineup)
			- display 형태 : 지역 - 1차 카테고리 탭에 따른 탭 선택(subcate_display_choice)
			- display 형태 : 쇼핑, 여행/레져 - 카테고리 아이콘 이미지(cateimg) ==> 둘의 차이는 사용자페이지에서 목록 이미지 크기가 다름
			- display 형태 : 기획전 - 비주얼 이미지(cateimg)
	*/
    if( $catedepth == 2 ) {

		switch($sr[subcate_display]){

			case "지역":
				$ex = explode("," , $sr[lineup]);
				echo "
					<tr>
						<td class='article'>카테고리 메인 적용 여부</td>
						<td class='conts'>
							" . _InputSelect( "subcate_main" , array("N" , "Y"), $row[subcate_main] , "" , array("미적용" , "카테고리 메인적용") , "") . "
							" . _DescStr("카테고리메인으로 적용시 각 카테고리의 메인으로 적용됩니다.") . "
							" . _DescStr("1차카테고리 하위로 카테고리메인이 적용되지 않을 경우 카테고리 메인이 적용되지 않습니다.") . "
						</td>
					</tr>
					<tr>
						<td class='article'>묶음탭 선택</td>
						<td class='conts'>
							" . _InputSelect( "subcate_display_choice" , $ex, $row[subcate_display_choice] , "" , "" , "") . "
							" . _DescStr("2차 카테고리에서 선택된 카테고리는 묶음탭에 묶여 노출됩니다. 예) 강원/제주 => 강원, 제주 두 지역 묶음 표시") . "
						</td>
					</tr>
					<tr>
						<td class='article'>2차 카테고리 탭</td>
						<td class='conts'>
							<input type='text' name='lineup' class='input_text' style='width:300px;' value=\"" . $row[lineup] . "\" />
							" . _DescStr("콤마(,)로 구분하여 등록하며, 등록된 탭은 상품등록 시 선택됩니다.") . "
							" . _DescStr("등록된 탭은 사용자페이지에 탭형태로 노출됩니다.") . "
						</td>
					</tr>
				";
				break;
			case "쇼핑": 
			case "여행/레저": 
			case "문화": 
				echo "
					<tr>
						<td class='article'>카테고리 메인 적용 여부</td>
						<td class='conts'>
							" . _InputSelect( "subcate_main" , array("N" , "Y"), $row[subcate_main] , "" , array("미적용" , "카테고리 메인적용") , "") . "
							" . _DescStr("카테고리메인으로 적용시 각 카테고리의 메인으로 적용됩니다.") . "
							" . _DescStr("1차카테고리 하위로 카테고리메인이 적용되지 않을 경우 카테고리 메인이 적용되지 않습니다.") . "
						</td>
					</tr>
					<!--tr>
						<td class='article'>아이콘 이미지</td>
						<td class='conts'>" . _PhotoForm( "../upfiles/product" , "cateimg"  , $row[cateimg] ) . "</td>
					</tr-->
				";
				// 쇼핑 형태만 적용
				/*echo "
					<tr>
						<td class='article'>2차 카테고리 탭</td>
						<td class='conts'>
							<input type='text' name='lineup' class='input_text' style='width:300px;' value='" . $row[lineup] . "' />
							" . _DescStr("콤마(,)로 구분하여 등록하며, 등록된 탭은 상품등록 시 선택됩니다.") . "
							" . _DescStr("등록된 탭은 사용자페이지에 탭형태로 노출됩니다.") . "
						</td>
					</tr>
				";*/
				break;

			case "기획전":  // 배너로 대체함
				/*echo "
					<tr>
						<td class='article'>비주얼 이미지</td>
						<td class='conts'>" . _PhotoForm( "../upfiles/product" , "cateimg"  , $row[cateimg] ) . "</td>
					</tr>
				";*/
				break;

		}

    }

    //1차 카테고리
    else if( $catedepth == 1 ) {
        echo "
			<tr>
				<td class='article'>메뉴디스플레이형태</td>
				<td class='conts'>" . _InputSelect( "subcate_display" , array("지역", "기획전", "쇼핑"), $row[subcate_display] , "" , "" , "") . "</td>
			</tr>
			<tr>
				<td class='article'>PC상품목록형태</td>
				<td class='conts'>" . _InputSelect( "pc_list_display" , array_keys($arrProductListTypePc), $row[pc_list_display] , "" , array_values($arrProductListTypePc) , "") . "</td>
			</tr>
			<tr>
				<td class='article'>Mobile상품목록형태</td>
				<td class='conts'>" . _InputSelect( "mobile_list_display" , array_keys($arrProductListTypeMoblie), $row[mobile_list_display] , "" , array_values($arrProductListTypeMoblie) , "") . "</td>
			</tr>
			<tr>
				<td class='article'>묶음탭</td>
				<td class='conts'>
					<input type='text' name='lineup' class='input_text' style='width:300px;' value=\"" . $row[lineup] . "\" />
					" . _DescStr("<B>디스플레이형태가 <FONT COLOR='red'>지역</FONT>일 경우에 적용됩니다.</B>") . "
					" . _DescStr("콤마(,)로 구분하여 등록하며, 등록된 탭은 쇼핑형태의 2차카테고리에서 선택가능합니다.") . "
					" . _DescStr("2차 카테고리에서 선택된 카테고리는1차 묶음탭에 묶여 노출됩니다. 예) 강원/제주 => 강원, 제주 두 지역 묶음 표시") . "
				</td>
			</tr>
			<tr style='display:none;'>
				<td class='article'>카테고리img<br>기본/선택</td>
				<td class='conts'>" . _PhotoForm( "../upfiles/product" , "cateimg"  , $row[cateimg] ) . "</td>
			</tr>
			<tr style='display:none;'>
				<td class='article'>카테고리img<br>미선택</td>
				<td class='conts'>" . _PhotoForm( "../upfiles/product" , "cateimg_none"  , $row[cateimg_none] ) . "</td>
			</tr>
			<tr style='display:none;'>
				<td class='article'>카테고리img<br>마우스오버</td>
				<td class='conts'>" . _PhotoForm( "../upfiles/product" , "cateimg_over"  , $row[cateimg_over] ) . "</td>
			</tr>
			<tr style='display:none;'>
				<td class='article'>카테고리img<br>모바일용</td>
				<td class='conts'>" . _PhotoForm( "../upfiles/product" , "cateimg_mobile"  , $row[cateimg_mobile] ) . "</td>
			</tr>
        ";
    }


    echo "
				</tbody> 
			</table>
	</div>

	<!-- 버튼영역 -->
	<div class='bottom_btn_area'>
		<div class='btn_line_up_center'>
			<span class='shop_btn_pack'>
				<input type='button' name='' class='input_large blue' value='저장' onClick='f_add_Save(\"".$row_admin[id]."\")'>
				<input type='button' name='' class='input_large red' value='삭제' onClick='f_add_Del()'>
				<input type='button' name='' class='input_large gray' value='닫기' onClick='self.close();'>
			</span>
		</div>
	</div>
	<!-- 버튼영역 -->

</form>
    ";
    exit;
}



//-----------------------------------------------------------------------------
// 메뉴추가 처리
//-----------------------------------------------------------------------------
if ("menu_add_tran" == $status) {

    $dir            ="../upfiles/product";// 파일 업로드 위치

    if( in_array($subMode , array("ins" , "edt")) ) {

		$cateimg_name			= _PhotoPro( $dir , "cateimg" ) ;
		$cateimg_over_name		= _PhotoPro( $dir , "cateimg_over" ) ;
		$cateimg_none_name		= _PhotoPro( $dir , "cateimg_none" ) ;
		$cateimg_mobile_name	= _PhotoPro( $dir , "cateimg_mobile" ) ;

        if( in_array($subMode , array("ins")) ) {

            // 등록전에 해당 catecode로 등록정보가 있는지 체킹함
			$sr = _MQ("select count(*) as cnt from odtCategory where catecode='${catecode}'");
            if( $sr[cnt] > 0 ) {
				error_msgPopup_s("동시에 등록된 다른정보가 있으니 다시한번 등록하시기 바랍니다.");
            }

        }

        $sque = "
				catename				=	'".$catename."'
				,catename_eng			=	'".$catename_eng."'
				,catecode				=	'".$catecode."'
				,cateidx				=	'".$cateidx."'
				,cHidden				=	'".$cHidden."'
				,catedepth				=   '".$catedepth."'
				,parent_catecode		=   '".$parent_catecode."'
				,cateimg				=   '".$cateimg_name."'
				,cateimg_over			=   '".$cateimg_over_name."'
				,cateimg_none			=   '".$cateimg_none_name ."'
				,cateimg_mobile			=   '".$cateimg_mobile_name ."'
				,subcate_display		=   '".$subcate_display."'
				,pc_list_display		=   '".$pc_list_display."'
				,mobile_list_display	=   '".$mobile_list_display."'
				,subcate_display_choice	=   '".$subcate_display_choice."'
				,lineup					=   '".$lineup."'
				,subcate_main			= '".$subcate_main."'
        ";
    }


	switch( $subMode ){

        // 등록
		case "ins" :
			_MQ_noreturn(" insert odtCategory set $sque ");
			break;



        // 수정
		case "edt" :
			_MQ_noreturn("update odtCategory set $sque where serialnum = '".$serialnum."'");
			break;


        // 삭제
		case "del" :

            // 해당 카테고리를 부모로 가진 카테고리 있을 경우 삭제 불가
            $sr = _MQ("select count(*) as cnt from odtCategory where find_in_set('${catecode}' , parent_catecode) > 0 ");
            if( $sr[cnt] > 0 ) {
				error_msgPopup_s("하위 카테고리가 있어 삭제할 수 없습니다.");
            }

            // 해당 상품있을 경우 삭제불가
            $r = _MQ(" 
				select count(*) as cnt
				from odtProductCategory as pct 
				inner join odtCategory as c on (c.catecode=pct.pct_cuid) 
				inner join odtProduct as p on (pct.pct_pcode=p.code) 
				where c.serialnum = '".$serialnum."' 
			");
            if($r[cnt] > 0 ) {
				error_msgPopup_s("상품이 등록되어있는 지역은 삭제할 수 없습니다.\\n\\n상품을 먼저 삭제하세요.");
            }
            else {
				$r = _MQ(" select * from odtCategory where serialnum = '".$serialnum."'  ");
				$cateimg		= $r[cateimg];
				$cateimg_over	= $r[cateimg_over];
				$cateimg_none	= $r[cateimg_none];
				$cateimg_mobile	= $r[cateimg_mobile];

                if($cateimg) 		{ _PhotoDel( $dir , $cateimg ) ; }//이미지 삭제
                if($cateimg_over) 	{ _PhotoDel( $dir , $cateimg_over ) ; }//이미지 삭제
                if($cateimg_none) 	{ _PhotoDel( $dir , $cateimg_none ) ; }//이미지 삭제
                if($cateimg_mobile) { _PhotoDel( $dir , $cateimg_mobile ) ; }//이미지 삭제
                _MQ_noreturn("delete from odtCategory where serialnum = '".$serialnum."'");//데이터 삭제
            }

            break;

    }


    if($framename &&  $_POST[subMode] == "ins") {
        echo "<script>opener.${framename}.location.reload(true);self.close();</script>";
        exit; 
    }
    else {
        echo "<script>opener.location.reload(true);self.close();</script>";
        exit;    
    }

}
?>