<?PHP
	if( !$_path_str ) {
		if( @file_exists("../include/config_database.php") ) {
			$_path_str = "..";
		}
		else {
			$_path_str = ".";
		}
	}

	include_once(dirname(__FILE__)."/../../include/inc.php");

	// --> 옵션/장바구니/비회원 구매를 위한 쿠키 적용여부 파악
	cookie_chk();

	switch($mode){

		// 선택삭제 (옵션)
		case "select_option_onlydelete":
			// 상품코드 추출
			$_product = _MQ(" select c_pcode from odtCart where c_uid = '".$cuid."' ");
			$code = $_product[c_pcode];
			$que = "delete from odtCart where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_uid = '".$cuid."' ";
			_MQ_noreturn($que);

			// 삭제후 남은 옵션중 필수 옵션이 없으면 모든 추가옵션 삭제
			$no_addoption_cnt = _MQ(" select count(*) as cnt from odtCart where c_cookie ='". $_COOKIE["AuthShopCOOKIEID"] ."' and c_pcode = '".$code."' and c_is_addoption = 'N' ");
			if($no_addoption_cnt[cnt]==0) {
				_MQ_noreturn(" delete from odtCart where c_cookie ='". $_COOKIE["AuthShopCOOKIEID"] ."' and c_pcode = '".$code."' ");
			}

			error_loc("/?pn=shop.cart.list");
			break;

		// 선택삭제 (상품)
		case "select_onlydelete":
			// 상품코드 추출
			$_product = _MQ(" select c_pcode from odtCart where c_uid = '".$cuid."' ");
			$code = $_product['c_pcode'];
			$que = "delete from odtCart where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_uid = '".$cuid."' ";
			_MQ_noreturn($que);

			// 삭제후 남은 옵션중 필수 옵션이 없으면 모든 추가옵션 삭제
			$no_addoption_cnt = _MQ(" select count(*) as cnt from odtCart where c_cookie ='". $_COOKIE["AuthShopCOOKIEID"] ."' and c_pcode = '".$code."' and c_is_addoption = 'N' ");
			if($no_addoption_cnt['cnt']==0) {
				_MQ_noreturn(" delete from odtCart where c_cookie ='". $_COOKIE["AuthShopCOOKIEID"] ."' and c_pcode = '".$code."' ");
			}

			error_loc("/?pn=shop.cart.list");
			break;


		// 선택수량변경
		case "select_modify":
			if(!$app_cnt) $app_cnt = $_ccnt[$cuid];
			if( $app_cnt <= 0 ) {
				error_msg("수정하실 수량은 0보다 커야 합니다.");
			}



			//---------- 상품 수량 select 값. ----------
			$buy_max = 200; // 최고 구매갯수 설정
			$que = "
				select c.*,p.*,oto.oto_cnt, otoa.pao_cnt from odtCart as c
				inner join odtProduct as p on (p.code=c.c_pcode)
				left join odtProductOption as oto on (oto.oto_uid = c.c_pouid)
				left join odtProductAddoption as otoa on (otoa.pao_uid = c.c_pouid)
				where c.c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_uid = '".$cuid."'
			";
			$r = _MQ($que);
			$r[oto_cnt] = ($r[c_is_addoption] == 'Y' ? $r[pao_cnt] : $r[oto_cnt]);// 추가 옵션 재고판별
			$buy_limit = $r[buy_limit] ? min($r[c_option1] ? $r[oto_cnt] : $r[stock] ,$r[buy_limit]) : min($r[c_option1] ? $r[oto_cnt] : $r[stock] ,$buy_max); // 구매제한이 없으면 재고만큼만 선택할수 있게 하되 max는 200
			if( $app_cnt > $buy_limit ) {
				error_msg($buy_limit . "개 이상 구매할 수 없습니다.");
			}
			//---------- 상품 수량 select 값. ----------

			$tmpVar = _MQ("select point,sale_enddate,c_price,c_optionprice from odtProduct as p inner join odtCart as c on (c.c_pcode = p.code) where c.c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c.c_uid = '".$cuid."'");
			$que = "update odtCart set c_cnt='".$app_cnt."' , c_point = '". ( ($tmpVar['c_price'] + $tmpVar['c_optionprice']) * $app_cnt * $tmpVar['point'] / 100  )."' where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_uid = '".$cuid."' ";
			_MQ_noreturn($que);

			/* ------------ 주문 상품 재고 체크 ------------------*/
				
			// 주문을 위한 상품 재고 체크
			// 카트에 담긴 상품 수량을 현재 재고와 확인하여. 만약 보유 수량보다 주문량이 더 많을시,
			// 카트에 담긴 상품 수량을 강제 조정한다.
			// 함수 리턴값 (품절 : soldout , 수량이 부족 : notenough , 그외 ok)
			// 그후 엑션은 페이지에 따라서 처리한다.
			switch(order_product_stock_check($_COOKIE["AuthShopCOOKIEID"])) {
				case "soldout" :
					error_loc_msg("/?pn=shop.cart.list","장바구니 담긴 상품중 품절 된 상품이 있습니다.");
					break;
				case "notenough" :
					error_loc_msg("/?pn=shop.cart.list","해당 상품의 재고량이 부족합니다.");
					break;
				case "ok" :
					break;
			}

			/* ------------ // 주문 상품 재고 체크 ------------------*/

			error_loc("/?pn=shop.cart.list");
			break;




		// 다수선택삭제
		case "select_delete":
			if( sizeof($_code) == 0 ) {
				error_msg("1개이상 선택해주시기 바랍니다.");
			}

			// 값이 key 에 있는지 val 에 있는지 체크하여 처리한다.
			if($_code[0]) $_code_array = implode("','" , $_code);
			else $_code_array = implode("','" , array_keys($_code));
			
			$que = "delete from odtCart where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_pcode in ('".$_code_array."') ";
			_MQ_noreturn($que);
			error_loc("/?pn=shop.cart.list");
			break;



		// - 다수 선택 추가  ---
		case "select_add":

			if( sizeof($pcode_array) == 0 ) {
				error_msg("1개이상 선택해주시기 바랍니다.");
			}

			for($i=0;$i<count($pcode_array);$i++) {
				$pcode = $pcode_array[$i];

				if( !$pass_type ) {
					$pass_type = "cart";
				}

				// 중복구매 체크
				$row_product = get_product_info($pcode);
				if($row_product[ipDistinct]) {
					$orderCheckTmp = _MQ_result("
						select count(*) from odtOrderProduct as op
						inner join odtOrder as o on (o.ordernum=op.op_oordernum and o.paystatus='Y' and o.canceled='N' and (o.ip='".$_SERVER[REMOTE_ADDR]."' or o.orderid = '".get_userid()."'))
						where op.op_pcode ='".$row_product[code]."'
					");
					if($orderCheckTmp > 0) {
						error_msgall("중복구입이 불가능한 상품이 포함되어 있습니다.","/");
						exit;
					}
					else{
						// SSJ: 2017-10-12 중복구입이 불가능한 상품 체크후 무통장/가상계좌로 주문된 건이 있는지 체크 
						$orderCheckTmp = _MQ_result("
							select count(*) from odtOrderProduct as op
							inner join odtOrder as o on (o.ordernum=op.op_oordernum and (o.paystatus='N' and o.paymethod in ('B','V')) and o.canceled='N' and (o.ip='".$_SERVER['REMOTE_ADDR']."' or o.orderid = '".get_userid()."'))
							where op.op_pcode ='".$row_product['code']."'
						");
						if($orderCheckTmp > 0) { error_msgall("중복구입이 불가능한 상품입니다.\\n무통장/가상계좌 주문을 확인해주시기 바랍니다.","/"); exit; }
					}
				}	

				// 이미 담긴 상품인지 체크
				$cnt_tmp = _MQ("select count(*) as cnt from odtCart  where c_pcode = '". $pcode ."' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and  c_pouid = '0'");
				if($cnt_tmp[cnt] > 0) continue;

				$sque = "
					insert odtCart set
						c_pcode = '". $pcode ."'
					, c_cnt = '1'
					, c_pouid = '0'
					".$sque_tmp."
					, c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
					, c_rdate = now()
					, c_supply_price = (select purPrice from odtProduct where code='".$pcode."')
					, c_price = (select price from odtProduct where code='".$pcode."')
					, c_point = (select price*(point/100) from odtProduct where code='".$pcode."')

				";

				_MQ_noreturn($sque);

			}	// end for



			if( $pass_type == "order" ) {
				error_loc("/?pn=shop.order.form");
			}
			else {
				error_loc("/?pn=shop.cart.list");
			}
			break;




		// - 추가 (상세페이지로부터 넘겨져옴) ---
		case "add":
		
			// 넘겨져온 변수
			//pcode=$code&pass_type=type(order:주문하기/cart:장바구니)
			$pcode = nullchk($pcode , "상품을 선택해주시기 바랍니다.");
			if( !$pass_type ) {
				$pass_type = "cart";
			}

			// LDD019 {
			if($row_setup['none_member_buy'] == 'N' && !is_login()) {

				error_msg("로그인 후 이용 가능합니다.");
			}
			// } LDD019

			// 중복구매 체크
			$row_product = get_product_info($pcode);
			if($row_product[ipDistinct]) {
				$orderCheckTmp = _MQ_result("
					select count(*) from odtOrderProduct as op
					inner join odtOrder as o on (o.ordernum=op.op_oordernum and o.paystatus='Y' and o.canceled='N' and (o.ip='".$_SERVER[REMOTE_ADDR]."' or o.orderid = '".get_userid()."'))
					where op.op_pcode ='".$row_product[code]."'
				");
				if($orderCheckTmp > 0) {
					error_msgall("중복구입이 불가능한 상품입니다.","/");
					exit;
				}
				else{
					// SSJ: 2017-10-12 중복구입이 불가능한 상품 체크후 무통장/가상계좌로 주문된 건이 있는지 체크 
					$orderCheckTmp = _MQ_result("
						select count(*) from odtOrderProduct as op
						inner join odtOrder as o on (o.ordernum=op.op_oordernum and (o.paystatus='N' and o.paymethod in ('B','V')) and o.canceled='N' and (o.ip='".$_SERVER['REMOTE_ADDR']."' or o.orderid = '".get_userid()."'))
						where op.op_pcode ='".$row_product['code']."' and op.op_cancel = 'N'
					");
					if($orderCheckTmp > 0) {
						error_msg("중복구입이 불가능한 상품입니다.\\n무통장/가상계좌 주문을 확인해주시기 바랍니다.");
					}
				}
			}	

			_MQ_noreturn(" update odtCart set c_direct = 'N' where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ");// 선택 구매 2015-12-04 LDD		

			// 옵션 없는 경우
			if( $option_select_type == "nooption" ) {
				// 장바구니 넣기

				// 이미 담긴 상품인지 체크
				$cnt_tmp = _MQ("select count(*) as cnt from odtCart  where c_pcode = '". $pcode ."' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and  c_pouid = '0'");
				if($cnt_tmp[cnt] > 0) error_frame_loc_msg("/?pn=shop.cart.list","이미 장바구니에 담긴 상품입니다.");

				$c_cnt = $option_select_cnt > 1 ? $option_select_cnt : 1;

				// 구매제한 초과
				if( $row_product[buy_limit] > 0 && $c_cnt > $row_product[buy_limit]) {
					error_msg("선택하신 상품은 " . $row_product[buy_limit] . "까지 구매가 가능하십니다.");
				}

				$sque = "
					insert odtCart set
					  c_pcode = '". $pcode ."'
					, c_cnt = '".$c_cnt."'
					, c_pouid = '0'
					".$sque_tmp."
					, c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
					, c_rdate = now()
					, c_supply_price = (select purPrice from odtProduct where code='".$pcode."')
					, c_price = (select price from odtProduct where code='".$pcode."')
					, c_point = ((select price*(point/100) from odtProduct where code='".$pcode."')*".$c_cnt.")
					, c_is_addoption = 'N'
					, c_direct			= '".($pass_type=='order'?'Y':'N')."'
				";


				_MQ_noreturn($sque);
			}
			else {
				// 선택옵션 정보 추출
				$que = "select * from odtTmpProductOption where otpo_mid='".$_COOKIE["AuthShopCOOKIEID"]."'";
				
				$res = _MQ_assoc($que);
				foreach( $res as $k=>$v ){
					// 같은 상품은 삭제한다
					_MQ_noreturn("delete from odtCart where c_pcode = '". $pcode ."' and c_pouid = '".$v[otpo_pouid]."' 
						and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_is_addoption = '".$v[otpo_is_addoption]."' ");

					$tmpVar = _MQ("select * from odtProduct where code = '".$pcode."'");

					// 판매종료된 상품은 통과시킨다.
					if($tmpVar[sale_enddate] < date('Y-m-d') && $tmpVar[sale_type] == 'T') continue;

					// 구매제한 초과 , 추가옵션일경우 구매제한 제외
					if($row_product[buy_limit] > 0 && $v[otpo_cnt] > $row_product[buy_limit] && $v[otpo_is_addoption] <> "Y") {
						error_msg("선택하신 상품은 " . $row_product[buy_limit] . "까지 구매가 가능하십니다.");
					}

					// 장바구니 넣기
					$sque = "
						insert odtCart set
							  c_pcode = '". $pcode ."'
							, c_option1 = '". $v[otpo_poptionname] ."'
							, c_option2 = '". $v[otpo_poptionname2]."'
							, c_option3 = '". $v[otpo_poptionname3]."'
							, c_cnt = '".$v[otpo_cnt]."'
							, c_pouid = '".$v[otpo_pouid]."'
							".$sque_tmp."
							, c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
							, c_rdate = now()

							, c_price = '". $v[otpo_pprice]."'
							, c_supply_price = '". $v[otpo_ppurprice]."'
							, c_optionprice = '". $v[otpo_poptionprice]."'
							, c_supply_optionprice = '". $v[otpo_poptionpurprice]."'
							, c_point = '". ( ($v[otpo_pprice] + $v[otpo_poptionprice]) * $v[otpo_cnt] * $tmpVar[point] / 100  )."'

							, c_is_addoption = '".$v[otpo_is_addoption]."'
							, c_addoption_parent	= '".$v['otpo_addoption_parent']."'
							, c_direct				= '".($pass_type=='order'?'Y':'N')."'
					";
					_MQ_noreturn($sque);
				}
				_MQ_noreturn("delete from odtTmpProductOption where otpo_uid ='". $_COOKIE["AuthShopCOOKIEID"] ."' ");
			}

			if( $pass_type == "order" ) {
				error_loc("/?pn=shop.order.form");
			}
			else {
				error_loc("/?pn=shop.cart.list");
			}
			break;

			// 선택 구매 2015-12-04 LDD
			case "select_buy":
				if( count($_code) > 0 ) {
					_MQ_noreturn(" update odtCart set c_direct = 'Y' where c_pcode in ('".implode("','",$_code)."') and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ");
				}
				error_frame_loc("/?pn=shop.order.form");
			break;


	}

?>