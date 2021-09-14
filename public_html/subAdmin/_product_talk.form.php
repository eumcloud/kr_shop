<?PHP

	include_once("inc.header.php");


	// - 수정 ---
	if( $_mode == "modify" ) {
		$que = " 
			select
				tt.*, p.name as p_name, p.prolist_img, p.main_img
			from odtTt as tt
			inner join odtProduct as p on (tt.ttProCode = p.code) 
			where
				tt.ttNo='".$ttNo."' 
		";
		$row = _MQ($que);
		$_str = "수정";
		$ttID = $row[ttID];
		$ttName = $row[ttName];
	}
	// - 수정 ---
	// - 등록 ---
	else {
		$_str = "등록";
		$ttID = $com[id];
		$ttName = $com[cName];
	}
	// - 등록 ---
?>
<script language='javascript' src='../../include/js/lib.validate.js'></script>

<form name=frm method=post action="_product_talk.pro.php" >
<input type=hidden name=_mode value='<?=$_mode?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
<input type=hidden name="ttNo" value="<?=$ttNo?>">
<?PHP

	if( $ttNo && $_mode == "add") {
		$ique = "
			select
				tt.*, p.name as p_name, p.prolist_img, p.main_img
			from odtTt  as tt
			inner join odtProduct as p on (tt.ttProCode = p.code)
			where
				tt.ttNo='".$ttNo."'
		";
		$ir = _MQ($ique);
		$ttName = $ir[ttName];

		$_p_img = replace_image(IMG_DIR_PRODUCT.($ir['prolist_img'] ? $ir['prolist_img'] : $ir['main_img']));
?>
					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class='article'>상품정보</td>
										<td class='conts'><img src='<?=$_p_img?>' width="150"><br><?=$ir[p_name]."<br>".$ir[ttProCode]?></td>
									</tr>
									<tr>
										<td class='article'>글등록자</td>
										<td class='conts'><?=$ttName?></td>
									</tr>
									<tr>
										<td class='article'>부모글내용</td>
										<td class='conts'><?=stripslashes($ir[ttContent])?></td>
									</tr>
								</tbody>
						</table>
					</div>
<?
}
?>
					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article">작성자</td>
										<td class="conts"><input type=text name=ttID class="input_text" value='<?=$ttID?>'></td>
									</tr>
									<tr>
										<td class="article">작성자명</td>
										<td class="conts"><input type=text name=ttName class="input_text" value='<?=$ttName?>'></td>
									</tr>
									<tr>
										<td class="article">댓글내용</td>
										<td class="conts"><textarea name=ttContent class="input_text" style="width:98%;height:200px;"><?=$row[ttContent]?></textarea></td>
									</tr>
								</tbody>
						</table>
					</div>

<?=_submitBTN("_product_talk.list.php")?>
</form>