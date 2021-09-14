<?PHP
#LDD018
include_once("inc.header.php");
$delivstatus = 'no';
$ordertype = 'product';



// - 엑셀업로드상태(업로드상태일경우엔 쿠폰번호/택배회사데이터를 사용하게됨) ---
if($isExcel=="Y") {
    // -- 운송정보가 들어있는 테이블을 조회 ---
	$arrExcel = array();
    $que = "select * from odtExpressTmpTable where partnerCode = '". $com[id] ."' ";
    $res = _MQ_assoc($que);
	foreach( $res as $sk=>$sv ){
        if(!$sv[opuid]) continue;
        $arrExcel[$sv[opuid]][expressname][] = $sv[express];
        $arrExcel[$sv[opuid]][expressnum][]  = $sv[expressNum];
    }
}


## 금액산출
$resSaleDate;           //판매일
$resMainName;           //상품명
$resSaleCnt;            //판매수량
$rescommission;         //수수료
$resComPrice;           //입점업체결제
$resPrice;              //판매가
$resMajin;              //마진


// 검색 체크
$s_query = " where op.op_settlementstatus='none' and o.paystatus='Y' and o.paystatus2='Y' and o.canceled='N' AND o.orderstatus='Y' AND op.op_partnerCode = '" . $com[id] . "' ";
$s_query .= ($delivstatus == "yes" ? " and op.op_delivstatus='Y' " : " and op.op_delivstatus='N' " );
$s_query .= ($ordertype == "coupon" ? " and op.op_orderproduct_type='coupon' " : " and op.op_orderproduct_type='product' " );
$s_query .= " and o.delivery_date!='' ";

if( $pass_sdate && $pass_edate ) { $s_query .= " AND o.delivery_date between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 검색기간
else if( $pass_sdate ) { $s_query .= " AND o.delivery_date >= '". $pass_sdate ."' "; }
else if( $pass_edate ) { $s_query .= " AND o.delivery_date <= '". $pass_edate ."' "; }

if( $pass_ordernum ) { $s_query .= " AND op.op_oordernum like '%". $pass_ordernum ."%' "; }//주문번호
if( $pass_orderid ) { $s_query .= " AND o.orderid like '%". $pass_orderid ."%' "; }//주문자ID
if( $pass_ordername ) { $s_query .= " AND o.ordername like '%". $pass_ordername ."%' "; }//주문자이름
if( $pass_orderhtel ) { $s_query .= " AND (concat(ordertel1 ,'-',ordertel2 ,'-',ordertel3) like '%". $pass_orderhtel ."%' or concat(orderhtel1 ,'-',orderhtel2 ,'-',orderhtel3) like '%". $pass_orderhtel ."%') "; }//주문자연락처
if($delivstatus == "no" && $ordertype == 'product') $s_query .= " and o.delivery_date != '0000-00-00' "; // LDD018
$s_query .= " and op.op_cancel = 'N' "; //LMH001



// 판매가 / 총 주문수 추출 - 검색조건내
$resPrice = 0;
$total = 0;
$que = " 
	select 
		sum((op_pprice + op_poptionprice) * op_cnt ) as tPrice , 
		 IF( 
			op.op_comSaleType='공급가' ,  
			sum((op.op_supply_price+op.op_poptionpurprice) * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price) , 
			sum((op.op_pprice+op.op_poptionprice) * op.op_cnt - (op.op_pprice+op.op_poptionprice) * op.op_cnt * op.op_commission/ 100 + op.op_delivery_price + op.op_add_delivery_price)
		 ) as comPrice,
		 sum( op_cnt ) as tCnt , 
		 count(*) as cnt 
	from odtOrderProduct as op 
	left join odtOrder as o on (o.ordernum = op.op_oordernum)
	$s_query 
	order by delivery_date asc
";
$res = _MQ($que);
if( sizeof($res) > 0 ) {
    $app_tPrice = $res[tPrice]; // 상품구매가
    $app_comPrice = $res[comPrice]; // 결제금
    $app_tCnt = $res[tCnt]; // 구매수량
}


$listmaxcount = 50 ;
if( !$listpg ) {$listpg = 1 ;}
$count = $listpg * $listmaxcount - $listmaxcount;

$TotalCount = $res[cnt];
$Page = ceil($TotalCount / $listmaxcount);


##제목설정
unset($title);
if($ordertype=="coupon") $title="쿠폰";
if($ordertype=="product") $title="배송상품";
if($delivstatus=="yes") $title .=" 발급목록";
else $title .=" 대기목록";
?>
<!-- 검색영역 -->
<form name=searchfrm method=post action='<?=$PHP_SELF?>'>
<input type=hidden name=mode value=search>
<input type=hidden name=delivstatus value=<?=$delivstatus?>>
<input type=hidden name=ordertype value=<?=$ordertype?>>

				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="100px"/><col width="230px"/><col width="100px"/><col width="200px"/><col width="100px"/><col width="*"/>
						</colgroup>
						<tbody>
							<tr>
								<td class="article">검색기간</td>
								<td class="conts">
									<input type=text name="pass_sdate" ID="pass_sdate" class=input_text value="<?=$pass_sdate?>" readonly style="width:80px;">
									~ 
									<input type=text name="pass_edate" ID="pass_edate" class=input_text value="<?=$pass_edate?>" readonly style="width:80px;">
								</td>
								<td class="article">주문번호</td>
								<td class="conts" colspan=3><input type=text name="pass_ordernum" class=input_text value="<?=$pass_ordernum?>"></td>
							</tr>
							<tr>
								<td class="article">주문자ID</td>
								<td class="conts"><input type=text name="pass_orderid" class=input_text value="<?=$pass_orderid?>"></td>
								<td class="article">주문자이름</td>
								<td class="conts"><input type=text name="pass_ordername" class=input_text value="<?=$pass_ordername?>"></td>
								<td class="article">주문자연락처</td>
								<td class="conts"><input type=text name="pass_orderhtel" class=input_text value="<?=$pass_orderhtel?>"></td>
							</tr>
						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == search) {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>?delivstatus=<?=$delivstatus?>&ordertype=<?=$ordertype?>" class="medium gray" title="전체목록" >전체목록</a></span>
							<?}?>
						</div>
					</div>
					<?=_DescStr("쿠폰(상품) 일괄발송 및 재발송을 할 수 있습니다.")?>
				</div>
