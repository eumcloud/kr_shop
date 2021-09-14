<?PHP
	include_once("inc.header.php");
?>
<script type='text/javascript' src='_totadm_menu.js'></script>



<form name='PUBLIC_FORM' method='post'>
					<!-- 검색영역 -->
					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
							<colgroup>
								<col width="50%"/><col width="50%"/>
							</colgroup>
							<thead>
								<tr>
									<th scope="col" class="colorset">
										<div class='btn_line_up_center'>
											<span class='shop_btn_pack'>1차 메뉴</span>
											<span class='shop_btn_pack'><span class='blank_3'></span></span>
											<span class='shop_btn_pack'><input type='button' name='' class='input_small blue' value='추가' onclick="f_add('1', '', '');"></span>
										</div>
									</th>
									<th scope="col" class="colorset">
										<div class='btn_line_up_center'>
											<span class='shop_btn_pack'>2차 메뉴</span>
											<span class='shop_btn_pack'><span class='blank_3'></span></span>
											<span class='shop_btn_pack'><input type='button' name='' class='input_small blue' value='추가' onclick="f_add('2', '', '');"></span>
										</div>
									</th>
								</tr>
							</thead> 
							<tbody> 
								<tr>
									<td class="conts"><iframe name='list1'  src='_totadm_menu.pro.php?status=code_list1' width='100%' height='450' align='center' marginwidth='0' marginheight='0' scrolling='yes' frameborder='0' style="border:2px solid #c1c1c1;"></iframe></td>
									<td class="conts"><iframe name='list2'  src='' width='100%' height='450' align='center' marginwidth='0' marginheight='0' scrolling='yes'  frameborder='0' style="border:2px solid #c1c1c1;"></iframe></td>
								</tr>
							</tbody> 
						</table>

						<table class="form_TB" summary="검색항목">
							<colgroup>
								<col width="200px"/><col width="*"/>
							</colgroup>
							<tbody> 
								<tr>
									<td class="article">코드값</td>
									<td class="conts">
										<input type='text' name='m2_code1' value='' class='input_text' style="width:80px;"  readonly />
										<input type='text' name='m2_code2' value='' class='input_text' style="width:80px;"  readonly />
										<?=_DescStr("코드값은 수정할 수 없습니다 (참고용)")?>
									</td>
								</tr>
								<tr>
									<td class="article">링크주소</td>
									<td class="conts">
										<input type='text' name='m2_link' value='' class='input_text' style="width:500px;"  maxlength='100' onfocus='gf_GetFocus(this);' onblur='gf_LostFocus(this);' />
										<?=_DescStr("예) /adm/code_form.php")?>
									</td>
								</tr>
								<tr>
									<td class="article">노출여부</td>
									<td class="conts">
										<label><input type=radio name=m2_vkbn value='y' > 노출</label>
										&nbsp;&nbsp;&nbsp;
										<label><input type=radio name=m2_vkbn value='n' > 숨김</label>
									</td>
								</tr>
							</tbody> 
						</table>
					</div>

					<!-- 버튼영역 -->
					<div class="bottom_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack">
								<input type="button" name="" class="input_large red" value="메뉴저장" onClick="f_save()">
								<input type="button" name="" class="input_large gray" value="새로고침" onClick="location.href='_totadm_menu.list.php';">
							</span>
						</div>
					</div>
					<!-- 버튼영역 -->

					<iframe name='set'  src='' width='0' height='0' align='center' marginwidth='0' marginheight='0' scrolling='no'  frameborder='0'></iframe>
</form>




<?PHP
	include_once("inc.footer.php");
?>