<?PHP

	include_once("inc.header.php");


	// 검색 체크
	$s_query = " where userType = 'C' and id !='onedaynet' ";
	if( $pass_id !="" ) { $s_query .= " and id like '%${pass_id}%' "; }
	if( $pass_name !="" ) { $s_query .= " and name like '%${pass_name}%' "; }
	if( $pass_cName !="" ) { $s_query .= " and cName like '%${pass_cName}%' "; }

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtMember $s_query ");
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
								<td class="article">아이디</td>
								<td class="conts"><input type=text name=pass_id class=input_text value="<?=$pass_id?>"></td>
								<td class="article">공급업체명</td>
								<td class="conts"><input type=text name=pass_cName class=input_text value="<?=$pass_cName?>"></td>
								<td class="article">담당자명</td>
								<td class="conts"><input type=text name=pass_name class=input_text value="<?=$pass_name?>"></td>
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
							<span class="shop_btn_pack"><a href="_entershop.form.php?_mode=add" class="medium red" title="입점업체등록" >입점업체등록</a></span>
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
								<th scope="col" class="colorset">아이디</th>
								<th scope="col" class="colorset">밴더사명</th>
								<th scope="col" class="colorset">공급업체명</th>
								<th scope="col" class="colorset">대표자</th>
								<th scope="col" class="colorset">담당자명</th>
								<th scope="col" class="colorset">전화번호</th>
								<th scope="col" class="colorset">등록일</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 
<?PHP

	$res = _MQ_assoc(" select *, concat(tel1,'-',tel2,'-',tel3) as tel, concat(htel1,'-',htel2,'-',htel3) as htel from odtMember {$s_query} ORDER BY serialnum desc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_mod = "<span class='shop_btn_pack'><input type=button value='수정' class='input_small white' onclick='location.href=(\"_entershop.form.php?_mode=modify&serialnum=$v[serialnum]&_PVSC=${_PVSC}\");'></span>";
		$_del = "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='del(\"_entershop.pro.php?_mode=delete&serialnum=$v[serialnum]&_PVSC=${_PVSC}\");'></span>";
		$_login = "<span class='shop_btn_pack'><input type=button value='자동로그인' class='input_small gray'  onclick='window.open(\"./?_mode=autologin&serialnum=$v[serialnum]\");'></span>";
		$_num = $TotalCount - $count - $k ;

		echo "
			<tr>
				<td>".${_num}."</td>
				<td>" . $v[id] . "</td>
				<td>" . $v[bannder] . "</td>
				<td>" . $v[cName] . "</td>
				<td>" . $v[ceoName] . "</td>
				<td>" . $v[name] . "</td>
				<td>" . implode("<br/>",array(phone_print($v[tel1],$v[tel2],$v[tel3]),phone_print($v[htel1],$v[htel2],$v[htel3]))) . "</td>
				<td>" . date("Y.m.d" , $v[signdate]) . "</td>
				<td>
					<div class='btn_line_up_center'>
						<span class='shop_btn_pack'><span class='blank_3'></span></span>
						".$_mod."
						<span class='shop_btn_pack'><span class='blank_3'></span></span>
						".$_del."
						<span class='shop_btn_pack'><span class='blank_3'></span></span>
						".$_login."
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