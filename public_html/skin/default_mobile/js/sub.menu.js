
var _addMargin = 0;
function getAddMargin(){ if($('.ctg_3depth').hasClass('ctg_wide')) { _addMargin = 170; } else { _addMargin = 0; } }
$(window).resize(getAddMargin);
$(document).ready(getAddMargin);

/* -------------------------------------
menu box 의 width값을 구해서 지정해준다.. 
---------------------------------------*/
function menu_box_width() {
    $(".ctg_3depth .menu_box").width($(document).width()-30 - _addMargin);
}
$(window).resize(menu_box_width);
$(document).ready(menu_box_width);
/* -------------------------------------
// menu box 의 width값을 구해서 지정해준다.. 
---------------------------------------*/


/* -------------------------------------
menu 의 width 값을 구해서 지정해준다.
현제 선택된 메뉴로 이동시킨다
---------------------------------------*/
var width_sum = 0;
var width_hit = 0;
var margin_left = 0;
function menu_width() {

    // menu 내 a의 width 합을 구한다.
    cnt = $(".ctg_3depth .menu a").length;
    for(i=0;i<cnt;i++) {
		// 선택된메뉴까지의 넓이값
		if($(".ctg_3depth .menu a").eq(i).attr("class") == "hit"){
			width_hit = width_sum;
		}
		
        width_sum += $(".ctg_3depth .menu a").eq(i).outerWidth()*1;
    }

    // menu 의  width 값을 설정한다. - 아이폰의경우 메뉴가 나타나지 않는경우 발생 => 넓이값을 조금 늘려준다
    $(".ctg_3depth .menu").width(width_sum + 1);

	// 선택된메뉴가 있으면 이동
	if(width_hit>0){
		//var move_pixel = 200;   // 한번클릭시 이동할 픽셀 
		var move_cnt = Math.floor(width_hit/200);
		if(move_cnt>0){

			if(width_sum + (move_cnt * -200) < $('.ctg_3depth .menu_box').width() ) { margin_left = $('.ctg_3depth .menu_box').width() - width_sum - 2; }
			else { margin_left = move_cnt * -200; }

			if(width_sum < $('.ctg_3depth .menu_box').width()) { margin_left = 15; }

			$(".ctg_3depth .menu").css("margin-left" , margin_left+"px");
		}
	}

}
$(document).ready(menu_width);
/* -------------------------------------
// menu 의 width 값을 구해서 지정해준다.
---------------------------------------*/


/* -------------------------------------
좌우 화살표 클릭시 동작
---------------------------------------*/
var move_pixel = 200;   // 한번클릭시 이동할 픽셀
var moving = false; // 더블클릭 방지를 위해.
$(".ctg_3depth .btn_next").click(function() { // 다음버튼 클릭시 이동..

    // 움직이고 있는중에는 동작하지 않는다.;
    if( moving==true ) 
        return;
    else 
        moving = true;

    now_margin_value = $(".ctg_3depth .menu").css("margin-left").replace(/[^-\d\.]/g, '')*1; // 현재 left 마긴값
    next_margin = now_margin_value - move_pixel; // 다음 left 마긴값

    move_speed = 1; // 이동속도 1000 = 1초
    tid = setInterval(next_move,move_speed);

});

// 다음버튼 클릭시 이동..
function next_move() {

    // 이동시킨다.
    var move_now_margin_value = $(".ctg_3depth .menu").css("margin-left").replace(/[^-\d\.]/g, '')*1;    // 현재 left 마긴값  
    $(".ctg_3depth .menu").css("margin-left",(move_now_margin_value-3)+"px");
    if(next_margin >= move_now_margin_value)    
        move_stop(); // 이동을 멈춘다.

    // 다음버튼을 감춘다.
    if(($(".ctg_3depth .menu").width()*1 + move_now_margin_value*1) < $(".ctg_3depth .menu_box").width()) 
        move_stop(); // 이동을 멈춘다.

    // 좌우 버튼 체크
    btn_check();
}

// 이전버튼 클릭시 이동..
$(".ctg_3depth .btn_prev").click(function() {

    // 움직이고 있는중에는 동작하지 않는다.;
    if( moving==true ) 
        return;
    else 
        moving = true;

    now_margin_value = $(".ctg_3depth .menu").css("margin-left").replace(/[^-\d\.]/g, '')*1; // 현재 left 마긴값
    prev_margin = now_margin_value + move_pixel; // 이전 left 마긴값

    move_speed = 1; // 이동속도 1000 = 1초
    tid = setInterval(prev_move,move_speed);

});

// 이전버튼 클릭시 이동..
function prev_move() {  

    // 이동시킨다.
    var move_now_margin_value = $(".ctg_3depth .menu").css("margin-left").replace(/[^-\d\.]/g, '')*1;    // 현재 left 마긴값  
    $(".ctg_3depth .menu").css("margin-left",(move_now_margin_value+3)+"px");
    if(prev_margin <= move_now_margin_value)    
        move_stop(); // 이동을 멈춘다.

    // 이전버튼을 감춘다.
    if(move_now_margin_value > -2) 
        move_stop();    // 이동을 멈춘다.

    // 좌우 버튼 체크
    btn_check();
}

// 터치로 이동시키기
function touch_move(touch_margin_value,gab) {  

    // 맨처음까지 가면 멈춘다.
    if( (touch_margin_value-gab) > -2 ) return;

    // 끝까지 가면 멈춘다.
    if(($(".ctg_3depth .menu").width()*1 + touch_margin_value*1) < $(".ctg_3depth .menu_box").width()) return;

    // 이동시킨다.
    $(".ctg_3depth .menu").css("margin-left",(touch_margin_value-gab)+"px");
    
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

    var now_margin = $(".ctg_3depth .menu").css("margin-left").replace(/[^-\d\.]/g, '')*1;   // 현재 left 마긴값  
 
    // 이전버튼을 감춘다.
    if(now_margin > -2) 
        $(".ctg_3depth .btn_prev").hide();
    else 
        $(".ctg_3depth .btn_prev").show();        

    // 다음버튼을 감춘다.
    if(($(".ctg_3depth .menu").width()*1 + now_margin*1) < $(".ctg_3depth .menu_box").width()) 
        $(".ctg_3depth .btn_next").hide();
    else
        $(".ctg_3depth .btn_next").show();        
    
}
$(document).ready(btn_check);
$(window).resize(btn_check);
/* -------------------------------------
// 버튼을 감출지, 노출시킬지 결정한다.
---------------------------------------*/
