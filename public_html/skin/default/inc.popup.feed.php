<!-- 메일링구독 -->
<div class="cm_ly_pop_tp subscription_pop cm_subscription" style="display:none;">

	<div class="title"><img src="/pages/images/cm_images/subscription_title.png" alt="타이틀이미지" /></div>

	<div class="form_email">
		<span class="lineup">
		<form name="feed_email" id="read_email" method="post" action="/pages/service.feed.pro.php" target="common_frame" onSubmit="return subscription_submit(this)">
			<input type="text" name="email_read" id="email_read" class="input_design" placeholder="정확한 이메일주소를 입력해주세요." />
			<input type="submit" name="" class="btn_ok" value="구독신청하기" />
			<span class="guide">
				메일주소를 남겨주시면 상품에 대한소식을 편리하게 받아보실 수 있습니다.<br/>
				남겨주신 이메일주소는 상품정보/할인안내 발신전용으로만 사용됩니다.
			</span>
			<!-- SSJ: 2017-09-20 구독신청 개인정보수집동의 추가 -->
			<div class="cm_step_agree no_bg" style="clear:both;">
				<textarea cols="" rows="" name="" readonly><?=stripslashes($row_company['subscrip_agree'])?></textarea>
				<label><input type="checkbox" name="subscrip_agree" id="subscrip_agree" class="" value="Y" /> 위 방침을 읽고 동의합니다.</label>
			</div>
			<!-- // SSJ: 2017-09-20 구독신청 개인정보수집동의 추가 -->
		</form>
		</span>		
	</div>	
	<div class="btn_okclose"><a href="#none" class="close" onclick="return false;">이미 등록하셨습니까?</a></div>
</div>

<script>
function popup_subscription() {
	$('.subscription_pop').lightbox_me({
		centered: true, closeEsc: false,
		onLoad: function() {}
	});
}
function subscription_submit(frm){
    // 개인정보수집동의 체크
    if( $('#subscrip_agree:checked').val() != 'Y'){
        alert("개인정보수집 및 이용에 동의해 주시기 바랍니다. "); return false;
    }

    var tmpEmail = $("#email_read").val();

    // 이메일 주소를 판별하기 위한 정규식  
    var format = /^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*$/;
  
    // 인자 email_address를 정규식 format 으로 검색
    if (tmpEmail.search(format) != -1) { return true; }
    else { alert("이메일 형식이 맞지 않거나 입력이 잘못 되었습니다. "); return false; }
}
</script>
<!-- 메일링구독 -->