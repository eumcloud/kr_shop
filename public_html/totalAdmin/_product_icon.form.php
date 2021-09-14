<?PHP

	// 페이지 표시
	$app_current_link = "/totalAdmin/_product_icon.list.php";

	include_once("inc.header.php");

	$app_cpname = "";
	if( $_mode == "modify" ) {
		$que = "  select * from odtProductIcon where pi_uid='". $_uid ."' ";
		$r = _MQ($que);
	}

?>


<form name=frm method=post action=_product_icon.pro.php enctype='multipart/form-data' autocomplete='off' >
<input type=hidden name=_mode value='<?=$_mode?>'>
<input type=hidden name=_uid value='<?=$_uid?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">

					<div class="form_box_area">

						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article">아이콘유형</td>
										<td class="conts"><?=_InputSelect( "_type" , array_keys($arr_product_icon_type) , $r[pi_type] , " hname='아이콘유형' required " , array_values($arr_product_icon_type) , "-유형선택-")?></td>
									</tr>
									<tr>
										<td class="article">아이콘이미지</td>
										<td class="conts"><?=_PhotoForm( "../upfiles/icon" , "_img"  , $r[pi_img] )?></td>
									</tr>

									<tr>
										<td class="article">아이콘 타이틀</td>
										<td class="conts">
										<input type=text name=_title value='<?=$r[pi_title]?>' size=80 maxlength=150 class=input_text>
										</td>
									</tr>
									<tr>
										<td class="article">노출순위</td>
										<td class="conts"><input type="text" name="_idx" class="input_text" style="width:20px" value='<?=$r[pi_idx]?>'  hname='노출순위'/>순위&nbsp;&nbsp;<?=_DescStr("낮은 순위가 먼저 나오며, 순위가 같을 경우 먼저 최근 등록한 순으로 나옵니다.")?></td>
									</tr>
								</tbody> 
							</table>
				
					</div>

					<?=_submitBTN("_product_icon.list.php" , ($_type ? "_type={$_type}" : ""))?>

				</form>




<script language='javascript' src='../../include/js/lib.validate.js'></script>

<?PHP
	include_once("inc.footer.php");
?>