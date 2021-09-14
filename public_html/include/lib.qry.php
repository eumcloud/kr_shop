<?PHP

/*--------- 티켓몰 4.0 솔루션에서  새로추가된 함수 -----------------------------------*/

	// 카테고리 정보 추출한다.
	// 인자 : 카테고리 코드
	// 리턴 : 카테고리 정보(배열)
	function get_category_info($catecode) {
		return _MQ("select * from odtCategory where catecode = '".$catecode."'");
	}

	// 1,2,3차 카테고리 정보를 모두 추출한다.
	// 인자 : 카테고리 코드
	// 리턴 : 1,2,3 카테고리 정보(배열)
	function get_total_category_info($catecode) {
		// 카테고리 정보
		$category_info = get_category_info($catecode);

		switch($category_info[catedepth]) {
			case 3;
				$total_info[depth3_catecode] = $category_info[catecode];
				$total_info[depth3_catename] = $category_info[catename];

				$parent_catecode = end(explode(",",$category_info[parent_catecode]));
				$category_info = get_category_info($parent_catecode);

			case 2;
				$total_info[depth2_catecode] = $category_info[catecode];
				$total_info[depth2_catename] = $category_info[catename];

				$parent_catecode = end(explode(",",$category_info[parent_catecode]));
				$category_info = get_category_info($parent_catecode);

			case 1;
				$total_info[depth1_catecode]			= $category_info[catecode];
				$total_info[depth1_catename]			= $category_info[catename];
				$total_info[depth1_display]				= $category_info[subcate_display];
				$total_info[depth1_lineup]				= $category_info[lineup];
				$total_info[depth1_pc_list_display]		= $category_info[pc_list_display];
				$total_info[depth1_mobile_list_display]	= $category_info[mobile_list_display];

		}

		return $total_info;
	}

    // 카테고리 1depth cuid 를 구한다.
    // 인자 : 카테고리 코드
    // 리턴 : 1depth 카테고리
    function get_1depth_catecode($catecode) {
        $row = _MQ("select parent_catecode,catedepth from odtCategory where catecode = '".$catecode."'");
        if($row[catedepth] == 1)
            return $catecode;
        else
            return reset(explode(",",$row[parent_catecode]));
    }

	// 최근 본상품 정보를 추출한다.
	// 인자 : 없음
	// 리턴 : 최근본 상품 코드 목록
	function get_latest_list() {
		global $_COOKIE;
		$r = _MQ_assoc("select * from odtProductLatest as pl inner join odtProduct as p on (p.code = pl.pl_pcode) where pl_uniqkey='" . $_COOKIE["AuthProductLatest"] . "' order by pl_rdate desc");
		return $r;
	}

	// *** 배너정보 추출 - ***
	//		- loc : 배너위치 - /include/var.php 참조
	//		- limit : 배너추출갯수 - 1개이상
	//		- date_type : 리턴 데이터값 형식 (html : 소스 , data : 일반데이터 )
	//		- <a href=링크 target=타겟>이미지</a> 형태의 배열로 return

	function info_banner($loc , $limit=1, $return_type = 'html'){
		$arr = array();
		$r = _MQ_assoc("select * from odtBanner where b_loc='" . $loc . "' and b_view='Y' and b_sdate <= CURDATE() and b_edate >= CURDATE() order by b_idx asc , b_uid asc limit 0, " . $limit );
		foreach($r as $k=>$v){
			if($return_type == "data" ) {
				$arr[$k] = $v;
			}
			else {
				$arr[$k] = "<img src='".IMG_DIR_BANNER.$v[b_img]."' alt='".stripslashes($v[b_title])."' />";
				if($v[b_link]) $arr[$k] = "<A HREF='".($v[b_link] ? $v[b_link] : "#")."' target='".$v[b_target]."'>".$arr[$k]."</A>";
			}

		}
		return $arr;
	}
	// *** 배너정보 추출 - ***

	// 로그인 체크
	function loginchk_insert($_id , $_type){
		global $_SERVER;
		$que = "insert odtLoginchk set lc_mid='". $_id ."' , lc_type='".$_type."' , lc_ip='".$_SERVER["REMOTE_ADDR"]."' , lc_rdate=now() ";
		_MQ_noreturn($que);
	}


	// 배송비 정보를 추출한다. (상품코드로 부터...)
	// 상품지정값인지, 입점업체지정값인지, 쇼핑몰기본값인지 체크하여 각각 처리한다. (우선순위 : 상품>입점업체>쇼핑몰기본값)
	// 리턴값 : freePrice : 무료배송비,
	//					price : 기본배송비,
	//					from : 배송비정보출처(global : 쇼핑몰기본정보, company : 입점업체정보, product : 상품정보) ,
	//					status : 배송결과값(1:무료배송/2:조건부무료배송/3:무조건배송비부과)
	function get_delivery_info($pcode){

		// 상품정보를 추출한다.
		$p_r = _MQ("select del_use,del_price,del_limit,customerCode from odtProduct where code = '".$pcode."'");

		if($p_r[del_use] == "Y") {		// 상품 배송비 정책을 사용한다.
			$dinfo[price] 		= $p_r[del_price];
			$dinfo[freePrice] 	= $p_r[del_limit];
			$dinfo[from]		= "product";	// 상품
			$dinfo[status] 		= $dinfo[price] == 0 ? "1" : ($dinfo[freePrice] == 0 ? "3" : "2");
		} else
			$dinfo = get_delivery_info_from_company($p_r[customerCode]);		// 입점업체 정보 추출

		return $dinfo;
	}


	// 배송비 정보를 추출한다. (입점업체로 부터...)
	// 상품지정값인지, 입점업체지정값인지, 쇼핑몰기본값인지 체크하여 각각 처리한다. (우선순위 : 상품>입점업체>쇼핑몰기본값)
	// 무료배송비, 기본배송비, 배송비정보출처(global : 쇼핑몰기본정보, company : 입점업체정보, product : 상품정보) , 배송결과값(1:무료배송/2:조건부무료배송/3:무조건배송비부과)
	function get_delivery_info_from_company($cpid){

		global $row_setup;

		// 업체정보를 추출
		$cp_r = _MQ("select * from odtMember where id = '".$cpid."' and userType = 'C'");
		// 티켓몰 4.0은 입점업체 배송정책기능을 지원하지 않습니다.
		if(0) {
//		if($cp_r[cpa_delivery_use] == "Y") {								// 입점업체 배송비 정책을 사용한다.
			//$dinfo[price] = $cp_r[cpa_delivery_price];
			//$dinfo[freePrice] = $cp_r[cpa_delivery_freeprice];
			//$dinfo[from] = "company";	// 입점업체

		}	else {															// 쇼핑몰  배송비 정책을 사용한다.
			$dinfo[price] = $row_setup[s_delprice];
			$dinfo[freePrice] = $row_setup[s_delprice_free];
			$dinfo[from] = "global";	// 쇼핑몰

		}

		$dinfo[status] = $dinfo[price] == 0 ? "1" : ($dinfo[freePrice] == 0 ? "3" : "2");

		return $dinfo;
	}

	// 문자를 발송한다.
	// 인자 : 상대방 전화번호, 문자 발송 유형 , 주문번호
	//				join					:회원가입,
	//				order_online	:주문완료_무통장
	//				order_pay			:주문완료_결제완료
	//				online_pay		:무통장입금확인
	//				delivery			:상품발송
	//				request				:문의
	// 리턴 : 없음 (자체 발송 처리)
	function shop_send_sms($to,$type,$ordernum="",$member_name="") {
		// global $row_setup,$row_company;
		// $arr_send = array();

		// // 사용자 발송
		// $smsInfo = _MQ("select * from m_sms_set where smskbn = '".$type."' limit 1");
		// if($smsInfo[smschk] == "y") {

		// 	$text = str_replace("{{주문번호}}",$ordernum,$smsInfo[smstext]);
		// 	$text = str_replace("{{회원명}}",$member_name,$smsInfo[smstext]);
		// 	$text = str_replace("{{주문상품명}}",$ordernum,$smsInfo[smstext]);
		// 	$text = str_replace("{{택배사명}}",$ordernum,$smsInfo[smstext]);
		// 	$text = str_replace("{{송장번호}}",$ordernum,$smsInfo[smstext]);
		// 	$text = str_replace("{{구매자명}}",$ordernum,$smsInfo[smstext]);
		// 	$text = str_replace("{{결제금액}}",$ordernum,$smsInfo[smstext]);
		// 	$text = str_replace("{{주문상품수}}",$ordernum,$smsInfo[smstext]);
		// 	$text = str_replace("{{쿠폰번호}}",$ordernum,$smsInfo[smstext]);
		// 	$text = str_replace("{{결제금액}}",$ordernum,$smsInfo[smstext]);


		// 	$arr_send[] = array('receive_num'=> $to , 'send_num'=> $row_company[tel] , 'msg'=> $text , 'reserve_time'=>'' );
		// }



		// // 관리자 발송
		// $smsInfo = _MQ("select * from m_sms_set where smskbn = 'admin_".$type."' limit 1");
		// if($smsInfo[smschk] == "y") {
		// 	$text = str_replace("{ORDER_NO_INSERT}",$ordernum,$smsInfo[smstext]);
		// 	$arr_send[] = array('receive_num'=> $siteInfo[s_glbmanagerhp], 'send_num'=> $siteInfo[s_glbtel] , 'msg'=> $text , 'reserve_time'=>'' );
		// }

		// onedaynet_sms_multisend($arr_send);

		return;
	}

	// 주문서 정보 추출
	function get_order_info($ordernum) {

        $r = _MQ("select *  from odtOrder where ordernum='".$ordernum."'");
        return $r;
	}

	// 주문서 상품 정보 추출
	function get_order_product_info($ordernum, $op_uid = false) {

		// @ 2017-02-27 LCY :: 주문상품패치
		$s_query = "";
        if($op_uid != false){
            $s_query = " and op_uid = '".$op_uid."'  ";
        }

        $r = _MQ_assoc("select *  from odtOrderProduct where op_oordernum='".$ordernum."' ".$s_query);

		return $r;

	}

	// 2차나 3차 카테고리 코드를 넣으면 2차 카테고리의 테마를 불러온다.
	function get_category_thema($cuid) {

		$c_info = get_category_info($cuid);

		if($c_info[catedepth] == 2) {
			return explode(",",$c_info[lineup]);
		} else {
			$parent_catecode = explode(",",$c_info[parent_catecode]);
			$c_info2 = get_category_info($parent_catecode[1]);

			return explode(",",$c_info2[lineup]);
		}

	}

	// 쇼핑몰 포인트 로그 입력
	// 인자 : 회원아이디(이메일) ,
	//				타이틀 ,
	//				적용포인트 ,		- 0이나 다른숫자가 들어오면 그냥 리턴
	//				적용상태(Y,N),
	//				처리일(0~ 숫자) - 0은 즉시 처리
	function shop_pointlog_insert( $_id , $_title , $_point , $_status , $_pro_date){

		if(!is_numeric($_point) || $_point==0) return;	 // 포인트가 0원이면 처리 하지 않는다.

		if(!is_numeric($_pro_date)) {	// 잘못입력되었거나, 숫자가 아닌경우 30일후 적립처리한다.
			$_appdate = date("Y-m-d" , strtotime("+30day"));
		} else {
			$_appdate = date("Y-m-d" , strtotime("+".$_pro_date."day"));
		}
		_MQ_noreturn("
			insert odtPointLog set
				  pointID='".$_id."'
				, pointTitle = '".$_title."'
				, pointPoint = ".$_point."
				, pointStatus='".$_status."'
				, redRegidate = '".$_appdate."'
				, pointRegidate = now()
		");
		$uid = mysql_insert_id();

		// 포인트 적립 클론 실행(즉시 적립을 위해)
		if($_appdate == DATE('Y-m-d')) {
			_MQ_noreturn("update odtMember set point = point + ". addslashes($_point) ." where  id = '". addslashes($_id) ."'");
			_MQ_noreturn("update odtPointLog set pointStatus = 'Y' where pointNo = '". $uid ."'");
		}
	}

	// 쇼핑몰 포인트 로그 삭제 | shop_pointlog_delete( 회원아이디(이메일) , 타이틀 )
	// - 기본적으로 포인트 로그 삭제는 30일 이내에 이루어져야 함 ---
	// - 주문의 경우 타이틀에 주문번호 명시되어 있으므로 삭제시 적용가능 ---
	function shop_pointlog_delete( $_id , $_title ){
		// 2014-12-09 포인트취소 패치 :: 이미포인트가 사용되었다면 회원포인트차감. 단, 음수이면 0으로
		$res = _MQ(" select pointStatus , pointPoint from odtPointLog where pointID='".$_id."' and pointTitle = '".$_title."' ");
		if($res["pointStatus"]=="Y"){
			_MQ_noreturn(" update odtMember set point = if(point < ".$res["pointPoint"]." , 0 , (point - ".$res["pointPoint"].")) where id = '".$_id."' ");
		}
		_MQ_noreturn(" delete from odtPointLog where pointID='".$_id."' and pointTitle = '".$_title."' ");
	}

	// 포인트 업데이트
	function point_update() {

		$r = _MQ_assoc("select * from odtPointLog where redRegidate <= '".date('Y-m-d')."' and pointStatus='N'");
		foreach( $r as $k=>$v ){

			_MQ_noreturn("update odtMember set point = point + ".$v[pointPoint]." where  id = '".$v[pointID]."'");
			_MQ_noreturn("update odtPointLog set pointStatus = 'Y' where  pointNo = '".$v[pointNo]."'");

		}

	}


	// 액션포인트 레벨 업데이트
	// 2016-03-14 :: 수정 -- 정준철 ::: 변경내역 - 전체회원을 타겟으로 할 경우 로딩이 길어져 하위를 실행하지 못하고 끝나는데 최근 3일간 액션포인트 변화가 있는 회원으로 제한을 두어 로딩 시간 줄임
	function action_point_update() {
		// 최근 3일 이내 변경이 있는 회원정보 추출 //
		$arr_id = array();
		$res = _MQ_assoc(" select acID from odtActionLog where DATE_ADD(acRegidate , INTERVAL +3 DAY) >= CURDATE() ");
		foreach( $res as $k=>$v ){$arr_id[$v['acID']]++;}

		if( sizeof($arr_id) > 0 ) {
			$member_assoc = _MQ_assoc("select action, actionLevel, id from odtMember where userType = 'B' and id in ('". implode("','" , array_keys($arr_id)) ."') ");
			foreach($member_assoc as $member_key => $member_row) {
				if($member_row[action] > 9000)			$actionLevel = 10;
				else if($member_row[action] > 8000)		$actionLevel = 9;
				else if($member_row[action] > 7000)		$actionLevel = 8;
				else if($member_row[action] > 6000)		$actionLevel = 7;
				else if($member_row[action] > 5000)		$actionLevel = 6;
				else if($member_row[action] > 4000)		$actionLevel = 5;
				else if($member_row[action] > 3000)		$actionLevel = 4;
				else if($member_row[action] > 2000)		$actionLevel = 3;
				else if($member_row[action] > 1000)		$actionLevel = 2;
				else									$actionLevel = 1;

				if( $member_row[actionLevel] <> $actionLevel ) {
					mysql_query("update odtMember set actionLevel = '".$actionLevel."' where id ='".$member_row[id]."'");
				}
			}
		}
		return;
	}


	// 주문상태 업데이트
	// 인자 : 주문번호
	// 리턴 : 주문상태(주문대기/주문취소/결제대기/결제확인/배송(발급)완료 - 주문상품 모두 배송상태가 Y 일경우 )
	function order_status_update($ordernum) {

		$order_row = _MQ("select * from odtOrder where ordernum='".$ordernum."'");

		if($order_row[orderstatus] == "N") {
			$orderstatus_step = "주문대기";
		} else {
			if($order_row[canceled]  == "Y") {
				$orderstatus_step = "주문취소";
			} else {
				if($order_row[paystatus] != "Y") {
				 	$orderstatus_step = "결제대기";
				} else {
					$orderstatus_step = "결제확인";

////					/* ----  주문상품의 배송상태 확인 ---- */
////					$op_assoc = _MQ_assoc("select op_delivstatus , op_cancel from odtOrderProduct where op_oordernum='".$ordernum."'");
////					foreach($op_assoc as $op_key => $op_row) {
////						if($op_row[op_delivstatus] == "Y" && $op_row[op_cancel] == "N") { $delivery_Y++; } else { $delivery_N++; }
////					}
////					if($delivery_Y > 0 && $delivery_N < 1) {	// 모두 배송 상태가 Y 일때,
////						if($order_row[order_type] == "coupon") {$orderstatus_step = "발급완료";}
////						else{$orderstatus_step = "발송완료";}
////					}
////					/* ---- // 주문상품의 배송상태 확인 ---- */
					/* ----  주문상품의 배송상태 확인 :: 부분취소된 상품 제외 2017-03-06 SSJ ---- */
					$op_assoc = _MQ_assoc("select op_delivstatus , op_cancel from odtOrderProduct where op_oordernum='".$ordernum."' and op_cancel != 'Y' ");
					foreach($op_assoc as $op_key => $op_row) {
						if($op_row[op_delivstatus] == "Y" ) {
							$delivery_Y++;
						}
						else {
							$delivery_N++;
						}
					}
					if($delivery_Y > 0 && $delivery_N < 1) {	// 모두 배송 상태가 Y 일때,
						if($order_row[order_type] == "coupon") {$orderstatus_step = "발급완료";}
						else{$orderstatus_step = "발송완료";}
					}
					/* ----  주문상품의 배송상태 확인 :: 부분취소된 상품 제외 2017-03-06 SSJ ---- */

				}
			}
		}
		_MQ_noreturn("update odtOrder set mem_cancelchk = '". ($delivery_Y > 0 ? "N" : "Y") ."' , orderstatus_step = '".$orderstatus_step."' where ordernum = '".$ordernum."'");
		return $orderstatus_step;

	}

	// 장바구니 담긴 상품갯수를 추출한다.
	// 인자 : 없음
	// 리턴 : 상품갯수(int)
	function get_cart_cnt() {
		global $_COOKIE;

		$r = _MQ("select count(*) as cnt from odtCart where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_is_addoption = 'N' ");

		return $r[cnt];
	}
/*--------- // 티켓몰 4.0 솔루션에서  새로추가된 함수 -----------------------------------*/

	function get_product_info($pcode) {

		return _MQ("SELECT * FROM odtProduct WHERE code ='${pcode}'");

	}


	// - 카테고리별 상품 갯수를 업데이트 한다. ---
	function update_catagory_product_count() {

		//return; // 사용을 위해서는 이줄을 제거 하세요 2015-11-20 LDD(jjc 지시)
		// -- 카테고리 상품 갯수 초기화 ---
		_MQ_noreturn("update odtCategory set c_pro_cnt=0");
		// -- 카테고리 당 상품 갯수 적용 ---
		$arr = array();
		$r = _MQ_assoc("
			select
				pct.pct_cuid,
				pct.pct_pcode,
				substring_index(c.parent_catecode , ',' ,-1) as c2_uid ,
				substring_index(c.parent_catecode , ',' ,1) as c1_uid
			from odtProductCategory as pct
			left join odtProduct as p on (p.code = pct.pct_pcode)
			left join odtCategory as c on (c.catecode = pct.pct_cuid and c.catedepth=3)
			where 1
				and if(sale_type = 'T', (CURDATE() BETWEEN sale_date and sale_enddate), sale_type = 'A')
				and p_view = 'Y'
		");
		//	and p.p_stock > 0
		//	group by pct.pct_cuid
		foreach($r as $k => $v) {
			$arr[$v[pct_cuid]][$v[pct_pcode]] ++;
			$arr[$v[c2_uid]][$v[pct_pcode]] ++;
			$arr[$v[c1_uid]][$v[pct_pcode]] ++;
		}
		foreach($arr as $k => $v) {
			$sque = " update odtCategory set c_pro_cnt = ".sizeof($v)." where catecode = '".$k."'";
			_MQ_noreturn($sque);
			//echo $sque . "<hr>";
		}
	}
	// - 카테고리별 상품 갯수를 업데이트 한다. ---


	// - 쇼핑몰 기본정보 호출 ---
	function info_basic() {
		return _MQ("SELECT * FROM odtSetup WHERE serialnum='1'");
	}
	// - 쇼핑몰 기본정보 호출 ---


	// - 회사정보 호출 ---
	function info_company() {
		return _MQ("SELECT * FROM odtCompany WHERE serialnum='1'");
	}
	// - 회사정보 호출 ---


	// - 로그인한 관리자 정보 호출 ---
	function info_admin($_MranDsum,$_MaddSum) {
		global $_COOKIE;
		$get_member_serialnum = $_COOKIE['auth_adminid'];
		$get_auth_adminid_sess = $_COOKIE['auth_adminid_sess'];
		$get_member_serialnum .= $_MranDsum .= $_MaddSum;
		$real_auth_adminid_sess = md5($get_member_serialnum);
		if($get_auth_adminid_sess == $real_auth_adminid_sess) {
			return _MQ("SELECT * FROM odtAdmin WHERE serialnum = '".$_COOKIE['auth_adminid']."'");
		}
	}
	// - 로그인한 관리자 정보 호출 ---


	// - 회원정보 호출 ---
	function info_member($ranDsum,$addSum) {
		global $_COOKIE;
		$get_member_serialnum = $_COOKIE['auth_memberid'];
		$get_auth_memberid_sess = $_COOKIE['auth_memberid_sess'];
		$get_member_serialnum .= $ranDsum .= $addSum;
		$real_auth_memberid_sess = md5($get_member_serialnum);
		if($get_auth_memberid_sess == $real_auth_memberid_sess) {
			return _MQ("SELECT * FROM odtMember WHERE serialnum = '".$_COOKIE['auth_memberid']."' AND secession != 'Y'");
		}
	}
	// - 회원정보 호출 ---

	// - 문자발송정보 호출 ---
	function info_sms() {

	    $assoc =  _MQ_assoc("select * from m_sms_set");
	    foreach($assoc as $k => $r) {
	        $sms_info[$r[smskbn]][smschk] = $r[smschk];
	        $sms_info[$r[smskbn]][smstext] = $r[smstext];
	        $sms_info[$r[smskbn]][smstitle] = $r[smstitle];
	        $sms_info[$r[smskbn]][smsfile] = $r[smsfile];
	        $sms_info[$r[smskbn]][sms_send_type] = $r[sms_send_type]; // 2015-09-15 LDD006

			// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			$sms_info[$r['smskbn']]['kakao_status'] = $r['kakao_status'];
			$sms_info[$r['smskbn']]['kakao_templet_num'] = $r['kakao_templet_num'];
			$sms_info[$r['smskbn']]['kakao_add1'] = $r['kakao_add1'];
			$sms_info[$r['smskbn']]['kakao_add2'] = $r['kakao_add2'];
			$sms_info[$r['smskbn']]['kakao_add3'] = $r['kakao_add3'];
			$sms_info[$r['smskbn']]['kakao_add4'] = $r['kakao_add4'];
			$sms_info[$r['smskbn']]['kakao_add5'] = $r['kakao_add5'];
			$sms_info[$r['smskbn']]['kakao_add6'] = $r['kakao_add6'];
			$sms_info[$r['smskbn']]['kakao_add7'] = $r['kakao_add7'];
			$sms_info[$r['smskbn']]['kakao_add8'] = $r['kakao_add8'];
			// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

	    }
	    return $sms_info;
	}


	// - 입점업체 관리자 정보 호출 ---
	function info_subcompany($_MranDsum,$_MaddSum) {
		global $_COOKIE;
		$get_member_serialnum = $_COOKIE['auth_comid'];
		$get_auth_adminid_sess = $_COOKIE['auth_comid_sess'];
		$get_member_serialnum .= $_MranDsum .= $_MaddSum;
		$real_auth_adminid_sess = md5($get_member_serialnum);

		if($get_auth_adminid_sess == $real_auth_adminid_sess) {
			return _MQ("SELECT * FROM odtMember WHERE serialnum = '".$_COOKIE['auth_comid']."'");
		}
	}
	// - 입점업체 관리자 정보 호출 ---



	// - 회원인증 ---
	function chk_login($url,$msg,$ranDsum,$addSum) {
		global $_COOKIE;
		$get_member_serialnum = $_COOKIE['auth_memberid'];
		$get_auth_memberid_sess = $_COOKIE['auth_memberid_sess'];

		if(!$get_member_serialnum) { error_loc_msg($url, $msg); }
		if(!$get_auth_memberid_sess) { error_loc_msg($url, $msg); }

		$get_member_serialnum .= $ranDsum .= $addSum;
		$real_auth_memberid_sess = md5($get_member_serialnum);

		if($get_auth_memberid_sess == $real_auth_memberid_sess) {
			return true;
		}
		else {
			error_loc_msg($url,$msg);
			return false;
		}
	}
	// - 회원인증 ---


	// - Get 방식 Encode ---
	function var_encode($get_string_value) {
		return base64_encode($get_string_value)."$!";
	}

	// - Get 방식 Decode ---
	function var_decode($get_string_value) {
		$decode_division = explode("&",base64_decode(str_replace("$!","",$get_string_value)));
		$decode_division_total = count($decode_division);
		for($i=0;$i<$decode_division_total;$i++){
			$array_division = explode("=",$decode_division[$i]);
			$_Decode[$array_division[0]] = $array_division[1];
		}
		return $_Decode;
	}


	// - 주문번호 생성 - 재귀함수 적용 - 2014-03-19 ---
	function get_ordernumber($length) {
		$length = $length ? $length : 5;
		$md5Temp = md5(uniqid(rand()));
		$unique = substr($md5Temp, 0, $length);
		$sumTemp = (date("Y")+date("m")+date("d")+date("H")+date("i")+date("s")+19)*997;
		$lengthTemp = strlen($sumTemp);
		$checksum = substr($sumTemp,$lengthTemp-2,2);
		$ordernum = "S".$unique.$checksum;

		// - 과거 같은 주문 번호 여부 확인 ---
		$orderchk = _MQ("select count(*) as cnt from odtOrder where ordernum = '".  strtoupper($ordernum) ."'");
		if( $orderchk[cnt] > 0 ){
			get_ordernumber($length);
		}
		return $ordernum;
	}
	// - 주문번호 생성 - 재귀함수 적용 - 2014-03-19 ---



	// 주문중인 상품의 재고를 확인한다.
	function order_product_stock_check($c_cookie) {

		// 재고 확인시 별문제가 없다면 ok가 리턴될것이다.
		$return_value = "ok";

		$r = _MQ_assoc("select * from odtCart where c_cookie = '".$c_cookie."' and c_cnt > 0");
		foreach($r as $k => $v) {
			$is_option = $v[c_pouid] ? true : false; // 옵션상품인지 일반상품인지 체크

			// 상품 수량
			list($cnt) = _MQ("select stock as `0` from odtProduct where code = '".$v[c_pcode]."'");

			// 상품 옵션 수량
			if($is_option) {
				$is_addoption = $v[c_is_addoption]=="Y" ? true : false; // 일반옵션인지 추가옵션인지 체크
				if($is_addoption){
					list($option_cnt) = _MQ("select pao_cnt as `0` from odtProductAddoption where pao_pcode = '".$v[c_pcode]."' and pao_uid = '".$v[c_pouid]."'");
					// 추가 옵션수량을 재고로 잡는다.
					$cnt = $option_cnt;
				}else{
					list($option_cnt) = _MQ("select oto_cnt as `0` from odtProductOption where oto_pcode = '".$v[c_pcode]."' and oto_uid = '".$v[c_pouid]."'");
					// 상품 본 수량과 옵션수량중 작은 수량을 재고로 잡는다.
					$cnt = min($option_cnt,$cnt);
				}
			}


			if($cnt < 1) {			// 이미 품절된 상품이면..
				// 장바구니에 담긴 수량을 0으로 수정
				_MQ_noreturn("update odtCart set c_cnt=0 where c_uid = '".$v[c_uid]."'");
				$return_value = "soldout";
			} else if($v[c_cnt] > $cnt) { // 재고량 보다 장바구니에 담긴 수량이 더 많으면...
				_MQ_noreturn("update odtCart set c_cnt=".$cnt." where c_uid = '".$v[c_uid]."'");
				if($return_value != "soldout") $return_value = "notenough";	// 이미 soldout된 상품이 있다면 리턴값을 수정할필요 없다.
			}
		}

		return $return_value;
	}

	// 해당 상품에 달린 토크 총 갯수를 구한다.
	// 인자 : 상품코드(코드),
	//				작성자유형(all:전체, normal:회원, admin:운영자, company:입점업체)
	//				조건문
	// 리턴 : 갯수
	function get_talk_total($pcode,$mtype='all', $where='') {
		global $arr_p_talk_type;

		if($mtype != "all"){
			if($mtype == "member"){
				$where .= " and m.userType = 'B' and m.Mlevel  != '9' ";
			}
			if($mtype == "company"){
				$where .= " and m.userType = 'C' and m.Mlevel  != '9' ";
			}
			if($mtype == "admin"){
				$where .= " and m.userType = 'B' and m.Mlevel  = '9' ";
			}
		}
		$where .= " and ttProCode = '".$pcode."' ";

		$row = _MQ("select count(*) as cnt from odtTt where 1 ".$where);

		return $row[cnt];

	}


	// 게시판 정보를 추출한다.
	// 인자 : 게시판 코드
	// 리턴 : 게시판 정보
	function get_board_info($b_menu) {

		$r = _MQ("select * from odtBbsInfo where bi_uid='".$b_menu."'");

		return $r;

	}

	// 게시물 정보를 추출한다.
	// 인자 : 게시물 코드
	// 리턴 : 게시물 정보
	function get_post_info($b_uid) {

		$r = _MQ("select * from odtBbs where b_uid='".$b_uid."'");

		return $r;

	}

	// 게시물을 최근 목록 추출한다.
	// 인자 : 게시판코드, 추출갯수
	// 리턴 : 게시물 목록
	function get_board_list($b_menu,$limit=10) {

		$r = _MQ_assoc("select * from odtBbs where b_menu='".$b_menu."' ORDER BY b_uid desc limit ".$limit);

		return $r;

	}

	// 게시물을 추출한다.
	// 인자 : 게시판코드, 조건문
	// 리턴 : 게시물 갯수
	function get_board_cnt($b_menu,$where='') {

		$r = _MQ("select count(*) as cnt from odtBbs where b_menu='".$b_menu."' ".$where);

		return $r[cnt];

	}

	// 게시물 갯수를 업데이트 한다.
	// 인자 : 게시판코드
	// 리턴 : 없음.
	function update_board_post_cnt($bi_uid='') {

		if($bi_uid) $where = " where bi_uid='".$bi_uid."'";
		_MQ_noreturn("update odtBbsInfo set bi_post_cnt = (select count(*) from odtBbs where b_menu = bi_uid) ".$where);
		return;

	}

	// 게시물 댓글을 추출한다.
	// 인자 : 게시판코드, 조건문
	// 리턴 : 게시물 댓글 갯수
	function get_board_talk_cnt($b_menu,$where='') {

		$r = _MQ("select sum(b_talkcnt) as talkcnt from odtBbs where b_menu='".$b_menu."' ".$where);

		return $r[talkcnt];

	}




	// --- 상품카테고리별 카테고리 종류 배열화 ---
	function _product_category_banner(){
		GLOBAL $arr_product_banner_loc;
		$arr_banner_loc = array();

		// 기획전이 아닐 경우 1차 카테고리 노출
		$cr = _MQ_assoc(" select * from odtCategory where cHidden='no' and catedepth=1 and subcate_display!='기획전' order by cateidx asc , catecode asc");
		foreach($cr as $sk=>$sv){
			foreach($arr_product_banner_loc as $k=>$v) {
				$arr_banner_loc[$sv[catecode].$k] = $sv[catename]."-".$v;
				$arr_banner_loc[$sv[catecode].$k] = $sv[catename]."-".$v;
				$arr_banner_loc[$sv[catecode].$k] = $sv[catename]."-".$v;
				$arr_banner_loc[$sv[catecode].$k] = $sv[catename]."-".$v;
			}
			/*$arr_banner_loc[$sv[catecode].",big"] = $sv[catename]."-(PC)큰비주얼배너(743 x 353)";
			$arr_banner_loc[$sv[catecode].",smalltop"] = $sv[catename]."-(PC)작은비주얼배너상단(225 x 172)";
			$arr_banner_loc[$sv[catecode].",smallbottom"] = $sv[catename]."-(PC)작은비주얼배너하단(225 x 172)";
			$arr_banner_loc[$sv[catecode].",mobile"] = $sv[catename]."-(Mobile)큰비주얼배너(565 x 291)";*/
		}

		// 기획전일 경우 2차 카테고리 노출
		$cr = _MQ_assoc("
			select
				c2.* , c1.catename as c1_catename
			from odtCategory as c2
			inner join odtCategory as c1 on (c1.catecode=c2.parent_catecode)
			where
				c2.cHidden='no' and
				c2.catedepth='2' and
				c1.subcate_display='기획전' and
				c1.cHidden='no'
			order by c2.cateidx asc , c2.catecode asc
		");
		foreach($cr as $sk=>$sv){
			$arr_banner_loc[$sv[catecode].",visual"] = "(". $sv[c1_catename] .")" . $sv[catename]."-비주얼배너(1000 x free[기본 : 638])";
		}

		return $arr_banner_loc;
	}
	// --- 상품카테고리별 카테고리 종류 배열화 ---




	// - 포인트 처리 ---
		// --- 지급예정일에 따른 포인트 지급처리 적용(전체 적용) ---
		function _point_add_all(){
			$que = " select * from odtPointLog where pointStatus ='N' and redRegidate <= CURDATE() ";
			$res = _MQ_assoc($que);
			foreach($res as $k=>$v) {
				_MQ_noreturn("update odtPointLog set pointStatus='Y' where pointNo = '".$v[pointNo]."'");
				_MQ_noreturn("update odtMember set point = point + ".$v[pointPoint]." where id ='".$v[pointID]."'");
			}
		}
		// --- 지급예정일에 따른 포인트 지급처리 적용(전체 적용) ---
	// - 포인트 처리 ---



	// - 공급업체 배열화 ---
	function arr_company(){
		$arr_customer = array();
		$res = _MQ_assoc("SELECT id , cName FROM odtMember where userType = 'C' and id !='onedaynet' ORDER BY cName asc ");
		foreach( $res as $k=>$v ){
			$arr_customer[$v[id]] = $v[cName] . "(아이디 : ". $v[id] .")";
		}
		return $arr_customer;
	}
	function arr_company2(){
		$arr_customer = array();
		$res = _MQ_assoc("SELECT id , cName FROM odtMember where userType = 'C' and id !='onedaynet' ORDER BY cName asc ");
		foreach( $res as $k=>$v ){
			$arr_customer[$v[id]] = $v[cName];
		}
		return $arr_customer;
	}
	// - 공급업체 배열화 ---




	// - MD 정보 배열화 ---
	function arr_mdlist(){
		$arr_mdlist = array();
		$res = _MQ_assoc("SELECT mdName FROM odtMD ORDER BY mdName asc ");
		foreach( $res as $k=>$v ){
			$arr_mdlist[$k] = $v[mdName];
		}
		return $arr_mdlist;
	}
	// - MD 정보 배열화 ---



	// - 게시판 종류 배열화 ---
	function arr_board($type=null){
		$_ARR_BBS = array();
		$res = _MQ_assoc("SELECT * from odtBbsInfo where bi_view='Y' order by bi_uid ");
		foreach( $res as $k=>$v ){
			if( $type == 'Y' ) {
				$_ARR_BBS[$v[bi_uid]] = array('name'=>$v[bi_name],'type'=>$v[bi_list_type]);
			} else {
				$_ARR_BBS[$v[bi_uid]] = $v[bi_name];
			}
		}
		return $_ARR_BBS;
	}
	// - 게시판 종류 배열화 ---


	// 사용자 정보를 보여준다
	// 관리자 전용 함수이며, 회원정보 수정 페이지로 넘겨 처리한다.
	// 인지	: 회원아이디
	//			: 링크연결
	// 회원정보가 없으면 $str 만 출력한다. (기업회원이나, 가상회원, 삭제된 회원등)
	function showUserInfo($id,$str='') {

		// $str 값이 없으면 아이디를 대신 출력한다.
		if(!$str) $str = $id;

		// 회원정보가 없으면 $str 만 출력한다.
		$r = _MQ("select * from odtMember where id = '".$id."'");
		if(sizeof($r) > 0 ){
			return	"<u><a href='_member.form.php?_mode=modify&serialnum=". $r[serialnum] ."' target='_blank'>".$str."</a></u>";
		}
		else {
			return $str;
		}
	}





	// 텍스트 연동 정보 입력하기 _text_info_insert( 테이블명 , 연동 부모데이터 고유번호 , 키워드타입(상세요강, 자기소개서등), text형태 값)
	function _text_info_insert( $tablename , $datauid , $keytype , $value , $trigger=null ){
		if(trim($value) || $trigger == "ignore") {
			// 데이터 확인
			$r = _MQ("select count(*) as cnt from odtTableText where ttt_tablename = '{$tablename}' and ttt_datauid = '{$datauid}' and ttt_keyword = '{$keytype}'");
			if($r[cnt] > 0) {
				// 데이터 수정
				_MQ_noreturn(" update odtTableText set ttt_value = '" . mysql_real_escape_string($value) . "' where ttt_tablename = '{$tablename}' and ttt_datauid = '{$datauid}' and ttt_keyword = '{$keytype}' ");
			}
			else {
				// 데이터 입력
				_MQ_noreturn(" insert odtTableText set ttt_tablename = '{$tablename}' , ttt_datauid = '{$datauid}' , ttt_keyword = '{$keytype}' , ttt_value = '" . mysql_real_escape_string($value) . "'");
			}
		}
	}

	// 일반 연동 정보 입력하기 _tail_info_delete( 테이블명 , 연동 부모데이터 고유번호 , 키워드타입(상세요강, 자기소개서등))
	function _text_info_delete( $tablename , $datauid , $keytype ){
		// 이전 데이터 삭제
		_MQ_noreturn("delete from odtTableText where ttt_tablename = '{$tablename}' and ttt_datauid = '{$datauid}' and ttt_keyword = '{$keytype}'");
	}

	// 일반 연동 정보 입력하기 _text_info_extraction( 테이블명 , 연동 부모데이터 고유번호 , 키워드타입(상세요강, 자기소개서등))
	function _text_info_extraction( $tablename , $datauid ){
		// 데이터 추출
		$arr = array();
		$r = _MQ_assoc("select ttt_keyword , ttt_value from odtTableText where ttt_tablename = '{$tablename}' and ttt_datauid = '{$datauid}' order by ttt_uid asc ");
		foreach($r as $k=>$v){
			$arr[$v[ttt_keyword]] = $v[ttt_value];
		}
		return $arr;
	}



	// 상품 아이콘 정보를 가져온다.
	// 인자 : 상품 아이콘 유형 (생략시 모든 아이콘 정보 추출)
	// 리턴 : 상품 아이콘 정보 배열값
	function get_product_icon_info_qry($_type = "all") {
		$data = array();
		$_where = $_type != "all" ? " and pi_type ='".$_type."' " : "";
		$r = _MQ_assoc("select * from odtProductIcon where 1 ".$_where." order by pi_idx");
		foreach($r as $k => $v) {
			$data[] = $v;
		}
		return $data;
	}

	// pg 결제 취소에 대한 로그를 쌓는다
	function card_cancle_log_write($tno,$res_msg, $ordernum=null) {

		global $_ordernum;
		$ordernum = $ordernum ? $ordernum : $_ordernum;
		_MQ_noreturn("update odtOrderCardlog set oc_cancle_content = '".$res_msg."' where oc_tid = '".$tno."' ". ($ordernum ? "  and oc_oordernum = '". $ordernum ."' " : ""));

	}

	// 주문상태에 해당하는 갯수를 구한다
	function get_order_status_cnt($status,$since=90) {
		
		// 90일전 일자정리
		$app_orderdate = DATE("Y-m-d" , strtotime("-". $since ." day")) . " 00:00:00";

		if( is_login() ) {
			switch($status){

				case "결제대기":
					$ordr = _MQ("
						select count(*) as cnt from odtOrder
						where
							orderid='".get_userid()."' and paystatus = 'N' and canceled = 'N' and 
							IF( paymethod in ('B','V') , orderstep='finish' , paystatus ='Y') and
							IF( paymethod = 'V' , ( SELECT count(*) FROM odtOrderCardlog where oc_oordernum = ordernum ) > 0 , 1) 

							/* JJC : 2020-01-31 : 속도개선 */
							/* and DATEDIFF(CURDATE(),orderdate) <= ".$since." */
							and orderdate >= '". $app_orderdate ."'
					");
					return $ordr['cnt'];
				break;

				case "결제확인":
				case "결제완료":
					$ordr = _MQ("
						select count(*) as cnt from odtOrder
						where orderid = '".get_userid()."' and paystatus = 'Y' and canceled = 'N' and orderstatus_step = '결제확인'

						/* JJC : 2020-01-31 : 속도개선 */
						/* and DATEDIFF(CURDATE(),orderdate) <= ".$since." */
						and orderdate >= '". $app_orderdate ."'
					");
					return $ordr['cnt'];
				break;

				case "발급대기":
				case "발송대기":
					$ordr = _MQ("
						select count(*) as cnt from odtOrderProduct as op
						left join odtOrder as o on (o.ordernum = op.op_oordernum)
						where orderid = '".get_userid()."' and paystatus='Y' and paystatus2='Y' and canceled='N' AND orderstatus='Y' and op.op_delivstatus='N'
						and op.op_orderproduct_type != 'coupon' and op.op_cancel != 'Y' and op.op_is_addoption != 'Y'

						/* JJC : 2020-01-31 : 속도개선 */
						/* and DATEDIFF(CURDATE(),orderdate) <= ".$since." */
						and orderdate >= '". $app_orderdate ."'
					");
					return $ordr['cnt'];
				break;

				case "발급완료":
				case "발송완료":
					$ordr = _MQ("
						select count(*) as cnt from odtOrderProduct as op
						left join odtOrder as o on (o.ordernum = op.op_oordernum)
						where orderid = '".get_userid()."' and paystatus='Y' and paystatus2='Y' and canceled='N' AND orderstatus='Y' and op.op_delivstatus='Y'
						and op.op_orderproduct_type != 'coupon' and op.op_cancel != 'Y' and op.op_is_addoption != 'Y'

						/* JJC : 2020-01-31 : 속도개선 */
						/* and DATEDIFF(CURDATE(),orderdate) <= ".$since." */
						and orderdate >= '". $app_orderdate ."'
					");
					return $ordr['cnt'];
				break;

				case "주문취소":
					$since_time = strtotime(" - ". $since ." day");
					$this_time = time();
					$ordr = _MQ("
						select count(*) as cnt from odtOrder
						where orderid = '".get_userid()."' and canceled = 'Y'

						/* JJC : 2020-01-31 : 속도개선 */
						/* and DATEDIFF(CURDATE(),FROM_UNIXTIME(canceldate)) <= ".$since." */
						and canceldate >= ". $since_time ." and canceldate <= ". $this_time ."
					");
					return $ordr['cnt'];
				break;

			}
		} else { return 0; }
	}



	// 주문의 정산상태 체크 -- 주문번호 입력시 확인
	function order_settlement_status($ordernum) {

		// 주문 정산상태 추출
		$or = _MQ("select paystatus3 from odtOrder where ordernum='". $ordernum ."' ");

		// 주문상품 정산상태 추출
		$arr_op = array();
		$opr = _MQ_assoc("select op_settlementstatus , count(*) as cnt from odtOrderProduct where op_cancel='N' and op_oordernum='". $ordernum ."' group by op_settlementstatus ");
		foreach($opr as $k=>$v){
			$arr_op[$v[op_settlementstatus]] = $v[cnt];
		}

		if( sizeof($opr) > 0 ) {
			if($arr_op["none"] > 0 ) {$_status = "none";}
			else if($arr_op["ready"] > 0 ) {$_status = "ready";}
			else if($arr_op["complete"] > 0 ) {$_status = "complete";}

			if( $_status <> $or[paystatus3] ){
				_MQ_noreturn(" update odtOrder set paystatus3='". $_status ."' where ordernum='". $ordernum ."' ");// 주문 정산상태 변경
			}
		}

		return $_status ;

	}

	// 주문의 정산상태 체크 -- 주문상품 고유번호 --> 배열형태 . 예) array(111, 222);
	function order_settlement_status_opuid($arr_opuid) {

		// 주문번호정보 추출
		$arr_ordernum = array();
		$opr = _MQ_assoc("select op_oordernum from odtOrderProduct where op_cancel='N' and op_uid in ('". implode("' , '" , array_values($arr_opuid) ) ."') group by op_oordernum ");
		foreach($opr as $k=>$v){
			$arr_ordernum[$v[op_oordernum]]++;
		}

		// 주문상품 정산상태 추출
		$arr_op = array();
		$opr = _MQ_assoc("select op_oordernum , op_settlementstatus , count(*) as cnt from odtOrderProduct where op_cancel='N' and op_oordernum in ('". implode("' , '" , array_keys($arr_ordernum) ) ."') group by op_oordernum , op_settlementstatus ");
		foreach($opr as $k=>$v){
			$arr_op[$v[op_oordernum]][$v[op_settlementstatus]] = $v[cnt];
		}

		if( sizeof($arr_op) > 0 ) {
			foreach($arr_op as $k=>$v){
				$_status = "";
				if($v["none"] > 0 ) {$_status = "none";}
				else if($v["ready"] > 0 ) {$_status = "ready";}
				else if($v["complete"] > 0 ) {$_status = "complete";}
				if( $_status  ){
					_MQ_noreturn(" update odtOrder set paystatus3='". $_status ."' where ordernum='". $k ."' ");// 주문 정산상태 변경
				}
			}
		}

		return $arr_op ;

	}


	// 세금계산서 연동 - 바로빌 로그 저장
	function tax_log_insert($suid , $mode , $code , $msg){
		$que = "
			insert odtOrderSettleCompleteLog set
				sl_suid = '". $suid ."',
				sl_mode ='". $mode ."',
				sl_code = '". $code ."',
				sl_remark ='". $msg ."',
				sl_rdate = now()
		";
		_MQ_noreturn($que);
	}


	// 휴면계정 별도 저장처리 -- odtMember -> odtMemberSleep 복사 후 수정
	function member_sleep_backup(){
		global $row_setup;

        // @ -- 2017-06-01 LCY -- 휴면계정개선패치 :: 필드업데이트
        UpdateMemberTable();

		$mr = _MQ_assoc("select * from odtMember where userType='B' and Mlevel != '9' AND name not in ('휴면전환' , '탈퇴한회원') and recentdate < '". strtotime("- ". $row_setup['member_sleep_period'] ." month") ."' ");
		foreach( $mr as $k=>$v ){

			// --- 복사 ---
			$_field1 = $_field2 = array();
			foreach( $v as $sk=>$sv ){
				$_field1[] = $sk . " = '". addslashes(stripslashes($sv)) ."' " ;
				if( !in_array( $sk , array("serialnum" , "id" , "name")) ){$_field2[] = $sk . " = '' " ; }
			}

			// --- odtMemberSleep 정보 추가 ---
			$sque = " insert odtMemberSleep set ms_rdate = now() , ". implode(" , " , array_filter($_field1)) ." ";
			_MQ_noreturn($sque);

			// --- odtMember 정보 변경 ---
			_MQ_noreturn(" update odtMember set name='휴면전환' , ". implode(" , " , array_filter($_field2)) ." where serialnum='". $v['serialnum'] ."' ");

		}
	}


	// 휴면계정 복귀처리 -- odtMemberSleep --> odtMember 수정 후 삭제
	function member_sleep_return( $_id ){

        // @ -- 2017-06-01 LCY -- 휴면계정개선패치 :: 필드업데이트
        UpdateMemberTable();

		$mr = _MQ("select * from odtMemberSleep where id='". $_id ."' ");
		if(sizeof($mr) > 0 ) {
			// --- 복사 ---
			$_field = array();
			foreach( $mr as $sk=>$sv ){if( !in_array( $sk , array("ms_serialnum" , "ms_rdate" , "ms_sendchk" , "serialnum" , "id" , "recentdate")) ){$_field[] = $sk . " = '". addslashes(stripslashes($sv)) ."' " ;}}
			$sque = " update odtMember set recentdate='". time() ."' , ". implode(" , " , array_filter($_field)) ." where serialnum='". $mr['serialnum'] ."' ";
			_MQ_noreturn($sque);

			// --- 삭제 ---
			_MQ_noreturn(" delete from odtMemberSleep where ms_serialnum='". $mr['ms_serialnum'] ."' ");
		}
		return (sizeof($mr) > 0 ? "Y" : "N");
	}


	// 관련상품 지정 리스트 호출 -> 상품설정에 따라 지정되거나 동일 카테고리에서 랜덤하게 리스트를 출력 한다. # LDD012
	function relation_list($pcode, $limit=10) {

		$row_product = get_product_info($pcode);
		$row_product = array_merge($row_product , _text_info_extraction( "odtProduct" , $row_product['serialnum'] ));

		if($row_product['relation_auto'] == 'Y') {

			$Tmp = create_function('$values',
				'
				$retun = array();
				if(sizeof($values) > 0 ){
					foreach($values as $k=>$v) {
						foreach($v as $kk=>$vv) { $return[] = $vv; }
					}
				}
				return $return;
				');
			$shuffle = create_function('&$array', '
			$keys = array_keys($array);
	        shuffle($keys);
			if(sizeof($array) > 0 ){
				foreach($keys as $key) {
					$new[$key] = $array[$key];
				}
			}
	        $array = $new;
	        return $array;
	        ');
			$Category = _MQ_assoc(" select `pct_cuid` from `odtProductCategory` where (1) and `pct_pcode` = '{$pcode}' ");
			if(sizeof($Category) > 0 ){
				//$rePcode = _MQ_assoc(" select `pct_pcode` from `odtProductCategory` where (1) and `pct_pcode` != '{$pcode}' and `pct_cuid` in(".implode(',', array_values($Tmp($Category))).") ");
				// SSJ:2017-10-11 노출설정, 판매기간, 재고에따른 검색 추가
				$rePcode = _MQ_assoc(" select `pct`.`pct_pcode` from `odtProductCategory` as `pct` left join `odtProduct` as `p` on (`pct`.`pct_pcode` = `p`.`code`) where (1) and `pct`.`pct_pcode` != '{$pcode}' and `pct`.`pct_cuid` in(".implode(',', array_values($Tmp($Category))).") and `p`.`p_view` = 'Y' and if(`p`.`sale_type` = 'T', (CURDATE() BETWEEN `p`.`sale_date` and `p`.`sale_enddate`), `p`.`sale_type` = 'A') and `p`.`stock` > 0 ");
				$row_product['p_relation'] = sizeof($rePcode) > 0 ? implode(',', array_slice(array_values($Tmp($shuffle($rePcode))), 0, $limit)) : "";
			}
		}
		$relation = str_replace("/",",",$row_product['p_relation']);
		return $relation;
	}



	# LDD018
	/*
	예약발송 상품의 개수를 출력한다.
	*/
	function reserve_order($stand_date = '', $comid = '', $delivstatus = 'no') {

		$sql_common = array(
				" o.paystatus = 'Y' ", // 입금확인여부
				" o.paystatus2 = 'Y' ", // 결제승인여부
				" o.canceled = 'N' ", // 취소여부
				" o.orderstatus = 'Y' ", // 주문형태
				" o.delivery_date != '0000-00-00' ", // 배송 예약일 지정 상품만
				" op.op_orderproduct_type = 'product' ", // 배송상품 구분
				" op.op_cancel = 'N' ", // 취소되지 않은 상품
				" op.op_settlementstatus = 'none' ", // 미정산 상품
				);

		// 날짜 지정으로 뽑아오기
		if($stand_date != '') {

			$stand_date = $stand_date*1;
			$sql_common = array_merge($sql_common, array(" o.delivery_date between date(now()) and date(date_add(CURDATE(), interval ".$stand_date." day)) "));
		}

		// 입점업체 아이디 지정
		if($comid != '') {

			$sql_common = array_merge($sql_common, array(" op.op_partnerCode = '".$comid."' "));
		}

		// 배송여부에 따른 쿼리문 추가
		if($delivstatus == 'yes') $sql_common = array_merge($sql_common, array(" op.op_delivstatus='Y' ")); // 배송된 상품
		else if($delivstatus == 'no') $sql_common = array_merge($sql_common, array(" op.op_delivstatus='N' ")); // 배송대기 상품

		// 쿼리 실행
		$que = "
			select
				sum(op_cnt) as tCnt
			from odtOrderProduct as op
			left join odtOrder as o on (o.ordernum = op.op_oordernum)
			where ".implode('and ', $sql_common);
		$Data = _MQ($que);

		// 개수 리턴
		return $Data['tCnt'];
	}


	// 장바구니 판매불가 상품 삭제
	function clean_cart_old(){
		global $_COOKIE;

		$cque = "
			select sale_date,sale_enddate,code,name,prolist_img,sum(c_cnt*(c_price+c_optionprice)) as sum_price,coupon_title,coupon_price,setup_delivery,point,c_pouid,c_uid,stock,p_view,sale_type
			from odtCart as c
			inner join odtProduct as p on (p.code=c.c_pcode)
			where c.c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
			group by code
		";
		$cr = _MQ_assoc($cque);

		// 재고수량 체크하여 없으면 장바구니에서 삭제한다
		foreach($cr as $k=>$v) {
			unset($_trigger);

			// 기본 재고 체크
			if($v[stock] < 1) { $_trigger++; }

			if($v[c_pouid] > 0) { // 옵션이 있으면
				if( $v[c_is_addoption] == 'Y' ) {
					$res = _MQ(" select pao_cnt from odtProductAddoption where pao_pcode = '".$v[code]."' and pao_uid = '".$v[c_pouid]."' ");
					if($res[pao_cnt] < 1) { $_trigger++; }
				} else {
					$res = _MQ(" select oto_cnt from odtProductOption where oto_pcode = '".$v[code]."' and oto_uid = '".$v[c_pouid]."' ");
					if($res[oto_cnt] < 1) { $_trigger++; }
				}
			}
			// 판매기간 아닌 상품 삭제
			if( $v[sale_type]!='A' ) {
				if( $v[sale_enddate] < date('Y-m-d') ) { $_trigger++; }
				if( $v[sale_date] > date('Y-m-d') ) { $_trigger++; }
			}
			// 비노출 상품 삭제
			if( $v[p_view]!='Y' ) { $_trigger++; }

			if( $_trigger > 0 ) {
				_MQ_noreturn(" delete from odtCart where c_uid = '".$v[c_uid]."' and c_cookie = '".$_COOKIE['AuthShopCOOKIEID']."' ");
			}
		}

	}

	// -- 2016-12-05 LCY :: 기존함수수정
	// 장바구니 판매불가 상품 삭제
	/*
	function clean_cart()
	{
		global $_COOKIE;

		$cque = "
			select sale_date,sale_enddate,code,c_pouid,c_uid,stock,p_view,sale_type, c_is_addoption
			from odtCart as c
			inner join odtProduct as p on (p.code=c.c_pcode)
			where c.c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
		";
		$cr = _MQ_assoc($cque);

		// 재고수량 체크하여 없으면 장바구니에서 삭제한다
		$arr_code  = array();
		foreach($cr as $k=>$v) {
			unset($_trigger);

			// 기본 재고 체크
			if($v[stock] < 1) { $_trigger++; }

			if($v[c_pouid] > 0) { // 옵션이 있으면
				if( $v[c_is_addoption] == 'Y' ) {
					$res = _MQ(" select pao_cnt from odtProductAddoption where pao_pcode = '".$v[code]."' and pao_uid = '".$v[c_pouid]."' ");
					if($res[pao_cnt] < 1) { $_trigger++;  }
				} else {
					$res = _MQ(" select oto_cnt from odtProductOption where oto_pcode = '".$v[code]."' and oto_uid = '".$v[c_pouid]."' ");
					if($res[oto_cnt] < 1) { $_trigger++; }
				}
			}
			// 판매기간 아닌 상품 삭제
			if( $v[sale_type]!='A' ) {
				if( $v[sale_enddate] < date('Y-m-d') ) { $_trigger++; }
				if( $v[sale_date] > date('Y-m-d') ) { $_trigger++; }
			}
			// 비노출 상품 삭제
			if( $v[p_view]!='Y' ) { $_trigger++; }

			if( $_trigger > 0 ) {
				_MQ_noreturn(" delete from odtCart where c_uid = '".$v[c_uid]."' and c_cookie = '".$_COOKIE['AuthShopCOOKIEID']."' ");
			}
		}

	}
	*/
	# 장바구니 판매불가 상품 삭제 2017-02-10 LDD
    function clean_cart() {

        # 변경된 상품 정보 조회(상품삭제, 노출여부, 판매기간종료 혹은 시작전, 상품재고, 상품가격, 상품공급가, 옵션재고, 옵션가격, 옵션공급가, 추가옵션재고, 추가옵션가격, 추가옵션공급가)
        $qur = "
            select
                c.c_pcode,
				c.c_pouid
            from
                odtCart as c left join
                odtProduct as p on(c.c_pcode = p.code)
            where (1) and
                c.c_cookie = '{$_COOKIE['AuthShopCOOKIEID']}' and
                (
                    p.code is null or
                    p.p_view != 'Y' or
                    (p.sale_type = 'T' and (p.sale_enddate < curdate() or sale_date > curdate())) or
					(p.option_type_chk = 'nooption' and c.c_pouid > 0 ) or
                    if(
                        c.c_pouid = 0,

                        (if(c.c_price = p.price and c.c_supply_price = p.purPrice and c.c_cnt <= p.stock, 'Y', 'N')),

                        (
                            if(
                                c.c_is_addoption != 'Y',
                                    if(
                                        (select concat(oto_pcode, oto_uid, oto_poptionpurprice, oto_poptionprice) from odtProductOption where oto_uid = c.c_pouid and oto_cnt >= c.c_cnt and oto_view = 'Y' and oto_pcode = c.c_pcode) =
                                        concat(c.c_pcode, c.c_pouid, c.c_supply_optionprice, c.c_optionprice),
                                        'Y', 'N'
                                    ),
                                    if(
                                        (select concat(pao_pcode, pao_uid, pao_poptionpurprice, pao_poptionprice) from odtProductAddoption where pao_uid = c.c_pouid and pao_cnt >= c.c_cnt and pao_view = 'Y' and pao_pcode = c.c_pcode ) =
                                        concat(c.c_pcode, c.c_pouid, c.c_supply_optionprice, c.c_optionprice),
                                        'Y', 'N'
                                    )
                            )
                        )
                    ) = 'N'
                )
        ";
        $cr = _MQ_assoc($qur);

        # 변경된 상품 삭제 및 경고창 출력
        if(count($cr) > 0) {
            foreach($cr as $k=>$v) {
                _MQ_noreturn(" delete from `odtCart` where `c_pcode` = '{$v['c_pcode']}' and (`c_pouid` = '{$v['c_pouid']}' or `c_addoption_parent` = '{$v['c_pouid']}') and c_cookie = '{$_COOKIE['AuthShopCOOKIEID']}' ");
            }
            error_loc_msg('/?pn=shop.cart.list', '판매정보가 변경된 상품이 포함되어 삭제하였습니다.');
        }

		// 추가옵션만 남아있다면 삭제 처리
		$CartList = _MQ_assoc(" select * from odtCart where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ");
		foreach($CartList as $k=>$v) {

			$chk_addoption = _MQ("
				select
					SUM(subAddoptionY) as addoptionY , SUM(subAddoptionN)  as addoptionN
				from (
					select
						IF(c_is_addoption = 'Y' , 1 , 0 ) as subAddoptionY,
						IF(c_is_addoption != 'Y' , 1 , 0 ) as subAddoptionN
					from odtCart
					where c_pcode = '". $v['c_pcode'] ."' and c_cookie  = '".$_COOKIE["AuthShopCOOKIEID"]."'
				) as tbl
			");
			if($chk_addoption['addoptionN'] == 0 && $chk_addoption['addoptionY'] > 0) {
				_MQ_noreturn(" delete from `odtCart` where `c_pcode` = '{$v[c_pcode]}' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ");
			}
		}
    }

    /* --------------------------------------------------------------------------- */
    // Mysql에 테이블에 필드가 있는지 확인 (필드가 있다면 1반환)
    if(!function_exists('IsField')) {
        function IsField($Table, $Field) {

            $sql = ' show columns from ' . $Table . ' like \''.$Field.'\' ';
            $result = mysql_query($sql);

            if(@mysql_num_rows($result)) return true;
            else return false;
        }
    }

    /* --------------------------------------------------------------------------- */
    // Mysql 테이블의 정보 출력 (인덱스, 컬럼 리스트, 컬럼 데이터 반환)
    if(!function_exists('IsTableData')) {
        function IsTableData($Table) {

            // 초기값
            $ColnumNum = 0;
            $IndexNum = 0;

            // 테이블 인덱스 정보
            $IndexResult = mysql_query(' show index from ' . $Table);
            while($IndexData = mysql_fetch_assoc($IndexResult)){

                $Index[$IndexNum] = $IndexData;
                $IndexNum++;
            }


            // 테이블 컬럼 상세 정보
            $ColumnResult = mysql_query(' show columns from ' . $Table);
            while($ColumnData = mysql_fetch_assoc($ColumnResult)){

                $Column['list'][$ColnumNum] = $ColumnData['Field'];
                $Column['data'][$ColumnData['Field']] = $ColumnData;
                $Column['data'][$ColumnData['Field']]['number'] = $ColnumNum;

                $ColnumNum++;
            }


            // 정보를 모두 변수에 담음
            $list['index'] = $Index; // 인덱스 정보
            $list['columns'] = $Column; // 컬럼 정보


            return $list;
        }
    }

    // @ -- 테이블 확인
    if(!function_exists('IsTable')) {
        function IsTable($table)
        {
            $row_chk = _MQ("SHOW TABLES LIKE '".$table."'");
            if(count($row_chk) > 0){ return true; }
            else{ return false; }
        }
    }

    // @ -- 컬럼 추가 ::
    if(!function_exists('AddFeidlUpdate')) {
        function AddFeidlUpdate($table,$column_data = array())
        {

            if( count($column_data) < 1){ return false; }
            $field = $column_data['Field']; // 필드
            $type = $column_data['Type']; // 타입
            $default = $column_data['Default']; // 기본값
            $extra = $column_data['Extra']; // 기본함수
            $add_type = '';
            if( $column_data['Null'] == 'NO'){
                $add_type .= $default == '' ? " not null "  : " not null default '".$default."'  "  ;
            }else{
                $add_type .= $default == '' ? ' default null ' : " default '".$default."'  " ;
            }
            _MQ_noreturn("alter table ".$table." add ".$field."  ".$type." ".$add_type." ".$extra);
        }
    }


    // @ -- $member_table_name 와  $membersleep_table_name 테이블을 비교하여 동기화 시켜준다.
    if(!function_exists('UpdateMemberTable')) {
        function UpdateMemberTable()
        {
            // @ -- 고정 테이블 및 컬럼 셋팅
            $member_table_name = "odtMember";
            $membersleep_table_name = "odtMemberSleep";
            $arr_except_columns = array('ms_serialnum','ms_rdate','ms_sendchk'); // 휴면계정 컬럼 삭제 시 제외할 컬럼


            // @ -- 테이블이 있는지 검사
            if( IsTable($member_table_name) == false || IsTable($membersleep_table_name) == false){ return false; }

            // @ -- 회원,휴면회원 테이블 검사   // 1 차배열 정보 => [index], [columns][list] : 칼럼명, [columns][data] : 칼럼속성
            $is_table_member = IsTableData($member_table_name);
            $is_table_member_sleep = IsTableData($membersleep_table_name);
            $arr_update_data = array(); //  업데이트 컬럼과 삭제될 컬럼 배열 초기화


            if( count($is_table_member) < 1 || count($is_table_member_sleep) < 1 ) { return false; }
            // @ -- 회원테이블 컬럼정보에서 회원휴면 테이블 칼럼정보와 비교한다.
            foreach($is_table_member['columns']['list'] as $k=>$v){
                if( IsField($membersleep_table_name,$v) == true) { continue; }
                AddFeidlUpdate($membersleep_table_name,$is_table_member['columns']['data'][$v]); // $membersleep_table_name 테이블에 칼럼 추가
                $arr_update_data['add'][$v] = $is_table_member['columns']['data'][$v]; // 단순기록
            }

            // @ -- 휴면테이블 컬럼정보에서 회원테이블에 없는 컬럼은 삭제처리 한다.
             foreach($is_table_member_sleep['columns']['list'] as $k=>$v){
                if( IsField($member_table_name,$v) == true || in_array($v,$arr_except_columns) == true) { continue; }
                _MQ_noreturn(" ALTER TABLE  ".$membersleep_table_name." DROP  `".$v."` "); // 컬럼 삭제
                $arr_update_data['drop'][$v] = $is_table_member_sleep['columns']['data'][$v]; // 단순기록
             }

            return $arr_update_data ;
        }
    }