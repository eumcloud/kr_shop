<?PHP
	include_once("inc.header.php");

	if( !$Form ) {

		$row = _MQ("SELECT * FROM odtCounterConfig WHERE serialnum='1'");
		$Cookie_Use = $row[Cookie_Use];
		$Cookie_Term = $row[Cookie_Term];
		$Counter_Use = $row[Counter_Use];
		$Now_Connect_Use = $row[Now_Connect_Use];
		$Route_Use = $row[Route_Use];
		$Now_Connect_Term = $row[Now_Connect_Term];
		$Total_Num = $row[Total_Num];
		$Total_NumD = number_format($Total_Num);
		$Admin_Check_Use = $row[Admin_Check_Use];
		$Admin_IP = $row[Admin_IP];
?>


<form name="CountConfigForm" method="post" action="<?=$PHP_SELF?>">
<input type="hidden" name="Form" value="ModifyForm">
<input type="hidden" name="Now_Connect_Use" value="N">
<input type="hidden" name="Now_Connect_Term" value="30">
<input type="hidden" name="Total_Num" value="<?=$Total_Num?>">

					<!-- 검색영역 -->
					<div class="form_box_area">

						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
								
									<tr>
										<td class="article">카운터 사용여부<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<?=_InputRadio( "Counter_Use" , array('Y','N') , $Counter_Use , "" , array('사용함','사용하지않음') , "")?>
										</td>
									</tr>

									<tr>
										<td class="article">접속경로 사용여부<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<?=_InputRadio( "Route_Use" , array('Y','N') , $Route_Use , "" , array('사용함','사용하지않음') , "")?>					
										</td>
									</tr>

									<tr>
										<td class="article">중복 접속설정<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<?=_InputRadio( "Cookie_Use" , array('A','T','O') , $Cookie_Use , "" , array('접속하는대로 카운터 증가','지정된 시간대로 카운터 증가','하루에 한번만 카운터 증가') , "")?>
										</td>
									</tr>

									<tr>
										<td class="article">중복접속시간설정<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<input type="text" name="Cookie_Term" style="width:50px"  value="<?echo"$Cookie_Term";?>" class="input_text" style='text-align:right;'><b> 초
										</td>
									</tr>

									<tr>
										<td class="article">관리자 통계포함</td>
										<td class="conts">
											<?=_InputRadio( "Admin_Check_Use" , array('Y','N') , $Admin_Check_Use , "" , array('포함함','포함하지않음') , "")?>												
										</td>
									</tr>

									<tr>
										<td class="article">관리자접속 아이피(IP)</td>
										<td class="conts"><input type="text" name="Admin_IP" style="width:300px"  value="<?echo"$Admin_IP";?>" class="input_text"></td>
									</tr>

									<tr>
										<td class="article">접속경로별 접속자료 초기화</td>
										<td class="conts"><span class="shop_btn_pack btn_input_gray"><input type="button" class="medium" value="접속경로별 접속자료 초기화"  onClick="self.location.replace('_config.counter.pro.php?mode=ROUTE')"></span></td>
									</tr>

									<tr>
										<td class="article">전체접속자료</td>
										<td class="conts"><span class="shop_btn_pack btn_input_gray"><input type="button" class="medium" value="전체 접속자료 초기화"  onClick="self.location.replace('_config.counter.pro.php?mode=ALL')"></span></td>
									</tr>

								</tbody> 
							</table>
				
					</div>
					<!-- // 검색영역 -->
					<!-- 버튼영역 -->
					<div class="bottom_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_red">
								<input type="submit" name="" class="input_large" value="확인">
							</span>
						</div>
					</div>
					<!-- 버튼영역 -->
</form>

<?
	}
	else if( $Form == "ModifyForm" ) {

		_MQ_noreturn("UPDATE odtCounterConfig SET Cookie_Use='$Cookie_Use',Cookie_Term='$Cookie_Term',Counter_Use='$Counter_Use',Now_Connect_Use='$Now_Connect_Use',Route_Use='$Route_Use',Now_Connect_Term='$Now_Connect_Term',Total_Num='$Total_Num',Admin_Check_Use='$Admin_Check_Use',Admin_IP='$Admin_IP' WHERE serialnum='1'");
		
		error_loc_msg("_config.counter.form.php" , "환경설정 변경이 잘 되었습니다.");

	}
	else {
		exit;
	}
?>

<?PHP
	include_once("inc.footer.php");
?>