<?
				// 거래번호
				$tno = $r[oc_tid]; // PG사 거래 번호

				#############################################################################################
				## KCP 결제 취소 START
				#############################################################################################
				require PG_M_DIR."/kcp/cfg/site_conf_inc.php";       // 환경설정 파일 include
				require PG_M_DIR."/kcp/files/pp_ax_hub_lib.php";     // library [수정불가]

				$c_PayPlus = new C_PP_CLI;
				$c_PayPlus->mf_clear();

				$tno     = trim($tno);
				$tran_cd = "00200000";
				$cust_ip = getenv("REMOTE_ADDR"); // 요청 IP

				$c_PayPlus->mf_set_modx_data( "tno",      $tno                         );  // KCP 원거래 거래번호
				$c_PayPlus->mf_set_modx_data( "mod_type", "STSC"                       );  // 원거래 변경 요청 종류
				$c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip                     );  // 변경 요청자 IP
				$c_PayPlus->mf_set_modx_data( "mod_desc", "cancel" );  // 변경 사유

				$c_PayPlus->mf_do_tx( $tno,  $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key ,  $tran_cd,    "", $g_conf_gw_url,  $g_conf_gw_port,  "payplus_cli_slib", $ordernum, $cust_ip, "3", 0, 0, $g_conf_key_dir, $g_conf_log_dir);

				$res_cd  = $c_PayPlus->m_res_cd;
				$res_msg = iconv("euckr","utf8",$c_PayPlus->m_res_msg);	// 결과 메세지

				// 취소 성공 여부
				$is_pg_status = $res_cd == "0000" ? true : false;

				// 취소결과 로그 기록
				card_cancle_log_write($tno,$res_msg);	// 카드거래번호 , 결과 메세지
				
?>