<?PHP

	// 메뉴 지정 변수
	$app_current_link = "/totalAdmin/_banner.list.php";

	include_once("inc.header.php");

	$app_cpname = "";
	if( $_mode == "modify" ) {
		$que = "  select * from odtBanner where b_uid='". $_uid ."' ";
		$r = _MQ($que);
	}
	else {
		$sr = _MQ(" select ifnull(max(b_idx),0) as max_idx from odtBanner ");
		//$r[b_idx] = $sr[max_idx] + 10;
		$r['b_idx'] = 1;
	}

	if(!$r['b_target']) {
		$r['b_target'] = "_self";
	}
?>

<link href="/include/js/colorpicker/spectrum.css" rel="stylesheet" type="text/css" /> 
<script src="/include/js/colorpicker/spectrum.js"></script>

<form name="frm" method="post" action="_banner.pro.php" enctype="multipart/form-data" autocomplete="off" >
	<input type="hidden" name="_mode" value="<?=$_mode?>">
	<input type="hidden" name="_uid" value="<?=$_uid?>">
	<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">

	<div class="form_box_area">

	<table class="form_TB" summary="검색항목">
		<colgroup>
		<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
		</colgroup>
		<tbody>
			<tr>
				<td class="article">배너위치</td>
				<td class="conts"><?=_InputSelect( "_loc" , array_keys($arr_banner_loc) , $r['b_loc'] , " hname='배너위치' required " , array_values($arr_banner_loc) , "-위치선택-")?></td>
			</tr>
			<tr>
				<td class="article">배너이미지1</td>
				<td class="conts"><?=_PhotoForm( "..".IMG_DIR_BANNER , "_img"  , $r['b_img'] )?></td>
			</tr>
			<tr>
				<td class="article">배너이미지2 (선택사항)</td>
				<td class="conts"><?=_PhotoForm( "..".IMG_DIR_BANNER , "_img2"  , $r['b_img2'] )?></td>
			</tr>
			<tr>
				<td class="article">배너이미지3 (선택사항) </td>
				<td class="conts"><?=_PhotoForm( "..".IMG_DIR_BANNER , "_img3"  , $r['b_img3'] )?></td>
			</tr>
			<tr class="banner_color" style="display:none;">
				<td class="article">배너 배경 색상</td>
				<td class="conts" id="picker_wrap">
					<input id="picker" type="text" name="_bgcolor" value="<?=$r['b_bgcolor']?$r['b_bgcolor']:'#ff0000'?>"/>
					<script>
					$("#picker").spectrum({
						showPaletteOnly: true, togglePaletteOnly: true, preferredFormat: "hex",
						clickoutFiresChange: true, flat: true, showInput: true, allowEmpty:true,
						color: '<?=$r[b_bgcolor]?$r[b_bgcolor]:"#ff0000"?>',
						palette: [
							["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
							["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
							["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
							["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
							["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
							["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
							["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
							["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
						]
					});
					</script>
				</td>
			</tr>
			<tr>
				<td class="article">배너 링크 형식</td>
				<td class="conts"><?=_InputRadio( "_target" , array('_self','_blank') , $r['b_target'] , "" , array('같은창','새창') , "")?></td>
			</tr>
			<tr>
				<td class="article">배너 링크 정보</td>
				<td class="conts"><input type=text name=_link value="<?=$r['b_link']?>" size=80 maxlength=150 class=input_text>
				<span class="shop_btn_pack" style="float:none;"><a onClick="productWin();" class='small blue'>상품연결</a></span>
			</td>
			</tr>
			<tr>
				<td class="article">배너 타이틀</td>
				<td class="conts">
					<input type=text name=_title value="<?=$r['b_title']?>" size=80 maxlength=150 class=input_text>
					<?=_DescStr("배너 타이틀은 배너이미지에 마우스를 올렸을 경우 배너의 내용을 설명해주는 것으로 웹표준화의 주요 방침 입니다.<br>&nbsp;또한 팝업창의 경우 팝업창 타이틀에 적용됩니다.")?>
				</td>
			</tr>
			<tr class="banner_content" style="display:none;">
				<td class="article">배너 설명글</td>
				<td class="conts" id="picker_wrap">
					<textarea name="_content" class="input_text" style="width:90%;height:100px;"><?=$r['b_content']?></textarea>
					<?=_DescStr("배너 설명글은 두줄로 작성하시는 것을 권장합니다.")?>
				</td>
			</tr>
			<tr>
				<td class="article">배너 노출</td>
				<td class="conts">
					<?=_InputRadio( "_view" , array('Y','N') , ($r['b_view'] ? $r['b_view'] : "Y") , "" , array('노출','숨김') , "")?>
				</td>
			</tr>
			<tr>
				<td class="article">노출순위</td>
				<td class="conts"><input type="text" name="_idx" class="input_text" style="width:20px" value="<?=$r['b_idx']?>"  hname='노출순위'/>순위&nbsp;&nbsp;<?=_DescStr("낮은 순위가 먼저 나오며, 순위가 같을 경우 먼저 최근 등록한 순으로 나옵니다.")?></td>
			</tr>
			<tr>
				<td class="article">게재기간</td>
				<td class="conts">
					시작일 : <input type="text" name="_sdate" ID="_sdate" value="<?=$r['b_sdate']?$r['b_sdate']:date('Y-m-d')?>" class="input_text" style="width:100px" readonly style="cursor:pointer;"/> ~
					종료일 <input type="text" name="_edate" ID="_edate" value="<?=$r['b_edate']?$r['b_edate']:date('Y-m-d',strtotime('+365 day'))?>" class="input_text" style="width:100px" readonly style="cursor:pointer;"/>
				</td>
			</tr>
		</tbody> 
		</table>

	</div>

	<?=_submitBTN("_banner.list.php")?>

</form>


<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script language='javascript' src='../include/js/lib.validate.js'></script>
<script>
	$(document).ready(function(){

		<? if(in_array($r['b_loc'],array('site_top_big','mobile_main_top_big'))) { ?>
			$('.banner_color').show(); $('input[name=_bgcolor]').val('#ff0000');
		<? } else { ?>
			$('input[name=_bgcolor]').val('');
		<? } ?>

		<? if(in_array($r['b_loc'],array('site_main_md'))) { ?>
			$('.banner_content').show();
		<? } ?>

	});

	$('select[name=_loc]').on('change',function(){
		var banner_type = $(this).val();
		if(banner_type == 'site_top_big') { $('.banner_color').show(); $('input[name=_bgcolor]').val('#ff0000'); }
		else { $('.banner_color').hide(); $('input[name=_bgcolor]').val(''); }
		if(banner_type == 'site_main_md') { $('.banner_content').show(); }
		else { $('.banner_content').hide(); }
	});
    $(function() {
        $("#_sdate").datepicker({
            changeMonth: true,
            changeYear: true
        });
        $("#_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#_sdate").datepicker( "option",$.datepicker.regional["ko"] );

        $("#_edate").datepicker({
            changeMonth: true,
            changeYear: true
        });
        $("#_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#_edate").datepicker( "option",$.datepicker.regional["ko"] );
    });
    function productWin() {
        window.open('_banner.product_link.pop.php?relation_procode=<? $relation_prop_code = str_replace("/?pn=product.view&pcode=","",$r[b_link]); echo $relation_prop_code; ?>','relation', 'width=1200, height=800, scrollbars=yes');
    }
    function click_link(field) {
    	if($("input[name="+field+"]").val().length == 0 ) {
    		alert("url을 입력하세요");
    	}
    	else {
    		// 미리 /include/js/default.js 불러와야 함
    		window.open($("input[name="+field+"]").val(), 'click_link', ''); 
    	}
    }
</script>

<?PHP
	include_once("inc.footer.php");
?>