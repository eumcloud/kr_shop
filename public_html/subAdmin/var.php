<?PHP

	// - 입점관리자 메뉴 ---
	$arrMenuTotalAdmin = 	array(
		"상품관리" => array(
			"_product.form.php" => "상품등록" ,  // 입점패치[유료]
			"_product.list.php" => "상품관리" ,   // 입점패치[유료]
			"_product_talk.list.php" => "상품토크관리" ,   // 입점패치[유료]
		),
		"주문관리" => array(
			"_order2.list.php?delivstatus=no&ordertype=coupon" => "(쿠폰)발급대기관리" , 
			"_order2.list.php?delivstatus=yes&ordertype=coupon" => "(쿠폰)발급완료관리" ,
			'_order2.reserve_list.php' => '(배송)예약발송대기관리', // LDD018
			"_order2.list.php?delivstatus=no&ordertype=product" => "(배송)발송대기관리" , 
			"_order2.list.php?delivstatus=yes&ordertype=product" => "(배송)발송완료관리" , 
			"_order3.list.php" => "정산대기관리" , //[유료]
			"_order4.list.php" => "정산완료관리" ,//[유료]
			"_ordercalc.view.php" => "정산현황" ,   // 입점패치[유료]
			"_return.list.php" => "교환/반품관리" ,   // 입점패치[유료]
		),
	);
	$arrMenuTotalAdmin_imgkey = array("상품관리" => 2 , "주문관리" => 3);
	// - 입점관리자 메뉴 ---

	// 변수를 사용하기 쉽게 메뉴를 재 배열 처리.
	foreach($arrMenuTotalAdmin as $menu_name1 => $sub_array) {
		foreach($sub_array as $menu_url => $menu_name2) {
			$idx_tmp++;
			$arrMenuTotalAdminVar[$idx_tmp][first] = $menu_name1 != $menu_name1_old ? true : false;
			$arrMenuTotalAdminVar[$idx_tmp][menu_name1] = $menu_name1;
			$arrMenuTotalAdminVar[$idx_tmp][menu_name2] = $menu_name2;
			$arrMenuTotalAdminVar[$idx_tmp][menu_url] = $menu_url;
			$menu_name1_old = $menu_name1;
		}
	}
?>