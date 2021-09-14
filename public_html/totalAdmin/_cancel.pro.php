<?
	include_once("inc.php");


	switch($_mode) {

	// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가  - op_cancel_discount_price 추가 -  ::: JJC --------------------
		case "req_cancel":

			$r = _MQ("
				SELECT * FROM odtOrderProduct as op
				left join odtProduct as p on (p.code=op.op_pcode)
				left join odtOrderCouponLog as cl on (cl.cl_pcode = op.op_pcode and cl.cl_oordernum = op.op_oordernum)
				left join odtOrder as o on (o.ordernum = op.op_oordernum)
				WHERE ordernum='" . $ordernum . "' and op_uid = '".$op_uid."'
			");
			if( $r['op_cancel'] <> 'R') { error_msg('부분취소 요청에 대한 삭제를 실행할 수 없는 상태입니다.'); }

			//추가옵션이 있다면 함께 취소
			$tmp = _MQ_assoc(" select * from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$r['op_pouid']."' and op_oordernum = '".$ordernum."' ");
			if( count($tmp)>0 ) {
				foreach($tmp as $tk=>$tv) {
					$sque = " update odtOrderProduct set
						op_cancel = 'N',
						op_cancel_msg = '',
						op_cancel_bank = '',
						op_cancel_bank_name = '',
						op_cancel_bank_account = '',
						op_cancel_rdate = '0000-00-00 00:00:00',
						op_cancel_type = '',
						op_cancel_discount_price = 0
						where op_oordernum = '".$ordernum."' and op_uid = '".$tv['op_uid']."'
					";
					// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가  - op_cancel_discount_price 추가 -  ::: JJC --------------------
					//echo $que . "<hr>";
					_MQ_noreturn($sque);
				}
			}

			$que = " update odtOrderProduct set
				op_cancel = 'N',
				op_cancel_msg = '',
				op_cancel_bank = '',
				op_cancel_bank_name = '',
				op_cancel_bank_account = '',
				op_cancel_rdate = '0000-00-00 00:00:00',
				op_cancel_type = '',
				op_cancel_discount_price = 0
				where op_oordernum = '".$ordernum."' and op_uid = '".$op_uid."'
			";
			//echo $que . "<hr>";
			_MQ_noreturn($que);

			error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "부분취소 요청을 삭제하였습니다.");

			break;
		// --- 부분취소 요청 삭제 - 2016-07-01 추가 ---






		// 부분취소 - 실행
		case "cancel":

			unset($totalPrice,$totadlPrice,$totalAprice);

			$r = _MQ("
				SELECT * FROM odtOrderProduct as op
				left join odtProduct as p on (p.code=op.op_pcode)
				left join odtOrderCouponLog as cl on (cl.cl_pcode = op.op_pcode and cl.cl_oordernum = op.op_oordernum)
				left join odtOrder as o on (o.ordernum = op.op_oordernum)
				WHERE ordernum='" . $ordernum . "' and op_uid = '".$op_uid."'
			");

			// 추가옵션 출력
			$add_res = _MQ_assoc(" select * from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$r['op_pouid']."' and op_oordernum = '".$r['op_oordernum']."' ");
			if( count($add_res) > 0 ){
				foreach($add_res as $adk=>$adv) {
					$totalAprice += ($adv[op_pprice] + $adv[op_poptionprice]) * $adv[op_cnt] + $adv[op_delivery_price] + $adv[op_add_delivery_price] ;
				}
			}

			$chk = _MQ(" select op_cancel from odtOrderProduct where op_uid = '".$op_uid."' ");
			if($chk[op_cancel]=='Y') { error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "이미 취소된 주문입니다."); }


			// 2016-11-30 ::: 환불 비용 계산 ::: JJC ---
			//		return $__cancelTotal = array('pg'=>PG비용 , 'point'=>포인트비용);
			//		reutnr $__console = 타입; // 적립금환불 요청 시
			//	넘길 변수
			//		$opr <== 부분취소 상품의 주문상품 / 주문 / 상품배열정보
			//		$ordernum <== 주분번호
			//		$totalPrice <== 부분취소 상품의 상품가격
			//		$totadlPrice <== 부분취소 상품의  배송비
			//		$totalAprice
			//		$totalDiscount <== 부분취소 상품의 할인비용
			$opr = $r;
			$totalPrice = ($r['op_pprice'] + $r['op_poptionprice']) * $r['op_cnt'] ;//총상품가격
			$totadlPrice = $r['op_delivery_price'] + $r['op_add_delivery_price'] ;//총배송비
			$totalDiscount = $r['op_cancel_discount_price'];// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------
			include("_cancel.inc_calc.php");// *** 파일생성 ***
			// 2016-11-30 ::: 환불 비용 계산 ::: JJC ---

			// op_usepoint 이동
			$_usep = _MQ(" select op_uid, op_usepoint from odtOrderProduct where op_cancel = 'N' and op_oordernum = '".$ordernum."' and op_uid != '".$op_uid."' order by ((op_pprice + op_poptionprice) * op_cnt ) desc limit 1 ");
			if(count($_usep)>0) {
				_MQ_noreturn(" update odtOrderProduct set op_usepoint = 0 where op_uid = '".$r['op_uid']."' ");
				_MQ_noreturn(" update odtOrderProduct set op_usepoint = op_usepoint + '". $r['op_usepoint'] ."' where op_uid = '".$_usep['op_uid']."' ");
			}
			$_ordernum = $ordernum; $_uid = $op_uid; $_applytype = "admin";


			unset($__trigger);
			if( $__cancelTotal['point'] > 0 ) {
				$_cancel_type = 'point';
				shop_pointlog_insert( $r[orderid] , "주문취소에 따른 사용 적립금 반환 (주문번호 : ".$_ordernum.")" , $__cancelTotal['point'] , "N" , 0);
				$_trigger = 'Y'; if($_trigger=='Y') { $__trigger++; }
				_MQ_noreturn(" update odtOrder set gRefundPrice = gRefundPrice + '".$__cancelTotal['point']."' where ordernum = '".$_ordernum."' ");
			}
			$_cancel_type = 'pg'; $_total_amount = $__cancelTotal['pg'];
			include($_SERVER["DOCUMENT_ROOT"] . "/pages/pg.cancle_part.php");
			//if(sizeof($arr_send) > 0 ){ onedaynet_sms_multisend($arr_send); }
			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			if(sizeof($arr_send) > 0 ){ onedaynet_alimtalk_multisend($arr_send); }
			if($_trigger=='Y') { $__trigger++; }

			if($__trigger > 0){ error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "주문을 취소하였습니다."); }
			else { error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "결제 취소요청이 실패하였습니다."); }

		break;






		case "mass":
			unset($__trigger);
			foreach($OpUid as $k=>$v) {

				unset($totalPrice,$totadlPrice,$totalAprice);

				$r = _MQ("
					SELECT * FROM odtOrderProduct as op
					left join odtProduct as p on (p.code=op.op_pcode)
					left join odtOrderCouponLog as cl on (cl.cl_pcode = op.op_pcode and cl.cl_oordernum = op.op_oordernum)
					left join odtOrder as o on (o.ordernum = op.op_oordernum)
					WHERE op_uid = '".$v."'
				");
				$ordernum = $r['op_oordernum']; $op_uid = $v;

				// 추가옵션 출력
				$add_res = _MQ_assoc(" select * from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$r['op_pouid']."' and op_oordernum = '".$r['op_oordernum']."' ");
				if( count($add_res) > 0 ){
					foreach($add_res as $adk=>$adv) {
						$totalAprice += ($adv[op_pprice] + $adv[op_poptionprice]) * $adv[op_cnt] + $adv[op_delivery_price] + $adv[op_add_delivery_price] ;
					}
				}

				$chk = _MQ(" select op_cancel from odtOrderProduct where op_uid = '".$op_uid."' ");
				if($chk[op_cancel]=='Y') { continue; /*error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "이미 취소된 주문입니다.");*/ }



				// 2016-11-30 ::: 환불 비용 계산 ::: JJC ---
				//		return $__cancelTotal = array('pg'=>PG비용 , 'point'=>포인트비용);
				//		reutnr $__console = 타입; // 적립금환불 요청 시
				//	넘길 변수
				//		$opr <== 부분취소 상품의 주문상품 / 주문 / 상품배열정보
				//		$ordernum <== 주분번호
				//		$totalPrice <== 부분취소 상품의 상품가격
				//		$totadlPrice <== 부분취소 상품의  배송비
				//		$totalAprice
				//		$totalDiscount <== 부분취소 상품의 할인비용
				$opr = $r;
				$totalPrice = ($r['op_pprice'] + $r['op_poptionprice']) * $r['op_cnt'] ;//총상품가격
				$totadlPrice = $r['op_delivery_price'] + $r['op_add_delivery_price'] ;//총배송비
				$totalDiscount = $r['op_cancel_discount_price'];// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------
				include("_cancel.inc_calc.php");// *** 파일생성 ***


					// op_usepoint 이동
					$_usep = _MQ(" select op_uid, op_usepoint from odtOrderProduct where op_cancel = 'N' and op_oordernum = '".$ordernum."' and op_uid != '".$op_uid."' order by ((op_pprice + op_poptionprice) * op_cnt ) desc limit 1 ");
					if(count($_usep)>0) {
						_MQ_noreturn(" update odtOrderProduct set op_usepoint = 0 where op_uid = '".$r['op_uid']."' ");
						_MQ_noreturn(" update odtOrderProduct set op_usepoint = op_usepoint + '". $r['op_usepoint'] ."' where op_uid = '".$_usep['op_uid']."' ");

						//_MQ_noreturn(" update odtOrderProduct set op_usepoint = 0 where op_uid = '".$op_uid."' ");
						// 추가옵션이 있다면 op_usepoint 함께 이동
						/*$add_res = _MQ_assoc(" select * from odtOrderProduct where op_is_addoption = 'Y' and op_addoption_parent = '".$r['op_pouid']."' and op_oordernum = '".$r['op_oordernum']."' ");
						if( count($add_res)>0 ) {
							foreach($add_res as $adk=>$adv) {
								_MQ_noreturn(" update odtOrderProduct set op_usepoint = op_usepoint+'".$adv[op_usepoint]."' where op_uid = '".$_usep[op_uid]."' ");
								_MQ_noreturn(" update odtOrderProduct set op_usepoint = 0 where op_uid = '".$adv[op_uid]."' ");
							}
						}*/
					}

				$_ordernum = $ordernum; $_uid = $op_uid; $_applytype = "admin";
				//$_total_amount = $cancel_total; $_cancel_type = $r[op_cancel_type];

				if( $__cancelTotal['point'] > 0 ) {
					$_cancel_type = 'point';
					shop_pointlog_insert( $r[orderid] , "주문취소에 따른 사용 적립금 반환 (주문번호 : ".$_ordernum.")" , $__cancelTotal['point'] , "N" , 0);
					$_trigger = 'Y'; if($_trigger=='Y') { $__trigger++; }
					_MQ_noreturn(" update odtOrder set gRefundPrice = gRefundPrice + '".$__cancelTotal['point']."' where ordernum = '".$_ordernum."' ");
				}
				$_cancel_type = 'pg'; $_total_amount = $__cancelTotal['pg'];
				include($_SERVER["DOCUMENT_ROOT"] . "/pages/pg.cancle_part.php");
				//if(sizeof($arr_send) > 0 ){ onedaynet_sms_multisend($arr_send); }
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				if(sizeof($arr_send) > 0 ){ onedaynet_alimtalk_multisend($arr_send); }
				if($_trigger=='Y') { $__trigger++; }
			}

			if($__trigger > 0){ error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "주문을 취소하였습니다."); }
			else { error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "결제 취소요청이 실패하였습니다."); }

		break;

		case "modify":
			$cancel_bank = nullchk($cancel_bank , "환불 은행을 선택하시기 바랍니다.");
			$cancel_bank_account = nullchk($cancel_bank_account , "환불 계좌번호를 입력하시기 바랍니다.");
			$cancel_bank_name = nullchk($cancel_bank_name , "환불 예금주명을 입력하시기 바랍니다.");

			_MQ_noreturn("
				update odtOrderProduct set
					op_cancel_bank = '".$cancel_bank."',
					op_cancel_bank_account = '".$cancel_bank_account."',
					op_cancel_bank_name = '".$cancel_bank_name."',
					op_cancel_msg = '".$cancel_msg."'
				where op_oordernum = '".$ordernum."' and op_uid = '".$op_uid."'
			");

			error_frame_loc_msg("_cancel.form.php?_mode=modify&ordernum=".$ordernum."&uid=".$op_uid."&_PVSC=".$_PVSC , "정보를 수정 하였습니다.");
		break;

		// - 엑셀다운로드 ---
	    case "select_excel": // 선택
	    case "search_excel": // 검색

			$toDay = date("YmdHis");
			$fileName = iconv('utf-8','euc-kr',"부분취소내역");

			// -- Exel 파일로 변환 ---
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");


			if($_mode == "select_excel") {
				$app_order_num = implode("','" , $OpUid);
				$s_query = " where op.op_uid in ('" . $app_order_num . "') ";
			}
			else if($_mode == "search_excel") {
				$s_query = enc('d',$_search_que);
			}

			echo "
					<table border=1>
						<tr>
							<td>주문번호</td><td>구매상품정보</td><td>주문자</td><td>E-mail</td><td>핸드폰번호</td><td>총결제액</td><td>배송료</td><td>할인액</td><td>결제수단</td><td>주문일시</td><td>결제일시</td><td>취소요청일시</td><td>취소처리일시</td><td>취소상태</td><td>환불금액</td><td>환불은행</td><td>환불계좌번호</td><td>환불예금주</td><td>고객 요청내용</td>
						</tr>
			";
			$que = " select * from odtOrderProduct as op left join odtOrder as o on (o.ordernum = op.op_oordernum) " . $s_query . " ORDER BY op_cancel_rdate desc ";
			$res = _MQ_assoc($que);
			foreach($res as $k=>$v) {

				// -- 상품정보 추출 ---
				$tmp_content  = "";
				$sque = "
					SELECT
						op.* , p.name , p.purPrice, ttt.ttt_value as comment3
					FROM odtOrderProduct as op
					left join odtProduct as p on ( p.code=op.op_pcode )
					left join odtTableText as ttt on ( p.serialnum = ttt.ttt_datauid and ttt.ttt_tablename = 'odtProduct' and ttt.ttt_keyword = 'comment3')
					where op.op_oordernum='". $v[ordernum] ."' and op.op_uid = '".$v[op_uid]."'
					order by op.op_is_addoption desc
				";
				$sres = _MQ_assoc($sque);
				foreach($sres as $sk=>$sv) {

					// 옵션값 추출(OrderNumValue:주문번호 offset:주문일련번호)
					$itemName = $sv[name];
					if($sv[op_option1]) {   // 해당상품에 대한 옵션내역이 있으면
						$itemName .= " (".($sv[op_is_addoption]=="Y" ? "추가" : "선택")." : " . trim($sv[op_option1]." ".$sv[op_option2]." ".$sv[op_option3]).")";
					}
					$itemName .= " " . $sv[op_cnt]."개";
					$itemName =  ($sk <> 0 ? " / " : "") . $itemName ;

					$tmp_content .= $itemName;
				}
				// -- 상품정보 추출 ---

				//$app_delchk = ($r[delchk]=="Y" ? "착불" : "선불");

                if($v[viewDel]=="1") {
                    $tel = $v[ordertel1]."-".$v[ordertel2]."-".$v[ordertel3];
                    $htel = $v[orderhtel1]."-".$v[orderhtel2]."-".$v[orderhtel3];
                }
                else if($v[viewDel] !="1" && $v[ordername] != $v[recname]){
                    $tel = $v[ordertel1]."-".$v[ordertel2]."-".$v[ordertel3];
                    $htel = $v[orderhtel1]."-".$v[orderhtel2]."-".$v[orderhtel3];
                }
                else{
                     $tel = $v[ordertel1]."-".$v[ordertel2]."-".$v[ordertel3];
                    $htel = $v[orderhtel1]."-".$v[orderhtel2]."-".$v[orderhtel3];
                }

				// 결제방식
				$PayMethod = $arr_paymethod_name[$v[paymethod]];

				$OrderDate  = $v[orderdate] != "0000-00-00 00:00:00" ? date("Y-m-d H:i:s", strtotime($v[orderdate])) : "미주문";
				$PayDate        = $v[paydate] != "0000-00-00 00:00:00" ? date("Y-m-d H:i:s", strtotime($v[paydate])) : "";
				$DelyDate       = $v[expressdate] ? $v[expressdate] : "";

				$cancel_status = $v[op_cancel]=='Y' ? '취소완료' : '취소요청중';
				$cancel_total = ( ($v[op_pprice] + $v[op_poptionprice]) * $v[op_cnt] ) + $v[op_delivery_price] + $v[op_add_delivery_price];
				$cancel_bank = $ksnet_bank[$v[op_cancel_bank]];
				$cancel_bank_account = $v[op_cancel_bank_account];
				$cancel_bank_name = $v[op_cancel_bank_name];
				$cancel_msg = $v[op_cancel_msg];
				$cancel_rdate = date('Y-m-d H:i:s',strtotime($v[op_cancel_rdate]));
				$cancel_cdate = ( rm_str($v[op_cancel_cdate])>0 ? date('Y-m-d H:i:s',strtotime($v[op_cancel_cdate])) : "" );

				echo "
					<tr>
						<td>$v[ordernum]</td><td>$tmp_content</td><td>$v[ordername]</td><td>$v[orderemail]</td><td>$htel</td><td>$v[tPrice]</td><td>$v[dPrice]</td><td>$v[sPrice]</td><td>$PayMethod</td><td>$OrderDate</td><td>$PayDate</td><td>$cancel_rdate</td><td>$cancel_cdate</td><td>$cancel_status</td><td>$cancel_total</td><td>$cancel_bank</td><td>$cancel_bank_account</td><td>$cancel_bank_name</td><td>$cancel_msg</td>
					</tr>
				";
			}
			echo "</table>";
			break;

	}

?>