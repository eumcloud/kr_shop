<?PHP
	include_once("inc.header.php");

/*
	[JJC001] // 세금계산서 연동
	ALTER TABLE  `odtOrderSettleComplete` ADD  `s_tax_mgtnum` VARCHAR( 50 ) NOT NULL COMMENT  '세금계산서 연동 - 자체문서관리번호',
	ADD  `s_tax_status` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '세금계산서 연동 - 상태값 ( /include/addons/barobill/include.var.php 변수 $arr_inner_state_table 참조)',
	ADD INDEX (  `s_tax_status` );

	ALTER TABLE  `odtSetup` 
	add `TAX_MODE` enum('service','test') NOT NULL default 'test' COMMENT '세금계산서 - 테스트 여부',
	add `TAX_CERTKEY` varchar(50) NOT NULL COMMENT '바로빌 CERTKEY',
	add `TAX_CHK` enum('Y','N') NOT NULL default 'N' COMMENT '바로빌 세금계산서 사용여부',
	add `TAX_BAROBILL_ID` varchar(50) NOT NULL COMMENT '세금계산서 - 바로빌 아이디',
	add `TAX_BAROBILL_NAME` varchar(50) NOT NULL COMMENT '세금계산서 - 바로빌 가입자명';
	ALTER TABLE  `odtSetup` ADD  `TAX_BAROBILL_PW` VARCHAR( 50 ) NOT NULL COMMENT  '세금계산서 - 바로빌 비밀번호';

	CREATE TABLE IF NOT EXISTS odtOrderSettleCompleteLog (
		sl_uid int(10) unsigned NOT NULL auto_increment,
		sl_suid int(10) NOT NULL default 0 COMMENT '정산완료 고유번호',
		sl_mode varchar(20) not null default 'regist' comment '연동모드 - regist:임시저장, issue:발행, cancel:발행취소, delete:삭제',
	sl_code VARCHAR( 20 ) NOT NULL COMMENT  '성고 또는 오류코드',
		sl_remark varchar(255) COMMENT '비고사항 : 성공 또는 오류내용', 
		sl_rdate datetime ,
		PRIMARY KEY  (sl_uid),
		KEY sl_suid (sl_suid)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='정산완료 - 바로빌 - 연동로그 ';
*/
?>

<form name="frm" method="post" action="_config.tax.pro.php"  target="common_frame">

					<!-- 검색영역 -->
					<div class="form_box_area">

						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>

									<tr >
										<td class="article">세금계산서 사용여부</td>
										<td class="conts"><?=_InputRadio( "TAX_CHK" , array("Y","N") ,  (!$row_setup['TAX_CHK'] ? "N" : $row_setup['TAX_CHK'] ) , "" , array("사용","미사용") , "")?></td>
									</tr>

									<tr class="auth_view">
										<td class="article"></td>
										<td class="conts">

											<B>1. 바로빌 가입</B><br>
												&nbsp;&nbsp;&nbsp;&nbsp;<B>테스트 회원가입</B> : <A HREF="http://testbed.barobill.co.kr" target='_blank'>http://testbed.barobill.co.kr</A><br>
												&nbsp;&nbsp;&nbsp;&nbsp;<B>실연동 회원가입</B> : <A HREF="http://www.barobill.co.kr" target='_blank'>http://www.barobill.co.kr</A><br>
												&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold; color:green">회원가입시 연동회원으로 가입하시고, 연동코드에 <span style="color:red">"GOBEYOND"</span> 을 입력하시기 바랍니다.</span><br>
												&nbsp;&nbsp;&nbsp;&nbsp;링크를 클릭하여 회원가입 하시기 바랍니다.<br><br>

											<B>2. 바로빌 공인인증서 등록</B><br>
												&nbsp;&nbsp;&nbsp;&nbsp;전자문서 &gt; 환경설정 &gt; 공인인증서관리<br>
												&nbsp;&nbsp;&nbsp;&nbsp;위 메뉴를 통해 세금계산서에 연동할 업체 공인인증서을 등록하시기 바랍니다.<br><br>

											<B>3. 바로빌 충전</B><br>
												&nbsp;&nbsp;&nbsp;&nbsp;마이페이지&gt; 포인트관리 &gt; 충전하기<br>
												&nbsp;&nbsp;&nbsp;&nbsp;위 메뉴를 통해 포인트를 충전하시기 바랍니다. <br>
												&nbsp;&nbsp;&nbsp;&nbsp;(단, 테스트의 경우 일정 포인트를 제공해드리고 있습니다.)<br>

										</td>
									</tr>

									<tr class="auth_view">
										<td class="article">세금계산서 서비스여부</td>
										<td class="conts">
											<?=_InputRadio( "TAX_MODE" , array("service","test") , (!$row_setup['TAX_MODE'] ? "test" : $row_setup['TAX_MODE'] ) , "" , array("서비스모드","테스트모드") , "")?>
											<?=_DescStr("테스트 모드 경우 실제 연동이 이루어지지 않습니다.")?>
										</td>
									</tr>

									<tr class="auth_view">
										<td class="article">가입정보<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<div class="line">바로빌 가입자명 : <input type="text" name="TAX_BAROBILL_NAME" class="input_text" style="width:200px" value='<?=$row_setup[TAX_BAROBILL_NAME] ?>' /></div>
											<div class="line">바로빌 가입아이디 : <input type="text" name="TAX_BAROBILL_ID" class="input_text" style="width:200px" value='<?=$row_setup[TAX_BAROBILL_ID] ?>' /></div>
											<div class="line">바로빌 가입비번 : <input type="password" name="TAX_BAROBILL_PW" class="input_text" style="width:200px" value='<?=$row_setup[TAX_BAROBILL_PW] ?>' /></div>
