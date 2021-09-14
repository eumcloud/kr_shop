<?php
include_once("inc.header.php");

$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);

// 자동 인스톨 처리
$InstallCK = mysql_query(' desc odtSMSLog ');
if(!@mysql_num_rows($InstallCK)) {

    _MQ_noreturn("
        CREATE TABLE  `odtSMSLog` (
                `idx` INT( 11 ) NOT NULL AUTO_INCREMENT COMMENT  '고유키',
                `code` VARCHAR( 5 ) NOT NULL COMMENT  '에러코드',
                `msg` VARCHAR( 255 ) NOT NULL COMMENT  '에러메시지',
                `send_num` VARCHAR( 20 ) NOT NULL COMMENT  '보내는 번호',
                `receive_num` VARCHAR( 20 ) NOT NULL COMMENT  '받는번호',
                `rdate` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00' COMMENT  '기록일',
                PRIMARY KEY (  `idx` )
            ) ENGINE = MYISAM COMMENT =  'SMS 발송 에러로그'
    ");
}

$listmaxcount = 20;
if(!$listpg) $listpg = 1;
$count = $listpg * $listmaxcount - $listmaxcount;

$res = _MQ(" select count(*) as cnt from odtSMSLog ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$res = _MQ_assoc("select * from odtSMSLog order by idx desc limit $count , $listmaxcount ");

/**
 *
 * [일회성] 조건충족을 하지 못하면 페이지 block
 * @return html
 *
 */
function sms_result_msg() {
	global $SMSUser;

	$SMSUser = onedaynet_sms_user();
	if($SMSUser['code'] == 'U01') { // 아이디 또는 비밀번호가 누락되었습니다.
		$ErrorMSG = $SMSUser['data'];
		$btn_msg = '수정하기';
		$btn_url = './_config.sms.form.php';
		$btn_target = '_self';
		$_trigger = 'on';
	}
	else if($SMSUser['code'] == 'U02') { // 잘못된 계정정보입니다.
		$ErrorMSG = $SMSUser['data'];
		$btn_msg = '수정하기';
		$btn_url = './_config.sms.form.php';
		$btn_target = '_self';
		$_trigger = 'on';
	}
	else if($SMSUser['code'] == 'U03') { // 등록되지 않은 아이피 입니다.
		$ErrorMSG = $SMSUser['data'];
		$btn_msg = '수정하기';
		$btn_url = './_config.sms.form.php';
		$btn_target = '_self';
		$_trigger = 'on';
	}
	else if($SMSUser['code'] == 'U04') { // 유효하지 않은 발신번호 입니다.
		$ErrorMSG = $SMSUser['data'];
		$btn_msg = '수정하기';
		$btn_url = './_config.default.form.php#sms_send_tel';
		$btn_target = '_self';
		$_trigger = 'on';
	}
	else if($SMSUser['code'] == 'U05') { // 발신번호 등록 후 이용가능 합니다.
		$ErrorMSG = $SMSUser['data'];
		$btn_msg = '수정하기';
		$btn_url = 'http://mobitalk.gobeyond.co.kr/pages/customer_modify.form.php';
		$btn_target = '_blank';
		$_trigger = 'on';
	}
	else if($SMSUser['code'] == 'U06') { // 발신번호 상태가 (대기/반려/만료) 입니다.
		$ErrorMSG = $SMSUser['data'];
		$btn_msg = $btn_url = $btn_target = '';
		$_trigger = 'on';
	}
	else if($SMSUser['code'] == 'U00' && $SMSUser['data'] <= 0) { // 잔액부족
		$ErrorMSG = '충전금액이 부족합니다.';
		$btn_msg = '충전하기';
		$btn_url = 'http://mobitalk.gobeyond.co.kr/';
		$btn_target = '_blank';
		$_trigger = 'on';
	}
	if($_trigger == 'on') {
		$Opacity = 8;
		$Uniq = uniqid();
		echo '<script>$(document).ready(function () {setInterval("$(\'.blink_text_'.$Uniq.'\').fadeOut().fadeIn();",1000);});</script>';
		echo '
			<div style="text-align:center;clear:both;">
				<div style="max-width: 1638px; width: 100%; height:97%; background-color:#fff;position:absolute;z-index:99; opacity: 0.'.$Opacity.';-ms-filter:\'progid:DXImageTransform.Microsoft.Alpha(Opacity='.$Opacity.'0)\';filter: alpha(opacity='.$Opacity.'0);-moz-opacity: 0.'.$Opacity.';-khtml-opacity: 0.'.$Opacity.';"></div>
				<div class="button_box" style="position:absolute; z-index:100; top:45%; left:40%; background-color:#43464F; padding:5px; border:1px solid #1D1F24; width:270px; padding: 30px 0">
					'.($btn_msg?'<span class="shop_btn_pack" style="float:none;"><a href="'.$btn_url.'" class="large red" target="'.$btn_target.'">'.$btn_msg.'</a></span>
					<div>':null).'
					<div style="color:#fff; font-weight:600; font-size:13px; margin-top:10px" class="blink_text_'.$Uniq.'">✘ '.$ErrorMSG.'</div>
					'.($btn_msg?'</div>':null).'
				</div>
			</div>
		';
	}
}
sms_result_msg();
?>
<script>
$(document).ready(function(){
	// IP 복사
	$('._copy').on('click',function(){ $(this).prop('contentEditable',true).css({'cursor':'text'}); document.execCommand('selectAll',false,null); });
	$('._copy').on('blur',function(){ $(this).prop('contentEditable',false).css({'cursor':'pointer'}); $(this).text('<?=$_SERVER[SERVER_ADDR]?>'); });
});
</script>
<div class="sub_title"><span class="icon"></span><span class="title">코드별 설명</span></div>
<div class="form_box_area">
	<table class="list_TB">
		<colgroup>
			<col width="100">
			<col width="250">
			<col width="*">
		</colgroup>
		<thead>
			<tr>
				<th scope="col" class="colorset">코드</th>
				<th scope="col" class="colorset">메시지</th>
				<th scope="col" class="colorset">상세설명</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>S02</td>
				<td style="text-align:left;">잘못된 계정정보입니다.</td>
				<td style="text-align:left;">
					<?=_DescStr('모비톡 계정이 존제하지 않거나 잘못된 정보로 설정된 경우입니다.')?>
					<?=_DescStr('<a href="./_config.sms.form.php"><strong style="color:#ff5a00">"환경설정"-"SMS(문자)설정"</strong></a>의 계정정보를 수정바랍니다.', 'orange')?>
				</td>
			</tr>
			<tr>
				<td>S03</td>
				<td style="text-align:left;">등록되지 않은 아이피 입니다.</td>
				<td style="text-align:left;">
					<?=_DescStr('홈페이지 아이피가 모비톡에 설정되지 않은 경우입니다.')?>
					<?=_DescStr('<a href="http://mobitalk.gobeyond.co.kr/pages/" target="_blank"><strong style="color:#ff5a00">"모비톡"-"메인"-"IP 보안정보 추가/삭제"-"IP추가하기"</strong></a>를 통하여 IP <strong style="cursor: pointer; outline: none; color:#0072ca;" class="_copy">'.$_SERVER['SERVER_ADDR'].'</strong>를 등록바랍니다.', 'orange')?>
				</td>
			</tr>
			<tr>
				<td>S04</td>
				<td style="text-align:left;">유효하지 않은 발신번호 입니다.</td>
				<td style="text-align:left;">
					<?=_DescStr('발신번호 형식이 올바르지 않은 상태입니다.')?>
					<?=_DescStr('<a href="./_config.default.form.php#sms_send_tel"><strong style="color:#ff5a00">"환경설정"-"기본설정"-"카피라이트정보"-"전화번호"</strong></a>를 수정바랍니다.', 'orange')?>
				</td>
			</tr>
			<tr>
				<td>S05</td>
				<td style="text-align:left;">발신번호 등록 후 이용가능 합니다.</td>
				<td style="text-align:left;">
					<?=_DescStr('모비톡에 발신번호 등록이 되어있지 않은 상태 입니다.')?>
					<?=_DescStr('<a href="http://mobitalk.gobeyond.co.kr/pages/" target="_blank"><strong style="color:#ff5a00">"모비톡"-"정보수정"-"서류등록"</strong></a>을 통하여 인증 요청바랍니다.', 'orange')?>
					<?=_DescStr('<a href="http://mobitalk.gobeyond.co.kr/pages/customer_notice.view.php?_uid=26&_PVSC=" target="_blank"><strong style="color:#ff5a00">발신번호사전등록제 안내 보기</strong></a>', 'orange')?>
				</td>
			</tr>
			<tr>
				<td>S06</td>
				<td style="text-align:left;">발신번호 상태가 (대기/반려/만료) 입니다.</td>
				<td style="text-align:left;">
					<?=_DescStr('모비톡에 등록된 발신번호 인증 상태가 대기, 반려, 만료 상태입니다.')?>
					<?=_DescStr('<a href="http://www.onedaynet.co.kr/201308/customer_03_01_customer.php" target="_blank"><strong style="color:#ff5a00">원데이넷 1:1온라인 고객문의</strong></a>를 통하여 문의바랍니다.', 'orange')?>
				</td>
			</tr>
			<tr>
				<td>S07</td>
				<td style="text-align:left;">유효하지 않은 수신번호 입니다.</td>
				<td style="text-align:left;">
					<?=_DescStr('수신자번호 형식이 올바르지 않은 상태입니다.')?>
					<?=_DescStr('수신자번호를 다시 확인 하시고 발송 바랍니다.', 'orange')?>
				</td>
			</tr>
			<tr>
				<td>S08</td>
				<td style="text-align:left;">잔여 건수가 부족합니다. (잔여: X건)</td>
				<td style="text-align:left;">
					<?=_DescStr('모비톡의 문자발송 건수가 부족하여 발송하지 못하는 상태입니다.')?>
					<?=_DescStr('<a href="http://mobitalk.gobeyond.co.kr/pages/cash_charge.form.php" target="_blank"><strong style="color:#ff5a00">"모비톡"-"충전하기"</strong></a>를 진행바랍니다.', 'orange')?>
				</td>
			</tr>
		</tbody>
	</table>
	<?=_DescStr('기타문의는 <a href="http://www.onedaynet.co.kr/201308/customer_03_01_customer.php" target="_blank"><strong style="color:#0072ca">원데이넷 1:1온라인 고객문의</strong></a>를 통하여 문의바랍니다')?>
</div>

<div class="sub_title"><span class="icon"></span><span class="title">에러로그</span></div>
<div class="content_section_inner">
	<table class="list_TB">
		<colgroup>
			<col width="60">
			<col width="100">
			<col width="*">
			<col width="150">
			<col width="150">
			<col width="150">
		</colgroup>
		<thead>
			<tr>
				<th scope="col" class="colorset">번호</th>
				<th scope="col" class="colorset">코드</th>
				<th scope="col" class="colorset">메시지</th>
				<th scope="col" class="colorset">발신번호</th>
				<th scope="col" class="colorset">수신번호</th>
				<th scope="col" class="colorset">발생일</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($res as $k=>$v) {

				$_num = $TotalCount-$count-$k;
			?>
			<tr>
				<td><?php echo $_num; ?></td>
				<td><?php echo $v['code']; ?></td>
				<td style="text-align:left;"><?php echo $v['msg']; ?></td>
				<td><?php echo $v['send_num']; ?></td>
				<td><?php echo $v['receive_num']; ?></td>
				<td><?php echo $v['rdate']; ?></td>
			</tr>
			<?php } ?>
			<?php if(count($res) <= 0) { ?>
			<tr>
				<td colspan="6" style="height:45px;">기록된 로그가 없습니다.</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<div class="list_paginate">
		<?=pagelisting($listpg, $Page, $listmaxcount, "?&{$_PVS}&listpg=", 'Y')?>
	</div>
</div>
<?php include_once('inc.footer.php'); ?>