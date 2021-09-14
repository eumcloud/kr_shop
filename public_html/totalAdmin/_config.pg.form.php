<?PHP
    include_once("inc.header.php");
/*
// -- 쿼리문 실행
ALTER TABLE  `odtSetup` ADD  `P_L_TYPE` ENUM(  'D',  'W' ) NOT NULL DEFAULT  'D' COMMENT  '토스페이먼츠 모듈 :: D:기존몬듈,W:웹표준모듈';
*/
    if($row_setup[P_I_TYPE] == '') {
        $sql = ' SHOW COLUMNS FROM odtSetup LIKE \'P_I_TYPE\' ';
        $result = mysql_query($sql);
        if(@mysql_num_rows($result) == false) _MQ_noreturn("ALTER TABLE  `odtSetup` ADD  `P_I_TYPE` ENUM(  'D',  'W' ) NOT NULL DEFAULT  'D' COMMENT  '이니시스 모듈선택(기본모듈D,웹표준모듈W)'");
    }

    if($row_setup[P_SKEY] == '') {
        $sql = ' SHOW COLUMNS FROM odtSetup LIKE \'P_SKEY\' ';
        $result = mysql_query($sql);
        if(@mysql_num_rows($result) == false) _MQ_noreturn("ALTER TABLE  `odtSetup` ADD  `P_SKEY` VARCHAR( 512 ) NOT NULL COMMENT  '이니시스 웹표준모듈 사인키'");
    }

    // -- 2017-01-19 LCY :: 에스크로 사인키추가
    if($row_setup[P_SID_SKEY] == '') {
        $sql = ' SHOW COLUMNS FROM odtSetup LIKE \'P_SID_SKEY\' ';
        $result = mysql_query($sql);
        if(@mysql_num_rows($result) == false) _MQ_noreturn("ALTER TABLE  `odtSetup` ADD  `P_SID_SKEY` VARCHAR( 512 ) NOT NULL COMMENT  '에스크로전용 사인키'");
    }

?>

