<?PHP
	include_once("inc.header.php");
?>

				<!-- 검색영역 -->
				<div class="form_box_area">
					<form name=searchfrm method=get action='<?=$_SERVER["PHP_SELF"]?>'>
					<input type=hidden name=mode value=search>
					<input type=hidden name=pt_type value=<?=$pt_type?>>
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody>
							<tr>
								<td class="article">상품명</td>
								<td class="conts"><input type="text" name="pass_pname" class="input_text" style="width:150px"  value='<?=$pass_pname?>' /></td>
								<td class="article">상품코드</td>
								<td class="conts"><input type="text" name="pass_ttProCode" class="input_text" style="width:150px"  value='<?=$pass_ttProCode?>' /></td>
							</tr>
							<tr>
								<td class="article">작성자아이디</td>
								<td class="conts"><input type="text" name="pass_ttID" class="input_text" style="width:150px"  value='<?=$pass_ttID?>' /></td>
								<td class="article">댓글내용</td>
								<td class="conts"><input type="text" name="pass_ttContent" class="input_text" style="width:150px"  value='<?=$pass_ttContent?>' /></td>
							</tr>
						</tbody>
					</table>

					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>?pt_type=<?=$pt_type?>" class="medium gray" title="목록" >전체목록</a></span>
							<?}?>
						</div>
					</div>
					</form>
				</div>
				<!-- // 검색영역 -->



				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<table class="list_TB" summary="리스트기본">
						<colgroup>
							<col width="50"/><col width="100"/><col width="150"/><col width="100"/><col width="*"/><col width="100"/><col width="140"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset" colspan=2>상품정보</th>
								<th scope="col" class="colorset">작성자</th>
								<th scope="col" class="colorset">댓글내용</th>
								<th scope="col" class="colorset">작성일</th>
								<th scope="col" class="colorset">비고</th>
							</tr>
						</thead>
						<tbody>

<?PHP
	// 검색 체크
	$s_query = "
		from odtTt as tt 
		inner join odtProduct as p on (tt.ttProCode = p.code) 
		where 1 
	";

	if( $mode == "search" ) {
		if( $pass_pname !="" ) { $s_query .= " and p.name like '%${pass_pname}%' "; }
		if( $pass_ttProCode !="" ) { $s_query .= " and tt.ttProCode like '%${pass_ttProCode}%' "; }
		if( $pass_ttID !="" ) { $s_query .= " and tt.ttID like '%${pass_ttID}%' "; }
		if( $pass_ttContent !="" ) { $s_query .= " and tt.ttContent  like '%${pass_ttContent}%' "; }
	}

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ("select count(*) as cnt ".$s_query);
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$que = "select
		tt.*, p.name as p_name, p.prolist_img, p.main_img,
		(select count(*) from odtTt as tt2 where tt2.ttIsReply=1 and tt2.ttSNo=tt.ttNo) as relation_cnt,
		case ttIsReply when 0 then ttNo else ttSNo end as orderby_uid
	".$s_query."
	order by orderby_uid desc , ttNo asc
	 limit $count , $listmaxcount";

	$res = _MQ_assoc($que);
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v){

		foreach($v as $sk=>$sv){ $v[$sk] = htmlspecialchars($sv); }

		$_add = "<span class='shop_btn_pack'><a href='_product_talk.form.php?_mode=add&ttNo=". urlencode($v[ttNo]) . "&_PVSC=${_PVSC}' class='small blue' title='댓글' >댓글</a></span>";
		$_mod = "<span class='shop_btn_pack'><a href='_product_talk.form.php?_mode=modify&ttNo=". urlencode($v[ttNo]) . "&_PVSC=${_PVSC}' class='small white' title='수정' >수정</a></span>";
		$_del = "<span class='shop_btn_pack'><a href='#none' onclick='del(\"_product_talk.pro.php?_mode=delete&ttNo=". urlencode($v[ttNo]) . "&_PVSC=${_PVSC}\");' class='small gray' title='삭제' >삭제</a></span>";

		if($v[ttIsReply] == "1") {
			$_add="";
			$_reple_icon = "┖";
		} 
		else {
			$_reple_icon = "";
		}

		$_p_img = $v[prolist_img] ? $v[prolist_img] : $v[main_img];

		$_num = $TotalCount - $count - $k ;

		echo "
							<tr>
								<td>" . $_num . "</td>
								<td><img src='".replace_image(IMG_DIR_PRODUCT.$_p_img)."' style='width:100px'></td>
								<td>" . $v[p_name]."<br>".$v[ttProCode] ."</td>
								<td>" . $v[ttName] . ($v[ttID]!='master'?"<br>(" .showUserInfo($v[ttID]) . ")":"")."</td>
								<td style='text-align:left;'>" . $_reple_icon." ".nl2br(stripslashes($v[ttContent])) . "</td>
								<td>" . str_replace(" ","<br>",$v[ttRegidate]) . "</td>
								<td>
									<div class='btn_line_up_right'>
										${_add}
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