<?PHP
	include_once("inc.header.php");

	if(!$form) {
		
        if($sc_type) {
            $sque = "where sc_type='" . $sc_type . "' limit 1";
        }
        else {
            $sque = "WHERE sc_type='링크프라이스'";
        }
        $row = _MQ("SELECT sc_type,sc_id,sc_use FROM odtClick " . $sque . " ");

?>


<form name="modForm" method="post" action="<?=$PHP_SELF?>">
<input type="hidden" name="form" value="modifyForm">

					<!-- 검색영역 -->
					<div class="form_box_area">
						<?=_DescStr("<b>제휴마케팅정보</b>를 설정합니다.")?>
					</div>
					<!-- // 검색영역 -->

					<!-- 검색영역 -->
					<div class="form_box_area">

						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
								
									<tr>
										<td class="article">제휴마케팅업체<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<?=_InputRadio( "sc_type" , array('링크프라이스','아이라이크클릭') , $row[sc_type] , "" , array('링크프라이스','아이라이크클릭') , "")?>
										</td>
									</tr>
									<tr>
										<td class="article">아이디</td>
										<td class="conts"><input type="text" name="sc_id" style="width:200px"  value="<?=$row[sc_id]?>" class="input_text"></td>
									</tr>
									<tr>
										<td class="article">활성화유무<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<?=_InputRadio( "sc_use" , array('Y','N') , $row[sc_use] , "" , array('활성화','비활성화') , "")?>					
										</td>
									</tr>

								</tbody> 
							</table>
				
					</div>
					<!-- // 검색영역 -->
					<!-- 버튼영역 -->
					<div class="bottom_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_red">
								<input type="submit" name="" class="input_large" value="확인">
							</span>
						</div>
					</div>
					<!-- 버튼영역 -->
</form>


					<div class="form_box_area" >
						<div class="desc_hidden" data-value="링크프라이스">
<?PHP
	include_once("_config.click.linkprice_desc.php");
?>
						</div>
						<div class="desc_hidden" data-value="아이라이크클릭">
<?PHP
	include_once("../ilikeclick/desc/mer.html");
?>
						</div>

					</div>

<script>
	var on_off = function() {
		$(".desc_hidden").hide();
		$("[data-value='" + $("input[name=sc_type]").filter(function() {if (this.checked) return this;}).val() + "']").show();
	}
	$(document).ready(on_off);
	$("input[name=sc_type]").click(on_off);
</script>




<?PHP
		include_once("inc.footer.php");

	}
	else if($form == "modifyForm" ) {


        $r = _MQ("select sc_idx from odtClick where sc_type='" . $sc_type . "' ");
        // 수정
        if( sizeof($r) > 0 ){
    		_MQ_noreturn("UPDATE odtClick SET sc_type='" . $sc_type . "',sc_id='" . $sc_id . "',sc_use='" . $sc_use . "' WHERE sc_idx='".$r[sc_idx]."'");
        }
        //입력
        else {
		    _MQ_noreturn("insert odtClick SET sc_type='" . $sc_type . "',sc_id='" . $sc_id . "',sc_use='" . $sc_use . "' ");
        }

		error_loc_msg("_config.click.form.php?sc_type=${sc_type}" , "적용이 잘 되었습니다.");

	}
	else {
		
		error_loc_msg("_config.click.form.php" , "허용되지 않은 접근 방식입니다.");

	}
?>