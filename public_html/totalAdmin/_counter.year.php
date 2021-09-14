<?PHP
	include_once("inc.header.php");

	$ToDay_Year = date("Y");

	$result = _MQ("SELECT ifnull(SUM(Visit_Num),0) as CDSV FROM odtCounterData");

	$Total = $result[CDSV];
	if(!$Total) $Total = 0;

?>



				<!-- 검색영역 -->
				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody> 
							<tr>
								<td class="article">총방문자 수</td>
								<td class="conts"><font face="tahoma" style="font-size:21;" color="#FF6600"><b><?=$Total?></b></font>명</td>
							</tr>
						</tbody> 
					</table>
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
		list($max) = _MQ("SELECT ifnull(SUM(Visit_Num),0) as `0` FROM odtCounterData group by Year ");

		$result_year = _MQ_assoc("SELECT Year FROM odtCounterData GROUP BY Year ORDER BY Year ASC");
		foreach($result_year as $k=>$row) {
			$Year_Select = $row[Year];
			$result = _MQ("SELECT ifnull(SUM(Visit_Num),0) as SV FROM odtCounterData WHERE Year='$Year_Select'");
			$Year_Num = $result[SV];

			$Percent = round(100 * $Year_Num / $Total, 2);
			$Percent_Width = 100 * $Percent / 100;

			if(!$Percent_Width) $Percent_Width = "1";


			if($max == $Year_Num && $max > 0) {
				$Back_Color = " bgcolor='#FF6600' ";
			}
			else {
				$Back_Color = " bgcolor='#CCCCCC' ";
			}

			$Percent = number_format($Percent,2);

			echo "
							<tr>
								<td><b>$row[Year]</b>년</td>
								<td><b>{$Year_Num}</b>명</td>
								<td style='text-align:left;'><table width='{$Percent_Width}%' border='0' cellspacing='0' cellpadding='0' height='8'>
									<tr><td {$Back_Color}>&nbsp;</td></tr>
								</table></td>
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