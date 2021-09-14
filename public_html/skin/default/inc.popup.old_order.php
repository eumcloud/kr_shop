<div class="ly_pop old_order" style="width:500px;display:none">
	<div class="title">이전 배송지선택
		<a href="#none" class="common btn_close close" title="닫기"></a>
	</div>
	
	<div class="inner">
		<table class="data_TB" summary="이전배송지목록">

			<colgroup>
				<col width="20%"><col width="*"><col width="20%">
			</colgroup>

			<thead>
				<tr>
					<th scope="col">받는사람</th>
					<th scope="col">배송지정보</th>
					<th scope="col">선택</th>
				</tr>
			</thead> 

			<tbody> 
				<?PHP
					// -- 과거배송지 추출 ---
					foreach( $sores as $k=>$v){
						echo "
							<tr>
								<td><b>$v[recname]</b></td>
								<td>".implode("-",array_filter(array($v[rechtel1],$v[rechtel2],$v[rechtel3])))."<br>
									($v[reczip1]-$v[reczip2])  $v[recaddress] $v[recaddress1]<br>
									($v[recaddress_doro]) </td>
								<td><span class='btn_style_pack'><a href='#none' onclick=\"select_addr_old('$v[reczonecode]','$v[recname]','$v[rechtel1]','$v[rechtel2]','$v[rechtel3]','$v[reczip1]','$v[reczip2]','$v[recaddress]','$v[recaddress1]','$v[recaddress_doro]');add_delivery();old_order_close();\" title='' class='btn_sm_white close'>선택<span class='right'></span></a></span></td>
							</tr>";
					}
								

					// -- 과거배송지 추출 ---
				?>

			</tbody> 
		</table>

	</div>

</div>



<script>
	function popup_old_order() {
		$('.old_order').lightbox_me({
			centered: true, 
			closeEsc: false,
			onLoad: function() { 
			}
		});
	}
</script>