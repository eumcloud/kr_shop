<?php

	include_once('inc.header.php');

	// DB 생성 여부 체크
	$row_chk = _MQ(" SHOW TABLES LIKE 'odtSiteTitle' ");
	if(count($row_chk) < 1){
		echo '
			<div class="form_box_area">
				'. _DescStr('사이트 타이틀 설정에 필요한 DB가 생성되지 않았습니다.') .'
				'. _DescStr('하단의 <em>DB생성</em>버튼을 눌러 DB를 추가해주세요.') .'
			</div>
			<div class="bottom_btn_area">
				<div class="btn_line_up_center">
					<span class="shop_btn_pack">
						<input type="button" name="" onclick="document.location.href=\'_config.title.pro.php?_mode=create\'" class="input_large red" value="DB생성">
					</span>
				</div>
			</div>
		';
		include_once('inc.footer.php');
		exit;
	}


	// 2018-11-09 SSJ :: 사이트 타이틀 기본 적용 페이지
	$arr_site_title_page = array(
		// 기본페이지
		'default' => array(
			'name'=> '기본페이지',
			'list' => array(
				'/' => array('name'=>'메인페이지', 'default'=>'{공통타이틀}'),
				'/?pn=product.main§§/?pn=product.list§§/?pn=product.promotion' => array('name'=>'상품목록', 'default'=>'{카테고리명} - {사이트명}'),
				'/?pn=product.view' => array('name'=>'상품상세보기', 'default'=>'{상품명} - {사이트명}'),
				'/?pn=product.search.list' => array('name'=>'상품검색', 'default'=>'{검색어} 검색결과 - {사이트명}'),
				'/?pn=shop.cart.list' => array('name'=>'장바구니', 'default'=>'장바구니 - {사이트명}'),
				'/?pn=shop.order.form§§/?pn=shop.order.result' => array('name'=>'주문/결제', 'default'=>'주문/결제 - {사이트명}'),
				'/?pn=shop.order.complete' => array('name'=>'주문완료', 'default'=>'주문완료 - {사이트명}'),
			),
		),

		// 회원/로그인
		'member' => array(
			'name'=> '멤버쉽',
			'list' => array(
				'/?pn=member.login.form' => array('name'=>'로그인', 'default'=>'로그인 - {사이트명}'),
				'/?pn=member.join.agree§§/?pn=member.join.form' => array('name'=>'회원가입', 'default'=>'회원가입 - {사이트명}'),
				'/?pn=member.join.complete' => array('name'=>'가입완료', 'default'=>'가입완료 - {사이트명}'),
				'/?pn=member.find.form' => array('name'=>'로그인 정보찾기', 'default'=>'로그인 정보찾기 - {사이트명}'),
				'/?pn=service.guest.order.list§§/?pn=service.guest.order.view' => array('name'=>'비회원 주문조회', 'default'=>'비회원 주문조회 - {사이트명}'),
			),
		),

		// 회원/마이페이지
		'mypage' => array(
			'name'=> '마이페이지',
			'list' => array(
				'/?pn=mypage.main' => array('name'=>'메인', 'default'=>'마이페이지 - {사이트명}'),
				'/?pn=mypage.order.list§§/?pn=mypage.order.view' => array('name'=>'주문내역', 'default'=>'주문내역 - {사이트명}'),
				'/?pn=mypage.wish.list' => array('name'=>'찜한상품', 'default'=>'찜한상품 - {사이트명}'),
				'/?pn=mypage.action_point.list' => array('name'=>'참여점수', 'default'=>'참여점수 - {사이트명}'),
				'/?pn=mypage.point.list' => array('name'=>'적립금', 'default'=>'적립금 - {사이트명}'),
				'/?pn=mypage.coupon.list' => array('name'=>'쿠폰함', 'default'=>'쿠폰함 - {사이트명}'),
				'/?pn=mypage.request.list' => array('name'=>'1:1상담내역', 'default'=>'1:1상담내역 - {사이트명}'),
				'/?pn=mypage.request.form' => array('name'=>'1:1 온라인 문의', 'default'=>'1:1 온라인 문의 - {사이트명}'),
				'/?pn=mypage.posting.list§§mypage.return.view' => array('name'=>'상품문의내역', 'default'=>'상품문의내역 - {사이트명}'),
				'/?pn=mypage.return.list' => array('name'=>'교환/반품내역', 'default'=>'교환/반품내역 - {사이트명}'),
				'/?pn=mypage.modify.form' => array('name'=>'정보수정', 'default'=>'정보수정 - {사이트명}'),
				'/?pn=mypage.leave.form' => array('name'=>'회원탈퇴', 'default'=>'회원탈퇴 - {사이트명}'),
			),
		),

		// 게시판
		'board' => array(
			'name'=> '게시판',
			'list' => array(
				'/?pn=board.list' => array('name'=>'게시판 리스트', 'default'=>'{게시판명} - {사이트명}'),
				'/?pn=board.view' => array('name'=>'게시판 상세보기', 'default'=>'{게시물제목} - {사이트명}'),
				'/?pn=board.form' => array('name'=>'게시판 글쓰기', 'default'=>'{게시판명} - {사이트명}'),
			),
		),

		// 고객센터
		'service' => array(
			'name'=> '고객센터',
			'list' => array(
				'/?pn=service.main' => array('name'=>'메인', 'default'=>'고객센터 - {사이트명}'),
				'/?pn=service.guide' => array('name'=>'이용안내', 'default'=>'이용안내 - {사이트명}'),
				'/?pn=service.partner.form' => array('name'=>'제휴/광고 문의', 'default'=>'제휴/광고 문의 - {사이트명}'),
				'/?pn=service.return.form' => array('name'=>'교환/반품신청', 'default'=>'교환/반품신청 - {사이트명}'),
			),
		),

		// 일반페이지
		'normal' => array(
			'name'=> '일반페이지',
			'list' => array(
				'/?pn=service.page.view&pageid=company' => array('name'=>'회사소개', 'default'=>'회사소개 - {사이트명}'),
				'/?pn=service.page.view&pageid=mobile' => array('name'=>'모바일쇼핑', 'default'=>'모바일쇼핑 - {사이트명}'),
				'/?pn=service.agree' => array('name'=>'이용약관', 'default'=>'이용약관 - {사이트명}'),
				'/?pn=service.privacy' => array('name'=>'개인정보처리방침', 'default'=>'개인정보처리방침 - {사이트명}'),
			),
		),
	);


	// 2018-11-09 SSJ :: 사이트 타이틀 적용가능 치환자
	$arr_site_title_replace = array(
		'/?pn=product.list' => '{카테고리명}',
		'/?pn=product.view' => '{상품명}',
		'/?pn=product.search.list' => '{검색어}',
		'/?pn=board.list' => '{게시판명}',
		'/?pn=board.view' => '{게시판명},{게시물제목}',
		'/?pn=board.form' => '{게시판명}',
	);

	// --- {공통타이틀}, {사이트명} 기본적용
	$app_replace = '<li data-text="{공통타이틀}" class="ui-draggable ui-draggable-handle"><strong class="replace_item_el">{공통타이틀}</strong> : 공통타이틀</li><li data-text="{사이트명}" class="ui-draggable ui-draggable-handle"><strong class="replace_item_el">{사이트명}</strong> : 사이트명</li>';

	/*
	CREATE TABLE  `hy30_db`.`odtSiteTitle` (
	`sst_uid` INT( 11 ) UNSIGNED NULL AUTO_INCREMENT COMMENT  '고유번호',
	`sst_name` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '페이지명',
	`sst_page` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '페이지URL',
	`sst_title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '페이지 타이틀',
	PRIMARY KEY (  `sst_uid` ) ,
	INDEX (  `sst_name` )
	) ENGINE = MYISAM COMMENT =  '사이트 타이틀 설정';
	*/


	// 2018-11-09 SSJ :: 페이지 타이틀 설정 불러오기
	$arrTitleSet = array();
	$res = _MQ_assoc(" select * from odtSiteTitle where 1 order by sst_uid ");
	if(count($res) > 0){
		foreach($res as $k=>$v){
			$v['sst_page'] = implode('§§', array_filter(explode('§§', $v['sst_page'])));
			$arrTitleSet[$v['sst_page']] = $v;
		}
	}
