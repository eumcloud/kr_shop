<?PHP
	include_once("inc.header.php");

?>

				<!-- 검색영역 -->
				<div class="form_box_area">
					<form name=searchfrm method=post action='<?=$_SERVER["PHP_SELF"]?>'>
					<input type=hidden name=mode value=search>
					<table class="form_TB" summary="검색항목">
						<colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><!-- 마지막값은수정안함 --><col width="*"/>
						</colgroup>
						<tbody> 
							<tr>
								<td class="article">이메일주소</td>
								<td class="conts"><input type=text name='pass_email'  class=input_text value='<?=$pass_email?>'></td>
								<td class="article">수신여부</td>
								<td class="conts"><?=_InputSelect( "pass_emailsend" , array('Y','N'), $pass_emailsend, "" , array('수신가능','수신거부') , '') ?></td>
							</tr>
				
						</tbody> 
					</table>
					
					<!-- 버튼영역 -->
					<div class="top_btn_area">
						<div class="btn_line_up_center">
							<span class="shop_btn_pack btn_input_blue"><input type="submit" class="input_medium" title="검색" value="검색"></span>
							<?if ($mode == "search") {?>
							<span class="shop_btn_pack"><span class="blank_3"></span></span>
							<span class="shop_btn_pack"><a href="<?=$_SERVER['SCRIPT_NAME']?>" class="medium gray" title="목록" >전체목록</a></span>
							<?}?>							
						</div>
					</div>
					</form>
				</div>
				<!-- // 검색영역 -->


<form name=frm method=post action='_feed.pro.php' >
<input type=hidden name=_mode value=''>
<input type=hidden name=_PVSC value=<?=$_PVSC?>>
				<!-- 리스트영역 -->
				<div class="content_section_inner">
					

					<div class="ctl_btn_area">
						<span class="shop_btn_pack"><a href="#none" class="small white" onclick="selectDelete()" title="선택삭제" >선택삭제</a></span>
					</div>


					<table class="list_TB" summary="리스트기본">
						<!-- <colgroup>
							<col width="120px"/><col width="200px"/><col width="120px"/><col width="*"/>
						</colgroup> -->
						<thead>
							<tr>
								<th scope="col" class="colorset"><input type="checkbox" name="allchk"></th>
								<th scope="col" class="colorset">NO</th>
								<th scope="col" class="colorset">이메일</th>
								<th scope="col" class="colorset">수신여부상태</th>
								<th scope="col" class="colorset">구독신청일</th>
								<th scope="col" class="colorset">관리</th>
							</tr>
						</thead> 
						<tbody> 

<?PHP
	######## 검색 체크
	$s_query = " from feedTable where 1 ";
	if( $pass_email !="" ) { $s_query .= " and ft_email like '%${pass_email}%' "; }
	if( $pass_emailsend !="" ) { $s_query .= " and ft_emailsend = '${pass_emailsend}' "; }

	$s_orderby = " ORDER BY ft_regidate desc ";

	$listmaxcount = 30 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);
	$res = _MQ_assoc(" select *  $s_query $s_orderby limit $count , $listmaxcount ");
	if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v){

		$_status = "<span class='shop_btn_pack'><a href='#none'  onclick='location.href=\"_feed.pro.php?_mode=update&ft_idx=" . $v[ft_idx] . "&_PVSC=" . $_PVSC . "\"' class='small white' title='수신상태변경' >수신상태변경</a></span>";
		$_del = "<span class='shop_btn_pack'><a href='#none'  onclick='del(\"_feed.pro.php?_mode=delete&ft_idx=" . $v[ft_idx] . "&_PVSC=" . $_PVSC . "\");' class='small gray' title='삭제' >삭제</a></span>";

		$_num = $TotalCount - $count - $k ;

		$emailsend = $v[ft_emailsend] == "Y" ? "<span class='shop_state_pack'><span class='orange'>수신가능</span></span>" : "<span class='shop_state_pack'><span class='gray'>수신거부</span></span>";
		echo "
							<tr>
								<td><input type=checkbox name='chk_pcode[".$v[ft_idx]."]' value='Y' class=class_pcode></td>
								<td>" . $_num ."</td>
								<td>".$v[ft_email]."</td>
								<td>".$emailsend."</td>
								<td>" . $v[ft_regidate] . "</td>
								<td>
									<div class='btn_line_up_center'>
									${_status}
									<span class='shop_btn_pack'><span class='blank_3'></span></span>
									${_del}
									</div>
								</td>
							</tr>
		";
	}

?>

						</tbody> 
					</table>


					<!-- 페이지네이트 -->
					<div class="list_paginate">			
						<?=pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
					</div>
					<!-- // 페이지네이트 -->

			</div>

<SCRIPT>

	 function selectDelete() {
		 if($('.class_pcode').is(":checked")){
			 if(confirm("정말 삭제하시겠습니까?")){
				$("form[name=frm]").children("input[name=_mode]").val("mass_delete");
				$("form[name=frm]").attr("action" , "_feed.pro.php");
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택하시기 바랍니다.');
		 }
	 }

	// - 전체선택 / 해제
	$(document).ready(function() {
		$("input[name=allchk]").click(function (){
			if($(this).is(':checked')){
				$('.class_pcode').attr('checked',true);
			}
			else {
				$('.class_pcode').attr('checked',false);
			}
		});
	});

</SCRIPT>
<?PHP
	include_once("inc.footer.php");
?>
