<?PHP

	// 페이지 표시
	$app_current_link = "/totalAdmin/_mailing_data.list.php";

	include_once("inc.header.php");

	if( !$_mduid ) {
		error_msg("잘못된 접근입니다.");
	}

	$row = _MQ("select * from odtMailingData where md_uid='${_mduid}' ");

	// 저장한 정보 불러오기 --> $app_profile 로 저장됨
	include_once("../upfiles/normal/mailing.profile.php");
	$ex_app_profile = array_filter(array_unique(explode("," , $app_profile)));
	$_cnt = sizeof($ex_app_profile);


?>

<script language='javascript' src='../include/js/lib.validate.js'></script>


<form name=frm method=post ENCTYPE='multipart/form-data' action="_mailing_profile.pro.php">
<input type=hidden name=_mode value='<?=$_mode?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
<input type=hidden name="_cnt" value="<?=$_cnt?>" hname="메일링 적용된 회원" required>
<input type=hidden name="_mduid" value="<?=$_mduid?>">

					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article">메일링 회원</td>
										<td class="conts">현재 메일링 적용된 회원 (<b ID="app_cnt"><?=number_format($_cnt)?></b>)명<br>
										<span class='shop_btn_pack'><input type=button value="회원검색" class="input_small gray" onclick="open_window('member_popup', '_mailing_profile.member_list.php', '10', '10', '850', '500', '', '', '', 'yes', '');"></span>
										<span class='shop_btn_pack'><span class='blank_3'></span></span>
										<span class='shop_btn_pack'><input type=button value="입점업체검색" class="input_small gray" onclick="open_window('company_popup', '_mailing_profile.company_list.php', '10', '10', '850', '500', '', '', '', 'yes', '');"></span>
										<span class='shop_btn_pack'><span class='blank_3'></span></span>
										<span class='shop_btn_pack'><input type=button value="선택회원 전체 삭제"  class="input_small gray" onclick="del('_mailing_profile.pro.php?_mduid=<?=$_mduid?>&_mode=profile_delete&_PVSC=<?=$_PVSC?>');"></span>
										
										</td>
									</tr>
									<tr>
										<td class="article">참고사항</td>
										<td class="conts">등록시간 : <?=$row[md_rdate]?></td>
									</tr>
								</tbody>
						</table>
					</div>

<?=_submitBTN("_mailing_data.list.php")?>
</form>


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
<?PHP
	$mpres = _MQ_assoc(" select  * from odtMailingProfile where mp_type='mail' and mp_mduid='" . $_mduid . "' ORDER BY mp_uid desc ");
	if(sizeof($mpres) < 1) echo "<tr><td colspan=4 height=45>발송 내역이 없습니다.</td></tr>";
	foreach($mpres as $k=>$mpr){

		echo "
							<tr>
								<td>" . substr($mpr[mp_rdate],0,10) . "</td>
								<td>" . str_replace(","," , ",cutstr($mpr[mp_email],100 , "...")). "</td>
								<td>" . ($mpr[mp_status]=="Y" ? "<FONT style='font-weight:bold;color:red;'>발송완료</FONT>" : "<div class='btn_line_up_center'><span class='shop_btn_pack'><input type=button value='발송하기' class='input_small red' onclick='send_mailing(".$mpr[mp_uid].")'></span></div>") . "</td>
								<td>" . ($mpr[mp_status]=="Y" ? $mpr[mp_sdate] : "미발송") . "</td>
							</tr>
				";
	}
?>
						</tbody> 
					</table>

			</div>



					<div class="form_box_area">
						<table class="form_TB" summary="메일정보">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr>
										<td class="article">메일링 제목</td>
										<td class="conts"><?=$row[md_title]?></td>
									</tr>
									<tr>
										<td class="article">메일링 내용</td>
										<td class="conts"><?=stripslashes($row[md_content])?></td>
									</tr>
								</tbody>
						</table>
					</div>


<script>
function send_mailing(uid) {

	if(!confirm("발송하시겠습니까?")) return false;

	common_frame.location.href="_mailing_profile.pro.php?_mode=sendpro&_mduid=<?=$_mduid?>&_uid="+uid;

}
</script>
<?PHP
	include_once("inc.footer.php");
?>










