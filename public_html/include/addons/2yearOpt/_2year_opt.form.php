<?
	// /pages/inc.daily.update.php 기능 테스트
	//------------------------- 있을 경우 관리자 메인페이지 노출하여야 함.....................
?>



<?php
	// ---------------------------- 매 2년마다 수신동의 설정 ----------------------------
?>
<form name='frm01' method='post' action="/include/addons/2yearOpt/_2year_opt.pro.php">
<input type='hidden' name='_mode' value='setup'>

		<!-- 내부 서브타이틀 -->
		<div class="sub_title"><span class="icon"></span><span class="title">수신동의 발송설정 (매2년)</span></div>
		<!-- // 내부 서브타이틀 -->

		<!-- 검색영역 -->
		<div class="form_box_area">

			<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>

						<tr>
							<td class="article">수신동의 발송적용여부 (매2년)<span class="ic_ess" title="필수"></span></td>
							<td class="conts"><?=_InputRadio( "_2year_opt_use" , array('Y','N') , $row_setup['s_2year_opt_use'] ? $row_setup['s_2year_opt_use'] : "N" , "" , array('발송','미발송') , "")?></td>
						</tr>

					</tbody>
				</table>

				<?=_DescStr("정보통신망법 제50조제8항 및 동법 시행령 제62조의3은 최초 동의한 날로부터 매2년마다 하도록 규정하고 있습니다. 이에 따라 수신동의 받은 날부터 매 2년 마다 수신동의 여부를 재확인 해야 합니다.")?>
				<?=_DescStr("개정법 시행 이전(2014년 11월 29일 이전)에 수신동의를 받은 자는 2016년 11월 28일까지 수신 동의 여부를 확인하여야 합니다.")?>

		</div>
		<!-- // 검색영역 -->

	<?=_submitBTNsub()?>

</form>
<script type="text/javascript">
	$(document).ready(function(){
		$('input[name=_2year_opt_use]').on('click',function(){
			if($(this).val() == 'Y') { $('#div_2year_opt_use').show(); }
			else { $('#div_2year_opt_use').hide(); }
		});
	});
</script>






<?php
	// ---------------------------- 메일 발송하기 ----------------------------
