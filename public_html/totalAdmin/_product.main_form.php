<?PHP
	include_once("inc.header.php");

?>
<form name="frm" method=post action="_product.main_pro.php" ENCTYPE='multipart/form-data'>

<!-- 내부 서브타이틀 -->
<div class="sub_title"><span class="icon"></span><span class="title">메인분류설정</span></div>
<!-- // 내부 서브타이틀 -->

<div class="form_box_area">

	<table class="form_TB" summary="검색항목">
		<colgroup>
			<col width="200"/><!-- 마지막값은수정안함 --><col width="*"/>
		</colgroup>
		<tbody>

			<tr>
				<td class="article">분류1 설정</td>
				<td class="conts">
					<div class="line" style="border:0;"><B>분류1 타이틀</B> : <input type="text" name="s_main_hot_title" class="input_text" value="<?=$row_setup['s_main_hot_title']?>"/></div>
				</td>
			</tr>

			<tr>
				<td class="article">분류2 설정</td>
				<td class="conts">
					<div class="line" style="border:0;"><B>분류2 타이틀</B> : <input type="text" name="s_main_new_title" class="input_text" value="<?=$row_setup['s_main_new_title']?>"/></div>
				</td>
			</tr>

			<tr>
				<td class="article">매진임박 설정</td>
				<td class="conts">
					<div class="line"><B>매진임박 타이틀</B> : <input type="text" name="s_main_close_title" class="input_text" value="<?=$row_setup['s_main_close_title']?>"/></div>
					<div class="line"><B>매진일수</B> : <input type="text" name="s_main_close_day" class="input_text" style="width:20px" value='<?=$row_setup['s_main_close_day'] ?>' />일전 상품이 매진임박 상품으로 설정됩니다.</div>
					<div class="line"><B>매진개수</B> : 재고가 <input type="text" name="s_main_close_cnt" class="input_text" style="width:40px" value='<?=$row_setup['s_main_close_cnt'] ?>' />개 이하일 경우 매진임박 상품으로 설정됩니다. </div>
					<?=_DescStr("메인에 노출되는 세번째 분류인 매진임박은 조건에 맞는 상품이 자동으로 노출됩니다.")?>
				</td>
			</tr>

		</tbody> 
	</table>
</div>
<!-- 검색영역 -->

<?=_submitBTNsub()?>

</form>

<?PHP
	include_once("inc.footer.php");
?>