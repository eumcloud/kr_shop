<?PHP
	include_once("inc.header.php");

	$r = _MQ_assoc("select * from m_sms_set");
	foreach($r as $k => $v) {
		$uid = $v[smskbn];
		${$uid."_status"} = $v[smschk];
		${$uid."_text"} = $v[smstext];
		${$uid."_kakao_status"} = $v['kakao_status'];// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
	}

	function php_sms_byte_calc($str){

		$pattern =  '/[가-힣]+/u'; // 한글 (2byte 계산)
		preg_match_all($pattern, $str, $match);
		$comment_mb_string = implode('', $match[0]);

		$pattern = '/[^가-힣]+/u'; // 특수문자 (1byte 계산)
		preg_match_all($pattern, $str , $match);
		$comment_special_string = implode('', $match[0]);

		$real_length = strlen($str) - strlen($comment_mb_string) - strlen($comment_special_string) + mb_strlen($comment_mb_string, 'utf-8')  * 2 + mb_strlen($comment_special_string, 'utf-8');

		return $real_length;
	}

?>


<!-- SMS기본설정 -->
<!-- SMS기본설정 -->
<div class="new_sms_default" style="min-width:1150px">
    <span class="opt">SMS계정설정</span>
	<div class="value">
		<form name="form_sms_info">
		<input type="hidden" name="mode" value="sms_info"/>
			<span class="txt">아이디</span>
			<span class="input_box"><input type="text" name="_smsid" required class="input_design" style="width:150px" value="<?=$row_setup[sms_id]?>" /></span>
			<span class="txt">비밀번호</span>
			<span class="input_box"><input type="text" name="_smspw" class="input_design" style="width:150px" value="" placeholder="변경 시 입력하세요" /></span>
			<!-- <span class="txt_color">현재아이피 :  121.78.246.122</span> -->

			<div class="button_box">
				<span class="shop_btn_pack btn_input_red"><input type="submit" name="" class="input_large" value="계정설정저장" /></span>
				<span class="shop_btn_pack"><a href="http://mobitalk.gobeyond.co.kr/" class="large gray" title="" target="_blank" >SMS 충전관리</a></span>
				<?php
				$SMSUser = onedaynet_sms_user();
				if($SMSUser['code'] == 'U04') {
					echo '<span class="shop_btn_pack"><a href="./_config.default.form.php#sms_send_tel" class="large white" title="" >발신번호수정</a></span>';
				}
				if($SMSUser['code'] == 'U00') echo '<span class="shop_btn_pack"><a href="http://mobitalk.gobeyond.co.kr/" target="_blank" class="large white" title="" >잔여: '.number_format($SMSUser['data']).'건</a></span>';
				?>
			</div>
		</form>
	</div>
	<script>
	$(document).ready(function(){
		$('form[name=form_sms_info]').on('submit', function(e){ e.preventDefault();
			//if($(this).valid()) {
				if($('input[name=_smspw]').val() == '') { alert('비밀번호를 입력바랍니다.'); $('input[name=_smspw]').focus(); return; }
				var data = $(this).serialize();
				$.ajax({
					data: data,
					type: 'POST',
					cache: false,
					url: './_config.sms.ajax.php',
					success: function(data) {
						if($.trim(data)=='OK') { alert('성공적으로 저장되었습니다.'); location.reload();  } else { alert(data); }
					},
					error:function(request,status,error){
						alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					}
				});
			//}
		});
	});
	</script>
</div>
<div class="guide_text"><span class="ic_blue"></span><span class="blue">SMS 관리시 반드시 적용할 서버의 아이피를 등록하시기 바랍니다. 아이피가 등록되지 않은 서버에서는 문자가 발송되지 않습니다. [현재 아이피 : <strong style="cursor: pointer; outline: none;" class="_copy"><?=$_SERVER[SERVER_ADDR]?></strong>]</span></div>
<?php
if($SMSUser['code'] != 'U00') {
    $Uniq = uniqid();
    echo '<script>$(document).ready(function () {setInterval("$(\'.blink_text_'.$Uniq.'\').fadeOut().fadeIn();",1000);});</script>';
    echo '<div style="margin: 5px 20px;" class="blink_text_'.$Uniq.'">'._DescStr('<b style="font-size:15px; color:#ff0000">'.$SMSUser['data'].'</b>', 'orange').'</div>';
}
?>



<form name="frm" method="POST" action="_config.sms.pro.php" enctype="multipart/form-data">
<input type="hidden" name="editing" value="N"/>
<input type="hidden" name="uid" value=""/>
<input type="hidden" name="a_uid" value=""/>

