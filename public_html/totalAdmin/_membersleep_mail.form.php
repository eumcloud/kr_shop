<?PHP

	//JJC002

	// 페이지 표시
	$app_current_link = "/totalAdmin/_membersleep.list.php";

	include_once("inc.header.php");


	//if( !$_mode ) {error_msg("잘못된 접근입니다.");}


	switch($_mode) {
		// --- 선택회원 ---
		case "select_mail":
			$mr = _MQ_assoc(" select * from odtMemberSleep where userType='B' and isRobot = 'N' and id in ('". implode("','" , array_values($chk_id)) ."') ORDER BY serialnum desc ");
			$app_title = "선택";
		break; 
		// --- 검색회원 ---
		case "search_mail":
			$mr = _MQ_assoc(" select * from odtMemberSleep " . enc('d' ,  $_search_que ) . " ORDER BY serialnum desc ");			
			$app_title = "검색";
		break;
	}

	$arr_data = array();
	if(sizeof($mr) >  0){
		foreach( $mr as $k=>$v ){
			$arr_data[] = $v['serialnum'];
		}
	}
	$_cnt = sizeof(array_filter($arr_data));
	
	//if( $_cnt == 0 ) {error_msg("1명 이상 선택하시기 바랍니다.");}

?>



<?if( $_cnt > 0 ):?>
<script language='javascript' src='../include/js/lib.validate.js'></script>

<form name=frm method=post ENCTYPE='multipart/form-data' action="_membersleep_mail.pro.php">
<input type=hidden name=_mode value='send'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
<input type=hidden name="_cnt" value="<?=$_cnt?>" hname="<?=$app_title?>회원" required>
<input type=hidden name="_mduid" value="<?=$_mduid?>">
<input type=hidden name="_pass_data" value="<?=enc('e' , implode("," , array_filter(array_values($arr_data))))?>">
					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article"><?=$app_title?> 회원</td>
										<td class="conts">현재 <?=$app_title?> 회원 (<b ID="app_cnt"><?=number_format($_cnt)?></b>)명</td>
									</tr>
								</tbody>
						</table>
					</div>
<?=_submitBTN("_membersleep.list.php")?>
</form>
<?else : ?>
					<div class="bottom_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_gray"><input type="button" name="" class="input_large" value="목록" onclick="location.href='_membersleep.list.php?<?=enc('d' , $_PVSC)?>'">
							</span>
						</div>
					</div>
<?endif;?>


				<!-- 리스트영역 -->
				<div class="content_section_inner">

					<table class="list_TB" summary="메일발송정보">
						 <colgroup>
							<col width="120px"/><col width="*"/><col width="200px"/><col width="200px"/><col width="300px"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" class="colorset">기록일</th>
								<th scope="col" class="colorset">발송이메일</th>
								<th scope="col" class="colorset">발송상태</th>
								<th scope="col" class="colorset">발송일자</th>
							</tr>
						</thead> 
						<tbody> 
<?php

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtMailingProfile where mp_type='sleep' ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$mpres = _MQ_assoc(" select  * from odtMailingProfile where mp_type='sleep'  ORDER BY mp_uid desc limit $count , $listmaxcount ");
	if(sizeof($mpres) < 1) echo "<tr><td colspan=4 height=45>발송 내역이 없습니다.</td></tr>";
	foreach($mpres as $k=>$mpr){

		$ex_app_profile = array_filter(explode("," , $mpr['mp_email']));
		$arr_name = array();
		$sr = _MQ_assoc("select name from odtMemberSleep where serialnum in ('". implode("' , '" , $ex_app_profile) ."')");
		foreach($sr as $sk=>$sv){$arr_name[]=$sv['name'];}
		echo "
			<tr>
				<td>" . substr($mpr[mp_rdate],0,10) . "</td>
				<td>" . cutstr(implode(", " , array_values(array_filter($arr_name))) ,100 , "..") . "</td>
				<td>" . ($mpr[mp_status]=="Y" ? "<FONT style='font-weight:bold;color:red;'>발송완료</FONT>" : "<div class='btn_line_up_center'><span class='shop_btn_pack'><input type=button value='발송하기' class='input_small red' onclick='send_mailing(".$mpr[mp_uid].")'></span></div>") . "</td>
				<td>" . ($mpr[mp_status]=="Y" ? $mpr[mp_sdate] : "미발송") . "</td>
			</tr>
		";
	}
?>
						</tbody> 
					</table>

					<!-- 페이지네이트 -->
					<div class="list_paginate">
						<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=")?>
					</div>
					<!-- // 페이지네이트 -->

			</div>



					<div class="form_box_area">
						<table class="form_TB" summary="메일정보">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article">메일링 제목</td>
										<td class="conts"><?=$arr_member_sleep_title?></td>
									</tr>
									<tr>
										<td class="article">메일링 내용</td>
										<td class="conts">
<?php

	// 치환자
	// {{name}} : 성명
	// {{sitename}} : 사이트명
	// {{recentdate}} : 최근접속일
	$_name = "홍길동";
	$_sitename = $row_setup['site_name'];
	$_recentdate = date("Y-m-d" , strtotime(" -370 day "));

	include_once(dirname(__FILE__)."/../pages/mail.contents.sleep.php");
	$_title = $arr_member_sleep_title;
	$_title_content = '
	안녕하세요. <strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">'.$_name.'님!</strong> '.$_sitename.'에서 알려드립니다.<br />
	<strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">'.$_name.'님</strong>께서는 <strong style="font-family:\'나눔고딕\',\'돋움\'; color:#000; font-weight:600">'.$_sitename.'</strong> 사이트 <br />
	장기 미사용으로 인하여 휴면계정으로 전환됨을 알려드립니다. <br />
	';
	$_content = get_mail_content($_title, $_title_content, $mailling_content);
	echo $_content ;

?>
										</td>
									</tr>
								</tbody>
						</table>
					</div>


<script>
function send_mailing(uid) {

	if(!confirm("발송하시겠습니까?")) return false;

	common_frame.location.href="_membersleep_mail.pro.php?_mode=sendpro&_uid="+uid;

}
</script>
<?php
	include_once("inc.footer.php");
?>