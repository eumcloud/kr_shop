<?php
include_once("inc.header.php");

/**
 *
 * [일회성] 조건충족을 하지 못하면 페이지 block
 * @return html
 *
 */
function sms_result_msg() {
    global $SMSUser;

    if($SMSUser['code'] == 'U01') { // 아이디 또는 비밀번호가 누락되었습니다.
        $ErrorMSG = $SMSUser['data'];
        $btn_msg = '수정하기';
        $btn_url = './_config.sms.form.php';
        $btn_target = '_self';
        $_trigger = 'on';
    }
    else if($SMSUser['code'] == 'U02') { // 잘못된 계정정보입니다.
        $ErrorMSG = $SMSUser['data'];
        $btn_msg = '수정하기';
        $btn_url = './_config.sms.form.php';
        $btn_target = '_self';
        $_trigger = 'on';
    }
    else if($SMSUser['code'] == 'U03') { // 등록되지 않은 아이피 입니다.
        $ErrorMSG = $SMSUser['data'];
        $btn_msg = '수정하기';
        $btn_url = './_config.sms.form.php';
        $btn_target = '_self';
        $_trigger = 'on';
    }
    else if($SMSUser['code'] == 'U04') { // 유효하지 않은 발신번호 입니다.
        $ErrorMSG = $SMSUser['data'];
        $btn_msg = '수정하기';
        $btn_url = './_config.default.form.php#sms_send_tel';
        $btn_target = '_self';
        $_trigger = 'on';
    }
    else if($SMSUser['code'] == 'U05') { // 발신번호 등록 후 이용가능 합니다.
        $ErrorMSG = $SMSUser['data'];
        $btn_msg = '수정하기';
        $btn_url = 'http://mobitalk.gobeyond.co.kr/pages/customer_modify.form.php';
        $btn_target = '_blank';
        $_trigger = 'on';
    }
    else if($SMSUser['code'] == 'U06') { // 발신번호 상태가 (대기/반려/만료) 입니다.
        $ErrorMSG = $SMSUser['data'];
        $btn_msg = $btn_url = $btn_target = '';
        $_trigger = 'on';
    }
    else if($SMSUser['code'] == 'U00' && $SMSUser['data'] <= 0) { // 잔액부족
        $ErrorMSG = '충전금액이 부족합니다.';
        $btn_msg = '충전하기';
        $btn_url = 'http://mobitalk.gobeyond.co.kr/';
        $btn_target = '_blank';
        $_trigger = 'on';
    }
    if($_trigger == 'on') {
        $Opacity = 8;
        $Uniq = uniqid();
        echo '<script>$(document).ready(function () {setInterval("$(\'.blink_text_'.$Uniq.'\').fadeOut().fadeIn();",1000);});</script>';
        echo '
            <div style="text-align:center;clear:both; ">
                <div style="max-width: 1638px; width: 100%; height:97%; background-color:#fff;position:absolute;z-index:99; opacity: 0.'.$Opacity.';-ms-filter:\'progid:DXImageTransform.Microsoft.Alpha(Opacity='.$Opacity.'0)\';filter: alpha(opacity='.$Opacity.'0);-moz-opacity: 0.'.$Opacity.';-khtml-opacity: 0.'.$Opacity.';"></div>
                <div class="button_box" style="position:absolute; z-index:100; top:45%; left:490px; background-color:#43464F; padding:5px; border:1px solid #1D1F24; width:270px; padding: 30px 0">
                    '.($btn_msg?'<span class="shop_btn_pack" style="float:none;"><a href="'.$btn_url.'" class="large red" target="'.$btn_target.'">'.$btn_msg.'</a></span>
                    <div>':null).'
                    <div style="color:#fff; font-weight:600; font-size:13px; margin-top:10px" class="blink_text_'.$Uniq.'">✘ '.$ErrorMSG.'</div>
                    '.($btn_msg?'</div>':null).'
                </div>
            </div>
        ';
    }
}
$SMSUser = onedaynet_sms_user();
sms_result_msg();
// $sms_split
?>
<script>
!function(e){var t,o={className:"autosizejs",id:"autosizejs",append:"\n",callback:!1,resizeDelay:10,placeholder:!0},i='<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; padding: 0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',a=["fontFamily","fontSize","fontWeight","fontStyle","letterSpacing","textTransform","wordSpacing","textIndent","whiteSpace"],n=e(i).data("autosize",!0)[0];n.style.lineHeight="99px","99px"===e(n).css("lineHeight")&&a.push("lineHeight"),n.style.lineHeight="",e.fn.autosize=function(i){return this.length?(i=e.extend({},o,i||{}),n.parentNode!==document.body&&e(document.body).append(n),this.each(function(){function o(){var t,o=window.getComputedStyle?window.getComputedStyle(u,null):null;o?(t=parseFloat(o.width),("border-box"===o.boxSizing||"border-box"===o.webkitBoxSizing||"border-box"===o.mozBoxSizing)&&e.each(["paddingLeft","paddingRight","borderLeftWidth","borderRightWidth"],function(e,i){t-=parseFloat(o[i])})):t=p.width(),n.style.width=Math.max(t,0)+"px"}function s(){var s={};if(t=u,n.className=i.className,n.id=i.id,d=parseFloat(p.css("maxHeight")),e.each(a,function(e,t){s[t]=p.css(t)}),e(n).css(s).attr("wrap",p.attr("wrap")),o(),window.chrome){var r=u.style.width;u.style.width="0px";{u.offsetWidth}u.style.width=r}}function r(){var e,a;t!==u?s():o(),n.value=!u.value&&i.placeholder?p.attr("placeholder")||"":u.value,n.value+=i.append||"",n.style.overflowY=u.style.overflowY,a=parseFloat(u.style.height)||0,n.scrollTop=0,n.scrollTop=9e4,e=n.scrollTop,d&&e>d?(u.style.overflowY="scroll",e=d):(u.style.overflowY="hidden",c>e&&(e=c)),e+=z,Math.abs(a-e)>.01&&(u.style.height=e+"px",n.className=n.className,w&&i.callback.call(u,u),p.trigger("autosize.resized"))}function l(){clearTimeout(h),h=setTimeout(function(){var e=p.width();e!==b&&(b=e,r())},parseInt(i.resizeDelay,10))}var d,c,h,u=this,p=e(u),z=0,w=e.isFunction(i.callback),f={height:u.style.height,overflow:u.style.overflow,overflowY:u.style.overflowY,wordWrap:u.style.wordWrap,resize:u.style.resize},b=p.width(),g=p.css("resize");p.data("autosize")||(p.data("autosize",!0),("border-box"===p.css("box-sizing")||"border-box"===p.css("-moz-box-sizing")||"border-box"===p.css("-webkit-box-sizing"))&&(z=p.outerHeight()-p.height()),c=Math.max(parseFloat(p.css("minHeight"))-z||0,p.height()),p.css({overflow:"hidden",overflowY:"hidden",wordWrap:"break-word"}),"vertical"===g?p.css("resize","none"):"both"===g&&p.css("resize","horizontal"),"onpropertychange"in u?"oninput"in u?p.on("input.autosize keyup.autosize",r):p.on("propertychange.autosize",function(){"value"===event.propertyName&&r()}):p.on("input.autosize",r),i.resizeDelay!==!1&&e(window).on("resize.autosize",l),p.on("autosize.resize",r),p.on("autosize.resizeIncludeStyle",function(){t=null,r()}),p.on("autosize.destroy",function(){t=null,clearTimeout(h),e(window).off("resize",l),p.off("autosize").off(".autosize").css(f).removeData("autosize")}),r())})):this}}(jQuery||$);
</script>


