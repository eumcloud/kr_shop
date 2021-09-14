<?PHP
// LMH007
// 메뉴 지정 변수
$app_current_link = "/totalAdmin/_config.agree.form.php";

include_once("inc.header.php");

// 정책설정 정보 추출 2017-09-13 SSJ
$row_policy = _MQ_assoc("select * from odtPolicy where 1 order by po_uid asc ");
$arr_policy = array();
foreach($row_policy as $k=>$v){
    $arr_policy[$v['po_name']][] = $v;
    $arr_policy[$v['po_name'] . '_use'] = $v['po_use'];
}

?>


<form name=frm method=post action="_config.agree.pro.php" enctype='multipart/form-data' >
	
	<!-- 내부 서브타이틀 -->
	<div class="sub_title"><span class="icon"></span><span class="title">약관 및 정책 설정 (텍스트)</span></div>
	<!-- // 내부 서브타이틀 -->

	<div class="form_box_area">

		<table class="form_TB" summary="검색항목">
				<colgroup>
					<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
				</colgroup>
				<tbody>

					<tr>
						<td class="article">이용약관</td>
						<td class="conts">
							<textarea name="guideinfo" class="input_text" style="width:99%;height:200px;" ><?=stripslashes($row_company[guideinfo])?></textarea>
							<?=_DescStr('회원가입 등에 표시될 이용약관을 입력하세요.')?>
						</td>
					</tr>
					<tr>
						<td class="article">[필수] 개인정보수집 및 이용 동의<br>(회원가입)</td>
						<td class="conts">
							<textarea name="privacyinfo" class="input_text" style="width:99%;height:200px;" ><?=stripslashes($row_company['privacyinfo'])?></textarea>
						</td>
					</tr>

					<tr>
						<td class="article">[선택] 개인정보수집 및 이용 동의<br>(회원가입)</td>
						<td class="conts">
							<?php
								$_appname = 'optional_privacyinfo';
								if(sizeof($arr_policy[$_appname]) < 1){ $arr_policy[$_appname][] = array(); } 
							?>
							<div class="line">
								<span class="shop_btn_pack" style="float:none;"><input type="button" class="input_small white" style="" onclick="policy_add(this,'<?php echo $_appname; ?>')" value="+ 개인정보수집 및 이용 동의 추가"></span>
							</div>
							<div class="line">
								<input type="hidden" name="_appname[]" value="<?php echo $_appname; ?>">
								* 사용여부 : <?php echo _InputRadio( $_appname . '_use', array('Y', 'N'), ($arr_policy[$_appname . '_use']?$arr_policy[$_appname . '_use']:'N') , ' class="" ' , array('사용', '미사용')); ?>
							</div>
							<?php foreach($arr_policy[$_appname] as $k=>$v){ ?>
							<div class="line">
								<input type="hidden" name="<?php echo $_appname; ?>_uid[]" value="<?php echo $v['po_uid']; ?>">
								<input type="text" name="<?php echo $_appname; ?>_title[]" class="input_text" value="<?php echo $v['po_title']; ?>" style="margin-bottom:3px; width:70%">
								<?php if($k>0){ ?><span class="shop_btn_pack" style="float:right;"><input type="button" class="input_small red" style="" onclick="policy_delete(this)" value="- 삭제"></span><?php } ?><br>
								<textarea name="<?php echo $_appname; ?>_content[]" class="input_text" style="width:99%;height:200px;" ><?php echo stripslashes($v['po_content'])?></textarea>
							</div>
							<?php } ?>
							<?=_DescStr('선택적 동의항목은 업체상황에 맞게 사용여부와 내용을 작성하여 사용해 주시기 바랍니다. ')?>
						</td>
					</tr>

					<tr>
						<td class="article">[선택] 개인정보 처리ㆍ위탁 동의<br>(회원가입)</td>
						<td class="conts">
							<?php
								$_appname = 'optional_consign';
								if(sizeof($arr_policy[$_appname]) < 1){ $arr_policy[$_appname][] = array(); } 
							?>
							<div class="line">
								<input type="hidden" name="_appname[]" value="<?php echo $_appname; ?>">
								* 사용여부 : <?php echo _InputRadio( $_appname . '_use', array('Y', 'N'), ($arr_policy[$_appname . '_use']?$arr_policy[$_appname . '_use']:'N') , ' class="" ' , array('사용', '미사용')); ?>
							</div>
							<div class="line">
								<span class="shop_btn_pack" style="float:none;"><input type="button" class="input_small white" style="" onclick="policy_add(this,'<?php echo $_appname; ?>')" value="+ 개인정보 처리ㆍ위탁 동의 추가"></span>
							</div>
							<?php foreach($arr_policy[$_appname] as $k=>$v){ ?>
							<div class="line">
								<input type="hidden" name="<?php echo $_appname; ?>_uid[]" value="<?php echo $v['po_uid']; ?>">
								<input type="text" name="<?php echo $_appname; ?>_title[]" class="input_text" value="<?php echo $v['po_title']; ?>" style="margin-bottom:3px; width:70%">
								<?php if($k>0){ ?><span class="shop_btn_pack" style="float:right;"><input type="button" class="input_small red" style="" onclick="policy_delete(this)" value="- 삭제"></span><?php } ?><br>
								<textarea name="<?php echo $_appname; ?>_content[]" class="input_text" style="width:99%;height:200px;" ><?php echo stripslashes($v['po_content'])?></textarea>
							</div>
							<?php } ?>
							<?=_DescStr('선택적 동의항목은 업체상황에 맞게 사용여부와 내용을 작성하여 사용해 주시기 바랍니다. ')?>
						</td>
					</tr>

					<tr>
						<td class="article">[선택] 개인정보 제3자 제공 동의<br>(회원가입)</td>
						<td class="conts">
							<?php
								$_appname = 'optional_thirdinfo';
								if(sizeof($arr_policy[$_appname]) < 1){ $arr_policy[$_appname][] = array(); } 
							?>
							<div class="line">
								<input type="hidden" name="_appname[]" value="<?php echo $_appname; ?>">
								* 사용여부 : <?php echo _InputRadio( $_appname . '_use', array('Y', 'N'), ($arr_policy[$_appname . '_use']?$arr_policy[$_appname . '_use']:'N') , ' class="" ' , array('사용', '미사용')); ?>
							</div>
							<div class="line">
								<span class="shop_btn_pack" style="float:none;"><input type="button" class="input_small white" style="" onclick="policy_add(this,'optional_thirdinfo')" value="+ 개인정보 제3자 제공 동의 추가"></span>
							</div>
							<?php foreach($arr_policy[$_appname] as $k=>$v){ ?>
							<div class="line">
								<input type="hidden" name="<?php echo $_appname; ?>_uid[]" value="<?php echo $v['po_uid']; ?>">
								<input type="text" name="<?php echo $_appname; ?>_title[]" class="input_text" value="<?php echo $v['po_title']; ?>" style="margin-bottom:3px; width:70%">
								<?php if($k>0){ ?><span class="shop_btn_pack" style="float:right;"><input type="button" class="input_small red" style="" onclick="policy_delete(this)" value="- 삭제"></span><?php } ?><br>
								<textarea name="<?php echo $_appname; ?>_content[]" class="input_text" style="width:99%;height:200px;" ><?php echo stripslashes($v['po_content'])?></textarea>
							</div>
							<?php } ?>
							<?=_DescStr('선택적 동의항목은 업체상황에 맞게 사용여부와 내용을 작성하여 사용해 주시기 바랍니다. ')?>
						</td>
					</tr>

					<tr>
						<td class="article">[필수] 개인정보수집 및 이용 동의<br>(비회원 주문)</td>
						<td class="conts">
							<textarea name="guestinfo" class="input_text" style="width:99%;height:200px;" ><?=stripslashes($row_company['guestinfo'])?></textarea>
							<?=_DescStr('비회원 주문시 표시될 비회원주문 개인정보수집 및 이용 동의를 입력하세요.')?>
						</td>
					</tr>

					<tr>
						<td class="article">[필수] 개인정보수집 및 이용 동의<br>(비회원 글쓰기)</td>
						<td class="conts">
							<textarea name="privacyinfo2" class="input_text" style="width:99%;height:200px;" ><?=stripslashes($row_company['privacyinfo2'])?></textarea>
							<?=_DescStr('비회원 글쓰기 등에 표시될 개인정보수집 및 이용 동의를 입력하세요.')?>
						</td>
					</tr>

					<tr>
						<td class="article">[필수] 개인정보수집 및 이용 동의<br>(광고/제휴문의)</td>
						<td class="conts">
							<textarea name="partner_agree" class="input_text" style="width:99%;height:200px;" ><?=stripslashes($row_company['partner_agree'])?></textarea>
							<?=_DescStr('광고/제휴문의 등록시 표시될 개인정보수집 및 이용 동의를 입력하세요.')?>
						</td>
					</tr>

					<tr>
						<td class="article">[필수] 개인정보수집 및 이용 동의<br>(상품메일)</td>
						<td class="conts">
							<textarea name="sendmail_agree" class="input_text" style="width:99%;height:200px;" ><?=stripslashes($row_company['sendmail_agree'])?></textarea>
							<?=_DescStr('상품홍보메일에 표시될 개인정보수집 및 이용 동의를 입력하세요.')?>
						</td>
					</tr>

					<tr>
						<td class="article">[필수] 개인정보수집 및 이용 동의<br>(구독하기)</td>
						<td class="conts">
							<textarea name="subscrip_agree" class="input_text" style="width:99%;height:200px;" ><?=stripslashes($row_company['subscrip_agree'])?></textarea>
							<?=_DescStr('구독하기에 표시될 개인정보수집 및 이용 동의를 입력하세요.')?>
						</td>
					</tr>

				</tbody> 
			</table>

	</div>
	<!-- 검색영역 -->

	<div class="sub_title"><span class="icon"></span><span class="title">약관 및 정책 설정 (페이지)</span></div>
	<!-- // 내부 서브타이틀 -->

	<div class="form_box_area">

		<table class="form_TB" summary="검색항목">
				<colgroup>
					<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<td class="article">이용약관 페이지 (PC)</td>
						<td class="conts">
							<textarea name="guideinfo_html" class="input_text" style="width:99%;height:200px;" geditor><?=stripslashes($row_company[guideinfo_html])?></textarea>
							<?=_DescStr('이용약관 페이지 (PC) 에 디자인이 적용되어 표시될 내용을 입력하세요.')?>
						</td>
					</tr>
					<tr>
						<td class="article">이용약관 페이지 (모바일)</td>
						<td class="conts">
							<textarea name="guideinfo_html_m" class="input_text" style="width:99%;height:200px;" geditor><?=stripslashes($row_company[guideinfo_html_m])?></textarea>
							<?=_DescStr('이용약관 페이지 (모바일) 에 디자인이 적용되어 표시될 내용을 입력하세요.')?>
						</td>
					</tr>
					<tr>
						<td class="article">개인정보취급방침 페이지 (PC)</td>
						<td class="conts">
							<textarea name="privacyinfo_html" class="input_text" style="width:99%;height:200px;" geditor><?=stripslashes($row_company[privacyinfo_html])?></textarea>
							<?=_DescStr('개인정보취급방침 페이지 (PC) 에 디자인이 적용되어 표시될 내용을 입력하세요.')?>
						</td>
					</tr>
					<tr>
						<td class="article">개인정보취급방침 페이지 (모바일)</td>
						<td class="conts">
							<textarea name="privacyinfo_html_m" class="input_text" style="width:99%;height:200px;" geditor><?=stripslashes($row_company[privacyinfo_html_m])?></textarea>
							<?=_DescStr('개인정보취급방침 페이지 (모바일) 에 디자인이 적용되어 표시될 내용을 입력하세요.')?>
						</td>
					</tr>

				</tbody> 
			</table>

	</div>
	<!-- 검색영역 -->

	<?=_submitBTNsub()?>

	</form>

    <script>
        // 약관설정 항목 추가 2017-09-13 SSJ
        function policy_add(obj, name){
            $this = $(obj).parent().parent().parent();
            var _html = '<div class="line">';
                _html += '<input type="hidden" name="'+name+'_uid[]" value="">';
                _html += '<input type="text" name="'+name+'_title[]" class="input_text" value="" style="margin-bottom:3px; width:70%">';
                _html += '<span class="shop_btn_pack" style="float:right;"><input type="button" class="input_small red" style="" onclick="policy_delete(this)" value="- 삭제"></span><br>';
                _html += '<textarea name="'+name+'_content[]" class="input_text" style="width:99%;height:200px;" ></textarea>';
                _html += '</div>';
            $this.append(_html);
        }
        // 약관설정 항목 삭제 2017-09-13 SSJ
        function policy_delete(obj){
            if(confirm('약관을 삭제하면 회원가입시 해당 약관에 동의한 내역도 모두 삭제됩니다.\n\n정말 삭제하시겠습니까?')){
                $this = $(obj).parent().parent();
                $this.remove();
            }
        }
    </script>

<?PHP include_once("inc.footer.php"); ?>