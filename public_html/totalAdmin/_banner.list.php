<?PHP

	include_once("inc.header.php");

	// 배너 위치 설정
	if( $_loc ) {
		$_loc_name = $arr_banner_loc[$_loc];
		$app_pass_loc = "<input type=hidden name=pass_loc value='{$_loc}'>";
	}
	else {
		$_loc_name = "";
		$app_pass_loc = "
			<td class='article'>배너구분</td>
			<td class='conts'>" . _InputSelect( 'pass_loc' , array_keys($arr_banner_loc) , $pass_loc , '' , array_values($arr_banner_loc) , '-선택-') . "</td>
		";
	}

?>

				<!-- 검색영역 -->
				<div class="form_box_area">
					<form name=searchfrm method=post action='<?=$PHP_SELF?>' autocomplete='off' >
					<input type=hidden name=mode value=search>
					<table class="form_TB" summary="검색항목">
						<tbody> 
							<tr>
								<?=$app_pass_loc?>
								<td class="article">노출여부</td>
								<td class="conts"><?=_InputSelect( "pass_view" , array('Y','N') , $pass_view , "" , array('노출','숨김') , "-선택-")?></td>
								<td class="article">배너타이틀</td>
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
							<span class="shop_btn_pack"><a href="_banner.form.php?_loc=<?=$_loc?>&_mode=add" class="medium red" title="배너등록" >배너등록</a></span>

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
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">구분</th>
								<th scope="col" class="colorset">순위</th>
								<th scope="col" class="colorset">게재일</th>
								<th scope="col" class="colorset">노출</th>
								<th scope="col" class="colorset">배너타이틀/배너이미지</th>
								<th scope="col" class="colorset">타겟/링크</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 

<?PHP

	// 검색 체크
	$s_query = " where b_type='normal'";
	if( $_loc !="" ) { $s_query .= " and b_loc='${_loc}' "; }
	if( $pass_loc !="" ) { $s_query .= " and b_loc='${pass_loc}' "; }
	if( $pass_view !="" ) { $s_query .= " and b_view='${pass_view}' "; }
	if( $pass_sdate !="" ) { $s_query .= " and b_sdate='${pass_sdate}' "; }
	if( $pass_edate !="" ) { $s_query .= " and b_edate='${pass_edate}' "; }

	$s_query .= " and b_loc in ('".implode("','",array_keys($arr_banner_loc))."') ";

	$listmaxcount = 10 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtBanner $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from odtBanner {$s_query} ORDER BY b_uid desc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_link_out = "<span class='shop_btn_pack'><a type=button class='small white' href='" . $v[b_link] . "' target='_blank'>바로가기</a></span>";
		$_mod = "<span class='shop_btn_pack'><input type=button value='수정' class='input_small blue' onclick='location.href=(\"_banner.form.php?_loc={$_loc}&_mode=modify&_uid=$v[b_uid]&_PVSC=${_PVSC}\");'></span>";
		$_del = "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='del(\"_banner.pro.php?_mode=delete&_uid=$v[b_uid]&_PVSC=${_PVSC}\");'></span>";

		$_num = $TotalCount - $count - $k ;

		$app_src = "..".IMG_DIR_BANNER.$v[b_img];
		if( $v[b_img] ) {
			$app_title = "";
			if( $v[b_title] ) {
				$app_title = " title='$v[b_title]' ";
			}
			$app_banner = "<img src='$app_src' style='max-width:150px;margin-top:5px;' ${app_title}>";
		}
		else {
			$app_banner = "&nbsp;";
		}

		if( $v[b_view] == "N") {
			$app_view = "노출안함";
		}
		else {
			$app_view = "노출";
		}


		echo "
							<tr>
								<td>".${_num}."</td>
								<td>" . $arr_banner_loc[$v[b_loc]] . "</td>
								<td>" . $v[b_idx] . "</td>
								<td>" . $v[b_sdate] . " ~ " . $v[b_edate] . "</td>
								<td>${app_view}</td>
								<td>$v[b_title]<br>${app_banner}</td>
								<td>$v[b_target]<br><A HREF='$v[b_link]' target=_blank>$v[b_link]</A></td>
								<td>
									<div class='btn_line_up_center'>
										".$_mod."
										<span class='shop_btn_pack'><span class='blank_3'></span></span>
										".$_del."
									</div>
									<br><div class='btn_line_up_center'>
										".${_link_out}."
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
						<?=pagelisting($listpg, $Page, $listmaxcount,"?${_PVS}&listpg=")?>
					</div>
					<!-- // 페이지네이트 -->

			</div>



<?PHP
	include_once("inc.footer.php");
?>