<!-- 문자내용 세부설정 -->
<div class="new_sms_form">

	<!-- 문자항목들 -->
	<div class="aside_opt_box">

		<ul>
		<?
			foreach($arr_sms_text_type as $uid => $value) {
				$_me_check = ${$uid."_status"}=='y'?true:false;
		?>
			<li class="sms_types" id="sms_item_<?=$uid?>" data-type="<?=$uid?>">
				<!-- hit할 경우 나오는 아이콘 -->
				<span class="hit_icon"><img src="./images/new_sms/opt_ic.png" alt="" /></span>
				<?php if(!${$uid."_text"}) { // 문자내용 미리보기가 있는 경우만 출력 ?>
				<!-- 문자내용미리보기 -->
				<div class="quick_preview">
					<span class="edge"><img src="./images/new_sms/prev_arrow.gif" alt="" /></span>
					<dl>
						<? if($_me_check) { ?>
						<dt style="padding: 5px 0;"><?=${$uid."_text"}?></dt>
						<? } ?>
					</dl>
				</div>
				<?php } ?>
				<a href="#none" onclick="return false;" class="link">
					<?=$value?>
					<!-- 전송하는지, 전송안하는지 체크하는 부분 me,checked_me는 회원용;;  ad,checked_ad는 관리자용 클래스 -->
					<span class="send_check">
						<?php
							// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
							$_me_kakao_check = ${$uid."_kakao_status"}=='Y'?true:false;
						?>
						<span class="me <?=$_me_check?'checked_me':''?> <?=$_me_check && $_me_kakao_check?'kakao_checked':''?>"><span class="icon"></span>발송</span>
						<?// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----?>
					</span>
				</a>
			</li>
		<? } ?>
		</ul>

		<!-- <div class="button_box">
			<span class="shop_btn_pack btn_input_red"><input type="submit" name="" class="input_large" value="문자내용 저장하기" /></span>
		</div> -->

	</div>


	<!-- 휴대폰 한번 감싸기 -->
	<div class="new_sms_phone_wrap">

		<!-- 패치묶음 :: SMS -->
		<div class="if_in_box">
			<div class="new_sms_phone">
				<div class="body">
					<div class="inner">

						<!-- 전송여부체크 -->
						<div class="check me"><label><input type="checkbox" name="m_status" class="m_status" value="y" checked />메세지를 전송합니다.</label></div>

						<!-- 제목 lms, mms : placeholder ie하위버전 체크바랍니다 -->
						<div class="title_box"><input type="text" class="input_design m_title" name="m_title" placeholder="문자메세지의 제목을 입력하세요" style="outline:0;" /></div>

						<!-- 이 상자가 스크롤이 생기는 부분입니다 -->
						<div class="fix_box m_box textarea_wrap" style="cursor: text;">
							<!-- 메세지내용 -->
							<div class="message_box">
								<textarea name="m_text" rows="" cols="" class="m_text chk_length textarea_content" tabindex="1" data-ma="m" placeholder="" style="outline:0;resize:none;"></textarea>
								<div class="bubble_bottom"></div>
							</div>
						</div>

						<!-- byte검사 문자구분 -->
						<div class="total_box"><a href="#none" class="btn_rollback" onclick="return false;" title="회원 문구를 초기 세팅상태로 되돌립니다." data-ma="m">기본문구</a><span class="m_len" style="color:inherit;">0</span> byte <b class="m_type">SMS</b></div>

						<!-- 이미지첨부 css로 손가락 표시가 잘 안되는데 크롬에서 손가락 표시(클릭표시) 될 수 있도록 하세요.. -->
						<div class="file_box">
							<div class="input_file_sms">
								<a href="#none" onclick="return false;" class="buttonImg_delete realFile_delete" data-ma="m" data-delete="Y" title="이미지삭제">&nbsp;</a>
								<input type="text" id="<m_fakeFileTxt></m_fakeFileTxt>" class="fakeFileTxt" readonly="readonly" disabled>
								<div class="fileDiv" title="이미지첨부">
									<input type="button" class="buttonImg" value="이미지첨부" />
									<input type="file" accept="image/jpeg" name="m_file" class="realFile m_file" data-ma="m" onchange="javascript:document.getElementById('m_fakeFileTxt').value = this.value.match(/[^\/\\]+$/)" />
									<input type="hidden" name="m_file_OLD" class="realFile_old m_file_OLD" data-ma="m" value=""/>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="bottom"></div>
			</div>
		</div>

		<!-- 패치묶음 :: 알림톡 -->
		<?// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----?>
		<div class="if_in_box">
			<div class="new_sms_phone if_kakao"><!-- 알림톡 경우 클래스값 추가 if_kakao -->
				<div class="body">
					<div class="inner">

						<!-- 전송여부체크 -->
						<div class="check me"><label><input type="checkbox" name="kakao_status" class="kakao_status" value="Y" />알림톡을 전송합니다.</label></div>

						<div class="title_box kakao_type"><input type="text" class="input_design " name="kakao_templet_num" placeholder="알림톡 템플릿 고유번호 입력" style="outline:0; "></div>
						<div class="title_box kakao_type"><input type="text" class="input_design " name="kakao_add1" placeholder="알림톡 치환용 추가정보1 입력" style="outline:0; "></div>
						<div class="title_box kakao_type"><input type="text" class="input_design " name="kakao_add2" placeholder="알림톡 치환용 추가정보2 입력" style="outline:0; "></div>
						<div class="title_box kakao_type"><input type="text" class="input_design " name="kakao_add3" placeholder="알림톡 치환용 추가정보3 입력" style="outline:0; "></div>
						<div class="title_box kakao_type"><input type="text" class="input_design " name="kakao_add4" placeholder="알림톡 치환용 추가정보4 입력" style="outline:0; "></div>
						<div class="title_box kakao_type"><input type="text" class="input_design " name="kakao_add5" placeholder="알림톡 치환용 추가정보5 입력" style="outline:0; "></div>
						<div class="title_box kakao_type"><input type="text" class="input_design " name="kakao_add6" placeholder="알림톡 치환용 추가정보6 입력" style="outline:0; "></div>
						<div class="title_box kakao_type"><input type="text" class="input_design " name="kakao_add7" placeholder="알림톡 치환용 추가정보7 입력" style="outline:0; "></div>
						<div class="title_box kakao_type"><input type="text" class="input_design " name="kakao_add8" placeholder="알림톡 치환용 추가정보8 입력" style="outline:0; "></div>

					</div>
				</div>
				<div class="bottom"></div>
			</div>
		</div>


		<!-- 2015-09-10 SMS발송옵션 설정 LDD006 {-->
		<div class="new_send_type_set">
			<dl>
				<dt>SMS발송옵션 설정</dt>
				<dd>
					<label>
						<input type="radio" name="m_send_type" class="m_send_type" value="D" checked>
						<span class="txt">일반발송</span>
						<span class="exp">90byte 이내: SMS 발송<br/>90byte 이상: LMS발송</span>
					</label>
				</dd>
				<dd>
					<label>
						<input type="radio" name="m_send_type" class="m_send_type" value="S">
						<span class="txt">SMS 단일발송</span>
						<span class="exp">90byte를 초과하는<br/>내용을 제외하고 발송</span>
					</label>
				</dd>
				<dd>
					<label>
						<input type="radio" name="m_send_type" class="m_send_type" value="M">
						<span class="txt">SMS 분할발송</span>
						<span class="exp">90byte를 초과 하였을 경우<br/>90byte를 기준으로 <br/>분할 하여 다수 발송</span>
					</label>
				</dd>
			</dl>
		</div>
		<!--} 2015-09-10 SMS발송옵션 설정 LDD006 -->


	</div>
	<!-- / 휴대폰 한번 감싸기 -->


	<!-- 패치묶음 :: 저장버튼 -->
	<div class="button_box">
		<span class="shop_btn_pack btn_input_red"><input type="submit" class="input_large" value="문자내용 저장하기" /></span>
	</div>




	<!-- 패치묶음 :: 도움말+치환자 -->
	<?// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----?>
	<div class="new_guide">
		<ul class="ul">
			<li class="li">
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">수정을 원하는 항목을 선택하신 후 문자내용이나 전송설정을 변경하신 후 <strong>문자내용 저장하기</strong>를 꼭 눌러주세요.</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">문자메세지의 제목은 LMS, MMS의 경우에만 전송됩니다.</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">MMS 전송시 이미지는 60kb 이하로 등록하세요.</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">이미지는 JPG 포멧만 업로드 가능합니다.</span></div>

				<div class="guide_text"><span class="ic_blue"></span><span class="blue">`알림톡을 전송합니다`에 체크할 경우 <strong class='orange'>문자메시지 대신 알림톡으로 발송</strong>됩니다.</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue"><strong class='orange'>알림톡을 전송하기 위해서는 반드시 `메시지를 전송합니다`에 체크</strong>가 되어 있어야 합니다.</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">알림톡은 단독발송이 불가능 하며 <strong class='orange'>사용자 또는 관리자 문자 발송을 사용하는 상태에서만 발송 가능</strong>합니다.</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">템플릿을 이용한 알림톡 발송이 실패할 경우 일반 문자메시지로 <strong class='orange'>대체 발송</strong>되는데, 이 경우 <strong class='orange'>알림톡 요금이 아닌 문자메시지 요금이 적용</strong>됩니다.</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue"><strong class='orange'>대체 발송 시 내용의 길이가 90byte 이하일 경우 SMS, 이상일 경우 LMS 요금이 과금됩니다.</strong></span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">
					<strong class='orange'>알림톡을 발송하기 위해서는 다음과 같은 진행절차를 따릅니다.</strong><br>
					1. 모비톡 정보수정 > 발신프로필 관리를 통해 플러스친구 검색용 아이디, 사업자등록증 첨부 (3~4일 정보 소요됩니다.)<br>
					2. 카카오톡 승인 후 발신프로필 키 확인<br>
					3. 알림톡 템플릿 등록 후 서비스 이용
				</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">
					<strong class='orange'>*플러스 친구란?</strong><br>
					1. 카카오톡 계정을 기반으로 개설 가능한 카카오톡 비즈니스용 아이디입니다.<br>
					2. 비즈뿌리오에서 제공하는 카카오톡 비즈메시지(알림톡, 친구톡)는 모두 플러스 친구와 비즈뿌리오 아이디가 연동 되어야 사용하실 수 있습니다.<br>
					3. 검색엔진에서 ‘플러스 친구’ 검색 또는 플러스 친구 관리자센터 (https://center-pf.kakao.com) 접속 후 ‘플러스친구 개설하기’를 진행해주세요.
				</span></div>
			</li>
			<li class="li">
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">치환자를 사용할 경우 실제 발송되는 글자수와 차이가 있을 수 있습니다.</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">문자메세지 종류에 따라 적용되지 않는 치환자가 있을 수 있습니다. 기본문구를 참고하세요.</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue"><strong>치환자</strong>: 아래 치환자를 끌어서 입력폼에 놓으면 추가됩니다.</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">
					사이트 및 회원, 문의 등 일반 치환자 :
					<ul class="replace_item">
						<li data-text="{{사이트명}}"><strong>{{사이트명}}</strong></li>
						<li data-text="{{회원명}}"><strong>{{회원명}}</strong></li>
						<li data-text="{{회원아이디}}"><strong>{{회원아이디}}</strong></li>
						<li data-text="{{상품문의}}"><strong>{{상품문의}}</strong></li>
					</ul>
				</span></div>
				<div class="guide_text"><span class="ic_blue"></span><span class="blue">
					결제 및 쿠폰 , 배송 등 주문 치환자 :
					<ul class="replace_item">
						<li data-text="{{주문번호}}"><strong>{{주문번호}}</strong></li>
						<li data-text="{{구매자명}}"><strong>{{구매자명}}</strong></li>
						<li data-text="{{결제금액}}"><strong>{{결제금액}}</strong></li>
						<li data-text="{{주문일}}"><strong>{{주문일}}</strong></li>
						<li data-text="{{결제일}}"><strong>{{결제일}}</strong></li>
						<li data-text="{{입금계좌정보}}"><strong>{{입금계좌정보}}</strong></li>
						<li data-text="{{전체주문상품명}}"><strong>{{전체주문상품명}}</strong></li>
						<li data-text="{{주문상품명}}"><strong>{{주문상품명}}</strong></li>
						<li data-text="{{주문상품수}}"><strong>{{주문상품수}}</strong></li>
						<li data-text="{{쿠폰번호}}"><strong>{{쿠폰번호}}</strong></li>
						<li data-text="{{송장번호}}"><strong>{{송장번호}}</strong></li>
						<li data-text="{{택배사명}}"><strong>{{택배사명}}</strong></li>
						<li data-text="{{배송일}}"><strong>{{배송일}}</strong></li>
					</ul>
				</span></div>
			</li>
		</ul>
	</div>
	<?// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----?>



</div>


</form>



<link rel='stylesheet' href='../../include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="../../include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script>

// detect IE version ( returns false for non-IE browsers )
var ie = function(){for(var e=3,n=document.createElement("div"),r=n.all||[];n.innerHTML="<!--[if gt IE "+ ++e+"]><br><![endif]-->",r[0];);return e>4?e:!e}();
if(ie!==false && ie<10) { $('.input_file_sms').addClass('old-ie'); } else { $('.input_file_sms').removeClass('old-ie'); }

// lastIndexOf function
Array.prototype.lastIndexOf||(Array.prototype.lastIndexOf=function(r){"use strict";if(null==this)throw new TypeError;var t=Object(this),e=t.length>>>0;if(0===e)return-1;var a=e;arguments.length>1&&(a=Number(arguments[1]),a!=a?a=0:0!=a&&a!=1/0&&a!=-(1/0)&&(a=(a>0||-1)*Math.floor(Math.abs(a))));for(var n=a>=0?Math.min(a,e-1):e-Math.abs(a);n>=0;n--)if(n in t&&t[n]===r)return n;return-1});

// 글자 바이트수로 자르기
function cutByte(r,t){var e=r,n=0,c=r.length;for(i=0;c>i;i++){if(n+=chr_byte(r.charAt(i)),n==t-1){e=2==chr_byte(r.charAt(i+1))?r.substring(0,i+1):r.substring(0,i+2);break}if(n==t){e=r.substring(0,i+1);break}}return e}function chr_byte(r){return escape(r).length>4?2:1}


// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
// 치환될 바이트수 정의
var replace_byte = <?
	$_byte = array(
		"{{사이트명}}" => array( php_sms_byte_calc('{{사이트명}}') , php_sms_byte_calc($row_setup[site_name],"EUC-KR") ),
		"{{회원명}}" => array( php_sms_byte_calc('{{회원명}}') , 8 ),
		"{{회원아이디}}" => array( php_sms_byte_calc('{{회원아이디}}') , 8 ),
		"{{상품문의}}" => array( php_sms_byte_calc('{{상품문의}}') , 50 ),

		"{{주문번호}}" => array( php_sms_byte_calc('{{주문번호}}') , 17 ),
		"{{구매자명}}" => array( php_sms_byte_calc('{{구매자명}}') , 8 ),
		"{{결제금액}}" => array( php_sms_byte_calc('{{결제금액}}') , 8 ),
		"{{주문일}}" => array( php_sms_byte_calc('{{주문일}}') , 10 ),
		"{{결제일}}" => array( php_sms_byte_calc('{{결제일}}') , 10 ),
		"{{입금계좌정보}}" => array( php_sms_byte_calc('{{입금계좌정보}}') , 45 ),

		"{{전체주문상품명}}" => array( php_sms_byte_calc('{{전체주문상품명}}') , 50 ),
		"{{주문상품명}}" => array( php_sms_byte_calc('{{주문상품명}}') , 30 ),
		"{{주문상품수}}" => array( php_sms_byte_calc('{{주문상품수}}') , 2 ),

		"{{쿠폰번호}}" => array( php_sms_byte_calc('{{쿠폰번호}}') , 17 ),

		"{{송장번호}}" => array( php_sms_byte_calc('{{송장번호}}') , 15 ),
		"{{택배사명}}" => array( php_sms_byte_calc('{{택배사명}}') , 15 ),
		"{{배송일}}" => array( php_sms_byte_calc('{{배송일}}') , 10 )
	);
	echo json_encode($_byte);
?>;
// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----


$(document).ready(function(){

	// 치환자 끌어놓기
	$('.replace_item li').disableSelection();
	$(".replace_item li").draggable({helper: 'clone',
		 start: function(e, ui)
		 {
			var _w = ($(this).width()+1); // SSJ: 2017-09-28 넓이에 소수점이 포함될경우 클론의 텍스트가 두줄되는것 방지
			$(ui.helper).css({'width': _w + 'px'});
		 }
	});
	$(".textarea_wrap").droppable({ accept: ".replace_item li", drop: function(ev, ui) {
		$(this).find('.textarea_content').insertAtCaret(ui.draggable.data('text')); check_length();
	}});

	// 문자입력 폼
	$('.textarea_content').autosize();
	$('.textarea_wrap').on('click',function(){ $(this).find('.textarea_content').focus(); });

	$editing = $('input[name=editing]');
	$uid = $('input[name=uid]');

	$('.m_title').hide();
	// SMS 구분 선택하면 입력 페이지 로드
	$('.sms_types').on('click',function(){

		if($editing.val()=='Y') {
			if(!confirm("현재 수정중인 메세지가 있습니다. 저장하지 않고 계속할까요?")) { return false; }
			else { $editing.val('N'); }
		}

		$('.sms_types').removeClass('hit'); $(this).addClass('hit');
		var _type = $(this).data('type');
		$.ajax({
			data: { mode: 'load', type: _type },
			type: 'POST',
			cache: false,
			dataType: 'JSON',
			url: './_config.sms.ajax.php',
			success: function(data) {
				// 현재 SMS 구분
				$uid.val(data.member._uid);

				// 문구 출력
				$('.m_text').val(data.member._text);

				// 제목 출력
				$('.m_title').val(data.member._title);

				// 문구 placeholder 출력
				$('.m_text').attr({ 'placeholder' : data.member._name + ' - 회원에게 전송할 내용을 입력하세요' });

				// 발송타입 출력
				$('.m_send_type').removeAttr('checked');
				$('.m_send_type[value='+data.member._send_type+']').attr('checked', true);
				//console.log(data.member);

				// 발송여부 선택
				if(data.member._status=='y') { $('.m_status').prop('checked',true); } else { $('.m_status').prop('checked',false); }


				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				if(data.member.kakao_status=='Y') { $('.kakao_status').prop('checked',true); } else { $('.kakao_status').prop('checked',false); }
				$("input[name='kakao_templet_num']").val(data.member.kakao_templet_num);
				$("input[name='kakao_add1']").val(data.member.kakao_add1);
				$("input[name='kakao_add2']").val(data.member.kakao_add2);
				$("input[name='kakao_add3']").val(data.member.kakao_add3);
				$("input[name='kakao_add4']").val(data.member.kakao_add4);
				$("input[name='kakao_add5']").val(data.member.kakao_add5);
				$("input[name='kakao_add6']").val(data.member.kakao_add6);
				$("input[name='kakao_add7']").val(data.member.kakao_add7);
				$("input[name='kakao_add8']").val(data.member.kakao_add8);
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----


				// 첨부파일 있으면 출력
				$('.m_img').remove(); $('.m_file, .m_file_OLD').val('');
				if(data.member._file) {
					$('.m_box .message_box').prepend('<div class="img_box m_img"><a href="#none" onclick="return false;" data-ma="m" data-delete="Y" class="realFile_delete btn_delete" title="이미지삭제"><img src="./images/new_sms/btn_img_delete.png" alt="" /></a><img src="/upfiles/'+data.member._file+'" alt="" /></div>');
					$('.m_file_OLD').val(data.member._file);
				}

				// 콘솔에 출력
				//console.log(data);

				// 문자 타입
				check_length(true);
			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});

	});

	// 최초 페이지 로드시 첫 구분 클릭
	<? if($_GET[_uid]) { ?>$('.sms_types#sms_item_<?=$_GET[_uid]?>').trigger('click');<? } else { ?>$('.sms_types:first').trigger('click');<? } ?>

	// 문구, 제목 수정시 editing 상태 변경
	$('.m_text, .m_title').on('focus',function(){ $editing.val('Y'); });


	// 파일업로드 처리
	$(".realFile").change(function(){
		var ma = $(this).data('ma');
		if($(this).val().length > 0) {
			// 사이즈 체크
			if(this.files && this.files[0].size > 60*1024) { alert("업로드한 파일 크기가 너무 큽니다.\n60KB 이하로 등록하세요."); $(this).val(''); return false; }
			// 확장자 체크
			var validExtensions = ['jpg','jpeg'];
			var fileName = (ie!==false&&ie<10)?$(this).val().match(/[^\/\\]+$/):this.files[0].name;
			var fileNameExt = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined; fileNameExt = $.trim(fileNameExt);
			if($.inArray(fileNameExt, validExtensions) == -1){ alert('JPG 파일만 등록할 수 있습니다.'); $(this).val(''); return false; }
		}
		readURL(ma,this); $('.'+ma+'_box textarea').focus();
	});

	// 업로드한 파일 취소
	$('.textarea_wrap').on('click','.realFile_delete',function(){
		var ma = $(this).data('ma'), del = $(this).data('delete');
		if(confirm("이미지를 삭제하시겠습니까?")) {
			if(del == 'Y') { $('.'+ma+'_file_OLD').val('').trigger('change'); }
			$('.'+ma+'_file').val('').trigger('change'); $('#'+ma+'_fakeFileTxt').val('');
		} else { return false; }
	});

	// 업로드한 파일 취소 (ie8)
	$('.input_file_sms').on('click','.realFile_delete',function(){
		var ma = $(this).data('ma'), del = $(this).data('delete');
		if($('#'+ma+'_fakeFileTxt').val().length == 0) {
			alert('삭제할 이미지가 없습니다.'); return false;
		} else {
			if(confirm("이미지를 삭제하시겠습니까?")) {
				if(del == 'Y') { $('.'+ma+'_file_OLD').val('').trigger('reset').trigger('change'); }
				$('.'+ma+'_file').val('').trigger('reset').trigger('change'); $('#'+ma+'_fakeFileTxt').val('');
			} else { return false; }
		}
	});


	// 문구 작성할때 길이 체크
	$('.chk_length').on('keyup change',function() { check_length(); });


	// IP 복사
	$('._copy').on('click',function(){ $(this).prop('contentEditable',true).css({'cursor':'text'}); document.execCommand('selectAll',false,null); });
	$('._copy').on('blur',function(){ $(this).prop('contentEditable',false).css({'cursor':'pointer'}); $(this).text('<?=$_SERVER[SERVER_ADDR]?>'); });


	// 초기문구로 되돌리기
	$('.btn_rollback').on('click',function(){
		var ma = $(this).data('ma'), uid = $('input[name=uid]').val();
		var confirm_txt = ma=='a'?'관리자':'회원';
		if(confirm(confirm_txt + ' 문구를 초기 세팅상태로 되돌리겠습니까?')) {
			$.ajax({
				data: {'mode':'rollback','uid':uid,'ma':ma},
				type: 'POST',
				cache: false,
				url: './_config.sms.ajax.php',
				success: function(data) {
					$('.'+ma+'_img').remove();
					$('.'+ma+'_text').val(data);
					$('.'+ma+'_file').val(''); $('.'+ma+'_file_OLD').val('');
					$('.chk_length').trigger('change');
				},
				error:function(request,status,error){
					alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}
	});

});



// 문자 타입 체크 (sms / lms / mms)
function check_length(onoff) {
	$('.chk_length').each(function(){
		var len = 0, ma = $(this).data('ma'), height = $('.'+ma+'_box').height(), val = $(this).val();
		var current_type = $('.'+ma+'_type').text(), do_not_alert = onoff===true?true:false;

		// 글자수 계산
		if("Mozilla"!=navigator.appCodeName)len=$(this).val().length;else for(var i=0;i<$(this).val().length;i++)$(this).val().substr(i,1)>"~"?len+=2:len++;

		// 치환자 체크
		$.each(replace_byte,function(e,n){-1!=val.indexOf(e)&&(len=len-n[0]+n[1])});

		if(len > 2000) {
			alert('최대 2,000 바이트까지 보내실 수 있습니다.'); val = cutByte(val,1990); $(this).val(val); len = 0;
			// 글자수 및 치환자 재계산
			if("Mozilla"!=navigator.appCodeName)len=$(this).val().length;else for(var i=0;i<$(this).val().length;i++)$(this).val().substr(i,1)>"~"?len+=2:len++;
			$.each(replace_byte,function(e,n){-1!=val.indexOf(e)&&(len=len-n[0]+n[1])});
		}

		$('.'+ma+'_len').text(String(len).comma());
		if($.trim($('.'+ma+'_file').val()).length == 0 && $.trim($('.'+ma+'_file_OLD').val()).length == 0)  {
			if(len > 90) {
				// LMS
				if(current_type=='SMS' && do_not_alert===false) { alert('LMS로 전환되며 추가요금이 발생합니다.'); }
				$('.'+ma+'_type').text('LMS');
				if($('.'+ma+'_title').is(':visible')) { }
				else { $('.'+ma+'_title').show(); $('.'+ma+'_box').height(height - 41); }
			} else {
				// SMS
				$('.'+ma+'_type').text('SMS');
				if($('.'+ma+'_title').is(':visible')) { $('.'+ma+'_title').hide(); $('.'+ma+'_box').height(height + 41); }
			}
		} else {
			// MMS
			if(current_type!='MMS' && do_not_alert===false) { alert('MMS로 전환되며 추가요금이 발생합니다.'); }
			$('.'+ma+'_type').text('MMS');
			if($('.'+ma+'_title').is(':visible')) { }
			else { $('.'+ma+'_title').show(); $('.'+ma+'_box').height(height - 41); }
		}
	});
}

// 파일업로드 처리
function readURL(ma,input) {
	if(ie!==false&&ie<10) {
		//alert($('.'+ma+'_file').val());
		if($('.'+ma+'_file').val().length > 0) {
			$('.'+ma+'_img').remove();
			$('.'+ma+'_text').focus();
			if($('.'+ma+'_title').is(':visible')) { }
			else { $('.'+ma+'_title').show(); $('.'+ma+'_box').height($('.'+ma+'_box').height() - 41); }
			check_length();
		} else { $('.'+ma+'_img').remove(); check_length(); }
	} else {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('.'+ma+'_img').remove();
				$('.'+ma+'_box .message_box').prepend('<div class="img_box '+ma+'_img"><a href="#none" onclick="return false;" data-ma="'+ma+'" class="realFile_delete btn_delete" title="이미지삭제"><img src="./images/new_sms/btn_img_delete.png" alt="" /></a><img src="'+e.target.result+'" alt="" /></div>');
				$('.'+ma+'_text').focus();
			}
			reader.readAsDataURL(input.files[0]);
			if($('.'+ma+'_title').is(':visible')) { }
			else { $('.'+ma+'_title').show(); $('.'+ma+'_box').height($('.'+ma+'_box').height() - 41); }
			check_length();
		} else { $('.'+ma+'_img').remove(); check_length(); }
	}
}

// textarea Auto Height
!function(e){var t,o={className:"autosizejs",id:"autosizejs",append:"\n",callback:!1,resizeDelay:10,placeholder:!0},i='<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; padding: 0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',a=["fontFamily","fontSize","fontWeight","fontStyle","letterSpacing","textTransform","wordSpacing","textIndent","whiteSpace"],n=e(i).data("autosize",!0)[0];n.style.lineHeight="99px","99px"===e(n).css("lineHeight")&&a.push("lineHeight"),n.style.lineHeight="",e.fn.autosize=function(i){return this.length?(i=e.extend({},o,i||{}),n.parentNode!==document.body&&e(document.body).append(n),this.each(function(){function o(){var t,o=window.getComputedStyle?window.getComputedStyle(u,null):null;o?(t=parseFloat(o.width),("border-box"===o.boxSizing||"border-box"===o.webkitBoxSizing||"border-box"===o.mozBoxSizing)&&e.each(["paddingLeft","paddingRight","borderLeftWidth","borderRightWidth"],function(e,i){t-=parseFloat(o[i])})):t=p.width(),n.style.width=Math.max(t,0)+"px"}function s(){var s={};if(t=u,n.className=i.className,n.id=i.id,d=parseFloat(p.css("maxHeight")),e.each(a,function(e,t){s[t]=p.css(t)}),e(n).css(s).attr("wrap",p.attr("wrap")),o(),window.chrome){var r=u.style.width;u.style.width="0px";{u.offsetWidth}u.style.width=r}}function r(){var e,a;t!==u?s():o(),n.value=!u.value&&i.placeholder?p.attr("placeholder")||"":u.value,n.value+=i.append||"",n.style.overflowY=u.style.overflowY,a=parseFloat(u.style.height)||0,n.scrollTop=0,n.scrollTop=9e4,e=n.scrollTop,d&&e>d?(u.style.overflowY="scroll",e=d):(u.style.overflowY="hidden",c>e&&(e=c)),e+=z,Math.abs(a-e)>.01&&(u.style.height=e+"px",n.className=n.className,w&&i.callback.call(u,u),p.trigger("autosize.resized"))}function l(){clearTimeout(h),h=setTimeout(function(){var e=p.width();e!==b&&(b=e,r())},parseInt(i.resizeDelay,10))}var d,c,h,u=this,p=e(u),z=0,w=e.isFunction(i.callback),f={height:u.style.height,overflow:u.style.overflow,overflowY:u.style.overflowY,wordWrap:u.style.wordWrap,resize:u.style.resize},b=p.width(),g=p.css("resize");p.data("autosize")||(p.data("autosize",!0),("border-box"===p.css("box-sizing")||"border-box"===p.css("-moz-box-sizing")||"border-box"===p.css("-webkit-box-sizing"))&&(z=p.outerHeight()-p.height()),c=Math.max(parseFloat(p.css("minHeight"))-z||0,p.height()),p.css({overflow:"hidden",overflowY:"hidden",wordWrap:"break-word"}),"vertical"===g?p.css("resize","none"):"both"===g&&p.css("resize","horizontal"),"onpropertychange"in u?"oninput"in u?p.on("input.autosize keyup.autosize",r):p.on("propertychange.autosize",function(){"value"===event.propertyName&&r()}):p.on("input.autosize",r),i.resizeDelay!==!1&&e(window).on("resize.autosize",l),p.on("autosize.resize",r),p.on("autosize.resizeIncludeStyle",function(){t=null,r()}),p.on("autosize.destroy",function(){t=null,clearTimeout(h),e(window).off("resize",l),p.off("autosize").off(".autosize").css(f).removeData("autosize")}),r())})):this}}(jQuery||$);

// paste text at cursor position
$.fn.insertAtCaret=function(t){return this.each(function(){if(document.selection)this.focus(),sel=document.selection.createRange(),sel.text=t,this.focus();else if(this.selectionStart||"0"==this.selectionStart){var s=this.selectionStart,e=this.selectionEnd,i=this.scrollTop;this.value=this.value.substring(0,s)+t+this.value.substring(e,this.value.length),this.focus(),this.selectionStart=s+t.length,this.selectionEnd=s+t.length,this.scrollTop=i}else this.value+=t,this.focus()})};

</script>

<?PHP
	include_once("inc.footer.php");
?>