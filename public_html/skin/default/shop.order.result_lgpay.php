<?
			/*
			 * [결제 인증요청 페이지(STEP2-1)]
			 *
			 * 샘플페이지에서는 기본 파라미터만 예시되어 있으며, 별도로 필요하신 파라미터는 연동메뉴얼을 참고하시어 추가 하시기 바랍니다.     
			 */

			/*
			 * 1. 기본결제 인증요청 정보 변경
			 * 
			 * 기본정보를 변경하여 주시기 바랍니다.(파라미터 전달시 POST를 사용하세요)
			 */
			$CST_PLATFORM               = $row_setup[P_MODE];// LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
			$CST_MID                    = $row_setup[P_ID];// 상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
																																					//테스트 아이디는 't'를 반드시 제외하고 입력하세요.
			$LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;  //상점아이디(자동생성)
			$LGD_OID                    = $ordernum ;           //주문번호(상점정의 유니크한 주문번호를 입력하세요)
			$LGD_AMOUNT                 = $r[tPrice] ;        //결제금액("," 를 제외한 결제금액을 입력하세요)
			$LGD_BUYER                  = $r[ordername];         //구매자명
			$LGD_PRODUCTINFO            = $app_product_name;   //상품명
			$LGD_BUYEREMAIL             = $r[orderemail];    //구매자 이메일

			$LGD_RECEIVER 				= $r[recname];    // 수취인
			$LGD_RECEIVERPHONE	 		= phone_print($r[rechtel1],$r[rechtel2],$r[rechtel3]);    // 수취인 전화번호
			$LGD_DELIVERYINFO 			= "(".$r[reczip1]."-".$r[reczip2].")" . $r[recaddress]. " " . $r[recaddress1];    // 배송정보

			$LGD_TIMESTAMP              = date(YmdHms);                         //타임스탬프
			$LGD_CUSTOM_SKIN            = "blue";                               //상점정의 결제창 스킨 (red, blue, cyan, green, yellow)
			//$LGD_CUSTOM_USABLEPAY 		= ( $r[paymethod] == "L" ? "SC0030" : "SC0010"); // 신용카드, 계좌이체만 적용
		/*
			신용카드 - SC0010
			계좌이체 -SC0030
			가상계좌-SC0040
			핸드폰-SC0060
		*/

			// 에스크로 설정
			$LGD_ESCROW_USEYN			= 'Y'; // 에스크로 사용 여부 
			$LGD_ESCROW_ZIPCODE			= $r[reczip1].'-'.$r[reczip2]; // 에스크로 배송지 우편번호
			$LGD_ESCROW_ADDRESS1		= $r[recaddress]; // 에스크로 배송지 주소 1 (동까지)
			$LGD_ESCROW_ADDRESS2		= $r[recaddress1]; // 에스크로 배송지 주소 2 (나머지)
			$LGD_ESCROW_BUYERPHONE		= $r[orderhtel1].'-'.$r[orderhtel2].'-'.$r[orderhtel3]; // 에스크로 구매자 휴대폰번호

			$paymethod = array(
				'L'=>'SC0030',
				'C'=>'SC0010',
				'V'=>'SC0040',
				'B'=>'SC0100'
			);

			$LGD_CUSTOM_USABLEPAY = $paymethod[$r[paymethod]];

			$LGD_MERTKEY				= $row_setup[P_PW];    //상점MertKey(mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
			$configPath 				= PG_DIR."/lgpay/lgdacom"; 						//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.
			$LGD_BUYERID                = $r[orderid];       //구매자 아이디
			$LGD_BUYERIP                = $_SERVER["REMOTE_ADDR"];       //구매자IP

			/*
			 * 가상계좌(무통장) 결제 연동을 하시는 경우 아래 LGD_CASNOTEURL 을 설정하여 주시기 바랍니다. 
			 */    
			$LGD_CASNOTEURL				= "http://".$_SERVER[HTTP_HOST]."/pages/shop.order.result_lgpay_casnoteurl.php";
			
			/*
			 *************************************************
			 * 2. MD5 해쉬암호화 (수정하지 마세요) - BEGIN
			 * 
			 * MD5 해쉬암호화는 거래 위변조를 막기위한 방법입니다. 
			 *************************************************
			 *
			 * 해쉬 암호화 적용( LGD_MID + LGD_OID + LGD_AMOUNT + LGD_TIMESTAMP + LGD_MERTKEY )
			 * LGD_MID          : 상점아이디
			 * LGD_OID          : 주문번호
			 * LGD_AMOUNT       : 금액
			 * LGD_TIMESTAMP    : 타임스탬프
			 * LGD_MERTKEY      : 상점MertKey (mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
			 *
			 * MD5 해쉬데이터 암호화 검증을 위해
			 * LG유플러스에서 발급한 상점키(MertKey)를 환경설정 파일(./lgpay/lgdacom/conf/mall.conf)에 반드시 입력하여 주시기 바랍니다.
			 */
			$xpay = &new XPayClient($configPath, $LGD_PLATFORM);
			$xpay->Init_TX($LGD_MID);
			$LGD_HASHDATA = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_TIMESTAMP.$xpay->config[$LGD_MID]);

			$LGD_CUSTOM_PROCESSTYPE = "TWOTR";
			/*
			 *************************************************
			 * 2. MD5 해쉬암호화 (수정하지 마세요) - END
			 *************************************************
			 */
	?>
	<script language="javascript" src="http://xpay.uplus.co.kr/xpay/js/xpay_install.js" type="text/javascript"></script>
	<script type="text/javascript">
	<!--
	/*
	 * 상점결제 인증요청후 PAYKEY를 받아서 최종결제 요청.
	 */
	function doPay_ActiveX(){
			ret = xpay_check(document.getElementById('LGD_PAYINFO'), '<?= $CST_PLATFORM ?>');

			if (ret=="00"){     //ActiveX 로딩 성공
					var LGD_RESPCODE        = dpop.getData('LGD_RESPCODE');       //결과코드
					var LGD_RESPMSG         = dpop.getData('LGD_RESPMSG');        //결과메세지

					if( "0000" == LGD_RESPCODE ) { //인증성공
							var LGD_PAYKEY      = dpop.getData('LGD_PAYKEY');         //LG유플러스 인증KEY
							var msg = "인증결과 : " + LGD_RESPMSG + "\n";
							msg += "LGD_PAYKEY : " + LGD_PAYKEY +"\n\n";
							document.getElementById('LGD_PAYKEY').value = LGD_PAYKEY;
							//alert(msg);
							document.getElementById('LGD_PAYINFO').submit();
					} else { //인증실패
							alert("인증이 실패하였습니다. " + LGD_RESPMSG);
							/*
							 * 인증실패 화면 처리
							 */
					}
			} else {
					alert("LG U+ 전자결제를 위한 플러그인 모듈이 설치되지 않았습니다.");
					/*
					 * 인증실패 화면 처리
					 */
			}
	}

	function isPluginOK(){
		if(hasXpayObject()) {
			//alert('LG U+ 전자결제를 위한 XPayClient (Plugin) 이 설치되었습니다. ');
		}else {
			//xpayShowInstall();
		}
	}
	//-->
	</script>

	<body onload='javascript:isPluginOK();'>
	<form name=frm method=post id="LGD_PAYINFO" action="/pages/shop.order.result_lgpay.pro.php" target="common_frame">
	<input type="hidden" name="CST_PLATFORM"                value="<?= $CST_PLATFORM ?>">                   <!-- 테스트, 서비스 구분 -->
	<input type="hidden" name="CST_MID"                     value="<?= $CST_MID ?>">                        <!-- 상점아이디 -->
	<input type="hidden" name="LGD_MID"                     value="<?= $LGD_MID ?>">                        <!-- 상점아이디 -->
	<input type="hidden" name="LGD_OID"                     value="<?= $LGD_OID ?>">                        <!-- 주문번호 -->
	<input type="hidden" name="LGD_BUYER"                   value="<?= $LGD_BUYER ?>">           			<!-- 구매자 -->
	<input type="hidden" name="LGD_PRODUCTINFO"             value="<?= $LGD_PRODUCTINFO ?>">     			<!-- 상품정보 -->
	<input type="hidden" name="LGD_AMOUNT"                  value="<?= $LGD_AMOUNT ?>">                     <!-- 결제금액 -->
	<input type="hidden" name="LGD_BUYEREMAIL"              value="<?= $LGD_BUYEREMAIL ?>">                 <!-- 구매자 이메일 -->
	<input type="hidden" name="LGD_CUSTOM_SKIN"             value="<?= $LGD_CUSTOM_SKIN ?>">                <!-- 결제창 SKIN -->
	<input type="hidden" name="LGD_CUSTOM_USABLEPAY"        value="<?= $LGD_CUSTOM_USABLEPAY ?>"> <!-- 신용카드, 계좌이체만 사용 -->
	<input type="hidden" name="LGD_CUSTOM_PROCESSTYPE"      value="<?= $LGD_CUSTOM_PROCESSTYPE ?>">         <!-- 트랜잭션 처리방식 -->
	<input type="hidden" name="LGD_TIMESTAMP"               value="<?= $LGD_TIMESTAMP ?>">                  <!-- 타임스탬프 -->
	<input type="hidden" name="LGD_HASHDATA"                value="<?= $LGD_HASHDATA ?>">                   <!-- MD5 해쉬암호값 -->
	<input type="hidden" name="LGD_PAYKEY"                  id="LGD_PAYKEY">                                <!-- LG유플러스 PAYKEY(인증후 자동셋팅)-->
	<input type="hidden" name="LGD_VERSION"         		value="PHP_XPay_1.0">							<!-- 버전정보 (삭제하지 마세요) -->
	<input type="hidden" name="LGD_BUYERIP"                 value="<?= $LGD_BUYERIP ?>">           			<!-- 구매자IP -->
	<input type="hidden" name="LGD_BUYERID"                 value="<?= $LGD_BUYERID ?>">           			<!-- 구매자ID -->
	<!-- 가상계좌(무통장) 결제연동을 하시는 경우  할당/입금 결과를 통보받기 위해 반드시 LGD_CASNOTEURL 정보를 LG 유플러스에 전송해야 합니다 . -->
	<input type="hidden" name="LGD_CASNOTEURL"          	value="<?= $LGD_CASNOTEURL ?>">			<!-- 가상계좌 NOTEURL -->  
	<input type="hidden" name="LGD_CASHRECEIPTYN"          	value="Y">										<!-- 계좌이체/무통장 결제시 현금영수증 사용여부 -->  
	
	<input type="hidden" name="LGD_ESCROW_USEYN"          	value="<?= $LGD_ESCROW_USEYN ?>">			<!-- 에스크로 사용여부 -->  
	<input type="hidden" name="LGD_ESCROW_ZIPCODE"          	value="<?= $LGD_ESCROW_ZIPCODE ?>">			<!-- 에스크로 배송지 우편번호 -->  
	<input type="hidden" name="LGD_ESCROW_ADDRESS1"          	value="<?= $LGD_ESCROW_ADDRESS1 ?>">			<!-- 에스크로 배송지 주소 1 -->  
	<input type="hidden" name="LGD_ESCROW_ADDRESS2"          	value="<?= $LGD_ESCROW_ADDRESS2 ?>">			<!-- 에스크로 배송지 주소 2 -->  
	<input type="hidden" name="LGD_ESCROW_BUYERPHONE"          	value="<?= $LGD_ESCROW_BUYERPHONE ?>">			<!-- 에스크로 구매자 휴대폰번호 -->  
	<? foreach($sr as $v) { ?>
	<input type="hidden" name="LGD_ESCROW_GOODID"                value="<?= $v[op_pcode] ?>">                   <!-- 에스크로 상품번호 -->
	<input type="hidden" name="LGD_ESCROW_GOODNAME"                value="<?= $v[name] ?>">                   <!-- 에스크로 상품명 -->
	<input type="hidden" name="LGD_ESCROW_GOODCODE"                value="<?= $v[op_pcode] ?>">                   <!-- 에스크로 상품코드 -->
	<input type="hidden" name="LGD_ESCROW_UNITPRICE"                value="<?= ($v[op_pprice] + $v[op_poptionprice]) ?>">                   <!-- 에스크로 상품금액 -->
	<input type="hidden" name="LGD_ESCROW_QUANTITY"                value="<?= $v[op_cnt] ?>">                   <!-- 에스크로 상품수량 -->
	<? } ?>

	<input type="hidden" name="LGD_RECEIVER" value="<?=$LGD_RECEIVER?>"> <!--수취인 -->
	<input type="hidden" name="LGD_RECEIVERPHONE" value="<?=$LGD_RECEIVERPHONE?>"> <!-- 수취인 전화번호 -->
	<input type="hidden" name="LGD_DELIVERYINFO" value="<?=$LGD_DELIVERYINFO?>"> <!-- 배송정보-->

	</form>
