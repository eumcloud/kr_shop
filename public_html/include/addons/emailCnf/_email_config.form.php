<?php # 이메일 수신동의, 수신거부에 대한 문구 설정

	# 수신동의 기본 문구 :: 한글
	$default_set= "본메일은 회원님의 수신동의 여부를 확인한 결과 회원님께서 수신동의를 하셨기에 발송되었습니다.\n본메일은 발신 전용 메일입니다. 메일수신을 원치 않으시면 [__deny__]를 눌러주십시오.\nif you do not want this of email_information, please click the [__deny__]";

/*	$is_column = IsField('odtSetup','s_set_email_txt');
	if($is_column == false){
		_MQ_noreturn("ALTER TABLE  `odtSetup` ADD  `s_set_email_txt` TEXT NOT NULL COMMENT  '이메일 수신동의 및 수신거미에 대한 문구'");
	}
*/



?>
<style>
b.substr{ color:blue;}
</style>
<form name='frm' method='post' action="/include/addons/emailCnf/_email_config.pro.php">

	<div class="form_box_area">

		<table class="form_TB" summary="검색항목">
				<colgroup>
					<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
				</colgroup>
				<tbody>

					<tr>
						<td class="article">수신동의/거부에 대한 문구</td>
						<td class="conts">
							<input type="hidden" id="default_set" value="<?=$default_set?>">
							<textarea style="width:80%; height:200px;" id="_set_email_txt" name="_set_email_txt"><?=$row_setup['s_set_email_txt'] == '' ? $default_set : $row_setup['s_set_email_txt']?></textarea>
							<?=_DescStr("광고성 관련 이메일을 보낼 시 수신동의를 한 회원에게만 발송이되며, 수신동의, 거부와 관련된 문구를 반드시 명시하셔야 하며, 수신거부 기능 또한 반드시 추가하셔 합니다.")?>
							<?=_DescStr("수신동의 문구는 html 또는 기타 스크립트등을 제외한 텍스트와 치환자로만 작성해 주셔야합니다.")?>
								<?=_DescStr("수신동의/거부에 대한 문구를 기본설정으로 되돌릴 시 <a style='color:red' onclick='set_default()'>이곳</a> 을 클릭해 주세요.")?>
						</td>
					</tr>

                    <tr>
                        <td class="article">사용가능 치환자</td>
                        <td class="conts">
                            <ul>
                                <li><b class="substr">[__deny__]</b> : 수신거부 에대한 기능 링크를 자동으로 생성해 줍니다.</li>
                            </ul>
                        </td>
                    </tr>

				</tbody> 
			</table>


	</div>
	<!-- 검색영역 -->

	<?=_submitBTNsub()?>

</form>
<script>
function set_default()
{
	var default_set = $('#default_set').val();

	$('#_set_email_txt').val(default_set);


}
</script>
