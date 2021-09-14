<?PHP
	$pass_menu = $_REQUEST[pass_menu] ? $_REQUEST[pass_menu] : "request";
	// 페이지 표시
	$app_current_link = "/totalAdmin/_request.list.php?pass_menu=" . $pass_menu ;
	include_once("inc.header.php");
?>


				<!-- 검색영역 -->
				<div class="form_box_area">
					<form name=searchfrm method=post action='<?=$_SERVER["PHP_SELF"]?>'>
					<input type=hidden name=mode value=search>
					<input type=hidden name=pass_menu value=<?=$pass_menu?>>
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody> 
							<tr>
								<td class="article">제목</td>
								<td class="conts"><input type="text" name="pass_title" class="input_text" style="width:100px"  value='<?=$pass_title?>' /></td>
								<td class="article">답변상태</td>
								<td class="conts"><?=_InputSelect( "pass_status" , array('답변대기','답변완료'), $pass_status , "  " ,  "" , '-선택-') ?></td>
							</tr>
						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_request.list.php?pass_menu=<?=$pass_menu?>" class="medium gray" title="목록" >전체목록</a></span>
							<?}?>
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
<?if( in_array($pass_menu , array("request")) ) {?>			
								<th scope="col" class="colorset">회원ID</th>
<?}?>
<?if( in_array($pass_menu , array("partner")) ) {?>			
								<th scope="col" class="colorset">문의자명</th>
								<th scope="col" class="colorset">연락처</th>
								<th scope="col" class="colorset">이메일</th>
<?}?>
								<th scope="col" class="colorset">제목</th>
								<th scope="col" class="colorset">상태</th>
								<th scope="col" class="colorset">문의일</th>
								<th scope="col" class="colorset">처리</th>
							</tr>
						</thead> 
						<tbody> 


<?PHP

	// 검색 체크
	$s_query = " from odtRequest where r_menu='{$pass_menu}' ";
	if( $mode == "search" ) {
		if( $pass_title !="" ) { $s_query .= " and r_title like '%{$pass_title}%' "; }
		if( $pass_status !="" ) { $s_query .= " and r_status='{$pass_status}' "; }
	}


	$listmaxcount = 30 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * {$s_query} ORDER BY r_rdate desc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$row){

		$_mod = "<input type=button value='수정' class=btn onclick='location.href=(\"\");'>";
		$_del = "<input type=button value='삭제' class=btn onclick='del(\"_request.pro.php?pass_menu={$pass_menu}&_mode=delete&_uid=$row[r_uid]&_PVSC=${_PVSC}\");'>";

		$_num = $TotalCount - $count - $k ;

		if($row[r_status] == "답변대기") {
			$_status = "<span class='shop_state_pack'><span class='sky'>".$row[r_status]."</span></span>";
		} else {
			$_status = "<span class='shop_state_pack'><span class='gray'>".$row[r_status]."</span></span>";
		}


		echo "
							<tr>
								<td>".${_num}."</td>
".(in_array($pass_menu , array("request")) ? "
								<td>".showUserInfo($row[r_inid],"")."</td>
" : NULL)."
".(in_array($pass_menu , array("partner")) ? "
								<td>".$row[r_comname]."</td>
								<td>".$row[r_tel]."</td>
								<td>".$row[r_email]."</td>
" : NULL)."
								<td>".strip_tags($row[r_title])."</td>
								<td>".$_status."</td>
								<td>".substr($row[r_rdate],0,10)."</td>
								<td>
									<div class='btn_line_up_center'>
										<span class='shop_btn_pack'><a href='_request.form.php?pass_menu={$pass_menu}&_mode=modify&_uid=$row[r_uid]&_PVSC=${_PVSC}' class='small white' title='수정' >수정</a></span>
										<span class='shop_btn_pack'><span class='blank_3'></span></span>
										<span class='shop_btn_pack'><a href='#none' onclick=\"del('_request.pro.php?pass_menu={$pass_menu}&_mode=delete&_uid=$row[r_uid]&_PVSC=${_PVSC}');\" class='small gray' title='삭제' >삭제</a></span>
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