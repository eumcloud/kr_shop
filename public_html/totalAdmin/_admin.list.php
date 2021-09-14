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
								<td class="article">아이디</td>
								<td class="conts"><input type=text name=pass_id class=input_text value="<?=$pass_id?>"></td>
								<td class="article">관리자명</td>
								<td class="conts"><input type=text name=pass_name class=input_text value="<?=$pass_name?>"></td>
							</tr>
							<tr>
								<td class="conts" colspan="4">
									<?=_DescStr("쇼핑몰관리자중 적어도 한명의 <b>최고관리자</b>가 필요합니다. ")?>
									<?=_DescStr("<b>최고관리자</b>란 [환경설정 > 관리페이지메뉴관리]메뉴의 모든권한을 가진 관리자를 의미합니다.")?>
									<?=_DescStr("관리자 아이디 삭제시 <b>최고관리자</b>아이디가 삭제되지 않도록 주의해주시기 바랍니다.")?>
									<?=_DescStr("관리자 아이디 변경방법 <br>1. 변경할 아이디로 쇼핑몰관리자 추가 <br>2. [환경설정 > 관리페이지메뉴관리]메뉴에서 추가된 관리자에게 권한 부여 <br>3. 기존 관리자아이디 삭제")?>
								</td>
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
							<span class="shop_btn_pack"><a href="_admin.form.php?_mode=add" class="medium red" title="쇼핑몰관리자등록" >쇼핑몰관리자등록</a></span>

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
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">아이디</th>
								<th scope="col" class="colorset">관리자명</th>
								<th scope="col" class="colorset">이메일</th>
								<th scope="col" class="colorset">전화번호</th>
								<th scope="col" class="colorset">등록일</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 

<?PHP

	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_id !="" ) { $s_query .= " and id like '%${pass_id}%' "; }
	if( $pass_name !="" ) { $s_query .= " and name like '%${pass_name}%' "; }

	$listmaxcount = 10 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtAdmin $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from odtAdmin {$s_query} ORDER BY serialnum desc limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_mod = "<span class='shop_btn_pack'><input type=button value='수정' class='input_small white' onclick='location.href=(\"_admin.form.php?_mode=modify&serialnum=$v[serialnum]&_PVSC=${_PVSC}\");'></span>";
		$_del = $v[serialnum]>1 ? "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='del(\"_admin.pro.php?_mode=delete&serialnum=$v[serialnum]&_PVSC=${_PVSC}\");'></span>" : "<span class='shop_btn_pack'><input type=button value='삭제' class='input_small gray'  onclick='alert(\"최고관리자는 삭제할 수 없습니다.\");'></span>";

		$_num = $TotalCount - $count - $k ;

		echo "
			<tr>
				<td>".${_num}."</td>
				<td>" . $v[id] . "</td>
				<td>" . $v[name] . "</td>
				<td>" . $v[email] . "</td>
				<td>" . $v[htel] . "</td>
				<td>" . date("Y.m.d" , $v[inputDate]) . "</td>
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