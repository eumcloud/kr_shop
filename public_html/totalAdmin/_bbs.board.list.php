<?PHP
	include_once("inc.header.php");
?>


				<!-- 검색영역 -->
				<div class="form_box_area">
					<form name=searchfrm method=post action='<?=$_SERVER["PHP_SELF"]?>'>
					<input type=hidden name=mode value=search>
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody> 
							<tr>
								<td class="article">게시판아이디</td>
								<td class="conts"><input type="text" name="pass_uid" class="input_text" style="width:100px"  value='<?=$pass_uid?>' /></td>
								<td class="article">게시판이름</td>
								<td class="conts"><input type="text" name="pass_name" class="input_text" style="width:100px"  value='<?=$pass_name?>' /></td>
							</tr>
							<tr>
								<td class="article">노출여부</td>
								<td class="conts"><?=_InputSelect( "pass_view" , array('Y','N'), $pass_view , "" , array('노출','숨김') , '') ?></td>
								<td class="conts"></td>
								<td class="conts"></td>
							</tr>
						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_bbs.board.list.php" class="medium gray" title="목록" >전체목록</a></span>
							<?}?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_bbs.board.form.php?_mode=add" class="medium red" title="게시판만들기" >게시판만들기</a></span>
						</div>
					</div>
					</form>
				</div>
				<!-- // 검색영역 -->

				<!-- 리스트영역 -->
				<div class="content_section_inner">
					

					<table class="list_TB" summary="리스트기본">
						<!-- <colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><col width="*"/>
						</colgroup> -->
						<thead>
							<tr>
								<th scope="col" class="colorset">번호</th>
								<th scope="col" class="colorset">게시판아이디</th>
								<th scope="col" class="colorset">게시판이름</th>
								<th scope="col" class="colorset">게시글수</th>
								<th scope="col" class="colorset">노출여부</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 


<?PHP

	// 검색 체크
	$s_query = " from odtBbsInfo	where 1 ";
	
	if( $mode == "search" ) {
		if( $pass_uid!="" ) { $s_query .= " and bi_uid like '%{$pass_uid}%' "; }
		if( $pass_name !="" ) { $s_query .= " and bi_name like '%{$pass_name}%' "; }
		if( $pass_view !="" ) { $s_query .= " and bi_view = '".$pass_view."' "; }
	}

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * {$s_query} limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$row){

		$_mod = "<span class='shop_btn_pack'><a href='_bbs.board.form.php?_mode=modify&_uid=".$row[bi_uid]."&_PVSC=".$_PVSC."' class='small white' title='수정' >수정</a></span>";
		$_del = "<span class='shop_btn_pack'><a href='#none' onclick='del(\"_bbs.board.pro.php?_mode=delete&_uid=".$row[bi_uid]."&_PVSC=".$_PVSC."\");' class='small gray' title='삭제' >삭제</a></span>";
		$_go_link = "
		<span class='shop_btn_pack'><a href='/?pn=board.list&_menu=".$row[bi_uid]."' target='_blank' class='small white' title='바로가기' >바로가기</a></span>";

		$_num = $TotalCount - $count - $k ;


		echo "
							<tr>
								<td>".$_num."</td>
								<td>".$row[bi_uid]."</td>
								<td>".$row[bi_name]."</td>
								<td>".number_format($row[bi_post_cnt])."</td>
								<td>".$arr_adm_button[($row[bi_view] == "Y" ? "노출" : "숨김")]."</td>
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


					<!-- 페이지네이트 -->
					<div class="list_paginate">			
						<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
					</div>
					<!-- // 페이지네이트 -->


			</div>
<?PHP
	include_once("inc.footer.php");
?>