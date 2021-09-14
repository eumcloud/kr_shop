<?PHP

	include_once( dirname(__FILE__)."/inc.php");


	if( in_array( $_mode , array('add' , 'modify') ) ){

		// --사전 체크 ---
		$_menu = nullchk($_menu , "메뉴를 선택하시기 바랍니다.");
		$_title = nullchk($_title , "제목을 입력하시기 바랍니다.");
		$_content = nullchk($_content , "문의내용을 입력하시기 바랍니다.");
		$_status = nullchk($_status , "답변상태를 선택하시기 바랍니다.");
		// --사전 체크 ---

		// --query 사전 준비 ---
		$sque = "
			 r_menu = '". $_menu ."'
			,r_comname = '". $_comname ."'
			,r_email = '". $_email ."'
			,r_tel = '". $_tel ."'
			,r_hp = '". $_hp ."'
			,r_status = '". $_status ."'
			,r_title = '". $_title ."'
			,r_content = '". $_content ."'
			,r_admcontent = '". $_admcontent ."'
		";
		// --query 사전 준비 ---

	}

	// 문의하기 정보 추출
	$r = _MQ(" select * from odtRequest where r_uid='{$_uid}' ");


	// - 모드별 처리 ---
	switch( $_mode ){


		case "add":
			$que = " insert odtRequest set $sque , r_rdate = now() ";
			_MQ_noreturn($que);
			$_uid = mysql_insert_id();
			error_loc("_request.form.php?pass_menu={$pass_menu}&_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;



		case "modify":
			$r = _MQ(" select * from odtRequest where r_uid='{$_uid}' ");

			$que = " update odtRequest set $sque ". ($_status == "답변완료" ? " , r_admdate = now()" : "")." where r_uid='{$_uid}' ";
			_MQ_noreturn($que);

			// 제휴문의일 경우 답변을 메일로 발송
			if($_status=='답변완료' && $_status != $r[r_status] && in_array($_menu,array('partner'))) {
				// - 메일발송 ---
				$_oemail = $_email;
				if( mailCheck($_oemail) ){
					$_title = "[".$row_company['name']."] 제휴문의에 관해 답변드립니다.";
					$_title_content = '
					<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">'.$_comname.'님!</strong> 저희 사이트에 제휴문의 해주셔서 감사합니다. <br />
					아래에 답변 드립니다.
					';
					$mailling_content = '
					<div style="margin:40px 50px 50px 50px;">
						<dl style="margin-top:30px">
							<!-- 내용작은 타이틀 -->
							<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/include/images/mailing/bullet.png\') left top no-repeat; opacity: 0.6; filter: alpha(opacity=60); padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">문의내용 ('.date('Y-m-d', strtotime($r['r_rdate'])).')</dt>
							<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0;  opacity: 0.6; filter: alpha(opacity=60);">
								'.nl2br($_content).'
							</dd>
						</dl>
						<dl style="margin-top:30px">
							<!-- 내용작은 타이틀 -->
							<dt style="font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; background:transparent url(\''.$mailing_url.'/include/images/mailing/bullet.png\') left top no-repeat; padding:0 0 10px 33px; line-height:1.7; border-bottom:1px solid #666">답변내용 ('.date('Y-m-d', strtotime($r['r_admdate'])).')</dt>
							<dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0">
								'.nl2br($_admcontent).'
							</dd>
						</dl>
					</div>
					';

					$_content = get_mail_content($_title, $_title_content, $mailling_content);

					// -- 메일 발송
					mailer( $_oemail, $_title , $_content );
				}
				// - 메일발송 ---
			}

			error_loc("_request.form.php?pass_menu={$pass_menu}&_mode=${_mode}&_uid=${_uid}&_PVSC=${_PVSC}");
			break;


		case "delete":
			// 이미지 삭제
			_FileDel( "../../../upfiles/normal" , $r[r_file]);

			_MQ_noreturn("delete from odtRequest where r_uid='{$_uid}' ");
			error_loc("_request.list.php?pass_menu={$pass_menu}&".enc('d' , $_PVSC ));
			break;

	}
	// - 모드별 처리 ---

	exit;
?>