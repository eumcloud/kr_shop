<?PHP

	include_once("inc.header.php");

?>
	<!-- 검색영역 -->
	<div class="form_box_area">
		<form name="searchfrm" method="get" action='<?=$PHP_SELF?>' autocomplete='off' style="border:0;padding:0;">
			<input type="hidden" name="mode" value="search">
			<table class="form_TB">
				<colgroup>
					<col width="120px"/><col width="200px"/><col width="120px"/><col width="*"/>
				</colgroup>
					<tbody>
					<tr>
						<td class="article">검색조건</td><td class="conts"><?=_InputSelect( "pass_view" , array('da_post','da_zone','da_addr') , $pass_view , "" , array('(구)우편번호','국가기초구역번호','주소') , "-검색조건-")?></td>
						<td class="article">검색어</td><td class="conts"><input type="text" name="pass_text" class="input_text" value="<?=$pass_text?>"></td>
					</tr>
				</tbody>
			</table>

			<!-- 버튼영역 -->
			<div class="top_btn_area">
				<div class="btn_line_up_center">
					<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
					<?if ($mode == search) {?>
					<span class="shop_btn_pack"><span class="blank_3"></span></span>
					<span class="shop_btn_pack"><a href="_delivery_addprice.list.php<?=$online_search_value?>" class="medium gray" title="목록" >전체목록</a></span>
					<?}?>
				</div>
			</div>
		</form>
	</div>
	<!-- // 검색영역 -->

	<div class="content_section_inner">
		<div class="ctl_btn_area">
			<span class="shop_btn_pack"><a href="#none" class="small blue" title="지역추가" onclick="javascript:window.open('_delivery_addprice.form.php?_mode=add','add_delivery_price','width=800,height=220,scrollbars=auto');">지역추가</a></span>
		</div>

	  <table class="list_TB">
	  	<colgroup>
			<col width="70px"/><col width="150px"/><col width="150px"/><col width="*"/><col width="150px"/><col width="90px"/>
		</colgroup>
	  	<thead>
		<tr>
		  <th class="colorset">NO</th>
		  <th class="colorset">(구)우편번호</th>
		  <th class="colorset">국가기초구역번호</th>
		  <th class="colorset">주소</th>
		  <th class="colorset">추가금액</th>
		  <th class="colorset"></th>
		</tr>
		</thead>
		<tbody>
<?PHP
	// odtDeliveryAddprice
	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_view !="" && $pass_text) {
		if( $pass_view == "da_post" ) { $pass_view = "REPLACE(da_post,'-','')"; $pass_text = str_replace("-","",$pass_text); }
		$s_query .= " and ${pass_view} like '%${pass_text}%' ";
	}

	$listmaxcount = 30 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from odtDeliveryAddprice $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from odtDeliveryAddprice {$s_query} ORDER BY da_addr asc limit $count , $listmaxcount ");

	$i = 0;
	foreach($res as $k=>$v) {

		$_num = $TotalCount - $count - $k ;

		echo "
			<tr>
				<td>${_num}</td>
				<td>" . $v[da_post] . "</td>
				<td>" . $v[da_zone] . "</td>
				<td style='text-align:left;'>" . $v[da_addr] . "</td>
				<td>" . number_format($v[da_price],0). "원</td>
				<td>
					";
		?>
			<span class='shop_btn_pack'>
				<input type=button value='수정' class='small gray' onclick="javascript:window.open('_delivery_addprice.form.php?_mode=modify&_uid=<?=$v[da_uid]?>&_PVSC=<?=${_PVSC}?>','add_delivery_price','width=800,height=220,scrollbars=yes');"></a>
			</span><span class="shop_btn_pack"><span class="blank_3"></span></span>
			<span class='shop_btn_pack'>
				<input type=button value='삭제' class='small red' onclick="del('_delivery_addprice.pro.php?_mode=delete&_uid=<?=$v[da_uid]?>&_PVSC=<?=${_PVSC}?>');"></a>
			</span>
		<?php
		echo "
				</td>
			</tr>
		";

		$i++;

	}

	if( $i == 0 ) echo "<tr align='center'><td colspan='5' height='200'>등록된 정보가 없습니다.</td></tr>";
?>
</tbody>
	</table>

<div class="list_paginate">
	<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=")?>
</div>

</div>


<link rel='stylesheet' href='../../include/js/jquery/jquery.ui.all.css' type=text/css>
<script src="../../include/js/jquery/jquery.ui.core.js"></script>
<script src="../../include/js/jquery/jquery.ui.widget.js"></script>
<script src="../../include/js/jquery/jquery.ui.datepicker.js"></script>
<script src="../../include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
    $(function() {
        $("#pass_sdate").datepicker({
            changeMonth: true,
            changeYear: true
        });
        $("#pass_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#pass_sdate").datepicker( "option",$.datepicker.regional["ko"] );

        $("#pass_edate").datepicker({
            changeMonth: true,
            changeYear: true
        });
        $("#pass_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#pass_edate").datepicker( "option",$.datepicker.regional["ko"] );
    });
</script>



<?PHP
	include_once("inc.footer.php");
?>