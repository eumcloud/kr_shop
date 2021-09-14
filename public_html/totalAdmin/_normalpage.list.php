<?PHP

	include_once("inc.header.php");

?>

				<!-- 검색영역 -->
				<div class="form_box_area">
					<form name=searchfrm method=post action='<?=$PHP_SELF?>' autocomplete='off' >
					<input type=hidden name=mode value=search>
					<table class="form_TB" summary="검색항목">
						<tbody> 
							<tr>
								<td class="article">노출여부</td>
								<td class="conts"><?=_InputSelect( "pass_view" , array('Y','N') , $pass_view , "" , array('노출','숨김') , "-선택-")?></td>
								<td class="article">페이지명</td>
								<td class="conts"><input type=text name=pass_title class=input_text value="<?=$pass_title?>"></td>
							</tr>
						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == search) {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="전체목록" >전체목록</a></span>
							<?}?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_normalpage.form.php?_mode=add" class="medium red" title="페이지등록" >페이지등록</a></span>

						</div>
					</div>
					</form>
				</div>
				<!-- // 검색영역 -->



				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<table class="list_TB" summary="리스트기본">
						<thead>
							<tr>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">순서</th>
								<th scope="col" class="colorset">노출</th>
								<th scope="col" class="colorset">페이지명</th>
								<th scope="col" class="colorset">등록일</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 

<?PHP

	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_view !="" ) { $s_query .= " and np_view='${pass_view}' "; }
	if( $pass_title !="" ) { $s_query .= " and np_title like '%${pass_title}%' "; }

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtNormalPage $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from odtNormalPage {$s_query} ORDER BY np_idx asc , np_uid asc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$app_link = "/?pn=service.page.view&pageid=" . $v[np_id];
		$_link_out = "<span class='shop_btn_pack'><a type=button class='small white' href='" . $app_link . "' target='_blank'>바로가기</a></span>";
		$_mod = "<span class='shop_btn_pack'><input type=button value='수정' class='input_small white' onclick='location.href=(\"_normalpage.form.php?_mode=modify&_uid=$v[np_uid]&_PVSC=${_PVSC}\");'></span>";
		$_del = "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='del(\"_normalpage.pro.php?_mode=delete&_uid=$v[np_uid]&_PVSC=${_PVSC}\");'></span>";

		$_num = $TotalCount - $count - $k ;

		if( $v[np_view] == "N") {
			$app_view = "노출안함";
		}
		else {
			$app_view = "노출";
		}

		echo "
			<tr>
				<td>".${_num}."</td>
				<td>" . $v[np_idx] . "</td>
				<td>" . $app_view . "</td>
				<td>" . $v[np_title] . "</td>
				<td>" . substr($v[np_rdate],0,10) . "</td>
				<td>
					<div class='btn_line_up_center'>
						".${_link_out}."
						<span class='shop_btn_pack'><span class='blank_3'></span></span>
						".$_mod."
						<span class='shop_btn_pack'><span class='blank_3'></span></span>
						".$_del."
					</div>
				</td>
			</tr>
		";

	}

?>

						</tbody> 
					</table>


					<!-- 페이지네이트 -->
					<div class="list_paginate">			
						<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=")?>
					</div>
					<!-- // 페이지네이트 -->

			</div>

<?PHP
	include_once("inc.footer.php");
?>