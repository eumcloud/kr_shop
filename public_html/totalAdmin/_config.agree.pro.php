<?PHP
// LMH007
	include "inc.php";


	// -- odtCompany 적용 ---
	$que = "
		update odtCompany set 
			guideinfo           = '".$guideinfo."', 
			privacyinfo         = '".$privacyinfo."', 
			guideinfo_html      = '".$guideinfo_html."', 
			guideinfo_html_m    = '".$guideinfo_html_m."', 
			privacyinfo_html    = '".$privacyinfo_html."', 
			privacyinfo_html_m  = '".$privacyinfo_html_m."', 
			privacyinfo2        = '".$privacyinfo2."', 
			thirdinfo           = '".$thirdinfo."', 
			guestinfo           = '".$guestinfo."'
			,partner_agree          = '".$partner_agree."'
			,sendmail_agree         = '".$sendmail_agree."'
			,subscrip_agree         = '".$subscrip_agree."'
		where 
			serialnum = 1
	";
	_MQ_noreturn($que);


	// -- odtPolicy 적용 2017-09-13 SSJ --- 
	if(sizeof($_appname) > 0){
		foreach($_appname as $k=>$v){
			$arr_uid = ${$v . '_uid'};
			$app_use = ${$v . '_use'};
			$arr_title = ${$v . '_title'};
			$arr_content = ${$v . '_content'};
			if(!is_array($arr_uid)) $arr_uid = array($arr_uid);
			if(!is_array($arr_title)) $arr_title = array($arr_title);
			if(!is_array($arr_content)) $arr_content = array($arr_content);
			$arr_uid = array_filter($arr_uid);
			$arr_title = array_filter($arr_title);
			$arr_content = array_filter($arr_content);
			// 삭제된 항목 먼저 삭제
			_MQ_noreturn(" delete from odtPolicy where po_name = '" . $v ."' and po_uid not in ('". implode("','" , $arr_uid) ."')");
			foreach($arr_title as $sk=>$sv){
				if($arr_uid[$sk] > 0){
					$que = "
						update odtPolicy set
							po_use = '". $app_use ."'
							,po_name = '". $v ."'
							,po_title = '". trim(addslashes(($arr_title[$sk]))) ."'
							,po_content = '". addslashes($arr_content[$sk]) ."'
						where po_uid = '".$arr_uid[$sk]."'
					";
				}else{
					$que = "
						insert into odtPolicy set
							po_use = '". $app_use ."'
							,po_name = '". $v ."'
							,po_title = '". trim(addslashes(($arr_title[$sk]))) ."'
							,po_content = '". addslashes($arr_content[$sk]) ."'
					";
				}
				_MQ_noreturn($que);
			}
		}
	}
	// -- odtPolicy 적용 2017-09-13 SSJ --- 


	error_loc("_config.agree.form.php");
	exit;


?>