?>
<div ID="div_2year_opt_use" style="display:<?=( $row_setup['s_2year_opt_use'] == "Y" ? "" : "none")?>">
<?php
	if( $row_setup['s_2year_opt_use'] == "Y" ) {
?>
<form name='frm02' method='post' >

		<!-- 내부 서브타이틀 -->
		<div class="sub_title"><span class="icon"></span><span class="title">발송하기</span></div>
		<!-- // 내부 서브타이틀 -->

		<!-- 검색영역 -->
		<div class="form_box_area">

			<table class="form_TB" summary="검색항목">
					<colgroup>
						<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
					</colgroup>
					<tbody>

						<tr>
							<td class="article">대상회원수</td>
							<td class="conts">
								<?php
									// JJC : 수정 : 2021-05-17
									//$mr_cnt = _MQ(" select count(*) as cnt from odt2yearOptLog where ol_status='N'  "); // 수신동의 2년 지난 -  회원
									$mr_cnt = _MQ(" select count(*) as cnt from odt2yearOptLog INNER join odtMember on (id = ol_mid and userType='B') where ol_status='N'  "); // 수신동의 2년 지난 -  회원
								?>
								<?=number_format($mr_cnt['cnt'])?>명
								<?=_DescStr("수신동의하신지 2년이 지난 회원을 검색합니다.")?>
							</td>
						</tr>

						<tr>
							<td class="article">발송형태<span class="ic_ess" title="필수"></span></td>
							<td class="conts">
								<?=_InputRadio( "_type" , array('email','sms' , 'both') , 'email' , "" , array('이메일발송' , '문자발송' , '이메일 + 문자발송') , "")?>
								<?=_DescStr("수신동의하신지 2년이 지난 회원중 메일수신동의, 문자수신동의를 하신 회원에게만 발송이 됩니다.")?>
							</td>
						</tr>

					</tbody>
				</table>


<?// --------------------- progress bar 적용 --------------------- //?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<style>
	.ui-progressbar {position: relative;}
	.progress-label {position: absolute;left: 50%;top: 4px;font-weight: bold;text-shadow: 1px 1px 0 #fff;}
</style>
<script>
	$(function() {

		var progressbar = $( "#progressbar" ), progressLabel = $( ".progress-label" );

		progressbar.progressbar({
			value: false,
			change: function() { progressLabel.text( progressbar.progressbar( "value" ) + "%" ); },
			complete: function() {progressLabel.text( "Complete!" );}
		});

		function progress() {
			progressbar = $( "#progressbar" ).show();
			var max_var = <?=$mr_cnt['cnt']?> * 1;
			max_var = max_var ? max_var : 1;
			$.ajax({
				data: {'_mode':'send' , '_type' : $("input[name='_type']").filter(function() {if (this.checked) return this;}).val()},
				type: 'POST', cache: false,
				url: '/include/addons/2yearOpt/_2year_opt.pro.php',
				success: function(data) {
					var app_data = data * 1;
					//console.log( max_var +' '+ app_data );
					progressbar.progressbar( "value",  Math.round(( (max_var - app_data) * 100  / max_var  )) + 1 );
					if ( app_data > 1 ) { setTimeout( progress, 300 ); }
					if( app_data == 0 ) { alert("발송을 완료하였습니다."); location.href=("_addons.php?pass_menu=2yearOpt/_2year_opt.form"); }
				}
			});
		}

		$("#mailsend_submit").click(function(){
			if(confirm("발송형태에 따라 시간이 걸리 수 있습니다.\n\n정말 발송하시겠습니까?")){
//				$("form[name='frm02']")[0].submit();
				progress();
			}
		});
	});
</script>

<div id="progressbar" style="display:none; margin:20px;"><div class="progress-label">Loading...</div></div>
<?// --------------------- progress bar 적용 --------------------- //?>


		</div>
		<!-- // 검색영역 -->

		<div class='bottom_btn_area'>
			<div class='btn_line_up_center'>
				<span class='shop_btn_pack btn_input_red'><input type='button' ID="mailsend_submit" name='' class='input_large' value='발송하기'></span>
			</div>
		</div>

</form>





<?php
	// ---------------------------- 발송메일 설정하기 ----------------------------
?>
<form name='frm03' method='post' action="/include/addons/2yearOpt/_2year_opt.pro.php">
<input type='hidden' name='_mode' value='mailsetup'>

	<!-- 내부 서브타이틀 -->
	<div class="sub_title"><span class="icon"></span><span class="title">발송메일 설정하기</span></div>
	<!-- // 내부 서브타이틀 -->

	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">제목<span class='ic_ess' title='필수'></span></td>
					<td class="conts">
						[사이트명]<input type=text name='_2year_opt_title' size='80' class='input_text' value="<?=stripslashes($row_setup['s_2year_opt_title'])?>">
						<?=_DescStr("하단에서 메일 형태를 확인하실 수 있습니다.")?>
					</td>
				</tr>
				<tr>
					<td class="article">상단내용<span class='ic_ess' title='필수'></span></td>
					<td class="conts">
						<textarea name="_2year_opt_content_top" class="input_text" geditor><?=stripslashes($row_setup['s_2year_opt_content_top'])?></textarea>
						<?=_DescStr("치환자 : <strong>{NAME} - 회원명 , {ID} - 회원아이디</strong>")?>
						<?=_DescStr("치환자를 이용하여 내용에 회원명이나 회원아이디를 넣을 수 있습니다.")?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

		<div class='bottom_btn_area'>
			<div class='btn_line_up_center'>
				<span class='shop_btn_pack btn_input_red'><input type='submit' name='' class='input_large' value='메일설정하기'></span>
				<span class='shop_btn_pack btn_input_gray' style="margin-left:5px;"><input type='button' onclick="mail_view();" name='' class='input_large' value='메일미리보기'></span>
			</div>
		</div>

</form>


<?php
	}
?>


</div>






<?php
	// ---------------------------- 발송메일 확인하기 ----------------------------
?>
<style type="text/css">
	/* ●●●●●●●●●● 레이어팝업 */
	.cm_ly_pop_tp {border:3px solid #2c2f34; border-radius:10px; overflow:hidden; background: #2c2f34; box-shadow:0 0 8px rgba(0,0,0,0.3);}
	/* 기본형 */
	.cm_ly_pop_tp .title_box {padding:15px 20px; color:#fff; font-size:18px; position:relative; background: #2c2f34; font-weight:600}
	.cm_ly_pop_tp .btn_close {position:absolute; top:50%; right:0; width:21px; height:21px; margin:-11px 20px 0 0; background:transparent url('/pages/images/cm_images/member_pop_close.gif') no-repeat; }
	.cm_ly_pop_tp .inner_box {overflow:hidden; padding:0px; background:#fff;}
</style>
<div class="cm_ly_pop_tp mail_page" style="display:none;">

	<!--  레이어팝업 공통타이틀 영역 -->
	<div class="title_box">발송메일 확인하기<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>

	<!-- 하얀색박스공간 -->
	<div class="inner_box">
		<?php
			include_once(dirname(__FILE__)."/mail.contents.2yearOpt.php"); // 메일 내용 불러오기 ($mailing_content)
			echo $_2year_opt_content ;
		?>
	</div>
</div>

<script>
	function mail_view(){
		$('.mail_page').lightbox_me({centered: true, closeEsc: true,onLoad: function() { }});
	}
</script>
<?php
	// ---------------------------- 발송메일 확인하기 ----------------------------
?>