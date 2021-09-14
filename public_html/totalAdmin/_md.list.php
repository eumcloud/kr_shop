<?PHP

	include_once("inc.header.php");


	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_mdName !="" ) { $s_query .= " and mdName like '%${pass_mdName}%' "; }
	if( $pass_mdNick !="" ) { $s_query .= " and mdNick like '%${pass_mdNick}%' "; }

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtMD $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

?>

<form name=searchfrm method=post action='<?=$PHP_SELF?>'>
<input type=hidden name=mode value=search>
				<!-- 검색영역 -->
				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<tbody> 
							<tr>
								<td class="article">이름</td>
								<td class="conts"><input type=text name=pass_mdName class=input_text value="<?=$pass_mdName?>"></td>
								<td class="article">닉네임</td>
								<td class="conts"><input type=text name=pass_mdNick class=input_text value="<?=$pass_mdNick?>"></td>
							</tr>
						</tbody> 
					</table>

					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
<?if ($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="전체목록" >전체목록</a></span>
<?}?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_md.form.php?_mode=add" class="medium red" title="MD등록" >MD등록</a></span>
						</div>
					</div>
				</div>
</form>
				<!-- // 검색영역 -->

				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<table class="list_TB" summary="리스트기본">
						<!-- <colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><col width="*"/>
						</colgroup> -->
						<thead>
							<tr>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">이름</th>
								<th scope="col" class="colorset">닉네임</th>
								<th scope="col" class="colorset">특이사항/목표</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 
<?PHP

	$res = _MQ_assoc(" select * from odtMD {$s_query} ORDER BY mdNo desc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_mod = "<span class='shop_btn_pack'><input type=button value='수정' class='input_small white' onclick='location.href=(\"_md.form.php?_mode=modify&mdNo=$v[mdNo]&_PVSC=${_PVSC}\");'></span>";
		$_del = "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='del(\"_md.pro.php?_mode=delete&mdNo=$v[mdNo]&_PVSC=${_PVSC}\");'></span>";

		$_num = $TotalCount - $count - $k ;

		echo "
			<tr>
				<td>".${_num}."</td>
				<td>" . $v[mdName] . "</td>
				<td>" . $v[mdNick] . "</td>
				<td style='text-align:left;padding-left:5px;'>특이사항 : ".$v[mdUnique]."<br>목표 : ".$v[mdAim]."</td>
				<td>
					<div class='btn_line_up_center'>
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