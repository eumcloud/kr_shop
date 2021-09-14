<?php

	// 페이지 표시 - 주문 취소목록 : 주문 전체목록 구분
	$app_current_link = ( $_GET[_mode] == "cancellist" ? "/totalAdmin/_ordercancel.list.php" : "/totalAdmin/_order.list.php" );

	include dirname(__FILE__)."/wrap.header.php";


    $r = _MQ("SELECT * FROM odtOrder WHERE ordernum='" . $ordernum . "'");

	$orderidTemp = ($r[member_type] == "member" ? $r[orderid] : "<span style='color:red;'>비회원</FONT>");

    $OrderSumpriceD = number_format($r[sumprice]);
    $OrderDeliveryD = number_format($r[dPrice]);
    $OrderTotalPriceD = number_format($r[tPrice]);
    $OrderUsedpointD = number_format($r[gPrice]);
    $OrderGetpointD = number_format($r[gGetPrice]);
    $OrderDate = date("Y년 m월 d일 H시 i분",strtotime($r[orderdate]));

	$r[expressdate] = $r[expressdate] ? $r[expressdate] : date("Y-m-d");


	// 포인트, 쿠폰에 대한 할인금액을 뽑도록 수정 2015-11-13 LCY[002]
	    $cque = "
	        select
	            cl_price
	        from odtOrderCouponLog
	        where cl_oordernum='".$ordernum."'
	    ";
	    $cres = _MQ_assoc($cque);
	    $total_cprice=0;
	    foreach($cres as $ck=>$cv){
	        $total_cprice+=$cv['cl_price'];
	    }

	// 상태 버튼 값
	$paystatusArray = array(
		"미결제"=>"<span class='blue'>미결제</span>",
		"결제확인"=>"<span class='red'>결제확인</span>",
		"발송완료"=>"<span class='orange'>발송완료</span>",
		"발송대기"=>"<span class='light'>발송대기</span>",
		"발급완료"=>"<span class='orange'>발급완료</span>",
		"발급대기"=>"<span class='light'>발급대기</span>",
	);

	// 주문자정보
	$_member = _MQ(" select * from odtMember where id = '".$r[orderid]."' and userType = 'B' ");
?>





<div class="post_hide_section">

	<!--  ●●●●● 내용들어가는 공간 -->
	<div class="container ">

		<!-- ●●●●● 데이터폼 -->
		<div class="data_form">


			<!-- 주문상품정보박스 -->
			<div class="cart_item_list if_nocart if_orderview">
				<!-- 단락타이틀 필요한 경우 -->
				<div class="group_title">주문상품정보</div>

				<ul>

