var selfID;
var req = null;

function create_request() {
    var request = null;
    try {
        request = new XMLHttpRequest();
    } catch (trymicrosoft) {
        try {
            request = new ActiveXObject("Msxml12.XMLHTTP");
        } catch (othermicrosoft) {
            try {
                request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (failed) {
                request = null;
            }
        }
    }
    if (request == null)
        alert("Error creating request object!");
    else
        return request;
}


function strreplace(obj) {
    obj.value = obj.value.replace(/-/g,"");
}

function showInfo(id,name,divID) {

    selfID = divID;

    // 백그라운드로 DB 추출.
    param = "id="+id+"&name="+name+"&selfid="+selfID+"_1";
    req = create_request();
    req.open("POST", "/showUserInfo.php", true);
    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=UTF-8");
    req.setRequestHeader("Cache-Control","no-cache, must-revalidate");
    req.setRequestHeader("Pragma","no-cache");
    req.send(param);
    req.onreadystatechange = function () {
        printHTML();
    }
    document.getElementById(divID).style.display='';
    document.getElementById(divID+"_1").style.display='';
}

function printHTML() {
    if (req.readyState == 4) {
        if(req.status == 200) {
            // 값이 있을때만 처리
                resultText = req.responseText;
                document.getElementById(selfID+"_1").innerHTML = resultText;
        }
    }
}

function f_check(id, old, value)
{
    if (old < value)
    {
        alert('쿠폰수량은 구매건수보다 클 수 없습니다.\n\n 다시 확인해 주십시오.');
    }
    else
    {
        hf.location.href='od_orderslist.php?status=coupon_check&id='+id+'&value='+value;
    }
}

function f_check2(pagename,uid , type)
{
    common_frame.location.href= pagename + '?status=coupon_check&uid='+uid+'&type='+type;
}

function f_num_press()
{
    var rtn_cd = true ;
    if( 48 > event.keyCode || 57 < event.keyCode )
    {
        rtn_cd = false ;
    }
    event.returnValue = rtn_cd;
}
//onKeyPress() 이벤트에서 호출(완전한숫자만입력)
function f_num_down()
{
    var rtn_cd = true ;
    if( 229 == event.keyCode )
    {
        rtn_cd = false;
    }
    event.returnValue = rtn_cd;
}


// 회원상세정보 출럭 Ajax 끝


            function set_date(opt1,opt2) {
                today = new Date();
                now_year = today.getYear();
                now_month = today.getMonth()+1;
                now_day = today.getDate();
                month_temp = now_month-1;
                switch(month_temp) {
                    case(1): day_temp=31;   break;
                    case(2): day_temp=28;   break;
                    case(3): day_temp=31;   break;
                    case(4): day_temp=30;   break;
                    case(5): day_temp=31;   break;
                    case(6): day_temp=30;   break;
                    case(7): day_temp=31;   break;
                    case(8): day_temp=31;   break;
                    case(9): day_temp=30;   break;
                    case(10): day_temp=31; break;
                    case(11): day_temp=30; break;
                    case(12): day_temp=31; break;
                    default: day_temp=31; break;
                }
                the_day = now_day;
                the_month = now_month;
                the_year = now_year;
                if(opt1 == 'd') {
                    opt_day = now_day-opt2;
                    if(opt_day > 0) {
                        the_day = opt_day;
                    }else{
                        opt_month = now_month-1;
                        the_day = day_temp+opt_day;
                        if(opt_month > 0) {
                            the_month = opt_month;
                        }else{
                            the_year = now_year-1;
                            the_month = 12;
                        }
                    }
                }else if(opt1 == 'm') {
                    opt_month = now_month-opt2;
                    if(opt_month > 0) {
                        the_month = opt_month;
                    }else{
                        the_year = now_year-1;
                        the_month = 12+opt_month;
                    }
                }else if(opt1 == 'y') {
                    the_year = now_year-opt2;
                }
                if(the_month < 10) the_month = '0'+the_month;
                if(the_day < 10) the_day = '0'+the_day;
                the_date = the_year+''+the_month+''+the_day;
                document.view.start_date.value = the_date;
                if(now_month < 10) now_month = '0'+now_month;
                if(now_day < 10) now_day = '0'+now_day;
                now_date = now_year+''+now_month+''+now_day;
                document.view.end_date.value = now_date;
                if(opt1 == 'w') {
                    document.view.start_date.value = '';
                    document.view.end_date.value = '';
                }
            }
            function view_submit() {
                if(document.view.start_date.value) {
                    if(!IsNumber(document.view.start_date)) {
                        alert('조회기간에는 숫자만 입력할 수 있습니다.  ');
                        document.view.start_date.focus();
                        document.view.start_date.select();
                        return false;
                    }
                }
                if(document.view.end_date.value) {
                    if(!IsNumber(document.view.end_date)) {
                        alert('조회기간에는 숫자만 입력할 수 있습니다.   ');
                        document.view.end_date.focus();
                        document.view.end_date.select();
                        return false;
                    }
                }
                document.view.submit();
            }
            function IsNumber(formname) {
                var formstr = eval(formname);
                for(var i=0 ; i<formstr.value.length ; i++) {
                    var chr=formstr.value.substr(i,1);
                    if((chr<'0'||chr>'9') && chr!='-' && chr!='_') return false;
                }
                return true;
            }

            function selectCheck(form) {
                var check_nums = document.OderAllDelete.elements.length;
                for (var i = 0; i < check_nums;  i++) {
                    var checkbox_obj = eval("document.OderAllDelete.elements[" + i + "]");
                    if (checkbox_obj.checked == true) {
                        break;
                    }
                }
                if(i == check_nums) {
                    alert ("먼저 삭제하고자 하는 주문 항목을 선택하세요.   ");
                    return;
                }else {
                    document.OderAllDelete.submit();
                }
            }
            function selectAll() {
                var form = document.OderAllDelete;
                for (var i=0;i<form.elements.length;i++) {
                    obj_str = eval(form.elements[i]);
                    obj_str.checked = !obj_str.checked;
                }
            }
            function saveExcel(fileTemp) {

                //선택된주문번호를 가져옴
                var	checkitems = $("input:checked[name=OrderNum[]]");
                if(checkitems.length == 0) {
                    alert ("저장하고자 하는 주문내역을 선택하세요.");
                    return;
                }

                frm = document.OderAllDelete;
                orgAction = frm.action
                frm.action = fileTemp;
                frm.submit();
                frm.action = orgAction
            }
            function paySuccess() {

                isCheck = false;
                frm = document.OderAllDelete;
                //obj = frm.elements['OrderNum[]'];
                obj = document.getElementsByName('OrderNum[]');

                for(i=0;i<obj.length;i++) {
                    if(obj[i].checked == true) isCheck = true;
                }
                if(isCheck != true) {
                    alert('승인하고자 하는 주문내역을 선택하세요.');return false;}

                frm.target = "hf";
                orgAction = frm.action
                frm.action = "od_paySuccess.php";
                frm.submit();
                frm.action = orgAction

            }
            function payBankOk() {
                isCheck = false;
                var checkCnt = 0;
                frm = document.OderAllDelete;
                obj = frm.elements['OrderNum[]'];

                for(i=0;i<obj.length;i++) {
                    if(obj[i].checked == true) {
                        isCheck = true;
                        checkCnt = checkCnt+1;
                    }
                }
                if(isCheck != true) {
                    alert('결제확인 처리 할 주문내역을 선택하세요.');return false;}

                if(!confirm('선택된 '+checkCnt+'건의 주문을 결제확인 처리 하시겠습니까?')) return false;

                orgAction = frm.action
                frm.action = "orderbankok.php";
                frm.target = "hf";
                frm.submit();
                frm.action = orgAction;
                frm.target = "";
            }


            function cal_setup(mon,day,year,xx,yy,mode){
                if(mode == 2){ 
                    if(document.view.start_date.value == ""){
                        alert('검색기간의 시작날을 먼저 입력하세요.');
                        return false;
                    }
                }
                if(document.cal_form.mode.value != mode){
                    document.cal_form.old_xx.value="";
                    document.cal_form.old_yy.value="";
                }
                x = (document.layers) ? e.pageX : document.body.scrollLeft+event.clientX;
                y = (document.layers) ? e.pageY : document.body.scrollTop+event.clientY;
                document.cal_form.mon.value = mon;
                document.cal_form.day.value = day;
                document.cal_form.year.value = year;
                if(xx == 0 && yy == 0){
                    document.cal_form.xx.value = xx;
                    document.cal_form.yy.value = yy;
                }
                else{
                    if(!document.cal_form.old_xx.value && !document.cal_form.old_yy.value){
                        document.cal_form.xx.value = x;
                        document.cal_form.yy.value = y;
                        document.cal_form.old_xx.value = x;
                        document.cal_form.old_yy.value = y;
                    }
                    else{
                        document.cal_form.xx.value = document.cal_form.old_xx.value;
                        document.cal_form.yy.value = document.cal_form.old_yy.value;
                    }
                }
                document.cal_form.mode.value = mode;
                document.cal_form.submit();
            }
            function cal_setup2(mon,day,year,xx,yy,mode){
                if(mode == 2){ 
                    if(document.view.start_date.value == ""){
                        alert('검색기간의 시작날을 먼저 입력하세요.');
                        return false;
                    }
                }
                if(document.cal_form.mode.value != mode){
                    document.cal_form.old_xx.value="";
                    document.cal_form.old_yy.value="";
                }
                x = (document.layers) ? e.pageX : document.body.scrollLeft+event.clientX;
                y = (document.layers) ? e.pageY : document.body.scrollTop+event.clientY;
                document.cal_form.mon.value = mon;
                document.cal_form.day.value = day;
                document.cal_form.year.value = year;
                if(xx == 0 && yy == 0){
                    document.cal_form.xx.value = xx;
                    document.cal_form.yy.value = yy;
                }
                else{
                    if(!document.cal_form.old_xx.value && !document.cal_form.old_yy.value){
                        document.cal_form.xx.value = x;
                        document.cal_form.yy.value = y;
                        document.cal_form.old_xx.value = x;
                        document.cal_form.old_yy.value = y;
                    }
                    else{
                        document.cal_form.xx.value = document.cal_form.old_xx.value;
                        document.cal_form.yy.value = document.cal_form.old_yy.value;
                    }
                }
                document.cal_form.mode.value = mode;
                var win_cal = window.open("","cal_win","top=300,left=300,width=240,height=175");
                document.cal_form.target="cal_win";
                document.cal_form.submit();
            }
            function click_day(clickday,mode){
                if(mode == 2){ 
                    if(document.view.start_date.value > clickday){
                        alert('검색기간의 시작날이 마지막날 보다 큽니다.');
                        return false;
                    }
                }
                document.cal_form.old_xx.value="";
                document.cal_form.old_yy.value="";
                if(mode == 1) document.view.start_date.value=clickday;
                else if(mode == 2) document.view.end_date.value=clickday;
                    document.all.cal_layer.style.display="none";
            }



//택배사 일괄선택
function expressSetFun(frm) {
    res = frm.expressSet.value;
    if(!res) {
        alert('택배사를 선택하세요.');
        frm.expressSet.focus();
        return;
    }
    obj = frm.elements['expressname[]'];
    obj2 = frm.elements['setTmp[]'];
    for(i=0;i<obj.length;i++) {
        if(obj2[i].value == '') obj[i].value = res;
    }
}



//쿠폰번호생성
function createCpNum(uniqid){
    var siteCode =  uniqid.toUpperCase();
    var errcnt = 0;     //오류횟수
    var okcnt=0;        //생성횟수
    var passnum = 0;    //넘어감횟수

//    siteCode="";    //고유코드를 사용하지않는다.(사용하려면 현재줄 전체를 주석처리하시기 바랍니다.)

    //선택된주문번호를 가져옴
    var	checkitems = $("input:checked[name=OrderNum[]]");
    if(checkitems.length == 0) {
        alert ("먼저 쿠폰번호를 생성하고자 하는 주문 항목을 선택하세요.   ");
        return;
    }

    //데이터오류확인(쿠폰상품의 경우 이미지와/주의사항이 반드시 등록되어야한다)
    var war1 = $("input:checked[name=OrderNum[]][warning=Y]").length;
    if(war1 > 0) { alert("쿠폰이미지 혹은 주의사항이 등록되어 있지 않은 상품이 있습니다."); return; }

    //선택된 주문번호의 쿠폰번호를 생성
    checkitems.each(function() {
        var code = $(this).val();   //주문번호를 쿠폰번호로 사용한다.
        var idx = 0;
        
        //선택된 주문번호의 쿠폰번호를 생성한다.인덱스는 000 형태로사용(최대값999)
        $("input:[name=expressnum[]][ordernum="+code+"]").each(function() {
            //var offset = ZeroNum(idx,3);
            var offset = idx;
            if($(this).val()=="") { 
                if($(this).attr("ordertype")=="product") {
                    passnum = passnum + 1;
                } else {
                    $(this).val( siteCode+"_"+code+offset); //쿠폰번호가 없는경우에만 생성한다.
                    okcnt = okcnt + 1;
                }
            } else {
                if($(this).attr("ordertype")=="product") {
                    passnum = passnum + 1;
                } else {
                    errcnt = errcnt + 1;
                }
            }
            idx++;
        });
    });
    alert("번호생성완료\n\n" + okcnt + "건의 쿠폰번호가 생성되었습니다.\n" 
        + errcnt + "건의 쿠폰번호는 이미 존재하여 생성되지 않았습니다.\n"
        + passnum + "건은 배송정보이므로 쿠폰번호가 생성되지 않았습니다.");
}

//쿠폰번호발송
function express(force) {

    var title1 = "먼저 발송(배송)처리를 하고자 하는 주문 항목을 선택하세요.   ";
    var title2 = "일괄처리할 주문이 100건 이상인 경우에는\n\n원활한 쿠폰발송을 위하여 1~2분 간격으로\n\n" + 
                 "100건씩 나누어 처리하시기 바랍니다.\n\n쿠폰번호(배송번호)가 없는 주문은 발행되지 않습니다.\n\n" +
                 "배송상품의 경우 택배회사가 없다면 발행되지 않습니다.\n\n주문을 발행처리 하시겠습니까?";
    
    if(force=="Y") {    //재발행일경우
        var title1 = "먼저 재발송(재배송)처리를 하고자 하는 주문 항목을 선택하세요.   ";
        var title2 = "일괄처리할 주문이 100건 이상인 경우에는\n\n원활한 발송을 위하여 1~2분 간격으로\n\n" +
                "100건씩 나누어 처리하시기 바랍니다.\n\n쿠폰번호(배송번호)가 없는 주문은 발행되지 않습니다.\n\n" +
                "배송상품의 경우 택배회사가 없다면 발행되지 않습니다.\n\n주문을 재발행 하시겠습니까?";
    }

    var	checkitems = $("input:checked[name=OrderNum[]]");
    if(checkitems.length == 0) {
        alert (title1);
        return;
    }

    frm = document.OderAllDelete;
    if(!confirm(title2)) return false;
    orgAction = frm.action;
    orgTarget = frm.target;

    frm.action = "od_orderexpress.php?force=" + force;
    frm.target = "common_frame";
    frm.submit();
    frm.action = orgAction;
    frm.target = orgTarget;
}

//숫자앞에 0을 채운다.
function ZeroNum(num,length) {
    var pow_num = Math.pow(10,length);
    var rt_value = Number(pow_num)+Number(num);
    CharNum = String(rt_value).substring(1,rt_value.length);
    return CharNum;
}
