<?PHP

	include_once("inc.header.php");


	// 검색 체크
	$s_query = " where canceled='Y' AND orderstatus='Y' ";

	if( $pass_ordernum ) { $s_query .= " AND ordernum like '%". $pass_ordernum ."%' "; }//주문번호
	if( $pass_ordername ) { $s_query .= " AND ordername like '%". $pass_ordername ."%' "; }//주문자명
	if( $pass_recname ) { $s_query .= " AND recname like '%". $pass_recname ."%' "; }//수령인명

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$que = " select count(*) as cnt from odtOrder $s_query ";
	$res = _MQ($que);
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$que = " select * from odtOrder " . $s_query . " ORDER BY canceldate desc limit $count , $listmaxcount  ";
	$res = _MQ_assoc($que);

?>

				<!-- 검색영역 -->
<form name=searchfrm method=post action='<?=$PHP_SELF?>'>
<input type=hidden name=mode value=search>
				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="100px"/><col width="200px"/><col width="100px"/><col width="200px"/><col width="100px"/><col width="*"/>
						</colgroup>
						<tbody>
							<tr>
								<td class="article">주문번호</td>
								<td class="conts"><input type=text name="pass_ordernum" class=input_text value="<?=$pass_ordernum?>"></td>
								<td class="article">주문자명</td>
								<td class="conts"><input type=text name="pass_ordername" class=input_text value="<?=$pass_ordername?>"></td>
								<td class="article">수령인명</td>
								<td class="conts"><input type=text name="pass_recname" class=input_text value="<?=$pass_recname?>"></td>
								
							</tr>
							<tr>
								<td class="conts" colspan="6">
									<?=_DescStr("<b>회원주문</b>인 경우에는 <b>주문번호가 볼드체(굵은글씨)로 표시</b> 됩니다.")?>
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
				<!-- // 검색영역 -->


<form name=frm method=post action="_order.pro.php" target="common_frame">
<input type=hidden name=_mode value=''>
<input type=hidden name=_seachcnt value='<?=$TotalCount?>'>
<input type=hidden name=_PVSC value="<?=$_PVSC?>">
<input type=hidden name=_search_que value="<?=enc('e',$s_query)?>">

				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<!-- 리스트 제어버튼영역 //-->
					<div class="top_btn_area">
						<span class="shop_btn_pack"><a href="javascript:select_excel_send();" class="small white" title="선택엑셀다운로드" >선택엑셀다운로드</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="javascript:search_excel_send();" class="small white" title="검색엑셀다운로드" >검색엑셀다운로드(<?=number_format($TotalCount)?>)</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="javascript:search_delete_send();" class="small white" title="선택삭제" >선택삭제</a></span>
					</div>
					<!-- // 리스트 제어버튼영역 -->

					<table class="list_TB" summary="리스트기본">
						<thead>
							<tr>
								<th scope="col" class="colorset"><input type="checkbox" name="allchk"></th>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">주문번호</th>
								<th scope="col" class="colorset">주문자</th>
								<th scope="col" class="colorset">수령인</th>
								<th scope="col" class="colorset">결제방법</th>
								<th scope="col" class="colorset">결제금액</th>
								<th scope="col" class="colorset">주문취소일</th>
								<th scope="col" class="colorset">주문일</th>
								<th scope="col" class="colorset">기능</th>
							</tr>
						</thead> 
						<tbody>