</form>
				<!-- // 검색영역 -->


				<div class="form_box_area">
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="100px"/><col width="400px"/><col width="100px"/><col width="*"/>
						</colgroup>
						<tbody>
							<tr>
								<td class="article">상품판매가</td>
								<td class="conts"><B><?=number_format($app_tPrice);?></B>원
									<?=_DescStr(" 상품판매가 = (판매가 + 옵션가) x 판매수량 ")?>
								</td>
								<td class="article">판매수량</td>
								<td class="conts"><B><?=number_format($app_tCnt);?></B>개</td>
							</tr>
							<tr>
								<td class="article">결제가</td>
								<td class="conts" colspan=3><B><?=number_format($app_comPrice);?></B>원
									<?=_DescStr(" 1.공급가방식 : 결제가 = ( 공급가 + 옵션공급가 ) x 판매수량 + 배송비 ")?>
									<?=_DescStr(" 2.수수료방식 : 결제가 = (1 - 수수료) x 상품판매가 x 판매수량 + 배송비 ")?>
								</td>
							</tr>
							<tr>
								<td class="article">예약배송현황</td>
								<td class="conts" colspan=3>
									<table class="list_TB">
										<thead>
											<tr>
												<th>내일<br><strong style="color:#ff0000">(~ <?php echo date('Y-m-d', strtotime('+1day', time())); ?>)</strong></th>
												<th>3일간<br>(~ <?php echo date('Y-m-d', strtotime('+3day', time())); ?>)</th>
												<th>7일간<br>(~ <?php echo date('Y-m-d', strtotime('+3day', time())); ?>)</th>
												<th>15일간<br>(~ <?php echo date('Y-m-d', strtotime('+15day', time())); ?>)</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td style="color:#ff0000; font-weight:bold;"><?php echo number_format(reserve_order(1, $com[id])); ?>건</td>
												<td><?php echo number_format(reserve_order(3, $com[id])); ?>건</td>
												<td><?php echo number_format(reserve_order(7, $com[id])); ?>건</td>
												<td><?php echo number_format(reserve_order(15, $com[id])); ?>건</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody> 
					</table>

				</div>




