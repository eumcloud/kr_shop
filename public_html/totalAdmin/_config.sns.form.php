<?PHP
include_once("inc.header.php");
?>

<form name="frm" method=post action=_config.sns.pro.php ENCTYPE='multipart/form-data' onsubmit="return mail_submit()" target="common_frame">
	<input type="hidden" name=_mail_checking id=_mail_checking value='0'>

	<!-- 검색영역 -->
	<div class="form_box_area">

		<table class="form_TB" summary="검색항목">
			<colgroup>
				<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<td class="article">페이스북 API </td>
					<td class="conts">
						앱 ID : <input type="text" name="Facebook_id" id="Facebook_id" class="input_text" style="width:300px;" value='<?=$row_setup[Facebook_id]?>' /><br>
						앱 시크릿 코드 : <input type="text" name="Facebook_pw" id="Facebook_pw" class="input_text" style="width:300px;" value='<?=$row_setup[Facebook_pw]?>' /><br>
						<?=_DescStr("발급 URL : <A HREF='https://developers.facebook.com/apps' target='_blank'>https://developers.facebook.com/apps</A>")?>
						<?=_DescStr("위 링크를 통해 페이스북의 새 앱 만들기를 진행합니다. ")?>
						<?=_DescStr("적용할 사이트의 도메인을 등록한 후 발급된 APP ID를 등록하시면 됩니다. 단, 등록한 도메인에서만 페이스북이 정상적으로 작동합니다.")?>
						<?=_DescStr("발급받으신 페이스북 API 정보를 입력하세요.")?>
					</td>
				</tr>

				<tr>
					<td class="article">트위터 API </td>
					<td class="conts">
						API key : <input type="text" name="twitter_key" id="twitter_key" class="input_text" style="width:300px;" value='<?=$row_setup[twitter_key]?>' /><br>
						API secret : <input type="text" name="twitter_secret" id="twitter_secret" class="input_text" style="width:300px;" value='<?=$row_setup[twitter_secret]?>' /><br>
						<?=_DescStr("발급 URL : <A HREF='https://dev.twitter.com' target='_blank'>https://dev.twitter.com</A>")?>
						<?=_DescStr("Create an app를 이용해서 만드신후 추가하시기 바랍니다.")?>
						<?=_DescStr("앱을 만드실때 도메인주소를 정확히 입력 해야 합니다, 등록된 도메인에서만 작동합니다.")?>
						<?=_DescStr("발급받으신 트위터 API 정보를 입력하세요.")?>
					</td>
				</tr>
				
				<tr>
					<td class="article">카카오톡 API </td>
					<td class="conts">API key : <input type="text" name="kakao_api" id="kakao_api" class="input_text" style="width:300px;" value='<?=$row_setup[kakao_api]?>' /><br>				<?=_DescStr("발급 URL : <A HREF='https://developers.kakao.com/' target='_blank'>https://developers.kakao.com/</A>")?>
						<?=_DescStr("위 링크를 통해 카카오톡의 새 앱 만들기를 진행합니다. ")?>
						<?=_DescStr("적용할 사이트의 도메인을 등록한 후 발급된 API key를 등록하시면 됩니다. 단, 등록한 도메인에서만 카카오톡이 정상적으로 작동합니다.")?>
						<?=_DescStr("발급받으신 카카오톡 API 정보를 입력하세요.")?>						
						<?=_DescStr("발급위치 : 카카오톡설정 > 설정 > 일반 > 앱키 > Javascript 키")?>			
						<?=_DescStr("도메인추가 : 카카오톡설정 > 설정 > 일반 > 플랫폼 추가 > 웹 > 사이트 도메인 추가")?>
					</td>
				</tr>

				<tr>
					<td class="article">구글 API KEY</td>
					<td class="conts">
						<?PHP
						echo "구글 키 추가 : <input type='text' name='_google_key[]' class='input_text' style='width:300px;' value='' /><br>";
						$ex = explode("§" , $row_setup[s_google_key]);
						foreach($ex as $k=>$v){
							if($v) {
								echo "구글 키 &nbsp;".($k + 1)."번 : <input type='text' name='_google_key[]' class='input_text' style='width:300px;' value='". $v ."' /><br>";
							}
						}
						?>
						<?=_DescStr("좌표 추출을 위한 구글 Place API 키를 입력합니다.")?>
						<?=_DescStr("발급 URL : <A HREF='https://cloud.google.com/console/project' target='_blank'>https://cloud.google.com/console/project</A>")?>
						<?=_DescStr("Create Project > APIs > Places API 를 ON 으로 변경하시기 바랍니다. ")?>
					</td>
				</tr>
				<tr>
					<td class="article">스팸방지 구글 API </td>
					<td class="conts">
						Site key : <input type="text" name="recaptcha_api" id="recaptcha_api" class="input_text" style="width:300px;" value='<?=$row_setup[recaptcha_api]?>' /><br>
						Secret key : <input type="text" name="recaptcha_secret" id="recaptcha_secret" class="input_text" style="width:300px;" value='<?=$row_setup[recaptcha_secret]?>' /><br>
					<?=_DescStr("발급 URL : <A HREF='https://www.google.com/recaptcha/' target='_blank'>https://www.google.com/recaptcha/</A>")?>
					<?=_DescStr("위 링크를 통해 새 사이트를 등록합니다. ")?>
					<?=_DescStr("적용할 사이트의 도메인을 등록한 후 발급된 API key를 등록하시면 됩니다. 단, 등록한 도메인에서만 스팸방지 기능이 정상적으로 작동합니다.")?>
					<?=_DescStr("발급받으신 구글 reCAPTCHA API 정보를 입력하세요.")?>
					</td>
				</tr>
			</tbody> 
		</table>
	</div>
	<!-- // 검색영역 -->

	<?=_submitBTNsub()?>
</form>

<script>
		function mail_submit(frm) {
			return true;
		}
</script>
<?PHP include_once("inc.footer.php"); ?>