<?PHP

		// 상태값 추출
		if($row_setup[TAX_BAROBILL_ID] && $row_setup['TAX_CERTKEY']) {
			// 세금계산서 잔여포인트 추출 - return_balance
			include_once( dirname(__FILE__)."/../include/addons/barobill/api_ti/_tax.GetBalanceCostAmount.php");
			echo "<div class=\"line\">바로빌 잔여포인트 : <B style='color:red;font-size:15px;'>" . number_format($return_balance) . "</B>P</div>";
			echo _DescStr("<B>세금계산서 발행시 포인트가 소모되며, 바로빌 포인트가 없으면 세금계산서 발행이 되지 않습니다.</B>");
		}

?>
										</td>
									</tr>



									<input type="hidden" name="TAX_CERTKEY" value="<?=$tax_barobill_certkery?>" />



									<tr class="auth_view">
										<td class="article">사업자(세금계산서)정보</td>
										<td class="conts">
											<div class="line">상호명(법인명) : <input type="text" name="name" class="input_text" style="width:200px" value='<?=$row_company[name] ?>' /></div>
											<div class="line">대표자명 : <input type="text" name="ceoname" class="input_text" style="width:100px" value='<?=$row_company[ceoname] ?>' /></div>
											<div class="line">사업자등록번호 : <input type="text" name="number1" class="input_text" style="width:200px" value='<?=$row_company[number1] ?>' /><?=_DescStr("사업자등록번호는 현금영수증 발급 기능에 필수 항목입니다. 반드시 입력하세요.")?></div>
											<div class="line">사업장소재지 : <input type="text" name="taxaddress" class="input_text" style="width:500px" value='<?=$row_company[taxaddress] ?>' /></div>
											<div class="line">업태 : <input type="text" name="taxstatus" class="input_text" style="width:200px" value='<?=$row_company[taxstatus] ?>' /></div>
											<div class="line">종목 : <input type="text" name="taxitem" class="input_text" style="width:200px" value='<?=$row_company[taxitem] ?>' /></div>
										</td>
									</tr>

									<tr class="auth_view">
										<td class="article">담당자 정보</td>
										<td class="conts">
											<div class="line">담당휴대폰 : <input type="text" name="htel" class="input_text" style="width:150px" value='<?=$row_company[htel] ?>' /></div>
											<div class="line">담당E-mail : <input type="text" name="email" class="input_text" style="width:200px" value='<?=$row_company[email] ?>' /></div>
											<div class="line">전화번호 : <input type="text" name="tel" class="input_text" style="width:150px" value='<?=$row_company[tel] ?>' /></div>
										</td>
									</tr>

								</tbody> 
							</table>
				
					</div>
					<!-- // 검색영역 -->

<?=_submitBTNsub()?>

</form>


<script>
	/*  메인스타일 ---------- */
	var onoff = function() {
		if($("input[name='TAX_CHK']").filter(function() {if (this.checked) return this;}).val() == "Y") {
			$(".auth_view td").show();
		}
		else {
			$(".auth_view td").hide();
		}
	}
	onoff();
	$("input[name='TAX_CHK']").click(function() {onoff();});
	/*  // 메인스타일 ---------- */
</script>
<?PHP
	include_once("inc.footer.php");
?>