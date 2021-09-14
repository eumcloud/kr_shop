function MM_swapImgRestore() { //v3.0
    var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
    var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
        var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
        if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
    var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
        d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
    if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
    for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
    if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
    var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
     if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}



function MM_showHideLayers() { //v9.0
    var i,p,v,obj,args=MM_showHideLayers.arguments;
    for (i=0; i<(args.length-2); i+=3) 
    with (document) if (getElementById && ((obj=getElementById(args[i]))!=null)) { v=args[i+2];
        if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
        obj.visibility=v; }
}

function Only_Numeric() {
    if((event.keyCode<48 || event.keyCode>57 || event.keyCode==45) && event.keyCode!=13) event.returnValue=false;
}

function openwindow(name,url,width,height,scrollbar) {
        scrollbar_str = scrollbar ? 'yes' : 'no';
        window.open(url,name,'width='+width+',height='+height+',scrollbars='+scrollbar_str);
}

function authFunction() {
        alert('해당 게시판에 대한 권한이 없습니다.   ');
}

// - Comma 적용 ---
String.prototype.comma=function() { 
    var l_text=this.replace(/,/g,''); 

    if(l_text == "0") return "0";

    var l_pattern=/^(-?\d+)(\d{3})($|\..*$)/; 

    if(l_pattern.test(l_text)){ 
        l_text=l_text.replace(l_pattern,function(str,p1,p2,p3) 
        { 
            return p1.comma() + ("," + p2 + p3); 
        }); 
    } 
    return l_text; 
}
// - Comma 적용 ---



