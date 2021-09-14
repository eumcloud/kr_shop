<?PHP

	$array_week = array("일","월","화","수","목","금","토");

	$arrDepth1CategoryCode = array('지역'=>1,'쇼핑'=>2,'여행/레저'=>3,'문화'=>96,'기획전'=>97);
	$arrDepth1CategoryUrl = array('1'=>"local",'2'=>"shopping",'3'=>"tour",'96'=>"culture",'97'=>"promotion");

	$arrProductListTypePc = array("type1"=>"한줄당 3개노출", "type2"=>"한줄당 2개노출");
	$arrProductListTypeMoblie = array("type1"=>"썸네일형", "type2"=>"리스트형", "type3"=>"여행/레저형");

	// - 배너위치 ---
	$arr_banner_loc = array(
		'info_logo'=>'========== 로고 및 아이콘 ==========',
		'site_top_logo'=>'사이트 탑 로고 (204 x 35)',
		'site_footer_logo'=>'사이트 푸터 로고 (220 x 57)',
		'mobile_site_top_logo'=>'모바일 사이트 탑 로고 (370 x 64)',
		'site_icon_basic'=>'사이트 기본 아이콘 (114 x 114)',
	);
	// - 배너위치 ---

	// - 상품배너위치 ---
	$arr_product_banner_loc = array();
	/*$arr_product_banner_loc = array(
		",big"=>'(PC)큰비주얼배너(743 x 353)',
		",smalltop"=>'(PC)작은비주얼배너상단(225 x 172)',
		",smallbottom"=>'(PC)작은비주얼배너하단(225 x 172)',
		",mobile"=>'(Mobile)큰비주얼배너(565 x 291)',
	);*/
	// - 상품배너위치 ---


	// - 추가 섬 값 ::: 로그인 등 랜덤 string ---
	$addSum = "dnjsepdlspt"; // 과거 rand_num() 함수 이용


	// - 문의하기 종류 ---
	$arrRequestMenu = array(
		'request' => '1:1문의',
		'partner' => '제휴문의',
	);


	// - 게시판 종류 ---
	$arrBoardMenu = array(
		"notice"=>"공지사항",
        "event"=>"이벤트",
        "faq"=>"자주묻는질문",
	);

	// - 게시판 유형 ---
	$arrBoardType = array(
		'board' => '일반게시판',
		'event' => '이벤트(목록)',
		'event_thumb' => '이벤트(썸네일)',
		'faq' => '자주묻는질문',
		'gallery' => '갤러리',
		'news' => '뉴스',
		'qna' => '질문답변'
	);

	// 게시판 카테고리
	$arr_board_category = array(
		"faq" => array("사이트이용","회원관련","기타")
	);


	// 문자발송 멘트를 설정하기 위한 유형
	//	Merge Striing {{회원명}}, {{주문번호}}, {{구매자명}}, {{결제금액}}, {{주문상품명}}, {{주문상품수}}, {{쿠폰번호}}, {{택배사명}}, {{송장번호}}, {{상품토크}}
	$arr_sms_text_type = array(
		"memjoin"			=>"회원가입시",
		"order_mem"			=>"주문완료시",
		"order_adm"			=>"주문완료시 (관리자)",
		"online_mem"		=>"무통장주문시",
		"online_adm"		=>"무통장주문시 (관리자)",
		"virtual_mem"		=>"가상계좌주문시",
		"virtual_adm"		=>"가상계좌주문시 (관리자)",
		"payconfirm_mem"	=>"입금확인시",
		"express"			=>"배송상품 발송시",
		"coupon"			=>"쿠폰상품 발급시",
		"cancel"            =>"주문취소시",
		"cancel_part"           =>"부분취소시",
		"talk"				=>"상품문의 등록시 (관리자)",//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		"talk_re"			=>"상품문의 답변시",
		"coupon_use"		=>"쿠폰사용시",
		"coupon_unuse"		=>"쿠폰사용취소시",
		"return_mem"		=>"교환/반품 신청시",
		"return_adm"		=>"교환/반품 신청시 (관리자)",
		"return_re_mem"		=>"교환/반품 답변시",
        "2year_opt"     =>"매2년마다 수신동의",
	);

	// 결제방식 유형.
	$arr_paymethod = array("card" => "C", "iche" => "L", "online" => "B", "point" => "G" ,"virtual" => "V");

	// 결제방식 유형.
	$arr_paymethod_name = array("C" => "카드결제", "L" => "실시간 계좌이체", "B" => "무통장 입금", "G" => "전액 포인트 결제", "V" => "가상계좌");

	// 결제방식 유형
	$arr_paymethod2 = array("카드결제" => "card" , "실시간 계좌이체" => "iche", "가상계좌"=>"virtual", "무통장 입금" => "online");
	$arr_paymethod2_mobile = array("카드결제" => "card" , "가상계좌"=>"virtual", "무통장 입금" => "online");

	// 결제방식 유형
	$arr_payment_type = array("card" => "카드결제" , "iche" => "실시간 계좌이체", "virtual"=>"가상계좌", "online" => "무통장 입금");

	// - PG사 정보 ---
	$arr_pg_type = array(
		'I'=>"이니시스",
		//'A'=>"올더게이트",
		'K'=>"KCP",
		'L'=>"토스페이먼츠",
		//'M'=>"인포뱅크",
		// 'B'=>"빌게이트", // LCY : 2021-01-22 : 빌게이트 사용안함
        'D'=>"페이조아"
	);
	// - PG사 정보 ---

	// - 주문 컴플레인 상태 ---
	$arr_order_complain = array('교환/반품신청' , '교환/반품완료' );
	$arr_order_moneyback = array('환불요청' , '환불완료' );
	// - 주문 컴플레인 상태 ---

	// 택배사 정보
	$array_delivery = array('우체국택배','롯데(현대)택배','한진택배','KGB택배','대한통운','로젠택배','삼성택배','KG옐로우캡','KGLogis','CJ택배','하나로택배','동부익스프레스','화물배송');

    // - 택배사정보 ---
    $arr_delivery_company = array(
         "CJ대한통운택배"=>"https://www.doortodoor.co.kr/parcel/doortodoor.do?fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT&invc_no="
        ,"드림택배"=>"http://www.idreamlogis.com/delivery/delivery_result.jsp?item_no="
        ,"우체국EMS"=>"http://service.epost.go.kr/trace.RetrieveEmsTrace.postal?ems_gubun=E&POST_CODE="
        ,"우체국등기"=>"http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1="
        ,"우체국택배"=>"http://service.epost.go.kr/trace.RetrieveRegiPrclDelivTibco.postal?sid1="
		,"한진택배"=>"https://www.hanjin.co.kr/kor/CMS/DeliveryMgr/WaybillResult.do?mCode=MN038&schLang=KR&wblnumText2="
        ,"롯데택배"=>"https://www.lotteglogis.com/home/reservation/tracking/linkView?InvNo="
        //,"로젠택배"=>"http://d2d.ilogen.com/d2d/delivery/invoice_tracesearch_quick.jsp?slipno="
        ,"로젠택배"=>"https://www.ilogen.com/web/personal/trace/"
        ,"KG로지스"=>"http://www.kglogis.co.kr/contents/waybill.jsp?item_no=" // 드림택배로 통합
        ,"CVSnet"=>"http://www.doortodoor.co.kr/jsp/cmn/TrackingCVS.jsp?pTdNo="
        ,"CU 편의점택배"=>"https://www.cupost.co.kr/postbox/delivery/localResult.cupost?invoice_no="
        ,"KGB택배"=>"#"
        ,"경동택배"=>"http://kdexp.com/basicNewDelivery.kd?barcode="
        ,"대신택배"=>"https://www.ds3211.co.kr/freight/internalFreightSearch.ht?billno="
        ,"일양택배"=>"http://www.ilyanglogis.com/functionality/tracking_result.asp?hawb_no="
        ,"합동택배"=>"http://www.hdexp.co.kr/basic_delivery.hd?barcode="
        ,"GTX로지스"=>"http://www.gtxlogis.co.kr/tracking/default.asp?awblno="
        ,"건영택배"=>"http://www.kunyoung.com/goods/goods_01.php?mulno="
        ,"천일택배"=>"http://www.chunil.co.kr/HTrace/HTrace.jsp?transNo="
        ,"한의사랑택배"=>"http://www.hanips.com/html/sub03_03_1.html?logicnum="
        ,"한덱스"=>"http://www.hanjin.co.kr/Logistics_html#"
        ,"DHL"=>"http://www.dhl.co.kr/content/kr/ko/express/tracking.shtml?brand=DHL&AWB="
        ,"TNT Express"=>"http://www.tnt.com/webtracker/tracking.do?respCountry=kr&respLang=ko&searchType=CON&cons="
        ,"UPS"=>"https://wwwapps.ups.com/WebTracking/track?track=yes&loc=ko_kr&trackNums="
        ,"Fedex"=>"http://www.fedex.com/Tracking?ascend_header=1&clienttype=dotcomreg&cntry_code=kr&language=korean&tracknumbers="
        ,"USPS"=>"https://tools.usps.com/go/TrackConfirmAction?tLabels="
        ,"i-Parcel"=>"https://tracking.i-parcel.com/Home/Index?trackingnumber="
        ,"DHL Global Mail"=>"http://webtrack.dhlglobalmail.com/?trackingnumber="
        ,"범한판토스"=>"http://totprd.pantos.com/jsp/gsi/vm/popup/notLoginTrackingListExpressPoPup.jsp?quickType=HBL_NO&quickNo="
        ,"AirBoyExpress"=>"http://www.airboyexpress.com/tracking/tracking.asp?shipping_number="
        ,"GSMNtoN"=>"http://www.gsmnton.com/gsm/handler/Tracking-OrderList?searchType=TrackNo&trackNo="
        ,"APEX(ECMS Express)"=>"http://www.apexglobe.com/#"
        ,"KGL네트웍스"=>"http://www.hydex.net/ehydex/jsp/home/distribution/tracking/tracingView.jsp?InvNo="
        ,"굿투럭"=>"http://www.goodstoluck.co.kr/#modal"
        ,"호남택배"=>"http://honamlogis.co.kr/#"
        ,"GSI Express"=>"http://www.gsiexpress.com/track_pop.php?track_type=ship_num&query_num="
        ,"SLX로지스"=>"http://slx.co.kr/delivery/delivery_number.php?param1="
        ,"ACI Express"=>"http://www.acieshop.com/pod.html?OrderNo="
        ,"CGM 국제택배"=>"http://idn.inlos.com/CST/CST2/CST2044.aspx?Hawb="
        ,"WIZWA"=>"http://www.wizwa.co.kr/tracking_exec.php?invoice_no="
        ,"고려택배"=>"http://www.klogis.com/main.asp#"
        ,"스피디익스프레스"=>"http://www.speedyexpress.net/tracking_view.php#"
        ,"[자체배송]"=>"#"
    );
    // - 택배사정보 ---


	$arr_o_status_main = array(
	"결제대기" => "<span class='icon_state state_ready'>결제대기</span>",
	"결제확인" => "<span class='icon_state state_pay'>결제확인</span>",
	"발송대기" => "<span class='icon_state state_deliver'>발송대기</span>",
	"발송완료" => "<span class='icon_state state_ok'>발송완료</span>",
	"주문취소" => "<span class='icon_state state_cancel'>주문취소</span>",
	"결제실패" => "<span class='icon_state state_cancel'>결제실패</span>",
	"발급완료" => "<span class='icon_state state_ok'>발급완료</span>",
	);

	$arr_o_status = array(
	"결제대기" => "<span class='gray'>결제대기</span>",
	"결제확인" => "<span class='red'>결제확인</span>",
	"발송대기" => "<span class='green'>발송대기</span>",
	"발송완료" => "<span class='orange'>발송완료</span>",
	"주문취소" => "<span class='light'>주문취소</span>",
	"결제실패" => "<span class='light'>결제실패</span>",
	"발급완료" => "<span class='orange'>발급완료</span>",
	);

	$arr_o_status_mobile = array(
	"결제대기" => "<span class='state state_ready'>결제대기</span>",
	"결제확인" => "<span class='state state_pay'>결제확인</span>",
	"발송대기" => "<span class='state state_delivery'>발송대기</span>",
	"발송완료" => "<span class='state state_get'>발송완료</span>",
	"주문취소" => "<span class='state state_cancel'>주문취소</span>",
	"결제실패" => "<span class='state state_cancel'>결제실패</span>",
	"발급완료" => "<span class='state state_get'>발급완료</span>",
	);


	$arr_adm_button = array(
	"카드결제" => "<span class='shop_state_pack'><span class='red'>카드결제</span></span>",
	"가상계좌" => "<span class='shop_state_pack'><span class='green'>가상계좌</span></span>",
	"무통장입금" => "<span class='shop_state_pack'><span class='green'>무통장입금</span></span>",
	"계좌이체" => "<span class='shop_state_pack'><span class='sky'>계좌이체</span></span>",
	"전액적립금결제" => "<span class='shop_state_pack'><span class='orange'>전액적립금결제</span></span>",

	"결제대기" => "<span class='shop_state_pack'><span class='gray'>결제대기</span></span>",
	"결제완료" => "<span class='shop_state_pack'><span class='orange'>결제완료</span></span>",
	"결제확인" => "<span class='shop_state_pack'><span class='orange'>결제확인</span></span>",

	"현금영수증 요청" => "<span class='shop_state_pack' style='margin-top: 2px; display: inline-block;'><span class='gray'>현금영수증 요청</span></span>",
	"현금영수증 발행" => "<span class='shop_state_pack' style='margin-top: 2px; display: inline-block;'><span style='background: #999;'>현금영수증 발행</span></span>",

	"배송대기" => "<span class='shop_state_pack'><span class='gray'>배송대기</span></span>",
	"발송대기" => "<span class='shop_state_pack'><span class='blue'>발송대기</span></span>",
	"발송완료" => "<span class='shop_state_pack'><span class='purple'>발송완료</span></span>",
	"주문취소" => "<span class='shop_state_pack'><span class='gray'>주문취소</span></span>",
	"결제실패" => "<span class='shop_state_pack'><span class='gray'>결제실패</span></span>",

	"노출" => "<span class='shop_state_pack'><span class='orange'>노출</span></span>",
	"숨김" => "<span class='shop_state_pack'><span class='gray'>숨김</span></span>",

	"수신가능" => "<span class='shop_state_pack'><span class='orange'>수신가능</span></span>",
	"수신거부" => "<span class='shop_state_pack'><span class='gray'>수신거부</span></span>",

	"처리완료" => "<span class='shop_state_pack'><span class='blue'>처리완료</span></span>",
	"적립예정" => "<span class='shop_state_pack'><span class='orange'>적립예정</span></span>",

	"사용" => "<span class='shop_state_pack'><span class='blue'>사용</span></span>",
	"미사용" => "<span class='shop_state_pack'><span class='orange'>미사용</span></span>",

	"공지" => "<span class='shop_state_pack'><span class='orange'>공지</span></span>",

	"미발행" => "<span class='shop_state_pack'><span class='gray'>미발행</span></span>",
	"임시저장" => "<span class='shop_state_pack'><span class='gray'>임시저장</span></span>",
	"세금계산서발행중" => "<span class='shop_state_pack'><span class='purple'>세금계산서발행중</span></span>",
	"발행거부" => "<span class='shop_state_pack'><span class='gray'>발행거부</span></span>",
	"발행취소" => "<span class='shop_state_pack'><span class='gray'>발행취소</span></span>",
	"발행완료" => "<span class='shop_state_pack'><span class='orange'>발행완료</span></span>",

	"발송" => "<span class='shop_state_pack'><span class='orange'>발송</span></span>",
	"미발송" => "<span class='shop_state_pack'><span class='gray'>미발송</span></span>",

	);

	// 상품설명 / 이미지 크기 w,h
	$arr_product_size = array(

		"상품상세"=>array(818 , 0),

		"메인"=>array(480 , 490),
		"정사각형목록"=>array(324 , 330),
		"직사각형목록"=>array(489 , 330), //여행/레져형

		"장바구니" =>array(100 , 100), //정사각형목록을 이용하여 썸네일 적용
		"최근본상품" =>array(85 , 57), //직사각형목록을 이용하여 썸네일 적용
		"주문확인" =>array(170 , 113), //직사각형목록을 이용하여 썸네일 적용

	);



	## 에스크로 시작 - ksnet 은행코드
	$ksnet_bank = array (
		"01" => "한국은행", "02" => "산업은행", "03" => "기업은행", "04" => "국민은행", "05" => "외환은행", "06" => "주택은행", "07" => "수협은행", "08" => "수출입", "09" => "장기신용", "10" => "신농협중앙", "11" => "농협중앙", "12~15" => "농협회원", "16" => "축협중앙", "20" => "우리은행", "21" => "조흥은행", "22" => "상업은행", "23" => "제일은행", "24" => "한일은행", "25" => "서울은행", "26" => "신한은행", "27" => "한미은행", "28" => "동화은행", "29" => "동남은행", "30" => "대동은행", "31" => "대구은행", "32" => "부산은행", "33" => "충청은행", "34" => "광주은행", "35" => "제주은행", "36" => "경기은행", "37" => "전북은행", "38" => "강원은행", "39" => "경남은행", "40" => "충북은행", "53" => "씨티은행", "71" => "우체국", "76" => "신용보증", "81" => "하나은행", "82" => "보람은행", "83" => "평화은행", "93" => "새마을금고");

	$ool_bank_name_array = array('39'=>'경남', '34'=>'광주', '04'=>'국민', '03'=>'기업', '11'=>'농협', '31'=>'대구', '32'=>'부산', '02'=>'산업', '45'=>'새마을금고', '07'=>'수협', '88'=>'신한', '26'=>'신한', '48'=>'신협', '05'=>'외환', '20'=>'우리', '71'=>'우체국', '37'=>'전북', '35'=>'제주', '81'=>'하나', '27'=>'한국씨티', '53'=>'씨티', '23'=>'SC은행', '09'=>'동양증권', '78'=>'신한금융투자증권', '40'=>'삼성증권', '30'=>'미래에셋증권', '43'=>'한국투자증권', '69'=>'한화증권');

	## 에스크로 끝


	// - 상품정보 기본항목 8가지  ---
	$arr_reqinfo_keys = array("제품소개","색상","사이즈","제조사","A/S 책임자와 전화번호","제조국","취급시주의사항","품질보증기준");


	// 상품 아이콘 유형
	$arr_product_icon_type = array(
		'product_name_small_icon' => "상품 이름에 붙는 작은 아이콘",
	);


	// 휴면계정 전환 - 이메일 타이틀명
	$arr_member_sleep_title = "장기 미사용 계정 휴면전환 안내";


	// 교환/반품 LMH008
	$arr_return_type = array("R"=>"반품","E"=>"교환"); // 타입별 명칭
	$arr_return_reason = array("단순변심","상품불량","오배송","기타"); // 사유
	$arr_return_status = array("N"=>"반려","Y"=>"완료","R"=>"대기"); // 상태


	// 바로빌 연동 CERTKEY
	$tax_barobill_certkery_service = "93E19D7C-47E6-421F-B68E-B8BB0AAC46F9"; // 서비스 certkey
	$tax_barobill_certkery_test = "8FE6E4DB-E408-4899-8A5E-57DB781245FA"; // 테스트 certkey

?>