

<!-- 메일링구독 -->
<div class="ly_pop subscription" style="display:none">

	<div class="title"><img src="/pages/images/common/subscription_title.png" alt="타이틀이미지" /></div>

	<form name="feed_email" id="read_email" method="post" action="/pages/service.subscription.pro.php" target="common_frame" onSubmit="return subscription_submit(this)">
	<div class="form">
		<span class="fix">
			<input type="text"name="email_read" id="email_read"  class="" value="정확한 이메일주소를 입력해주세요" onfocus="this.value=''"/>
			<input type='image' src="/pages/images/common/subscription_btn.png" alt="구독신청하기" />
			<span class="guide">
				메일주소를 남겨주시면 상품에 대한소식을 편리하게 받아보실 수 있습니다<br/>
				남겨주신 이메일주소는 상품정보/할인안내 발신전용으로만 사용됩니다
			</span>
		</span>
	</div>
	</form>

	<div class="close">
		<a href="#none"><img src="/pages/images/common/subscription_close.png" alt="창닫기" /></a>
	</div>

</div>
<script>
	function popup_subscription() {
		$('.subscription').lightbox_me({
			centered: true, 
			closeEsc: false,
			onLoad: function() { 
			}
		});
	}
	function subscription_submit(frm){
		var tmpEmail = $("#email_read".val());

    // 이메일 주소를 판별하기 위한 정규식  
    var format = /^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*$/;  
      
    // 인자 email_address를 정규식 format 으로 검색  
    if (tmpEmail.search(format) != -1)  
    	return true;
    else  {
        alert("이메일 형식이 맞지 않거나 입력이 잘못 되었습니다. ");
        return false;  
    }
	}
</script>
<!-- 메일링구독 -->