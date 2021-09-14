<?php

	// 페이지 표시
	$app_current_link = "/totalAdmin/_order.list.php" . ( $_REQUEST["style"] == "b" ? "?style=b" : "" ) ;

	include dirname(__FILE__)."/wrap.header.php";


	// 검색 체크
	$s_query = " where canceled='N' AND orderstatus='Y' ";

	// 주문자명 체크
	if($search_type == "open") {$pass_ordername_tmp = $pass_ordername ? $pass_ordername : $pass_ordername_tmp;}
	else {$pass_ordername = $pass_ordername_tmp ? $pass_ordername_tmp : $pass_ordername;}

	// 무통장 관리 페이지 일 경우 처리
	$pass_paymethod = ( $style == "b" ? "B" : $pass_paymethod);
	$pass_paystatus = ( $style == "b" ? "N" : $pass_paystatus);


	if( $pass_sdate && $pass_edate ) { $s_query .= " AND left(orderdate,10) between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 검색기간
	else if( $pass_sdate ) { $s_query .= " AND left(orderdate,10) >= '". $pass_sdate ."' "; }
	else if( $pass_edate ) { $s_query .= " AND left(orderdate,10) <= '". $pass_edate ."' "; }

	if( $pass_paymethod ) { $s_query .= " AND paymethod = '". $pass_paymethod ."' "; }//결제수단
	$pass_paystatus = $pass_paystatus ? $pass_paystatus : "Y";// 결제상태 미지정시 Y 고정
	if( $pass_paystatus ) { $s_query .= " AND paystatus = '". $pass_paystatus ."' "; }//결제상태
	if( $pass_paystatus2 ) { $s_query .= " AND paystatus2 = '". $pass_paystatus2 ."' "; }//결제승인
	if( $pass_ordernum ) { $s_query .= " AND ordernum like '%". $pass_ordernum ."%' "; }//주문번호
	if( $pass_orderid ) { $s_query .= " AND orderid like '%". $pass_orderid ."%' "; }//주문자ID
	if( $pass_ordername ) { $s_query .= " AND ordername like '%". $pass_ordername ."%' "; }//주문자이름
	if( $pass_orderhtel ) { $s_query .= " AND (concat_ws('',ordertel1,ordertel2,ordertel3) like '%". rm_str($pass_orderhtel) ."%' or concat_ws('',userhtel1,userhtel2,userhtel3) like '%". rm_str($pass_orderhtel) ."%') "; }//주문자연락처
	if( $pass_member_type ) { $s_query .= " AND member_type = '". $pass_member_type ."' "; }//회원타입
	if($pass_mobile_order) {  $s_query .= " and mobile = '".$pass_mobile_order."' "; } // 구매기기 LDD002
	// --- 검색 슬래시 풀기 ---
	$pass_ordernum = stripslashes($pass_ordernum);
	$pass_orderid = stripslashes($pass_orderid);
	$pass_ordername = stripslashes($pass_ordername);
	$pass_ordername_tmp = stripslashes($pass_ordername_tmp);

	$listmaxcount = 5 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$que = " select count(*) as cnt from odtOrder $s_query ";
	$res = _MQ($que);
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$que = " 
		select 
			* ,
			concat(ordertel1 ,'-',ordertel2 ,'-',ordertel3) as ordertel ,
			concat(orderhtel1 ,'-',orderhtel2 ,'-',orderhtel3) as orderhtel
		from odtOrder 
		" . $s_query . "
		ORDER BY serialnum desc limit $count , $listmaxcount 
	";
	$res = _MQ_assoc($que);

?>



<form role="search" name="searchfrm" method="post" action="<?=$_SERVER["PHP_SELF"]?>">
<input type=hidden name=mode value=search>
<input type="hidden" name="search_type" value="close">
<input type="hidden" name="style" value="<?=$style?>"/>
	<!-- 상단에 들어가는 검색등 공간 검색닫기를 누르면  if_closed 처음설정을 닫혀있도록 해도 좋을듯.. -->
	<div class="page_top_area if_closed">

		<div class="title_box"><span class="txt">SEARCH</span>
			<div class="before_search">
				<button type="submit" class="btn_search"></button>
				<input type="search" name="pass_ordername_tmp" value="<?=$pass_ordername_tmp?>" class="input_design" placeholder="주문자명 검색">
			</div>
			<a href="#none" onclick="view_search(); return false;" class="btn_ctrl btn_close" title="검색닫기">상세검색닫기<span class="shape"></span></a>
			<a href="#none" onclick="view_search(); return false;" class="btn_ctrl btn_open" title="검색열기">상세검색열기<span class="shape"></span></a>
		</div>

		<!-- ●●●●● 검색폼 -->
		<div class="cm_search_form">
			<ul>
				<li class="">
					<span class="opt">주문자명</span>
					<div class="value"><input type="text" name="pass_ordername" value="<?=$pass_ordername?>" class="input_design" placeholder="주문자명을 입력하세요." /></div>
				</li>
				<li class="">
					<span class="opt">주문자ID</span>
					<div class="value"><input type="text" name="pass_orderid" value="<?=$pass_orderid?>" class="input_design" placeholder="주문자ID를 입력하세요." /></div>
				</li>
				<li class="">
					<span class="opt">주문번호</span>
					<div class="value"><input type="text" name="pass_ordernum" value="<?=$pass_ordernum?>" class="input_design" placeholder="주문번호를 입력하세요." /></div>
				</li>
<?PHP
	// 무통장 입금 관리 페이지에서는 보이지 않게 함
	if( $style != "b"){
?>
				<li class="ess">
					<span class="opt">결제수단</span>
					<div class="value"><div class="select"><span class="shape"></span><?=_InputSelect( "pass_paymethod" , array_keys($arr_paymethod_name) , $pass_paymethod , "" , array_values($arr_paymethod_name) , "-결제수단-")?></div></div>
				</li>
				<li class="">
					<span class="opt">결제상태</span>
					<div class="value"><?=_InputRadio_totaladmin( "pass_paystatus" , array("Y" , "N") , $pass_paystatus , "" , array("결제완료" , "결제대기") , "")?></div>
				</li>
				<li class="">
					<span class="opt">결제승인</span>
					<div class="value"><?=_InputRadio_totaladmin( "pass_paystatus2" , array("","Y" , "N") , $pass_paystatus2 , "" , array("전체","결제승인" , "승인대기") , "")?></div>
				</li>
<?}?>
				<li class="ess double">
					<span class="opt">검색기간</span>
					<div class="value">
						<div class="input_wrap"><span class="upper_txt">시작</span><input type="text" name="pass_sdate" ID="pass_sdate" value="<?=$pass_sdate?>" class="input_design input_date" placeholder="시작일선택" /></div>
						<div class="input_wrap"><span class="upper_txt">종료</span><input type="text" name="pass_edate" ID="pass_edate" value="<?=$pass_edate?>" class="input_design input_date" placeholder="종료일선택" /></div>
					</div>
				</li>
				<li class="">
					<span class="opt">연락처</span>
					<div class="value"><input type="text" name="pass_orderhtel" pattern="\d*" value="<?=$pass_orderhtel?>" class="input_design" placeholder="주문자 연락처를 입력하세요." /></div>
				</li>
				<li class="">
					<span class="opt">회원타입</span>
					<div class="value"><?=_InputRadio_totaladmin( "pass_member_type" , array("","member" , "guest") , $pass_member_type , "" , array("전체","회원" , "비회원") , "")?></div>
				</li>
				<li class="">
					<span class="opt">구매기기</span>
					<div class="value"><?=_InputRadio_totaladmin( "pass_mobile_order" , array("","Y" , "N") , $pass_mobile_order , "" , array("전체","모바일구매" , "PC구매") , "")?></div>
				</li>
			</ul>

			<!-- ●●●●● 도움말 공간 dt는 주황색 dd는 파란색 -->
			<div class="guide_box">
				<dl>
					<dt>주문정보를 삭제할 경우 상품 재고량과 회원이 사용한 적립금이 환원되지 않습니다.</dt>
					<dd>상품의 재고량과 회원이 사용한 적립금이 환원되기를 바란다면 반드시 <strong>주문취소로 처리 하셨다가 삭제</strong>하시기 바랍니다.</dd>
					<dd>회원주문인 경우 주문번호가 볼드체(굵은글씨)로 표시 됩니다.</dd>
					<dd>주문내역에 대한 엑셀파일은 검색조건에 맞는 내역만 저장됩니다.</dd>
				</dl>
			</div>
			<!-- 도움말 공간 -->

			<!-- ●●●●● 가운데정렬버튼 -->
			<div class="cm_bottom_button">
				<ul>
					<li><span class="button_pack"><input type="submit" class="btn_md_blue" value="검색하기"></span></li>
					<?if($mode == "search") :?><li><span class="button_pack"><a href="_order.list.php?style=<?=$style?>" class="btn_md_black">전체목록</a></span></li><?endif;?>
				</ul>
			</div>
			<!-- / 가운데정렬버튼 -->

		</div>

	</div>
	<!-- / 상단에 들어가는 검색등 공간 -->
</form>





	<?/*<!-- 탭메뉴필요하면 * 3개정도까지만 사용권장 -->
	<!-- <div class="page_tabmenu">
		<div class="inner_box">
			<ul>
				<li class="hit"><a href="" class="tab">탭하나</a></li>
				<li><a href="" class="tab">탭둘</a></li>
			</ul>
		</div>
	</div> -->
	<!-- / 탭메뉴 -->*/?>





<form name=frm method=post action="_order.pro.php" target="common_frame">
<input type=hidden name=_mode value=''>
<input type=hidden name=_seachcnt value='<?=$TotalCount?>'>
<input type=hidden name=_PVSC value="<?=$_PVSC?>">
<input type=hidden name=_search_que value="<?=enc('e',$s_query)?>">


<?
	if(sizeof($res) == 0 ) :
		echo "<div class='cm_no_conts'><div class='no_icon'></div><div class='gtxt'>등록된 내용이 없습니다.</div></div>"; 
	else :
?>
	<!-- 리스트 제어영역 -->
	<div class="top_ctrl_area">
		<label class="allcheck" title="모두선택"><input type="checkbox" name="allchk" /></label>
		<!-- 제어버튼 -->
		<span class="ctrl_button">
			<?if( $pass_paystatus == "Y") {  // 결제완료된 주문목록에만 나오게 함?><span class="button_pack"><a href="#none" onclick="select_auth_send();" class="btn_sm_white">선택결제승인</a></span><?}?>
			<span class="button_pack"><a href="#none" onclick="mass_cancel();" class="btn_sm_white">선택주문취소</a></span>
		</span>
		<!-- / 제어버튼 -->
	</div>
	<!-- / 리스트 제어영역 -->
<? endif;?>


	<!--  ●●●●● 내용들어가는 공간 -->
	<div class="container">

		<!-- ●●●●● 데이터리스트 주문리스트 추가 if_order -->
		<div class="data_list if_order">

<?PHP

	foreach($res as $k=>$v) {

		$_mod = "<span class='button_pack'><a href='_order.form.php?_mode=modify&ordernum=" . $v[ordernum] . "&_PVSC=" . $_PVSC . "' class='btn_sm_blue'>상세보기</a></span>";
		$_del = ($v[canceled] == "N" ? "<span class='button_pack'><a href='#none' onclick='del(\"_order.pro.php?_mode=cancel&ordernum=" . $v[ordernum] . "&_PVSC=" . $_PVSC . "\");' class='btn_sm_black'>주문취소</a></span>" : "");
		if($v[paymethod]=='V' && $v[paystatus]=='Y') {
			$_del = ($v[canceled] == "N" ? "<span class='button_pack'><a href='#none' onclick='alert(\"결제완료된 가상계좌 건은 상세페이지에서 환불계좌 정보 입력 후 취소 가능합니다.\");location.href=(\"_order.form.php?_mode=modify&ordernum=" . $v[ordernum] . "&_PVSC=" . $_PVSC . "\");' class='btn_sm_black'>주문취소</a></span>" : "");
		}

		$_num = $TotalCount - $count - $k ;


		// -- 상품정보 추출 ---
		$tmp_content = ""; // 상품정보 - 문장
		$tmp_pname = ""; // 첫번째 옵션 상품명 임시 저장
		$sque = "
			SELECT 
				op.* , ttt.ttt_value as comment3
			FROM odtOrderProduct as op 
			left join odtProduct as p on ( p.code=op.op_pcode )
			left join odtTableText as ttt on ( p.serialnum = ttt.ttt_datauid and ttt.ttt_tablename = 'odtProduct' and ttt.ttt_keyword = 'comment3')
			where op.op_oordernum='". $v[ordernum] ."' order by p.code, op.op_is_addoption desc
		";
		$sres = _MQ_assoc($sque);
		foreach($sres as $sk=>$sv) {

			// -- 발송상태 --- LMH001
			if($sv[op_cancel]=='Y') { $app_op_status = "<span class='dark'>주문취소</span>"; }
			else {
				if($sv[op_orderproduct_type] == "product") { $app_op_status = ($sv[op_delivstatus] == "Y" ? "<span class='orange'>발송완료</span>" : "<span class='light'>발송대기</span>"); } 
				else { $app_op_status = ($sv[op_delivstatus] == "Y" ? "<span class='orange'>발급완료</span>" : "<span class='light'>발급대기</span>"); }
			}
			// -- 발송상태 ---

			// -- 다수 옵션일 경우 상품명만 미리 추출 ---
			$itemName = "";// 옵션정보 초기화
			if($tmp_pname <> $sv[op_pname] && $sv[op_option1] ) {
				$tmp_pname = $sv[op_pname];
				$itemName .= $sv[op_pname];
			}

			// 쿠폰일경우 이미지/주의사항확인
			$_trigger = "N";  //쿠폰이미지 - 쿠폰주의사항
			if($sv[op_orderproduct_type]=="coupon" && !$sv[comment3]) {  $_trigger = "Y"; }

			// 옵션값 추출(OrderNumValue:주문번호 offset:주문일련번호)
			// 해당상품에 대한 옵션내역이 있으면
			if($sv[op_option1]) { $itemName .= "<div class='sub_option'>(".($sv[op_is_addoption]=="Y" ? "추가" : "선택").":".$sv[op_option1]." ".$sv[op_option2]." ".$sv[op_option3].")" . $sv[op_cnt]."개</div>"; }
			else {$itemName .= $sv[op_pname] . " ".$sv[op_cnt]."개 ";}

			// 쿠폰이미지/주의사항이 등록되어있지않으면 취소선으로 표시한다.
			$tmp_content .= "<li><span class='texticon_pack'>". $app_op_status ."</span>" . ($_trigger == "Y" ? "<strike>".$itemName."</strike>" : $itemName) . "</li>";

        }
		// -- 상품정보 추출 ---



        $orderstepArray = array(
			"before"=>"<span class='green'>주문작성중</span>",
			"ing"=>"<span class='blue'>진행중</span>",
			"ready"=>"<span class='blue'>결제전</span>",
			"wait"=>"<span class='blue'>결제대기</span>",
			"cancle"=>"<span class='light'>사용자취소</span>",
			"fail"=>"<span class='light' onclick='alert(\"".$v[ordersau]."\")' style='cursor:pointer;'>결제실패[사유]</span>",
			"finish"=>"<span class='orange'>정상처리</span>",
			"paystatus"=>"<span class='red'>결제확인</span>",
			"paystatus2"=>"<span class='red'>결제승인</span>",
			"cancle_ing"=>"<span class='purple'>취소요청</span>",
		);

		unset($_paystatus_ , $orderstep); // 변수 초기화

        // -- 결제상태 ---
        if($v[paystatus] == "Y") {$_paystatus_ = ( $v[paystatus2] =="Y" ? $orderstepArray['paystatus2'] : $orderstepArray['paystatus'] );}
        else if(in_array($v[paymethod] , array("B" , "E"))) {$_paystatus_ = $orderstepArray['ready'];}
        else {$_paystatus_ = ($v[orderstep] == "fail" ? $orderstepArray['fail'] : $orderstepArray['wait']);}
        // -- 결제상태 ---

        // -- 결제진행사항 ---
		if( !($v[paystatus] != "Y" && $v[paystatus2] != "Y" && in_array($v[paymethod] , array("B" , "E"))) ) {$orderstep = $orderstepArray[$v['orderstep']];} 
		else if($v[paystatus2] == "C") {$orderstep = $orderstepArray['cancle_ing'];}
        // -- 결제진행사항 ---

		echo "
			<dl>
				<dd>
					<div class='first_box'>
						<label class='check'><input type='checkbox' name='OrderNum[]' value='".$v[ordernum]."' class=class_ordernum /></label>
						<span class='number'>no.". $_num ."</span>
						<span class='date'>주문일 : ". date("y.m.d",strtotime($v[orderdate])) ."</span>
					</div>
					<!--  주문정보 -->
					<div class='order_info'>
						<div class='ordernum'>주문번호 : ". $v[ordernum] ."</div>
						<div class='name'>주문자 : <span class='txt'>".$v[ordername]."</span><strong>(".( $v[member_type] == "member" ? $v[orderid] : "비회원" ).")</strong></div>
						<div class='tel'>연락처 : " . (rm_str($v[ordertel]) > 0 ? "<a href='tel:".$v[ordertel]."'>".$v[ordertel]."</a>" : "") . (rm_str($v[orderhtel]) > 0 ? "<a href='tel:".$v[orderhtel]."'>".$v[orderhtel]."</a>" : "") . "</div>
						<div class='price'>결제정보 : <span class='pay'>". $arr_paymethod_name[$v[paymethod]] ."</span><span class='value'>" . ($v[tPrice] > 0 ? number_format($v[tPrice])."원" : "전액적립금") . "</span></span></div>
						<div class='order_item'>
							<div class='order_where'>
								<span class='texticon_pack checkicon'>".($v['mobile'] == 'Y' ? "<span class='green'>모바일주문</span>" : "<span class='blue'>PC주문</span>")."</span>
								". ($_paystatus_ ? "<span class='texticon_pack checkicon'>". $_paystatus_ ."</span>" : "") ."
								". ($orderstep ? "<span class='texticon_pack checkicon'>". $orderstep ."</span>" : "") ."
							</div>
							<ul>
								" . $tmp_content . "
							</ul>
						</div>
					</div>
				</dd>
				<dt>
					<div class='btn_box'>
						<ul>
							<li>". $_mod ."</li>
							<li>". $_del ."</li>
						</ul>
					</div>
				</dt>
			</dl>
		";
	}
?>

		</div>
		<!-- / 데이터리스트 -->

	</div>
	<!-- / 내용들어가는 공간 -->
</form>


	<?=pagelisting_mobile_totaladmin($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>



<?php

	include dirname(__FILE__)."/wrap.footer.php";

?>


<SCRIPT>
	// - 결제승인 ---
	 function select_auth_send() {
		 if($('.class_ordernum').is(":checked")){
			$("input[name=_mode]").val("auth");
			$("form[name=frm]")[0].submit();
		 }
		 else {
			 alert('1건 이상 선택시 결제승인이 가능합니다..');
		 }
	 }
	// - 결제승인 ---
	 // - 선택취소 ---
	 function mass_cancel() {
	 	var c=confirm('정말 주문을 취소하시겠습니까?');
	 	if(c) {
		 if($('.class_ordernum:checked').length > 0 ){
			$("input[name=_mode]").val("mass_cancel");
			$("form[name=frm]")[0].submit();
		 }
		}
		 else {
			 alert('1건 이상 선택하세요.');
		 }
	 }
	 // - 선택취소 ---
	// - 전체선택해제 ---
	$(document).ready(function() {
		$("input[name=allchk]").click(function (){
			if($(this).is(':checked')){
				$('.class_ordernum').attr('checked',true);
			}
			else {
				$('.class_ordernum').attr('checked',false);
			}
		});
	});
	// - 전체선택해제 ---
</SCRIPT>

<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
<script>
    $(function() {
        $("#pass_sdate").datepicker({changeMonth: true, changeYear: true });
        $("#pass_sdate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#pass_sdate").datepicker( "option",$.datepicker.regional["ko"] );

        $("#pass_edate").datepicker({changeMonth: true, changeYear: true });
        $("#pass_edate").datepicker( "option", "dateFormat", "yy-mm-dd" );
        $("#pass_edate").datepicker( "option",$.datepicker.regional["ko"] );
    });
</script>