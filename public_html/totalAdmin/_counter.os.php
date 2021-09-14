<?PHP
	include_once("inc.header.php");

	$ToDay_Time = time();
	$ToDay_Year = date("Y");
	$ToDay_Month = date("m");
	$ToDay_Day = date("d");
	$ToDay_Hour = date("H");
	
	$result = _MQ( "SELECT SUM(Visit_Num) as COBSV FROM odtCounterOSBrowser WHERE Kinds='O'" ); 
	$Total = $result[COBSV];
	if(!$Total) $Total = 0;

?>

				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<table class="list_TB" summary="리스트기본">
						<colgroup>
							<col width="80px"/><col width="100px"/><col width="*"/><col width="100px"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" class="colorset">운영체제</th>
								<th scope="col" class="colorset">접속자수</th>
								<th scope="col" class="colorset">그래프</th>
								<th scope="col" class="colorset">비율</th>
							</tr>
						</thead> 
						<tbody> 


<?PHP

	## 최대방문자수 구함
	$max = _MQ(" SELECT ifnull(MAX(Visit_Num),0) as MV FROM odtCounterOSBrowser WHERE Kinds='O' ");
	$max = $max[MV];
	if(!$max) $max = 0;

	$query = "SELECT Name, Visit_Num FROM odtCounterOSBrowser WHERE Kinds='O' ORDER BY Visit_Num DESC";
	$result = _MQ_assoc($query);
	foreach( $result as $k=>$row ){
		$Name = $row[Name];
		$Visit_Num = $row[Visit_Num];

		if($Total) {
			$Percent = round(100 * $Visit_Num / $Total,2);
			$Percent1 = round(100 * $Visit_Num / $max,2);
			$Percent_Width = 100 * $Percent1 / 100;
		}
		else {
			$Percent = 0;
		}

		if(!$Percent_Width){
			$Percent_Width = "1";
		}

		if($max == $Visit_Num && $max > 0) {
			$Back_Color = " bgcolor='#FF6600' ";
		}
		else {
			$Back_Color = " bgcolor='#CCCCCC' ";
		}

		$Percent = number_format($Percent,2);


		echo "
							<tr>
								<td><b>". $Name ."</b></td>
								<td><b>" . $Visit_Num  . "</b>명</td>
								<td style='text-align:left;'><table width='" . $Percent_Width . "%' border='0' cellspacing='0' cellpadding='0' height='8'><tr><td " . $Back_Color . ">&nbsp;</td></tr></table></td>
								<td><b>" . $Percent . "</b>%</td>
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
