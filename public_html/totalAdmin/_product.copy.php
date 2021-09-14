<?PHP

	include_once("inc.php");

		// --이미지 처리 ---
		$dir ="../upfiles/product";

		// 코드 생성
		$code = shop_pcode_create();

		$_current = _MQ("select * from odtProduct where code='".$pcode."'");

		// 추가옵션 복사
		$_addoption = _MQ_assoc("select * from odtProductAddoption where pao_pcode='".$pcode."' and pao_depth = '1' order by pao_uid asc");
		foreach($_addoption as $v) {
			_MQ_noreturn("insert into odtProductAddoption (pao_poptionname,pao_pcode,pao_parent,pao_depth,pao_poptionprice,pao_poptionpurprice,pao_cnt) values ('".$v['pao_poptionname']."','".$code."','".$v['pao_parent']."','".$v['pao_depth']."','".$v['pao_poptionprice']."','".$v['pao_poptionpurprice']."','".$v['pao_cnt']."')");
			$pao_uid = mysql_insert_id();
			$pao_option_2 = _MQ_assoc("select * from odtProductAddoption where pao_pcode='".$pcode."' and pao_parent = '".$v['pao_uid']."' and pao_depth = '2' order by pao_uid asc");
			foreach($pao_option_2 as $v2) {
				_MQ_noreturn("insert into odtProductAddoption (pao_poptionname,pao_pcode,pao_parent,pao_depth,pao_poptionprice,pao_poptionpurprice,pao_cnt) values ('".$v2['pao_poptionname']."','".$code."','".$pao_uid."','".$v2['pao_depth']."','".$v2['pao_poptionprice']."','".$v2['pao_poptionpurprice']."','".$v2['pao_cnt']."')");
			}
		}

		// 카테고리 복사
		$_category = _MQ_assoc("select * from odtProductCategory where pct_pcode='".$pcode."'");
		foreach($_category as $v) {
			_MQ_noreturn("insert into odtProductCategory (pct_pcode,pct_cuid) values ('".$code."','".$v['pct_cuid']."')");
		}

		// 옵션 복사
		$_option = _MQ_assoc("select * from odtProductOption where oto_pcode='".$pcode."' and oto_depth='1' order by oto_uid asc");
		foreach($_option as $v) {
			mysql_query("insert into odtProductOption (oto_pcode,oto_poptionname,oto_poptionprice,oto_poptionpurprice,oto_cnt,oto_depth,oto_parent)
					values ('".$code."','".$v['oto_poptionname']."','".$v['oto_poptionprice']."','".$v['oto_poptionpurprice']."','".$v['oto_cnt']."','".$v['oto_depth']."','".$v['oto_parent']."')");
			$oto_uid = mysql_insert_id();

			$_option_2 = _MQ_assoc("select * from odtProductOption where oto_pcode='".$pcode."' and oto_parent='".$v['oto_uid']."' and oto_depth='2' order by oto_uid asc");
			foreach($_option_2 as $v2) {
				mysql_query("insert into odtProductOption (oto_pcode,oto_poptionname,oto_poptionprice,oto_poptionpurprice,oto_cnt,oto_depth,oto_parent)
					values ('".$code."','".$v2['oto_poptionname']."','".$v2['oto_poptionprice']."','".$v2['oto_poptionpurprice']."','".$v2['oto_cnt']."','".$v2['oto_depth']."','".$oto_uid."')");
				$oto_uid2 = $oto_uid.','.mysql_insert_id();

				$_option_3 = _MQ_assoc("select * from odtProductOption where oto_pcode='".$pcode."' and find_in_set('".$v2['oto_uid']."',oto_parent) and oto_depth='3' order by oto_uid asc");
				foreach($_option_3 as $v3) {
				mysql_query("insert into odtProductOption (oto_pcode,oto_poptionname,oto_poptionprice,oto_poptionpurprice,oto_cnt,oto_depth,oto_parent)
					values ('".$code."','".$v3['oto_poptionname']."','".$v3['oto_poptionprice']."','".$v3['oto_poptionpurprice']."','".$v3['oto_cnt']."','".$v3['oto_depth']."','".$oto_uid2."')");
		}}}

		// 정보제공고시 복사
		$_reqinfo = _MQ_assoc("select * from odtProductReqInfo where pri_pcode='".$pcode."'");
		foreach($_reqinfo as $v) {
			_MQ_noreturn("insert into odtProductReqInfo (pri_pcode,pri_key,pri_value,pri_rdate) values ('".$code."','".addslashes($v[pri_key])."','".addslashes($v[pri_value])."',now())");
		}

		function _PhotoCopy($name){
			global $dir;

			if(strpos($name, '//') !== false) return $name; // # LDD015
			$ex_image_name = explode(".",$name); $app_ext = strtolower($ex_image_name[(sizeof($ex_image_name)-1)]); // 확장자
			$img_name = sprintf("%u" , crc32($name . time())) . "." . $app_ext ;
			@copy($dir.'/'.$name , $dir.'/'.$img_name);
			return $img_name;
		}

		$main_img_name = _PhotoCopy( $_current['main_img'] ) ;
		$prolist_img_name    = _PhotoCopy( $_current['prolist_img'] ) ;
		$prolist_img2_name    = _PhotoCopy( $_current['prolist_img2'] ) ;

		// -- 썸네일 적용 ---
		if( $prolist_img_name && strpos($prolist_img_name, '//') === false) {
			app_product_thumbnail( $dir , $prolist_img_name , "장바구니");
		}
		if( $prolist_img2_name && strpos($prolist_img2_name, '//') === false) {
			app_product_thumbnail( $dir , $prolist_img2_name , "최근본상품");
			app_product_thumbnail( $dir , $prolist_img2_name , "주문확인");
		}
		// -- 썸네일 적용 ---

			$que="insert into odtProduct set
					code			= '" . $code . "',
					parent_code		= '" . $_current['parent_code'] . "',
					customerCode	= '" . $_current['customerCode'] . "',
					customerName	= '" . $_current['customerName'] . "',
					mainName		= '" . $_current['mainName'] . "',
					name			= '" . '[복사] '.$_current['name'] . "',
					purPrice		= '" . $_current['purPrice'] . "',
					commission		= '" . $_current['commission'] . "',
					comSaleType		= '" . $_current['comSaleType'] . "',
					price			= '" . $_current['price'] . "',
					price_per		= '" . $_current['price_per'] . "',
					coupon_title	= '" . $_current['coupon_title'] . "',
					coupon_price	= '" . $_current['coupon_price'] . "',
					price_org		= '" . $_current['price_org'] . "',
					mainPrice		= '" . $_current['mainPrice'] . "',
					point			= '" . $_current['point'] . "',
					stock			= '" . $_current['stock'] . "',
					buy_limit		= '" . $_current['buy_limit'] . "',
					saleNum			= '" . $_current['saleNum'] . "',
					md_name			= '" . $_current['md_name'] . "',
					sale_type		= '" . $_current['sale_type'] . "',
					sale_date		= '" . $_current['sale_date'] . "',
					sale_dateh		= '" . $_current['sale_dateh'] . "',
					sale_datem		= '" . $_current['sale_datem'] . "',
					sale_enddate	= '" . $_current['sale_enddate'] . "',
					sale_enddateh	= '" . $_current['sale_enddateh'] . "',
					sale_enddatem	= '" . $_current['sale_enddatem'] . "',
					del_price_com	= '" . $_current['del_price_com'] . "',
					del_price		= '" . $_current['del_price'] . "',
					del_type		= '" . $_current['del_type'] . "',
					del_limit		= '" . $_current['del_limit'] . "',
					delchk			= '" . $_current['delchk'] . "',
					main_img		= '" . $main_img_name . "',
					prolist_img		= '" . $prolist_img_name . "',
					prolist_img2	= '" . $prolist_img2_name . "',
					short_comment	= '" . $_current['short_comment'] . "',
					message			= '" . $_current['message'] . "',
					guestDisabled	= '" . $_current['guestDisabled'] . "',
					ipDistinct		= '" . $_current['ipDistinct'] . "',
					talkDisabled	= '" . $_current['talkDisabled'] . "',
					bankDisabled	= '" . $_current['bankDisabled'] . "',
					seeDisabled		= '" . $_current['seeDisabled'] . "',
					isSaleCnt		= '" . $_current['isSaleCnt'] . "',
					saleCnt			= '0',
					saleCntMax		= '" . $_current['saleCntMax'] . "',
					setup_subscribe	= '" . $_current['sum_setup_subscribe'] . "',
					setup_delivery	= '" . $_current['setup_delivery'] . "',
					rssarea1		= '" . $_current['rssarea1'] . "',
					rssarea2		= '" . $_current['rssarea2'] . "',
					rsscate			= '" . $_current['rsscate'] . "',
					expire			= '" . $_current['expire'] . "',
					com_juso		= '" . $_current['com_juso'] . "',
					com_mapx		= '" . $_current['com_mapx'] . "',
					com_mapy		= '" . $_current['com_mapy'] . "',
					isNow			= '" . $_current['isNow'] . "',
					pro_idx			= '" . $_current['pro_idx'] . "',
					option1_title	= '" . $_current['option1_title'] . "',
					option2_title	= '" . $_current['option2_title'] . "',
					option3_title	= '" . $_current['option3_title'] . "',
					option_type_chk	= '" . $_current['option_type_chk'] . "',
					thema			= '" . $_current['thema'] . "',
					bestview		= '" . $_current['bestview'] . "',
					p_view			= '" . $_current['p_view'] . "',
					relation_auto			= '" . $_current['relation_auto'] . "',
					inputDate		= now()";
			//JJC003 - 묶음배송 관련  - 항목추가
			$res = mysql_query($que) or die(mysql_error());
			$serialnum = mysql_insert_id();

			function get_text($keyword,$datauid) {
				$_current_text = _MQ("select * from odtTableText where ttt_datauid = '".$datauid."' and ttt_keyword = '".$keyword."'");
				/*$dom = new DOMDocument; libxml_use_internal_errors(true);
				$dom->loadHTML( $_current_text[ttt_value] ); $xpath = new DOMXPath( $dom ); libxml_clear_errors();
				$doc = $dom->getElementsByTagName("img")->item(0); $src = $xpath->query(".//@src");
				$tmp_dir = '/upfiles/tinymce/';
				foreach ( $src as $s ) {
					if(strpos($s->nodeValue,$tmp_dir) !== false) {
						$tmp = explode($tmp_dir,$s->nodeValue);
						$ex_image_name = explode(".",$tmp[1]); $app_ext = strtolower($ex_image_name[(sizeof($ex_image_name)-1)]); // 확장자
						$img_name = sprintf("%u" , crc32($tmp[1] . time())) . "." . $app_ext ;
						@copy($_SERVER[DOCUMENT_ROOT].$tmp_dir.$tmp[1] , $_SERVER[DOCUMENT_ROOT].$tmp_dir.$img_name);
						$s->nodeValue = $tmp_dir.$img_name;
					}
				}
				$output = $dom->saveXML( $doc ); $_content = mysql_real_escape_string($output);*/
				$_content = addslashes($_current_text['ttt_value']);
				return $_content;
			}

			// - text 연동정보 입력 => addslashes는 _text_info_insert 함수에서 실행
			_text_info_insert( "odtProduct" , $serialnum , "comment2" , get_text('comment2',$_current['serialnum']) );// 상품 상세설명
			_text_info_insert( "odtProduct" , $serialnum , "comment2_m", get_text('comment2_m',$_current['serialnum']));// 상품 상세설명(모바일) LDD005
			_text_info_insert( "odtProduct" , $serialnum , "comment_proinfo" , get_text('comment_proinfo',$_current['serialnum']) );// 상품사용정보
			_text_info_insert( "odtProduct" , $serialnum , "comment_useinfo" , get_text('comment_useinfo',$_current['serialnum']) );// 업체이용정보
			_text_info_insert( "odtProduct" , $serialnum , "comment3" , get_text('comment3',$_current['serialnum']) );// 주문확인서 주의사항
			_text_info_insert( "odtProduct" , $serialnum , "p_relation" , get_text('p_relation',$_current['serialnum']) );// 관련상품 코드 - 구분자 /

			// 카테고리 상품 개수 업데이트
			update_catagory_product_count();

			if ($res) {
				error_loc_msg("_product.form.php?_mode=modify&code=".$code."&_PVSC=".$_PVSC,"복사되었습니다. 복사된 상품페이지로 이동합니다.");
			}
			else {
				error_loc_msg("_product.form.php?_mode=modify&code=".$pcode."&_PVSC=".$_PVSC,"오류가 발생하였습니다");
			}


?>
