<?PHP
	// 날짜가 저장된 파일
	$date_file = $_SERVER[DOCUMENT_ROOT].IMG_DIR_NORMAL."update.date.php";
	@include_once($date_file);

	// 하루 한번 실행한다.
	if($last_update_date != date("Y-m-d")) {


		//---------- 발급 또는 발송이 일어나지 않는 경우 날짜 기록 상단에서 실행 ----------

			// 카테고리별 상품 수량 조정 2015-11-21 LDD
			//update_catagory_product_count();


			// 포인트를 업데이트 한다.
			point_update();

			// 참여점수 레벨을 갱신한다.
			action_point_update();

			// 지급쿠폰 만료일 처리
			_MQ_noreturn("update odtCoupon set coUse ='E'  where coLimit  <= CURDATE() and coUse='N'");

			// 자동정산대기 처리
			include_once(dirname(__FILE__).'/inc.settle.update.php');

		//---------- 발급 또는 발송이 일어나지 않는 경우 날짜 기록 상단에서 실행 ----------



        // 오늘날짜를 기록한다.
        $fp = fopen($date_file, "w");
        fputs($fp,"<?PHP\n\t\$last_update_date = '".date("Y-m-d")."';?>");
        fclose($fp);

		// 휴면계정 별도 저장처리 -- odtMember -> odtMemberSleep 복사 후 변형
		member_sleep_backup();


		//---------- 발급 또는 발송이 일어날 경우 날짜 기록 하단에서 실행 (중복 방지) ----------

			// 입금기한 지난 무통장결제 취소 처리 LMH004
			if($row_setup['auto_cancel'] == 'Y') {

				$arr_send = array();
				$b_row = _MQ_assoc(" select ordernum from odtOrder where paymethod = 'B' and DATE_ADD(orderdate,INTERVAL + ".$row_setup['P_B_DATE']." day) < CURDATE() and canceled = 'N' and paystatus = 'N' ");
				foreach($b_row as $k=>$v) {
					// - 취소처리 ---
					$_ordernum = $v['ordernum'];
					$_applytype = "admin";// 실행타입 : admin , member ::: member 일 경우 반드시 get_userid() 있어야 함
					// return 변수 1 : $_trigger = "Y"; // 처리형태 : Y(성공) , N(실패)
					// return 변수 2 : $arr_send ::: 문자메시지
					include($_SERVER["DOCUMENT_ROOT"] . "/pages/pg.cancle_total.php");
					// - 취소처리 ---
				}
				//if(sizeof($arr_send) > 0 ){ onedaynet_sms_multisend($arr_send); }
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				if(sizeof($arr_send) > 0 ){ onedaynet_alimtalk_multisend($arr_send); }
			}
			// 입금기한 지난 무통장결제 취소 처리 끝


			// 2016-05-18 ::: 매 2년마다 수신동의 설정 ----- 수신동의한지 2년이 넘은 회원 체크하여 - odt2yearOptLog 에 데이터 등록
			$_2year_opt_file_name = $_SERVER["DOCUMENT_ROOT"] . "/include/addons/2yearOpt/inc.2year_opt.php";
			if(@file_exists($_2year_opt_file_name)) { include_once($_2year_opt_file_name); }

		//---------- 발급 또는 발송이 일어날 경우 날짜 기록 하단에서 실행 (중복 방지) ----------

		// PG 사별로 결제는 되었으나 상태가 결제 대기이고 하루전인 주문 결제 완료로 변경 kms 2019-04-22
		switch($row_setup['P_KBN']){
			case "D" :
				include_once($_SERVER["DOCUMENT_ROOT"] . '/pages/check.pg.daupay.php');
				break;
			case "L" :
				include_once($_SERVER["DOCUMENT_ROOT"] . '/pages/check.pg.lgpay.php');
				break;
			case "I" :
				include_once($_SERVER["DOCUMENT_ROOT"] . '/pages/check.pg.inicis.php');
				break;
		}
	}
?>