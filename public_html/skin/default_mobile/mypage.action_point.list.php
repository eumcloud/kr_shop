<?PHP
	// 로그인 체크
	member_chk();
	
	$actionPrint['상품구매'] = $actionPrint['상품문의'] = $actionPrint['상품후기'] = $actionPrint['첫 로그인'] = 0;
	$resA = _MQ_assoc("select * from odtActionLog where acID='".$row_member['id']."'");
	foreach($resA as $k=>$v) {
		$v['acTitle'] = strstr($v['acTitle']," 구매") ? "상품구매" : $v['acTitle'];
		$actionPrint[$v['acTitle']]++;
	}

	// - 넘길 변수 설정하기 ---
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---

	$page_title = "참여점수";
	include dirname(__FILE__)."/mypage.header.php";
?>
<div class="common_page">
<div class="common_inner common_full">

	<!-- 참여점수 전체단계 -->
	<div class="cm_mypage_score">
		<ul>
		<? $ii = 0; for($i=1;$i<=10;$i++){ $min = $ii>0?($ii*1000+1):0; $max = $i*1000; ?>
			<li class="<?=$row_member['actionLevel']==$i?'hit':''?>">
				<div class="box">
					<div class="level"><?=$i?></div>
					<div class="score"><?=number_format($min)?>~<?=number_format($max)?></div>
				</div>
			</li>
		<? $ii++; } ?>
		</ul>
	</div>
	<!-- 참여점수 전체단계 -->

	<!-- 나의참여점수 -->
	<div class="cm_mypage_action">

		<!-- 나의등급 -->
		<div class="my_level">
			<div class="score">나의 참여점수 등급 : <b><?=number_format($row_member['actionLevel'])?></b>등급</div>
			<div class="level">Level.<?=number_format($row_member['actionLevel'])?></div>
		</div>
		
		<!--  참여점수 통계 -->
		<div class="my_score">
			<ul>
				<li>
					<div class="opt">총 상품문의</div>
					<div class="value"><strong><?=$actionPrint['상품문의']?></strong>회</div>
				</li>
				<li>
					<div class="opt">총 로그인</div>
					<div class="value"><strong><?=$actionPrint['첫 로그인']?></strong>회</div>
				</li>
				<li>
					<div class="opt">총 참여점수</div>
					<div class="value"><strong><?=number_format($row_member['action'])?></strong>점</div>
				</li>			
			</ul>
		</div>
		
		<!-- 포인트로전환 -->
		<div class="change_point">
			<form name=frm method=post target="common_frame">
			<div class="input_box"><input type="text" name="_point" pattern="\d*" class="input_design" placeholder="1,000점 이상" /></div>
			<span class="button_pack"><a href="#none" onclick="pointChange();return false;" class="btn_md_black">적립금 전환</a></span>
			</form>
		</div>

	</div>

	<!-- 마이리스트 -->
	<div class="cm_mypage_list list_point">
		<?
		$listmaxcount = 10; // 미입력시 20개 출력
		if( !$listpg ) {$listpg = 1 ;}
		$count = $listpg * $listmaxcount - $listmaxcount;
		$s_query = " and acID ='".get_userid()."' ";

		$res = _MQ(" select count(*)  as cnt from odtActionLog where 1 ".$s_query."");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);

		$coupon_assoc = _MQ_assoc("select * from odtActionLog where 1 ".$s_query." order by acRegidate desc limit ".$count.", ".$listmaxcount."");
		if( count($coupon_assoc)==0 ) {
		?>
		<!-- 내용없을경우 모두공통 -->
		<div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">적립된 내역이 없습니다.</div></div>
		<!-- // 내용없을경우 모두공통 -->
		<? } else { ?>
		<ul>
			<? foreach($coupon_assoc as $key => $row) { ?>
			<li>
				<div class="title"><?=$row['acTitle']?></div>
				<div class="double_box">
					<div class="date_soon"><?=date('Y-m-d H:i:s',strtotime($row['acRegidate']))?></div>
					<div class="price"><?=number_format($row['acPoint'])?>점</div>							
				</div>					
			</li>
			<? } ?>
		</ul>
		<? } ?>
	</div><!-- .cm_mypage_list -->

	<div class="cm_paginate">
		<?=pagelisting_mobile($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
	</div>

	<!-- 페이지 이용도움말 -->
	<div class="cm_user_guide">
		<dl>
			<dt>참여점수 지급안내</dt>
			<?if($row_setup[s_action_join] > 0) {?><dd>회원가입 : <strong><?=number_format($row_setup[s_action_join])?>점</strong></dd><? } ?>
			<?if($row_setup[s_action_login] > 0) {?><dd>로그인 : <strong><?=number_format($row_setup[s_action_login])?>점</strong> (횟수에 상관없이 하루 한번 <?=number_format($row_setup[s_action_login])?>점 지급)</dd><? } ?>
			<?if($row_setup[s_action_order] > 0) {?><dd>상품구매 : <strong><?=number_format($row_setup[s_action_order])?>점</strong> (상품별 책정된 포인트 별도지급)</dd><? } ?>
			<?if($row_setup[s_action_talk] > 0) {?><dd>상품문의 작성 : <strong><?=number_format($row_setup[s_action_talk])?>점</strong> (1일 1회)</dd><?}?>
			<dd>참여점수는 매일 <strong>0시</strong>에 갱신됩니다.</dd>

			<dt>회원등급분류안내</dt>
			<dd>회원등급은 1등급 (최하위) ~ 10등급(최상위)으로 분류합니다.</dd>
			<dd>등급별 점수는 <strong>1,000점</strong> 단위로 계산하고 최저 0점 ~ 최고 10,000점 이상까지를 최고점수로 분류합니다.</dd>
			<dd>참여점수는 <strong>1,000점</strong>씩 쌓이면 적립금으로 전환이 가능하며 적립금 <strong><?=number_format($row_setup[paypoint])?>원</strong>이 쌓이면 현금처럼 사용할 수 있습니다.</dd>
		</dl>
	</div>
	<!-- / 페이지 이용도움말 -->

</div><!-- .common_inner -->
</div><!-- .common_page -->

<script>
	function pointChange() {
		if(document.frm._point.value == ""){
			alert('숫자를 입력해주세요.');
			document.frm._point.focus();
		}
		else if(document.frm._point.value%1000 != 0 || document.frm._point.value == 0 ){
			alert('숫자는 천단위로 입력해주세요.');
			document.frm._point.value = "";
			document.frm._point.focus();
		}
		else if(document.frm._point.value > <?=$row_member['action']?> ){
			alert('보유하신 참여점수보다 큰 숫자는 입력할 수 없습니다.');
			document.frm._point.value = "";
			document.frm._point.focus();
		}
		else {
			if(confirm('정말 전환하시겠습니까?')) {
				document.frm.action = "/pages/mypage.action_point.pro.php";
				document.frm.submit();
			}
		}
	}
</script> 