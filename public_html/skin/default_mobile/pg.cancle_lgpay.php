<?
				// 거래번호
				$LGD_TID = $r[oc_tid]; // PG사 거래 번호

				/* [결제취소 요청 사전 정리] *************/
				/*
				 *
				 * LG유플러스으로 부터 내려받은 거래번호(LGD_TID)를 가지고 취소 요청을 합니다.(파라미터 전달시 POST를 사용하세요)
				 * (승인시 LG유플러스으로 부터 내려받은 PAYKEY와 혼동하지 마세요.)
				 */
				$CST_PLATFORM               = $row_setup[P_MODE];       //LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
				$CST_MID                    = $row_setup[P_ID];            //상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
																					 //테스트 아이디는 't'를 반드시 제외하고 입력하세요.
				$LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;  //상점아이디(자동생성)    
				
				$configPath 				= PG_M_DIR . "/lgpay/lgdacom"; //LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.   
				
				require_once(PG_M_DIR. "/lgpay/lgdacom/XPayClient.php");
				/* [결제취소 요청 사전 정리] *************/

				$xpay = &new XPayClient($configPath, $CST_PLATFORM);
				$xpay->Init_TX($LGD_MID);
				$xpay->Set("LGD_TXNAME", "Cancel");
				$xpay->Set("LGD_TID", $LGD_TID);// 거래번호 지정

				// 취소 성공 여부
				$is_pg_status = $xpay->TX();	// pg 모듈 호출상태

				// 취소결과 로그 기록
				card_cancle_log_write($LGD_TID,$xpay->Response("LGD_RESPMSG", 0));	// 카드거래번호 , 결과 메세지
?>