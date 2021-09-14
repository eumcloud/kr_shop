<?PHP

	#####################################################
	### 설명 : 쿠폰(배송상품) 발행파일
	### 참조 :
	### PARAM : force(Y) Y=쿠폰재발송상태(데이터기록을하지않음)
	#####################################################

	include_once("inc.php");


	// 타입별 메일발송
	// order_email( 주문번호 , 이메일 , 타입 , 이미지 )
	function order_email( $_ordernum , $_oemail , $_type , $_title_img , $_title , $_opuids = NULL ){
		$_ordernum = $_ordernum;
		$_oemail = $_oemail;
		$_type = $_type ; // delivery , coupon
		$_title = $_title;
		//$_title_img = $_img ; // images/mailing/title_delivery.jpg , images/mailing/title_coupon.jpg
		include("../pages/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
		$_content = $mailing_app_content;
		$_content = get_mail_content($_title,$_title_img,$_content);
		mailer( $_oemail , $_title , $_content );
//		echo " $_oemail .. $_title ";
//		highlight_string($_content);
//		echo "<hr>";
	}


	if(!is_array($OpUid)) { $OpUid = array($op_uid); }
	if(!is_array($op_uid)) { $op_uid = array($op_uid); }
	if(!is_array($OrderNumValue)) { $OrderNumValue = array($ordernum); }
	if(!is_array($expressname)) { $expressname = array($expressname); }
	if(!is_array($expressnum)) { $expressnum = array($expressnum); }

	// -- 송장번호 적용 ---
	if( $ordertype == "product") {

		$no_expresnum_cnt_temp = 0;
		$arr_ordernum = array(); // 주문정보 저장
		$arr_send = $arr_dup_chk = array();
		foreach( $OpUid as $k=>$v ){

			// --- 주문 및 주문상품 정보 추출 ---
			$opque = " SELECT op.*, o.* FROM odtOrderProduct as op left join odtOrder as o on ( o.ordernum=op.op_oordernum ) where op.op_uid = '". $v ."' ";
			$opr = _MQ($opque);

			// --- 주문상품 key , 쿠폰정보 추출 ---
			foreach( $op_uid as $sk=>$sv ){
				if($v==$sv && ($expressnum[$sk] || $expressname[$sk] == '[자체배송]')) {

                    // @ 2017-02-27 LCY :: 주문상품패치 추가 $op_uid temp => 주문상품 고유번호
                    $op_uid_temp = $v;

					$sque = "
						update odtOrderProduct set
							op_expressname = '". $expressname[$sk] ."',
							op_expressnum = '".$expressnum[$sk]."',
							op_expressdate = now()
						where op_uid = '". $v ."'
					";
					// - 신규발급 적용 ---
					if( $force <> "Y" ) {
						//echo $sque . "<br>";
						_MQ_noreturn($sque);
						_MQ_noreturn("update odtOrderProduct set op_delivstatus = 'Y' where op_uid = '". $sv ."'"); // 상태를 발급완료로 변경 // - 신규발급 적용 ---
					}

					if($mode!='modify') {

						// ----- 중복된 건은 한번만 보낼 수 있도록 함. --  택배사 중괄호 있는 경우 문제발생 - 택배사 제외 -----
						if(!in_array( $expressnum[$sk] . $opr['op_oordernum'] , array_keys($arr_dup_chk))){

							// ---- 배송상품 sms 발송. ---
							$smskbn = "express";	// 문자 발송 유형
							if($row_sms[$smskbn][smschk] == "y") {

								// 사용자 전화번호 적용 - 2016-01-22
								$app_htel = tel_format($opr[rechtel1].$opr[rechtel2].$opr[rechtel3]);
								$app_htel_tmp = tel_format($opr[orderhtel1].$opr[orderhtel2].$opr[orderhtel3]);
								$sms_to		=  $app_htel ? $app_htel : $app_htel_tmp ;
								$sms_from	= $row_company[tel];

								//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
								// 치환작업
								$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $opr['ordernum'], array("{{택배사명}}"=>$expressname[$sk], "{{송장번호}}"=>$expressnum[$sk]), $op_uid_temp);
								$sms_msg = $arr_sms_msg['msg'];
								$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
								//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

								$arr_dup_chk[$expressnum[$sk] .  $opr['op_oordernum']] ++; // 중복체크

							}
						}
						// ----- 중복된 건은 한번만 보낼 수 있도록 함. --  택배사 중괄호 있는 경우 문제발생 - 택배사 제외 -----
					}
					// 주문정보 저장
					$arr_ordernum[$OrderNumValue[$sk]] = $opr[orderemail];
				} else { $no_expresnum_cnt_temp++;}
			}
		}


		// 티켓 메일과 티켓 문자를 전송
		//if(sizeof($arr_send) > 0 ) { onedaynet_sms_multisend($arr_send); }
		//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		if(sizeof($arr_send) > 0 ) { onedaynet_alimtalk_multisend($arr_send); }



		if($mode!='modify') {
			// 선택 주문 loop
			foreach( $arr_ordernum as $orderk=>$orderv ){
				// --- 메일 발송. ---
				// order_email( 주문번호 , 이메일 , 타입 , 이미지 , 타이틀 )
				order_email( $orderk , $orderv , "delivery" , '<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">고객님</strong>께서 주문하신 상품이 발송되었습니다. <br />저희 사이트에서 구매해주셔서 감사합니다. 보다 나은 상품과 큰 만족을 위해 최선을 다하겠습니다.' , "[".$row_setup[site_name]."] 주문하신 상품이 발송되었습니다." , $OpUid);// 배송상품 발송

				// --- 주문상태 업데이트 --- // - 신규발급 적용 ---
				if( $force <> "Y" ) {
					order_status_update($orderk);
				}
			}

			$msg = sizeof($op_uid)*sizeof($OpUid)-$no_expresnum_cnt_temp."건의 주문상품이 발송되었습니다.\\n\\n". sizeof($arr_send) ."건의 배송문자가 발송되었습니다." ;
			if((sizeof($OpUid) - (sizeof($op_uid)*sizeof($OpUid)-$no_expresnum_cnt_temp))>0) {
				$msg .= "\\n\\n발송에 실패한 주문상품이 있습니다. 송장번호를 입력하세요.";
			}
			error_frame_reload($msg) ;
		}
	}
	// -- 송장번호 적용 ---





	// -- 쿠폰발급 ---
	else if( $ordertype == "coupon") {
		$no_expresnum_cnt_temp = 0;
		$arr_ordernum = array(); // 주문정보 저장
		$arr_send = array();
		foreach( $OpUid as $k=>$v ){
			// --- 주문 및 주문상품 정보 추출 ---
			$opque = " SELECT op.*, o.* FROM odtOrderProduct as op left join odtOrder as o on ( o.ordernum=op.op_oordernum ) where op.op_uid = '". $v ."' ";
			$opr = _MQ($opque);

			// --- 주문상품 key , 쿠폰정보 추출 ---
			foreach( $op_uid as $sk=>$sv ){
				if($v==$sv && $expressnum[$sk]) {
					$sque = "
						insert into odtOrderProductCoupon set
							opc_opuid = '". $v ."',
							opc_expressnum = '".$expressnum[$sk]."',
							opc_rdatetime=now(),
							opc_status='대기'
					";
					// - 신규발급 적용 ---
					if( $force <> "Y" ) {
						//echo $sque . "<br>";
						_MQ_noreturn($sque);
						_MQ_noreturn("update odtOrderProduct set op_delivstatus = 'Y' where op_uid = '". $v ."'");
					}

					// ---- 쿠폰번호 sms 발송. ---
					$smskbn = "coupon";	// 문자 발송 유형
					if($row_sms[$smskbn][smschk] == "y") {

						// 사용자 전화번호 적용 - 2016-01-22
						$app_htel = tel_format($opr[userhtel1] . $opr[userhtel2] . $opr[userhtel3]);
						$app_htel_tmp = tel_format($opr[orderhtel1] . $opr[orderhtel2] . $opr[orderhtel3]);
						$sms_to		=  $app_htel ? $app_htel : $app_htel_tmp ;
						$sms_from	= $row_company[tel];

						//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
						// 치환작업
						$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $opr['ordernum'], array("{{쿠폰번호}}"=>$expressnum[$sk]));
						$sms_msg = $arr_sms_msg['msg'];
						$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
						//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

					}

					// 주문정보 저장
					$arr_ordernum[$OrderNumValue[$sk]] = $opr[orderemail];
				} else { $no_expresnum_cnt_temp++; }
			}
		}


		// 티켓 메일과 티켓 문자를 전송
		//if(sizeof($arr_send) > 0 ) { onedaynet_sms_multisend($arr_send); }
		//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		if(sizeof($arr_send) > 0 ) { onedaynet_alimtalk_multisend($arr_send); }


		if($mode!='modify') {
			// 선택 주문 loop
			foreach( $arr_ordernum as $orderk=>$orderv ){
				// --- 메일 발송. ---
				// order_email( 주문번호 , 이메일 , 타입 , 이미지 , 타이틀 )
				order_email( $orderk , $orderv , "coupon" , '<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">고객님</strong>께서 주문하신 쿠폰이 발송되었습니다. <br />저희 사이트에서 구매해주셔서 감사합니다. 보다 나은 상품과 큰 만족을 위해 최선을 다하겠습니다.', "[".$row_setup[site_name]."] 주문하신 상품의 쿠폰이 발송되었습니다.");// 쿠폰발송

				// --- 주문상태 업데이트 --- // - 신규발급 적용 ---
				if( $force <> "Y" ) {
					order_status_update($orderk);
				}
			}
			$msg = sizeof($op_uid)*sizeof($OpUid)-$no_expresnum_cnt_temp."건의 주문상품에 쿠폰이 발급되었습니다.\\n\\n". sizeof($arr_send) ."건의 쿠폰문자가 발급되었습니다.";
			if($no_expresnum_cnt_temp>0) {
				$msg .= "\\n\\n발급에 실패한 주문상품이 있습니다. 쿠폰번호를 입력하세요";
			}
			error_frame_reload($msg);
		}

	}
	// -- 쿠폰발급 ---


?>