<?PHP

	// 배송비 rowspan 적용을 위한 상품코드별 개수 추출
	$arr_pcodecnt = array();
	$tmpque = "
		select op_pcode , count(*) as cnt from odtOrderProduct as op
		left join odtProduct as p on (p.code=op.op_pcode)
		left join odtOrderCouponLog as cl on (cl.cl_pcode = op.op_pcode and cl.cl_oordernum = op.op_oordernum)
		where op.op_oordernum='".$ordernum."'
		group by op_pcode
	";
	$tmpres = _MQ_assoc($tmpque);
	foreach( $tmpres as $k=>$v ){
		$arr_pcodecnt[$v[op_pcode]] = $v[cnt];
	}


	// 주문 상품정보 추출
	$totalPrice = 0 ;//총상품가격
	$sque = "
		select
			op.* , p.prolist_img , p.expire , cl.cl_title , cl.cl_price, o.orderstatus_step
		from odtOrderProduct as op
		left join odtProduct as p on (p.code=op.op_pcode)
		left join odtOrderCouponLog as cl on (cl.cl_pcode = op.op_pcode and cl.cl_oordernum = op.op_oordernum)
		left join odtOrder as o on (o.ordernum = op.op_oordernum)
		where op.op_oordernum='".$ordernum."' order by p.code , op.op_delivery_price desc , op.op_is_addoption desc
	";
	$sres = _MQ_assoc($sque);

	    // 쿠폰별 가격 정산 추출 $v['ocs_ordernum']    2015-11-13 LCY002
	    $cque = "
	        select
	            cl_price
	        from odtOrderCouponLog
	        where cl_oordernum='".$ordernum."'
	    ";
	    $cres = _MQ_assoc($cque);
	    $total_cprice=0;
	    foreach($cres as $ck=>$cv){
	        $total_cprice+=$cv['cl_price'];
	    }



	// 현금영수증용 상품명 생성
	$cash_product_name = (count($sres)>1)?$sres[0][op_pname].'외 '.(count($sres)).'개':$sres[0][op_pname];

	foreach( $sres as $sk=>$sv ){
		// -- 이미지 ---
		$img_src	= app_thumbnail( "장바구니" , $sv );
		$img_src = @file_exists("/upfiles/product/" . $img_src) ? $img_src : $sv[prolist_img];

		// -- 추가옵션 ---
		$add_option = "";
		if($sv[op_add_option1]||$sv[op_add_option2]||$sv[op_add_option3]||$sv[op_add_option4]||$sv[op_add_option5]||$sv[op_add_option6]||$sv[op_add_option7]||$sv[op_add_option8]||$sv[op_add_option9]||$sv[op_add_option10]) {
			if($sv[op_add_option1_name]) { $add_option .= '['.$sv[op_add_option1_name].':'.$sv[op_add_option1].']&nbsp;'; }
			if($sv[op_add_option2_name]) { $add_option .= '['.$sv[op_add_option2_name].':'.$sv[op_add_option2].']&nbsp;'; }
			if($sv[op_add_option3_name]) { $add_option .= '['.$sv[op_add_option3_name].':'.$sv[op_add_option3].']&nbsp;'; }
			if($sv[op_add_option4_name]) { $add_option .= '['.$sv[op_add_option4_name].':'.$sv[op_add_option4].']&nbsp;'; }
			if($sv[op_add_option5_name]) { $add_option .= '['.$sv[op_add_option5_name].':'.$sv[op_add_option5].']&nbsp;'; }
			if($sv[op_add_option6_name]) { $add_option .= '['.$sv[op_add_option6_name].':'.$sv[op_add_option6].']&nbsp;'; }
			if($sv[op_add_option7_name]) { $add_option .= '['.$sv[op_add_option7_name].':'.$sv[op_add_option7].']&nbsp;'; }
			if($sv[op_add_option8_name]) { $add_option .= '['.$sv[op_add_option8_name].':'.$sv[op_add_option8].']&nbsp;'; }
			if($sv[op_add_option9_name]) { $add_option .= '['.$sv[op_add_option9_name].':'.$sv[op_add_option9].']&nbsp;'; }
			if($sv[op_add_option10_name]) { $add_option .= '['.$sv[op_add_option10_name].':'.$sv[op_add_option10].']'; }
		}
		// -- 추가옵션 ---


		// -- 쿠폰정보 ---
		unset($coupon_html,$coupon_html_body,$use_cnt,$notuse_cnt);
		if($sv[op_orderproduct_type] == "coupon") {
			$coupon_assoc = _MQ_assoc("select * from odtOrderProductCoupon where opc_opuid = '".$sv[op_uid]."'");
			if(sizeof($coupon_assoc) < 1) {
				$coupon_html_body = "<div class='coupon_number' style='padding-left:10px; padding-bottom:10px; '>"._DescStr_mobile_totaladmin("결제가 확인되면 쿠폰이 발급됩니다." , "orange")."</div>";
			}
			foreach($coupon_assoc as $coupon_key => $coupon_row) {

				// 미사용, 사용 쿠폰 개수
				if($coupon_row[opc_status] == "대기") {
					$notuse_cnt++;
					$coupon_html_body .="
						<div class='coupon_number'>
							<span class='texticon_pack'><span class='orange'>미사용</span></span>
							".$coupon_row[opc_expressnum]."
							<span class='button_pack'><a href='/skin/".$row_setup[P_SKIN]."/mypage.order.pro.php?_mode=coupon_sms_resend&opcuid=".$coupon_row[opc_uid]."' class='btn_sm_black' target='common_frame'>문자발송</a></span>
						</div>
					";
				}
				else if($coupon_row[opc_status] == "사용") {
					$coupon_html_body .="
						<div class='coupon_number'>
							<span class='texticon_pack'><span class='light'>사용완료</span></span>
							".$coupon_row[opc_expressnum]."
						</div>
					";
					$use_cnt++;
				}
				else if($coupon_row[opc_status] == "취소") {
					$coupon_html_body .="
						<div class='coupon_number'>
							<span class='texticon_pack'><span class='dark'>취소</span></span>
							".$coupon_row[opc_expressnum]."
						</div>
					";
				}
			}
			$coupon_html .="

				<dd class='thisis_coupon'>
					" . $coupon_html_body . "
				</dd>
			";
		}
		// -- 쿠폰정보 ---

		// -- 배송상품정보 ::: 택배, 송장, 발송일 표기 ---
		if($sv[op_orderproduct_type] == "product" && $sv[op_delivstatus] == "Y" ) {
			$coupon_html .="
				<dd class='thisis_coupon'>
					<div class='thisis_txt'>택배사 : ". $sv[op_expressname] ."</div>
					<div class='thisis_txt'>송장번호 : ". $sv[op_expressnum] ."</div>
					<div class='thisis_txt'>발송일 : ". substr($sv[op_expressdate],0, 10) ."</div>
				</dd>
			";
		}
		// -- 배송상품정보 ---

		// -- 발송여부 --- LMH001
		$app_status = "<span class='texticon_pack checkicon'>";
		if($sv[op_cancel]=='Y') { $app_status .= "<span class='dark'>주문취소</span>"; }
		else if($sv[op_cancel]=='R') { $app_status .= "<span class='purple'>취소요청중</span>"; }
		else {
			if($sv[op_orderproduct_type] == "product") { $app_status .= ($sv[op_delivstatus] == "Y" ? "<span class='orange'>발송완료</span>" : "<span class='light'>발송대기</span>");  }
			else { $app_status .= ($sv[op_delivstatus] == "Y" ? "<span class='orange'>발급완료</span>" : "<span class='light'>발급대기</span>"); }
		}
		$app_status .= "</span>";
		// -- 발송여부 ---


		// 부분취소 버튼 LMH001
		unset($tmp_delivery_print , $delivery_print , $_cancel_btn , $status_print , $_individual_cancel);
		if($r[paystatus]=="Y") {
			switch($sv[op_cancel]) {
				case "Y": // 취소완료
				case "R": // 취소요청중
					$_cancel_btn = "";
				break;
				case "N":
					// 부분취소 버튼
					$_cancel_btn = "<dd><span class='button_pack'><a href='#none' onclick='return false;'  class='product_cancel btn_md_black' data-ordernum='".$sv[op_oordernum]."' data-opuid='".$sv[op_uid]."'>부분취소</a></span></dd>";

					// 부분취소 신청 form // <!-- ★★★★★★★★★★★ 부분취소 클릭하면 열림 (2015-11-16) -->
					$_individual_cancel = "
						<div class='part_cancel' ID='opuid_form_".$sv[op_uid]."'>
<form name='product_cancel_".$sv[op_uid]."'>
<input type='hidden' name='mode' value='cancel'/>
<input type='hidden' name='ordernum' value='".$sv[op_oordernum]."'/>
<input type='hidden' name='op_uid' value='".$sv[op_uid]."'/>
<input type='hidden' name='paymethod' value='".$r[paymethod]."'/>
							<div class='data_form'>

								<!-- 테이블형 div colspan 하고 싶으면 ul에 if_full -->
								<div class='like_table'>
									<ul class=''>
										<li class='opt ess'>환불수단</li>
										<li class='value'>
											<label><input type='radio' name='cancel_type' value='pg' />PG사 직접 환불</label>
											<label><input type='radio' name='cancel_type' value='point' />적립금 환불</label>
										</li>
									</ul>
									".(
										!in_array($r[paymethod],array('C','G')) ?
										"
											<ul class=''>
												<li class='opt ess'>환불계좌</li>
												<li class='value'>
													<div class='select'>
														<span class='shape'></span>
														". _InputSelect( "cancel_bank" , array_keys($ksnet_bank) , $_member[cancel_bank] , "" , array_values($ksnet_bank) , "은행선택") ."
													</div>
													<input type='tel' name='cancel_bank_account' class='input_design' value='".$_member[cancel_bank_account]."' placeholder='계좌번호'/>
													<input type='text' name='cancel_bank_name' class='input_design' value='".$_member[cancel_bank_name]."' placeholder='예금주'/>
												</li>
											</ul>
										" :
										""
									)."
									<ul class=''>
										<li class='opt '>전달내용</li>
										<li class='value'>
											<textarea cols='' rows='' class='textarea_design' name='cancel_msg' placeholder='관리자에게 전달하실 내용이 있다면 입력해주세요.' ></textarea>
										</li>
									</ul>
								</div>

							</div>
							<!-- / 데이터폼 -->

							<!-- 컨트롤 버튼들 -->
							<div class='order_view_btn'>
								<dl>
									<dd><span class='button_pack'><a href='#none' onclick='return false;' class='btn_md_blue product_cancel_submit' data-opuid='".$sv[op_uid]."'>부분취소 신청하기</a></span></dd>
									<dd><span class='button_pack'><a href='#none' onclick='return false;' class='btn_md_white product_cancel_close'  data-opuid='".$sv[op_uid]."'>닫기</a></span></dd>
								</dl>
							</div>
</form>
						</div>
					";

				break;
			}
		}

		// -- 배송상태 ---
		if($sv[op_delivstatus] == "Y" && $sv[op_orderproduct_type] == "product") {
			$status_print = "<dd><span class='button_pack'><a href='".$arr_delivery_company[$sv[op_expressname]].rm_str($sv[op_expressnum])."' target='_blank' class='btn_md_black' >배송조회</a></span></dd>";
		}
		// -- 배송상태 ---


		// -- 배송비 ---
		if($sv[op_orderproduct_type] == "product") {	// 배송적용 상품이 아니면
			$tmp_delivery_print .=  ($sv['op_delivery_price'] > 0 && $delivery_print != "무료배송" ? "<strong>".number_format($sv[op_delivery_price])."</strong>원" : "무료배송");
			$tmp_delivery_print .=  ($sv[op_add_delivery_price] ? "<div class='guide_txt'>추가배송비 : +".number_format($sv[op_add_delivery_price])."원</div>" : "");
		}
		// -- 배송비 ---

		// -- 배송상태 ---
		if($prev_pcode != $sv[op_pcode] && $tmp_delivery_print) {
			$delivery_print =  "
				<div class='item_charge'>
					<dl>
						<dd>
							<span class='opt'>배송비</span>
							<div class='value'>" . $tmp_delivery_print . "</div>
						</dd>
					</dl>
				</div>
			";
		}
		$prev_pcode = $sv[op_pcode];
		// -- 배송상태 ---



		// -- 변수적용 ---
		$totalPrice += ($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt] ;//총상품가격
		$totalClprice += ($sv[cl_price] > 0 ? $v[cl_price] : 0 );  //총 상품별 사용 쿠폰가격
		$totadlPrice += $sv[op_delivery_price] + $sv[op_add_delivery_price] ;//총배송비

		echo "
			<li>

				<!-- 상품이름과 사진 -->
				<div class='item_info'>
					<div class='thumb'>". ($img_src ? "<img src='" . replace_image('/upfiles/product/'.$img_src) . "' >" : "-") ."</div>
					<div class='name'>" . stripslashes($sv[op_pname]) . "</div>
				</div>

				<!-- 옵션등과 쿠폰정보 (옵션반복) -->
				<div class='item_name'>

					<!-- 옵션별 상태표시 -->
					<div class='order_view_state'>" . $app_status . "</div>

					<dl>
						<!-- 필수옵션 -->
						<dd class=''>
							<div class='option_name'>
								" . ($sv[op_option1] ? "".($sv[op_is_addoption]=="Y" ? "추가옵션" : "선택옵션")." : " . trim($sv[op_option1]." ".$sv[op_option2]." ".$sv[op_option3]) :  "옵션없음" ) . "
								" . ($sv[cl_title] ? "<br>적용쿠폰 : ".$sv[cl_title]." ( ".number_format($sv[cl_price])."원 할인" :  "" ) . "
								" . ($sv[expire] && $sv[op_orderproduct_type] == "coupon" ? "<br>유효기간 : ".$sv[expire]." 까지" :  "" ) . "
							</div>

							<!-- 수량조정/가격 -->
							<div class='counter_box'>
								<span class='option_number'>
									<!-- 옵션가격 --><span class='option_price'><strong>" . number_format($sv[op_pprice] + $sv[op_poptionprice]) . "</strong>원 <em>X</em></span>
									<!-- 구매개수 --><strong>" . $sv[op_cnt] . "</strong>개
								</span>
								<span class='counter_right'>
									<!-- 합계금액 --><span class='option_price'><strong>" . number_format(($sv[op_pprice] + $sv[op_poptionprice]) * $sv[op_cnt]) . "</strong>원</span>
								</span>
							</div>

						</dd>

						" . $coupon_html . "

					</dl>

					<!-- 컨트롤 버튼들 -->
					<div class='order_view_btn'>
						<dl>". $_cancel_btn . $status_print . "</dl>
					</div>

					". $_individual_cancel . "


				</div>
				<!-- / 옵션등과 쿠폰정보 (옵션반복) -->

				<!-- 상품가격(배송비) -->
				". $delivery_print ."

			</li>
			<!-- 상품별 li반복 -->
		";
	}


	echo "
		<li>
			<div class='item_charge'>
				<dl>
					<dd>
						<span class='opt'>총합계금액</span>
						<div class='value'>
							<strong>" . number_format($r[tPrice]) . "</strong>원
							<div class='guide_txt'>총상품가격 : +" . number_format($totalPrice) . "원</div>
							<div class='guide_txt'>총배송비 : +" . number_format($r[dPrice]) . "원</div>
							<div class='guide_txt'>총할인금액 : -" . number_format($r[sPrice]) . "원</div>
						</div>
					</dd>
					<dd>
						<span class='opt'>적립금</span>
						<div class='value'>
							<div class='guide_txt'>" . number_format($r[gGetPrice]) . "원</div>
						</div>
					</dd>
					" . (
						$r['sPrice']>0 ? // 할인 상세내역 표시 2015-11-13 LCY002
						"
							<dd>
								<span class='opt'>할인상세내역</span>
								<div class='value'>
									<strong>" . number_format($r['gPrice']+$total_cprice) . "</strong>원
									<div class='guide_txt'>쿠폰 : +" . number_format($total_cprice) . "원</div>
									<div class='guide_txt'>포인트 : +" . number_format($r[gPrice]) . "원</div>
								</div>
							</dd>
						" :
						""
					)
					."
				</dl>
			</div>
		</li>
	";

