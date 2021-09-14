<?PHP

	include_once("inc.php");


	switch ($_mode) {





		// - 결제승인 ---
		case "auth":

			if( sizeof($OrderNum) > 0 ) {
				$app_order_num = implode("','" , $OrderNum);
				$que = "update odtOrder set paystatus2='Y' where paystatus='Y' and paystatus2='N' and ordernum in ('" . $app_order_num . "') ";
				_MQ_noreturn($que);


				// 주문상태 업데이트
				foreach($OrderNum as $ordernum) {
					order_status_update($ordernum);
				}

				error_loc_msg("_order.list.php?" . enc('d' , $_PVSC) , "결제가 승인처리 되었습니다","top");
			}

			break;
		// - 결제승인 ---



		// - 결제수정 ---
		case "modify":

			$sque = "
				UPDATE odtOrder SET
					ordertel1		='" . rm_str($ordertel1) . "',
					ordertel2		='" . rm_str($ordertel2) . "',
					ordertel3		='" . rm_str($ordertel3) . "',
					orderhtel1		='" . rm_str($orderhtel1) . "',
					orderhtel2		='" . rm_str($orderhtel2) . "',
					orderhtel3		='" . rm_str($orderhtel3) . "',
					orderemail		='" . $orderemail . "',
					recname			='" . $recname . "',
					recemail		='" . $recemail . "',
					rectel1			='" . rm_str($rectel1) . "',
					rectel2			='" . rm_str($rectel2) . "',
					rectel3			='" . rm_str($rectel3) . "',
					rechtel1		='" . rm_str($rechtel1) . "',
					rechtel2		='" . rm_str($rechtel2) . "',
					rechtel3		='" . rm_str($rechtel3) . "',
					reczip1			='" . rm_str($_rzip1) . "',
					reczip2			='" . rm_str($_rzip2) . "',
					reczonecode		='" . rm_str($_rzonecode) . "',
					recaddress		='" . $_raddress . "',
					recaddress1		='" . $_raddress1 . "',
					recaddress_doro	='" . $_raddress_doro . "',
					username		='" . $username . "',
					userhtel1		='" . rm_str($userhtel1) . "',
					userhtel2		='" . rm_str($userhtel2) . "',
					userhtel3		='" . rm_str($userhtel3) . "',
					useremail		='" . $useremail . "',
					comment			='" . trim($comment) . "',
					comment1		='" .trim($comment1) . "',
					taxorder		= '".$_get_tax."',
					delivery_date	= '".$delivery_date."'
				WHERE
					ordernum		='" . $ordernum . "'
			"; // #LDD018 (delivery_date	= '".$delivery_date."')
			_MQ_noreturn($sque);

			// 주문발송 상태 변경
			order_status_update($ordernum);


			$r = _MQ("SELECT * FROM odtOrder WHERE ordernum='" . $ordernum . "'");


			//- 결제확인 메일 및 문자 발송 --- 취소주문 수정이 아닌경우
			if( $r[canceled] == "N" && $r[paystatus]<>$paystatus && $paystatus == "Y" ) {

				// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
				$_ordernum = $ordernum ;
				$sque = "update odtOrder set paystatus='Y' ,paydate = now() where ordernum='". $ordernum ."' ";
				_MQ_noreturn($sque);

				// 주문발송 상태 변경
				order_status_update($ordernum);


				// 결제가 확인되었을 경우 - 포인트 쿠폰 - 적용
				// 제공변수 : $_ordernum
				include_once($_SERVER["DOCUMENT_ROOT"] . "/pages/shop.order.pointadd_pro.php");

				// 쿠폰상품은 티켓을 발행한다.
				// 제공변수 : $_ordernum
				include_once($_SERVER["DOCUMENT_ROOT"] . "/pages/shop.order.couponadd_pro.php");

				// 상품 재고 차감 및 판매량 증가
				include_once($_SERVER["DOCUMENT_ROOT"] . "/pages/shop.order.salecntadd_pro.php");

				// 제휴마케팅 처리
				include_once($_SERVER["DOCUMENT_ROOT"] . "/pages/shop.order.aff_marketing_pro.php");

				// - 메일발송 ---
				if( mailCheck($r[orderemail]) ){
					$_ordernum = $ordernum;
					$_type = "payconfirm"; // 결제확인처리
					include($_SERVER["DOCUMENT_ROOT"] . "/pages/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
					$_title = "[".$row_setup[site_name]."] 입금내역이 확인되었습니다.!";
					$_title_content = '';
					$_content = $mailing_app_content;
					$_content = get_mail_content($_title, $_title_content, $_content);
					mailer( $r[orderemail] , $_title , $_content );
				}
				// - 메일발송 ---

				// - 문자발송 ---
				$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				$smskbn = "payconfirm_mem";	// 문자 발송 유형
				if($row_sms[$smskbn][smschk] == "y") {
					$sms_to		= phone_print($orderhtel1,$orderhtel2,$orderhtel3);
					$sms_from	= $row_company[tel];

					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
					// 치환작업
					$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $r['ordernum']);
					$sms_msg = $arr_sms_msg['msg'];
					$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

				}
				//onedaynet_sms_multisend($arr_send);
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				onedaynet_alimtalk_multisend($arr_send);
				// - 문자발송 ---

				// 주문발송 상태 변경
				order_status_update($ordernum);

			}
			//- 결제확인 메일 및 문자 발송 --- 취소주문 수정이 아닌경우


			error_loc_msg("_order.form.php?_mode=modify&ordernum=${ordernum}&_PVSC=${_PVSC}" , "수정이 잘 되었습니다.");

			break;
		// - 결제수정 ---


		case "cancel":

			// 부분취소된 주문이 있는지 체크
			$tmp = _MQ(" select count(*) as cnt from odtOrderProduct where op_oordernum = '".$ordernum."' and (op_cancel = 'Y' or op_cancel = 'R') ");
			if ( $tmp[cnt] > 0 ) {
				$app_link = "_order.form.php?_mode=modify&ordernum=".$ordernum."&_PVSC=".$_PVSC;
				error_frame_loc_msg($app_link,"부분취소된 상품이 있습니다. 나머지 상품도 부분취소 요청으로 처리하시기 바랍니다.");
			} else {

				// - 취소처리 ---
				$_ordernum = $ordernum;
				$_applytype = "admin";// 실행타입 : admin , member ::: member 일 경우 반드시 get_userid() 있어야 함

				// return 변수 1 : $_trigger = "Y"; // 처리형태 : Y(성공) , N(실패)
				// return 변수 2 : $arr_send ::: 문자메시지
				include($_SERVER["DOCUMENT_ROOT"] . "/pages/pg.cancle_total.php");
				//if(sizeof($arr_send) > 0 ){ onedaynet_sms_multisend($arr_send); }
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				if(sizeof($arr_send) > 0 ){ onedaynet_alimtalk_multisend($arr_send); }

				if($_trigger == "Y"){ error_loc_msg("_order.list.php?_PVSC=${_PVSC}" , "주문을 취소하였습니다."); }
				else { error_loc_msg("_order.list.php?_PVSC=${_PVSC}" , "결제 취소요청이 실패하였습니다."); }
				// - 취소처리 ---

			}

			break;

		case "force_cancel":

			// - 취소처리 ---
			$_ordernum = $ordernum;
			$_applytype = "admin";// 실행타입 : admin , member ::: member 일 경우 반드시 get_userid() 있어야 함
			$_force_cancel = true;

			// return 변수 1 : $_trigger = "Y"; // 처리형태 : Y(성공) , N(실패)
			// return 변수 2 : $arr_send ::: 문자메시지
			include($_SERVER["DOCUMENT_ROOT"] . "/pages/pg.cancle_total.php");
			//if(sizeof($arr_send) > 0 ){ onedaynet_sms_multisend($arr_send); }
			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			if(sizeof($arr_send) > 0 ){ onedaynet_alimtalk_multisend($arr_send); }

			if($_trigger == "Y"){ error_loc_msg("_order.list.php?_PVSC=${_PVSC}" , "주문을 강제취소하였습니다."); }
			else { error_loc_msg("_order.list.php?_PVSC=${_PVSC}" , "결제 강제취소요청이 실패하였습니다."); }
			// - 취소처리 ---

			break;

		case "moneyback":
			$_ordernum = $ordernum;
			if($bank_code && $refund_account && $refund_nm) {
				$moneyback_content = '환불계좌: ['.$ool_bank_name_array[$bank_code].'] '.$refund_account.' '.$refund_nm;
				$que = "update odtOrder set moneyback_comment = '".$moneyback_content."' where ordernum = '".$_ordernum."'";
				_MQ_noreturn($que);
				$trigger = "Y";
			}
			error_loc_msg("_order.form.php?_mode=cancellist&ordernum=".$ordernum."&_PVSC=".$_PVSC , ($trigger=="Y" ? "처리되었습니다." : "계좌정보가 없습니다."));
			break;

		case "mass_cancel":
			$v_cnt = 0;
			foreach($OrderNum as $k=>$v) {

				// 부분취소된 주문이 있는지 체크
				$tmp = _MQ(" select count(*) as cnt from odtOrderProduct where op_oordernum = '".$v."' and (op_cancel = 'Y' or op_cancel = 'R') ");
				if ( $tmp[cnt] > 0 ) {
					$app_link = "_order.form.php?_mode=modify&ordernum=".$v."&_PVSC=".$_PVSC;
					error_loc_msg($app_link,"부분취소된 상품이 있습니다. 나머지 상품도 부분취소 요청으로 처리하시기 바랍니다.");
				} else {

					// - 취소처리 ---
					$_ordernum = $v;
					$_applytype = "admin";// 실행타입 : admin , member ::: member 일 경우 반드시 get_userid() 있어야 함

					// return 변수 1 : $_trigger = "Y"; // 처리형태 : Y(성공) , N(실패)
					// return 변수 2 : $arr_send ::: 문자메시지
					include($_SERVER["DOCUMENT_ROOT"] . "/pages/pg.cancle_total.php");

					if($_trigger == "N"){ error_loc_msg("_order.list.php?_PVSC=${_PVSC}" , "결제 취소요청이 실패하였습니다."); }
					// - 취소처리 ---
				}

			}
			//if(sizeof($arr_send) > 0 ){ onedaynet_sms_multisend($arr_send); }
			//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			if(sizeof($arr_send) > 0 ){ onedaynet_alimtalk_multisend($arr_send); }

			error_loc_msg("_order.list.php?_PVSC=${_PVSC}" , "주문이 취소되었습니다.","top");
			break;


		// - 엑셀다운로드 ---
		case "select_excel": // 선택
		case "search_excel": // 검색

			$toDay = date("YmdHis");
			$fileName = "od_order_list";

			// -- Exel 파일로 변환 ---
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");


			if($_mode == "select_excel") {
				$app_order_num = implode("','" , $OrderNum);
				$s_query = " where ordernum in ('" . $app_order_num . "') ";
			}
			else if($_mode == "search_excel") {
				$s_query = enc('d',$_search_que);
			}

			echo "
					<table border=1>
						<tr>
							<td>주문번호</td><td>구매상품정보</td><td>주문자</td><td>E-mail</td><td>수령인</td><td>전화번호</td><td>핸드폰번호</td><td>수령인이메일</td><td>총결제액</td><td>배송료</td><td>할인액</td><td>지급포인트</td><td>결제수단</td><td>주문일시</td><td>결제일시</td><td>새 우편번호</td><td>우편번호</td><td>주소</td><td>배송메세지</td>
						</tr>
			";
			$que = " select * from odtOrder " . $s_query . " ORDER BY serialnum desc ";
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
					where op.op_oordernum='". $v[ordernum] ."'
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
					$tel = $v[rectel1]."-".$v[rectel2]."-".$v[rectel3];
					$htel = $v[rechtel1]."-".$v[rechtel2]."-".$v[rechtel3];
				}
				else if($v[viewDel] !="1" && $v[ordername] != $v[recname]){
					$tel = $v[rectel1]."-".$v[rectel2]."-".$v[rectel3];
					$htel = $v[rechtel1]."-".$v[rechtel2]."-".$v[rechtel3];
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

				echo "
					<tr>
						<td>$v[ordernum]</td><td>$tmp_content</td><td>$v[ordername]</td><td>$v[orderemail]</td><td>$v[recname]</td><td>$tel</td><td>$htel</td><td>$v[recemail]</td><td>$v[tPrice]</td><td>$v[dPrice]</td><td>$v[sPrice]</td><td>$v[gGetPrice]</td><td>$PayMethod</td><td>$OrderDate</td><td>$PayDate</td><td>$v[reczonecode]</td><td>$v[reczip1]-$v[reczip2]</td><td>$v[recaddress] $v[recaddress1]</td><td>$v[comment]</td>
					</tr>
				";
			}
			echo "</table>";
			break;
		// - 엑셀다운로드 ---






		// - 선택주문 완전삭제 ---
		case "select_wiping":

			if( sizeof($OrderNum) > 0 ) {
				$que = "select * from odtOrder where canceled='Y' AND orderstatus='Y' and ordernum in ('" . implode("','" , $OrderNum) . "') ";
				$res = _MQ_assoc($que);
				foreach( $res as $k=>$v ){
					_MQ_noreturn("delete from odtOrder where ordernum='". $v[ordernum] ."' ");// 주문삭제
					//_MQ_noreturn("delete from odtOrderCardlog where oc_oordernum='". $v[ordernum] ."' ");// 주문결제기록(카드/이체)관리
					//_MQ_noreturn("delete from odtOrderCouponLog where cl_oordernum='". $v[ordernum] ."' ");// 주문 시 쿠폰사용 로그
					_MQ_noreturn("delete from odtOrderProduct where op_oordernum='". $v[ordernum] ."' ");// 주문상품관리 테이블
					_MQ_noreturn("delete from odtOrderSettle where os_oordernum='". $v[ordernum] ."' ");// 상점 정산관련 정보
				}
			}
			error_frame_loc_msg("_ordercancel.list.php?" . enc('d' , $_PVSC) , "선택하신 주문을 완전삭제하였습니다.");
			break;
		// - 선택주문 완전삭제 ---



	}


	exit;

?>