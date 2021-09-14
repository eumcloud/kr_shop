<?PHP
	include_once("inc.header.php");
?>
<script type='text/javascript' src='_category.js'></script>



<form name='PUBLIC_FORM' method='post'>
<input type=hidden name=chk_list2 value=''>
<input type=hidden name=chk_list3 value=''>
<input type=hidden name=chk_list4 value=''>
					<!-- 검색영역 -->
					<div class="form_box_area">

						<table class="form_TB" summary="검색항목">
							<colgroup>
								<col width="33%"/><col width="33%"/><col width="33%"/>
							</colgroup>
							<thead>
								<tr>
									<th scope="col" class="colorset">
										<div class='btn_line_up_center'>
											<span class='shop_btn_pack'>1차 카테고리</span>
											<span class='shop_btn_pack'><span class='blank_3'></span></span>
											<span class='shop_btn_pack'><input type='button' name='' class='input_small blue' value='추가' onclick="f_add('1','','list1');"></span>
										</div>
									</th>
									<th scope="col" class="colorset">
										<div class='btn_line_up_center'>
											<span class='shop_btn_pack'>2차 카테고리</span>
											<span class='shop_btn_pack'><span class='blank_3'></span></span>
											<span class='shop_btn_pack'><input type='button' name='' class='input_small blue' value='추가' onclick="f_add('2','','list2');"></span>
										</div>
									</th>
									<th scope="col" class="colorset">
										<div class='btn_line_up_center'>
											<span class='shop_btn_pack'>3차 카테고리</span>
											<span class='shop_btn_pack'><span class='blank_3'></span></span>
											<span class='shop_btn_pack'><input type='button' name='' class='input_small blue' value='추가' onclick="f_add('3','','list3');"></span>
										</div>
									</th>
								</tr>
							</thead> 
							<tbody> 
								<tr>
									<td class="conts"><iframe name='list1'  src='_category.pro.php?depth=1' width='100%' height='400' align='center' marginwidth='0' marginheight='0' scrolling='yes' frameborder='0' style="border:2px solid #c1c1c1;"></iframe></td>
									<td class="conts"><iframe name='list2'  src='' width='100%' height='400' align='center' marginwidth='0' marginheight='0' scrolling='yes'  frameborder='0' style="border:2px solid #c1c1c1;"></iframe></td>
									<td class="conts"><iframe name='list3'  src='' width='100%' height='400' align='center' marginwidth='0' marginheight='0' scrolling='yes'  frameborder='0' style="border:2px solid #c1c1c1;"></iframe></td>
								</tr>
							</tbody> 
						</table>

					</div>
					<iframe name='list4'  src='' width='0' height='0' align='center' marginwidth='0' marginheight='0' scrolling='no'  frameborder='0'></iframe>
					<iframe name='set'  src='' width='0' height='0' align='center' marginwidth='0' marginheight='0' scrolling='no'  frameborder='0'></iframe>

<?PHP
	include_once("inc.footer.php");
?>