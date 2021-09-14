<?PHP
	include_once("inc.header.php");


	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);

    if(!$idtype) $idtype="Connect_Route";
    if(!$limit) $limit="100";
	if($id) $s_query = " where ".$idtype." like '%".$id."%' ";

?>


				<!-- 검색영역 -->
				<div class="form_box_area">
					<form name=searchfrm method=post action='<?=$_SERVER["PHP_SELF"]?>'>
					<input type=hidden name=mode value=search>
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><col width="500px"/><col width="120px"/><col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody> 
							<tr>
								<td class="article">검색방법</td>
								<td class="conts"><?=_InputSelect( "idtype" , array("Connect_Route" , "Connect_IP") , $idtype , " id='idtype' value='Connect_Route' " , array("접속경로" , "접속IP") , "-선택-")?> <input type="text" id="id" name="id" style='width:150px' class=input_text value="<?=$id?>"><?=_DescStr("접속경로가 없는 내역은 즐겨찾기나 도메인을 바로 입력해서 접속한 경우입니다.")?></td>
								<td class="article">목록수</td>
								<td class="conts"><?=_InputSelect( "limit" , array("100" , "1000" , "2000") , $idtype , " id='limit' value='1000' onChange='search();' " , "" , "-선택-")?></td>
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
							<col width="60px"/><col width="130px"/><col width="130px"/><col width="130px"/><col width="*"/>
						</colgroup> 
						<thead>
							<tr>
								<th scope="col" class="colorset">번호</th>
								<th scope="col" class="colorset">아이피</th>
								<th scope="col" class="colorset">접속시간</th>
								<th scope="col" class="colorset">키워드</th>
								<th scope="col" class="colorset">접속경로</th>
							</tr>
						</thead> 
						<tbody> 
<?PHP

	$listmaxcount = $limit ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(distinct(Connect_IP)) as cnt from odtCounter $s_query  ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);


  $que = "select * from odtCounter ".$s_query." group by Connect_IP order by Time desc  limit $count , $listmaxcount ";
	$res = _MQ_assoc($que);
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='45px'>내용이 없습니다</td></tr>";
	foreach($res as $k=>$row) {
		$keyword = "";
		$tmp = explode("?",$row[Connect_Route]);
		$tmp = explode("&",$tmp[1]);
		for($i=0;$i<count($tmp);$i++) {
			$tmp2 = explode("=",$tmp[$i]);
			if(strtolower($tmp2[0]) == "query") {
				$keyword = rawurldecode($tmp2[1]) ;//iconv("euckr","utf-8",unescape($tmp2[1]));
				if(!$keyword) $keyword = rawurldecode($tmp2[1]);
			}else if(strtolower($tmp2[0]) == "q") {
				$keyword = rawurldecode($tmp2[1]) ;//iconv("euckr","utf-8",unescape($tmp2[1]));
				if(!$keyword) $keyword = rawurldecode($tmp2[1]);
			}
		}

		$_num = $TotalCount - $count - $k ;

		$Percent = number_format($Percent,2);

		echo "
							<tr>
								<td>".${_num}."</td>
								<td>".$row[Connect_IP]."</td>
								<td>".date('y-m-d H:i:s',$row[Time])."</td>
								<td>".$keyword."</td>
								<td align='left'>".($row[Connect_Route] ? "<a href='".$row[Connect_Route]."' target='_blank'>".$row[Connect_Route]."</a>" : $row[Connect_Route])."</td>
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