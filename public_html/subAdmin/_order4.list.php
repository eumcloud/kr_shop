<?PHP
# LDD007
include_once("inc.header.php");

$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_GET , $_POST)) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);

$arr_customer = arr_company();
$arr_customer_name = arr_company2();

// 최초 기간 설정 오늘-7
if(!$pass_sdate) {$pass_sdate = date("Y-m-d" , strtotime("-7 day"));}
$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');


// 기본 쿼리 + 검색 조건
$s_query = " where (1) and s_partnerCode='".$com[id]."' ";
if( $pass_sdate && $pass_edate ) { $s_query .= " and left(s_date,10) between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 검색기간
else if( $pass_sdate ) { $s_query .= " and left(s_date,10) >= '". $pass_sdate ."' "; }
else if( $pass_edate ) { $s_query .= " and left(s_date,10) <= '". $pass_edate ."' "; }


// 페이징 사전 준비
$listmaxcount = 10;
if( !$listpg ) {$listpg = 1;}
$count = $listpg * $listmaxcount - $listmaxcount;

$res = _MQ(" select count(*) as cnt from odtOrderSettleComplete $s_query ");
$TotalCount = $res[cnt];
$Page = ceil($TotalCount / $listmaxcount);

$res = _MQ_assoc(" select * from odtOrderSettleComplete {$s_query} ORDER BY s_uid desc limit $count , $listmaxcount ");
?>
<form name="searchfrm" method="post" action="<?=$PHP_SELF?>">
	<input type="hidden" name="mode" value="search">
	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="120px"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">검색기간</td>
					<td class="conts" >
						<input type=text name="pass_sdate" ID="pass_sdate" class=input_text value="<?=$pass_sdate?>" readonly style="width:100px;">
						~ 
						<input type=text name="pass_edate" ID="pass_edate" class=input_text value="<?=$pass_edate?>" readonly style="width:100px;">
						(정산일 기준)
					</td>
				</tr>
			</tbody> 
		</table>
		
		<!-- 버튼영역 -->
		<div class="top_btn_area">
			<div class="btn_line_up_center">
				<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
				<?if ($mode == search) {?>
				<span class="shop_btn_pack"><span class="blank_3"></span></span>
				<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="전체목록" >전체목록</a></span>
				<?}?>
			</div>
		</div>
	</div>
</form>


<div class="content_section_inner">
	<form name="OderAllDelete" method="post" target="common_frame">
		<input type="hidden" name="PageL" value="All">
		<input type="hidden" name="_mode" value=''>
		<input type="hidden" name="_seachcnt" value='<?=$TotalCount?>'>
		<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">
		<input type="hidden" name="_search_que" value="<?=enc('e',$s_query)?>">

		<!-- 엑셀다운 {-->
		<div class="top_btn_area">
			<span class="shop_btn_pack"><a href="#none" onclick="saveExcel('_order4.excel.php');" id="saveexcel" class="small white" title="엑셀저장" >엑셀저장</a></span>
			<span class="shop_btn_pack"><span class="blank_3"></span></span>
			<span class="shop_btn_pack"><a href="#none" onclick="search_excel_send('_order4.excel.php');" id="saveexcel" class="small white" title="엑셀저장" >검색엑셀저장(<?=number_format($TotalCount)?>)</a></span>
		</div>
		<!--} 엑셀다운 -->

		<!-- 정보출력 {-->
		<table class="list_TB" summary="리스트기본">
			<thead>
				<tr>
					<th scope="col" class="colorset"><input type="checkbox" name="allchk" onclick="selectAll();" value="Y"></th>
					<th scope="col" class="colorset">정산일</th>
					<th scope="col" class="colorset">입점업체명<br>입점아이디</th>
					<th scope="col" class="colorset">총금액</th>
					<th scope="col" class="colorset">정산수량</th>
					<th scope="col" class="colorset">배송비</th>
					<th scope="col" class="colorset">입점업체<br>정산금액</th>
					<th scope="col" class="colorset">할인액</th>
					<th scope='col' class='colorset'>수수료</th>
					<?=($row_setup['TAX_CHK'] == 'Y' ? "<th scope='col' class='colorset'>세금계산서<br>발행상태</th>" : "")// JJC001?>
					<th scope='col' class='colorset'>상세보기</th>
				</tr>
			</thead> 
			<tbody>
				<?php
				if(sizeof($res) <= 0) echo "<tr><td colspan='".($row_setup['TAX_CHK'] == 'Y'?'13':'12')."' height='100' style='text-align:center;'><font color='darkorange'>정산완료내역이 없습니다.</font></td></tr>";
				foreach($res as $k=>$v) {
				?>
				<tr>
					<td>
						<input type="checkbox" name="OpUid[]" value="<?php echo $v['s_uid']; ?>" class="class_uid">
					</td>
					<td>
						<?php echo date('Y-m-d', strtotime($v['s_date'])); ?>
					</td>
					<td>
						<?php echo $arr_customer_name[$v['s_partnerCode']]; ?><br>
						<?php echo $v['s_partnerCode']; ?>
					</td>
					<td><?php echo number_format($v['s_price']); ?>원</td>
					<td><?php echo number_format($v['s_count']); ?>건</td>
					<td><?php echo number_format($v['s_delivery_price']); ?>원</td>
					<td><?php echo number_format($v['s_com_price']); ?>원</td>
					<td><?php echo number_format($v['s_usepoint']); ?>원</td>
					<td><?php echo number_format($v['s_discount']); ?>원</td>
					<?php
						// JJC001
						if($row_setup['TAX_CHK'] == 'Y' ) {
							// 세금계산서 상태에 따른 버튼 노출 변경
							echo "<td>";
							switch( $v['s_tax_status'] ){
								case 1000 :echo $arr_adm_button["임시저장"] ; break;
								case 2010 : case 2011 :echo $arr_adm_button["세금계산서발행중"] ; break;
								case 4012 :echo $arr_adm_button["발행거부"] ; break;
								case 3014 : case 3011 : echo $arr_adm_button["발행완료"] ; break;
								case 5013 : case 5031 : echo $arr_adm_button["발행취소"] ; break;
								default : echo $arr_adm_button["미발행"] ; break;
							}
							echo "</td>";
						}
					?>
					<td>
						<div class="btn_line_up_center">
							<span class="shop_btn_pack"><input type="button" onclick="location.href='./_order4.view.php?suid=<?php echo $v['s_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>';" value="상세보기" class="input_small blue"></span>
						</div>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<!--} 정보출력 -->
	</form>

	<!-- 페이지네이트 -->
	<div class="list_paginate">			
		<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=")?>
	</div>
	<!-- // 페이지네이트 -->
</div>



<script type="text/javascript" src="_order2.list.js" ></script>
<link rel='stylesheet' href='../include/js/jquery/jquery.ui.all.css' type=text/css>
<script src="../include/js/jquery/jquery.ui.core.js"></script>
<script src="../include/js/jquery/jquery.ui.widget.js"></script>
<script src="../include/js/jquery/jquery.ui.datepicker.js"></script>
<script src="../include/js/jquery/jquery.ui.datepicker-ko.js"></script>
<script>
    $(function() {
        $("#pass_sdate").datepicker({changeMonth: true, changeYear: true });
        $("#pass_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#pass_sdate").datepicker( "option",$.datepicker.regional["ko"] );

        $("#pass_edate").datepicker({changeMonth: true, changeYear: true });
        $("#pass_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#pass_edate").datepicker( "option",$.datepicker.regional["ko"] );
    });
     function search_excel_send(fileTemp) {
    	 if($('input[name=_seachcnt]').val()*1 > 0 ){
    		$("input[name=_mode]").val("search_excel");
    		frm = document.OderAllDelete;
    		orgAction = frm.action
    		frm.action = fileTemp;
    		frm.submit();
    		frm.action = orgAction;
    	 }
    	 else {
    		 alert('1건 이상 검색시 엑셀다운로드가 가능합니다.');
    	 }
     }
</script>

<?PHP include_once("inc.footer.php"); ?>