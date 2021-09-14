<?
	// 로그인 체크
	member_chk();

	if(!$uid) { error_msg("잘못된 접근입니다."); }

	$r = _MQ(" select * from odtRequestReturn where rr_uid = '".$uid."' and rr_member = '".get_userid()."' ");
	if(count($r)==0) { error_msg("잘못된 접근입니다."); }
	$_ordernum = $r['rr_ordernum'];

$ordernum = $_ordernum ? $_ordernum : $ordernum;
if( $ordernum ) {

	if( is_login() ) {
		$s_query = " and o.orderid = '".get_userid()."' and o.member_type = 'member' ";
	} else {
		$s_query = " and o.member_type = 'guest' ";
	}

	$ordr = _MQ_assoc("
		select * from odtOrderProduct as op
		left join odtOrder as o on (op.op_oordernum = o.ordernum)
		left join odtProduct as p on (op.op_pcode = p.code)
		left join odtRequestReturn as rr on (op.op_uid = SUBSTRING_INDEX(rr.rr_opuid , ',',-1) and rr.rr_ordernum = op.op_oordernum)
		where 1 ".$s_query."
		and op.op_oordernum = '".$ordernum."' and op.op_cancel = 'N' and o.canceled = 'N' and o.order_type in ('both','product') and op.op_is_addoption = 'N'
		and op.op_delivstatus = 'Y' and op.op_uid in ('".$r['rr_opuid']."')
		group by op.op_pcode order by op_uid asc
		");
	if( count($ordr) == 0 ) { error_msg("잘못된 접근입니다."); }

}

$page_title = "교환/반품내역";
include dirname(__FILE__)."/mypage.header.php";

?>
<div class="common_page">
<div class="common_inner common_full">
	<div class="cm_board_form">
		<ul>
			<li class="ess">
				<span class="opt">상태</span>
				<div class="value">
					<?
					switch($r['rr_status']) {
						case "Y": $request_status = "<span class='red'>".$arr_return_status[$r['rr_status']]."</span>"; break;
						case "N": $request_status = "<span class='dark'>".$arr_return_status[$r['rr_status']]."</span>"; break;
						case "R": $request_status = "<span class='light'>".$arr_return_status[$r['rr_status']]."</span>"; break;
					}
					?>
					<span class="texticon_pack"><?=$request_status?></span>
				</div>
			</li>
			<li class="ess">
				<span class="opt">주문번호</span>
				<div class="value">
					<input type="text" class="input_design" placeholder="주문번호를 입력해주세요." readonly value="<?=$ordernum?>" <?=$ordernum_style?>/>
				</div>
			</li>
			<li class="ess">
				<span class="opt">상품정보</span>
				<div class="value">
					<?
						foreach($ordr as $k=>$v) {
							$pro_img = replace_image(IMG_DIR_PRODUCT.app_thumbnail("장바구니", $v));
							$ordr_pr = _MQ_assoc("
								select * from odtOrderProduct as op
								left join odtRequestReturn as rr on (op.op_uid = SUBSTRING_INDEX(rr.rr_opuid , ',',-1) and rr.rr_ordernum = op.op_oordernum)
								where op.op_oordernum = '".$ordernum."' and op.op_pcode = '".$v['op_pcode']."' and op.op_is_addoption = 'N' and op.op_delivstatus = 'Y'
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
									<label style="cursor:default;">
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
					<input type="text" class="input_design" readonly value="<?=$arr_return_type[$r['rr_type']]?>"/>
				</div>
			</li>
			<li class="ess double">
				<span class="opt">사유</span>
				<div class="value">
					<input type="text" class="input_design" readonly value="<?=$r['rr_reason']?>"/>
				</div>
			</li>
			<!-- 에디터 들어갈 자리 -->
			<li class="ess">
				<span class="opt">내용</span>
				<div class="value"><!-- 에디터 혹은 --><textarea readonly class="textarea_design"><?=nl2br(strip_tags($r['rr_content']))?></textarea>
				</div>
			</li>
			<li class="ess double">
				<span class="opt">신청일</span>
				<div class="value">
					<input type="text" class="input_design" readonly value="<?=date('Y-m-d H:i',strtotime($r['rr_rdate']))?>"/>
				</div>
			</li>
			<li class="ess double">
				<span class="opt">처리일</span>
				<div class="value">
					<input type="text" class="input_design" readonly value="<?=rm_str($r['rr_edate'])>0?date('Y-m-d H:i',strtotime($r['rr_edate'])):'관리자가 처리 중입니다.'?>"/>
				</div>
			</li>
			<li class="ess">
				<span class="opt">관리자 답변</span>
				<div class="value"><!-- 에디터 혹은 --><textarea readonly class="textarea_design"><?=nl2br(strip_tags($r['rr_admcontent']))?></textarea>
				</div>
			</li>
		</ul>
	</div><!-- .cm_board_form -->

	<!-- 가운데정렬버튼 -->
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><a href="<?=$_GET['_PVSC']?"/?".enc('d',$_PVSC):"/"?>" class="btn_lg_black">목록으로<span class="edge"></span></a></span></li>
		</ul>
	</div>
	<!-- / 가운데정렬버튼 -->
	</form>
	
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
			_opuid		:	{ required : true }
		},
		messages: {
			_ordernum	:	{ required : "주문번호를 입력하세요." },
			_type		:	{ required : "교환/반품 분류를 선택하세요." },
			_reason		:	{ required : "사유를 선택하세요." },
			_opuid		:	{ required : "상품을 선택하세요." }
		},
		submitHandler : function(form) {
			form.submit();
		}
	});
});
</script>