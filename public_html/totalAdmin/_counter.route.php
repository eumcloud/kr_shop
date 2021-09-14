<?PHP
	include_once("inc.header.php");

	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);

?>

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
							<col width="60px"/><col width="130px"/><col width="130px"/><col width="*"/><col width="100px"/>
						</colgroup> 
						<thead>
							<tr>
								<th scope="col" class="colorset">번호</th>
								<th scope="col" class="colorset">접속자수</th>
								<th scope="col" class="colorset">키워드</th>
								<th scope="col" class="colorset">접속경로</th>
								<th scope="col" class="colorset">비율</th>
							</tr>
						</thead> 
						<tbody> 


<?PHP

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select ifnull(sum(Visit_Num),0) as Visit_Num from odtCounterRoute ");
	$total = $res[Visit_Num];

	$res = _MQ(" select count(*) as cnt from odtCounterRoute ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);


    $que = "select Connect_Route, Time, Visit_Num from odtCounterRoute order by Visit_Num desc  limit $count , $listmaxcount ";
	$res = _MQ_assoc($que);
	foreach($res as $ik=>$row){

		$Connect_Route = $row[Connect_Route];

		###  문자열이 긴 경우 지정한 길이만큼 잘라내기 위한 부분 시작  ###
		$strings = $Connect_Route;
		$str02 = strlen($Connect_Route);
		$lenstr = 75;

		for($k=0; $k<$lenstr-1; $k++) {
			if(ord(substr($strings, $k, 1))>127) $k++;
		}

		if($str02 > $lenstr) {
			$str01=substr($strings, 0, $k)."...";
			$Connect_RouteD = stripslashes($str01);
		}
		else {
			$str01=$strings;
			$Connect_RouteD = stripslashes($str01);
		}

		###  문자열이 긴 경우 지정한 길이만큼 잘라내기 위한 부분 끝  ###
		$Time = $row[Time];
		$Time = date("Y년 m월 d일 H시 i분 s초", $Time);
		$Visit_Num = $row[Visit_Num];

		if(!$Connect_Route) $Connect_RouteD = "<font color='#6633CC' face='tahoma'>즐겨찾기 또는 URL 직접입력을 통한 접속</font>";
		else $Connect_RouteD = "<a href='$Connect_Route' target='_blank' title='$Connect_Route'><font color='0197C2'>$Connect_RouteD</font></a>";

		$Percent = round(100*$Visit_Num/$total, 2);

		unset($keyword);
		$tmp = explode("?",$row[Connect_Route]);
		$tmp = explode("&",$tmp[1]);
		for($a=0;$a<count($tmp);$a++) {
			$tmp2 = explode("=",$tmp[$a]);
			if(strtolower($tmp2[0]) == "query") {
				$keyword = rawurldecode($tmp2[1]);
				if(!$keyword ) $keyword = rawurldecode($tmp2[1]);
			}else if(strtolower($tmp2[0]) == "q") {
				$keyword = rawurldecode($tmp2[1]) ;//iconv("euckr","utf-8",unescape($tmp2[1]));
				if(!$keyword) $keyword = rawurldecode($tmp2[1]);
			}
		}

		$_num = $TotalCount - $count - $ik ;

		$Percent = number_format($Percent,2);

		echo "
							<tr>
								<td>".${_num}."</td>
								<td >".number_format($Visit_Num)."</td>
								<td style='text-align:left; padding-left:5px;'>".$keyword."</td>
								<td style='text-align:left; padding-left:5px;'>{$Connect_RouteD}<br>{$Time}</td>
								<td >".($Percent)."%</td>
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