<?PHP
	include_once("inc.header.php");

?>
				<!-- 검색영역 -->
				<div class="form_box_area">
					<form name=searchfrm method=post action='<?=$PHP_SELF?>' autocomplete='off' >
					<input type=hidden name=mode value=search>
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody> 
							<tr>
								<td class="article">노출여부</td>
								<td class="conts"><?=_InputSelect( "pass_view" , array('Y','N') , $pass_view , "" , array('노출','숨김') , "-선택-")?></td>
								<td class="conts"></td>
								<td class="conts"></td>
							</tr>
						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == search) {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="목록" >전체목록</a></span>
							<?}?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_popup.form.php?_mode=add" class="medium red" title="팝업등록" >팝업등록</a></span>
						</div>
					</div>
					</form>
				</div>
				<!-- // 검색영역 -->



				<!-- 리스트영역 -->
				<div class="content_section_inner">
					

					<!-- 리스트 제어버튼영역
					<div class="top_btn_area">
						<span class="shop_btn_pack"><a href="#none" class="small white" title="전체선택" >전체선택</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="#none" class="small white" title="전체선택" >전체선택해제</a></span>
					</div>
					<!-- // 리스트 제어버튼영역 -->


					<table class="list_TB" summary="리스트기본">
						<!-- <colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><col width="*"/>
						</colgroup> -->
						<thead>
							<tr>
								<th scope="col" class="colorset">번호</th>
								<th scope="col" class="colorset">순위</th>
								<th scope="col" class="colorset">노출/일정</th>
								<th scope="col" class="colorset">타이틀/이미지</th>
								<th scope="col" class="colorset">타겟/링크</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 


<?PHP

	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_view !="" ) { $s_query .= " and p_view='${pass_view}' "; }

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtPopup $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from odtPopup {$s_query} ORDER BY p_edate desc , p_idx asc limit $count , $listmaxcount ");

	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_link_out = "<span class='shop_btn_pack'><a type=button class='small white' href='" . $v[p_link] . "' target='_blank'>바로가기</a></span>";
		$_mod = "<span class='shop_btn_pack'><a href='_popup.form.php?_mode=modify&_uid=$v[p_uid]&_PVSC=${_PVSC}' class='small white' title='수정' >수정</a></span>";
		$_del = "<span class='shop_btn_pack'><a href='#none' onclick='del(\"_popup.pro.php?_mode=delete&_uid=$v[p_uid]&_PVSC=${_PVSC}\");' class='small gray' title='삭제' >삭제</a></span>";

		$_num = $TotalCount - $count - $k ;

		$app_src = "..".IMG_DIR_BANNER.$v[p_img];
		if( $v[p_img] && @file_exists($app_src)) {
			$_s = @getimagesize($app_src);
			if( $_s[0] > 150 ) {
				$_s[0] = 150;
			}
			$app_title = "";
			if( $v[p_title] ) {
				$app_title = " title='$v[p_title]' ";
			}
			$app_popup = "<img src='$app_src' width=$_s[0] ${app_title}>";
		}
		else {
			$app_popup = "&nbsp;";
		}

		if( $v[p_view] == "N") {
			$app_view = "노출안함";
		}
		else {
			$app_view = "노출";
		}

		echo "
							<tr>
								<td>".${_num}."</td>
								<td>".$v[p_idx]."</td>
								<td>${app_view}&nbsp;(" . $v[p_sdate] . " ~ " . $v[p_edate] . ")</td>

								<td>$v[p_title]<br>${app_popup}</td>
								<td>$v[p_target]<br><A HREF='$v[p_link]' target=_blank>$v[p_link]</A></td>
								<td>
									<div class='btn_line_up_center'>
										".$_link_out."
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
						<?=pagelisting($listpg, $Page, $listmaxcount," ?pass_menu={$pass_menu}&${_PVS}&listpg=" , "Y")?>
					</div>
					<!-- // 페이지네이트 -->

			</div>



<?PHP
	include_once("inc.footer.php");
?>