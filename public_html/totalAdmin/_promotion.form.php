<?PHP

	// LMH005

	// 메뉴 지정 변수
	$app_current_link = "/totalAdmin/_promotion.list.php";

	include_once("inc.header.php");


	if($_mode == "modify") {
        $row = _MQ(" SELECT * FROM odtPromotionCode WHERE pr_uid='" . $pr_uid . "' ");
	}

?>

<form name="frm" method="post" action="_promotion.pro.php" enctype="multipart/form-data" >
<input type="hidden" name="_mode" value="<?=($_mode ? $_mode : "add")?>"/>
<input type="hidden" name="pr_uid" value="<?=$pr_uid?>"/>
<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"/>

					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article">사용여부<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<?=_InputRadio("pr_use", array('N', "Y"), ($row['pr_use']?$row['pr_use']:"Y"), "", array('미사용', "사용") )?>
										</td>
									</tr>
									<tr>
										<td class="article">프로모션코드<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<input type="text" name="pr_code" size="30" class="input_text" <?=$_mode=='modify'?'readonly':''?> value="<?=$row[pr_code]?>">
											<?=$_mode=='modify'?_DescStr("한번 생성한 프로모션코드는 변경할 수 없습니다."):""?>
										</td>
									</tr>
									<tr>
										<td class="article">프로모션코드명</td>
										<td class="conts">
											<input type="text" name="pr_name" size="30" class="input_text" value="<?=$row[pr_name]?>">
											<?=_DescStr("코드명은 관리자 참고용으로 사용자에게 노출되지 않습니다.")?>
										</td>
									</tr>
									<tr>
										<td class="article">할인금액<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<div class="line">
												<?=_InputRadio("pr_type", array('A', "P"), ($row['pr_type']?$row['pr_type']:"A"), "", array('할인금액(원)', "할인율(%)") )?>
											</div>
											<div class="line">
												<input type="text" name="pr_amount" size="20" class="input_text number_style" style="text-align:right;" value="<?=$row[pr_amount]?$row[pr_amount]:0?>"/>
												<span class="type_print type_P" style="display:none;">%</span>
												<span class="type_print type_A">원</span>
											</div>
											<div class="type_print type_P" style="display:none;"><?=_DescStr("할인율(%)로 선택하면 장바구니에 담긴 상품총액 기준 할인율이 적용됩니다 (배송비제외).")?></div>
											<div class="type_print type_A"><?=_DescStr("할인금액(원)으로 선택하면 설정한 금액만큼 할인이 적용됩니다. 상품총액이 할인율보다 작을 경우 상품총액만큼 할인이 적용됩니다.")?></div>
											<script>
											var this_type = '';
											$(document).ready(function(){
												this_type = $('input[name=pr_type]:checked').val(); trigger_type();
												$('input[name=pr_type]').on('click',function(){ this_type = $('input[name=pr_type]:checked').val(); trigger_type(); });
											});
											function trigger_type(){
												$('.type_print').hide(); $('.type_print.type_'+this_type).show();
											}
											</script>
										</td>
									</tr>
									<tr>
										<td class="article">만료일<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<input type="text" name="pr_expire_date" size="15" readonly class="input_text" value="<?=($row[pr_expire_date] ? $row[pr_expire_date] : date("Y-m-d" , strtotime("+ 30 day")))?>">
										</td>
									</tr>
									<? if($_mode=='modify') { ?>
									<tr>
										<td class="article">생성일</td>
										<td class="conts">
											<?=date('Y-m-d H:i:s',strtotime($row[pr_rdate]))?>
										</td>
									</tr>
									<? if(rm_str($row[pr_edate])>0) { ?>
									<tr>
										<td class="article">수정일</td>
										<td class="conts">
											<?=date('Y-m-d H:i:s',strtotime($row[pr_edate]))?>
										</td>
									</tr>
									<? } ?>
									<? } ?>
								</tbody> 
							</table>
				
					</div>

					<?=_submitBTN("_promotion.list.php")?>

</form>

<SCRIPT LANGUAGE="JavaScript">
    $(document).ready(function(){
		// -  validate --- 
        $("form[name=frm]").validate({
			ignore: "input[type=text]:hidden",
            rules: {
            	pr_code: { required: true },
				pr_name: { required: false },
				pr_amount: { required: true },
				pr_expire_date: { required: true }
            },
            messages: {
            	pr_code: { required: "프로모션 코드를 입력하시기 바랍니다." },
				pr_name: { required: "코드명을 입력하시기 바랍니다." },
				pr_amount: { required: "할인율 또는 할인금액을 입력하시기 바랍니다." },
				pr_expire_date: { required: "만료일을 선택하시기 바랍니다." }
            }
        });
		// - validate --- 
	});
</SCRIPT>


<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
    $(function() {
		$("input[name=pr_expire_date]").datepicker({changeMonth: true,changeYear: true});
        $("input[name=pr_expire_date]").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("input[name=pr_expire_date]").datepicker( "option",$.datepicker.regional["ko"] );
    });

</script>

<?PHP
	include_once("inc.footer.php");
?>