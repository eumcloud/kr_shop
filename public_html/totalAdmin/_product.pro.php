<?PHP
ini_set('memory_limit','512M'); // 2020-02-20 SSJ :: 파일용량이 클경우 썸네일 생성 시 메모리 오류 방지
include_once("inc.php");

// - 입력수정 사전처리 ---
if(in_array($_mode, array("add" , "modify"))) {

	// --이미지 처리 ---
	$dir ="../upfiles/product";

	if(!trim($comment2)) { error_msg('상품 상세설명을 입력하세요.'); exit(); }
	if(!trim($comment3) && $setup_delivery <> "Y") { error_msg('주문확인서 주의사항을 입력하세요.'); exit(); }

	# D015
	$main_img_name = (strpos($main_img, '//') !== false?$main_img:_PhotoPro($dir, "main_img"));
	$prolist_img_name = (strpos($prolist_img, '//') !== false?$prolist_img:_PhotoPro($dir, "prolist_img"));
	$prolist_img2_name = (strpos($prolist_img2, '//') !== false?$prolist_img2:_PhotoPro($dir, "prolist_img2"));

	// -- 썸네일 적용 ---
	if($_FILES["prolist_img"]["size"] > 0) {
		app_product_thumbnail($dir, $prolist_img_name, "장바구니");
		// GD2 라이브러리가 있다면 대표색상 추출
		if (extension_loaded('gd') && function_exists('gd_info')) {
			$colors = array();
			$delta = 24;
			$reduce_brightness = true;
			$reduce_gradients = true;
			$num_results = 1;
			include_once($_SERVER['DOCUMENT_ROOT']."/include/class.getcolor.php");
			$ex = new GetMostCommonColors();
			$colors = $ex->Get_Color($_SERVER['DOCUMENT_ROOT'].IMG_DIR_PRODUCT.$prolist_img_name, $num_results, $reduce_brightness, $reduce_gradients, $delta);
			foreach($colors as $hex=>$count) { $v_color = $hex; }
		}
	}
	if($_FILES["prolist_img2"]["size"] > 0) {
		app_product_thumbnail($dir, $prolist_img2_name, "최근본상품");
	}
	if($_FILES["prolist_img2"]["size"] > 0) {
		app_product_thumbnail($dir, $prolist_img2_name, "주문확인");
	}
	// -- 썸네일 적용 ---

	// 테마 변수 설정
	$app_thema = (sizeof($_thema) > 0?implode(",", array_unique(array_values($_thema))):"");

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


	// - 상품추가 ---
	case "add":

		// 상시상품일 경우 판매시작일을 등록일로 설정
		if( $sale_type == 'A' ) { $sale_date = date('Y-m-d'); }

		$que="insert into odtProduct set
				code			= '" . $code . "',
				parent_code		= '" . $parent_code . "',
				customerCode	= '" . $customerCode . "',
				customerName	= '" . $customerName . "',
				mainName		= '" . $mainName . "',
				name			= '" . $name . "',
				purPrice		= '" . $purPrice . "',
				commission		= '" . $commission . "',
				comSaleType		= '" . $comSaleType . "',
				price			= '" . $price . "',
				price_per		= '" . $price_per . "',
				coupon_title	= '" . $coupon_title . "',
				coupon_price	= '" . $coupon_price . "',
				price_org		= '" . $price_org . "',
				mainPrice		= '" . $mainPrice . "',
				point			= '" . $point . "',
				stock			= '" . $stock . "',
				buy_limit		= '" . $buy_limit . "',
				saleNum			= '" . $saleNum . "',
				md_name			= '" . $md_name . "',
				sale_type		= '".$sale_type."',
				sale_date		= '" . $sale_date . "',
				sale_dateh		= '" . $sale_dateh . "',
				sale_datem		= '" . $sale_datem . "',
				sale_enddate	= '" . $sale_enddate . "',
				sale_enddateh	= '" . $sale_enddateh . "',
				sale_enddatem	= '" . $sale_enddatem . "',
				del_price_com	= '" . $del_price_com . "',
				del_price		= '" . $del_price . "',
				del_type		= '" . $del_type . "',
				del_limit		= '" . $del_limit . "',
				delchk			= '" . $delchk . "',
				main_img		= '" . $main_img_name . "',
				prolist_img		= '" . $prolist_img_name . "',
				prolist_img2	= '" . $prolist_img2_name . "',
				short_comment	= '" . $short_comment . "',
				message			= '" . $message . "',
				guestDisabled	= '" . $guestDisabled . "',
				ipDistinct		= '" . $ipDistinct . "',
				talkDisabled	= '" . $talkDisabled . "',
				bankDisabled	= '" . $bankDisabled . "',
				seeDisabled		= '" . $seeDisabled . "',
				isSaleCnt		= '" . $isSaleCnt . "',
				saleCnt			= '" . $saleCnt . "',
				saleCntMax		= '" . $saleCntMax . "',
				setup_subscribe	= '"  . $sum_setup_subscribe . "',
				setup_delivery	= '"  . $setup_delivery . "',
				rssarea1		= '" . $rssarea1 . "',
				rssarea2		= '" . $rssarea2 . "',
				rsscate			= '" . $rsscate . "',
				expire			= '" . $expire . "',
				com_juso		= '"  . $com_juso . "',
				com_mapx		= '"  . $com_mapx . "',
				com_mapy		= '"  . $com_mapy . "',
				isNow			= '".$isNow."',
				pro_idx			= '".$pro_idx."',
				option1_title	= '".$option1_title."',
				option2_title	= '".$option2_title."',
				option3_title	= '".$option3_title."',
				option_type_chk	= '".$option_type_chk."',
				thema			= '". $app_thema ."',
				bestview		= '". $bestview ."',
				p_view			= '". $p_view ."',
				p_icon			= '". @implode(",",$_icon)."',
				relation_auto	= '".$relation_auto."',
				v_color			= '".$v_color."',
				inputDate		= now()";
		//JJC003 - 묶음배송 관련  - 항목추가
		$res = mysql_query($que) or die(mysql_error());
		$serialnum = mysql_insert_id();

		// - text 연동정보 입력 => addslashes는 _text_info_insert 함수에서 실행
		_text_info_insert("odtProduct", $serialnum, "comment2", $comment2, "ignore");// 상품 상세설명
		_text_info_insert("odtProduct", $serialnum, "comment2_m", $comment2_m, "ignore");// 상품 상세설명(모바일) LDD005
		_text_info_insert("odtProduct", $serialnum, "comment_proinfo", $comment_proinfo, "ignore");// 상품사용정보
		_text_info_insert("odtProduct", $serialnum, "comment_useinfo", $comment_useinfo, "ignore");// 업체이용정보
		_text_info_insert("odtProduct", $serialnum, "comment3", $comment3, "ignore");// 주문확인서 주의사항
		_text_info_insert("odtProduct", $serialnum, "p_relation", $p_relation, "ignore");// 관련상품 코드 - 구분자 /

		// 카테고리 상품 개수 업데이트
		update_catagory_product_count();

		if($res) {

			error_loc("_product.form.php?_mode=modify&code=${code}&_PVSC=${_PVSC}");
		}
		else {

			error_loc_msg("_product.form.php?_mode=modify&code=${code}&_PVSC=${_PVSC}" , "오류가 발생하였습니다");
		}
		break;
	// - 상품추가 ---






	// - 상품수정 ---
	case "modify":

		$que="update odtProduct set
				parent_code		= '" . $parent_code . "',
				customerCode	= '" . $customerCode . "',
				customerName	= '" . $customerName . "',
				mainName		= '" . $mainName . "',
				name			= '" . $name . "',
				purPrice		= '" . $purPrice . "',
				commission		= '" . $commission . "',
				comSaleType		= '" . $comSaleType . "',
				price			= '" . $price . "',
				price_per		= '" . $price_per . "',
				coupon_title	= '" . $coupon_title . "',
				coupon_price	= '" . $coupon_price . "',
				price_org		= '" . $price_org . "',
				mainPrice		=   '" . $mainPrice . "',
				point			= '" . $point . "',
				stock			= '" . $stock . "',
				buy_limit		= '" . $buy_limit . "',
				saleNum			= '" . $saleNum . "',
				md_name			= '" . $md_name . "',
				sale_type		= '".$sale_type."',
				sale_date		= '" . $sale_date . "',
				sale_dateh		= '" . $sale_dateh . "',
				sale_datem		= '" . $sale_datem . "',
				sale_enddate	= '" . $sale_enddate . "',
				sale_enddateh	= '" . $sale_enddateh . "',
				sale_enddatem	= '" . $sale_enddatem . "',
				del_price_com	= '" . $del_price_com . "',
				del_price		= '" . $del_price . "',
				del_type		= '" . $del_type . "',
				del_limit		= '" . $del_limit . "',
				delchk			= '" . $delchk . "',
				main_img		= '" . $main_img_name . "',
				prolist_img		= '" . $prolist_img_name . "',
				prolist_img2	= '" . $prolist_img2_name . "',
				short_comment	= '" . $short_comment . "',
				message			= '" . $message . "',
				guestDisabled	=   '" . $guestDisabled . "',
				ipDistinct		=   '" . $ipDistinct . "',
				talkDisabled	=   '" . $talkDisabled . "',
				bankDisabled	=   '" . $bankDisabled . "',
				seeDisabled		=   '" . $seeDisabled . "',
				isSaleCnt		=   '" . $isSaleCnt . "',
				saleCnt			=   '" . $saleCnt . "',
				setup_subscribe	= '"  . $sum_setup_subscribe . "',
				setup_delivery	= '"  . $setup_delivery . "',
				rssarea1		= '" . $rssarea1 . "',
				rssarea2		= '" . $rssarea2 . "',
				rsscate			= '" . $rsscate . "',
				expire			= '" . $expire . "',
				com_juso		= '"  . $com_juso . "',
				com_mapx		= '"  . $com_mapx . "',
				com_mapy		= '"  . $com_mapy . "',
				pro_idx			= '".$pro_idx."',
				option1_title	= '".$option1_title."',
				option2_title	= '".$option2_title."',
				option3_title	= '".$option3_title."',
				option_type_chk	= '".$option_type_chk."',
				isNow			= '".$isNow."',
				thema			= '". $app_thema ."',
				bestview		= '". $bestview ."',
				p_view			= '". $p_view ."',
				p_icon			= '". @implode(",",$_icon)."',
				relation_auto	= '".$relation_auto."',
				saleCntMax		=   '" . $saleCntMax . "',
				v_color			= '".$v_color."'
				where
				code				= '" . $code . "'";
		//JJC003 - 묶음배송 관련  - 항목추가
		$res=mysql_query($que) or die(mysql_error());


		// - text 연동정보 입력 => addslashes는 _text_info_insert 함수에서 실행
		$que = " select * from odtProduct where code = '".$code."' ";
		$r = _MQ($que);
		_text_info_insert("odtProduct", $r[serialnum], "comment2", $comment2, "ignore");// 상품 상세설명
		_text_info_insert("odtProduct", $r[serialnum], "comment2_m", $comment2_m, "ignore");// 상품 상세설명(모바일) LDD005
		_text_info_insert("odtProduct", $r[serialnum], "comment_proinfo", $comment_proinfo, "ignore");// 상품사용정보
		_text_info_insert("odtProduct", $r[serialnum], "comment_useinfo", $comment_useinfo, "ignore");// 업체이용정보
		_text_info_insert("odtProduct", $r[serialnum], "comment3", $comment3, "ignore");// 주문확인서 주의사항
		_text_info_insert("odtProduct", $r[serialnum], "p_relation", $p_relation, "ignore");// 관련상품 코드 - 구분자 /

		// 카테고리 상품 개수 업데이트
		update_catagory_product_count();

		if($res) {

			error_loc("_product.form.php?_mode=modify&code=${code}&_PVSC=${_PVSC}");
		}
		else {

			error_loc_msg("_product.form.php?_mode=modify&code=${code}&_PVSC=${_PVSC}" , "오류가 발생하였습니다");
		}

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
		$dir ="../upfiles/product";
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
		if(sizeof($chk_pcode) == 0 ) {

			error_msg("잘못된 접근입니다.");
		}
		foreach($chk_pcode as $k=>$v) {

			if($v) {

				$que = "
					update odtProduct set
						pro_idx = '" . $chk_idx[$v] . "'
					where
						code = '" . $v . "'
				";
				_MQ_noreturn($que);
			}
		}

		error_loc("_product.list.php?".enc('d' , $_PVSC));

		break;
	// - 선택순위수정 ---
}