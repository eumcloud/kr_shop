<?PHP
	include_once("inc.header.php");

	$ToDay_Year = date("Y");
	$ToDay_Month = date("m");

	if(!$Select_Year) $Select_Year = $ToDay_Year;
	if(!$Select_Month) $Select_Month = $ToDay_Month;


	## 전체방문자수
	$result = _MQ("SELECT sum(Visit_Num) as CDSV FROM odtCounterData WHERE Year='$Select_Year' AND month = '$Select_Month'");
	$Total = $result[CDSV];

	if(!$Total) $Total = 0;

	## 시작년도 구함
	$result_start_year = _MQ("SELECT MIN(Year) as CDMY FROM odtCounterData");
	$Start_Year = $result_start_year[CDMY];

	if(!$Start_Year) $Start_Year = $ToDay_Year;


	## 시작월 구함
	$result_start_month = _MQ("SELECT MIN(Month) as CDMM FROM odtCounterData WHERE Year='$Start_Year'");
	$Start_Month = $result_start_month[CDMM];

	if(!$Start_Month) $Start_Month = $ToDay_Month;


	$arr_year = array();	$arr_month = array();
	for($i = ($ToDay_Year-2) ; $i <= $ToDay_Year ; $i++) {$arr_year[$i]++;}
	for($i = 1 ; $i <= 12 ; $i++) {$arr_month[$i]++;}

?>



				<!-- 검색영역 -->
				<div class="form_box_area">
					<form method=get name=frm action="<?=$_SERVER["PHP_SELF"]?>">
					<input type=hidden name=mode value=search>
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody> 
							<tr>
								<td class="article">총방문자 수</td>
								<td class="conts"><font face="tahoma" style="font-size:21;" color="#FF6600"><b><?=$Total?></b></font>명</td>
								<td class="article">월 선택</td>
								<td class="conts">
									<?=_InputSelect( "Select_Year" , array_keys($arr_year) , $Select_Year , " " , "" , "-선택-")?>년&nbsp;&nbsp;
									<?=_InputSelect( "Select_Month" , array_keys($arr_month) , $Select_Month , "  " , "" , "-선택-")?>월</td>
							</tr>

						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == search) {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER[SCRIPT_NAME]?>" class="medium gray" title="목록" >전체목록</a></span>
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
							<col width="80px"/><col width="100px"/><col width="*"/><col width="100px"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" class="colorset">구분</th>
								<th scope="col" class="colorset">접속자수</th>
								<th scope="col" class="colorset">그래프</th>
								<th scope="col" class="colorset">비율</th>
							</tr>
						</thead> 
						<tbody> 


<?PHP

	## 최대방문자수 구함
	$max = _MQ(" SELECT ifnull(MAX(Visit_Num),0) as MV FROM odtCounterData WHERE Year='$Select_Year' AND month='$Select_Month' ");
	$max = $max[MV];
	if(!$max) $max = 0;

	for($i=1 ; $i<=31 ; $i++){
		$result = _MQ("SELECT ifnull(SUM(Visit_Num),0) as SV FROM odtCounterData WHERE Year='$Select_Year' AND month='$Select_Month' AND Day='$i'");
		$Month_Num = $result[SV];
		if(!$Month_Num) $Month_Num = 0;

		if($Total) {
			$Percent = round(100 * $Month_Num / $Total,2);
			$Percent1 = round(100 * $Month_Num / $max,2);
			$Percent_Width = 100 * $Percent1 / 100;
		}
		else {
			$Percent = 0;
		}

		if(!$Percent_Width){
			$Percent_Width = "1";
		}

		if($max == $Month_Num && $max > 0) {
			$Back_Color = " bgcolor='#FF6600' ";
		}
		else {
			$Back_Color = " bgcolor='#CCCCCC' ";
		}

		$Percent = number_format($Percent,2);

		echo "
							<tr>
								<td><b>{$i}</b>일</td>
								<td><b>{$Month_Num}</b>명</td>
								<td style='text-align:left;'>
									<table width='{$Percent_Width}%' border='0' cellspacing='0' cellpadding='0' height='8'>
										<tr><td {$Back_Color}>&nbsp;</td></tr>
									</table>
								</td>
								<td><b>{$Percent}</b>%</td>
							</tr>	
		";

	}
?>

						</tbody> 
					</table>

			</div>

<?PHP
	include_once("inc.footer.php");
?>