?>
				</ul>
			</div>




<form name=frm method=post action="_order.pro.php">
<input type=hidden name=_mode value='modify'>
<input type=hidden name=ordernum value='<?=$ordernum?>'>
<input type=hidden name=code value='<?=$code?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
<input type="hidden" name="statusUpdate" value="yes">
			<!-- 테이블형 div colspan 하고 싶으면 ul에 if_full -->
			<div class="like_table">
				<!-- 단락타이틀 필요한 경우 -->
				<div class="group_title">관리자 관리내용</div>

				<ul class="if_full ess">
					<!-- <li class="opt">문의내용</li> -->
					<li class="value">
						<textarea cols="" rows="" class="textarea_design" name="comment1"><?=htmlspecialchars(stripslashes($r[comment1]))?></textarea>
					</li>
				</ul>





				<!-- 단락타이틀 필요한 경우 -->
				<div class="group_title">결제정보</div>
				<ul class=''>
					<li class=' opt'>총결제금액</li>
					<li class='value'>
						<div class='only_txt'><?=$OrderTotalPriceD?>원</div>
					</li>
				</ul>

<?PHP
	// -- 취소상태 표시 ---
	if($r[canceled] == "Y"){
		echo "
				<ul class=''>
					<li class=' opt'>주문상태</li>
					<li class='value'>
						<span class='texticon_pack checkicon'><span class='dark'>주문취소</span></span>
						<div class='only_txt'>(". date("Y년 m월 d일 H시 i분 s초",$r[canceldate]) .")</div>
					</li>
				</ul>
		";
	}
	else {
		echo "
			<ul class=''>
				<li class='opt '>강제취소</li>
				<li class='value'>
					<span class='button_pack'><a href='#none' class='btn_md_red' onclick=\"if(confirm('PG관리자에서 직접 주문을 취소하였거나 일부 오류로 강제 취소를 할 경우 사용 바랍니다.\\n\\n계속하시겠습니까?'))  document.location.href = '_order.pro.php?_mode=force_cancel&ordernum=".$ordernum."&_PVSC=".$_PVSC."';\">강제취소</a></span>
				</li>
			</ul>
		";
	}

	if($r[tPrice] > 0) {

		echo "
			<ul class=''>
				<li class=' opt'>결제상태</li>
				<li class='value'>".
					(
						$r[paystatus] == "Y" ?
							"<span class='texticon_pack checkicon'>".$paystatusArray["결제확인"]."</span>"
							:
							( !in_array( $r[paymethod] , array("B" , "E"))|| $r[canceled] == "Y" ?
								"<span class='texticon_pack checkicon'>".$paystatusArray["미결제"]."</span>"
								:
								_InputRadio_totaladmin("paystatus", array('Y', "N"), $row['paystatus'], "", array('결제확인', "결제미확인") ) .
								_DescStr_mobile_totaladmin("결제확인 시 쿠폰상품의 경우 자동발급되며, 그에 따라 메일이 발송되며 문자는 관리자 설정에 따릅니다.")
							)
					)
				."</li>
			</ul>
		";
	}