<form name="form_sms" method="post" action="_sms.pro.php" target="common_frame" enctype="multipart/form-data">
	<input type="hidden" name="form" id="form" value="sendform">
	<input type="hidden" name="send_list_serial" id="send_list_serial">

	<!-- 검색영역 -->
	<div class="form_box_area">
	<?=_DescStr("SMS 충전 및 발송내역 확인 : <a href='http://mobitalk.gobeyond.co.kr' target='_blank'><b>http://mobitalk.gobeyond.co.kr</b></a>")?>
	<?=($SMSUser['code'] == 'U00'?_DescStr('모비톡 발송 잔여건수 : <a href="http://mobitalk.gobeyond.co.kr/"" target="_blank"><b style="color:#ff0000">'.number_format($SMSUser['data']).'</b></a>건'):null)?>
	</div>
	<!-- // 검색영역 -->

	<!-- 문자내용 세부설정 -->
	<div class="new_sms_form freeHeight if_sendpage"> <!-- 직접발송페이지에서 if_sendpage 추가 2018-07-31 : ARA :  -->

		<!-- 문자항목들 -->
		<div class="aside_send_box">
			<span class="arrow_edge"><img src="./images/new_sms/opt_ic.png" alt="" /></span>

			<!-- 받는 사람 입력부분 -->
			<div class="send_to">
				<div class="title">받는 사람</div>
				<div class="phone_number">
					<input name="send_to_num1" tabindex="1" id="send_to_num1" type="text" maxlength="3" class="input_design" onkeyup="onlyNum()"><span class="unit">-</span>
					<input name="send_to_num2" tabindex="2" id="send_to_num2" type="text" maxlength="4" class="input_design" onkeyup="onlyNum()"><span class="unit">-</span>
					<input name="send_to_num3" tabindex="3" id="send_to_num3" type="text" maxlength="4" class="input_design" onkeyup="onlyNum()">
				</div>
				<script>
				$(document).ready(function(){
					$('#send_to_num1').on('keyup',function(){ if($(this).val().length == 3) { $('#send_to_num2').focus(); } });
					$('#send_to_num2').on('keyup',function(){ if($(this).val().length == 4) { $('#send_to_num3').focus(); } });
					$('#send_to_num3').on('keyup',function(){ if($(this).val().length == 4) { $('.add_to .btn_add').focus(); } });
				});
				</script>
				<input type="button" name="" tabindex="4" class="btn_add" value="+받는사람 추가하기" onclick="send_list_add(form_sms)"/>

				<div class="list">
					<select name="send_list" id="send_list" multiple class="" ></select>
				</div>
				<div class="total">
					받는 사람 총 <b id="slt_phonecnt">0</b></span>명
					<a href="#none" onclick="send_list_delete(form_sms);return false;" class="btn_delete">선택삭제</a>
				</div>
			</div>

			<!-- 예약전송 입력부분 -->
			<div class="send_reserve">
				<dl>
					<dt><label><input type="checkbox" name="_reserv_chk" id="_reserv_chk" value="Y" />예약으로 전송합니다.</label></dt>
					<?PHP
						$arr_12 = array(); $arr_30 = array(); $arr_24 = array(); $arr_60 = array();
						for( $i=1;$i<=12;$i++ ){ $arr_12[] = sprintf("%02d",$i); }
						for( $i=0;$i<=31;$i++ ){ $arr_30[] = sprintf("%02d",$i); }
						for( $i=0;$i<=23;$i++ ){ $arr_24[] = sprintf("%02d",$i); }
						for( $i=0;$i<=59;$i++ ){ $arr_60[] = sprintf("%02d",$i); }
					?>
					<dd>
						<?=_InputSelect( "_reserv_y" , array(date(Y),date(Y,strtotime("+1 year"))) , date(Y) , "" , "" , "-")?><span class="unit">년</span>
						<?=_InputSelect( "_reserv_m" , $arr_12 , date(m) , "" , "" , "-")?><span class="unit">월</span>
						<?=_InputSelect( "_reserv_d" , $arr_30 , date(d) , "" , "" , "-")?><span class="unit">일</span>
					</dd>
					<dd>
						<?=_InputSelect( "_reserv_h" , $arr_24 , date(H) , "" , "" , "-")?><span class="unit">시</span>
						<?=_InputSelect( "_reserv_i" , $arr_60 , date(i) , "" , "" , "-")?><span class="unit">분</span>
					</dd>
				</dl>
			</div>


			<!-- 보내는 사람 입력부분 (기본정보에서 기본입력) -->
			<div class="send_from">
				<div class="title">보내는 사람</div>
				<div class="phone_number">
					<input name="send_from_num" id="send_from_num" class="input_design" type="text" value="<?=$row_company[tel]?>" readonly="readonly">
				</div>
			</div>


			<div class="button_box">
				<span class="shop_btn_pack btn_input_red"><input type="button" name="" onclick="send_ok(form_sms)" class="input_large" value="문자 전송하기" /></span>
			</div>

		</div>


		<!-- 휴대폰한번감싸기 -->
		<div class="new_sms_send_wrap">


			<!-- 휴대폰폼 -->
			<div class="new_sms_phone">
				<div class="body">
					<div class="inner">

						<!-- 제목 lms, mms : placeholder ie하위버전 체크바랍니다 -->
						<div class="title_box"><input type="text" class="input_design a_title" style="outline:0;" name="send_title" placeholder="문자메세지의 제목을 입력하세요." /></div>

						<!-- 이 상자가 스크롤이 생기는 부분입니다 -->
						<div class="fix_box a_box textarea_wrap" style="cursor:text;">
							<!-- 메세지내용 -->
							<div class="message_box">
								<!-- 이미지첨부 들어갈 위치 -->
								<div class="textarea" style="border:0;cursor: text;">
									<textarea name="message" id="message" tabindex="1" rows="4" data-ma="a" style="display:block;resize:none;width:100%;outline:0;" class="textarea_content chk_length"></textarea>
								</div>
								<div class="bubble_bottom"></div>
							</div>
						</div>

						<!-- byte검사 문자구분 -->
						<div class="total_box"><span style="color:inherit;" id="message_len_id" class="a_len">0</span> byte <b id="sms_type" class="a_type">SMS</b></div>

						<!-- 이미지첨부 -->
						<div class="file_box">
							<div class="input_file_sms">
								<a href="#none" onclick="return false;" class="buttonImg_delete realFile_delete" data-ma="a" data-delete="Y" title="이미지삭제">&nbsp;</a>
								<input type="text" id="a_fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled>
								<div class="fileDiv" title="이미지첨부">
									<input type="button" class="buttonImg" value="이미지첨부" />
									<input type="file" accept="image/jpeg" name="a_file" class="realFile a_file" data-ma="a" onchange="javascript:document.getElementById('a_fakeFileTxt').value = this.value.match(/[^\/\\]+$/)" />
									<input type="hidden" name="a_file_OLD" class="realFile_old a_file_OLD" data-ma="a" value=""/>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="bottom"></div>
			</div>

			<!-- 2015-09-15 SMS발송옵션 설정 LDD006 {-->
			<div style="clear:both;"></div>
			<div class="new_send_type_set lms_msg" style="left:666px; display:none">
				<dl>
					<dt>LMS발송옵션 설정</dt>
					<dd>
						<label>
							<input type="radio" name="m_send_type" class="m_send_type" value="D" checked>
							<span class="txt">일반발송</span>
							<span class="exp">LMS발송: 최대 2,000Byte</span>
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
			<div class="guide_text"><span class="ic_blue"></span><span class="blue"><b>이미지가 첨부된 경우 "LMS발송옵션 설정"과 상관없이 MMS발송 처리 됩니다.</b></span></div>
			<!--} 2015-09-15 SMS발송옵션 설정 LDD006 -->

			<div class="guide_text"><span class="ic_blue"></span><span class="blue">문자메세지의 제목은 LMS, MMS의 경우에만 발송됩니다.</span></div>
			<div class="guide_text"><span class="ic_blue"></span><span class="blue">이미지는 <b>JPG 포멧만</b> 업로드 가능합니다.</span></div>
			<div class="guide_text"><span class="ic_blue"></span><span class="blue">이미지는 <b>60KB 미만 파일</b>만 업로드 가능합니다.</span></div>

		</div>
		<!-- / 휴대폰한번감싸기 -->
	</div>
