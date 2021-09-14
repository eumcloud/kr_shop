<?PHP

	include_once("inc.header.php");

	// - 게시판 종류 배열형태로 추출 ---
	$_ARR_BBS = arr_board();

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
								<td class="article">게시판선택</td>
								<td class="conts"><?=_InputSelect( "pass_menu" , array_keys($_ARR_BBS), $pass_menu , "" , array_values($_ARR_BBS) , '-선택-') ?></td>
								<td class="article">등록자ID </td>
								<td class="conts"><input type="text" name="pass_inid" class="input_text" style="width:100px"  value='<?=$pass_inid?>' /></td>
							</tr>
							<tr>
								<td class="article">등록자명</td>
								<td class="conts"><input type="text" name="pass_writer" class="input_text" style="width:100px"  value='<?=$pass_writer?>' /></td>
								<td class="article">댓글내용</td>
								<td class="conts"><input type="text" name="pass_content" class="input_text" style="width:200px"  value='<?=$pass_content?>' /></td>
							</tr>
						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_bbs.comment_mng.list.php" class="medium gray" title="목록" >전체목록</a></span>
							<?}?>

						</div>
					</div>
					</form>
				</div>
				<!-- // 검색영역 -->


				<!-- 리스트영역 -->
				<div class="content_section_inner">
					

					<div class="ctl_btn_area">
						<span class="shop_btn_pack"><a href="#none" class="small white" title="전체선택" onclick="selectAll()">전체선택</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="#none" class="small white" title="선택해제" onclick="unselectAll()">선택해제</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="#none" class="small gray" title="선택삭제" onclick="selectDelete()">선택삭제</a></span>
					</div>

					<form name=listFrm id="listFrm" method=post action='_bbs.comment_mng.pro.php' >
					<input type=hidden name=_mode value=''>
					<input type=hidden name=_PVSC value=<?=$_PVSC?>>

					<table class="list_TB" summary="리스트기본">
						<!-- <colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><col width="*"/>
						</colgroup> -->
						<thead>
							<tr>
								<th scope="col" class="colorset">선택</th>
								<th scope="col" class="colorset">게시판명/게시물제목/댓글내용</th>
								<th scope="col" class="colorset">작성자</th>
								<th scope="col" class="colorset">등록일</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 


<?PHP

	// 검색 체크
	$s_query = " 
		from odtBbsComment as bt
		inner join odtBbs as b on (b.b_uid = bt_buid)
		inner join odtBbsInfo as bi on (bi.bi_uid = b.b_menu)
		left join odtMember as m on (m.id=b.b_inid)
		where 1 
	";
	
	if( $pass_menu !="" ) { $s_query .= " and b.b_menu='{$pass_menu}' "; }
	if( $mode == "search" ) {
		if( $pass_inid!="" ) { $s_query .= " and bt.bt_inid like '%{$pass_inid}%' "; }
		if( $pass_writer !="" ) { $s_query .= " and bt.bt_writer like '%{$pass_writer}%' "; }
		if( $pass_content !="" ) { $s_query .= " and bt.bt_content like '%{$pass_content}%' "; }
	}

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select bt.*, b.* ,bi.*, m.name {$s_query} ORDER BY bt_uid desc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$row){

		$_mod = "<span class='shop_btn_pack'><a href='_bbs.comment_mng.form.php?_mode=modify&_uid=".$row[bt_uid]."&_PVSC=".$_PVSC."' class='small white' title='수정' >수정</a></span>";
		$_del = "<span class='shop_btn_pack'><a href='#none' onclick='del(\"_bbs.comment_mng.pro.php?_mode=delete&_uid=".$row[bt_uid]."&_PVSC=".$_PVSC."\");' class='small gray' title='삭제' >삭제</a></span>";
		$_go_link = "
		<span class='shop_btn_pack'><a href='/?pn=board.view&_menu=".$row[b_menu]."&_uid=".$row[b_uid]."&btuid=".$row[bt_uid]."' target='_blank' class='small white' title='바로가기' >바로가기</a></span>";

		$_num = $TotalCount - $count - $k ;

		echo "
							<tr>
								<td><input type=checkbox name='chk_uid[".$row[bt_uid]."]' value='Y' class=class_uid></td>
								<td style='text-align:left!important'>
									<b>[" .$row[bi_name]."] ". strip_tags($row[b_title])."</b><br>
									". strip_tags($row[bt_content])."
								</td>
								<td>".showUserInfo($row[bt_inid],$row[bt_writer])."</td>
								<td>".substr($row[bt_rdate],0,10)."</td>
								<td>
									<div class='btn_line_up_center'>
										${_go_link}										
										<span class='shop_btn_pack'><span class='blank_3'></span></span>
										${_mod}
										<span class='shop_btn_pack'><span class='blank_3'></span></span>
										${_del}										
									</div>
								</td>
							</tr>

		";
	}

?>


						</tbody> 
					</table>

					</form>


					<!-- 페이지네이트 -->
					<div class="list_paginate">			
						<?=pagelisting($listpg, $Page, $listmaxcount," ?pass_menu={$pass_menu}&${_PVS}&listpg=" , "Y")?>
					</div>
					<!-- // 페이지네이트 -->


			</div>
<?PHP
	include_once("inc.footer.php");
?>

<SCRIPT>
	 function selectAll() {
		 $('.class_uid').attr('checked',true);
	 }
	 function unselectAll() {
		 $('.class_uid').attr('checked',false);
	 }
	 function selectDelete() {
		 if($('.class_uid').is(":checked")){
			 if(confirm("정말 삭제하시겠습니까?")){
				$("form[name=listFrm]").children("input[name=_mode]").val("select_delete");
				document.listFrm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택하시기 바랍니다.');
		 }
	 }
 </script>