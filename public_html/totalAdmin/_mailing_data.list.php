<?PHP
	include_once("inc.header.php");

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기

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
								<td class="article">제목</td>
								<td class="conts"><input type="text" name="pass_title" class="input_text" style="width:100px"  value='<?=$pass_title?>' /></td>
								<td class="conts"></td>
								<td class="conts"></td>
							</tr>
				
						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="목록" >전체목록</a></span>
							<?}?>		
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="_mailing_data.form.php?_mode=add" class="medium red" title="메일링등록" >메일링등록</a></span>					
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
						 <colgroup>
							<col width="120px"/><col width="*"/><col width="200px"/><col width="200px"/><col width="300px"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">제목</th>
								<th scope="col" class="colorset">등록일</th>
								<th scope="col" class="colorset">발송횟수</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 
<?PHP
	// 검색 체크
	$s_query = " where 1 ";
	if( $mode == "search" ) {
		if( $pass_title !="" ) { $s_query .= " and md_title like '%${pass_title}%' "; }
	}

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;


	$res = _MQ(" select count(*) as cnt from odtMailingData $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" 
		select 
			md.* , 
			( select count(distinct(mp_brother)) from odtMailingProfile as mp where mp_mduid = md_uid ) as mp_cnt 
		from odtMailingData as md
		$s_query ORDER BY md_rdate desc limit $count , $listmaxcount
	");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height=45>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$row){

		$_send = "<span class='shop_btn_pack'><a href='#none'  onclick='location.href=(\"_mailing_profile.form.php?_mode=send&_mduid=". $row[md_uid] . "&_PVSC=${_PVSC}\");' class='small blue' title='메일수신자등록' >메일수신자등록</a></span>";
		$_mod = "<span class='shop_btn_pack'><a href='_mailing_data.form.php?_mode=modify&_uid=". $row[md_uid] . "&_PVSC=${_PVSC}' class='small white' title='수정' >수정</a></span>";
		$_del = "<span class='shop_btn_pack'><a href='#none'  onclick='del(\"_mailing_data.pro.php?_mode=delete&_uid=". $row[md_uid] . "&_PVSC=${_PVSC}\");' class='small gray' title='삭제' >삭제</a></span>";

		$_num = $TotalCount - $count - $k ;


		echo "
							<tr>
								<td>" . $_num . "</td>
								<td>".$row[md_title]."</td>
								<td>" . $row[md_rdate] . "</td>
								<td>" . number_format($row[mp_cnt]) . "회</td>
								<td>
									<div class='btn_line_up_center'>
									${_send}									
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