<?PHP

	if(sizeof($res) == 0 ) echo "<tr><td colspan=10 height='40'>주문 내역이 없습니다.</td></tr>";
	foreach($res as $k=>$v) {

		$_mod = "<span class='shop_btn_pack'><input type=button value='상세보기' class='input_small blue' onclick='location.href=(\"_order.form.php?_mode=cancellist&ordernum=" . $v[ordernum] . "&_PVSC=" . $_PVSC . "\");'></span>";
		$_num = $TotalCount - $count - $k ;

		switch($v[paymethod]){
			case "B": $OrderPaymethodD = "은행"; break;
			case "E": $OrderPaymethodD = "에스크로"; break;
			case "L": $OrderPaymethodD = "실시간"; break;
			case "G": $OrderPaymethodD = "포인트"; break;
			case "V": $OrderPaymethodD = "가상계좌"; break;
			case "C": default : $OrderPaymethodD = "카드"; break;
		}

		if($v[tPrice] > 0) {
			$TotalOrderPriceD = number_format($v[tPrice])."원";
		}
		else {
			$TotalOrderPriceD = "전액적립금결제";
		}

		if(!$v[orderid] || $v[orderid] == "guest") {
			$orderNumber = $v[ordernum];
		}
		else {
			$orderNumber = "<b>".$v[ordernum]."</b>";
		}

		$cancelDate = date("Y-m-d H:i:s",$v[canceldate]);
		//$cancelDate = $v[canceldate];
		$orderdate = date("Ymd",strtotime($v[orderdate]));

		echo "
							<tr>
								<td><input type=checkbox name='OrderNum[]' value='".$v[ordernum]."' class=class_ordernum></td>
								<td>". $_num ."</td>
								<td>". $orderNumber ."</td>
								<td>" . $v[ordername] . "</td>
								<td>" . $v[recname] . "</td>
								<td>". $OrderPaymethodD ."</td>
								<td>". $TotalOrderPriceD ."</td>
								<td>". $cancelDate ."</td>
								<td>". $orderdate ."</td>
								<td>
									<div class='btn_line_up_center'>
										". $_mod."
									</div>
								</td>
							</tr>
		";
	}
?>
						</tbody> 
					</table>


					<!-- 리스트 제어버튼영역 //-->
					<div class="top_btn_area">
						<span class="shop_btn_pack"><a href="javascript:select_excel_send();" class="small white" title="선택엑셀다운로드" >선택엑셀다운로드</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="javascript:search_excel_send();" class="small white" title="검색엑셀다운로드" >검색엑셀다운로드(<?=number_format($TotalCount)?>)</a></span>
					</div>
					<!-- // 리스트 제어버튼영역 -->


					<!-- 페이지네이트 -->
					<div class="list_paginate">			
						<?=pagelisting($listpg, $Page, $listmaxcount," ?pass_menu={$pass_menu}&${_PVS}&listpg=" , "Y")?>
					</div>
					<!-- // 페이지네이트 -->

			</div>
</form>


<?PHP
	include_once("inc.footer.php");
?>

<SCRIPT>
	// - 선택삭제 ---
	 function search_delete_send() {
		 if($('.class_ordernum').is(":checked")){
			 if(confirm("선택하신 주문을 완전삭제합니다.\n\n정말 삭제하시겠습니까?")){
				$("input[name=_mode]").val("select_wiping");
				$("form[name=frm]")[0].submit();
			 }
		 }
		 else {
			 alert('1건 이상 선택 시 삭제가 가능합니다..');
		 }
	 }
	// - 선택삭제 ---
	// - 선택엑셀 ---
	 function select_excel_send() {
		 if($('.class_ordernum').is(":checked")){
			$("input[name=_mode]").val("select_excel");
			$("form[name=frm]")[0].submit();
		 }
		 else {
			 alert('1건 이상 선택 시 엑셀다운로드가 가능합니다..');
		 }
	 }
	// - 선택엑셀 ---
	// - 검색엑셀 ---
	 function search_excel_send() {
		 if($('input[name=_seachcnt]').val()*1 > 0 ){
			$("input[name=_mode]").val("search_excel");
			$("form[name=frm]")[0].submit();
		 }
		 else {
			 alert('1건 이상 검색시 엑셀다운로드가 가능합니다..');
		 }
	 }
	// - 검색엑셀 ---
	// - 전체선택해제 ---
	$(document).ready(function() {
		$("input[name=allchk]").click(function (){
			if($(this).is(':checked')){
				$('.class_ordernum').attr('checked',true);
			}
			else {
				$('.class_ordernum').attr('checked',false);
			}
		});
	});
	// - 전체선택해제 ---
</SCRIPT>