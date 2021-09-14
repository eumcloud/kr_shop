<?PHP

	include_once("inc.header.php");

	## Amail 등록된 아이디와 패스워드가 없으면 페이지를 아이디 확인 페이지로 이동
	$amail_id = $row_setup[amail_id];
	$amail_pw = $row_setup[amail_pw];

	if(!$amail_id || !$amail_pw){ 
			error_loc_msg("_config.mail.form.php","계정확인을 먼저 해 주셔야 합니다.   ");
	}
	
	$amail_id = "new_".$amail_id;

?>
				<!-- 검색영역 -->
				<div class="form_box_area" style="width:860px!important">
					<?=_DescStr("프리미엄 메일링은 익스플로러에서만 동작합니다.")?>
					<?=_DescStr("익스플로러 10 을 사용중이신분은 호환성보기를 활성화 하시기 바랍니다.")?>

				</div>
				<!-- // 검색영역 -->

	<div style="width:900px;overflow:">
	<?if($row_company[name] && $row_company[number1] && $row_company[email] && $row_company[tel] && $row_company[htel]){?>
		<iframe name="pass_postman" src="http://partners.postman.co.kr:90/home/login_partner.jsp?cooperation_id=OD&user_id=<?=$amail_id?>&user_nm=<?=urlencode(iconv("utf-8","euckr",$row_company[name]))?>&user_no=<?=urlencode($row_company[number1])?>&user_email=<?=urlencode($row_company[email])?>&user_domain=<?=$_SERVER[HTTP_HOST]?>&user_tel=<?=urlencode($row_company[tel])?>&user_cell=<?=urlencode($row_company[htel])?> " width=100% height=800 frameborder=0 scrolling="auto"></iframe>
	<?}else{?>
		<div class="form_box_area" style="width:860px!important">
			<?=_DescStr("회사 기본정보가 누락되었습니다.")?>
			<?=_DescStr("<a href='/totalAdmin/_config.default.form.php' style='font-weight:bold;color:#0072ca;'>상점설정관리 > 기본설정</a> 메뉴의 다음항목을 확인하시기 바랍니다.")?>
			<div class="guide_text">
				<div class="blue" style="padding-left:15px;line-height:15px;">
					- 사업자등록번호<br>
					- 담당E-mail <br>
					- 전화번호<br>
					- 담당휴대폰
				</div>
			</div>
		</div>
	<?}?>
	</div>

<?PHP
	include_once("inc.footer.php");
?>