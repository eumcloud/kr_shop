<?PHP

	session_start();
	include( dirname(__FILE__) . "/../../include/inc.php");

	// 비회원 구매를 위한 쿠키 적용여부 파악
	cookie_chk();

	// LDD019 {
	if($row_setup['none_member_buy'] == 'N' && !is_login()) {
		error_msg("로그인 후 이용 가능합니다.");
	}
	// } LDD019

	// 회원정보 추출
	$indr = $row_member;
	$_use_point = str_replace(',','',$_use_point);

	// 사후 체킹1 - 결제금액 <> (구매총액 + 배송비 - 할인총액) LMH005
	if( $price_total <> ($price_sum + $price_delivery - ($_use_point + $use_coupon_price_member + $use_coupon_price_product + $use_promotion_price)) ) {
		error_loc_msg("/?pn=shop.order.form","결제금액이 맞지 않습니다.");
	}

	// -- 쿠폰을 사용했다면 db 정보와 같이 체크한다 :: 보안강화 -- 2017-07-10 LCY

	// 사후체킹1-2 : 결제금액이 0이고 할인금액이 0보다클때만 전액적립금결제 허용 LMH005
	if($_paymethod == "point"){
		if( !($price_total == 0 && ($_use_point + $use_coupon_price_member + $use_coupon_price_product + $use_promotion_price) > 0) ){
			error_loc_msg("/?pn=shop.order.form","전액 적립금 결제는 선택할수 없습니다. ");
		}
	}

	### -- 쿠폰검사 강화 패치 -- 2017-07-10 LCY {{{
	// -- 회원전용 쿠폰을 사용했다면 db 정보와 같이 체크한다 :: 회원전용 쿠폰 --
	if( $use_coupon_price_member > 0) {
		$coupon_info = _MQ("select * from odtCoupon where coNo = '".$use_coupon_member."' AND coUse = 'N' and coID = '".get_userid()."'  "); // 쿠폰번호가 있고 아직 미사용이고 회원인것
		$member_coupon_sum = count($coupon_info) > 0 ? $coupon_info['coPrice'] : 0;
		if( $member_coupon_sum != $use_coupon_price_member){  error_loc_msg("/?pn=shop.order.form","사용한 쿠폰금액이 맞지 않습니다.");  } // 주문에서 사용된 금액과 db 상 금액이 다르다면

	}

	// -- 상품쿠폰을 사용했다면 db 정보와 같이 체크한다 :: 상품쿠폰의 경우 %로 들어오기때문에 암호화된값으로 검증 -- 2017-07-10 LCY
	if( $use_coupon_price_product > 0) {
		if( count($product_coupon) > 0){
			$cartr = _MQ(" select ifnull(sum(FLOOR((c_price + c_optionprice) * c_cnt * p.coupon_price /100))  , 0) as chk_c_price from odtCart as c inner join odtProduct as p on(p.code = c.c_pcode) where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y' and c_pcode in ('".implode("','", array_keys($product_coupon))."')  ");// 선택 구매 2015-12-04 LDD
			if ( $use_coupon_price_product != $cartr['chk_c_price']){  error_loc_msg("/?pn=shop.order.form","사용한 쿠폰금액이 맞지 않습니다.");  }
		}else{ // 상품쿠폰 합산금액은 있는데 상품쿠폰 적용이 안되었다면 무조건 오류
			 error_loc_msg("/?pn=shop.order.form","사용한 쿠폰금액이 맞지 않습니다.");
		}
	}
	### -- 쿠폰검사 강화 패치 -- 2017-07-10 LCY }}}


	// 실결제금액 1000원 이상 체크
	if( $price_total < 1000 && $price_total != 0 ){ error_loc_msg("/?pn=shop.order.form","실제 결제금액은 1,000원 이상이어야 합니다."); }


	// - 주문서 저장 ---------------------------------------------
	if( $_SESSION["order_start"] == $_COOKIE["AuthShopCOOKIEID"]){

		// 사후 체킹2 - 구매총액 <> 장바구니 총액
		$cartr = _MQ(" select ifnull(sum((c_price + c_optionprice)*c_cnt) , 0) as sum_c_price from odtCart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y'  ");// 선택 구매 2015-12-04 LDD
		if( $price_sum <> $cartr['sum_c_price']) {
			error_loc_msg("/?pn=shop.order.form","결제금액이 맞지 않습니다.");
		}

		// -- 사후체크 ---
		$price_sum	= nullchk($price_sum , "상품이 선택되지 않았습니다.");
		$_oname		= nullchk($_oname , "주문자명을 입력해주시기 바랍니다.");
		$_oemail	= nullchk($_oemail , "이메일을 입력해주시기 바랍니다.");
		$_ohtel		= nullchk($_ohtel , "휴대폰번호을 입력해주시기 바랍니다.");
		$_paymethod	= nullchk($_paymethod , "결제방식을 선택해주시기 바랍니다.");

		$_ohp = tel_format($_ohtel); $_ohp = explode('-',$_ohp); $_ohtel1 = $_ohp[0]; $_ohtel2 = $_ohp[1]; $_ohtel3 = $_ohp[2];
		$_rhp = tel_format($_rhtel); $_rhp = explode('-',$_rhp); $_rhtel1 = $_rhp[0]; $_rhtel2 = $_rhp[1]; $_rhtel3 = $_rhp[2];
		$_uhp = tel_format($_uhtel); $_uhp = explode('-',$_uhp); $_uhtel1 = $_uhp[0]; $_uhtel2 = $_uhp[1]; $_uhtel3 = $_uhp[2];

		// -- 변수 준비 ---
		$_ordernum					= shop_ordernum_create();//주문번호 생성 예) 12345-23456-34567
		$_mid						= ( is_login() ? get_userid() : $_COOKIE["AuthShopCOOKIEID"] );//회원아이디, 비회원일 경우 쿠키정보 입력
		$_price_real				= $price_total;// 실제결제해야할 금액
		$_price_total				= $price_sum;// 구매총액
		$_price_delivery			= $price_delivery; //배송비
		$_price_supplypoint			= $app_point;//제공해야할 포인트
		$_price_usepoint			= $_use_point;//포인트사용액
		$_price_coupon_member		= $use_coupon_price_member;//보너스쿠폰사용액
		$_price_coupon_product		= $use_coupon_price_product;//상품쿠폰사용액
		$_price_promotion			= $use_promotion_price;//프로모션코드 할인금액 LMH005
		$_price_sale_total			= $_price_usepoint + $_price_coupon_member + $_price_coupon_product;
		$_price_sale_total			= $_price_sale_total + $_price_promotion;//프로모션코드 할인금액 추가 LMH005
		$_paymethod					= $_paymethod;//결제방식
		$_paystatus					= "N";//결제상태
		$_canceled					= "N";//결제취소상태
		$_status					= "결제대기";//주문상태
		$_get_tax					= $_get_tax;	// 현금영수증
		$_paydate					= explode("-",$paydate); // 입금예정일
		$_paybankname				= $_bank; // 입금은행정보
		$_order_type				= $order_type;

		$_price_supplypoint			= is_login() ? $_price_supplypoint : 0; // 비회원일 경우 적립금 없음


		// 포인트 사용량 체크 LMH005
		if( $_price_usepoint > 0 && $_price_usepoint > $row_member['point'] ) { error_loc_msg("/?pn=shop.order.form","소유한 포인트보다 사용포인트량이 많습니다."); }

		// 품절 체크
		$is_soldout= _MQ_result("select min(p.stock) from odtCart as c left join odtProduct as p on (p.code = c.c_pcode) where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y' ");// 선택 구매 2015-12-04 LDD
		if($is_soldout < 1) { error_loc_msg('/','해당 상품은 품절되었습니다.'); }

		// 모바일 주문 체크
		require_once dirname(__FILE__)."/../../include/Mobile_Detect/Mobile_Detect.php";
		$detect = new Mobile_Detect; $mobile_order = $detect->isMobile() ? "Y" : "N";

		// -- odtOrder 입력 ---
		$sque = "
			insert odtOrder set
				ordernum			= '". $_ordernum ."'
				, orderid			= '". $_mid ."'
				, ordername			= '". $_oname ."'
				, orderhtel1		= '". $_ohtel1 ."'
				, orderhtel2		= '". $_ohtel2 ."'
				, orderhtel3		= '". $_ohtel3 ."'
				, orderemail		= '". $_oemail ."'
				, username			= '". $_uname ."'
				, userhtel1			= '". $_uhtel1 ."'
				, userhtel2			= '". $_uhtel2 ."'
				, userhtel3			= '". $_uhtel3 ."'
				, useremail			= '". $_uemail ."'
				, recname			= '". $_rname ."'
				, rechtel1			= '". $_rhtel1 ."'
				, rechtel2			= '". $_rhtel2 ."'
				, rechtel3			= '". $_rhtel3 ."'
				, recemail			= '". $_remail ."'
				, reczip1			= '". $_rzip1 ."'
				, reczip2			= '". $_rzip2 ."'
				, reczonecode		= '". $_rzonecode ."'
				, recaddress		= '". $_raddress ."'
				, recaddress1		= '". $_raddress1 ."'
				, recaddress_doro	= '". $_raddress_doro ."'
				, comment			= '". $_content ."'
				, sPrice			= '". $_price_sale_total ."'
				, tPrice			= '". $_price_real ."'
				, dPrice			= '". $_price_delivery ."'
				, gPrice			= '". $_price_usepoint."'
				, gGetPrice			= '". $_price_supplypoint."'
				, pointed			= 'N'
				, orderstep			= 'finish'
				, taxorder			= '". $_get_tax. "'
				, paymethod			= '". $arr_paymethod[$_paymethod] ."'
				, paystatus			= '". $_paystatus."'
				, order_type		= '". $_order_type."'
				, orderstatus		= 'Y'
				, paybankname		= '". $_paybankname."'
				, payname			= '". $_deposit."'
				, paydatey			= '". $_paydate[0]."'
				, paydatem			= '". $_paydate[1]."'
				, paydated			= '". $_paydate[2]."'
				, orderdate			= now()
				, apply_point		= 'N'
				, mobile			= '".$mobile_order."'
				, member_type		= '". ($row_member['id'] ? "member" : "guest") ."'
				, delivery_date		= '".$delivery_date."'
		"; // LDD018 (, delivery_date		= '".$delivery_date."')
		// 프로모션코드 사용했다면 저장 LMH005
		if($_price_promotion > 0) {
			$sque .= " , o_promotion_code = '".$promotion_code."', o_promotion_price = '".$_price_promotion."' ";
		}

		_MQ_noreturn($sque);


		// -- odtOrderProduct 입력 ---
		$arr_tmp_delivery_price = array(); // 배송비 처리 배열
		$sres = _MQ_assoc(" select c.*,p.name,p.setup_delivery,p.customerCode , p.comSaleType, p.commission from odtCart as c left join odtProduct as p on (p.code = c.c_pcode) where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y' order by c_is_addoption = 'Y' asc, c_rdate asc ");// 선택 구매 2015-12-04 LDD
		foreach( $sres as $k=>$v ){

			$ssque = "
				insert odtOrderProduct set
					op_oordernum			= '". $_ordernum ."'
					, op_pcode				= '". $v['c_pcode'] ."'
					, op_pname				= '". addslashes($v['name']) ."'
					, op_partnerCode		= '". $v['customerCode']."'
					, op_pouid				= '". $v['c_pouid']."'
					, op_option1			= '". addslashes($v['c_option1']) ."'
					, op_option2			= '". addslashes($v['c_option2']) ."'
					, op_option3			= '". addslashes($v['c_option3']) ."'
					, op_delivery_price		= '". ($arr_tmp_delivery_price["dp_".$v['c_pcode']] > 0 ? 0 : $product_delivery_price[$v['c_pcode']])."'
					, op_add_delivery_price	= '". ($arr_tmp_delivery_price["add_dp_".$v['c_pcode']] > 0 || $v['c_is_addoption']=='Y' ? 0 : $product_add_delivery_price[$v['c_pcode']])."'

					, op_pprice				= '". $v['c_price'] ."'
					, op_supply_price		= '". $v['c_supply_price'] ."'
					, op_poptionprice		= '". $v['c_optionprice'] ."'
					, op_poptionpurprice	= '". $v['c_supply_optionprice'] ."'

					, op_point				= '". $v['c_point'] ."'
					, op_cnt				= '". $v['c_cnt'] ."'
					, op_orderproduct_type	= '". ($v['setup_delivery'] == "Y" ? "product" : "coupon") ."'
					, op_is_addoption		= '". $v['c_is_addoption'] ."'
					, op_addoption_parent	= '". $v['c_addoption_parent'] ."'
					, op_delivstatus		= 'N'
					, op_comSaleType		= '". $v['comSaleType'] ."'
					, op_commission			= '". $v['commission'] ."'
			";
			_MQ_noreturn($ssque);

			// 배송비 상품당 1회 적용
			if($product_delivery_price[$v['c_pcode']] > 0 ) $arr_tmp_delivery_price["dp_".$v['c_pcode']] ++;
			if($product_add_delivery_price[$v['c_pcode']] > 0 ) $arr_tmp_delivery_price["add_dp_".$v['c_pcode']] ++;
		}
		_MQ_noreturn(" delete from odtCart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y' ");// 장바구니 정보 삭제// 선택 구매 2015-12-04 LDD
		// -- odtOrderProduct 입력 ---

		// -- odtOrderCouponLog 입력 ---
		if($use_coupon_member) { // 사용자 쿠폰을 사용하였다면,
			$coupon_info = _MQ("select * from odtCoupon where coNo = '".$use_coupon_member."'");
			_MQ_noreturn("insert into odtOrderCouponLog set
							cl_type			= 'member',
							cl_title		= '".$coupon_info['coName']."',
							cl_price		= '".$coupon_info['coPrice']."',
							cl_oordernum	= '".$_ordernum."',
							cl_coNo			= '".$coupon_info['coNo']."',
							cl_pcode		= '',
							cl_rdate		= now()");
		}

		if(sizeof($product_coupon) > 0) { // 상품 쿠폰을 사용하였다면,
			foreach($product_coupon as $coupon_pcode => $coupon_price) {
			$cl_title = _MQ_result("select concat(coupon_title,' ',coupon_price,'%') from odtProduct where code = '".$coupon_pcode."'");
			_MQ_noreturn("insert into odtOrderCouponLog set
							cl_type			= 'product',
							cl_title		= '".$cl_title."',
							cl_price		= '".$coupon_price."',
							cl_oordernum	= '".$_ordernum."',
							cl_coNo			= '',
							cl_pcode		= '".$coupon_pcode."',
							cl_rdate		= now()");
			}
		}
		// -- // odtOrderCouponLog 입력 ---

		// -- 주문상품의 op_usepoint 적용 : 할인액이 있을 경우에만 적용 ---
		// 변수확인1 . _price_sale_total - 총할인액
		// 변수확인1 . _ordernum - 주문번호
		include_once(dirname(__FILE__)."/shop.order.usepoint.php");

		// 메일발송
		// 무통장입금 : 입금요청 메일
		// 전액포인트	:	결제 성공 메일
		// 카드/이체는 order.result_pro.php 에서 처리
		switch($_paymethod) {
			case "online" :

				// 제공변수 : $_ordernum  2015-11-04 LDD - 무통장 쿠폰 패치
				include_once($_SERVER['DOCUMENT_ROOT']."/pages/shop.order.pointadd_pro.php");

				// 제휴마케팅 처리
				//$_ordernum = $_ordernum
				include_once(dirname(__FILE__)."/shop.order.aff_marketing_pro.php");

				if( mailCheck($_oemail) ){
					// $_ordernum ==> 주문번호
					$_type = "online"; // 결제확인처리
					include(dirname(__FILE__)."/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
					$_title = "[".$row_setup['site_name']."] 무통장 결제를 하셨습니다.";
					$_title_content = '
					<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">고객님이 주문하신 내역입니다.</strong><br />
					기한내에 입금해주시면 주문이 완료됩니다.
					';
					$_content = $mailing_app_content;
					$_content = get_mail_content($_title, $_title_content, $_content);
					mailer( $_oemail , $_title , $_content );
				}

				/*-------------- 문자 발송 ---------------*/
				$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				$smskbn = "online_mem";	// 문자 발송 유형
				$SMSProduct = get_order_product_info($_ordernum); // 2016-07-19 LDD
				$SMSProductCnt = sizeof($SMSProduct);
				$SMSProduct = $SMSProduct[0]; // 2016-07-19 LDD
				if($row_sms[$smskbn]['smschk'] == "y") {
					$sms_to		= phone_print($_ohtel1,$_ohtel2,$_ohtel3);
					$sms_from	= $row_company['tel'];

					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
					// 치환작업
					$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $_ordernum, array(
						'{{주문번호}}'     => $_ordernum,
						'{{결제금액}}' => number_format($_price_real),
						'{{입금계좌정보}}' => $_paybankname,
						'{{사이트명}}' => $row_setup['site_name'],
						'{{주문상품명}}' => $SMSProduct['op_pname'],
						'{{주문상품수}}' => $SMSProductCnt,
					));
					$sms_msg = $arr_sms_msg['msg'];
					$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

				}

				$smskbn = "online_adm";	// 문자 발송 유형
				if($row_sms[$smskbn]['smschk'] == "y") {
					$sms_to		= $row_company['htel'];
					$sms_from	= $row_company['tel'];

					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
					// 치환작업
					$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], $_ordernum); // LDD008
					$sms_msg = $arr_sms_msg['msg'];
					$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
					//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

				}
				//onedaynet_sms_multisend($arr_send);
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				onedaynet_alimtalk_multisend($arr_send);
				/*-------------- // 문자 발송 ---------------*/


				break;

			case "point" :	// 전액 포인트 결제

				// 제공변수 : $_ordernum
				include_once(dirname(__FILE__)."/shop.order.pointadd_pro.php");

				// 상품 재고 차감 및 판매량 증가
				$_ordernum = $_ordernum;
				include_once(dirname(__FILE__)."/shop.order.salecntadd_pro.php");

				// 결제상태 Y로 변경
				_MQ_noreturn("update odtOrder set paystatus='Y' , paydate = now() where ordernum ='".$_ordernum."' and paymethod='G' and paystatus='N' and canceled='N' and tPrice = 0");

				// 쿠폰상품은 티켓을 발행한다.
				// 제공변수 : $_ordernum
				$_ordernum = $_ordernum;
				include_once(dirname(__FILE__)."/shop.order.couponadd_pro.php");

				// 결제완료 문자발송
				$_ordernum = $_ordernum;
				include_once(dirname(__FILE__)."/shop.order.sms_send.php");


				if( mailCheck($_oemail) ){
					// $_ordernum ==> 주문번호
					$_type = "card"; // 결제확인처리
					include(dirname(__FILE__)."/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
					$_title = "[".$row_setup['site_name']."] 주문하신 상품의 결제가 성공적으로 완료되었습니다.";
					$_title_content = '<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">고객님이 주문하신 내역입니다.</strong>';
					$_content = $mailing_app_content;
					$_content = get_mail_content($_title, $_title_content, $_content);
					mailer( $_oemail , $_title , $_content );

					if( in_array($_order_type , array("coupon" , "both"))) {
						$_type = "coupon"; // 쿠폰발송
						include(dirname(__FILE__)."/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
						$_title = "[".$row_setup['site_name']."] 주문하신 상품의 쿠폰이 발송되었습니다.";
						$_title_content = '
						<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">고객님</strong>께서 주문하신 쿠폰이 발송되었습니다. <br />
						저희 사이트에서 구매해주셔서 감사합니다. 보다 나은 상품과 큰 만족을 위해 최선을 다하겠습니다.
						';
						$_content = $mailing_app_content;
						$_content = get_mail_content($_title, $_title_content, $_content);
						mailer( $_oemail , $_title , $_content );
					}
				}

				break;

		}

		// -- 주문서 저장 후 세션파괴 ::: 재등록 막기 ---
		$_SESSION["order_start"] = "";
		session_destroy();
		session_start();

		if(substr(phpversion(),0,3) < 5.4) { session_register("session_ordernum"); }
		$_SESSION["session_ordernum"] = $_ordernum;//주문번호

	}
	// - 주문서 저장 ---------------------------------------------

	// 주문상태 업데이트
	order_status_update($_ordernum);

	// PG 연동시 중간처리 -> order.result.php
	// 무통장입금시  -> order.complete.php

	if( $_paymethod == "online" || $_paymethod == "point") { error_loc("/?pn=shop.order.complete"); }
	else { error_loc("/?pn=shop.order.result"); }
?>