?>
				<ul class=''>
					<li class=' opt'>결제수단</li>
					<li class='value'>
						<div class='only_txt'>
							<?=$arr_paymethod_name[$r[paymethod]]?>
<?PHP
	if( in_array( $r[paymethod] , array("H" , "L" , "C") ) ) {
		echo $r[paystatus]=="Y" ? "(승인번호: <b>".$r[authum]."</b>)" : "<font color=red>(미결제)</font>";
	}
?>
						</div>
					</li>
				</ul>


<?PHP
	if( in_array( $r[paymethod] , array("B") ) ) {  // 무통장 입금 정보

		$OrderBankDiv = explode('/',$r[paybankname]);
		$BankName = $OrderBankDiv[0];
		$BankPerson = $OrderBankDiv[1];

		echo "
				<ul class=''>
					<li class=' opt'>입금은행</li>
					<li class='value'>
						<div class='only_txt'>" . $BankName . " " . $r[paybanknum] . " " . $BankPerson . "</div>
					</li>
				</ul>
				<ul class=''>
					<li class=' opt'>결제예정일</li>
					<li class='value'>
						<div class='only_txt'>" . $r[paydatey] . "-" . $r[paydatem] . "-" . $r[paydated] . "</div>
					</li>
				</ul>
				<ul class=''>
					<li class=' opt'>입금인명</li>
					<li class='value'>
						<div class='only_txt'>" . $r[payname] . "</div>
					</li>
				</ul>
		";
	}

	if( in_array( $r[paymethod] , array("V","E") ) ) { // 가상계좌 입금 정보

		$v_bank = _MQ("select ool_tid, ool_date, ool_account_num, ool_deposit_name, ool_bank_name from odtOrderOnlinelog where ool_ordernum='$ordernum' and ool_type='R'");

		echo "
				<ul class=''>
					<li class=' opt'>입금은행</li>
					<li class='value'>
						<div class='only_txt'>" . $v_bank[ool_bank_name] . " " . $v_bank[ool_account_num] . " " . $v_bank[ool_deposit_name] . "</div>
					</li>
				</ul>
				<ul class=''>
					<li class=' opt'>결제예정일</li>
					<li class='value'>
						<div class='only_txt'>" . date('Y-m-d',strtotime($v_bank[ool_date])+$row_setup[P_V_DATE]*86400) . "</div>
					</li>
				</ul>
				<ul class=''>
					<li class=' opt'>입금인명</li>
					<li class='value'>
						<div class='only_txt'>" . $v_bank[ool_deposit_name] . "</div>
					</li>
				</ul>
		";
	}