</form>






<script>
	// detect IE version ( returns false for non-IE browsers )
	var ie = function(){for(var e=3,n=document.createElement("div"),r=n.all||[];n.innerHTML="<!--[if gt IE "+ ++e+"]><br><![endif]-->",r[0];);return e>4?e:!e}();
	if(ie!==false && ie<10) { $('.input_file_sms').addClass('old-ie'); } else { $('.input_file_sms').removeClass('old-ie'); }

	// lastIndexOf function
	Array.prototype.lastIndexOf||(Array.prototype.lastIndexOf=function(r){"use strict";if(null==this)throw new TypeError;var t=Object(this),e=t.length>>>0;if(0===e)return-1;var a=e;arguments.length>1&&(a=Number(arguments[1]),a!=a?a=0:0!=a&&a!=1/0&&a!=-(1/0)&&(a=(a>0||-1)*Math.floor(Math.abs(a))));for(var n=a>=0?Math.min(a,e-1):e-Math.abs(a);n>=0;n--)if(n in t&&t[n]===r)return n;return-1});

	// 글자 바이트수로 자르기
	function cutByte(r,t){var e=r,n=0,c=r.length;for(i=0;c>i;i++){if(n+=chr_byte(r.charAt(i)),n==t-1){e=2==chr_byte(r.charAt(i+1))?r.substring(0,i+1):r.substring(0,i+2);break}if(n==t){e=r.substring(0,i+1);break}}return e}function chr_byte(r){return escape(r).length>4?2:1}

	// 숫자에 콤마 추가
	String.prototype.comma=function(){var r=this.replace(/,/g,"");if("0"==r)return"0";var t=/^(-?\d+)(\d{3})($|\..*$)/;return t.test(r)&&(r=r.replace(t,function(r,t,e,n){return t.comma()+(","+e+n)})),r};


	function send_list_add(form){

		var send_list_count = document.getElementById("send_list").options.length;

		var send_to_num1 = document.getElementById("send_to_num1").value;
		var send_to_num2 = document.getElementById("send_to_num2").value;
		var send_to_num3 = document.getElementById("send_to_num3").value;

		if(!send_to_num1) { alert("받는사람 번호를 입력하세요."); $('#send_to_num1').focus(); return false; }
		if(!send_to_num2) { alert("받는사람 번호를 입력하세요."); $('#send_to_num2').focus(); return false; }
		if(!send_to_num3) { alert("받는사람 번호를 입력하세요."); $('#send_to_num3').focus(); return false; }

		if(send_to_num1 && send_to_num2 && send_to_num3) {

			var send_to_new = send_to_num1+"-"+send_to_num2+"-"+send_to_num3;

			document.getElementById("send_list").options[send_list_count] = new Option(send_to_new,send_to_new);

			document.getElementById("send_to_num1").value="";
			document.getElementById("send_to_num2").value="";
			document.getElementById("send_to_num3").value="";

			document.getElementById("slt_phonecnt").innerHTML=document.getElementById("send_list").options.length;

			document.getElementById("send_to_num1").focus();
		}
	}

	function send_list_delete(form){
		var send_list_count = document.getElementById("send_list").options.length;

		for(i=0;i<send_list_count;i++){
			if(document.getElementById("send_list").options[i].selected == true){
				document.getElementById("send_list").options[i] = null;
				send_list_count--;
				i--;
			}
		}
		document.getElementById("slt_phonecnt").innerHTML=document.getElementById("send_list").options.length;
	}


	function send_ok(form){

		if(document.getElementById("message").value=="메세지를 입력하세요" || document.getElementById("message").value==""){
			alert("메시지를 입력하세요.");
			document.getElementById("message").value="";
			document.getElementById("message_len_id").innerHTML="0";
			//document.getElementById("message").focus();
			$('.textarea_content').focus();
		}
		else{
			var send_list_count = document.getElementById("send_list").options.length;
			var send_list_value = "";

			for(i=0;i<send_list_count;i++){
				if(i==0) send_list_value += document.getElementById("send_list").options[i].value;
				else send_list_value += "/" + document.getElementById("send_list").options[i].value;
			}

			if(send_list_value == ""){
				alert("메시지를 받을 전화번호를 추가하세요");
				document.getElementById("send_to_num1").focus();
			}
			else{
				if(confirm('메시지 발송 개수에 따라 수초에서 수분이상 걸릴 수 있습니다.\n발송완료 메시지를 받을때까지\n추가로 문자전송하기 버튼을 클릭하지마세요.\n전송하시겠습니까?')){
					document.getElementById("send_list_serial").value = send_list_value;
					document.form_sms.submit();
				}
			}
		}
	}

	function str_length(form) {

		if ( navigator.appCodeName != 'Mozilla' ) {
			return document.getElementById("message").value.length;
		}

		var len = 0;

		for (var i=0; i<document.getElementById("message").value.length; i++) {

			if ( document.getElementById("message").value.substr(i, 1) > '~' ) {
				len+=2;
			}
			else {
				len++;
			}
		}

		return len;
	}

	function str_prev() {
		if ( navigator.appCodeName != 'Mozilla' ) {
			return document.SEND.h_content.value.length;
		}
		var len = 0;

		for (var i=0; i<document.SEND.h_content.value.length; i++) {
			if ( document.SEND.h_content.value.substr(i, 1) > '~' ) {
				len+=2;
			}
			else {
				len++;
			}

			if (len > 200) {
				return i
			}
		}

		return len;
	}

	$(document).ready(function(){

		// 문자입력 폼
		$('.textarea_content').autosize();
		$('.textarea_wrap').on('click',function(){ $(this).find('.textarea_content').focus(); });

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
		$('.chk_length').on('keyup',function() { check_length(); });

	});



	// 문자 타입 체크 (sms / lms / mms)
	function check_length(onoff) {
		$('.chk_length').each(function(){
			var len = 0, ma = $(this).data('ma'), height = $('.'+ma+'_box').height(), val = $(this).val();
			var current_type = $('.'+ma+'_type').text(), do_not_alert = onoff===true?true:false;

			// 글자수 계산
			if("Mozilla"!=navigator.appCodeName)len=$(this).val().length;else for(var i=0;i<$(this).val().length;i++)$(this).val().substr(i,1)>"~"?len+=2:len++;

			if(len > 2000) {
				alert('최대 2,000 바이트까지 보내실 수 있습니다.'); val = cutByte(val,1990); $(this).val(val); len = 0;
				// 글자수 재계산
				if("Mozilla"!=navigator.appCodeName)len=$(this).val().length;else for(var i=0;i<$(this).val().length;i++)$(this).val().substr(i,1)>"~"?len+=2:len++;
			}

			$('.'+ma+'_len').text(String(len).comma());
			if($.trim($('.'+ma+'_file').val()).length == 0 && $.trim($('.'+ma+'_file_OLD').val()).length == 0)  {
				if(len > 90) {
					// LMS
					if(current_type=='SMS' && do_not_alert===false) { alert('LMS로 전환되며 추가요금이 발생합니다.'); }
					$('.'+ma+'_type').text('LMS');
					$('.lms_msg').show(); // LMS를 SMS 규격으로 분할 발송 LDD006
				} else {
					// SMS
					$('.'+ma+'_type').text('SMS');
					$('.lms_msg').hide(); // LMS를 SMS 규격으로 분할 발송 LDD006
				}
			} else {
				// MMS
				if(current_type!='MMS' && do_not_alert===false) { alert('MMS로 전환되며 추가요금이 발생합니다.'); }
				$('.'+ma+'_type').text('MMS');
				$('.lms_msg').hide(); // LMS를 SMS 규격으로 분할 발송 LDD006
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
				check_length();
			} else { $('.'+ma+'_img').remove(); check_length(); }
		}
	}
</script>


<?php
// - 회원선택으로부터 넘어왔을 경우 체크 ---
if($_mode) {

	switch($_mode) {

		// --- 선택회원 ---
		case "select":

			$chk_cellular = array();
			$sres = _MQ_assoc(" select concat(htel1,'-',htel2,'-',htel3) as htel from odtMember where userType='B' and isRobot = 'N' and id in ('". implode("','" , array_values($chk_id)) ."') ORDER BY serialnum desc ");
			foreach($sres as $sk=>$sv){
				$chk_cellular[rm_str($sv['htel'])] ++;
			}
		break;
		// --- 선택회원 ---

		// --- 검색회원 ---
		case "search":

			$chk_cellular = array();
			$sres = _MQ_assoc(" select concat(htel1,'-',htel2,'-',htel3) as htel from odtMember " . enc('d' ,  $_search_que ) . " ORDER BY serialnum desc ");
			foreach($sres as $sk=>$sv){
				$chk_cellular[rm_str($sv['htel'])] ++;
			}
		break;
		// --- 검색회원 ---
	}
?>
<SCRIPT>
	$(document).ready(function() {

		var option_str = "";
		$("#send_list").find("option").remove();
		<?php foreach(array_filter(array_keys($chk_cellular)) as $sk=>$sv) { echo "option_str += \"<option value='". $sv ."' >". $sv ."</option>\";\n"; } ?>
		$("#send_list").append(option_str);
		$("#slt_phonecnt").html("<?=sizeof($chk_cellular)?>");
	});
</SCRIPT>
<?
}
// - 회원선택으로부터 넘어왔을 경우 체크 ---


// 하단
include_once("inc.footer.php");
?>