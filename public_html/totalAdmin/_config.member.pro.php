<?PHP
	include "inc.php";

	if($_mode == 'modify'){

		$sque = " update odtSetup set
					s_join_auth_use = '".$_join_auth_use."',
					s_join_auth_kcb_code = '".$_join_auth_kcb_code."'
				where
					serialnum = 1";

		_MQ_noreturn($sque);

		// 설정페이지 이동
		error_loc_msg('_config.member.form.php', '정상적으로 수정되었습니다.');

	}else{

		// kcp 본인인증 설치 여부 체크
		$row_chk = _MQ(" SHOW TABLES LIKE 'odtIndividualAuthLog' ");
		if(count($row_chk) < 1){
			// DB 추가
			$que = "
				CREATE TABLE IF NOT EXISTS odtIndividualAuthLog (
				  inl_uid int(11) NOT NULL auto_increment COMMENT '고유번호',
				  inl_ordr_idxx varchar(30) NOT NULL COMMENT '본인인증주문번호',
				  inl_site_cd varchar(30) character set utf8 collate utf8_bin NOT NULL COMMENT '사이트 코드',
				  inl_cert_no varchar(30) NOT NULL COMMENT '인증 번호',
				  inl_enc_cert_data text NOT NULL COMMENT '본인인증 회원정보-암호화',
				  inl_rdate datetime NOT NULL COMMENT '등록일',
				  PRIMARY KEY  (inl_uid),
				  UNIQUE KEY inl_ordr_idxx (inl_ordr_idxx),
				  KEY inl_ordr_idxx_2 (inl_ordr_idxx)
				)
			";
			_MQ_noreturn($que);

			// 사이트 설정 변경 :: KCP 테스트코드 입력
			$que = "
				update odtSetup set
				  s_join_auth_use = 'N'
				, s_join_auth_kcb_code = 'S6186'
				where serialnum = '1'
			";
			_MQ_noreturn($que);

			// 설정페이지 이동
			error_loc_msg('_config.member.form.php', 'KCP본인확인 DB가 초기화 되었습니다.');

		}else{

			// 설정페이지 이동
			error_loc_msg('_config.member.form.php', '이미 완료된 작업입니다.');

		}

	}

	exit;
?>