<form name="frm" method=post action=_config.pg.pro.php ENCTYPE='multipart/form-data'>

					<!-- 검색영역 -->
					<div class="form_box_area">

						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<tr class="PG_L PG_K PG_I PG_A PG_M PG_B PG_D">
										<td class="article">PG사<span class="ic_ess" title="필수"></span></td>
										<td class="conts"><?=_InputRadio( "P_KBN" , array_keys($arr_pg_type) , $row_setup[P_KBN] , "" , array_values($arr_pg_type) , "")?></td>
									</tr>

                                    <tr class="PG_I">
                                        <td class="article">모듈선택<span class="ic_ess" title="필수"></span></td>
                                        <td class="conts">
                                        <?=_InputRadio( "P_I_TYPE" , array('D','W') , $row_setup[P_I_TYPE] ? $row_setup[P_I_TYPE] : 'D' , " " , array('기본 모듈','웹표준 모듈') , "")?>
										<?=_DescStr("사용하실 모듈을 선택해 주세요.")?>
                                        <?=_DescStr("웹표준 모듈의 경우 구글크롬, IE, Safari 에서 결제가 가능합니다.")?>
                                        </td>
                                    </tr>

                                    <tr class="PG_L">
                                        <td class="article">모듈선택<span class="ic_ess" title="필수"></span></td>
                                        <td class="conts">
                                        <?=_InputRadio( "P_L_TYPE" , array('D','W') , $row_setup[P_L_TYPE] ? $row_setup[P_L_TYPE] : 'D' , " " , array('기본 모듈','웹표준 모듈') , "")?>
										<?=_DescStr("사용하실 모듈을 선택해 주세요.")?>
                                        <?=_DescStr("웹표준 모듈의 경우 구글크롬, IE, Safari 에서 결제가 가능합니다.")?>
                                        </td>
                                    </tr>


                                    <tr class="PG_L PG_K PG_I PG_A PG_M PG_B PG_D">
                                        <td class="article">PG 아이디/사이트코드<span class="ic_ess" title="필수"></span></td>
                                        <td class="conts"><input type="text" name="P_ID" class="input_text" style="width:120px" value='<?=$row_setup[P_ID]?>' />
											<div class="PG_VIEW PG_L_SHOW PG_I_SHOW PG_A_SHOW PG_K_SHOW PG_B_SHOW">
												<?=_DescStr("PG사에서 발급받은 아이디/사이트 코드를 입력하세요.")?>
											</div>
											<div class="PG_VIEW PG_D_SHOW">
												<?=_DescStr("페이조아에서 제공하는 CPID를 입력하세요.")?>
											</div>
                                        </td>
                                    </tr>

                                    <tr class="PG_I">
                                        <td class="article">이니시스 사인키(signKey)</td>
                                        <td class="conts"><input type="text" name="P_SKEY" class="input_text" style="width:340px" value='<?=$row_setup[P_SKEY]?>' />
                                        <?=_DescStr("이니시스 모듈이 웹표준 모듈일경우 KG 이니시스에서 발급받으신 사인키를 입력해 주세요. ")?>
                                        <?=_DescStr("사인키(signKey) 의 경우  KG 이니시스 관리자 페이지의 상점정보 > 계약정보 > 부가정보의 웹결제 signkey 생성 조회 버튼 클릭 후 팝업창에서 생성 버튼을 클릭하여 발급받을 수 있습니다.","orange")?>
                                        </td>
                                    </tr>

                                    <tr class="PG_I">
                                        <td class="article">에스크로 아이디</td>
                                        <td class="conts"><input type="text" name="P_SID" class="input_text" style="width:120px" value='<?=$row_setup[P_SID]?>' />
                                        <?=_DescStr("PG사에서 발급받은 에스크로 아이디를 입력하세요.")?>
                                        </td>
                                    </tr>

                                    <tr class="PG_I">
                                        <td class="article">이니시스(에스크로) 사인키(signKey)</td>
                                        <td class="conts"><input type="text" name="P_SID_SKEY" class="input_text" style="width:340px" value='<?=$row_setup[P_SID_SKEY]?>' />
                                        <?=_DescStr("이니시스 모듈이 웹표준 모듈일경우 KG 이니시스에서 발급받으신 <b>에스크로 전용</b> 사인키를 입력해 주세요. ")?>
                                        <?=_DescStr("사인키(signKey) 의 경우  KG 이니시스 관리자 페이지의 상점정보 > 계약정보 > 부가정보의 웹결제 signkey 생성 조회 버튼 클릭 후 팝업창에서 생성 버튼을 클릭하여 발급받을 수 있습니다.","orange")?>
                                        </td>
                                    </tr>


									<tr class="PG_L PG_K PG_M">
										<td class="article">PG사 KEY/사이트 키</td>
										<td class="conts"><input type="text" name="P_PW" class="input_text" style="width:300px" value='<?=$row_setup[P_PW]?>' /></td>
									</tr>

									<tr class="PG_M ">
										<td class="article">인포뱅크 비밀번호</td>
										<td class="conts"><input type="text" name="P_KEY" class="input_text" style="width:100px" value='<?=$row_setup[P_KEY]?>' />
										<?=_DescStr("PG사에서 발급받은 KEY를 입력하세요.")?>
										</td>
									</tr>
								    <tr class="PG_D">
								        <td class="article">PG사 암호화 KEY<span class="ic_ess" title="필수"></span></td>
								        <td class="conts"><input type="text" name="P_PG_ENC_KEY" class="input_text" style="width:120px" value='<?=$row_setup[P_PG_ENC_KEY]?>' />
								        <?=_DescStr("암호화키 설정 및 변경은 페이조아 기술팀(support@kiwoompay.co.kr)으로 요청해주시기 바랍니다.")?>

								        </td>
								    </tr>

								    <tr class="PG_D">
								        <td class="article">결제 상품 유형<span class="ic_ess" title="필수"></span></td>
								        <td class="conts">
								            <?=_InputRadio( "P_PG_PRO_TYPE" , array("1","2") , $row_setup[P_PG_PRO_TYPE] , "" , array("디지털","실물") , "")?>
								            <?=_DescStr("PG가입시 선택한 결제상품 유형을 선택하세요.")?>
								        </td>
								    </tr>
									<tr class="PG_M PG_D">
										<td class="article">에스크로 사용여부</td>
										<td class="conts"><?=_InputRadio( "P_SKBN" , array("0","1") , $row_setup[P_SKBN] , "" , array("미사용","사용") , "")?></td>
									</tr>

									<tr class="PG_L PG_K PG_I PG_A PG_B PG_D">
										<td class="article">에스크로 가입여부 노출</td>
										<td class="conts"><input type="checkbox" name="s_view_escrow_join_info" class="input_text" value='Y' <?=$row_setup[s_view_escrow_join_info] == "Y" ? "checked" : NULL;?> /> 에스크로 가입여부 노출
										<?=_DescStr("에스크로 가입 조회 배너가 사이트 하단에 노출됩니다.")?></td>
									</tr>

									<tr class="PG_L PG_B PG_D">
										<td class="article">활성화여부</td>
										<td class="conts"><?=_InputRadio( "P_MODE" , array("service","test") , $row_setup[P_MODE] , "" , array("실결제 모드","테스트 모드") , "")?>
										<?=_DescStr("테스트 모드 경우 실 결제가 이루어지지 않습니다.")?>
										</td>
									</tr>
									<tr class="PG_L PG_K PG_I PG_A PG_M">
										<td class="article">가상계좌 입금기한</td>
										<td class="conts">
										<input type="text" class="input_text" name="_P_V_DATE" value="<?=$row_setup[P_V_DATE]?>"/> 일
										<?=_DescStr("가상계좌 결제시 계좌번호 발급 후 입금까지 기한을 설정합니다. 기한내에 입금하지 않으면 해당 계좌번호로 입금할 수 없게 됩니다. 기본은 10일이며 14일을 넘길 수 없습니다.")?>

										</td>
									</tr>

                                    <tr class="PG_I"><!-- 이니시스 -->
                                        <td class="article">참고사항</td>
                                        <td class="conts">
