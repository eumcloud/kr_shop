<?
$mailling_content = "
						<!-- 회원가입 -->
						<div style='font:normal 15px Dotum; font-weight:bold; margin-bottom:30px'>
							안녕하세요. <b style='color:#ff6c00'>".$id."</b> 회원님.
							<em style='font:normal 12px Dotum; color:#666; line-height:16px; display:block; margin-top:15px; font-weight:normal'>
							저희 <b style='color:#ff6c00'>".$row_setup[site_name]."</b>의 신규회원으로 가입해 주셔서 감사합니다.<br />
							고객님의 회원가입을 축하드리며 가입정보는 다음과 같습니다. 
							</em>
						</div>

						<table style='overflow:hidden; background:#f8f8f8; width:100%; color:#666; border-top:2px solid #666;'>
							<tr>
								<td style='text-align:center; padding:10px 0;'>고객명: <b>".$_name."님</b></td>
								<td style='text-align:center; padding:10px 0;'>아이디: <b>".$id."</b></td>
							</tr>
						</table>
						<!-- //회원가입 -->
";

?>



