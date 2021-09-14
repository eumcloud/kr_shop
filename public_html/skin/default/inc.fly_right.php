<?
$side_margin_top = $side_margin_top ? $side_margin_top : "320";
?>
<!-- 우측사이드 -->
<div class="fly_right" style="margin-top:<?=$side_margin_top?>px;">

	<div id="quick_slide">
		<!-- 오늘본상품 -->
		<div id="ly_fly" data-page='1'>
		<?include dirname(__FILE__)."/inc.latest.view.php";?>
		</div>
		<div class="btn_area">
			<a href="/?pn=shop.cart.list" class="btn">장바구니 [<strong><?=get_cart_cnt()?></strong>]</a>
			<? if(is_login()) { ?>
			<a href="/?pn=mypage.order.list" class="btn">주문배송조회</a>
			<? } else { ?>
			<a href="/?pn=service.guest.order.list" class="btn">비회원주문조회</a>
			<? } ?>
			<a href="/?pn=mypage.request.form" class="btn">1:1 온라인문의</a>
		</div>
	</div>
	
</div>

<!-- 위아래 -->
<div class="topposition" id="topdown_slide" style="top:0;">
	<a href="#none" onclick="return false;" class="scrollto btn" data-scrollto="topPosition" title="맨위로">
		<span class="lineup">
			<img src="/pages/images/btn_txt_up.png" alt="맨위로" class="off" />
			<img src="/pages/images/btn_txt_up_over.png" alt="맨위로" class="over" />
		</span>
	</a>
	<a href="#none" onclick="return false;" class="scrollto btn" data-scrollto="footPosition" title="맨아래로">
		<span class="lineup">
			<img src="/pages/images/btn_txt_down.png" alt="맨아래로" class="off" />
			<img src="/pages/images/btn_txt_down_over.png" alt="맨아래로" class="over" />
		</span>	
	</a>
</div>
<!-- //우측사이드 -->

<!-- 최근본상품 -->
<script>

	var late_page = 1;

	function late_page_move(type) {
		
		var now_page = this.late_page;
		var next_page = now_page*1+1;
		var prev_page = now_page*1-1;



		if(type == "next") {
			if($("#ly_fly .page"+next_page).length == 0) {alert('마지막페이지 입니다.');return;}
			$("#ly_fly .page"+now_page).hide();
			$("#ly_fly .page"+next_page).show();
			this.late_page = next_page;
		} else {
			if($("#ly_fly .page"+prev_page).length == 0) {alert('처음페이지 입니다.');return;}
			$("#ly_fly .page"+now_page).hide();
			$("#ly_fly .page"+prev_page).show();
			this.late_page = prev_page;
		}

		// 현재 페이지 번호를 반영
		$(".page_now").html(this.late_page);



	}
	function late_delete(uid) {
		common_frame.location.href='/pages/inc.latest.pro.php?uid='+uid;
		this.late_page = 1;
	}
	function latest_view() {
			$.ajax({
				url: "/pages/inc.latest.view.php?_page_num="+this.late_page,
				cache: false,
				type: "POST",
				data: "" ,
				success: function(data){
					$("#ly_fly").html(data);
				}
			});


	}

	var latest_box_controller = function() {
		var position = $(window).scrollTop(); // 현재 스크롤바의 위치값을 반환

		if(position > (<?=$latest_scroll_top?>)) { // $latest_scroll_top 는 wrap.header.php 파일에 위치
			$("#quick_slide").css({ "position":"fixed", "top":0, "margin-top":"20px" });
			$("#topdown_slide").attr('style','').css({ "position":"fixed", "bottom":0, "margin-top":0, "margin-bottom":"20px" });
		} else { 
			$('#quick_slide').attr('style','');
			$('#topdown_slide').attr('style','').css({"top":0});
		}
	}
	// 옵션 박스 위치조정후 버튼을 노출
	latest_box_controller();

	$(document).ready(latest_box_controller);
	$(window).scroll(latest_box_controller);
	$(window).resize(latest_box_controller);
</script>