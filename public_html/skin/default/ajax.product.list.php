<?
	include_once(dirname(__FILE__)."/../../include/inc.php");

	// 인자 정보는 아래와 같다.
	// (선택) $cuid				: 카테고리코드
	// (선택) $list_type		: 리스트 형태 (type1 , type2)
	// (선택) $pagenate_use		: pagenate 사용여부 (Y, N)
	// (선택) $listmaxcount		: 페이지당 노출 갯수 
	// (선택) $thema			: 테마
	// (선택) $event_type		: 이벤트 요소
	// (선택) $hit_num_use		: 상품순위 아이콘 사용여부 (Y,N)
	// (선택) $order_field		: 정렬하고 싶은 필드명
	// (선택) $order_sort		: 정렬방식 

	// 인자 기본값 설정 
	if(!$list_type) { $list_type    = "type1"; }
	if(!$pagenate_use) { $pagenate_use = "N"; }
	if(!$listmaxcount) { $listmaxcount = "999"; }
	if(!$hit_num_use) { $hit_num_use  = "Y"; }
	if(!$order_field) { $order_field  = "pro_idx"; }
	if(!$order_sort) { $order_sort   = "asc"; }

	// 관련 변수 초기화
	unset($s_query);

	/*--------------------- 이벤트 요소별 처리 --------------------*/

	// 베스트상품
	if($event_type == "best_product") {
		$s_from .= " inner join odtProductMainSetup as pms on (pms.pms_pcode = p.code) ";
		$s_query .= " and pms.pms_type ='hot'";
		$s_order = " order by pms.pms_idx asc, inputDate desc ";
	}

	// 신상품
	if($event_type == "new_product") {
		$s_from .= " inner join odtProductMainSetup as pms on (pms.pms_pcode = p.code) ";
		$s_query .= " and pms.pms_type ='new'";
		$s_order = " order by pms.pms_idx asc, inputDate desc ";
	}

	// 마감임박 : 매진시작 2일 이내의 상품과, 재고 50개 이하 상품은 매진임박상품으로 분류
	if($event_type == "soldout_soon_product") { 
		$s_query .= " and ((p.sale_enddate <= '".date('Y-m-d',strtotime("+".$row_setup['s_main_close_day']." days"))."' and sale_type = 'T') or stock <= ".$row_setup['s_main_close_cnt'].") ";
	}

	// 오늘마감
	if($event_type == "today_close") { $s_query .= " and p.sale_enddate ='".date('Y-m-d')."' and sale_type = 'T' "; }

	// 상품검색
	if($event_type == "product_search") {
		$s_query .= " and ( ";
		$search_tmp = explode(' ',addslashes($search_keyword)); $s_query_array = array();
		foreach($search_tmp as $skk=>$skv) {
			$s_query_array[] = " replace(p.name,' ','') like '%".$skv."%' ";
		}
		$s_query .= implode(' or ',$s_query_array);
		$s_query .= " or p.code = '".addslashes($search_keyword)."' ";
		$s_query .= " ) ";
	}

	// 가격대 검색
	if(rm_str($q_price) > 0) { $s_query .= " and p.price <= ".rm_str($q_price); }
	/*--------------------- //이벤트 요소별 처리 ------------------*/


	/*--------------------- 카테고리 검색 --------------------*/
	if($cuid) {
		// 카테고리 정보
		$category_info = get_category_info($cuid);	

		if( $category_info['catedepth'] == 3 ) {
			$s_query .= " and (select count(*) from odtProductCategory as pct where pct.pct_pcode=p.code and pct.pct_cuid='".$cuid."') > 0 "; 
		} else { 
			$s_query .= " 
				and (
					select 
						count(*)
					from odtProductCategory as pct 
					left join odtCategory as c on (c.catecode = pct.pct_cuid)
					where 
						pct.pct_pcode=p.code and 
						find_in_set('" . $cuid . "' , c.parent_catecode)>0
				) > 0 
			"; 
		}
	}
	/*--------------------- // 카테고리 검색 --------------------*/

	// 검색결과가 없을때 추천 상품 출력
	if($event_type == "search_none_suggest") {
		$s_query = "";
		$s_order = " order by saleCnt desc ";
		$listmaxcount = 6;
	}

	// 판매일에 해당되는 상품만 검색.
	$s_query .= " and if(sale_type = 'T', (CURDATE() BETWEEN sale_date and sale_enddate), sale_type = 'A') ";

	// 테마검색
	if($thema) { $s_query .= " and find_in_set('".$thema."', thema) "; }

	// 정렬처리
	if(!$s_order) { 
		if($order_field=='sale_enddate') {
			$s_order = " order by sale_enddate>0 desc, stock asc, pro_idx asc, inputDate desc ";
		} else if( $order_field=='sale_date' ) {
			$s_order = " order by sale_date ".$order_sort.", pro_idx asc, inputDate desc ";
		} else {
			$s_order = " order by ".$order_field." ".$order_sort; 
		}
	}

	// limit 처리
	if($listmaxcount!='N') { $s_limit = " limit 0, ".$listmaxcount; }
	
	$assoc = _MQ_assoc("select * from odtProduct as p ".$s_from." where p_view='Y' ".$s_query . $s_order . $s_limit);
	//echo "select * from odtProduct as p ".$s_from." where p_view='Y' ".$s_query . $s_order . $s_limit;
	
	switch($list_type) {
		case "type1" :
			include dirname(__FILE__)."/ajax.product.list.type1.php"; // 줄당 3개씩 노출되는 일반적인 타입		
			break;
		case "type2" :
			include dirname(__FILE__)."/ajax.product.list.type2.php"; // 줄당 3개씩 노출되는 일반적인 타입		
			break;
		case "none" : break;
	}
?>