<!-- 하단공통 -->
<div class="footer">
	<div class="bt_menu">
		<span class="lineup">
			<a href="/m/?pn=service.page.view&pageid=company" class="btn">회사소개</a><span class="divi"></span>
			<a href="/m/?pn=service.agree" class="btn">이용약관</a><span class="divi"></span>
			<a href="/m/?pn=service.privacy" class="btn"><strong>개인정보처리방침</strong></a><span class="divi"></span>
			<a href="/m/?pn=service.main" class="btn">고객센터</a>
		</span>
	</div>

	<a href="tel:<?=$row_company['tel']?>" class="btn_call"><?=$row_company['tel']?></a>

	<div class="copyright">
		<?=$row_company['name']?><span class="divi"></span>대표: <?=$row_company['ceoname']?><span class="divi"></span>개인정보관리책임자: <?=$row_company['name1']?><br />
		통신판매업신고번호: <?=$row_company['number2']?><br />사업자등록번호: <?=$row_company['number1']?><br />
		<?=$row_company['taxaddress']?><span class="divi"></span>팩스: <?=$row_company['fax']?>
		<span class="divi"></span>Hosting by (주)상상너머

		<div class="copy">COPYRIGHT &copy; <?=$row_company['name']?>. ALL RIGHTS RESERVED.</div>
	</div>

	<div class="btn_bottom">
		<!-- 로그인 후 로그아웃, 정보수정, PC버전보기 -->
		<span class="lineup">
			<? if( is_login() ){ ?>
			<a href="/m/?pn=mypage.main" class="btn_member">마이페이지</a>
			<a href="/m/member.login.pro.php?_mode=logout" class="btn_member">로그아웃</a>
			<? } else { ?>
			<a href="/m/?pn=member.join.agree" class="btn_member">회원가입</a>
			<a href="/m/?pn=member.login.form&path=<?=enc("e",$_SERVER['QUERY_STRING'])?>" class="btn_member">로그인</a>
			<? } ?>
			<a href="/?_pcmode=chk&<?=str_replace('_mobilemode=chk','',$_SERVER['QUERY_STRING'])?>" class="btn_member">PC버전 보기</a>
		</span>
	</div>
</div>

<!-- 하단공통 -->
<!-- 하단 공백을 위한 div -->
<div class="bottom_blank"></div>
<div class="upper_footer upper_bottom">
	<!-- 해당 페이지일 때 btn_hit 추가 -->
	<a href="/m/" class="btn <?=!$pn||$pn=='main'?'btn_hit':''?>">
		<span class="lineup">
			<span class="img_ic">
				<img src="/m/images/ic_bt_home_off.png" alt="홈으로" class="off" />
				<img src="/m/images/ic_bt_home_hit.png" alt="홈으로" class="hit" />
			</span>
			<span class="txt">홈으로</span>
		</span>
	</a>
	<a href="#none" onclick="return false;" class="btn open_slide">
		<span class="lineup">
			<span class="img_ic">
				<img src="/m/images/ic_bt_ctg_off.png" alt="카테고리" class="off" />
				<img src="/m/images/ic_bt_ctg_hit.png" alt="카테고리" class="hit" />
			</span>
			<span class="txt">카테고리</span>
		</span>
	</a>
	<a href="/m/?pn=mypage.main" class="btn <?=preg_match("/mypage./i",$pn)?'btn_hit':''?>">
		<span class="lineup">
			<span class="img_ic">
				<img src="/m/images/ic_bt_my_off.png" alt="마이페이지" class="off" />
				<img src="/m/images/ic_bt_my_hit.png" alt="마이페이지" class="hit" />
			</span>
			<span class="txt">마이페이지</span>
		</span>
	</a>
</div>


<script>
//찜하기 버튼 설정
$(document).ready(function(){
	init_lazyload();
	$('body').delegate('.ajax_wish','click',function(e){ e.preventDefault();
		<? if(is_login()) { ?>
		var mode = 'add', code = $(this).data('code'), $this = $(this);
		if($(this).hasClass('btn_wish_hit')) { mode = 'delete'; }
		$.ajax({
			data: {'mode':mode,'code':code},
			type: 'POST',
			cache: false,
			url: '/pages/ajax.product.wish.php',
			success: function(data) {
				if($.isNumeric(data)) {
					/*data = String(data).comma();
					$('.ajax_cart_txt').text(data);*/
					if( mode == 'add' ) { alert('상품을 찜했습니다.'); $this.addClass('btn_wish_hit'); $this.attr('title','찜해제'); }
					if( mode == 'delete' ) { alert('상품 찜을 해제했습니다.'); $this.removeClass('btn_wish_hit'); $this.attr('title','찜하기'); }
				} else { alert(data); }
			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
		<? } else { ?>
		if( confirm('찜하기는 로그인 후 이용하실 수 있습니다. 로그인 하시겠습니까?') ) {
			location.href='/?pn=member.login.form&path=<?=enc('e',$_SERVER[QUERY_STRING])?>';
		}
		<? } ?>
	});
});
function init_lazyload(){
	$("img.lazy").lazyload();
}
</script>
<? include dirname(__FILE__)."/inc.footer.php"; ?>
