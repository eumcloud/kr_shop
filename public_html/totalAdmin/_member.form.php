<?PHP

	// 메뉴 지정 변수
	$app_current_link = "/totalAdmin/_member.list.php";

	include_once("inc.header.php");

	if($_mode == "modify") {

		if($id) {
			$row = _MQ(" SELECT * FROM odtMember WHERE id='" . $id . "' ");
			$serialnum = $row[serialnum];
		}
		else {
			$row = _MQ(" SELECT * FROM odtMember WHERE serialnum='" . $serialnum . "' ");
		}
	}

?>


<form name=frm method=post action=_member.pro.php enctype='multipart/form-data' >
<input type=hidden name=_mode value='<?=($_mode ? $_mode : "add")?>'>
<input type=hidden name=serialnum value='<?=$serialnum?>'>
<input type=hidden name="_PVSC" value="<?=$_PVSC?>">

	<!-- 내부 서브타이틀 -->
	<div class="sub_title"><span class="icon"></span><span class="title"><?=$row[name]?>님의 기본정보 수정</span></div>
	<!-- // 내부 서브타이틀 -->

	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">아이디(ID)</td>
					<td class="conts"><B><?=$row[id]?></B></td>
				</tr>
				<tr>
					<td class="article">이름</td>
					<td class="conts"><b><?=$row[name]?> (<?=$row[sex] =="F" ? "여" : "남";?>) <?=$row[birthy].". ".$row[birthm].". ".$row[birthd]?></b></td>
				</tr>
				<tr>
					<td class="article">E-mail</td>
					<td class="conts"><input type=text name="email" value='<?=$row[email]?>' size=30 class=input_text></td>
				</tr>
				<tr>
					<td class="article">전화번호</td>
					<? $app_tel = $row[tel1]. ($row[tel2] ? "-" . $row[tel2] : "") . ($row[tel3] ? "-" . $row[tel3] : "");?>
					<td class="conts"><input type=text name="tel" value='<?=$app_tel?>' size=20 class=input_text><?=_DescStr("하이푼(-)을 포함하시기 바랍니다.")?></td>
				</tr>
				<tr>
					<td class="article">휴대폰번호</td>
					<? $app_htel = $row[htel1]. ($row[htel2] ? "-" . $row[htel2] : "") . ($row[htel3] ? "-" . $row[htel3] : "");?>
					<td class="conts"><input type=text name="htel" value='<?=$app_htel?>' size=20 class=input_text><?=_DescStr("하이푼(-)을 포함하시기 바랍니다.")?></td>
				</tr>
				<tr>
					<td class="article">생년월일</td>
					<? $app_birth = date("Y-m-d" , strtotime("$row[birthy]-$row[birthm]-$row[birthd]")); ?>
					<td class="conts"><input type=text name="birth" value='<?=$app_birth?>' size=15 class="input_text" readonly style="cursor:pointer;"></td>
				</tr>
				<tr>
					<td class="article">성별</td>
					<td class="conts"><?=_InputRadio( "sex" , array('M','F') , $row[sex] ? $row[sex] : "M" , "" , array('남','여') , "")?></td>
				</tr>
				<tr>
					<td class="article">우편번호</td>
					<td class="conts">
						<input type=text name="zip1" value='<?=$row[zip1]?>' size=5 class=input_text>-
						<input type=text name="zip2" value='<?=$row[zip2]?>' size=5 class=input_text>
					</td>
				</tr>
				<tr>
					<td class="article">주소</td>
					<td class="conts">
						기본주소 : <input type=text name="address" value='<?=$row[address]?>' size=50 class=input_text><br>
						상세주소 : <input type=text name="address1" value='<?=$row[address1]?>' size=50 class=input_text><br>
						도로명주소 : <input type=text name="address_doro" value='<?=$row[address_doro]?>' size=70 class=input_text>
						<br/>새 우편번호 : <input type="text" name="zonecode" value="<?=$row[zonecode]?>" size="10" class="input_text"/>
					</td>
				</tr>
				<tr>
					<td class="article">메일링 수신</td>
					<td class="conts"><?=_InputRadio( "mailling" , array('Y','N') , $row[mailling] ? $row[mailling] : "Y" , "" , array('동의합니다.','동의하지 않습니다.') , "")?></td>
				</tr>
				<tr>
					<td class="article">문자 수신</td>
					<td class="conts"><?=_InputRadio( "sms" , array('Y','N') , $row[sms] ? $row[sms] : "Y" , "" , array('동의합니다.','동의하지 않습니다.') , "")?></td>
				</tr>
			</tbody> 
		</table>
	</div>

	<!-- 내부 서브타이틀 -->
	<div class="sub_title"><span class="icon"></span><span class="title"><?=$row[name]?>님의 등급, 포인트, 비밀번호 수정</span></div>
	<!-- // 내부 서브타이틀 -->
	<!-- 비밀번호 변경일, 갱신일 추가 lcy -->
	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">등급설정</td>
					<td class="conts"><?=_InputRadio( "Mlevel" , array('1','9') , $row[Mlevel] ? $row[Mlevel] : "1" , "" , array('일반회원','관리자') , "")?></td>
				</tr>
				<tr>
					<td class="article">포인트</td>
					<td class="conts"><input type=text name="point" value='<?=$row[point]?>' size=8 class=input_text> p</td>
				</tr>
				<tr>
					<td class="article">참여점수</td>
					<td class="conts"><input type=text name="action" value='<?=$row[action]?>' size=8 class=input_text> p</td>
				</tr>
				<tr>
					<td class="article">비밀번호</td>
					<td class="conts"><input type=password name="passwd" value='' size=20 class=input_text><?=_DescStr("변경할 경우에만 입력하세요.")?></td>
				</tr>
				<tr>
					<td class="article">비번확인</td>
					<td class="conts"><input type=password name="repasswd" value='' size=20 class=input_text><?=_DescStr("다시 한번 입력하세요.")?></td>
				</tr>		                 
		                  <tr>
		                    	<td class="article">비밀번호 변경일</td>
		                   	 <td class="conts"><?=date("Y년 m월 d일 H시 i분 s초",$row[cpasswd])?></td>
		                 </tr>		                 
		                 <tr>
		                   	 <td class="article">변경알림 갱신일</td>
		                    	<td class="conts"><?=date("Y년 m월 d일 H시 i분 s초",$row[cpasswd_ck])?></td>
		                </tr> 
				<tr>
					<td class="article">가입일</td>
					<td class="conts"><?=date("Y년 m월 d일 H시 i분 s초",$row[signdate])?></td>
				</tr>
				<tr>
					<td class="article">최근 접속일</td>
					<td class="conts"><?=date("Y년 m월 d일 H시 i분 s초", $row[recentdate])?></td>
				</tr>
				<tr>
					<td class="article">최근 정보변경일</td>
					<td class="conts"><?=date("Y년 m월 d일 H시 i분 s초", $row[modifydate])?></td>
				</tr>
				<tr>
					<td class="article">최근 정보변경일</td>
					<td class="conts"><?=($row[maildate] ? date("Y년 m월 d일 H시 i분 s초", $row[maildate]) : "-")?></td>
				</tr>
			</tbody> 
		</table>

	</div>

	<!-- 내부 서브타이틀 --> <!-- LMH001 -->
	<div class="sub_title"><span class="icon"></span><span class="title"><?=$row[name]?>님의 환불계좌 정보</span></div>
	<!-- // 내부 서브타이틀 -->

	<div class="form_box_area">
		<table class="form_TB" summary="검색항목">
				<colgroup>
					<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<td class="article">은행</td>
						<td class="conts">
							<select name="cancel_bank">
								<option value="">- 은행 선택 -</option>
								<? foreach($ksnet_bank as $kk=>$vv) { ?>
								<option value="<?=$kk?>" <?=$row[cancel_bank]==$kk?'selected':''?>><?=$vv?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="article">계좌번호</td>
						<td class="conts"><input type=text name="cancel_bank_account" value='<?=$row[cancel_bank_account]?>' size=20 class=input_text></td>
					</tr>
					<tr>
						<td class="article">예금주명</td>
						<td class="conts"><input type=text name="cancel_bank_name" value='<?=$row[cancel_bank_name]?>' size=20 class=input_text></td>
					</tr>
				</tbody> 
			</table>

	</div>

<?php 
    // 사용중인 선택동의 목록 추출
    // 정책설정 정보 추출 2017-09-13 SSJ
    $arr_po_type = array('optional_privacyinfo', 'optional_consign', 'optional_thirdinfo');
    $row_policy = _MQ_assoc("select * from odtPolicy where 1 order by po_uid asc ");
    $arr_policy = array();
    $arr_policy_use = array();
    foreach($row_policy as $k=>$v){
        $arr_policy[$v['po_name']][] = $v;
        $arr_policy[$v['po_name'] . '_use'] = $v['po_use'];
    }
    foreach($arr_po_type as $k=>$v){
        if($arr_policy[$v.'_use']=='Y'){
            foreach($arr_policy[$v] as $sk=>$sv){
                $arr_policy_use[$v][$sv['po_uid']] = $sv['po_title'];
            }
        }
    }

    // 선택동의 내역 추출
    $ex_agree_privacy = explode(",", $row['member_agree_privacy']);
?>
    <?php if(sizeof($arr_policy_use) > 0){ ?>
    <!-- 내부 서브타이틀 --> 
    <div class="sub_title"><span class="icon"></span><span class="title">개인정보수집 및 이용 동의 내역</span></div>
    <!-- // 내부 서브타이틀 -->

        <div class="form_box_area">
        <table class="form_TB" summary="검색항목">
                <colgroup>
                    <col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
                </colgroup>
                <tbody>
                    <tr>
                        <td class="article">[선택] 개인정보수집 및 이용 동의</td>
                        <td class="conts">
                            <?php 
                                if(sizeof($arr_policy_use['optional_privacyinfo'])>0){ 
                                    foreach($arr_policy_use['optional_privacyinfo'] as $k=>$v){
                                        echo ' - ' . trim($v) . ' : ';
                                        echo (array_search($k, $ex_agree_privacy) !== false ? '동의함' : '<strike style="color:#999">동의안함</strike>');
                                        echo '<br>';
                                    }
                                }else{
                                    echo '사용안함';
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="article">[선택] 개인정보 처리ㆍ위탁 동의</td>
                        <td class="conts">
                            <?php 
                                if(sizeof($arr_policy_use['optional_consign'])>0){ 
                                    foreach($arr_policy_use['optional_consign'] as $k=>$v){
                                        echo ' - ' . trim($v) . ' : ';
                                        echo (array_search($k, $ex_agree_privacy) !== false ? '동의함' : '<strike style="color:#999">동의안함</strike>');
                                        echo '<br>';
                                    }
                                }else{
                                    echo '사용안함';
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="article">[선택] 개인정보 제3자 제공 동의</td>
                        <td class="conts">
                            <?php 
                                if(sizeof($arr_policy_use['optional_thirdinfo'])>0){ 
                                    foreach($arr_policy_use['optional_thirdinfo'] as $k=>$v){
                                        echo ' - ' . trim($v) . ' : ';
                                        echo (array_search($k, $ex_agree_privacy) !== false ? '동의함' : '<strike style="color:#999">동의안함</strike>');
                                        echo '<br>';
                                    }
                                }else{
                                    echo '사용안함';
                                }
                            ?>
                        </td>
                    </tr>
                </tbody> 
            </table>

    </div>
    <?php } ?>

    <?=_submitBTN("_member.list.php")?>

</form>

<SCRIPT LANGUAGE="JavaScript">

	$(document).ready(function(){
		// -  validate --- 
		$("form[name=frm]").validate({
			ignore: "input[type=text]:hidden",
			rules: {
				email: { email:true},//이메일
				htel: { phone:true},//휴대폰번호
				repasswd: { equalTo: "input[name=passwd]" }// 비번확인
			},
			messages: {
				email: { email : "이메일이 올바르지 않습니다."},//이메일
				htel: { phone : "휴대폰번호가 올바르지 않습니다."},//휴대폰번호
				repasswd: { equalTo: "비밀번호가 다릅니다." }// 비번확인
			}
		});
		// - validate --- 
	});

</SCRIPT>

<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
	$(function() {
		$("input[name=birth]").datepicker({changeMonth: true,changeYear: true});
		$("input[name=birth]").datepicker( "option", "dateFormat", "yy-mm-dd" );
		$("input[name=birth]").datepicker( "option",$.datepicker.regional["ko"] );
	});
</script>



<?PHP
	include_once("inc.footer.php");
?>