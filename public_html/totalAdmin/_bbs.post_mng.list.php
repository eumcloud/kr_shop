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
		<col width="120"/><col width="200"/><col width="120"/><col width="200"/><col width="120"/><!-- 마지막값은수정안함 --><col width="*"/>
	</colgroup>
	<tbody> 
		<tr>
			<td class="article">게시판선택</td>
			<td class="conts"><?=_InputSelect( "pass_menu" , array_keys($_ARR_BBS), $pass_menu , "" , array_values($_ARR_BBS) , '-선택-') ?></td>
			<td class="article">등록자ID </td>
			<td class="conts"><input type="text" name="pass_inid" class="input_text" style="width:100px"  value='<?=$pass_inid?>' /></td>
			<td class="article">공지사항</td>
			<td class="conts"><?=_InputSelect( "pass_notice" , array("Y","N"), $pass_notice , "" , array("적용","미적용") , '-선택-') ?></td>
		</tr>
		<tr>
			<td class="article">등록자명</td>
			<td class="conts"><input type="text" name="pass_writer" class="input_text" style="width:100px"  value='<?=$pass_writer?>' /></td>
			<td class="article">제목</td>
			<td class="conts"><input type="text" name="pass_title" class="input_text" style="width:100px"  value='<?=$pass_title?>' /></td>
			<td class="article">베스트글</td>
			<td class="conts"><?=_InputSelect( "pass_best" , array("Y","N"), $pass_best , "" , array("적용","미적용") , '-선택-') ?></td>
		</tr>
	</tbody> 
</table>

<!-- 버튼영역 -->
<div class="top_btn_area">
	<div class="btn_line_up_center">
		<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
		<?if($mode == "search") {?>
		<span class="shop_btn_pack"><span class="blank_3"></span></span>
		<span class="shop_btn_pack"><a href="_bbs.post_mng.list.php" class="medium gray" title="목록" >전체목록</a></span>
		<?}?>
		<span class="shop_btn_pack"><span class="blank_3"></span></span>
		<span class="shop_btn_pack"><a href="_bbs.post_mng.form.php?_mode=add" class="medium red" title="게시물등록" >게시물등록</a></span>

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

<form name=listFrm id="listFrm" method=post action='_bbs.post_mng.pro.php' >
<input type=hidden name=_mode value=''>
<input type=hidden name=_PVSC value=<?=$_PVSC?>>

<table class="list_TB" summary="리스트기본">
	<!-- <colgroup>
		<col width="120px"/><col width="200px"/><col width="120px"/><col width="*"/>
	</colgroup> -->
	<thead>
		<tr>
			<th scope="col" class="colorset">선택</th>
			<th scope="col" class="colorset">번호</th>
			<th scope="col" class="colorset">게시판이름</th>
			<th scope="col" class="colorset">제목</th>
			<th scope="col" class="colorset">작성자</th>
			<th scope="col" class="colorset">등록일</th>
			<th scope="col" class="colorset">관리</th>
		</tr>
	</thead> 
	<tbody> 


<?PHP

	// 검색 체크
	$s_query = " 
		from odtBbs as b
		inner join odtBbsInfo as bi on (bi.bi_uid = b.b_menu)
		left join odtMember as m on (m.id=b.b_inid)
		where 1 
	";
	
	if( $pass_menu !="" ) { $s_query .= " and b.b_menu='{$pass_menu}' "; }
	if( $mode == "search" ) {
		if( $pass_inid!="" ) { $s_query .= " and b.b_inid like '%{$pass_inid}%' "; }
		if( $pass_writer !="" ) { $s_query .= " and b.b_writer like '%{$pass_writer}%' "; }
		if( $pass_title !="" ) { $s_query .= " and b.b_title like '%{$pass_title}%' "; }
		if( $pass_content !="" ) { $s_query .= " and b.b_content like '%{$pass_content}%' "; }
		if( $pass_notice !="" ) { $s_query .= " and b.b_notice = '{$pass_notice}' "; }
		if( $pass_best !="" ) { $s_query .= " and b.b_bestview = '{$pass_best}' "; }
	}

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select b.* ,bi.*, m.name, CASE b_depth WHEN 1 THEN b_uid ELSE b_relation END as b_orderuid {$s_query} ORDER BY b_orderuid desc , b_depth asc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$row){

		$_mod = "<span class='shop_btn_pack'><a href='_bbs.post_mng.form.php?_mode=modify&_uid=".$row[b_uid]."&_PVSC=".$_PVSC."' class='small white' title='수정' >수정</a></span>";
		$_reply = ($row[b_depth] <> 2 ? "<span class='shop_btn_pack'><a href='_bbs.post_mng.form.php?_mode=reply&_uid=".$row[b_uid]."&_PVSC=".$_PVSC."' class='small blue' title='답글' >답글</a></span><span class='shop_btn_pack'><span class='blank_3'></span></span>" : "");
		$_del = "<span class='shop_btn_pack'><a href='#none' onclick='del(\"_bbs.post_mng.pro.php?_mode=delete&_uid=".$row[b_uid]."&_PVSC=".$_PVSC."\");' class='small gray' title='삭제' >삭제</a></span>";
		$_go_link = "
		<span class='shop_btn_pack'><a href='/?pn=board.view&_menu=".$row[b_menu]."&_uid=".$row[b_uid]."' target='_blank' class='small white' title='바로가기' >바로가기</a></span>";

		$_num = $TotalCount - $count - $k ;

		// 이미지 정보
		$app_img_info  = "이미지1 : " . ($row[b_img1] ? "<B>등록</B>" . " / 위치 : " . ($row[b_img1_loc]=="bottom" ? "하단" : "상단" ) : "미등록" );
		$app_img_info .= "<br>이미지2 : " . ($row[b_img2] ? "<B>등록</B>" . " / 위치 : " . ($row[b_img2_loc]=="bottom" ? "하단" : "상단" ) : "미등록" );

		// 부가정보(공감/hit/댓글 수)
		$app_append_info = "조회수 : " . number_format($row[b_hit]);
		$app_append_info .= " / 댓글수 : " . number_format($row[b_talkcnt]);

		if($row[b_notice] == "Y") $notice_icon = $arr_adm_button["공지"]." ";
		else $notice_icon = "";

		echo "
			<tr>
				<td><input type=checkbox name='chk_uid[".$row[b_uid]."]' value='Y' class=class_uid></td>
				<td>".${_num}."</td>
				<td>".$row[bi_name]."</td>
				<td style='text-align:left!important'>" .($row[b_depth] == 2 ? "└":"") . $notice_icon. strip_tags(htmlspecialchars($row[b_title]))."</td>
				<td>".showUserInfo($row[b_inid],htmlspecialchars($row[b_writer]))."</td>
				<td>".substr($row[b_rdate],0,10)."</td>
				<td>
					<div class='btn_line_up_right'>
						${_reply}
						${_mod}
						<span class='shop_btn_pack'><span class='blank_3'></span></span>
						${_del}	
						<span class='shop_btn_pack'><span class='blank_3'></span></span>
						${_go_link}
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