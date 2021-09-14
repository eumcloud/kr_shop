</div> <!-- #hiddenCheckHeight -->
<?
//에스크로 이미지가 있을 때만 노출
$escro_url = $row_company['escrow_url'] ? $row_company['escrow_url'] : '#none';
?>


<!-- 공통:하단정보 -->
<div class="footer_menu">
	<div class="layout_fix">
		<div class="lineup">
			<a href="/?pn=service.page.view&pageid=company" class="btn">회사소개</a>
			<span class="line"></span>
			<a href="/?pn=service.agree" class="btn">이용약관</a>
			<span class="line"></span>
			<a href="/?pn=service.privacy" class="btn"><strong>개인정보처리방침</strong></a>
			<span class="line"></span>
			<a href="/?pn=service.guide" class="btn">이용안내</a>
			<span class="line"></span>
			<a href="/?pn=service.partner.form" class="btn">광고/제휴문의</a>
			<span class="line"></span>
			<a href="/?pn=service.main" class="btn">고객센터</a>
		</div>
	</div>
</div>

<div class="footer">
	<div class="layout_fix">

		<? $banner_info = info_banner("site_footer_logo",1,"data"); ?>
		<div class="logo">
			<img src="<?=IMG_DIR_BANNER.$banner_info[0]['b_img']?>" alt="<?=IMG_DIR_BANNER.$banner_info[0]['b_title']?>" />
			<!-- 2016-02-03 에스크로추가 없으면 div 숨김 -->
			<?php // 에스크로 URL 적용
			if( $row_company['escrow_img'] && $row_company['escrow_url'] ) {
				echo "<div class=\"escrow\"><a href='".$row_company['escrow_url']."' target='_blank' ><img src='/upfiles/normal/".$row_company['escrow_img']."' alt=''></a></div>";
			}
			?>
		</div>

		<div class="right">
			<div class="text">
				상호명: <?=$row_company['name']?><em class="line"></em>대표: <?=$row_company['ceoname']?><em class="line"></em>통신판매업신고번호: <?=$row_company['number2']?><em class="line"></em>사업자등록번호: <?=$row_company['number1']?><br />
				사업장 소재지: <?=$row_company['taxaddress']?><em class="line"></em>대표전화: <?=$row_company['tel']?><em class="line"></em>팩스: <?=$row_company['fax']?><br />
				개인정보관리책임자: <?=$row_company['name1']?> <?=$row_company['email']?>
				<em class="line"></em>Hosting by (주)상상너머
			</div>

			<div class="copy">
				COPYRIGHT &copy; <?=$row_company['name']?>. ALL RIGHTS RESERVED.<br />
				고객님은 안전거래를위해 현금등으로 5만원이상 결제시 저희쇼핑몰에서 가입한 구매안전 서비스를 이용하실 수 있습니다.
			</div>

			<div class="ic_btn">
				<?if($row_company['view_network_company_info'] == "Y") { ?>
				<a href="#none" onclick="window.open('http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no=<?=str_replace("-","",$row_company['number1'])?>', 'communicationViewPopup', 'width=750, height=700;');" class="btn"><img src="/pages/images/ft_ic01.gif" alt="사업자 가입 정보 확인" /></a>
				<? } ?>
				<?
				switch($row_setup['P_KBN']) {
					case "L" :
						if($row_setup['s_view_escrow_join_info'] == "Y") {
							echo "<a href='#none' onclick=\"window.open('https://pgweb.uplus.co.kr/pg/wmp/mertadmin/jsp/mertservice/s_escrowYn.jsp?mertid=".$row_setup['P_ID']."','check','width=345, height=270, scrollbars=no, left = 200, top = 50')\" class='btn'><img src='/pages/images/ft_ic02.gif' alt='서비스가입사실확인' /></a>";
						}
						echo "<img src='/pages/images/pg_lg.gif' alt='엘지유플러스' style='height:16px;'/>";
						break;
					case "I" :
						if($row_setup['s_view_escrow_join_info'] == "Y") {
							echo "<a href='#none' onclick=\"window.open('https://mark.inicis.com/mark/escrow_popup.php?mid=".$row_setup['P_SID']."','check','width=565, height=683, scrollbars=no, left = 200, top = 50')\" class='btn'><img src='/pages/images/ft_ic02.gif' alt='서비스가입사실확인' /></a>";

						}
						echo "<img src='/pages/images/pg_inicis.gif' alt='이니시스' style='height:16px'/>";
						break;
					case "K" :
						if($row_setup['s_view_escrow_join_info'] == "Y") {
							echo "<a href='#none' onclick=\"window.open('http://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp?site_cd=".$row_setup['P_ID']."','check','width=500, height=450, scrollbars=no, left = 200, top = 50')\" class='btn'><img src='/pages/images/ft_ic02.gif' alt='서비스가입사실확인' /></a>";
						}
						echo "<img src='/pages/images/pg_kcp.gif' alt='KCP' style='height:16px;'/>";
						break;
					case "A" :
						if($row_setup['s_view_escrow_join_info'] == "Y") {
							echo "<a href='#none' onclick=\"window.open('http://www.allthegate.com/hyosung/paysafe/escrow_check.jsp?service_id=".$row_setup['P_ID']."&biz_no=".str_replace("-","",$row_company['number1'])."','check','width=400, height=300, scrollbars=no, left = 200, top = 50')\" class='btn'><img src='/pages/images/ft_ic02.gif' alt='서비스가입사실확인' /></a>";
						}
						echo "<img src='/pages/images/pg_allthe.gif' alt='올더게이트' style='height:16px;'/>";
						break;
					case "M" :
						echo "<img src='/pages/images/pg_mb.jpg' alt='인포뱅크' style='height:16px;'/>";
						break;
					case "B" :
						echo "<img src='/pages/images/pg_billgate.png' alt='빌게이트' style='height:16px;'/>";
						break;
				    case "D" :
				        if($row_setup['s_view_escrow_join_info'] == "Y") {
				            echo "<a href='#none' onclick=\"window.open('https://agent.daoupay.com/EscrowUsedCheck.jsp?CPID=".$row_setup['P_ID']."&CPBUSINESSNO=".str_replace("-","",$row_company['number1'])."','check','width=470, height=484, scrollbars=no, left = 200, top = 50')\" class='btn'><img src='/pages/images/ft_ic02.gif' alt='서비스가입사실확인' /></a>";
				        }
				        echo "<img src='/pages/images/pg_daupay.png' alt='다우페이' style='height:16px;'/>";
				        break;
				}
				?>
				<a href="#none" onclick="window.open('http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no=<?=str_replace('-','',$row_company['number1'])?>', 'communicationViewPopup', 'width=750, height=700');" class="btn"><img src="/pages/images/kftc_ic.gif" alt="공정거래위원회" /></a>
			</div>
		</div>
	</div>
</div>
<!-- //공통:하단정보 -->

<a name="footPosition" id="footPosition"></a>
</div>


<script>
//찜하기 버튼 설정
$(document).ready(function(){
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
</script>

<?
	include "inc.popup.feed.php"; // 메일링구독
	include dirname(__FILE__)."/inc.footer.php";
?>