// 상품 목록 보기 ajax 함수 (노출시킬 class명, 카테고리코드, 한페이지에 노출시킬 갯수, 리스트 유형, 페이징 사용여부, 인기상품순위 아이콘 사용여부, 테마, 기타 이벤트요소,정렬 필드)
function product_list_view(display_area,cuid,listmaxcount,list_type,pagenate,hit_num_use,thema,event_type,order_field,order_sort,keyword,price) {

	if(cuid         == undefined) cuid="";
	if(listmaxcount == undefined) listmaxcount="";
	if(list_type    == undefined) list_type="";
	if(pagenate     == undefined) pagenate="";
	if(thema   		== undefined) thema="";
	if(event_type   == undefined) event_type="";
	if(hit_num_use  == undefined) hit_num_use="";
	if(order_field  == undefined) order_field="";
	if(order_sort   == undefined) order_sort="";
	if(price  		== undefined) price="";	
	if(keyword 		== undefined) keyword="";

	$.ajax({
			url: "/m/ajax.product.list.php",
			cache: false,
			type: "POST",
			data: "cuid="+cuid+"&listmaxcount="+listmaxcount+"&list_type="+list_type+"&pagenate="+pagenate+"&hit_num_use="+hit_num_use+"&thema="+thema+"&event_type="+event_type+"&order_field="+order_field+"&order_sort="+order_sort+'&search_keyword='+keyword+'&q_price='+price,
			success: function(data){
				$("."+display_area).html(data); init_lazyload();
			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
	});
}

// 목록에서 구매/장바구니 담기
function app_submit_from_list(pcode,_type,is_option) {
    if(is_option) {
        alert("옵션이 있는 상품입니다. 옵션을 선택해주시기 바랍니다.");
        location.href = rewrite_url(pcode);
    }
    else {
        location.href = '/m/shop.cart.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type + '&option_select_type=nooption';
    }
}

// - 상세페이지에서 구매/장바구니 담기 
function app_submit(pcode,_type) {
    if( !( $("#option_select_cnt").val() * 1 > 0 ) ) {
        alert("옵션을 하나 이상 선택해주시기 바랍니다.")
    }
    else if( !( $("#option_select_expricesum").val() * 1 > 0 ) ) {
        alert("옵션 합계금액이 0원을 초과해야 합니다.")
    }
    else {
        if($('#add_option_select_1_id').val()) { var add_option_select_1 = $('#add_option_select_1_id').val(); } else { var add_option_select_1 = ''; }
        if($('#add_option_select_2_id').val()) { var add_option_select_2 = $('#add_option_select_2_id').val(); } else { var add_option_select_2 = ''; }
        if($('#add_option_select_3_id').val()) { var add_option_select_3 = $('#add_option_select_3_id').val(); } else { var add_option_select_3 = ''; }
        if($('#add_option_select_4_id').val()) { var add_option_select_4 = $('#add_option_select_4_id').val(); } else { var add_option_select_4 = ''; }
        if($('#add_option_select_5_id').val()) { var add_option_select_5 = $('#add_option_select_5_id').val(); } else { var add_option_select_5 = ''; }
        if($('#add_option_select_6_id').val()) { var add_option_select_6 = $('#add_option_select_6_id').val(); } else { var add_option_select_6 = ''; }
        if($('#add_option_select_7_id').val()) { var add_option_select_7 = $('#add_option_select_7_id').val(); } else { var add_option_select_7 = ''; }
        if($('#add_option_select_8_id').val()) { var add_option_select_8 = $('#add_option_select_8_id').val(); } else { var add_option_select_8 = ''; }
        if($('#add_option_select_9_id').val()) { var add_option_select_9 = $('#add_option_select_9_id').val(); } else { var add_option_select_9 = ''; }
        if($('#add_option_select_10_id').val()) { var add_option_select_10 = $('#add_option_select_10_id').val(); } else { var add_option_select_10 = ''; }
        location.href = ('/m/shop.cart.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type  + '&add_option_select_1=' + add_option_select_1 + '&add_option_select_2=' + add_option_select_2 + '&add_option_select_3=' + add_option_select_3 + '&add_option_select_4=' + add_option_select_4 + '&add_option_select_5=' + add_option_select_5 + '&add_option_select_6=' + add_option_select_6 + '&add_option_select_7=' + add_option_select_7 + '&add_option_select_8=' + add_option_select_8 + '&add_option_select_9=' + add_option_select_9 + '&add_option_select_10=' + add_option_select_10 + '&option_select_type=' + $("#option_select_type").val() + '&option_select_cnt=' + $("#option_select_cnt").val()); // cart 처리페이지 이동
    }
}

function app_soldout() {
    alert("품절된 상품입니다.");
}

// 로그인 알럿
function login_alert(pn) {
    if( confirm('로그인 후 이용할수 있습니다.\n\n로그인 페이지로 이동 하시겠습니까?') ){
		top.location.href='/?pn=member.login.form&path='+pn;
	}
}

// defaultValue 값 입력
function input_dv_insert() {

    obj = $("input[type=text], textarea");
    for(i=0;i<obj.length;i++) {
        dv = $(obj).eq(i).attr("defaultValue");
        !$(obj).eq(i).val() && dv ? $(obj).eq(i).val(dv) : null;
    }

}

// 입력전 defaultValue 값 제거
function input_dv_delete() {

    obj = $("input[type=text], textarea");
    for(i=0;i<obj.length;i++) {
        dv = $(obj).eq(i).attr("defaultValue");
        $(obj).eq(i).val() == dv ? $(obj).eq(i).val("") : null;
    }

}

// 천단위 콤마 제거
function number_style_comma_delete() {

    obj = $(".number_style");

    if(obj.length > 0)
        for(var i in obj) 
            if(obj[i].value != undefined) 
                obj[i].value = obj[i].value.replace(/,/g,"");
}

// 폼 서브밋을 위한 초기화
function formSubmitSet() {

    input_dv_delete();
    number_style_comma_delete();

}

$(document).ready(function() {

    /* ----- default 값 자동입력 및 처리. ---------- */
    input_dv_insert();// default 값 입력

    // input 포커스가 들어왔을때 default 값을 삭제한다.
    $("input[type=text], textarea").focus(function() {
        dv = $(this).attr("defaultValue");
        if($(this).val() == dv) 
            $(this).val("");
    });

    // input 값이 없이 포커스가 나가면 default 값을 삽입한다.
    $("input[type=text], textarea").blur(function() {
        dv = $(this).attr("defaultValue");
        if(!$(this).val() && dv) 
            $(this).val(dv);
    }); 
    /* ----- // default 값 자동입력 및 처리. ---------- */



    /* ----- 숫자만 입력받고 천단위 콤마를 삽입한다. ---------- */
    $(".number_style").keypress(function() {

        // 숫자만 입력
        if((event.keyCode<48 || event.keyCode>57 || event.keyCode==45) && event.keyCode!=13) event.returnValue=false;

    });
    $(".number_style").keyup(function() {

        // 천단위 콤마
        this.value = this.value.comma();

    });

    obj = $(".number_style");

    if(obj.length > 0)
        for(var i in obj) 
            if(obj[i].value != undefined) 
                obj[i].value = obj[i].value.comma();

    /* ----- // 숫자만 입력받고 천단위 콤마를 삽입한다. ---------- */

});

// 메뉴보기
function view_menu() {
    patt = /block/g;
    displayValue = $(".page_menu").css("display");
    if(patt.test(displayValue)) $(".page_menu").slideUp(300);
    else $(".page_menu").slideDown(300);
}


/*
2015-11-13 LDD
자바스크립트 number_format
"1000".number_format();
var num = 1000;
num.number_format();
*/
Number.prototype.number_format = function(){
    if(this==0) return 0;
 
    var reg = /(^[+-]?\d+)(\d{3})/;
    var n = (this + '');
 
    while (reg.test(n)) n = n.replace(reg, '$1' + ',' + '$2');
 
    return n;
};
String.prototype.number_format = function() { // 문자열 타입에서 쓸 수 있도록 format() 함수 추가
    var num = parseFloat(this);
    if( isNaN(num) ) return "0";
 
    return num.format();
};




// 2015-11-17 rewrite 함수
function rewrite_url(pcode, add_url) {

    if(pcode === undefined || pcode === '') pcode = '';
    if(add_url === undefined || add_url === '') add_url = '';
    if(rewrite_chk == 'no') return od_url+'/?pn=product.view&pcode='+pcode+'&'+add_url;
    else return od_url+'/'+pcode+'?'+add_url;
}