※ 이니시스 테스트 아이디는 <b>INIpayTest</b> 입니다 (테스트 결제시에는 카드만 가능합니다)<br>
※ 이니시스 웹표준 모듈 사용시 테스트 사인키는  <b>SU5JTElURV9UUklQTEVERVNfS0VZU1RS</b> 입니다<br>
※ 이니시스 승인절차가 끝나시면 필히 상위에 고객님의 key 아이디를 등록해야만 정상 결제가 이루어 집니다<br>
    이니시스에서 받으신 키파일을 압축을 푸시면 디렉토리가 생성됩니다 사이트에 ftp로 접속하셔서<br>
   PC : /pg/pc/inicis/key<br>
   모바일 : /pg/m/inicis/key<br>
   경로에 디렉토리까지 포함한 파일전체를 올려주십시오<br>
    (key 파일 디렉토리가 없으면 아이디를 등록하셔도 정상결제가 되지 않습니다)<br>
<!--
※ 에스크로 이용시 에스크로 아이디를 등록하시고 위와 같은 방법으로 /public_html/INIescrow41/key 에 키파일을 올려주십시오
-->
※ 결제취소시에는 카드결제같은 경우에는 사이트에서 취소처리 하시면 pg사와 연동하여 카드사까지 한번에 취소처리가 됩니다<br>
※ 실시간계좌이체같은 경우에는 사이트에서 취소처리후 pg사 관리자모드에서 또한번 취소처리를 해야 합니다<br>
※ 결제취소시 사이트에서 먼저 하시고 pg사에서 해야 합니다 반대로 할 경우 오류가 발생합니다.<br>
※ 무통장 입금이나 가상계좌 결제 후 현금영수증을 신청한 경우, 가맹점관리자 페이지에서 직접 발급해야 합니다.<br>
※ 가맹점관리자 > 거래내역 > 가상계좌 > 입금통보방식선택 메뉴의 입금내역통보URL 항목에 <strong>http://<?=$_SERVER[HTTP_HOST]?>/pages/shop.order.result_inicis_vacctinput.php</strong> 를 복사해 넣으셔야 합니다.
<!-- ※ <B>이니시스 키파일 업로드방법</B><br>
<img src="./images/introkey.png"> -->
										</td>
									</tr>

									<tr class="PG_A">
										<td class="article">참고사항</td>
										<td class="conts">
