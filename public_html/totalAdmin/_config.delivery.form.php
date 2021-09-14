<?PHP
include_once("inc.header.php");

if($_mode == "modify") {

	$que = "  select * from $TBtableName where t_uid='". $_uid ."' ";
	//$r = _MQ($que);
}
?>

<form name="frm" method=post action=_config.delivery.pro.php ENCTYPE='multipart/form-data'>

	<!-- 내부 서브타이틀 -->
	<div class="sub_title"><span class="icon"></span><span class="title">배송설정</span></div>
	<!-- // 내부 서브타이틀 -->
	<!-- 검색영역 -->
	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/>
				<col width="*"/>
			</colgroup>
			<tbody>
				<?php// LDD018 { ?>
				<tr>
					<td class="article">예약발송<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<?=_InputRadio("reserv_del_use", array('Y', 'N'), $row_setup['reserv_del_use']? $row_setup['reserv_del_use']:'N', ' class="reserv_del_use"', array('사용함','사용안함'), "")?>
						<?=_DescStr("'사용함' 설정시 구매자가 발송일을 요청(구매자 선택) 할 수 있습니다.")?>
						<script>
							$(function() {
								$('.reserv_del_use').on('click', function() {

									var Value = $(this).val();
									if(Value == 'Y') $('.reserv_del').show();
									else $('.reserv_del').hide();
								});
							})
						</script>
					</td>
				</tr>
				<tr class="reserv_del"<?php echo ($row_setup['reserv_del_use'] == 'N'?' style="display:none"':null); ?>>
					<td class="article">예약발송 최대기간<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						최소: <input type="text" name="reserv_del_term_min" size="4" class="input_text" value="<?php echo $row_setup['reserv_del_term_min']; ?>">일 ~
						최대: <input type="text" name="reserv_del_term_max" size="4" class="input_text" value="<?php echo $row_setup['reserv_del_term_max']; ?>">일
						<?=_DescStr("구매자가 발송일을 요청시 주문일로 부터 요청 가능 최대 기간을 설정 할 수 있습니다.")?>
						<?=_DescStr("만약, 주문일이 <strong>".date('Y-m-d', time())."</strong>이며 최대 기간이 <strong>7</strong>일 이라면 구매자는 발송일을 <strong>".date('Y-m-d', strtotime('+ 7day', time()))."까지</strong> 선택 가능 합니다.")?>
						<?=_DescStr("최대 기간을 지정 하고 싶지 않으시다면 <strong>0</strong>을 입력 하세요.")?>
					</td>
				</tr>
				<?php // } LDD018 ?>
				<tr>
					<td class="article">기본배송비<span class="ic_ess" title="필수"></span></td>
					<td class="conts"><input type="text" name="_delprice" class="input_text number_style" style="width:60px" value='<?=$row_setup['s_delprice']?>' />원
					<?=_DescStr("무료배송은 0을 입력하세요.")?>
					</td>
				</tr>
				<tr>
					<td class="article">무료배송비<span class="ic_ess" title="필수"></span></td>
					<td class="conts"><input type="text" name="_delprice_free" class="input_text number_style" style="width:60px" value='<?=$row_setup['s_delprice_free']?>' />원
					<?=_DescStr("0 입력 시 무료배송비가 적용되지 않고, 항상 배송비가 적용됩니다.")?>
					</td>
				</tr>
				<tr>
					<td class="article">지정택배사<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
					<?=_InputSelect( "_del_company" , array_keys($arr_delivery_company), $row_setup['s_del_company'] , "" , "" , "") ?>
					</td>
				</tr>

				<?php // } 추가배송비 설정 추가 2017-08-19 :: SSJ ?>
				<tr>
					<td class="article">추가배송비 설정<span class="ic_ess" title="필수"></span></td>
					<td class="conts">
						<?=_InputRadio("_del_addprice_use", array('Y', 'N'), $row_setup['s_del_addprice_use']? $row_setup['s_del_addprice_use']:'N', ' class="del_addprice_use"', array('사용함','사용안함'), "")?>
						<?=_DescStr("'사용함' 설정시 각 입점업체의 도서산간 추가배송비 설정에따라 추가배송비가 적용됩니다.")?>
						<?=_DescStr("'사용안함' 설정시 각 입점업체의 도서산간 추가배송비 설정에 상관없이 추가배송비가 적용되지 않습니다.")?>

						<div class="line del_addprice_detail" style="<?php echo ($row_setup['s_del_addprice_use']<>"Y"?"display:none;":""); ?>">
							* 일반배송상품에 추가배송비를 적용합니다.(필수적용)<br>
							* 일반배송상품을 무료배송비이상 구매하여 무료배송이 되었을때 추가배송비를 
							(
								<label><input type="radio" name="_del_addprice_use_normal" value="Y" <?php echo ($row_setup['s_del_addprice_use_normal']=="Y"?" checked ":"");?>>적용합니다.</label>
								<label><input type="radio" name="_del_addprice_use_normal" value="N" <?php echo ($row_setup['s_del_addprice_use_normal']<>"Y"?" checked ":"");?>>적용하지 않습니다.</label>
							)
						</div>
						<div class="line del_addprice_detail" style="<?php echo ($row_setup['s_del_addprice_use']<>"Y"?"display:none;":""); ?>">
							* 개별배송상품에 추가배송비를 
							(
								<label><input type="radio" name="_del_addprice_use_unit" value="Y" <?php echo ($row_setup['s_del_addprice_use_unit']=="Y"?" checked ":"");?>>적용합니다.</label>
								<label><input type="radio" name="_del_addprice_use_unit" value="N" <?php echo ($row_setup['s_del_addprice_use_unit']<>"Y"?" checked ":"");?>>적용하지 않습니다.</label>
							)
						</div>
						<div class="line del_addprice_detail" style="<?php echo ($row_setup['s_del_addprice_use']<>"Y"?"display:none;":""); ?>">
							* 무료배송상품에 추가배송비를 
							(
								<label><input type="radio" name="_del_addprice_use_free" value="Y" <?php echo ($row_setup['s_del_addprice_use_free']=="Y"?" checked ":"");?>>적용합니다.</label>
								<label><input type="radio" name="_del_addprice_use_free" value="N" <?php echo ($row_setup['s_del_addprice_use_free']<>"Y"?" checked ":"");?>>적용하지 않습니다.</label>
							)
						</div>

						<script>
							$(function() {
								$('.del_addprice_use').on('click', function() {

									var Value = $(this).val();
									if(Value == 'Y') $('.del_addprice_detail').show();
									else $('.del_addprice_detail').hide();
								});
							})
						</script>
					</td>
				</tr>
				<?php // } 추가배송비 설정 추가 2017-08-19 :: SSJ ?>

				<tr><td class="conts" colspan=2><?=_DescStr("입점업체의 배송정책이 없을 경우 기본배송설정으로 적용됩니다.")?></td></tr><?//JJC003?>
			</tbody> 
		</table>
	</div>
	<!-- // 검색영역 -->



	<!-- LDD007 {-->
	<!-- 내부 서브타이틀 -->
	<div class="sub_title">
		<span class="icon"></span>
		<span class="title">자동정산대기 처리 설정</span>
	</div>
	<!-- // 내부 서브타이틀 -->
	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/>
				<col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">사용여부</td>
					<td class="conts">
						<label><input type="checkbox" name="_product_auto_on" value="Y"<?php echo ($row_setup['s_product_auto_on'] == 'Y'?' checked':''); ?>> 사용여부</label>
						<?=_DescStr("<b>주의:</b> 사용안함에서 사용함으로 변경시 모든 데이터가 자동처리 됩니다.")?>
					</td>
				</tr>
				<tr>
					<td class="article">
						배송상품 설정
						<?=_DescStr("배송완료 후 설정된 기간이 지나면 자동 정산대기로 넘어갑니다.")?>
					</td>
					<td class="conts">
						<?php foreach($arr_paymethod_name as $k=>$v) { ?>
						<div style="margin-top:10px;">
							<div style="float: left; width:100px;"><?=$v;?></div>
							<div style="float: left">: <input type="text" name="_product_auto_<?=$k;?>" class="input_text" style="width:60px;" value="<?php echo $row_setup['s_product_auto_'.$k]; ?>"> 일</div>
							<div style="clear: both"></div>
						</div>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="article">
						쿠폰상품 설정
						<?=_DescStr("발급이 정상이며 사용되었다면 설정된 기간 이후 자동 정산대기로 넘어갑니다.")?>
					</td>
					<td class="conts">
						<?php foreach($arr_paymethod_name as $k=>$v) { ?>
						<div style="margin-top:10px;">
							<div style="float: left; width:100px;"><?=$v;?></div>
							<div style="float: left">: <input type="text" name="_coupon_auto_<?=$k;?>" class="input_text" style="width:60px;" value="<?php echo $row_setup['s_coupon_auto_'.$k]; ?>">일</div>
							<div style="clear: both"></div>
						</div>
						<?php } ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!--} LDD007 -->



	<!-- 내부 서브타이틀 -->
	<div class="sub_title"><span class="icon"></span><span class="title">상품공통등록설정</span></div>
	<!-- // 내부 서브타이틀 -->
	<!-- 검색영역 -->
	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/>
				<col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">기본 설정 수수료<span class="ic_ess" title="필수"></span></td>
					<td class="conts"><input type="text" name="_product_commission" class="input_text number_style" style="width:20px" value='<?=$row_setup['s_product_commission']?>' />%
					<?=_DescStr("설정한 수수료는 기업회원이 상품등록 시 자동으로 적용되는 수수료율을 지정합니다.")?>
					<?=_DescStr("기업회원이 상품등록을 하면 업체정산형태는 수수료를 자동선택합니다.")?>
					</td>
				</tr>
				<tr>
					<td class="article">주문확인서 주의사항<span class="ic_ess" title="필수"></span></td>
					<td class="conts"><textarea name="_product_notice" class="input_text" style="width:100%;height:250px;" geditor><?=stripslashes($row_setup['s_product_notice'])?></textarea>
						<?=_DescStr("5줄 이내로 입력하시기 바랍니다.")?>
						<?=_DescStr("기업회원이 상품등록시 자동으로 적용되는 쿠폰에 들어갈 주의사항입니다.")?>
					</td>
				</tr>
			</tbody> 
		</table>
	</div>
	<!-- // 검색영역 -->

	<?=_submitBTNsub()?>
</form>


<?PHP include_once("inc.footer.php"); ?>