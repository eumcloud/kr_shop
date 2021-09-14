<?
$ordernum = $_ordernum ? $_ordernum : $ordernum;
$ordernum = trim($ordernum);
if( $ordernum ) {

	// JJC : 교환/반품 : 2018-07-09
	$arr_rropuid = array();
	$rr_que = "select rr_opuid from odtRequestReturn where rr_ordernum = '". $ordernum ."' ";
	$rr_res = _MQ_assoc($rr_que);
	foreach($rr_res as $rr_k => $rr_v){
		$ex = array_filter(explode("," , $rr_v['rr_opuid']));
		$arr_rropuid = array_merge($arr_rropuid , $ex);
	}

	if( is_login() ) {
		$s_query = " and o.orderid = '".get_userid()."' and o.member_type = 'member' ";
	} else {
		$s_query = " and o.member_type = 'guest' ";
	}

	$s_query .= "
			". (sizeof($arr_rropuid) > 0 ? " and op.op_uid NOT IN ('". implode("' , '" , array_filter($arr_rropuid)) ."') " : "") ."
			and op.op_oordernum = '". $ordernum ."'
			and op.op_cancel = 'N'
			and op.op_orderproduct_type = 'product'
			and op.op_is_addoption = 'N'
			and op.op_delivstatus = 'Y'
			and o.canceled = 'N'
	";

	$que = "
		select *
		from odtOrderProduct as op
		left join odtOrder as o on (op.op_oordernum = o.ordernum)
		left join odtProduct as p on (op.op_pcode = p.code)
		where 1
			".$s_query."
		GROUP BY op.op_pcode
	";
	$ordr = _MQ_assoc($que);
	if( sizeof($ordr) > 0 ) {
		$ordr_chk = true;
	} else {
		$ordr_chk = false;
		error_msg("잘못된 주문번호 이거나 교환/반품할 수 있는 상품이 없습니다.");
	}
}

if( $ordr_chk === true ) {
	$form_action = "/pages/service.return.pro.php";
	$form_target = "common_frame";
	$form_method = "POST";
	$submit_text = "신청하기";
	$ordernum_style = " style='font-weight:bold;' readonly ";
} else {
	$form_action = "/";
	$form_target = "";
	$form_method = "GET";
	$submit_text = "주문번호조회";
	$ordernum_style = "";
}