<form name=OderAllDelete method=post action="_order2.pro.php" target="common_frame" >
<input type="hidden" name="PageL" value="All">
<input type=hidden name=_mode value=''>
<input type=hidden name=_seachcnt value='<?=$TotalCount?>'>
<input type=hidden name=_PVSC value="<?=$_PVSC?>">
<input type=hidden name=_search_que value="<?=enc('e',$s_query)?>">
<input type=hidden name=delivstatus value=<?=$delivstatus?>>
<input type=hidden name=ordertype value=<?=$ordertype?>>
	<input type="hidden" name="reserve_del" value="Y">

				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<!-- 리스트 제어버튼영역 //-->
					<div class="top_btn_area">


<?if( $delivstatus == "no" && $ordertype == "coupon" ) : // (쿠폰)발급대기관리 ?>
						<span class="shop_btn_pack"><a href="#none" onclick="createCpNum('<?=$onedaynet_id?>');" id="createnum" class="small white" title="쿠폰번호발급" >쿠폰번호발급</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="#none" onclick="express()" id="batchexpress" class="small white" title="일괄발급처리" >일괄발급처리</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
<?endif;?>

<?if( $delivstatus == "yes" && $ordertype == "coupon" ) : // (쿠폰)발급완료관리 ?>
						<span class="shop_btn_pack"><a href="#none" onclick="express('Y');" id="rebatchexpress" class="small white" title="쿠폰번호재발급" >쿠폰번호재발급</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
<?endif;?>

<?if( $delivstatus == "no" && $ordertype == "product" ) : // (배송)발송대기관리 ?>
						<span class="shop_btn_pack"><a href="#none" onclick="excel_insert();" class="small white" title="배송정보엑셀적용" >배송정보엑셀적용</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="#none" onclick="express()" id="batchexpress" class="small white" title="일괄발송처리" >일괄발송처리</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
<?endif;?>

<?if( $delivstatus == "yes" && $ordertype == "product" ) : // (배송)발송완료관리 ?>
						<span class="shop_btn_pack"><a href="#none" onclick="express('Y')" id="batchexpress" class="small white" title="배송정보재발송" >배송정보재발송</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
<?endif;?>

						<span class="shop_btn_pack"><a href="#none" onclick="saveExcel('_order2.excel.php');" id="saveexcel" class="small white" title="선택엑셀다운로드" >선택엑셀다운로드</a></span>
						<span class="shop_btn_pack"><span class="blank_3"></span></span>
						<span class="shop_btn_pack"><a href="#none" onclick="search_excel_send('_order2.excel.php');" class="small white" title="검색엑셀다운로드" >검색엑셀다운로드(<?=number_format($TotalCount)?>)</a></span>

					</div>
					<!-- // 리스트 제어버튼영역 -->

					<table class="list_TB" summary="리스트기본">
						<thead>
							<tr>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset"><input type="checkbox" name="allchk" onclick="selectAll();" value="Y"></th>
								<th scope="col" class="colorset">주문자<br>수령인</th>
								<th scope="col" class="colorset">발송요청일</th>
								<th scope="col" class="colorset">상품정보</th>
								<th scope="col" class="colorset">연락처</th>
								<th scope="col" class="colorset">주문일<br>결제일</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody>