※ 올더게이트 테스트 아이디는 <b>aegis</b> 입니다 (테스트 결제시에는 카드만 가능합니다)<br>
※ 올더게이트에서 승인절차가 끝나시면 필히 상위에 고객님의 올더게이트 아이디를 등록해야만 정상 결제가 이루어 집니다<br>
※ 결제취소시에는 카드결제같은 경우에는 사이트에서 취소처리 하시면 pg사와 연동하여 카드사까지 한번에 취소처리가 됩니다<br>
※ 실시간계좌이체같은 경우에는 사이트에서 취소처리후 pg사 관리자모드에서 또한번 취소처리를 해야 합니다<br>
※ 결제취소시 사이트에서 먼저 하시고 pg사에서 해야 합니다 반대로 할 경우 오류가 발생합니다<br/>
※ <strong>현금영수증은 자동으로 발급되지 않습니다. 주문내역에서 확인하시고 pg사에서 발급해야 합니다</strong>
										</td>
									</tr>

									<tr class="PG_K">
										<td class="article">참고사항</td>
										<td class="conts">
※ KCP 테스트 사이트코드 <b>T0000</b>, 테스트 사이트키 <b>3grptw1.zW0GSo4PQdaGvsF__</b> 입니다<br>
※ KCP 승인절차가 끝나시면 필히 상위에 발급한 사이트코드와 사이트키를 등록해야만 정상 결제가 이루어 집니다<br>
※ 결제취소시에는 카드결제같은 경우에는 사이트에서 취소처리 하시면 pg사와 연동하여 카드사까지 한번에 취소처리가 됩니다<br>
※ 실시간계좌이체같은 경우에는 사이트에서 취소처리후 pg사 관리자모드에서 또한번 취소처리를 해야 합니다<br>
※ 결제취소시 사이트에서 먼저 하시고 pg사에서 해야 합니다 반대로 할 경우 오류가 발생합니다<br/>
※ 가상계좌를 이용하려면 가맹점관리자 > 상점정보관리 > 정보변경 > 공통URL정보에 http://<?=$_SERVER[HTTP_HOST]?>/pages/shop.order.result_kcp_return.php 를 입력해야 합니다.
										</td>
									</tr>

									<tr class="PG_L"><!-- 토스페이먼츠 -->
										<td class="article">참고사항</td>
										<td class="conts">
※ 토스페이먼츠 테스트 사이트아이디 <b>lgdacomxpay</b>, 테스트 사이트키 <b>95160cce09854ef44d2edb2bfb05f9f3</b> 입니다<br>
※ 결제취소시에는 카드결제같은 경우에는 사이트에서 취소처리 하시면 pg사와 연동하여 카드사까지 한번에 취소처리가 됩니다<br>
※ 토스페이먼츠 에서 발급받으신 아이디와 Key 를 입력하시고, ftp에 접속하여 <br>
PC : /pg/pc/lgpay/lgdacom/conf/mall.conf<br>
모바일 : /pg/m/lgpay/lgdacom/conf/mall.conf<br>
에도 입력하시기 바랍니다.<br>
<span style="color:blue;">※ IOS 11.3 이상에서 모바일 앱카드의 정상 결제를 위해서는 반드시 <strong>보안서버(SSL)</strong>이 설치되어야 합니다. </span><br>
										</td>
									</tr>

									<tr class="PG_M"><!-- 인포뱅크 -->
										<td class="article">참고사항</td>
										<td class="conts">
