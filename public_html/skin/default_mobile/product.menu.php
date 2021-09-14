	<?
	//ViewArr($category_total_info);
	if($category_total_info[depth1_display] == "지역") {	// 지역 스타일인 경우 좌우슬라이드 없이 지역을 선택할수 있도록 한다.
		$local_title_name = $_GET[pn] != "product.list" ? "모든지역" : "관심지역";


				// 지역 sub_cuid (묶음탭)
				$sub_assoc = explode(",",$category_total_info[depth1_lineup]);

				// 지역 sub_cuid가 없을 경우 추출
				if(!$sub_cuid) {
					$tmp = _MQ("select parent_catecode from odtCategory where catecode='".$cuid."' ");
					$tmp = _MQ("select subcate_display_choice from odtCategory where catecode='".end(explode(',',$tmp[parent_catecode]))."'");
					$sub_cuid = $tmp[subcate_display_choice];
				}

				// 지역 묶음탭
				foreach($sub_assoc as $sub_key => $sub_val) {
					$sub_row = _MQ("select * from odtCategory where subcate_display_choice = '".$sub_val."' and find_in_set('".$category_total_info[depth1_catecode]."',parent_catecode) > 0 order by cateidx asc limit 1");
					// 링크값
					$sub_url = "/m/?pn=product.".($sub_row[subcate_main] == "Y" ? "main" : "list")."&sub_cuid=".$sub_val."&cuid=".$sub_row[catecode];
					$category_1depth_html .= "<option value='".$sub_url."' data-parent='".$category_total_info[depth1_catecode].",".$sub_row[catecode]."' ".($category_total_info[depth1_catecode] == $sub_row[catecode] || ($sub_row[subcate_main] == "Y" && !$_GET[cuid]) || $sub_cuid == $sub_val ? "selected" : NULL).">".$sub_val."</option>";
				}

				// 2차 카테고리
				$sub_assoc = _MQ_assoc("select * from odtCategory where subcate_display_choice = '".$sub_cuid."' and find_in_set('".$category_total_info[depth1_catecode]."',parent_catecode) > 0 and catedepth = 2  order by cateidx asc");
				foreach($sub_assoc as $sub_key => $sub_val) {
					// 링크값
					$sub_url = "/m/?pn=product.".($sub_val[subcate_main] == "Y" ? "main" : "list")."&sub_cuid=".$sub_cuid."&cuid=".$sub_val[catecode];
					$category_2depth_html .= "<option value='".$sub_url."' data-subcuid='".$sub_cuid."' data-parent='".$category_total_info[depth1_catecode].",".$sub_val[catecode]."' ".($category_total_info[depth2_catecode] == $sub_val[catecode] || ($sub_val[subcate_main] == "Y" && !$_GET[cuid]) ? "selected" : NULL).">".$sub_val[catename]."</option>";	
				}
		?>

				<div class="page_title_area ctg_wide">
					<ul>
						<li>
							<div class="select">
								<span class="ic_pack ic_arrow"></span>
								<select id="depth1"><?=$category_1depth_html?></select>
							</div>
						</li>
						<li class="arrow"><img src="/m/images/slide_arrow2.png" alt="" /></li>
						<li>
							<div class="select">
								<span class="ic_pack ic_arrow"></span>
								<select id="depth2"><?=$category_2depth_html?></select>
							</div>
						</li>
					</ul>

					<!-- 2차 카테고리 -->
					<div style="padding: 6px; padding-top: 0; background: #ddd;">
						<div class="select">
							<span class="ic_pack ic_arrow"></span>
							<select id="depth3">
								<?=$category_3depth_html?>
							</select>
						</div>
						<div style="clear:both;"></div>
					</div>
					<script>
						$(document).ready(function(){
							$.ajax({
								data: {
									'parent': $('.page_title_area #depth2 option:selected').data('parent'),
									'cuid': '<?=$category_total_info[depth1_catecode]?>',
									'sub_cuid': $('.page_title_area #depth2 option:selected').data('subcuid')
								},
								type: 'POST',
								cache: false,
								url: '/m/ajax.product.category_3depth.php',
								success: function(data) {
									var cuid = '<?=$cuid?>';
									$('.page_title_area #depth3').html(data);
									$('.page_title_area #depth3 option#cuid_'+cuid).prop('selected',true);
								},
								error:function(request,status,error){
									alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
								}
							});
						});
						/*$('.page_bar #depth1').on('change',function(){
							$.ajax({
								data: {'parent':$(this).find('option:selected').data('parent')},
								type: 'POST',
								cache: false,
								url: '/m/ajax.local.category_3depth.php',
								success: function(data) {
									$('.page_bar #depth2').html(data);
								},
								error:function(request,status,error){
									alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
								}
							});
						});*/
						$('.page_title_area').on('change','#depth1',function(){ var link = $(this).val(); location.href=link; });
						$('.page_title_area').on('change','#depth2',function(){ var link = $(this).val(); location.href=link; });
						$('.page_title_area').on('change','#depth3',function(){ var link = $(this).val(); location.href=link; });
					</script>


				</div>
	<?
	} else if($category_total_info[depth1_display] == "기획전") {

		// product.promotion.php 에서 처리한다.

	} else { ?>
		<div class="page_title_area ctg_wide">
			<ul>
				<li>
<?

		// 상품 리스트의 탭을 위하여 2차 카테고리 추출
		$sub_assoc = _MQ_assoc("select catename,catecode,cateimg,subcate_main from odtCategory where catedepth=1 and cHidden='no' order by cateidx asc");	
		foreach($sub_assoc as $sub_key => $sub_row) {

			// 2015-03-25 Lim
			$category_2depth_first = _MQ("select catecode,subcate_main from odtCategory where find_in_set(".$sub_row[catecode].",parent_catecode) and catedepth=2 and cHidden='no' order by cateidx asc");
			$category_1depth_attr = "data-target='".($category_2depth_first['subcate_main']=='Y'?'product.main':'product.list')."' data-code='".$category_2depth_first['catecode']."'";

			if($category_total_info[depth1_catecode] == $sub_row[catecode] || ($sub_row[subcate_main] == "Y" && !$_GET[cuid])) {
			$category_1depth_html .= "<option value='".$sub_row[catecode]."' ".($category_total_info[depth1_catecode] == $sub_row[catecode] || ($sub_row[subcate_main] == "Y" && !$_GET[cuid]) ? "selected" : NULL)." ".$category_1depth_attr.">".$sub_row[catename]."</option>";
			}
		}
	?>			
		<div class="select"><span class="ic_arrow"></span>
				<select name="depth2_catecode" id="depth1_catecode" onchange="depth1_catecode_select()">
					<!-- <option value="<?=$category_total_info[depth1_catecode]?>"><?=$category_total_info[depth1_catename]?> 전체</option> -->
					<?=$category_1depth_html?>
				</select>
		</div>


				</li>
						<li class="arrow"><img src="/m/images/slide_arrow2.png" alt="" /></li>
				<li>
	<?

		// 상품 리스트의 탭을 위하여 2차 카테고리 추출
		$sub_assoc = _MQ_assoc("select catename,catecode,cateimg,subcate_main from odtCategory where find_in_set(".$category_total_info[depth1_catecode].",parent_catecode) and catedepth=2 and cHidden='no' order by cateidx asc");
		foreach($sub_assoc as $sub_key => $sub_row) {
			$category_2depth_html .= "<option value='".$sub_row[catecode]."' ".($category_total_info[depth2_catecode] == $sub_row[catecode] || ($sub_row[subcate_main] == "Y" && !$_GET[cuid]) ? "selected" : NULL).">".$sub_row[catename]."</option>";
		}
	?>			
		<div class="select"><span class="ic_arrow"></span>
				<select name="depth2_catecode" id="depth2_catecode" onchange="depth2_catecode_select()">
					<!-- <option value="<?=$category_total_info[depth2_catecode]?>"><?=$category_total_info[depth2_catename]?> 전체</option> -->
					<?=$category_2depth_html?>
				</select>
		</div>
	</li>
	</ul>
	</div>

		<script>
		// 지역 이외는 2차 카테고리
		function depth1_catecode_select() {
			/* 2015-03-25
			cuid = $("#depth1_catecode").val();
			location.href="/?pn=<?=$_GET[pn]?>&cuid="+cuid;
			*/
			var cuid =  $("#depth1_catecode").val();
			var target = $("#depth1_catecode option:selected").attr('data-target')+'';
			cuid = $("#depth1_catecode option:selected").attr('data-code');
			if(!$("#depth1_catecode option:selected").attr('data-target')) target = 'product.list';
			if(!$("#depth1_catecode option:selected").attr('data-code')) cuid = $("#depth1_catecode").val();

			location.href="/?pn="+target+"&cuid="+cuid;
		}
		function depth2_catecode_select() {
			cuid = $("#depth2_catecode").val();
			location.href="/?pn=<?=$_GET[pn]=='product.main'?'product.list':$_GET[pn]?>&cuid="+cuid;
		}
		$(document).ready(function() {

			$(".sub_product_list_order_tab").click(function() {

				$(".sub_product_list_order_tab").removeClass("hit");
				$(this).addClass("hit");

			});

		});
		</script>
	<?
	}
	?>