<?PHP

    // 현 페이지 주문번호 추출
    $que = "
        SELECT 
			op.*, o.* , ttt.ttt_value as comment3, p.prolist_img, p.prolist_img2,
			concat(ordertel1 ,'-',ordertel2 ,'-',ordertel3) as ordertel ,
			concat(orderhtel1 ,'-',orderhtel2 ,'-',orderhtel3) as orderhtel
		FROM odtOrderProduct as op 
        left join odtOrder as o on ( o.ordernum=op.op_oordernum )
        left join odtProduct as p on ( p.code=op.op_pcode )
		left join odtTableText as ttt on ( p.serialnum = ttt.ttt_datauid and ttt.ttt_tablename = 'odtProduct' and ttt.ttt_keyword = 'comment3')
		left join odtOrderCouponLog as cl on (cl.cl_pcode = op.op_pcode and cl.cl_oordernum = op.op_oordernum)
        $s_query 
        ORDER BY delivery_date asc
		limit $count , $listmaxcount
	";
    $res = _MQ_assoc($que);
	if(sizeof($res)==0){
		echo "<tr><td colspan='9' height='200' style='text-align:center;'><font color='darkorange'>주문 내역이 없습니다.</font></td></tr>";
	}

	foreach($res as $sk=>$sv){

		// -- 상세보기 버튼 ---
		$ex_modify_text = ($delivstatus=='yes')?'수정':(($ordertype=='product')?'발송':'발급');
		$_mod = "
			<span class='shop_btn_pack'><input type=button value='상세보기' class='input_small blue' onclick='window.open(\"_order.view.php?ordernum=" . $sv[ordernum] . "&_PVSC=" . $_PVSC . "\");'></span>
			<span class='shop_btn_pack'>&nbsp;<input type=button class='input_small gray ex_modify' data-uid='".$sv[op_uid]."' value='".$ex_modify_text."'/></span>
		";

		// -- 순번 ---
		$_num = $TotalCount - $count - $sk ;

		// --- 옵션값 추출  ---
		$itemName = $sv[op_pname];
		$itemName .= ($sv[op_option1] ? " (".($sv[op_is_addoption]=="Y" ? "추가" : "선택").":".$sv[op_option1]." ".$sv[op_option2]." ".$sv[op_option3].")" : "");// 해당상품에 대한 옵션내역이 있으면
		##쿠폰이미지/주의사항이 등록되어있지않으면 취소선으로 표시한다.

		// --- *** 배송일 경우 *** ---
		if($sv[op_orderproduct_type]=="product") {

			// --- 송장번호 / 택배사정보 ---
			$_expressnum = $sv[op_expressnum];
			$_expressname = $sv[op_expressname];

			// --- 엑셀로 가져온 정보가있다면 불러온다. ---
			if($isExcel=="Y") {
				$_expressname = $_expressname ? $_expressname : $arrExcel[$sv[op_uid]][expressname][0];
				$_expressnum = $_expressnum ? $_expressnum : $arrExcel[$sv[op_uid]][expressnum][0];
			}

			// --- 택배사 미지정시 기본택배사 지정 ---
			$_expressname = $_expressname ? $_expressname : $row_setup[s_del_company];

			// -- 배송조회 ---
			$app_delivery_link = $arr_delivery_company[$_expressname] . rm_str($_expressnum);// 변수위치 : include/var.php 

			// -- 완료된택배회사라면 SELECT 박스가아닌 단순보여주기만 한다.
			unset($ExpressList);
			if($sv[op_orderproduct_type]=="product" && $sv[op_delivstatus]=="Y") {
				$ExpressList = "<input type='hidden' name ='expressname[]' value='$_expressname'><B>".$_expressname."</B>";
			} 
			else {
				$ExpressList  = _InputSelect( "expressname[]" , array_keys($arr_delivery_company) , $_expressname , "" , "" , "-택배사 선택-")."<br>";						
			}

			// 쿠폰번호입력창의 활성화여부결정
			$readonly = ( $sv[op_orderproduct_type]=="product" && $sv[op_delivstatus]!="Y" ? "" : $readonly);


			// 모바일 아이콘 LDD002
			$device_icon = '<span class="shop_state_pack" style="display:block"><span class="blue">PC주문</span></span>';
			if($sv['mobile'] == 'Y') $device_icon = '<span class="shop_state_pack" style="display:block"><span class="orange">MOBILE주문</span></span>';


			$app_content = "
				<table border='0' width='100%' cellspacing='0' cellpadding='0' align='center' class='none'>
					<tr id='$sv[op_uid]'>
						<td >
							<table border='0' width='100%' cellspacing='0' cellpadding='0' align='center' >
								<tr>
									<td style='text-align:left;'>".$device_icon.$itemName."</td>
									<td  width='30'>".$sv[op_cnt]."개</td>
									<td width='150' style='text-align:left;'>
<input type='hidden' name='OrderNumValue[]' value='".$sv[ordernum]."'>
<input type='hidden' name='op_uid[]' value='". $sv[op_uid] ."'>
<input type='hidden' name='offset[]' value='0'>
<input type='hidden' name='productcode[]' value='".$sv[op_pcode]."'>
<input type='hidden' name='setTmp[]' value='".trim($_expressnum)."'>
										".$ExpressList."<br>
										<input type=text warn1='" . $warning1 . "' warn2='" . $warning2 . "' qty='" . $sv[op_cnt] . "' ordernum='" . $sv[op_oordernum] . "' op_uid='". $sv[op_uid] ."'  name='expressnum[]' ordertype='".$sv[op_orderproduct_type]."' class=input_text style='width:115px' value='".$_expressnum."' ".$readonly.">
									</td>
									<td width='80'>
										<li style='clear:both;display:inline; float:left; padding-top:3px;'><span class='shop_state_pack'>".($sv[op_delivstatus] == "Y" ? "<span class='orange'>발송완료</span>" : "<span class='lightgray'>발송대기</span>")."</span></li>
										<li style='clear:both;display:inline; float:left; padding-top:3px;display:".($sv[op_delivstatus] == "Y" ? "" : "none").";'><span class='shop_btn_pack'><input type=button value='배송조회' class='input_small blue' onclick='window.open(\"" . $app_delivery_link . "\");'></span></li>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			";
		}
		// --- *** 배송일 경우 *** ---


		// --- *** 쿠폰일 경우 *** ---
		else {

			// --- 쿠폰일경우 이미지/주의사항확인 ---
			$warning1 = $sv[prolist_img2] ? "N" : "Y";  //쿠폰이미지 체크 - 없으면 Y
			$warning2 = $sv[comment3] ? "N" : "Y";  //쿠폰주의사항 체크 - 없으면 Y
			$warning = ($warning1 == "Y" || $warning2 == "Y") ? "Y" : "N" ; // 하나라도 체크되면 Y
			$itemName = ($warning1 == "Y" || $warning2 == "Y") ? "<strike>".$itemName."</strike>" : $itemName;

			$tmp_content = "";
			$_row = 0;
			$coupon_assoc = _MQ_assoc("select * from odtOrderProductCoupon where opc_opuid = '".$sv[op_uid]."'");
			if(sizeof($coupon_assoc) > 0 ) {
				foreach($coupon_assoc as $coupon_key => $coupon_row) {

					// ---- 쿠폰발급완료 일때만 버튼이 출력 ---
					$usestring = "";
					if ($sv[op_delivstatus]=="Y") {
						// 미사용, 사용
						if($coupon_row[opc_status] == "대기") {
							$usestring ="<span class='orange' onClick=\"f_check2('" . $coupon_row[opc_uid] . "','use');\" style='cursor:pointer;' alt='클릭시 해당쿠폰이 사용으로 변경됩니다.'>미사용</span>";
						} 
						else if($coupon_row[opc_status] == "사용") {
							$usestring ="<span class='light' onClick=\"f_check2('" . $coupon_row[opc_uid] . "','unuse');\" style='cursor:pointer;' alt='클릭시 해당쿠폰이 미사용으로 변경됩니다.'>사 &nbsp;&nbsp;용</span>";
						} 
					}

					$tmp_content .= "
<input type='hidden' name='OrderNumValue[]' value='".$sv[ordernum]."'>
<input type='hidden' name='op_uid[]' value='". $sv[op_uid] ."'>
<input type='hidden' name='opc_uid[]' value='".$coupon_row[opc_uid]."'>
						<li style='clear:both;display:inline; float:left; padding-top:3px;'><input type=text warn1='" . $warning1 . "' warn2='" . $warning2 . "' qty='" . $sv[op_cnt] . "' ordernum='" . $sv[ordernum] . "' op_uid='". $sv[op_uid] ."'  name='expressnum[]' ordertype='".$sv[op_orderproduct_type]."' class=input_textSmall style='width:125px' value='" . $coupon_row[opc_expressnum] . "' readonly></li>
						<li style='display:inline; float:right; padding-top:3px;'><span class='shop_state_pack'>" . $usestring . "</span></li>
					";
				}
			}

			// 발급된 쿠폰이 없을 경우 - 개수만큼 만들어 줌
			else {
				for($i=0;$i<$sv[op_cnt];$i++){
					##쿠폰발급완료 일때만 버튼이 출력
					$usestring= "<span class='gray' >미발급</span>";
					$tmp_content .= "
<input type='hidden' name='OrderNumValue[]' value='".$sv[ordernum]."'>
<input type='hidden' name='op_uid[]' value='". $sv[op_uid] ."'>
<input type='hidden' name='opc_uid[]' value='". $i ."'>
						<li style='clear:both;display:inline; float:left; padding-top:3px;'><input type=text warn1='" . $warning1 . "' warn2='" . $warning2 . "' qty='" . $sv[op_cnt] . "' ordernum='" . $sv[ordernum] . "' op_uid='". $sv[op_uid] ."' name='expressnum[]' ordertype='".$sv[op_orderproduct_type]."' class=input_textSmall style='width:125px' value='' readonly></li>
						<li style='display:inline; float:right; padding-top:3px;'><span class='shop_state_pack'>" . $usestring . "</span></li>
					";
				}
			}

			// 모바일 아이콘 LDD002
			$device_icon = '<span class="shop_state_pack" style="display:block"><span class="blue">PC주문</span></span>';
			if($sv['mobile'] == 'Y') $device_icon = '<span class="shop_state_pack" style="display:block"><span class="orange">MOBILE주문</span></span>';

			$app_content = "
				<table border='0' width='100%' cellspacing='0' cellpadding='0' align='center' class='none'>
					<tr>
						<td >
							<TABLE width=100%>
								<TR>
									<TD style='text-align:left;'>".$device_icon.$itemName."</TD>
									<td  width='30'>".$sv[op_cnt]."개</td>
									<td width='100'><span class='shop_state_pack'>".($sv[op_delivstatus] == "Y" ? "<span class='orange'>발급완료</span>" : "<span class='lightgray'>발급대기</span>")."</span></td>
								</TR>
							</TABLE>									
						</td>								
						<td width='185' align='center'>".$tmp_content."</td>
					</tr>
				</table>
			";
		}
		// --- *** 쿠폰일 경우 *** ---



		echo "
			<tr>
				<td >" . $_num . "</td>
				<td ><input type='checkbox' name='OpUid[]' warning='" . $warning . "' value='" . $sv[op_uid] . "' class='class_uid'></td>
				<td width='80'>
					" . $sv[ordername] . "
					" . ( $sv[recname] ? ( $sv[recname] == $sv[ordername] ? "" : "<br>" . $sv[recname] ) : "" ) . "
				</td>
				<td><strong style='color:#ff0000'>".$sv['delivery_date']."</strong></td>
				<td >" . $app_content . "</td>
				<td>
					" . ($sv[ordertel] ? $sv[ordertel] ."<br>" : "") . "
					" . ($sv[orderhtel] ? $sv[orderhtel] : "") . "
				</td>
				<td >
					".date('y.m.d H:i',strtotime($sv[orderdate]))."<!-- 주문일 --><br>
					". (substr($sv[paydate],0,10) == "0000-00-00" ? "-" : date('y.m.d H:i',strtotime($sv[paydate]))) ."<!-- 결제일 -->
				</td>
				<td><div class='btn_line_up_center'>". $_mod."</div></td>
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
</form>





<?PHP
	include_once("inc.footer.php");
?>






<script type="text/javascript" src="_order2.list.js" ></script>

<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
    $(function() {
        $("#pass_sdate").datepicker({changeMonth: true, changeYear: true });
        $("#pass_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#pass_sdate").datepicker( "option",$.datepicker.regional["ko"] );

        $("#pass_edate").datepicker({changeMonth: true, changeYear: true });
        $("#pass_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#pass_edate").datepicker( "option",$.datepicker.regional["ko"] );
    });

    $('.ex_modify').on('click',function(){
    	var op_uid = $(this).data('uid'),
		op_value = $(this).val(),
		ordernum = $('#'+op_uid).find('input[name^=OrderNumValue]').val(),
    	expressnum = $('#'+op_uid).find('input[name^=expressnum]').val(),
    	expressname = $('#'+op_uid).find('input[name^=expressname]').val();
		if(!expressname) { expressname = $('#'+op_uid).find('select[name^=expressname] option:selected').val(); }

		if(confirm("정말 " + op_value + "하시겠습니까?")) {
			$.ajax({
				data: {mode:'<?=($delivstatus=='yes')?'modify':''?>',ordertype:'<?=$ordertype?>',ordernum:ordernum ,op_uid:op_uid, expressnum:expressnum, expressname:expressname},
				type: 'POST',
				cache: false,
				url: '_order2.express.php',
				success: function(data) {
					window.location.reload();
				},
				error:function(request,status,error){
					alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}
    });
</script>