※ 인포뱅크 테스트 아이디는 <b>mnbank001m</b> 입니다 (테스트 결제시에는 카드만 가능합니다)<br />
※ 인포뱅크 테스트 상점서명키 <b>zutht7y2mL0DQWk7mkY2Jt+2B7hxqRBtnQ0tK0nl3ZhfztnX5sXSyApEatooQODfz5wNa7DTxzogjWqbxLfa6Q==</b> 입니다.<br />
※ 인포뱅크 테스트 비밀번호 <b>infobank1!</b> 입니다.<br />
※ 인포뱅크 승인절차가 끝나시면 필히 상위에 고객님의 아이디와 상점서명키를 등록해야만 정상 결제가 이루어 집니다<br />
※ 인포뱅크 비밀번호는 취소시에 암호체크 부분입니다 (인포뱅크 관리자모드에서 설정하신 비밀번호가 있을때만 등록하십시오)<br />
※ 결제취소시에는 카드결제같은 경우에는 사이트에서 취소처리 하시면 pg사와 연동하여 카드사까지 한번에 취소처리가 됩니다<br />
<b>※ 실시간계좌이체같은 경우에는 사이트에서 취소처리후 pg사 관리자모드에서 또한번 취소처리를 해야 합니다</b><br />
<b>※ 결제취소시 사이트에서 먼저 하시고 pg사에서 해야 합니다 반대로 할 경우 오류가 발생합니다</b><br />
※ 가상계좌 통보를 위해 - 가맹점관리자 > 회원사정보 > 일반정보 > 일반정보 메뉴의 결제 데이터 통보 항목 중 가상계좌(인터넷, 스마트폰 모두) 항목에<br> <strong>http://<?=$_SERVER[HTTP_HOST]?>/pages/shop.order.result_mnbank_return.php</strong>
를 복사해 넣으셔야 합니다.
										</td>
									</tr>
								    <tr class="PG_D"><!-- 페이조아 -->
								        <td class="article">참고사항</td>
								        <td class="conts">

								            ※ 페이조아 테스트 아이디는 페이조아 측에 직접 요청하여 발급 받으셔야 합니다.<br>
								            ※ 가상계좌와 계좌이체는 에스크로 결제로 기본 적용됩니다.<br>
								            ※ 신용카드와, 계좌이체 취소연동을 위해 페이조아 관리자페이지(https://agent.daoupay.com)에서 암호화 key를 설정 해야 합니다.<br>
								            ※ 가상계좌는 PG사와 취소연동이 되지 않으며, 주문취소 후 고객에게 직접 환불 해야 합니다.<br>
								            ※ 방화벽설정에서 IP 27.102.213.207, 27.102.213.205 에 대한 64001, 46001 포트를 열어주셔야 합니다. (서버업체에 문의)<br>
								            ※ 페이조아 정상적인 사용을 위해서 아래 정보를 페이조아 기술팀(support@kiwoompay.co.kr)으로 보내주시기 바랍니다.<br>
								            1. DB처리페이지 URL : http://<?=$_SERVER[HTTP_HOST]?>/pages/shop.order.result_daupay.pro.php<br>
								            2. 가상계좌발행 DB처리페이지 URL : http://<?=$_SERVER[HTTP_HOST]?>/pages/shop.order.result_daupay.pro.php<br>
								            3. 상점 IP : <?=$_SERVER[SERVER_ADDR]?><br>
								            4. 암호화키 : <?=($row_setup[P_PG_ENC_KEY] ? $row_setup[P_PG_ENC_KEY] : '<font color="red">"PG사 암호화 KEY"를 설정해 주세요.</font>')?><br>
											<?=_DescStr("암호화키는 반드시 영숫자 8자리 이상으로 변경 후에 메일을 발송해주시기 바랍니다.")?>
											<?=_DescStr("페이조아에서 제공해주는 CPID중 수기결제용 CPID는 사용하지 않습니다.")?>

								        </td>
								    </tr>
								<tr class="PG_B"><!-- 빌게이트 -->
									<td class="article">참고사항</td>
									<td class="conts">
									<?
										// 빌게이트 JDK 설치 확인
										@include $_SERVER[DOCUMENT_ROOT]."/../pg/pc/billgate/config.php";
										$cmd = sprintf("%s \"%s\" \"%s\" \"%s\"", $COM_CHECK_SUM, "DIFF", $CHECK_SUM, $temp); $checkSum = @exec($cmd);
									?>
									※ <?=($checkSum)?"JDK (Java Development Kit) 가 정상적으로 설치되어 있습니다.":"<span style='color:red;'>JDK (Java Development Kit) 1.5 버전 이상이 반드시 설치되어 있어야 합니다 (서버 관리자에게 문의하세요).</span>"?><br/>
									<? if(!$checkSum) { ?>
									※ JDK 설치 후 아래 파일을 수정하여 경로를 반드시 지정해야 합니다.
									<div style="margin: 5px; border: 1px solid #ccc; padding: 7px; box-sizing: border-box; width: 80%;">
									<p>
										<b>/pg/pc/billgate/config.php 11째줄</b><br/>
										$JAVA=$JAVA_HOME."/bin/java";<br/>
									</p>
									<p>
									* 경로를 모르실 경우 서버 관리자에게 문의하시기 바랍니다.
									</p>
									</div>
									<? } ?>
									※ 빌게이트 테스트 아이디는 <b>glx_api</b> 입니다 (테스트 결제시에는 카드만 가능합니다)<br>
									※ 빌게이트에서 승인절차가 끝나시면 필히 상단 PG사 코드 항목에 고객님의 빌게이트 아이디를 등록하고 아래 파일을 수정해야 정상 결제가 이루어 집니다
									<div style="margin: 5px; border: 1px solid #ccc; padding: 7px; box-sizing: border-box; width: 80%;">
									<p>
										<b>/pg/pc/billgate/config/config.ini 77째줄</b><br/>
										[변경전]<br/>
										log_file = <br/>
										[변경후]<br/>
										log_file = <?=dirname($_SERVER[DOCUMENT_ROOT])?>/pg/pc/billgate/log<br/>
										<b>/pg/pc/billgate/config/config.ini 84~85째줄</b><br/>
										[변경전]<br/>
										key = QkZJRlBDRTI4T0c1OUtBMw==<br/>
										iv = PRJ59Q2GHPT844TQ<br/>
										[변경후]<br/>
										key = <span style="font-style: italic; color: #999;">빌게이트에서 발급받은 암호화 키</span><br/>
										iv = <span style="font-style: italic; color: #999;">빌게이트에서 발급받은 암호화 Initialize Vector</span>
									</p>
									</div>
									※ 테스트모드에서 실결제모드로 변경할 경우 아래 절차를 따라 반드시 파일을 수정해야 합니다.
									<div style="margin: 5px; border: 1px solid #ccc; padding: 7px; box-sizing: border-box; width: 80%;">
									<p>
										<b>/pg/pc/billgate/config/config.ini 68째줄</b><br/>
										[변경전]<br/>
										mode = 0<br/>
										[변경후]<br/>
										mode = 1
									</p>
									</div>
									※ 결제취소시에는 카드결제같은 경우에는 사이트에서 취소처리 하시면 pg사와 연동하여 카드사까지 한번에 취소처리가 됩니다<br>
									※ 가상계좌 결제 사용을 위해서는 반드시 아래 정보를 <b>tech@billgate.net</b>으로 보내야 합니다.
									<div style="margin: 5px; border: 1px solid #ccc; padding: 7px; box-sizing: border-box; width: 80%;">
									<p><b>메일제목: </b> 가상계좌 DB처리페이지 추가 요청</p>
									<p><b>내용: </b><br/>
									* 가맹점 아이디 (빌게이트에서 발급된 아이디) : <?=$row_setup[P_ID]?><br/>
									* DB처리할 페이지의 경로 (http:// 로 시작하는 url) : http://<?=$_SERVER[HTTP_HOST]?>/pages/shop.order.result_billgate_vacctinput.php<br/>
									* DB처리할 웹 서버의 IP 주소 : <?=$_SERVER[SERVER_ADDR]?><br/>
									* DB처리할 웹 서버의 포트번호 (default : 80) : <?=$_SERVER[SERVER_PORT]?></p>
									</div>
									※ 테스트 아이디(glx_api) 로 결제시 8009 에러가 발생할 경우 빌게이트 기술담당자에게 문의하시기 바랍니다.
									</td>
								</tr>

                                <tr class="PG_L PG_K PG_I PG_A PG_M PG_B PG_D">
                                    <td class="article">참고사항<br>(가상계좌 부분취소)</td>
                                    <td class="conts">

                                        ※ 가상계좌의 부분취소는 PG사와 취소연동이 되지 않으며, 주문취소 후 고객에게 직접 환불 해야 합니다.<br>
                                        <div style="margin: 5px; border: 1px solid #ccc; padding: 7px; box-sizing: border-box; width: 80%;">
                                            <p><b>* 직접 환불 방법</b></p>
                                            <p>
                                                1) 주문관리 메뉴에서 부분취소할 상품이 포함된 주문을 검색합니다. <br/>
                                                2) 검색된 주문의 "상세보기" 버튼을 눌러 주문 상세보기 페이지에 접속합니다.<br/>
                                                3) 부분취소할 상품의 "부분취소"버튼을 눌러 부분취소를(직접환불) 진행합니다. <br/>
                                                4) 부분취소요청관리 메뉴에서 부분취소 요청 내역을 확인 합니다. <br/>
                                                3) 취소된 금액을 고객님의 환불계좌에 직접 이체 합니다. <br/>
                                                4) 부분취소요청관리 메뉴에서 "취소처리"버튼을 눌러 해당상품을 취소합니다.
                                            </p>
                                        </div>

                                    </td>
                                </tr>

								</tbody>
							</table>

					</div>
					<!-- // 검색영역 -->

					<!-- 검색영역 -->
					<div class="form_box_area">
						<table class="form_TB" summary="검색항목">
								<colgroup>
									<col width="200px"/><!-- 마지막값은수정안함 --><col width="*"/>
								</colgroup>
								<tbody>
									<?php //<!-- LMH004 --> ?>
									<tr class="PG_L PG_K PG_I PG_A PG_M PG_B">
										<td class="article">무통장 자동 취소<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<?=_InputRadio("auto_cancel", array('Y', 'N'), $row_setup['auto_cancel'] ? $row_setup['auto_cancel']:'N', '', array('적용','미적용'), "")?>
											<?=_DescStr("자동취소 적용시 무통장 결제 후 입금기한이 지난 주문을 자동 취소 합니다.")?>
										</td>
									</tr>
									<tr class="PG_L PG_K PG_I PG_A PG_M PG_B"><!-- LMH004 -->
										<td class="article">무통장 입금 기한<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<input type="text" name="_P_B_DATE" value="<?=$row_setup[P_B_DATE]?>" size="4" class="input_text"/>일
											<?=_DescStr("무통장 결제시 주문완료 후 입금까지 기한을 설정합니다. 기한내에 입금하지 않으면 해당 주문은 자동으로 취소됩니다.")?>
										</td>
									</tr>
									<?php //<!-- LMH004 --> ?>
									<tr class="PG_L PG_K PG_I PG_A PG_M PG_B PG_D">
										<td class="article">무통장 입금 계좌<span class="ic_ess" title="필수"></span></td>
										<td class="conts">
											<div class="line">
											<input type="text" class="input_text" style="width:100px;border:0px;text-align:center" value='은행명' readonly />
											<input type="text" class="input_text" style="width:200px;border:0px;text-align:center" value='계좌번호'  readonly/>
											<input type="text" class="input_text" style="width:100px;border:0px;text-align:center" value='예금주' readonly />
											</div>
										<?
										$bank_r = _MQ_assoc("select * from odtBank order by serialnum asc");
										foreach($bank_r as $bank_k => $bank_v) {
										?>
											<div class="line">
											<input type="text" name="bankname[<?=$bank_v[serialnum]?>]" class="input_text" style="width:100px" value='<?=$bank_v[bankname]?>' />
											<input type="text" name="banknum[<?=$bank_v[serialnum]?>]" class="input_text" style="width:200px" value='<?=$bank_v[banknum]?>' />
											<input type="text" name="name[<?=$bank_v[serialnum]?>]" class="input_text" style="width:100px" value='<?=$bank_v[name]?>' />
											</div>
										<?
										}
										?>
											<div class="line">
											<input type="text" name="bankname[add]" class="input_text" style="width:100px" value='<?=$bankInfo[bankname]?>' />
											<input type="text" name="banknum[add]" class="input_text" style="width:200px" value='<?=$bankInfo[banknum]?>' />
											<input type="text" name="name[add]" class="input_text" style="width:100px" value='<?=$bankInfo[name]?>' />
											<?=_DescStr("입금 계좌를 입력 후 확인을 누르시면 슬롯이 하나씩 추가 됩니다.")?>
											<?=_DescStr("계좌 삭제를 원할 경우 해당 계좌항목을 모두 지우신 후 확인을 누르시면 됩니다.")?>
											</div>
										</td>
									</tr>
								</tbody>
						</table>
					</div>


<?=_submitBTNsub()?>

</form>

<script>
	var on_off = function() {
		$(".form_TB tbody tr td").hide();
		$(".PG_"+$("input[name=P_KBN]:checked").val()+" td").show();

		$(".PG_VIEW").hide();
		$(".PG_"+$("input[name=P_KBN]:checked").val()+"_SHOW").show();
	}
	$(document).ready(on_off);
	$("input[name=P_KBN]").click(on_off);
</script>
<?PHP
	include_once("inc.footer.php");
?>