/* -------------------------------------
menu box 의 width값을 구해서 지정해준다.. 
---------------------------------------*/
function menu_box_width() {
    $(".menu_box").width($(document).width()-50);
}
$(window).resize(menu_box_width);
$(document).ready(menu_box_width);
/* -------------------------------------
// menu box 의 width값을 구해서 지정해준다.. 
---------------------------------------*/


/* -------------------------------------
menu 의 width 값을 구해서 지정해준다.
---------------------------------------*/
var width_sum = 0;
function menu_width() {

    // menu 내 a의 width 합을 구한다.
    cnt = $(".menu a").length;
    for(i=1;i<=cnt;i++) {
        width_sum += $(".menu a").eq(i).outerWidth()*1;
    }

    // menu 의  width 값을 설정한다.
    $(".menu").width(width_sum);

}
$(document).ready(menu_width);
/* -------------------------------------
// menu 의 width 값을 구해서 지정해준다.
---------------------------------------*/


/* -------------------------------------
좌우 화살표 클릭시 동작
---------------------------------------*/
var move_pixel = 100;   // 한번클릭시 이동할 픽셀
var moving = false; // 더블클릭 방지를 위해.
$(".ctg_2depth .ic_next").click(function() { // 다음버튼 클릭시 이동..

alert(1);
    // 움직이고 있는중에는 동작하지 않는다.;
    if( moving==true ) 
        return;
    else 
        moving = true;

    now_margin_value = $(".menu").css("margin-left").replace(/[^-\d\.]/g, '')*1; // 현재 left 마긴값
    next_margin = now_margin_value - move_pixel; // 다음 left 마긴값

    move_speed = 1; // 이동속도 1000 = 1초
    tid = setInterval(next_move,move_speed);

});

// 다음버튼 클릭시 이동..
function next_move() {

    // 이동시킨다.
    var move_now_margin_value = $(".menu").css("margin-left").replace(/[^-\d\.]/g, '')*1;    // 현재 left 마긴값  
    $(".menu").css("margin-left",(move_now_margin_value-3)+"px");
    if(next_margin >= move_now_margin_value)    
        move_stop(); // 이동을 멈춘다.

    // 다음버튼을 감춘다.
    if(($(".menu").width()*1 + move_now_margin_value*1) < $(".menu_box").width()) 
        move_stop(); // 이동을 멈춘다.

    // 좌우 버튼 체크
    btn_check();
}

// 이전버튼 클릭시 이동..
$(".ctg_2depth .ic_prev").click(function() {

    // 움직이고 있는중에는 동작하지 않는다.;
    if( moving==true ) 
        return;
    else 
        moving = true;

    now_margin_value = $(".menu").css("margin-left").replace(/[^-\d\.]/g, '')*1; // 현재 left 마긴값
    prev_margin = now_margin_value + move_pixel; // 이전 left 마긴값

    move_speed = 1; // 이동속도 1000 = 1초
    tid = setInterval(prev_move,move_speed);

});

// 이전버튼 클릭시 이동..
function prev_move() {  

    // 이동시킨다.
    var move_now_margin_value = $(".menu").css("margin-left").replace(/[^-\d\.]/g, '')*1;    // 현재 left 마긴값  
    $(".menu").css("margin-left",(move_now_margin_value+3)+"px");
    if(prev_margin <= move_now_margin_value)    
        move_stop(); // 이동을 멈춘다.

    // 이전버튼을 감춘다.
    if(move_now_margin_value > -2) 
        move_stop();    // 이동을 멈춘다.

    // 좌우 버튼 체크
    btn_check();
}
/* -------------------------------------
//좌우 화살표 클릭시 동작
---------------------------------------*/

// 이동을 멈춘다.
function move_stop() {
        clearInterval(tid); // 자동실행 멈춤.
        moving = false; // 더블클릭 체크 값을 초기화 한다.
}


/* -------------------------------------
버튼을 감출지, 노출시킬지 결정한다.
---------------------------------------*/
function btn_check() {

    var now_margin = $(".menu").css("margin-left").replace(/[^-\d\.]/g, '')*1;   // 현재 left 마긴값  
 
    // 이전버튼을 감춘다.
    if(now_margin > -2) 
        $(".ctg_2depth .ic_prev").hide();
    else 
        $(".ctg_2depth .ic_prev").show();        

    // 다음버튼을 감춘다.
    if(($(".menu").width()*1 + now_margin*1) < $(".menu_box").width()) 
        $(".ctg_2depth .ic_next").hide();
    else
        $(".ctg_2depth .ic_next").show();        
    
}
$(document).ready(btn_check);
$(window).resize(btn_check);
/* -------------------------------------
// 버튼을 감출지, 노출시킬지 결정한다.
---------------------------------------*/