?>


<?  if($r[paystatus]=='Y' && in_array($r[paymethod],array("B","V","E"))) {  // 현금영수증 관련?>
				<ul class=''>
					<li class=' opt'>현금영수증</li>
					<li class='value'>
						<label><input type="checkbox" name="_get_tax" value="Y" <?=$r[taxorder] == "Y" ? "checked" : NULL;?> >현금영수증 발행을 신청합니다.</label>
						<?php
							if($r[taxorder]=='Y'){
								if(in_array($row_setup[P_KBN],array('L','K','B'))) { // 올더게이트, 이니시스는 현금영수증 자동 발행기능 미제공
									$cash_status = _MQ("select * from odtOrderCashlog where ocs_ordernum = '{$ordernum}' order by ocs_uid desc limit 1");

									if(/*$r[o_paymethod]=='virtual'&&*/!in_array($r[orderstatus_step],array('결제취소','결제실패','결제대기','주문취소'))&&sizeof($cash_status)==0){
										echo "<span class='button_pack' id='cash_status' style='clear:both;'><a href='#none' class='btn_md_white' id='cash_issue'>현금영수증</a></span>";
									}
									//if($r[o_paymethod]=='online') { echo _DescStr('무통장입금의 경우 현금영수증 자동 발행기능을 지원하지 않습니다. 직접 발행해야 합니다.'); }

									// 현금영수증 출력 준비
									$CST_PLATFORM = $row_setup[P_MODE]; $CST_MID = $row_setup[P_ID]; $LGD_MID = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
								}
								else {
									echo _DescStr_mobile_totaladmin('현금영수증은 '.$arr_pg_type[$row_setup[P_KBN]].' 가맹점페이지에서 직접 발행해야 합니다.');
								}
							}
						?>
					</li>
				</ul>
			<?if($r[taxorder]=='Y' && in_array($row_setup[P_KBN],array('L','K','B'))){?>
				<ul class='cash_container' style="display: <?=($r[taxorder]=='Y' && sizeof($cash_status)>0)?'block':'none'?>;">
					<li class=' opt'>현금영수증 발행 내역</li>
					<li class='value'>
						<?
							$cash_status_list = _MQ_assoc("select * from odtOrderCashlog where ocs_ordernum='{$ordernum}' order by ocs_uid");
							$cash_cancel_cnt = _MQ("select count(*) as cnt from odtOrderCashlog where ocs_ordernum='{$ordernum}' and ocs_method='CANCEL'");
							foreach($cash_status_list as $v) {
								$cash_del = $cash_cancel_cnt==0?" <span class='cash_cancel' style='display:inline-block; margin-left:10px; color: red; cursor: pointer;' data-tid='".$v['ocs_tid']."'>취소</span> ":'';
								echo "<div class='only_txt'>"
								.(($v['ocs_method']=='AUTH')?"<span style='color:green;'>O</span> 발행":"<span style='color:red;'>X</span> 취소")."일:</strong> ".date('Y-m-d h:i',strtotime($v['ocs_date']))
								." / <strong>주문번호</strong>: ".$v['ocs_ordernum']
								." / <strong>현금영수증 승인번호</strong>: ".$v['ocs_cashnum']
								//." / <strong>소비자번호</strong>: ".$v['ocs_cardnum']
								." / <strong>금액</strong>: ".number_format($v['ocs_amount'])
								.$cash_del.
								"</div>";
							}
						?>
					</li>
				</ul>
			<? } ?>

<? } ?>


<?php
	echo "
		<ul class=''>
			<li class=' opt'>결제/발송/발급 상태</li>
			<li class='value'>
				<span class='texticon_pack checkicon' >".$paystatusArray[$sv[orderstatus_step]]."</span><br/>
				" . (
					in_array($r[order_type] , array("coupon" , "both")) && in_array($r[orderstatus_step] , array("결제확인" , "발송대기" , "발송완료")) ?
						"<span class='button_pack' style='clear:both;'><a href='/totalAdmin/_order.coupon_view.php?ordernum=" . $r[ordernum] . "' class='btn_md_white' target='_blank'>쿠폰발송메일보기</a></span>" :
						""
				) . "
				". _DescStr_mobile_totaladmin('결제/발송/발급상태를 표시합니다.') ."
			</li>
		</ul>
	";