?>

<style>
/* ------------ 스타일 시트 불러오기 ------------------ */
.sms_code {border:1px solid #ccc; border-top:0; display:none; width:90%; padding:0 5px;}
.sms_code .inner_box {display:table; width:100%; box-sizing:border-box; table-layout:fixed;}
.sms_code ul {display:table-cell; vertical-align:middle; padding:3px 0px; }
.sms_code li {cursor: move; list-style:none; float:left; background:#eee; border:1px solid #ddd; border-radius:100px; box-sizing:border-box; letter-spacing:-1px;}
.sms_code li {height:27px; line-height:18px; line-height:17px\0; margin:3px; padding:0 15px; }
.sms_code li strong {letter-spacing:0px;}
</style>



<form name="frm" method="post" action="_config.title.pro.php" ENCTYPE="multipart/form-data" onsubmit="return submitFunc();">
<input type="hidden" name="_mode" value="modify">
<input type="hidden" name="menuUid" value="<?php echo $menuUid; ?>">

	<div class="sub_title"><span class="icon"></span><span class="title">기본 페이지</span></div>

	<div class="form_box_area">
		<!-- ● 데이터 리스트 -->
		<table class="list_TB">
			<colgroup><col width="200"><col width="200"><col width="300"><col width="*"></colgroup>
			<thead>
				<tr>
					<th scope="col">구분</th>
					<th scope="col">페이지명</th>
					<th scope="col">페이지 URL</th>
					<th scope="col">타이틀 설정</th>
				</tr>
			</thead>
			<tbody>
				<?PHP
					if(sizeof($arr_site_title_page) > 0 ) {
						foreach( $arr_site_title_page as $k=>$v ){
							if(count($v['list']) == 0) continue; // 세부 목록 체크

							// 적용 메뉴 count
							$app_cnt = count($v['list']);

							echo '<tr>';
							echo '	<th class="" rowspan="'. $app_cnt .'">'. $v['name'] .'</th>';
							// 세부목록
							$_idx = 0;
							foreach($v['list'] as $sk=>$sv){
								if($_idx > 0) echo '</tr><tr>';

								// 치환자 추출
								$_replace = $app_replace;
								if($arr_site_title_replace[$sk]){
									$_ex =  explode(',', $arr_site_title_replace[$sk]);
									foreach($_ex as $ek=>$ev) $_replace .= '<li data-text="'. $ev .'" class="ui-draggable ui-draggable-handle"><strong class="replace_item_el">'. $ev .'</strong> : '. str_replace(array('{','}'), '', $ev) .'</li>';
								}

								// 페이지 URL 추출
								$exUrl = array_filter(explode("§§", $sk));
								$strUrl = '';
								if(count($exUrl) > 0){
									foreach($exUrl as $ek=>$ev){
										if($strUrl <> '') $strUrl .= '<br>';
										$strUrl .= stripslashes($ev);
										//$strUrl .= '<a href="'. stripslashes($ev) .'" target="_blank" title="'. $sv .'">'. stripslashes($ev) .'</a>';
									}
								}
								echo '
										<td class="left">'. $sv['name'] .'</td>
										<td class="left">'. $strUrl .'</td>
										<td class="left">
											<div class="js_drop_wrap">
												<input type="hidden" name="_uid[]" value="1">
												<input type="hidden" name="_name[]" value="'. $v['name'] .' - '. $sv['name'] .'" class="js_input_name">
												<input type="hidden" name="_page[]" value="'. stripslashes($sk) .'" class="js_input_page">
												<input type="text" name="_title[]" value="'. ($arrTitleSet[$sk]['sst_title'] ? stripslashes($arrTitleSet[$sk]['sst_title']) : $sv['default']) .'" class="input_text js_drop_me js_input_title" placeholder="'. $sv['default'] .'" style="width:90%">
												<div class="sms_code"><div class="inner_box"><ul class="replace_item">'. $_replace .'</ul></div></div>
											</div>
										</td>
								';

								// 매칭되는 값은 제외
								unset($arrTitleSet[$sk]);
								$_idx++;
							}
							echo '</tr>';
						}
					}
				?>
			</tbody>
		</table>

		<div class="tip_box">
			<?php echo _DescStr('<strong>{사이트명} : </strong><em>공통</em>, [환경설정 > 기본설정]메뉴의 "기본설정"항목에 설정된 사이트명이 노출됩니다.'); ?>
			<?php echo _DescStr('<strong>{공통타이틀} : </strong><em>공통</em>, [환경설정 > 기본설정]메뉴의 "사이트 메타테그 설정"항목에 설정된 상점 타이틀이 노출됩니다.'); ?>
			<?php echo _DescStr('<strong>{카테고리명} : </strong><em>상품목록 전용</em>, 선택된 카테고리명이 노출됩니다.'); ?>
			<?php echo _DescStr('<strong>{기획전명} : </strong><em>쇼핑몰 기획전 상세 전용</em>, 선택된 기회전명이 노출됩니다.'); ?>
			<?php echo _DescStr('<strong>{브랜드명} : </strong><em>브랜드상품(브랜드 선택 시) 전용</em>, 선택된 브랜드명이 노출됩니다.'); ?>
			<?php echo _DescStr('<strong>{상품명} : </strong><em>상품상세보기 전용</em>, 선택된 상품명이 노출됩니다.'); ?>
			<?php echo _DescStr('<strong>{검색어} : </strong><em>상품검색 전용</em>, 입력한 검색어가 노출됩니다.'); ?>
			<?php echo _DescStr('<strong>{게시판명} : </strong><em>게시판 공통</em>, 선택된 게시판명이 노출됩니다.'); ?>
			<?php echo _DescStr('<strong>{게시물제목} : </strong><em>게시판 상세보기 전용</em>, 선택된 게시물제목이 노출됩니다.'); ?>
		</div>
	</div>



	<div class="sub_title"><span class="icon"></span><span class="title">추가적용 페이지</span></div>

	<div class="form_box_area">


		<div class="shop_btn_pack" style="float:none;margin-bottom:5px;"><input type="button" class="input_small blue" style="cursor: pointer; height:30px;" onclick="page_add(); return false;" value="페이지 추가하기"></div>

		<div ID="page_area" class="clear_both">

			<!-- ● 데이터 리스트 -->
			<table class='list_TB'>
				<colgroup><col width="50"><col width="200"><col width="300"><col width="*"><col width='70'></colgroup>
				<thead>
					<tr>
						<th scope="col">순번</th>
						<th scope="col">페이지명</th>
						<th scope="col">페이지 URL</th>
						<th scope="col">타이틀 설정</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
				<?PHP

					$_idx = -1;
					if(sizeof($arrTitleSet) > 0 ) {
						foreach( $arrTitleSet as $k=>$v ){
							$_idx++;

							// 치환자 추출
							$_replace = $app_replace;
							if($arr_site_title_replace[$sk]){
								$_ex =  explode(',', $arr_site_title_replace[$sk]);
								foreach($_ex as $ek=>$ev) $_replace .= '<li data-text="'. $ev .'" class="ui-draggable ui-draggable-handle"><strong class="replace_item_el">'. $ev .'</strong> : '. str_replace(array('{','}'), '', $ev) .'</li>';
							}

							// 페이지 URL 추출
							$strUrl = '<a href="'. stripslashes($v['sst_page']) .'" target="_blank" title="'. $v['sst_name'] .'">'. stripslashes($v['sst_page']) .'</a>';
							echo '
								<tr>
									<td ><span class="num">'. ($_idx+1) .'</span></td>
									<td class="left"><input type="text" name="_name[]" value="'. stripslashes($v['sst_name']) .'" class="input_text js_input_name" style="width:90%"></td>
									<td class="left"><input type="text" name="_page[]" value="'. stripslashes($v['sst_page']) .'" class="input_text js_input_page" style="width:90%"></td>
									<td class="left">
										<div class="js_drop_wrap">
											<input type="hidden" name="_uid[]" value="1">
											<input type="text" name="_title[]" value="'. ($v['sst_title'] ? stripslashes($v['sst_title']) : '{공통타이틀}') .'" class="input_text js_drop_me js_input_title" placeholder="{공통타이틀}" style="width:90%">
											<div class="sms_code"><div class="inner_box"><ul class="replace_item">'. $_replace .'</ul></div></div>
										</div>
									</td>
									<td ><div class="btn_line_up_center"><span class="shop_btn_pack"><input type="button" value="삭제" class="input_small gray" onclick="page_delete(this); return false;"></span></div></td>
								</tr>
							';
						}
						echo "
						";
					}
				?>
				</tbody>
			</table>
		</div>
		<div class="tip_box">
			<?php echo _DescStr("<strong>페이지명</strong>은 페이지 구분용으로 관리자 페이지에서만 노출됩니다. "); ?>
			<?php echo _DescStr("<strong>페이지 URL</strong>은 <em>필수항목</em>입니다. 반드시 입력해주세요."); ?>
			<?php echo _DescStr("<strong>페이지 URL</strong>은 추가적용할 페이지의 주소창에서 <em>도매인을 제외한 부분</em>을 모두 입력해주세요. (ex. http://". $_SERVER['SERVER_NAME'] ."/?pn=member.login.form => /?pn=member.login.form)"); ?>
			<?php echo _DescStr("<strong>페이지 URL</strong>은 중복될 수 없습니다. 중복될 경우 나중에 등록된 내용으로 적용됩니다."); ?>
			<?php echo _DescStr("<em>기본페이지에 포함된</em> <strong>페이지 URL</strong>을 등록할 경우 새로 추가되지 않고 기본페이지에 적용됩니다."); ?>
			<?php echo _DescStr("페이지 추가/삭제 후 <strong>확인</strong>버튼을 클릭하여야 반영됩니다."); ?>
		</div>

	</div>


	<?php echo _submitBTNsub(); ?>


</form>



<script>
$(document).ready(auto_init);
// 스크립트 셋팅
function auto_init(){
	// 치환자 끌어놓기
	$('.replace_item li').disableSelection();
	$(".replace_item li").draggable({helper: 'clone',
		 start: function(e, ui)
		 {
			var _w = ($(this).outerWidth()+1); // SSJ: 2017-09-28 넓이에 소수점이 포함될경우 클론의 텍스트가 두줄되는것 방지
			$(ui.helper).css({'width': _w + 'px'});
		 }
	});
	$(".js_drop_me").droppable({ accept: ".replace_item li", drop: function(ev, ui) {
		$(this).insertAtCaret(ui.draggable.data('text'));
	}});

	// 치환자 노출
	$('.js_drop_me').on('focus', function(){
		$this = $(this).closest('td').find('.sms_code');

		if($this.is(':visible') === false){
			$('.js_drop_wrap .sms_code').hide();
			$(this).closest('td').find('.sms_code').show();
		}
	});

	// 치환자 노출
	$('html').on('click', function(e) {
		if(!$(e.target).hasClass("js_drop_me") && !$(e.target).hasClass("replace_item") && !$(e.target).hasClass("ui-draggable") && !$(e.target).hasClass("replace_item_el")) {
			$('.js_drop_wrap .sms_code').hide();
		}
	});

	$('input').attr({'autocomplete':'off'});

	autoNum();
}


// 항목 추가
function page_add(){
	var _str = '';
	_str += '<tr>';
	_str += '	<td ><span class="num">0</span></td>';
	_str += '	<td class="left"><input type="text" name="_name[]" value="" class="input_text js_input_name" style="width:90%"></td>';
	_str += '	<td class="left"><input type="text" name="_page[]" value="" class="input_text js_input_page" style="width:90%"></td>';
	_str += '	<td class="left">';
	_str += '		<div class="js_drop_wrap">';
	_str += '			<input type="hidden" name="_uid[]" value="1">';
	_str += '			<input type="text" name="_title[]" value="" class="input_text js_drop_me js_input_title" placeholder="{공통타이틀}" style="width:90%">';
	_str += '			<div class="sms_code"><div class="inner_box"><ul class="replace_item"><?php echo $app_replace ?></ul></div></div>';
	_str += '		</div>';
	_str += '	</td>';
	_str += '	<td ><div class="btn_line_up_center"><span class="shop_btn_pack"><input type="button" value="삭제" class="input_small gray" onclick="page_delete(this); return false;"></span></div></td>';
	_str += '</tr>';
	$('#page_area tbody').append(_str);

	auto_init();
}
<?php if(sizeof($arrTitleSet) < 1 ) { echo 'page_add();'; } ?>


// 삭제 - 마지막 element 삭제
function page_delete(o){
	if( confirm("정말 삭제하시겠습니까?") ){
		$(o).closest('tr').remove();

		auto_init();
	}
}

function autoNum(){
	var num = 0;
	$('#page_area tbody .num').each(function(){
		num++;
		var str = (num+'').comma();
		$(this).text(str);
	});
}


// 폼체크
function submitFunc(){
	var chk = 0;
	$('.js_input_page').each(function(){
		if($(this).val().trim() == ''){
			$wrap = $(this).closest('tr');
			if($wrap.find('.js_input_name').val().trim() != '' || $wrap.find('.js_input_title').val().trim() != '') chk++;
		}
	});

	var result = false;
	if(chk>0){
		if(confirm('페이지 URL은 필수 입력항목입니다.\n페이지 URL이 입력되지 않은 항목은 저장되지 않습니다. \n계속진행하시겠습니까?')) result =  true;
		else result =  false;
	}else{
		result =  true;
	}

	if(result === true){
		$('.js_input_title').each(function(){
			if( $(this).val().trim() == '' ) $(this).val($(this).attr('placeholder'));
		});
		return true;
	}else{
		return false;
	}
}

// paste text at cursor position
$.fn.insertAtCaret=function(t){return this.each(function(){if(document.selection)this.focus(),sel=document.selection.createRange(),sel.text=t,this.focus();else if(this.selectionStart||"0"==this.selectionStart){var s=this.selectionStart,e=this.selectionEnd,i=this.scrollTop;this.value=this.value.substring(0,s)+t+this.value.substring(e,this.value.length),this.focus(),this.selectionStart=s+t.length,this.selectionEnd=s+t.length,this.scrollTop=i}else this.value+=t,this.focus()})};
</script>


<?php

	include_once('inc.footer.php');

?>