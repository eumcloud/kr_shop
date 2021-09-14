<?PHP

	include "inc.php";


	// 추천 검색어 구분
	$ex = explode("," , $s_recommend_keyword);
	$arr_recommend_keyword = array();
	foreach($ex as $k=>$v){
		$arr_recommend_keyword[trim($v)]++;
	}

	//$login_page_phone = $login_page_phone?"'".$login_page_phone."'":'DEFAULT(login_page_phone)';
	//$login_page_email = $login_page_email?"'".$login_page_email."'":'DEFAULT(login_page_email)';

	// -- odtSetup 적용 ---  member_cpw_period = ".$member_cpw_period.",  추가 lcy
	$que = "
		update odtSetup set 
			site_name				= '".$site_name."', 
			smsMaxCount				= '".$smsMaxCount."', 
			rewrite_chk				= '".$rewrite_chk."', 
			auto_endalim			= '".$auto_endalim."',
			paypoint				= '".rm_str($paypoint)."',
			paypoint_limit			= '".rm_str($paypoint_limit)."',
			paypoint_join			= '".rm_str($paypoint_join)."',
			paypoint_joindate		= '".rm_str($paypoint_joindate)."',
			paypoint_productdate	= '".rm_str($paypoint_productdate)."',
			s_action_join			= '".rm_str($_action_join)."',
			s_action_login			= '".rm_str($_action_login)."',
			s_action_order			= '".rm_str($_action_order)."',
			s_action_talk			= '".rm_str($_action_talk)."',
			s_search_keyword		= '".$s_search_keyword."',
			s_recommend_keyword		= '". implode("," , array_keys($arr_recommend_keyword)) ."',
			login_page_phone		= '".$login_page_phone."',
			login_page_email		= '".$login_page_email."',
			member_sleep_period		= ".$member_sleep_period.",
			member_cpw_period		= ".$member_cpw_period.", 
			view_social_commerce	= '".$view_social_commerce."',
			none_member_buy			= '".$none_member_buy."'
		where 
			serialnum = 1
	"; // LDD019 (none_member_buy = '".$none_member_buy."')
	_MQ_noreturn($que);



	// - -에스크로 구매안전 배너 처리 ---
	$dir            ="../upfiles/normal";
	$escrow_img_name = _PhotoPro( $dir , "escrow_img" ) ;


	// -- odtSetup 적용 ---
	$que = "
		update odtCompany set 
			homepage					= '".$homepage."', 
			htel						= '".$htel."', 
			email						= '".$email."', 
			tel							= '".$tel."', 
			fax							= '".$fax."', 
			number2						= '".$number2."', 
			name1						= '".$name1."', 
			address						= '".$address."', 
			escrow_url					= '".$escrow_url."', 
			escrow_img					= '".$escrow_img_name."', 
			name						= '".$name."', 
			ceoname						= '".$ceoname."', 
			number1						= '".$number1."', 
			taxaddress					= '".$taxaddress."', 
			taxstatus					= '".$taxstatus."', 
			taxitem						= '".$taxitem."', 
			homepage_title				= '".$homepage_title."', 
			homepage_title_product		= '".$homepage_title_product."', 
			metatag						= '".$metatag."', 
			metatag_keyword				= '".$metatag_keyword."',
			view_network_company_info	= '".($view_network_company_info == "Y" ? "Y" : "N")."',
			officehour					= '".$officehour."'
		where 
			serialnum = 1
	";
	_MQ_noreturn($que);


	error_loc("_config.default.form.php");
	exit;


?>