?>



				<div class="group_title">주문자 정보</div>
				<ul class="">
					<li class=" opt">주문번호</li>
					<li class="value">
						<div class="only_txt"><?=$r[ordernum]?></div>
					</li>
				</ul>
				<ul class="">
					<li class=" opt">주문일시</li>
					<li class="value">
						<div class="only_txt"><?=$OrderDate?></div>
					</li>
				</ul>
				<ul class="">
					<li class=" opt">배송비결제</li>
					<li class="value">
						<div class="only_txt"><?=($r[delchk]=="Y" ? "<b>배송비(착불)</b>" : "<b>배송비(선불)</b>")?></div>
					</li>
				</ul>
				<ul class="">
					<li class=" opt">주문자명</li>
					<li class="value">
						<div class="only_txt"><?=$r[ordername]?> (<?=$orderidTemp?>)</div>
					</li>
				</ul>
				<ul class="">
					<li class="opt">전화번호</li>
					<li class="value">
						<input type="tel" name="ordertel1" class="input_design" style="width:55px;" value="<?=$r[ordertel1]?>" /><span class="dash"></span>
						<input type="tel" name="ordertel2" class="input_design" style="width:55px;" value="<?=$r[ordertel2]?>" /><span class="dash"></span>
						<input type="tel" name="ordertel3" class="input_design" style="width:55px;" value="<?=$r[ordertel3]?>" />
					</li>
				</ul>
				<ul class="">
					<li class="opt">휴대폰번호</li>
					<li class="value">
						<input type="tel" name="orderhtel1" class="input_design" style="width:55px;" value="<?=$r[orderhtel1]?>" /><span class="dash"></span>
						<input type="tel" name="orderhtel2" class="input_design" style="width:55px;" value="<?=$r[orderhtel2]?>" /><span class="dash"></span>
						<input type="tel" name="orderhtel3" class="input_design" style="width:55px;" value="<?=$r[orderhtel3]?>" />
					</li>
				</ul>
				<ul class="">
					<li class="opt">E-mail</li>
					<li class="value">
						<input type="email" name="orderemail" class="input_design" value="<?=$r[orderemail]?>" />
					</li>
				</ul>



<?PHP
	// 쿠폰상품이 있을경우에만 노출.
	if($r[order_type] == "coupon" || $r[order_type] == "both") {
?>
				<div class="group_title">사용자 정보</div>
				<ul class="">
					<li class=" opt">사용자 이름</li>
					<li class="value">
						<input type="text" name="username" class="input_design" value="<?=$r[username]?>" />
					</li>
				</ul>
				<ul class="">
					<li class="opt">사용자 휴대폰</li>
					<li class="value">
						<input type="tel" name="userhtel1" class="input_design" style="width:55px;" value="<?=$r[userhtel1]?>" /><span class="dash"></span>
						<input type="tel" name="userhtel2" class="input_design" style="width:55px;" value="<?=$r[userhtel2]?>" /><span class="dash"></span>
						<input type="tel" name="userhtel3" class="input_design" style="width:55px;" value="<?=$r[userhtel3]?>" />
					</li>
				</ul>
				<ul class="">
					<li class="opt">사용자 E-mail</li>
					<li class="value">
						<input type="email" name="useremail" class="input_design" value="<?=$r[useremail]?>" />
					</li>
				</ul>
<?PHP
	}
?>


<?PHP
    // 배송상품이 있을경우에만 노출.
    if($r[order_type] == "product" || $r[order_type] == "both") {
?>
				<div class="group_title">받는분 정보</div>
				<ul class="">
					<li class=" opt">받는분 이름</li>
					<li class="value">
						<input type="text" name="username" class="input_design" value="<?=$r[recname]?>" />
					</li>
				</ul>
				<ul class="">
					<li class="opt">전화번호</li>
					<li class="value">
						<input type="tel" name="rectel1" class="input_design" style="width:55px;" value="<?=$r[rectel1]?>" /><span class="dash"></span>
						<input type="tel" name="rectel2" class="input_design" style="width:55px;" value="<?=$r[rectel2]?>" /><span class="dash"></span>
						<input type="tel" name="rectel3" class="input_design" style="width:55px;" value="<?=$r[rectel3]?>" />
					</li>
				</ul>
				<ul class="">
					<li class="opt">휴대폰번호</li>
					<li class="value">
						<input type="tel" name="rechtel1" class="input_design" style="width:55px;" value="<?=$r[rechtel1]?>" /><span class="dash"></span>
						<input type="tel" name="rechtel2" class="input_design" style="width:55px;" value="<?=$r[rechtel2]?>" /><span class="dash"></span>
						<input type="tel" name="rechtel3" class="input_design" style="width:55px;" value="<?=$r[rechtel3]?>" />
					</li>
				</ul>
				<ul class="">
					<li class="opt">받는분 E-mail</li>
					<li class="value">
						<input type="email" name="recemail" class="input_design" value="<?=$r[recemail]?>" />
					</li>
				</ul>
				<ul class="">
					<li class="opt">우편번호</li>
					<li class="value">
						<input type="tel" name="_rzip1" id="_post1" class="input_design" value="<?=$r[reczip1]?>" style="width:50px;"/><span class="dash"></span>
						<input type="tel" name="_rzip2" id="_post2" class="input_design" value="<?=$r[reczip2]?>" style="width:50px;"/>
						<span class="button_pack"><a href='#none' onclick="post_popup_show(); return false;" class="btn_md_black">우편번호</a></span>
					</li>
				</ul>
				<ul class="">
					<li class="opt">배송지 주소</li>
					<li class="value">
						<input type="text" name="_raddress" id="_addr1" class="input_design" value="<?=$r[recaddress]?>" />
						<input type="text" name="_raddress1" id="_addr2" class="input_design" value="<?=$r[recaddress1]?>" />
					</li>
				</ul>
				<ul class="">
					<li class="opt">도로명 주소</li>
					<li class="value">
						<input type="text" name="_raddress_doro" id="_addr_doro" class="input_design" value="<?=$r[recaddress_doro]?>" />
					</li>
				</ul>
				<ul class="">
					<li class="opt">새 우편번호</li>
					<li class="value">
						<input type="text" name="_rzonecode" id="_zonecode" class="input_design" value="<?=$r[reczonecode]?>" />
					</li>
				</ul>
				<?php
				# LDD018
				if($r['delivery_date'] != '0000-00-00') {
				?>
				<ul class="">
					<li class="opt">배송 요청일</li>
					<li class="value">
						<input type="text" name="delivery_date" class="input_design" value="<?=$r[delivery_date]?>" />
					</li>
				</ul>
				<?php } ?>
				<ul class=" ">
					<li class="opt">배송시 유의사항</li>
					<li class="value">
						<textarea cols="" rows="" class="textarea_design" name="comment"><?=htmlspecialchars(stripslashes($r[comment]))?></textarea>
					</li>
				</ul>
<?PHP
	}
