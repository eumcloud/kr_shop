<?php
	include 'inc.php';

	if($_mode == 'modify'){
		// 모든 정보 삭제
		_MQ_noreturn(" delete from odtSiteTitle where 1 ");
		if(count($_uid)){
			$arrQue = array();
			foreach($_uid as $k=>$v){
				// 새로 추가
				if(trim($_page[$k]) <> '') $arrQue[] = "('". addslashes(trim($_name[$k])) ."', '§§". addslashes(trim($_page[$k])) ."§§', '". addslashes(trim($_title[$k])) ."')";
			}
			$que = " insert into odtSiteTitle (sst_name, sst_page, sst_title) values " . implode(",", $arrQue);
			_MQ_noreturn($que);
		}
		 error_loc_msg('_config.title.form.php?menuUid='.$menuUid , '정상적으로 저장되었습니다.');
	}else if($_mode == 'create'){
		// DB 생성 여부 체크
		$row_chk = _MQ(" SHOW TABLES LIKE 'odtSiteTitle' ");
		if(count($row_chk) < 1){

			// 테이블 생성
			$que = "
				CREATE TABLE  `odtSiteTitle` (
				`sst_uid` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT  '고유번호',
				`sst_name` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '페이지명',
				`sst_page` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '페이지URL',
				`sst_title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '페이지 타이틀',
				PRIMARY KEY (  `sst_uid` ) ,
				INDEX (  `sst_name` )
				) ENGINE = MYISAM COMMENT =  '사이트 타이틀 설정';
			";
			_MQ_noreturn($que);

			// 기본 데이터 등록
			$que = "
				INSERT INTO `odtSiteTitle` (`sst_uid`, `sst_name`, `sst_page`, `sst_title`) VALUES
				(1, '기본페이지 - 메인페이지', '§§/§§', '{공통타이틀}'),
				(2, '기본페이지 - 상품목록', '§§/?pn=product.main§§/?pn=product.list§§/?pn=product.promotion§§', '{카테고리명} - {사이트명}'),
				(3, '기본페이지 - 상품상세보기', '§§/?pn=product.view§§', '{상품명} - {사이트명}'),
				(4, '기본페이지 - 상품검색', '§§/?pn=product.search.list§§', '{검색어} 검색결과 - {사이트명}'),
				(5, '기본페이지 - 장바구니', '§§/?pn=shop.cart.list§§', '장바구니 - {사이트명}'),
				(6, '기본페이지 - 주문/결제', '§§/?pn=shop.order.form§§/?pn=shop.order.result§§', '주문/결제 - {사이트명}'),
				(7, '기본페이지 - 주문완료', '§§/?pn=shop.order.complete§§', '주문완료 - {사이트명}'),
				(8, '멤버쉽 - 로그인', '§§/?pn=member.login.form§§', '로그인 - {사이트명}'),
				(9, '멤버쉽 - 회원가입', '§§/?pn=member.join.agree§§/?pn=member.join.form§§', '회원가입 - {사이트명}'),
				(10, '멤버쉽 - 가입완료', '§§/?pn=member.join.complete§§', '가입완료 - {사이트명}'),
				(11, '멤버쉽 - 로그인 정보찾기', '§§/?pn=member.find.form§§', '로그인 정보찾기 - {사이트명}'),
				(12, '멤버쉽 - 비회원 주문조회', '§§/?pn=service.guest.order.list§§/?pn=service.guest.order.view§§', '비회원 주문조회 - {사이트명}'),
				(13, '마이페이지 - 메인', '§§/?pn=mypage.main§§', '마이페이지 - {사이트명}'),
				(14, '마이페이지 - 주문내역', '§§/?pn=mypage.order.list§§/?pn=mypage.order.view§§', '주문내역 - {사이트명}'),
				(15, '마이페이지 - 찜한상품', '§§/?pn=mypage.wish.list§§', '찜한상품 - {사이트명}'),
				(16, '마이페이지 - 참여점수', '§§/?pn=mypage.action_point.list§§', '참여점수 - {사이트명}'),
				(17, '마이페이지 - 적립금', '§§/?pn=mypage.point.list§§', '적립금 - {사이트명}'),
				(18, '마이페이지 - 쿠폰함', '§§/?pn=mypage.coupon.list§§', '쿠폰함 - {사이트명}'),
				(19, '마이페이지 - 1:1상담내역', '§§/?pn=mypage.request.list§§', '1:1상담내역 - {사이트명}'),
				(20, '마이페이지 - 1:1 온라인 문의', '§§/?pn=mypage.request.form§§', '1:1 온라인 문의 - {사이트명}'),
				(21, '마이페이지 - 상품문의내역', '§§/?pn=mypage.posting.list§§mypage.return.view§§', '상품문의내역 - {사이트명}'),
				(22, '마이페이지 - 교환/반품내역', '§§/?pn=mypage.return.list§§', '교환/반품내역 - {사이트명}'),
				(23, '마이페이지 - 정보수정', '§§/?pn=mypage.modify.form§§', '정보수정 - {사이트명}'),
				(24, '마이페이지 - 회원탈퇴', '§§/?pn=mypage.leave.form§§', '회원탈퇴 - {사이트명}'),
				(25, '게시판 - 게시판 리스트', '§§/?pn=board.list§§', '{게시판명} - {사이트명}'),
				(26, '게시판 - 게시판 상세보기', '§§/?pn=board.view§§', '{게시물제목} - {사이트명}'),
				(27, '게시판 - 게시판 글쓰기', '§§/?pn=board.form§§', '{게시판명} - {사이트명}'),
				(28, '고객센터 - 메인', '§§/?pn=service.main§§', '고객센터 - {사이트명}'),
				(29, '고객센터 - 이용안내', '§§/?pn=service.guide§§', '이용안내 - {사이트명}'),
				(30, '고객센터 - 제휴/광고 문의', '§§/?pn=service.partner.form§§', '제휴/광고 문의 - {사이트명}'),
				(31, '고객센터 - 교환/반품신청', '§§/?pn=service.return.form§§', '교환/반품신청 - {사이트명}'),
				(32, '일반페이지 - 회사소개', '§§/?pn=service.page.view&pageid=company§§', '회사소개 - {사이트명}'),
				(33, '일반페이지 - 모바일쇼핑', '§§/?pn=service.page.view&pageid=mobile§§', '모바일쇼핑 - {사이트명}'),
				(34, '일반페이지 - 이용약관', '§§/?pn=service.agree§§', '이용약관 - {사이트명}'),
				(35, '일반페이지 - 개인정보처리방침', '§§/?pn=service.privacy§§', '개인정보처리방침 - {사이트명}');
			";
			_MQ_noreturn($que);

			error_loc_msg('_config.title.form.php', 'DB가 추가 되었습니다. ');
		}else{
			error_loc_msg('_config.title.form.php', '이미 실행된 작업입니다. ');
		}
	}else{
		error_msg('잘못된 접근입니다.');
	}