$page_title = "교환/반품신청";
include dirname(__FILE__)."/cs.header.php";
?>
<div class="common_page">
<div class="common_inner common_full">
	<? if($ordr_chk===true) { ?>
	<form name="board_form" id="board_form" method="<?=$form_method?>" action="<?=$form_action?>" enctype="multipart/form-data" target="<?=$form_target?>">
	<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"/>
	<input type="hidden" name="pn" value="<?=$pn?>"/>
	<div class="cm_board_form">
		<ul>
			<li class="ess">
				<span class="opt">주문번호</span>
				<div class="value">
					<input type="text" name="_ordernum" class="input_design" placeholder="주문번호를 입력해주세요." value="<?=$ordernum?>" <?=$ordernum_style?>/>
				</div>
			</li>
			<li class="ess">
				<span class="opt">상품정보</span>
				<div class="value">
					<?
						foreach($ordr as $k=>$v) {
							$pro_img = replace_image(IMG_DIR_PRODUCT.app_thumbnail("장바구니", $v));
							$ordr_pr = _MQ_assoc("
								select *
								from odtOrderProduct as op
								left join odtRequestReturn as rr on ( rr.rr_ordernum = op.op_oordernum and FIND_IN_SET(rr.rr_opuid , op.op_uid) > 0)
								left join odtOrder as o on (op.op_oordernum = o.ordernum)
								where 1
									".$s_query."
									and op.op_pcode = '".$v['op_pcode']."'
								order by op_uid asc
							");
							if( count($ordr_pr)==0 ) { continue; }
					?>
					<!-- 상품정보 -->
					<div class="this_item">
						<div class="thumb"><img src="<?=product_thumb_img( $v , '장바구니' ,  'data')?>" alt="<?=$v['name']?>" /></div>
						<div class="info">
							<div class="info_title"><?=strip_tags($v['name'])?></div>
							<dl>
								<?
									foreach($ordr_pr as $pk=>$pv) {
										$option_name = $pv['op_option1'] ? array($pv['op_option1'],$pv['op_option2'],$pv['op_option3']) : '옵션없음';
										$option_name = is_array($option_name) ? implode(' ',$option_name) : $option_name;
										$add_row = _MQ_assoc(" select concat(op_option1,' ',op_option2,' ',op_option3) as option_name, op_cnt from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$pv['op_pouid']."' and op_oordernum = '".$ordernum."' and op_pcode = '".$v['op_pcode']."' order by op_uid asc ");
										$add_option_name_array = array(); unset($add_option_name);
										if(count($add_row)>0) {
											$add_option_name = " (추가: ";
											foreach($add_row as $adk=>$adv) {
												$add_option_name_array[] = $adv['option_name']." <strong>".number_format($adv['op_cnt'])."</strong>개";
											}
											$add_option_name .= implode(" / ",$add_option_name_array);
											$add_option_name .= ") ";
										}
								?>
								<dd>
									<label>
										<input type="checkbox" name="_opuid[]" value="<?=$pv['op_uid']?>"/>
										<?=$option_name?> <strong><?=number_format($pv['op_cnt'])?></strong>개
										<?=$add_option_name?>
									</label>
								</dd>
								<? } ?>
							</dl>
						</div>
					</div>
					<!-- / 상품정보 -->
					<? } ?>

				</div>
			</li>
			<li class="ess double">
				<span class="opt">분류</span>
				<div class="value">
					<div class="select"><span class="shape"></span>
					<select name="_type">
					<? foreach($arr_return_type as $k=>$v) { ?>
					<option value="<?=$k?>" <?=$k=='R'?'selected':''?>><?=$v?></option>
					<? } ?>
					</select>
					</div>
				</div>
			</li>
			<li class="ess double">
				<span class="opt">사유</span>
				<div class="value">
					<div class="select"><span class="shape"></span>
					<select name="_reason">
						<? foreach($arr_return_reason as $k=>$v) { ?>
						<option value="<?=$v?>" <?=$k==0?'selected':''?>><?=$v?></option>
						<? } ?>
					</select>
					</div>
				</div>
			</li>
			<!-- 에디터 들어갈 자리 -->
			<li class="ess">
				<span class="opt">내용</span>
				<div class="value"><!-- 에디터 혹은 --><textarea cols="" rows="" name="_content" class="textarea_design"></textarea>
					<div class="tip_txt">
						<dl>
							<dt>교환/반품 사유 및 관리자에게 전할 내용을 입력하세요.</dt>
						</dl>
					</div>
				</div>
			</li>
			<? require_once dirname(__FILE__)."/inc.recaptcha.php"; ?>
		</ul>
	</div><!-- .cm_board_form -->

	<? if( !is_login() ) { ?>
	<!-- 비회원일경우 약관동의하기 필요하면 사용 -->
	<div class="cm_step_agree">
		<textarea cols="" rows="" name="" readonly><?=stripslashes($row_company['privacyinfo2'])?></textarea>
		<label><input type="checkbox" name="order_agree" id="order_agree" class="" value="Y" /> 위 방침을 읽고 동의합니다.</label>
	</div>
	<!-- / 동의하기 -->
	<? } ?>

	<!-- 가운데정렬버튼 -->
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><a href="<?=$_GET['_PVSC']?"/?".enc('d',$_PVSC):"/"?>" class="btn_lg_black">취소<span class="edge"></span></a></span></li>
			<li><span class="button_pack"><a href="#none" onclick="return false;" id="board_submit" class="btn_lg_color"><?=$submit_text?><span class="edge"></span></a></span></li>
		</ul>
	</div>
	<!-- / 가운데정렬버튼 -->
	</form>
	<? } else { ?>

		<!--  주문조회 -->
		<div class="cm_guest_order">

			<div class="gtxt_box">
				<div class="telnumber"><strong>교환/반품신청</strong></div>
				주문 시 입력한 주문번호를 입력하세요. 정보가 기억나지 않으시면 <b>고객센터</b>로 직접 연락주시길 바랍니다.
			</div>
			<div class="search_form">
					<form name="guest_order_frm" id="guest_order_frm" method="get" action="/">
					<input type="hidden" name="pn" value="<?=$pn?>"/>
					<div class="input_box">
						<ul>
							<li><span class="shop_icon ic_tel"></span>
								<input type="text" name="_ordernum" class="input_design" value="" placeholder="주문번호"  >
							</li>
						</ul>
					</div>
					<input type="submit" name="" class="btn_search" value="<?=$submit_text?>">
				</form>
			</div>
		</div>

	<? } ?>

	<!-- ●●●●●●●●●● 페이지 이용도움말 -->
	<div class="cm_user_guide">
		<dl>
			<dt>교환/반품 시 유의사항을 알려드립니다.</dt>
			<dd>교환/반품을 신청하면 관리자가 확인 후 처리해드립니다.</dd>
			<dd>필요한 경우 주문시 입력한 주문자 정보로 연락드리겠습니다.</dd>
			<dd>신청하신 순서대로 처리해드리고 있으므로, 즉시 처리되지 않을 수 있습니다.</dd>
			<dd>추가적인 문의나 요청사항은 <strong>고객센터 (<?=$row_company['tel']?>)</strong> 를 이용해 주시기 바랍니다.</dd>
		</dl>
	</div>
	<!-- / 페이지 이용도움말 -->

</div><!-- .layout_fix -->
</div><!-- .common_page -->


<link rel="stylesheet" href="/include/js/jquery/jqueryui/jquery-ui.min.css" type="text/css">
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
$(function() {
	$("#_sdate").datepicker({ changeMonth: true, changeYear: true });
	$("#_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
	$("#_sdate").datepicker( "option",$.datepicker.regional["ko"] );

	$("#_edate").datepicker({ changeMonth: true, changeYear: true });
	$("#_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
	$("#_edate").datepicker( "option",$.datepicker.regional["ko"] );
});
$("#board_submit").click(function() {
	$("#board_form").submit();
});
$(document).ready(function(){
	$("#board_form").validate({
		ignore: "input[type=text]:hidden",
		rules: {
			_ordernum	:	{ required : true },
			_type		:	{ required : true },
			_reason		:	{ required : true },
			_opuid		:	{ required : true },
			<? if(!is_login()) { ?>order_agree: { required: true }<? } ?>
		},
		messages: {
			_ordernum	:	{ required : "주문번호를 입력하세요." },
			_type		:	{ required : "교환/반품 분류를 선택하세요." },
			_reason		:	{ required : "사유를 선택하세요." },
			_opuid		:	{ required : "상품을 선택하세요." },
			<? if(!is_login()) { ?>order_agree: { required: "위 방침을 읽고 동의해주세요." }<? } ?>
		},
		submitHandler : function(form) {
			form.submit();
		}
	});
});
</script>