?>

			</div>

		</div>
		<!-- / 데이터폼 -->

	</div>
	<!-- / 내용들어가는 공간 -->



	<!-- ●●●●●●●●●● 가운데정렬버튼 -->
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><input type="submit" class="btn_lg_red" value="확인"></span></li>
			<li><span class="button_pack"><a href="_order.list.php?<?=enc('d' , $_PVSC)?>" class="btn_lg_white">목록으로</a></span></li>
		</ul>
	</div>
	<!-- / 가운데정렬버튼 -->
</form>






<? if($r[paymethod]=='V' && $r[paystatus]=='Y') { ?>
<form action="_order.pro.php" method="post" target="" name="refund">
<div class="form_box_area">
<input type="hidden" name="_mode" value="<?=($r[moneyback])?'moneyback':'cancel'?>"/>
<input type="hidden" name="ordernum" value="<?=$r[ordernum]?>"/>
<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"/>

	<!--  ●●●●● 내용들어가는 공간 -->
	<div class="container ">
		<!-- ●●●●● 데이터폼 -->
		<div class="data_form">
			<div class="like_table">

				<div class="group_title">주문 취소하기</div>

	<? if($r[moneyback]=='환불완료') { ?>
				<ul class="">
					<li class=" opt">환불완료</li>
					<li class="value">
						<?=_DescStr_mobile_totaladmin($r[moneyback_comment]." 로 환불이 완료되었습니다. 자세한 사항은 <a href='_order_moneyback.list.php'>환불요청관리</a> 페이지에서 확인하세요.")?>
					</li>
				</ul>
	<? } else { ?>
		<? if($r[moneyback]=='환불요청') { ?>
				<ul class="">
					<li class=" opt">환불요청중</li>
					<li class="value">
						<div class="only_txt"><?=$r[moneyback_comment]?></div>
						<?=_DescStr_mobile_totaladmin("환불 요청 중입니다. 아래에서 환불 계좌정보를 변경할 수 있습니다.")?>
						<?=_DescStr_mobile_totaladmin("자세한 사항은 PC버전의 환불요청관리 페이지에서 확인하세요.")?>
					</li>
				</ul>
		<? } ?>
				<ul class=''>
					<li class='opt ess'>환불받을 은행</li>
					<li class='value'>
						<div class='select'>
							<span class='shape'></span>
								<?
									$ool_bank_name_array = array('39'=>'경남', '34'=>'광주', '04'=>'국민', '03'=>'기업', '11'=>'농협', '31'=>'대구', '32'=>'부산', '02'=>'산업', '45'=>'새마을금고', '07'=>'수협', '88'=>'신한', '26'=>'신한', '48'=>'신협', '05'=>'외환', '20'=>'우리', '71'=>'우체국', '37'=>'전북', '35'=>'제주', '81'=>'하나', '27'=>'한국씨티', '53'=>'씨티', '23'=>'SC은행', '09'=>'동양증권', '78'=>'신한금융투자증권', '40'=>'삼성증권', '30'=>'미래에셋증권', '43'=>'한국투자증권', '69'=>'한화증권');
									echo _InputSelect( "bank_code" , array_keys($ool_bank_name_array) , "" , "" , array_values($ool_bank_name_array) , "-선택-")
								?>
						</div>
					</li>
				</ul>
				<ul class="">
					<li class="opt">환불받을 계좌번호</li>
					<li class="value">
						<input type="text" name="refund_account" class="input_design" value="" />
					</li>
				</ul>
				<ul class="">
					<li class="opt">환불계좌 예금주명</li>
					<li class="value">
						<input type="text" name="refund_nm" class="input_design" value="" />
						<?=_DescStr_mobile_totaladmin("가상계좌 결제는 고객의 환불계좌 정보를 입력해야 취소됩니다.")?>
					</li>
				</ul>
	<? } ?>
			</div>
		</div>
	</div>
	<? if($r[moneyback]!='환불완료') { ?>
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><input type="submit" class="btn_lg_red" value="주문취소"></span></li>
		</ul>
	</div>
	<? } ?>
</form>
<? } ?>



</div>


<? include_once dirname(__FILE__)."/../../../newpost/newpost.search_m.php"; ?>
<?php
	include dirname(__FILE__)."/wrap.footer.php";
?>



