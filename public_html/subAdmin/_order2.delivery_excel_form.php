<?PHP

	include_once("inc.header.php");


	if(!$pass_paystatus) {
		$pass_paystatus = "Y";
	}

?>

<script>
	function wFun() {
		if(!confirm("입력하시겠습니까?")) return false;
		return true;
	}
</script>


				<!-- 검색영역 -->
<form name="wFrm" method="post" enctype="multipart/form-data" action="_order2.delivery_excel_pro.php">
<input type="hidden" name="tran_type" value="ins_excel">
<input type=hidden name=_mode value=''>
<input type=hidden name=_PVSC value="<?=$_PVSC?>">
<input type=hidden name=delivstatus value="<?=$delivstatus?>">
<input type=hidden name=ordertype value="<?=$ordertype?>">
				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody> 
							<tr>
								<td class="article">엑셀파일</td>
								<td class="conts"><input type=file name='w_excel_file' class=input_text ></td>
							</tr>
							<tr>
								<td class="conts" colspan=2>
									<?=_DescStr("<B>택배사, 송장번호를 일괄적으로 변경</B>할 수 있습니다. (단, 발송처리하는 것은 아니니 확인 바랍니다.)")?>
									<?=_DescStr("엑셀 97~2003 버전 파일만 업로드가 가능합니다.")?>
									<?=_DescStr("엑셀 2007 이상 버전은(xlsx) 다른이름저장을 통해 97~2003버전으로 저장하여 등록하세요.")?>
									<?=_DescStr("(배송)발송대기관리의 엑셀저장으로 다운받아 송장정보를 변경하시면 됩니다. <A HREF='/upfiles/normal/delivery_sample.xls'><B>[샘플파일 다운로드]</B></A>")?>
								</td>
							</tr>
						</tbody> 
					</table>

					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="엑셀등록" value="엑셀등록"></span>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_order2.list.php?<?=enc("d" , $_PVSC)?>" class="medium gray" title="목록" >목록</a></span>
						</div>
					</div>
				</div>
</form>
				<!-- // 검색영역 -->



<?PHP

	include_once("inc.footer.php");
?>