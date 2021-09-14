/******************************************************************************
* 파 일 명: admmenu_form.js
* 작업내용: 관리자메뉴 관리 스크립트함수
* 인    수: 
* 작성일자: 2011.02.23
* 작 성 자: 원데이넷
 ******************************************************************************/

//-----------------------------------------------------------------------------
// 테이블에 속한 tr 색상 변경
//-----------------------------------------------------------------------------
function f_row(table)
{ 
    var Tbl = document.getElementById(table); 

    for(i = 0; i < Tbl.rows.length; i++)
    { 
        Tbl.rows[i].style.background = "#ffffec"; 
    }
} 

//-----------------------------------------------------------------------------
// 메뉴 추가 새창화면 띄우기
//-----------------------------------------------------------------------------
function f_add(catedepth,serialnum,framename)
{
    var rtn_cd = true;
    var parent_catecode = "";

    if( catedepth == 2 ) {
        parent_catecode = parent.document.PUBLIC_FORM.chk_list2.value;
        if( !parent_catecode ) {
            alert('1차 카테고리를 선택하세요.');
            return false;
        }
    }
    else if( catedepth == 3 ) {
        parent_catecode = parent.document.PUBLIC_FORM.chk_list3.value;
        if( !parent_catecode ) {
            alert('2차 카테고리를 선택하세요.');
            return false;
        }
    }

    window.open('_category.pro.php?status=menu_add&catedepth=' + catedepth + '&parent_catecode=' + parent_catecode + '&serialnum=' + serialnum + '&framename=' + framename,'메뉴추가','width=500,height=400,toolbar=no,scrollbars=no,top=0,left=0');
    return rtn_cd;
}


//-----------------------------------------------------------------------------
// 메뉴 추가
//-----------------------------------------------------------------------------
function f_add_Save(id)
{
    var rtn_cd = true;
    var catedepth    = document.PUBLIC_FORM.catedepth.value;
    

    if (1 != catedepth)
    {
        if ("" == document.PUBLIC_FORM.parent_catecode.value) 
        {
            alert("부모 카테고리를 선택하세요.");
            document.PUBLIC_FORM.parent_catecode.focus();
        }
    }

    if ("" == document.PUBLIC_FORM.catename.value) 
    {
        alert("카테고리명을 입력하세요.");
        document.PUBLIC_FORM.catename.focus();
    }
    else
    {
        document.PUBLIC_FORM.status.value = "menu_add_tran";
        PUBLIC_FORM.submit();
    }

    return rtn_cd;
}

//-----------------------------------------------------------------------------
// 메뉴 삭제
//-----------------------------------------------------------------------------
function f_add_Del()
{
    chkDel = confirm('메뉴를 삭제하시겠습니까?');
    if(true == chkDel) 
    {
        document.PUBLIC_FORM.status.value = "menu_add_tran";
        document.PUBLIC_FORM.subMode.value = "del";
        PUBLIC_FORM.submit();
    }
}

//-----------------------------------------------------------------------------
// 표시순서 상위로 이동
//-----------------------------------------------------------------------------
function f_vup(serialnum , framename)
{
    parent.set.location.href='_category.pro.php?status=view_up&serialnum='+serialnum + '&framename=' + framename;
}

//-----------------------------------------------------------------------------
// 표시순서 하위로 이동
//-----------------------------------------------------------------------------
function f_vdown(serialnum , framename)
{
    parent.set.location.href='_category.pro.php?status=view_down&serialnum='+serialnum + '&framename=' + framename;
}

//-----------------------------------------------------------------------------
// 메뉴 정보 저장
//-----------------------------------------------------------------------------
function f_save()
{
    var code1 = document.PUBLIC_FORM.m2_code1.value;
    var code2 = document.PUBLIC_FORM.m2_code2.value;
    var link  = document.PUBLIC_FORM.m2_link.value;

    if (document.PUBLIC_FORM.m2_vkbn[0].checked == true)
    {
        var vkbn = "y";
    }
    else
    {
        var vkbn = "n";
    }

    if (code1 || code2)
    {
        set.location.href='_category.pro.php?status=menu_save&code1='+code1+'&code2='+code2+'&m2_link='+link+'&m2_vkbn='+vkbn;
    }
    else
    {
        alert("메뉴를 선택하세요");
    }
}

//-----------------------------------------------------------------------------
// 메뉴 선택시 해당 메뉴의 정보를 추출
//-----------------------------------------------------------------------------
function f_change_set()
{
    var code1 = parent.document.PUBLIC_FORM.m2_code1.value;
    var code2 = parent.document.PUBLIC_FORM.m2_code2.value;

    parent.set.location.href='_category.pro.php?status=menu_set&code1='+code1+'&code2='+code2;
}



//-----------------------------------------------------------------------------
// 4차 메뉴 클릭시 해당 상품수정 페이지로 이동
//-----------------------------------------------------------------------------
function new_wingo(val)
{
    window.open('/odprogram/odmanager/odproducts/od_input_coupon.php?code='+val, '_new', 'scrollbars=yes, resizable=yes');
}



