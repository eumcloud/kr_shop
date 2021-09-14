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
function f_add(kbn, code1, code2)
{
    var rtn_cd = true;
    if (2 == kbn && !code1)
    {
        var code1 = document.PUBLIC_FORM.m2_code1.value;
    }

    window.open('_totadm_menu.pro.php?status=menu_add&KBN='+kbn+'&m2_code1='+code1+'&m2_code2='+code2,'메뉴추가','width=500,height=250,toolbar=no,scrollbars=no,top=0,left=0');
    return rtn_cd;
}


//-----------------------------------------------------------------------------
// 메뉴 추가
//-----------------------------------------------------------------------------
function f_add_Save()
{
    var rtn_cd = true;
    var kbn    = document.PUBLIC_FORM.KBN.value;
    if (1 == kbn)
    {
        if ("" == document.PUBLIC_FORM.m2_name1.value) 
        {
            alert("1차 메뉴를 등록하십시오.");
            document.PUBLIC_FORM.m2_name1.focus();
        }
        else
        {
            document.PUBLIC_FORM.status.value = "menu_add_tran";
            PUBLIC_FORM.submit();
        }
    }
    else if (2 == kbn)
    {
        if ("" == document.PUBLIC_FORM.m2_name2.value) 
        {
            alert("2차 메뉴를 등록하십시오.");
            document.PUBLIC_FORM.m2_name2.focus();
        }
        else
        {
            document.PUBLIC_FORM.status.value = "menu_add_tran";
            PUBLIC_FORM.submit();
        }
    }

    return rtn_cd;
}

//-----------------------------------------------------------------------------
// 메뉴 삭제
//-----------------------------------------------------------------------------
function f_add_Del()
{
    chkDel = confirm('메뉴를 삭제하시겠습니까?\n\n하위 메뉴가 있을 경우 같이 삭제되니 주의 바랍니다.');
    if(true == chkDel) 
    {
        document.PUBLIC_FORM.status.value = "menu_del_tran";
        PUBLIC_FORM.submit();
    }
}

//-----------------------------------------------------------------------------
// 표시순서 상위로 이동
//-----------------------------------------------------------------------------
function f_vup(code1, code2)
{
    parent.set.location.href='_totadm_menu.pro.php?status=view_up&code1='+code1+'&code2='+code2;
}

//-----------------------------------------------------------------------------
// 표시순서 하위로 이동
//-----------------------------------------------------------------------------
function f_vdown(code1, code2)
{
    parent.set.location.href='_totadm_menu.pro.php?status=view_down&code1='+code1+'&code2='+code2;
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
        set.location.href='_totadm_menu.pro.php?status=menu_save&code1='+code1+'&code2='+code2+'&m2_link='+encodeURIComponent(link)+'&m2_vkbn='+vkbn;
    }
    else
    {
        alert("메뉴를 선택하세요.");
    }
}

//-----------------------------------------------------------------------------
// 메뉴 선택시 해당 메뉴의 정보를 추출
//-----------------------------------------------------------------------------
function f_change_set()
{
    var code1 = parent.document.PUBLIC_FORM.m2_code1.value;
    var code2 = parent.document.PUBLIC_FORM.m2_code2.value;

    parent.set.location.href='_totadm_menu.pro.php?status=menu_set&code1='+code1+'&code2='+code2;
}