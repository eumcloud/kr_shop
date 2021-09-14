<?php
set_time_limit(0);
# LDD014
include_once('inc.php');

foreach($code as $k=>$v) {

	# 기본변수 준비
	$_mode = ($mode[$v]=='a'?'add':'modify');

	// 좌표처리
	$com_mapx = $com_mapy = '';
	if($com_juso[$v]) {

		$com_map = get_mapcoordinates($com_juso[$v]);
		$ex = explode(",", $com_map);
		$com_mapx = $ex[0];
		$com_mapy = $ex[1];
	}

	// 커스터머 및 배송상품 여부 처리
    $r = _MQ(" SELECT cName, address, com_mapx, com_mapy  FROM odtMember WHERE `id` = '" . $customerCode[$v] . "' ");
    $customerName = $r['cName'];
    $setup_delivery[$v] = $setup_delivery[$v] ? $setup_delivery[$v] : "N"; // 배송상품 여부

    // 자동주소마킹
    if($com_juso[$v] == '공급업체') {

        $com_juso[$v] = $r['address'];
        $com_mapx = $r['com_mapx'];
        $com_mapy = $r['com_mapy'];
    }

	// 중복 구매 불가 설정
	if($ipDistinct[$v] == 'Y') $ipDistinct[$v] = 1;
	else $ipDistinct[$v] = '';

	// 카테고리 처리
	//if($_mode == 'add') { // 추가 상품은 카테고리 insert

		$que = "
			INSERT INTO odtProductCategory (pct_pcode, pct_cuid) VALUES ('". $v ."', '". $catecode[$v] ."')
			ON DUPLICATE KEY UPDATE pct_pcode='". $v ."' , pct_cuid='". $catecode[$v] ."'
		";
		_MQ_noreturn($que);

//		// 카테고리 상품 개수 업데이트
//		update_catagory_product_count();
//	}
//	else if($_mode == 'modify' && $catecode_old[$v] != $catecode[$v]) { // 수정이지만 카테고리가 바뀐 경우
//
//		$que = "
//			update odtProductCategory set pct_pcode = '{$v}', pct_cuid = '{$catecode[$v]}' where pct_pcode = '{$v}' and pct_cuid = '{$catecode_old[$v]}'
//		";
//		_MQ_noreturn($que);
//
//		// 카테고리 상품 개수 업데이트
//		update_catagory_product_count();
//	}

	switch ($_mode) {

		// - 상품추가 ---
		case "add":

			$que="insert into odtProduct set
					code			= '" . $v . "',
					parent_code		= '" . $parent_code[$v] . "',
					customerCode	= '" . $customerCode[$v] . "',
					customerName	= '" . $customerName . "',
					mainName		= '" . $mainName[$v] . "',
					name			= '" . $name[$v] . "',
					purPrice		= '" . $purPrice[$v] . "',
					commission		= '" . $commission[$v] . "',
					comSaleType		= '" . $comSaleType[$v] . "',
					price			= '" . $price[$v] . "',
					price_per		= '" . $price_per[$v] . "',
					coupon_title	= '" . $coupon_title[$v] . "',
					coupon_price	= '" . $coupon_price[$v] . "',
					price_org		= '" . $price_org[$v] . "',
					mainPrice		= '" . $mainPrice[$v] . "',
					point			= '" . $point[$v] . "',
					stock			= '" . $stock[$v] . "',
					buy_limit		= '" . $buy_limit[$v] . "',
					saleNum			= '" . $saleNum[$v] . "',
					md_name			= '" . $md_name[$v] . "',
					sale_type		= '" . $sale_type[$v] . "',
					sale_date		= '" . $sale_date[$v] . "',
					sale_dateh		= '" . $sale_dateh[$v] . "',
					sale_datem		= '" . $sale_datem[$v] . "',
					sale_enddate	= '" . $sale_enddate[$v] . "',
					sale_enddateh	= '" . $sale_enddateh[$v] . "',
					sale_enddatem	= '" . $sale_enddatem[$v] . "',
					del_price_com	= '" . $del_price_com[$v] . "',
					del_price		= '" . $del_price[$v] . "',
					del_type		= '" . $del_type[$v] . "',
					del_limit		= '" . $del_limit[$v] . "',
					delchk			= '" . $delchk[$v] . "',
					main_img		= '" . $main_img[$v] . "',
					prolist_img		= '" . $prolist_img[$v] . "',
					prolist_img2	= '" . $prolist_img2[$v] . "',
					short_comment	= '" . $short_comment[$v] . "',
					message			= '" . $message[$v] . "',
					guestDisabled	= '" . $guestDisabled[$v] . "',
					ipDistinct		= '" . $ipDistinct[$v] . "',
					talkDisabled	= '" . $talkDisabled[$v] . "',
					bankDisabled	= '" . $bankDisabled[$v] . "',
					seeDisabled		= '" . $seeDisabled[$v] . "',
					isSaleCnt		= '" . $isSaleCnt[$v] . "',
					saleCnt			= '" . $saleCnt[$v] . "',
					saleCntMax		= '" . $saleCntMax[$v] . "',
					setup_subscribe	= '" . $sum_setup_subscribe[$v] . "',
					setup_delivery	= '" . $setup_delivery[$v] . "',
					rssarea1		= '" . $rssarea1[$v] . "',
					rssarea2		= '" . $rssarea2[$v] . "',
					rsscate			= '" . $rsscate[$v] . "',
					expire			= '" . $expire[$v] . "',
					com_juso		= '" . $com_juso[$v] . "',
					com_mapx		= '" . $com_mapx . "',
					com_mapy		= '" . $com_mapy . "',
					isNow			= '" . $isNow[$v]."',
					pro_idx			= '" . $pro_idx[$v]."',
					option1_title	= '" . $option1_title[$v]."',
					option2_title	= '" . $option2_title[$v]."',
					option3_title	= '" . $option3_title[$v]."',

					thema			= '" . $app_thema[$v] ."',
					bestview		= '" . $bestview[$v] ."',
					p_view			= '" . $p_view[$v] ."',
					relation_auto	= '" . $relation_auto[$v]."',
					inputDate		= now()";
			//JJC003 - 묶음배송 관련  - 항목추가
			$res = mysql_query($que) or die(mysql_error());
			$serialnum = mysql_insert_id();

			// option_type_chk	= '" . $option_type_chk[$v]."',

			// - text 연동정보 입력 => addslashes는 _text_info_insert 함수에서 실행
			_text_info_insert("odtProduct", $serialnum, "comment2", $comment2[$v], "ignore");// 상품 상세설명
			_text_info_insert("odtProduct", $serialnum, "comment2_m", $comment2_m[$v], "ignore");// 상품 상세설명(모바일) LDD005
			_text_info_insert("odtProduct", $serialnum, "comment_proinfo", $comment_proinfo[$v], "ignore");// 상품사용정보
			_text_info_insert("odtProduct", $serialnum, "comment_useinfo", $comment_useinfo[$v], "ignore");// 업체이용정보
			_text_info_insert("odtProduct", $serialnum, "comment3", $comment3[$v], "ignore");// 주문확인서 주의사항
			_text_info_insert("odtProduct", $serialnum, "p_relation", $p_relation[$v], "ignore");// 관련상품 코드 - 구분자 /

			// 카테고리 상품 개수 업데이트
			//update_catagory_product_count();

		break;
		// - 상품추가 ---

		// - 상품수정 ---
		case "modify":

			$que="update odtProduct set
					parent_code		= '" . $parent_code[$v] . "',
					customerCode	= '" . $customerCode[$v] . "',
					customerName	= '" . $customerName . "',
					mainName		= '" . $mainName[$v] . "',
					name			= '" . $name[$v] . "',
					purPrice		= '" . $purPrice[$v] . "',
					commission		= '" . $commission[$v] . "',
					comSaleType		= '" . $comSaleType[$v] . "',
					price			= '" . $price[$v] . "',
					price_per		= '" . $price_per[$v] . "',
					coupon_title	= '" . $coupon_title[$v] . "',
					coupon_price	= '" . $coupon_price[$v] . "',
					price_org		= '" . $price_org[$v] . "',
					mainPrice		= '" . $mainPrice[$v] . "',
					point			= '" . $point[$v] . "',
					stock			= '" . $stock[$v] . "',
					buy_limit		= '" . $buy_limit[$v] . "',
					saleNum			= '" . $saleNum[$v] . "',
					md_name			= '" . $md_name[$v] . "',
					sale_type		= '" . $sale_type[$v] . "',
					sale_date		= '" . $sale_date[$v] . "',
					sale_dateh		= '" . $sale_dateh[$v] . "',
					sale_datem		= '" . $sale_datem[$v] . "',
					sale_enddate	= '" . $sale_enddate[$v] . "',
					sale_enddateh	= '" . $sale_enddateh[$v] . "',
					sale_enddatem	= '" . $sale_enddatem[$v] . "',
					del_price_com	= '" . $del_price_com[$v] . "',
					del_price		= '" . $del_price[$v] . "',
					del_type		= '" . $del_type[$v] . "',
					del_limit		= '" . $del_limit[$v] . "',
					delchk			= '" . $delchk[$v] . "',
					main_img		= '" . $main_img[$v] . "',
					prolist_img		= '" . $prolist_img[$v] . "',
					prolist_img2	= '" . $prolist_img2[$v] . "',
					short_comment	= '" . $short_comment[$v] . "',
					message			= '" . $message[$v] . "',
					guestDisabled	= '" . $guestDisabled[$v] . "',
					ipDistinct		= '" . $ipDistinct[$v] . "',
					talkDisabled	= '" . $talkDisabled[$v] . "',
					bankDisabled	= '" . $bankDisabled[$v] . "',
					seeDisabled		= '" . $seeDisabled[$v] . "',
					isSaleCnt		= '" . $isSaleCnt[$v] . "',
					saleCnt			= '" . $saleCnt[$v] . "',
					setup_subscribe	= '" . $sum_setup_subscribe[$v] . "',
					setup_delivery	= '" . $setup_delivery[$v] . "',
					rssarea1		= '" . $rssarea1[$v] . "',
					rssarea2		= '" . $rssarea2[$v] . "',
					rsscate			= '" . $rsscate[$v] . "',
					expire			= '" . $expire[$v] . "',
					com_juso		= '" . $com_juso[$v] . "',
					com_mapx		= '" . $com_mapx . "',
					com_mapy		= '" . $com_mapy . "',
					pro_idx			= '" . $pro_idx[$v]."',
					option1_title	= '" . $option1_title[$v]."',
					option2_title	= '" . $option2_title[$v]."',
					option3_title	= '" . $option3_title[$v]."',
					option_type_chk	= '" . $option_type_chk[$v]."',
					isNow			= '" . $isNow[$v]."',
					thema			= '" . $app_thema[$v] ."',
					bestview		= '" . $bestview[$v] ."',
					p_view			= '" . $p_view[$v] ."',
					relation_auto	= '" . $relation_auto[$v]."',
					saleCntMax		= '" . $saleCntMax[$v] . "'
					where
					code			= '" . $v . "'";
			//JJC003 - 묶음배송 관련  - 항목추가
			$res=mysql_query($que) or die(mysql_error());


			// - text 연동정보 입력 => addslashes는 _text_info_insert 함수에서 실행
			$que = " select * from odtProduct where code = '".$v."' ";
			$r = _MQ($que);
			_text_info_insert("odtProduct", $r['serialnum'], "comment2", $comment2[$v], "ignore");// 상품 상세설명
			_text_info_insert("odtProduct", $r['serialnum'], "comment2_m", $comment2_m[$v], "ignore");// 상품 상세설명(모바일) LDD005
			_text_info_insert("odtProduct", $r['serialnum'], "comment_proinfo", $comment_proinfo[$v], "ignore");// 상품사용정보
			_text_info_insert("odtProduct", $r['serialnum'], "comment_useinfo", $comment_useinfo[$v], "ignore");// 업체이용정보
			_text_info_insert("odtProduct", $r['serialnum'], "comment3", $comment3[$v], "ignore");// 주문확인서 주의사항
			_text_info_insert("odtProduct", $r['serialnum'], "p_relation", $p_relation[$v], "ignore");// 관련상품 코드 - 구분자 /

			// 카테고리 상품 개수 업데이트
			//update_catagory_product_count();
		break;
		// - 상품수정 ---

	} // case END

}

// 카테고리 상품 개수 업데이트
update_catagory_product_count();

error_loc_msg("_product.list.php" , "업로드 처리가 완료 되었습니다.");