<script>
	<?
		$clque = "select * from odtOrderCardlog where oc_oordernum= '".$ordernum."'"; $clr = _MQ($clque);
		$company_info = _MQ("select * from odtCompany where serialnum='1'");
		$paymethod_convert = array('B'=>'online','C'=>'card','V'=>'virtual','L'=>'iche');
	?>

	// 현금영수증을 신청합니다. 버튼을 누르면 odtOrder 테이블의 taxorder 필드 업데이트
	$('input[name=_get_tax]').on('click',function(){
		if($(this).is(':checked')) { var tax = 'Y'; } //$('.cash_container').css('display','inline-block');
		else { var tax = 'N'; }//$('.cash_container').hide();
		//$.post('_order.form.cashUpdate.php',{tax: tax, ordernum: '<?=$ordernum?>'});
		$.ajax({
			data: {tax:tax, ordernum: '<?=$ordernum?>'},
			type: 'POST',
			cache: false,
			url: '/totalAdmin/_order.form.cashUpdate.php',
			success: function() { window.location.reload(); }
		});
	});

	$('#cash_issue').on('click',function(e){ // 현금영수증 발행 버튼
		e.preventDefault();
		if (confirm('<?=$r[ordername]?>님 <?=$r[orderhtel1].'-'.$r[orderhtel2].'-'.$r[orderhtel3]?> 번호로 현금영수증 발행을 신청합니다.')) {
			$.ajax({
				data: {
					method:		'AUTH',
					ordernum:	'<?=$ordernum?>',
					paymethod:	'<?=$paymethod_convert[$r[paymethod]]?>',
					tid:		'<?=$clr[oc_tid]?>',
					member:		'<?=$r[orderid]?>',
					amount:		'<?=$r[tPrice]?>',
					num:		'<?=$r[orderhtel1].$r[orderhtel2].$r[orderhtel3]?>',
					use:		'1', // 발급용도 1 = 소득공제, 2 = 지출증빙
					product:	'<?=$cash_product_name?>', // 상품명
					store:		'<?=$company_info[number1]?>' // 상점 사업자등록번호
				},
				type: 'POST',
				cache: false,
				url: '/pages/totalCashReceipt.ajax.php',
				success: function(data) {
					if(data=='AUTH'){ // 작업에 성공했다면 진행 - AUTH = 현금영수증 발행, OK = 현금영수증 신청 완료
						$('#cash_status').remove();
						window.location.reload();
					} else if(data=='OK') {
						return false;
					} else { // 아니라면 오류 메세지
						alert('현금영수증 발행에 실패했습니다.'+data);
					}
				},
				error:function(request,status,error){
					alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		} else {
			return false;
		}
	});

	$('.cash_cancel').on('click',function(e){ // 현금영수증 발행 버튼
		e.preventDefault();
		var tid = $(this).attr('data-tid');
		if (confirm('현금영수증 발행을 취소합니다.')) {
			$.ajax({
				data: {
					method:		'CANCEL',
					tid: 		tid,
					ordernum:	'<?=$ordernum?>',
					paymethod:	'<?=$paymethod_convert[$r[paymethod]]?>',
					member:		'<?=$r[orderid]?>',
					amount:		'<?=$r[tPrice]?>',
					num:		'<?=$r[orderhtel1].$r[orderhtel2].$r[orderhtel3]?>',
					use:		'1', // 발급용도 1 = 소득공제, 2 = 지출증빙
					product:	'<?=$cash_product_name?>', // 상품명
					store:		'<?=$company_info[number1]?>' // 상점 사업자등록번호
				},
				type: 'POST',
				cache: false,
				url: '/pages/totalCashReceipt.ajax.php',
				success: function(data) {
					if(data=='CANCEL'){ // 작업에 성공했다면 진행 - AUTH = 현금영수증 발행, OK = 현금영수증 신청 완료
						$('#cash_status').remove();
						window.location.reload();
					} else if(data=='OK') {
						return false;
					} else { // 아니라면 오류 메세지
						alert('현금영수증 취소에 실패했습니다.'+data);
					}
				},
				error:function(request,status,error){
					alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		} else {
			return false;
		}
	});

	$("form[name=refund]").validate({
		ignore: "input[type=text]:hidden",
		rules: {
			bank_code: { required: true },
			refund_account: { required: true },
			refund_nm: { required: true },
		},
		messages: {
			bank_code: { required: "은행을 선택하세요" },
			refund_account: { required: "계좌번호를 입력하세요" },
			refund_nm: { required: "예금주명을 입력하세요" },
		}
	});
</script>







<!-- / 부분취소신청 -->
<script>
$(document).ready(function(){

	// 부분취소 닫기
    $('.product_cancel_close').on('click',function(){
		var app_uid = $(this).data("opuid");
		$("#opuid_form_" + app_uid).hide(); // 선택 부분취소 닫기
    });

	// 부분취소 열기
    $('.product_cancel').on('click',function(){
		var app_uid = $(this).data("opuid");
		$("#opuid_form_" + app_uid).show(); // 선택 부분취소 열기
    });

    $('.product_cancel_submit').on('click',function(){

		var arr_type = [ '1', 'pg' , 'point' ]; // 1은 dummy data
		var arr_paymethod = [ '1', 'C' , 'G' ]; // 1은 dummy data
		var app_uid = $(this).data("opuid");
		var app_form = $("form[name='product_cancel_"+app_uid+"']");
		var app_cancel_type = $("form[name='product_cancel_"+app_uid+"'] input[name='cancel_type']").filter(function() {if (this.checked) return this;}).val();

		// 사전 체크
		if( jQuery.inArray( app_cancel_type , arr_type) < 0  ) {alert('환불수단을 선택해주시기 바랍니다.');return false;}//환불수단
		if( jQuery.inArray( $("form[name='product_cancel_"+app_uid+"'] input[name='paymethod']").val() , arr_paymethod) < 1 && $("form[name='product_cancel_"+app_uid+"'] select[name='cancel_bank']").val() == '' ) {alert('은행을 선택해주시기 바랍니다.');return false;}//환불계좌 - 은행선택 (PG선택시 적용되게 함)
		if( jQuery.inArray( $("form[name='product_cancel_"+app_uid+"'] input[name='paymethod']").val() , arr_paymethod) < 1 && $("form[name='product_cancel_"+app_uid+"'] input[name='cancel_bank_account']").val() == '' ) {alert('계좌번호를 입력해주시기 바랍니다.');return false;}//환불계좌 - 계좌번호 (PG선택시 적용되게 함)
		if( jQuery.inArray( $("form[name='product_cancel_"+app_uid+"'] input[name='paymethod']").val() , arr_paymethod) < 1 && $("form[name='product_cancel_"+app_uid+"'] input[name='cancel_bank_name']").val() == '' ) {alert('예금주를 입력해주시기 바랍니다.');return false;}//환불계좌 - 예금주 (PG선택시 적용되게 함)

		if(confirm("정말 주문을 취소하시겠습니까?")===true) {
			var app_data = app_form.serialize();
            $.ajax({
                data: app_data , type: 'POST' , cache: false,
                url: '/pages/mypage.order.view.ajax.php',
                success: function(data) {
                    if(data=='OK') {alert('성공적으로 취소요청 되었습니다.'); location.reload(); return false;}
					else {alert(data);}
                },
                error:function(request,status,error){
                    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                }
            });
        }
    });
});
</script>
<!-- / 부분취소신청 -->



<?php
# LDD018
if($r['delivery_date'] != '0000-00-00') {
?>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
	$(function() {
	 	$("input[name=delivery_date]").datepicker({changeMonth: true, changeYear: true });
        $("input[name=delivery_date]").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("input[name=delivery_date]").datepicker( "option",$.datepicker.regional["ko"] );
    });
</script>
<?php } ?>