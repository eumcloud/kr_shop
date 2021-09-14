<?php

	include_once("inc.php");


	// --이미지 처리 ---
	$dir ="../../../upfiles/product";


	// - 입력수정 사전처리 ---
	if(in_array($_mode, array("add" , "modify"))) {

		// 테마 변수 설정
		$app_thema = (sizeof($_thema) > 0?implode(",", array_values($_thema)):""); 

		// 좌표처리
		if( $com_juso && !$com_mapx && !$com_mapy ) {
			$com_map = get_mapcoordinates($com_juso);
			$ex = explode(",", $com_map);
			$com_mapx = $ex[0];
			$com_mapy = $ex[1];
		}

		$r = _MQ(" SELECT cName FROM odtMember WHERE `id` = '" . $customerCode . "' ");
		$customerName = $r[cName];
		$setup_delivery = $setup_delivery ? $setup_delivery : "N"; // 배송상품 여부

	}


	switch ($_mode) {


		// - 상품수정 ---
		case "modify":
			$que="
				update odtProduct set
					customerCode	= '" . $customerCode . "',
					customerName	= '" . $customerName . "',
					v_color			= '".$v_color."',
					bestview		= '". $bestview ."',
					p_view			= '". $p_view ."',
					pro_idx			= '".$pro_idx."',
					sale_type		= '".$sale_type."',
					sale_date		= '" . $sale_date . "',
					sale_dateh		= '" . $sale_dateh . "',
					sale_datem		= '" . $sale_datem . "',
					sale_enddate	= '" . $sale_enddate . "',
					sale_enddateh	= '" . $sale_enddateh . "',
					sale_enddatem	= '" . $sale_enddatem . "',
					name			= '" . $name . "',
					md_name			= '" . $md_name . "',
					purPrice		= '" . $purPrice . "',
					commission		= '" . $commission . "',
					comSaleType		= '" . $comSaleType . "',
					price			= '" . $price . "',
					price_per		= '" . $price_per . "',
					price_org		= '" . $price_org . "',
					setup_delivery	= '"  . $setup_delivery . "',
					del_price		= '" . $del_price . "',
					del_type		= '" . $del_type . "',
					del_limit		= '" . $del_limit . "',
					coupon_title	= '" . $coupon_title . "',
					coupon_price	= '" . $coupon_price . "',
					option1_title	= '".$option1_title."',
					option2_title	= '".$option2_title."',
					option3_title	= '".$option3_title."',
					thema			= '". $app_thema ."',
					guestDisabled	=   '" . $guestDisabled . "',
					ipDistinct		=   '" . $ipDistinct . "',
					point			= '" . $point . "',
					stock			= '" . $stock . "',
					buy_limit		= '" . $buy_limit . "',
					saleCnt			=   '" . $saleCnt . "',
					short_comment	= '" . $short_comment . "',
					com_juso		= '"  . $com_juso . "',
					com_mapx		= '"  . $com_mapx . "',
					com_mapy		= '"  . $com_mapy . "',
					expire			= '" . $expire . "',
					p_icon			= '". @implode(",",$_icon)."'
				where 
					code				= '" . $code . "'
			";
			//JJC003 - 묶음배송 관련  - 항목추가
			_MQ_noreturn($que);

			// 카테고리 상품 개수 업데이트
			update_catagory_product_count();

			error_loc("_product.form.php?_mode=modify&code=${code}&_PVSC=${_PVSC}");

			break;
		// - 상품수정 ---







		// - 상품삭제 ---
		case "delete":

			$que = " select * from odtProduct where code = '".$code."' ";
			$r = _MQ($que);

			// 상품 옵션삭제
			_MQ_noreturn("delete from odtProductOption where oto_pcode='". $code ."' ");

			// - text 연동정보 삭제
			_text_info_delete("odtProduct", $r['serialnum'], "comment2");// 상품 상세설명
			_text_info_delete("odtProduct", $r['serialnum'], "comment2_m");// 상품 상세설명(모바일) LDD005
			_text_info_delete("odtProduct", $r['serialnum'], "comment_proinfo");// 상품사용정보
			_text_info_delete("odtProduct", $r['serialnum'], "comment_useinfo");// 업체이용정보
			_text_info_delete("odtProduct", $r['serialnum'], "comment3");// 주문확인서 주의사항
			_text_info_delete("odtProduct", $r['serialnum'], "p_relation");// 관련상품 코드 - 구분자 /

			// - 상품 이미지 삭제 ---
			_PhotoDel($dir, $r['main_img']);
			_PhotoDel($dir, $r['prolist_img']);
			_PhotoDel($dir, $r['prolist_img2']);

			_PhotoDel($dir, $arr_product_size["장바구니"][0]."x".$arr_product_size["장바구니"][1]."_".$r['prolist_img']);
			_PhotoDel($dir, $arr_product_size["최근본상품"][0]."x".$arr_product_size["최근본상품"][1]."_".$r['prolist_img2']);
			_PhotoDel($dir, $arr_product_size["주문확인"][0]."x".$arr_product_size["주문확인"][1]."_".$r['prolist_img2']);
			// - 상품 이미지 삭제 ---

			// 상품정보삭제
			_MQ_noreturn("delete from odtProduct where code='". $code ."' ");

			// 카테고리 상품 개수 업데이트
			update_catagory_product_count();

			error_loc("_product.list.php?" . enc('d' , $_PVSC));

			break;






		// - 선택순위수정 ---
		case "mass_sort":
			if(sizeof($chk_pcode) == 0 ) {error_msg("잘못된 접근입니다.");}
			foreach($chk_pcode as $k=>$v) {
				if($v) {_MQ_noreturn(" update odtProduct set pro_idx = '" . $chk_idx[$v] . "' where  code = '" . $v . "' ");}
			}			
			error_loc("_product.list.php?".enc('d' , $_PVSC));
			break;
		// - 선택순위수정 ---
	}
?>