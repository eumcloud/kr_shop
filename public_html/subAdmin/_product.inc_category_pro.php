<?PHP

	include_once("inc.php");

 
	// 넘어온 변수
	//		==> _mode2 : add , delete , list 
	//		==> code : 상품코드
	//		==> catecode : 카테고리


	// 사전체크
	if( in_array($_mode2 , array("add" , "delete")) ){
		$code = nullchk($code , "상품코드가 확인되지 않습니다.");
		$catecode = nullchk($catecode , "상품분류를 선택하시기 바랍니다.");
	}


	switch($_mode2){


		// -- 카테고리 추가 ---
		case "add":

			// 중복 배제 추가
			$que = "
				INSERT INTO odtProductCategory (pct_pcode, pct_cuid) VALUES ('". $code ."', '". $catecode ."') 
				ON DUPLICATE KEY UPDATE pct_pcode='". $code ."' , pct_cuid='". $catecode ."'
			";
			_MQ_noreturn($que);

			// 카테고리 상품 개수 업데이트
			update_catagory_product_count();

			break;
		// -- 카테고리 추가 ---




		// -- 카테고리 삭제 ---
		case "delete":

			// 삭제
			$que = "delete from odtProductCategory where pct_pcode='". $code ."' and pct_cuid='". $catecode ."' ";
			_MQ_noreturn($que);

			// 카테고리 상품 개수 업데이트
			update_catagory_product_count();

			break;
		// -- 카테고리 삭제 ---




		// -- 카테고리 목록 ---
		case "list":

			$code = nullchk($code , "상품코드가 확인되지 않습니다.");

			// 목록
			$arr_cate2 = array();
			$que = "
				select 
					pct.* , ct3.catename as ct3_name , ct2.catename as ct2_name , ct2.catecode as ct2_catecode , ct1.catename as ct1_name
				from odtProductCategory as pct 
				left join odtCategory as ct3 on (ct3.catecode = pct.pct_cuid and ct3.catedepth=3)
				left join odtCategory as ct2 on (substring_index(ct3.parent_catecode , ',' ,-1) = ct2.catecode and ct2.catedepth=2)
				left join odtCategory as ct1 on (substring_index(ct3.parent_catecode , ',' ,1) = ct1.catecode and ct1.catedepth=1)
				where 
					pct.pct_pcode='". $code ."'
					order by pct.pct_uid asc
			";
			$r = _MQ_assoc($que);
			foreach( $r as $k=>$v ){
				echo "
					<div style='clear:both; width:428px; ' >
						<div style='float:left; padding:6px;' >
							<B>". $v[ct1_name] ." &gt; ". $v[ct2_name] ." &gt; ". $v[ct3_name] ."</B>
						</div>
						<div style='float:right;' >
							<span class='shop_btn_pack' style='margin-right:10px'><a href='#none' class='small blue' onclick=\"category_delete('". $v[pct_cuid] ."');\">삭제</a></span>
						</div>
					</div>
				";
				$arr_cate2[$v[ct2_catecode]]++;
			}
			if(is_array($arr_cate2)){
				foreach( $arr_cate2 as $k=>$v ){
					echo "<input type='hidden' name='chk_cate2[". $k ."]' value='".$k."' class='cls_chk_cate2'>";
				}
			}

			break;
		// -- 카테고리 목록 ---

	}

?>