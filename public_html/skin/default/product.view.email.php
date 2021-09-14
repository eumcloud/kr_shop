<?
// url 축소하기 적용--------------------------------------
$org_url = rewrite_url($row_product['code']);
//$app_shorten_url = get_shortURL_2($org_url);
$app_shorten_url = $org_url;
?>
<!-- ●●●●●●●●●● 레이어팝업 -->
<div class="cm_ly_pop_tp" id="share_email" style="width:550px;display:none;">
	
	<!--  레이어팝업 공통타이틀 영역 -->
	<div class="title_box">이메일로 알려주기<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	
	<form name="smsFrm" id="share_email_form">
	<input type="hidden" name="pcode" value="<?=$row_product['code']?>">
	<input type="hidden" name="type" value="mail">
	<input type="hidden" name="url" value="<?=$app_shorten_url?>">
	<!-- 하얀색박스공간 -->
	<div class="inner_box">
		
		<!-- 설명글 -->
		<div class="top_txt">
			<strong>오늘의 멋진 딜 소식을 많은 분들에게 알려주세요!</strong><br/>
			다음 정보를 입력하고 보내기를 클릭하면 메일이 전송됩니다.<br/>
		</div>	
		
		<!-- 폼들어가는곳 -->
		<div class="form_box">
			<ul>
				<li>
					<span class="opt">받는사람 이름</span>
					<div class="value"><input type="text" name="toName" class="input_design icon_name" value="" placeholder="받으실 분의 이름을 입력해주세요." /></div>
				</li>
				<li>
					<span class="opt">받는사람 이메일</span>
					<div class="value"><input type="text" name="toMail" class="input_design icon_email" value="" placeholder="받으실 분의 이메일주소 (아이디@주소)" /></div>
				</li>
				<li>
					<span class="opt">보내는이 이름</span>
					<div class="value"><input type="text" name="fromName" class="input_design icon_name" value="<?=$row_member['name']?>" placeholder="보내는 분의 이름을 입력해주세요." /></div>
				</li>
				<li>
					<span class="opt">보내는이 이메일</span>
					<div class="value"><input type="text" name="fromMail" class="input_design icon_email" value="<?=$row_member['email']?>" placeholder="보내는 분의 이메일주소 (아이디@주소)" /></div>
				</li>
				<li>
					<span class="opt">전달 소식내용</span>
					<div class="value"><textarea name="toContent" rows="" cols="" class="textarea_design"><?=$row_product['name']?></textarea></div>
				</li>
			</ul>
		</div>
		<!-- / 폼들어가는곳 -->
		
		<!-- SSJ: 2017-09-20 상품메일 개인정보수집동의 추가 -->
		<div class="cm_step_agree no_bg" style="clear:both;margin-bottom:0;">
			<textarea cols="" rows="" name="" readonly><?=stripslashes($row_company['sendmail_agree'])?></textarea>
			<label><input type="checkbox" name="sendmail_agree" id="sendmail_agree" class="" value="Y" /> 위 방침을 읽고 동의합니다.</label>
		</div>
		<!-- // SSJ: 2017-09-20 상품메일 개인정보수집동의 추가 -->

		<!-- ●●●●●●●●●● 가운데정렬버튼 -->
		<div class="cm_bottom_button">
			<ul>
				<li><span class="button_pack"><a href="#none" onclick="return false;" title="" class="close btn_md_black">닫기</a></span></li>
				<li><span class="button_pack"><a href="#none" onclick="return false;" title="" class="sns_mail_submit btn_md_color">보내기</a></span></li>
			</ul>
		</div>
		<!-- / 가운데정렬버튼 -->  

	</div>
	<!-- / 하얀색박스공간 -->
	</form>

</div>
<!-- / 레이어팝업 -->


<script>
$(document).ready(function(){
	$(".sns_mail_submit").on('click',function() {
		// 숫자 콤마와, 기본값을 제거한다.
		formSubmitSet();
		$("#share_email_form").submit();
	});
	$("#share_email_form").validate({
		rules: {
			sendmail_agree: { required: true },
			fromName: { required: true },
			fromMail:{ required: true, email: true },
			toName:{ required: true },
			toMail:{ required: true, email: true },
			text:{ required: true }
		},
		messages: {
			sendmail_agree : { required: "개인정보수집 및 이용에 동의해 주시기 바랍니다." },
			fromName : { required: "보내는 사람 이름을 입력해주세요." },
			fromMail: { required: "보내는 사람 이메일을 입력하세요.", email: "올바른 이메일 주소를 입력하세요." },
			toName: { required: "받는 사람 이름을 입력하세요." },
			toMail: { required: "받는 사람 이메일을 입력하세요.", email: "올바른 이메일 주소를 입력하세요." },
			text: { required: "전송 메세지를 입력하세요." }
		},
		submitHandler : function(form) {
			// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
			var data = $('#share_email_form').serialize();
			$.ajax({
				data: data,
				type: 'POST',
				cache: false,
				url: '/pages/product.view.email.pro.php',
				success: function(data) {
					if( data == 'OK' ) {
						alert('추천메일을 발송했습니다.');
						$('#share_email .close').trigger('click');
					} else { alert(data); }
				},
				error:function(request,status,error){
					alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}
	});
});
</script>
