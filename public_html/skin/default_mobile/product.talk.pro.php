<?PHP
	include_once(dirname(__FILE__)."/../../include/inc.php");

	if( in_array($_mode , array("add","delete","reply" )) ){
		member_chk();// 로그인 체크는 등록 / 삭제시에만 적용됨
	}

	// 모드별 처리
	switch( $_mode ){

		// - 상품 토크 등록 ---
		case "add":

			$que = "
				insert odtTt set
					ttProCode	= '". $pcode ."'
					,ttID		= '".get_userid()."'
					,ttName		= '".$row_member['name']."'
					,ttContent	= '".$_content."'
					,ttIsReply	= 0
					,ttSNo		= 0
					,ttRegidate	= now()
			";
			_MQ_noreturn($que);

			// 참여점수지급
			$isFirst = _MQ("select count(*) as cnt from odtActionLog where acID = '".get_userid()."' and acTitle ='상품문의' and left(acRegidate,10) = CURDATE() ");
			if($isFirst['cnt'] < 1 && $row_setup['s_action_talk']>0){
				_MQ_noreturn("insert into odtActionLog set acID= '".get_userid()."', acTitle ='상품문의', acPoint='". $row_setup['s_action_talk'] ."', acRegidate = now(), ip='".$_SERVER['REMOTE_ADDR']."'");
				_MQ_noreturn("update odtMember set action = action + ". $row_setup['s_action_talk'] ." where id='".get_userid()."'");
			}
			// 참여점수지급

			// 관리자에게 문자 발송
			$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			$smskbn = "talk";	// 문자 발송 유형
			if($row_sms[$smskbn]['smschk'] == "y") {

				$sms_to		= $row_company['htel'];
				$sms_from	= $row_company['tel'];

				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				// 치환작업
				$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext'], '', array(
					'{{상품문의}}'     => $_content
				));
				$sms_msg = $arr_sms_msg['msg'];
				$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

				//onedaynet_sms_multisend($arr_send);
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				onedaynet_alimtalk_multisend($arr_send);
			}
			break;
		// - 상품 댓글 등록 ---

		// - 상품 토크 리플 등록 ---
		case "reply":

			$que = "
				insert odtTt set
					ttProCode	= '". $pcode ."'
					,ttID		= '".get_userid()."'
					,ttName		= '".$row_member['name']."'
					,ttContent	= '".$_content."'
					,ttIsReply	= '1'
					,ttSNo		= '".$ttNo."'
					,ttRegidate	= now()
			";
			_MQ_noreturn($que);

			// 토크 작성자에게 문자 발송
			$arr_send = array();//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			$smskbn = "talk_re";	// 문자 발송 유형
			if($row_sms[$smskbn]['smschk'] == "y") {

				$tt_info = _MQ("select m.name,m.htel1,m.htel2,m.htel3 from odtTt as tt left join odtMember as m on (tt.ttID = m.id) where ttNo = '".$ttNo."'");
				$sms_to		= phone_print($tt_info['htel1'],$tt_info['htel2'],$tt_info['htel3']);
				$sms_from	= $row_company['tel'];

				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				// 치환작업
				$arr_sms_msg = sms_msg_replace_array($row_sms[$smskbn]['smstext']);
				$sms_msg = $arr_sms_msg['msg'];
				$arr_send = array_merge($arr_send , sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $row_sms[$smskbn]['smstitle'] , $row_sms[$smskbn]['smsfile']));
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

				//onedaynet_sms_multisend($arr_send);
				//----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
				onedaynet_alimtalk_multisend($arr_send);
			}
			break;
		// - 상품 댓글 등록 ---


		// - 상품 댓글 삭제 ---
		case "delete":
			$uid = nullchk($uid , "잘못된 접근입니다." , "" , "ALT");

			// 등록 상품 댓글 확인
			$r = _MQ(" select count(*) as cnt from odtTt where ttNo = '".$uid."' and ttID = '".get_userid()."' ");
			if( $r['cnt'] == 0 ) {
				echo "no data";//error_alt("등록하신 글이 아닙니다.");
				exit;
			}

			// 댓글있는 상품 댓글인지 확인
			$r = _MQ(" select count(*) as cnt from odtTt where ttSNo = '".$uid."' ");
			if( $r['cnt'] > 0 ) {
				echo "is reply";//error_alt("댓글이 있으므로 삭제가 불가합니다.");
				exit;
			}

			$que = " delete from odtTt where ttNo = '".$uid."' and ttID='".get_userid()."' ";
			_MQ_noreturn($que);
			break;
		// - 상품 댓글 삭제 ---


		// - 댓글 갯수 추출 ---
		case "getcnt":
			echo "(".get_talk_total($pcode,"normal").")";
			break;

		// - 상품 댓글 보기 ---
		case "view":

			echo "<div class='list_area'><ul>";

			$s_query = "from odtTt as pt
				where ttIsReply=0 and ttProCode = '" . $pcode . "'";


			// 페이징을 위한 작업
			$listmaxcount = 5;																		// $view_cnt
			$listpg = $listpg ? $listpg : 1;														// $page_num
			$count = ($listpg-1) * $listmaxcount;													// $limit_start_num
			$res = _MQ("select count(*) as cnt ".$s_query);
			$TotalCount = $res['cnt'];
			$Page = $TotalCount ? ceil($TotalCount / $listmaxcount) : 1;
			$page_num = $TotalCount-$count;

			// - 상품 댓글 목록 ---
			$que = "
				select
					pt.* ,
					case ttIsReply when 0 then ttNo else ttSNo end as orderby_uid
				".$s_query."
				order by orderby_uid desc , ttNo asc limit  $count , $listmaxcount
			";
			$res = _MQ_assoc($que);

			if(sizeof($res) < 1 ) { echo "<div class='cm_no_conts'><div class='no_icon'></div><div class='gtxt'>등록된 내용이 없습니다.</div></div>"; }
			foreach( $res as $k=>$v ){
				unset($talk_btn,$reply_content);

				$talk_name = $v['ttName'];
				$talk_rdate = date('Y-m-d',strtotime($v['ttRegidate']));
				$talk_content = nl2br(htmlspecialchars($v['ttContent']));
				$talk_btn = $v['ttID'] == get_userid() ? "<a href='#none' onclick=\"talk_del('".$v['ttNo']."');return false;\" title='' class='btn_sm_black'>삭제</a>" : NULL;
				$talk_reply = get_userid() ? "<a href='#none' title='' onclick=\"view_reply_form('".$v['ttNo']."');return false;\" class='btn_sm_black'>댓글</a>" : NULL;
				$talk_show = "<a href='#none' title='' onclick=\"talk_show('".$v['ttNo']."');return false;\" class='btn_sm_white'>내용보기</a>";
				$talk_close = "<a href='#none' title='' onclick=\"talk_show('".$v['ttNo']."');return false;\" class='btn_sm_black'>닫기</a>";

				// 리플 추출
				unset($reply_content);
				$reply_r = _MQ_assoc("select * from odtTt where ttIsReply='1' and ttSNo = '".$v['ttNo']."'");
				foreach($reply_r as $k2 => $v2) {
					$talk_name2 = $v2['ttName'] ? $v2['ttName'] : $v2['ttID'];
					$talk_rdate2 = date('Y-m-d',strtotime($v2['ttRegidate']));
					$talk_content2 = nl2br(($v2['ttContent']));
					$talk_btn2 = $v2['ttID'] == get_userid() ? "<a href='#none' onclick=\"talk_del('".$v2['ttNo']."');return false;\" title='댓글삭제' class='btn_delete'><span class='shape'></span></a>" : NULL;

					$reply_content .= "
						<div class='reply'>
							<span class='shape_ic'></span>
							<div class='conts_txt'>
								<span class='admin'>
									<span class='name'>".$talk_name2."</span>
									<span class='bar'></span><span class='date'>".$talk_rdate2."</span>
									".$talk_btn2."
								</span>
								".$talk_content2."
							</div>
						</div>
					";
				}

				// 리플 폼.
				unset($reply_form);
				if(is_login()) {
					$reply_form = "
						<div class='form_area reply_form' id='reply_form_".$v['ttNo']."'>
							<div class='inner'>
								<div class='form_conts'>
									<div class='textarea_box'>
										<textarea id='_content_".$v['ttNo']."' cols='' rows='' class='textarea_design' placeholder='이 글에 관한 댓글을 남겨주세요.'></textarea>
									</div>
									<input type='button' onclick='talk_reply_add(".$v['ttNo'].")' class='btn_ok' name='' class='' value='등록'>
								</div>
							</div>
						</div>
					";
				}
				echo "
					<li id='".$v['ttNo']."'>
						<div class='post_box'>
							<a href='#none' onclick=\"talk_show('".$v['ttNo']."');return false;\" class='upper_link'></a>
							".(count($reply_r)>0?"<span class='texticon_pack'><span class='red'>답변완료</span></span>":"<span class='texticon_pack'><span class='light'>답변대기</span></span>")."
							<span class='title'>".strip_tags($talk_content)."</span>
							<span class='title_icon'>".(time()-strtotime($v['ttRegidate'])<86400*2?"N":"")."</span>
							<span class='writer'>
								<span class='name'>".$talk_name."</span>
								<span class='bar'></span>
								<span class='date'>".$talk_rdate."</span>
							</span>
						</div>
						<div class='open_box'>
							<div class='conts_txt'>
								<dl>
									<dd>".$talk_content."</dd>
								</dl>
								<span class='button_pack'>".$talk_btn."</span>
							</div>
							".$reply_content."
						</div>
					</li>
				";
			}

	$for_start_num = $Page <= 10 || $listpg <= 5 ? 1 : (($Page - $listpg) < 5 ? $Page-9 : $listpg-5);
	$for_end_num = $Page < ($for_start_num + 9) ? $Page : ($for_start_num + 9) ;
	$first	= "1";
	$prev		= $listpg > 1 ? $listpg-1 : 1;
	$next		= $listpg < $Page ? $listpg+1 : $Page;
	$last		= $Page;

?>
</ul></div>

<div class='cm_paginate'>
	<span class="inner">

		<? if($listpg == $prev){ ?>
		<a href="#none" onclick="return false;" class="prevnext" title="이전"><span class="arrow"></span></a>
		<? } else { ?>
		<a href="#none" onclick="talk_view(<?=$prev?>);return false;" class="prevnext" title="이전"><span class="arrow"></span></a>
		<? } ?>

		<?
			for($ii=$for_start_num;$ii<=$for_end_num;$ii++) {
				if($ii != $listpg) { echo "<a href='#none' class='number' onclick=\"talk_view(".$ii.");return false;\">$ii</a>"; }
				else { echo "<a href='#none' onclick='return false;' class='number hit'>$ii</a>"; }
			}
		?>

		<? if($listpg == $next){ ?>
		<a href="#none" onclick="return false;" class="prevnext" title="다음"><span class="arrow"></span></a>
		<? } else { ?>
		<a href="#none" onclick="talk_view(<?=$next?>);return false;" class="prevnext" title="다음"><span class="arrow"></span></a>
		<? } ?>

	</span>
</div>
<?
			// - 상품 댓글 목록 ---
		break;
	}
?>
