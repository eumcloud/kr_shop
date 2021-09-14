<?php
/*--------- 티켓몰 4.0 솔루션에서  실제 사용될 함수 -----------------------------------*/

	// - 변수 넘김시 사용되는 변수 encode / decode ---
	function enc( $mode ,  $str ) {
		$a = array( "+"=>"§" , "?"=>"※" , "#"=>"☆" , "&"=>"★" , "/"=>"○" );
		if($mode=="e") { // encoding
			$str=base64_encode($str);
			foreach( $a as $k=>$v ) { $str=str_replace( $k , $v , $str ); }
		}
		if($mode=="d") { // decoding
			foreach( $a as $k=>$v ) { $str=str_replace( $v , $k , $str ); }
			$str=base64_decode($str);
		}
		return $str;
	}
	//예) 인코딩 => enc( 'e' ,  '테스트입니다.');
	//예) 디코딩 => enc( 'd' ,  $인코딩);
	// - 변수 넘김시 사용되는 변수 encode / decode ---


	// zip파일만 등록가능함
	//$fileLOC-- 파일 등록 디렉토리
	//$fileVAR -- 파일 변수
	//$fileOLD -- OLD 파일명
	function _FileForm( $fileLOC , $fileVAR ,$fileOLD) {
		$_img_reg = "";
		if($fileOLD) {
			$fileOLD_src = $fileLOC . "/" . $fileOLD ;
			$_img_reg .= "<A HREF='{$fileOLD_src}' target=_blank>{$fileOLD}</A><input type=hidden name='{$fileVAR}_OLD' value='{$fileOLD}'>";
			$_img_reg .= "<input type=checkbox name='{$fileVAR}_DEL' value='Y'>파일삭제<br>";
		}
		$_img_reg .= "<input type=file name='{$fileVAR}' size=20 class=input_text>";
		return $_img_reg ;
	}



	//$fileLOC-- 파일 등록 디렉토리
	//$fileVAR -- 파일 변수
	function _FilePro( $fileLOC , $fileVAR, $fileEtx='zip') {
		 $fileOLD = $fileVAR . "_OLD" ; // OLD 파일명
		 $fileDEL = $fileVAR . "_DEL" ; // 파일 삭제 여부
		global $_FILES , $$fileOLD ,  $$fileDEL ;
		$fileEtx = $fileEtx ? $fileEtx : 'zip';

		if($_FILES[$fileVAR][error] > 0 && $_FILES[$fileVAR][tmp_name] ){
			switch($_FILES[$fileVAR][error]){
				case "1":error_msg("업로드한 파일 크기가 설정용량 이상입니다."); break;//업로드한 파일 크기가 PHP 'upload_max_filesize' 설정값보다 클 경우
				case "2":error_msg("업로드한 파일 크기가 설정용량 이상입니다."); break;// UPLOAD_ERR_FORM_SIZE, 업로드한 파일 크기가 HTML 폼에 명시한 MAX_FILE_SIZE 값보다 클 경우
				case "3":error_msg("파일 전송에 오류가 있습니다."); break;//파일중 일부만 전송된 경우
				//case "4":error_msg("파일이 전송되지 않습니다."); break;//파일도 전송되지 않았을 경우
			}
		}

		if($_FILES[$fileVAR][size]> 0){
			$ex_image_type = explode(".",$_FILES[$fileVAR][name]);
			if( !preg_match("/".$fileEtx."/i" , $ex_image_type[(sizeof($ex_image_type)-1)])) {
				error_msg("등록가능한 파일이 아닙니다.");
			}
			if( $$fileOLD ) {
				@unlink( $fileLOC . "/" . $$fileOLD );
			}
			$file_name = sprintf("%u" , crc32($_FILES[$fileVAR][name] . time() . rand())) . strtolower(substr($_FILES[$fileVAR][name],-4));
			@copy($_FILES[$fileVAR][tmp_name] , $fileLOC . "/" . $file_name);
		}
		elseif( $$fileDEL == 'Y' ) {
			if( $$fileOLD ) {
				@unlink( $fileLOC . "/" . $$fileOLD );
			}
			$file_name = "";
		}
		else{
			$file_name = $$fileOLD ;
		}
		return $file_name ;
	}



	//$fileLOC-- 파일 등록 디렉토리
	//$fileNAME -- 파일명
	function _FileDel( $fileLOC , $fileNAME) {
		$fileFILE = $fileLOC . "/" . $fileNAME ;
		if( @file_exists($fileFILE) ) {
			@unlink( $fileLOC . "/" . $fileNAME );
		}
	}





	//$photoLOC-- 이미지파일 등록 디렉토리
	//$photoVAR -- 이미지 변수
	//$photoOLD -- OLD 이미지 명
	function _PhotoForm( $photoLOC , $photoVAR ,$photoOLD) {
		$_img_reg = "";
		if($photoOLD) {
			$photoOLD_src = $photoLOC . "/" . $photoOLD ;
			$_size = @getimagesize( $photoOLD_src);
			if( $_size[0] > 300 ) { $_size[0]= 300; }
			$_img_reg .= "<img src='{$photoOLD_src}' id='img_{$photoVAR}' style='max-width:".$_size[0]."px;'><br>";
			$_img_reg .= "<input type=hidden name='{$photoVAR}_OLD' value='{$photoOLD}'>";
			$_img_reg .= "<span class='multi'><label><input type=checkbox name='{$photoVAR}_DEL' value='Y'>이미지 삭제</label></span><br>";
		} else {
			$_img_reg .= "<div style=''><img src='{$photoOLD_src}' id='img_{$photoVAR}' style='max-width:300px;margin-bottom:5px;display:none;'></div>";
		}
		$_img_reg .= "<input type=file name='{$photoVAR}' size=20 class=input_text>";
		return $_img_reg ;
	}


	//외부링크 기능이 포함된 사진 폼 LDD015
	//$photoLOC-- 이미지파일 등록 디렉토리
	//$photoVAR -- 이미지 변수
	//$photoOLD -- OLD 이미지 명
	function _PhotoHybridForm($photoLOC, $photoVAR, $photoOLD) {

		$_img_reg = "";
		$http_check = strpos($photoOLD, '//');
		if($photoOLD) {

			if($http_check !== false) $photoOLD_src = $photoOLD;
			else $photoOLD_src = $photoLOC . "/" . $photoOLD;
			$_size = @getimagesize( $photoOLD_src);
			if( $_size[0] > 300 ) { $_size[0]= 300; }
			$_img_reg .= "<img src='{$photoOLD_src}' id='img_{$photoVAR}' style='max-width:".$_size[0]."px;'><br>";
			$_img_reg .= "<input type=hidden name='{$photoVAR}_OLD' value='{$photoOLD}'>";
			$_img_reg .= "<span class='multi n".md5($photoVAR)."_del'><label><input type=checkbox name='{$photoVAR}_DEL' value='Y'>이미지 삭제</label></span><br>";
		}
		else {

			$_img_reg .= "<div style=''><img src='{$photoOLD_src}' id='img_{$photoVAR}' style='max-width:300px;margin-bottom:5px;display:none;'></div>";
		}

		if($http_check !== false) {
			$_img_reg .= "
			<script>
			$(function() {

				$('.n".md5($photoVAR)."_checkbox').attr('checked', true);
			})
			</script>
			";
		}

		if($photoOLD && $http_check !== false) {
			$_img_reg .= "
			<script>
			$(function() {

				$('.n".md5($photoVAR)."_del').hide();
			})
			</script>
			";
		}
		else if($photoOLD && $http_check === false) {

			$_img_reg .= "
			<script>
			$(function() {

				$('.n".md5($photoVAR)."_del').show();
			})
			</script>
			";
		}

		//
		$_img_reg .= "
		<div><input type='file' name='{$photoVAR}' size='20' class='input_text n".md5($photoVAR)."_file'></div>
		<div style='margin-top:5px;'><label><input type='checkbox' class='n".md5($photoVAR)."_checkbox'> 외부이미지 사용</label></div>
		<script>
		$(function(){

			file_input_change_".md5($photoVAR)."($('.n".md5($photoVAR)."_checkbox'));
			$('.n".md5($photoVAR)."_checkbox').on('click', function() {

				file_input_change_".md5($photoVAR)."($(this));
			});
			function file_input_change_".md5($photoVAR)."(Target) {

				var checked = Target.is(':checked');
				if(checked === true) $('.n".md5($photoVAR)."_file').attr('type', 'input').attr('placeholder', 'http://를 포함하여 외부 이미지 링크를 기입해주세요.').attr('size', '50').val('{$photoOLD}');
				else $('.n".md5($photoVAR)."_file').attr('type', 'file').removeAttr('placeholder').removeAttr('size').val('');
			}
		});
		</script>
		";
		return $_img_reg ;
	}

	//$photoLOC-- 이미지파일 등록 디렉토리
	//$photoVAR -- 이미지 변수
	function _PhotoPro( $photoLOC , $photoVAR) {
			$photoOLD = $photoVAR . "_OLD" ; // OLD 이미지 명
			$photoDEL = $photoVAR . "_DEL" ; // 이미지 삭제 여부
	  global $_FILES , $$photoOLD ,  $$photoDEL ;

			if($_FILES[$photoVAR][error] > 0 && $_FILES[$photoVAR][tmp_name] ){
				switch($_FILES[$photoVAR][error]){
					case "1":error_msg("업로드한 파일 크기가 2Mb 이상입니다."); break;//업로드한 파일 크기가 PHP 'upload_max_filesize' 설정값보다 클 경우
					case "2":error_msg("업로드한 파일 크기가 2Mb 이상입니다."); break;// UPLOAD_ERR_FORM_SIZE, 업로드한 파일 크기가 HTML 폼에 명시한 MAX_FILE_SIZE 값보다 클 경우
					case "3":error_msg("파일 전송에 오류가 있습니다."); break;//파일중 일부만 전송된 경우
					case "4":error_msg("파일이 전송되지 않습니다."); break;//파일도 전송되지 않았을 경우
				}
			}

			// LCY : 2021-08-11 : 1차 이미지 파일 보안 처리 { 
			$s = file_get_contents($_FILES[$photoVAR][tmp_name]);
			if( preg_match("/(\<\?php)/", $s) > 0){ error_msg("등록가능한 이미지가 아닙니다.");  }
			// LCY : 2021-08-11 : 1차 이미지 파일 보안 처리 }


			if($_FILES[$photoVAR][size]> 0){
				$ex_image_name = explode(".",$_FILES[$photoVAR][name]);
				$app_ext = strtolower($ex_image_name[(sizeof($ex_image_name)-1)]); // 확장자
				if( !preg_match("/gif|jpg|jpeg|bmp|png/i" , $app_ext) ) {
					error_msg("등록가능한 이미지가 아닙니다.");
				}
				if( $$photoOLD ) {
									@unlink( $photoLOC . "/" . $$photoOLD );
							}
				$img_name = sprintf("%u" , crc32($_FILES[$photoVAR][name] . time() . rand())) . "." . $app_ext ;
				@copy($_FILES[$photoVAR][tmp_name] , $photoLOC . "/" . $img_name);
			}
					elseif( $$photoDEL == 'Y' ) {
				if( $$photoOLD ) {
									@unlink( $photoLOC . "/" . $$photoOLD );
				}
				$img_name = "";
			} else{
				$img_name = $$photoOLD ;
			}

			return $img_name ;
		}



	//$photoLOC-- 이미지파일 등록 디렉토리
	//$photoNAME -- 이미지명
	function _PhotoDel( $photoLOC , $photoNAME) {

			$photoFILE = $photoLOC . "/" . $photoNAME ;
			if( @file_exists($photoFILE) ) {
				@unlink( $photoLOC . "/" . $photoNAME );
			}

	}


	## 배열정보 보기 ##
	function ViewArr($arr) {
		echo "<xmp>". print_r($arr , true) ."</xmp>";
	}
	function ViewPost() {
		global $_POST;
		echo "<xmp>". print_r($_POST , true) ."</xmp>";
	}
	function ViewReq() {
		global $_REQUEST;
		echo "<xmp>". print_r($_REQUEST , true) ."</xmp>";
	}

	// - UTF-8 한글 자르기 최종 함수 ::: 2013-05-10 정준철---
	 function cutstr_old($msg,$cut_size,$tail="...") {
		$han = $eng = $tmp_i =0; // 한글 , 영숫어 , 임시 i 갯수
		for($i=0;$i<$cut_size;$i++) {
		 if(@ord($msg[$tmp_i])>127) {
			$han++;
			$tmp_i += 3;
		 }
		 else {
			$eng++;
			$tmp_i ++;
		 }
		}
		$cut_size = $han * 3 + $eng ;
		$snowtmp = "";//return string
		for($i=0;$i<$cut_size;$i++) {
		 if(ord($msg[$i]) <= 127){
			$snowtmp.=$msg[$i];
		 }
		 else {
			$snowtmp .= $msg[$i].$msg[($i+1)].$msg[($i+2)];
			$i+=2;
		 }
		}
		return $snowtmp . ( $msg != $snowtmp ? $tail : "");
	 }
	 function cutstr($str, $chars, $tail = '...') {
	   if (utf8_length($str) <= $chars)//전체 길이를 불러올 수 있으면 tail을 제거한다.
		$tail = '';
	   else
		$chars -= utf8_length($tail);//글자가 잘리게 생겼다면 tail 문자열의 길이만큼 본문을 빼준다.
	   $len = strlen($str);
	   for ($i = $adapted = 0; $i < $len; $adapted = $i) {
		$high = ord($str{$i});
		if ($high < 0x80)
		 $i += 1;
		else if ($high < 0xE0)
		 $i += 2;
		else if ($high < 0xF0)
		 $i += 3;
		else
		 $i += 4;
		if (--$chars < 0)
		 break;
	   }
	   return trim(substr($str, 0, $adapted)) . $tail;
	 }
	 // - UTF-8 한글 자르기 최종 함수 ::: 2013-01-08 정준철---

	function msg_Error($msg) {
		echo "
		<table cellspacing=0 cellpadding=10 border=0 bgcolor='#fff7c5' width=580>
			<tr><td><font size=2 color='darkred'> $msg </a></td></tr>
			<tr><td><font size=2 color='darkred'> ".mysql_error()."</a></td></tr>
			<tr><td><font size=2 color='darkred'> ".mysql_errno()."</a></td></tr>
			<tr><td><input type=button value='BACK' onClick='history.back()' style='font-size:10pt; color:#112244; border-width:1; border-color:#eeeeee;'></td></tr>
		</table>
		";
		exit();
	}

	// 현재 로그인한 아이디를 리턴하는 함수
	function get_userid() {
		global $row_member;

		return $row_member[id];

	}

	// 현재 로그인 상태인지 체크
	function is_login() {
		global $row_member;

		if($row_member[id])
			return true;
		else
			return false;

	}

	// 최고 관리자인지 확인 (사용자 모드 상에서...)
	function is_admin() {
		global $row_member,$_COOKIE;

		if($row_member[Mlevel] == 9 || $_COOKIE["auth_adminid"] != "")  return true;
		else return false;

	}

	// 쿠키 체크 - 쇼핑몰의 비회원구매을 위한 조치
	function cookie_chk() {
		global $_COOKIE;
		if( !$_COOKIE["AuthShopCOOKIEID"] ) {
			error_loc_msg("/" , "잘못된 접근입니다.");
		}
	}


	####null , 널 , 빈값체크 함수
	function nullchk($val , $str , $loc=null , $popup=null) {
		if ( preg_replace("/[[:space:]]/i","",$val) == "" ) {
			if( $popup == "Y" ) { error_msgPopup_s( $str ); }
			elseif( $popup == "ALT" ) { error_alt( $str ); }
			elseif( $loc != "" ) { error_loc_msg($loc , $str ); }
			else { error_msg( $str ); }
		}
		else {
			return $val ;
		}
	}

	// 개인 회원 체크
	// frame ::: 프레임인지 확인
	function member_chk($frame=null) {
		global $_COOKIE;
		if( !is_login() ) {
			if($frame == "Y"){
				error_loc_msg("/?pn=member.login.form&path=" . enc("e",$_SERVER['QUERY_STRING']),"로그인 후 이용하실 수 있습니다.","top");
			}
			else {
				error_loc_msg("/?pn=member.login.form&path=" . enc("e",$_SERVER['QUERY_STRING']),"로그인 후 이용하실 수 있습니다.");
			}
		}
	}

	// - 구글키 추출 ---
	function get_google_key(){
		global $row_setup ;

		$arr_key =array();
		$ex = explode("§" , $row_setup[s_google_key]);
		foreach($ex as $k=>$v) {
			$arr_key[($k + 1)] = $v;
		}
		//구글 api 키 발급 : https://code.google.com/apis/console/?pli=1 > 동의 > services > place 키 API 를  on으로 체크
		$arr_api_key = $arr_key;
		return $arr_api_key[rand(1,sizeof($ex))];

	}

	// goo.gl 을 이용한 shorten url 적용
	function get_shortURL_2($longURL){

		return $longURL;
		$api_key = get_google_key(); // 구글키

		$curlopt_url = "https://www.googleapis.com/urlshortener/v1/url?key=".$api_key;

		$ch = curl_init();
		//$timeout = 10;

		curl_setopt($ch, CURLOPT_URL, $curlopt_url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$jsonArray = array('longUrl' => $longURL);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonArray));
		$shortURL = curl_exec($ch);
		curl_close($ch);
		$result_array = json_decode($shortURL, true);
		if($result_array['shortUrl']) return $result_array['shortUrl'];// durl.me
		else if($result_array['id']) return $result_array['id'];    // goo.gl
		else return false;

		$shortURL = curl_exec($ch);
		curl_close($ch);

		return $shortURL;

	}



	// 구글 API를 이용한 지도 좌표구하기
	function get_mapcoordinates($addr){

		$api_key = get_google_key(); // 구글키

		$addr = urlencode(trim($addr));
		$url = "https://maps.googleapis.com/maps/api/place/textsearch/xml?query=" . $addr . "&sensor=true&key=" . $api_key ;
		//$url = "https://maps.googleapis.com/maps/api/geocode/xml?address=".$addr."&sensor=false"; // 구글맵 API 주소 변경 2014-06-25
		$return_string = "";

		$tmp = simplexml_load_string( CurlExec( $url ));
		if( $tmp->status == "OK") {
			$return_string = $tmp->result->geometry->location->lat . "," . $tmp->result->geometry->location->lng;
		}
		return $return_string;
	}



	// 샵용 메일 컨텐츠를 추출한다.
	// 인자 : 타이틀
	//				탑이미지
	//				컨텐츠
	// 리턴 : 메일내용
	// LDD017
	function get_mail_content($mailing_title, $mailing_title_content, $mailling_content) {
		global $_SERVER, $row_setup, $row_company;

		$mailing_url = 'http://'.$_SERVER['SERVER_NAME'];
		$mail_body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="kr" lang="kr" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>'.strip_tags($mailing_title).'</title>
		</head>
		<body>
			<div class="mailing_wrap" style="background:#cbd3d5; padding:30px">
				<div style="width:900px; margin:0 auto; background:#fff;">

					<!-- 메일링 탑정보 -->
					<div style="overflow:hidden; background:#950000 url(\''.$mailing_url.'/include/images/mailing/top_bg.png\') left -20px top 10px no-repeat; text-align:center; padding:30px">
						<dl>
							<!-- 상점 타이틀(제목) 혹은 상점명..  -->
							<dt style="text-align:center; padding:0; margin:0; font-family:\'나눔고딕\',\'돋움\'; font-size:38px; font-weight:600; color:#fff">
								'.$row_setup[site_name].'
							</dt>
							<!-- 사이트주소 -->
							<dd style="text-align:center; padding:0; margin:0; font-family:calibri; font-size:19px; color:rgba(255,255,255,0.5); margin-top:10px">
								<a href="'.$mailing_url.'" target="_blank" style="font-size:19px; color:rgba(255,255,255,0.5);text-decoration:none;">'.$mailing_url.'</a>
							</dd>
						</dl>
					</div>
					<!--// 메일링 탑정보 -->

					<!-- 메일링제목 -->
					<div style="background:transparent url(\''.$mailing_url.'/include/images/mailing/title_bg.jpg\') left top no-repeat; height:110px; text-align:center;">
						<span style="display:inline-block; font-family:\'나눔고딕\',\'돋움\'; font-size:27px; font-weight:600; color:#fff; letter-spacing:-1px; line-height:110px; background:transparent url(\''.$mailing_url.'/include/images/mailing/title_icon.png\') left center no-repeat; padding-left:70px">
							'.$mailing_title.'
						</span>
					</div>
					<!--// 메일링제목 -->

					'.($mailing_title_content != ''?'
					<!-- 메일의 전달사항 -->
					<div style="background:#f1f1f1; color:#666; font-family:\'나눔고딕\',\'돋움\'; font-size:17px; text-align:center; line-height:1.5; padding:30px 20px; letter-spacing:-1px; border-bottom:1px solid #ddd">
						'.$mailing_title_content.'
					</div>
					<!--// 메일의 전달사항 -->
					':'').'

					<!-- 메인컨텐츠 -->
					'.$mailling_content.'
					<!--// 메인컨텐츠 -->

					<!-- 바로가기버튼 -->
					<div style="text-align:center; padding:0 0 40px 0;">
						<a href="'.$mailing_url.'" target="_blank" style="background:#4f5458; text-decoration:none; color:#fff; font-family:\'나눔고딕\',\'돋움\'; font-size:17px; font-weight:600; padding:16px 35px; border-radius:60px">홈페이지바로가기</a>
					</div>
					<!--// 바로가기버튼 -->

					<!-- 카피라잇 -->
					<div style="background: #e0e6e8; padding:30px 0; font-family:\'나눔고딕\',\'돋움\'; font-size:11px; color:#6d717c; text-align:center; line-height:1.5;">
						<strong>본메일은 발신전용입니다. 궁금하신 사항은 홈페이지 <a href="'.$mailing_url.'/?pn=service.main" target="_blank">고객센터</a>를 이용하여 주시기 바랍니다.</strong><br/><br/>
						TEL: '.$row_company['tel'].($row_company['fax']?', FAX: '.$row_company['fax']:'').', E-mail: '.$row_company['email'].' <br/>
						'.$row_company['taxaddress'].' 대표: '.$row_company['ceoname'].' 사업자등록번호: '.$row_company['number1'].'<br/>
						Copyright © '.$row_company['name'].'. All Rights Reserved
					</div>
					<!--// 카피라잇 -->
				</div>
			</div>
		</body>
		</html>';

		return $mail_body;
	}

	function pagelisting($cur_page, $total_page, $n, $url , $depth=null) {

		$start_page = ( ( (int)( ($cur_page - 1 ) / 10 ) ) * 10 ) + 1;
		$end_page = $start_page + 9;

		if($end_page >= $total_page) $end_page = $total_page;
		if(!$end_page) $end_page=1;
		$retValue = "	<span class='lineup'>";
		if($cur_page > 1) {
			$retValue .= "<span class='nextprev'>";
			$retValue .= "<span class='btn click'><span class='no'><span class='icon ic_first'></span></span><a href='" .$url . "1' class='ok' title='처음' ><span class='icon ic_first'></span></a></span>";
			$retValue .= "<span class='btn click'><span class='no'><span class='icon ic_prev'></span></span><a href='" . $url . ($cur_page-1) . "' class='ok' title='이전' ><span class='icon ic_prev'></span></a></span>";
			$retValue .= "</span>";
		} else {
			$retValue .= "<span class='nextprev'>";
			$retValue .= "<span class='btn'><span class='no'><span class='icon ic_first'></span></span><a href='" .$url . "1' class='ok' title='처음' ><span class='icon ic_first'></span></a></span>";
			$retValue .= "<span class='btn'><span class='no'><span class='icon ic_prev'></span></span><a href='" . $url . ($cur_page-1) . "' class='ok' title='이전' ><span class='icon ic_prev'></span></a></span>";
			$retValue .= "</span>";
		}

		$retValue .= "<span class='number'>";
		for($k=$start_page;$k<=$end_page;$k++)
		if($cur_page != $k) $retValue .= "<a href='$url$k'>${k}</a>";
		else $retValue .= "<a href='#none' onclick='return false;' class='hit'>${k}</a>";
		$retValue .= "</span>";

		if($cur_page < $total_page) {
			$retValue .= "<span class='nextprev'>";
			$retValue .= "<span class='btn click'><span class='no'><span class='icon ic_next'></span></span><a href='" . $url . ($cur_page+1) . "' class='ok' title='다음' ><span class='icon ic_next'></span></a></span>";
			$retValue .= "<span class='btn click'><span class='no'><span class='icon ic_last'></span></span><a href='" . $url . $total_page . "' class='ok' title='끝' ><span class='icon ic_last'></span></a></span>";
			$retValue .= "</span>";
		} else {
			$retValue .= "<span class='nextprev'>";
			$retValue .= "<span class='btn'><span class='no'><span class='icon ic_next'></span></span><a href='" . $url . ($cur_page+1) . "' class='ok' title='다음' ><span class='icon ic_next'></span></a></span>";
			$retValue .= "<span class='btn'><span class='no'><span class='icon ic_last'></span></span><a href='" . $url . $total_page . "' class='ok' title='끝' ><span class='icon ic_last'></span></a></span>";
			$retValue .= "</span>";
		}
		$retValue .= "</span>";
		return $retValue;
	}


	function pagelisting_mobile($cur_page, $total_page, $n, $url , $depth=null) {
		$start_page = ( ( (int)( ($cur_page - 1 ) / 5 ) ) * 5 ) + 1;
		$end_page = $start_page + 4;

		if($end_page >= $total_page) $end_page = $total_page;
		if(!$end_page) $end_page=1;

		$retValue = "<span class='inner'>";

		if($cur_page > 1) {
			$retValue .= "<a href='" . $url . ($cur_page-1) . "' class='prevnext' title='이전' ><span class='arrow'></span></a>";
		} else {
			$retValue .= "<a class='prevnext' title='이전' ><span class='arrow'></span></a>";
		}

		for($k=$start_page;$k<=$end_page;$k++)
		if($cur_page != $k) $retValue .= "<a href='$url$k' class='number'>${k}</a>";
		else $retValue .= "<a href='#none' class='number hit'>${k}</a>";

		if($cur_page < $total_page) {
			$retValue .= "<a href='" . $url . ($cur_page+1) ."' class='prevnext' title='다음'><span class='arrow'></span></a>";
		} else {
			$retValue .= "<a class='prevnext' title='다음'><span class='arrow'></span></a>";
		}

		$retValue .= "</span>";

		return $retValue;
	}

	// - 쇼핑몰 쿠폰번호 생성 ---
	function shop_couponnum_create($type=null){
		// --> 상품코드 - 영숫자조합으로 15글자 적용, 예)
		// --> 생성원리 1. 다섯번째 글자는  영문대문자로 한다
		// --> 생성원리 2. 5개씩 3단락
		//	--> 생성예. A1234-B1234-C1234
		// --> chr(65) : A ~ chr(90) : Z
		$_code = "";
		for( $i=0;$i<3; $i++ ){
			if( $i <> 0 ) {
				$_code .= "-";
			}
			for( $j=0; $j<5 ; $j++ ){
				if( $j<>4 ) { // 숫자
					$_code .= rand(0,9);
				}
				else { // 영문
					$_code .= chr(rand(65,90));
				}
			}
		}
		return $_code ;
	}
	// - 상품코드 생성 ---

	// 메일발송함수
	// mailer( 받을메일주소 , 메일제목 , 메일내용 )
	function mailer( $_email , $_title , $_content ) {
		global $row_company, $row_setup;

		// -- 변수 준비
		// 네이버 깨짐현상으로 인해
		if( preg_match("/@naver.com/i" , $_email) ){
			$_header = "From: \"". $row_setup[site_name] ."\" <". $row_company[email] ."> \r\n";
		}
		else {
			$_header = "From: \"" . "=?UTF-8?B?".base64_encode( $row_setup[site_name] )."?="  . "\" <". $row_company[email] ."> \r\n" ;
		}
		//$_header .= "MIME-Version: 1.0\r\n" ;
		$_header .= "Content-Type: text/html; charset=utf-8\r\n";
		$_header .= "Reply-To:" . $row_company[email] ."\r\n";
		//$_header .= "X-Priority: 1 (Higuest)\r\n";
		//$_header .= "X-MSMail-Priority: High\r\n";
		//$_header .= "Importance: High\r\n";

		// -- 메일 발송
		return @mail( $_email , '=?UTF-8?B?'.base64_encode($_title).'?=' , $_content , $_header , "-f" . $row_company[email] );

	}
	## 로그인
	function apply_login($serialnum,$ranDsum,$addSum) {
		global $_COOKIE;
		$string_join = $serialnum.$ranDsum.$addSum;

		if($_COOKIE['auth_memberid']) {
			//samesiteCookie("auth_memberid","",-999,"/");

			return false;
		}
		else {
			samesiteCookie("auth_memberid",$serialnum,0,"/");
			samesiteCookie("auth_memberid",$serialnum,0,"/",".*.*");
			samesiteCookie("auth_memberid_sess",md5($string_join),0,"/");
			samesiteCookie("auth_memberid_sess",md5($string_join),0,"/",".*.*");

			// -- 사용자 로그인 정보 보안 강화 처리 -- 2019-05-20 LCY
			UserLogin($serialnum);
			// -- 사용자 로그인 정보 보안 강화 처리 -- 2019-05-20 LCY

			return true;
		}
	}

	// - 쇼핑몰 주문번호 생성 ---
	function shop_ordernum_create($type=null){
		// --> 주문번호 - 숫자조합으로 15글자 적용, 예)
		// --> 생성원리 1. 5개씩 3단락
		//	--> 생성예. 12345-23456-34567

		$ex = explode(' ', microtime());
		$tmp1 = sprintf("%05d" , rand(0,99999));
		$tmp2 = sprintf("%u" , crc32( microtime(). rand(1,99999) ));
		$tmp2 = str_pad( $tmp2 , 10 , '0', STR_PAD_RIGHT);
		$order_a = sprintf("%05d" , substr($tmp2 , 0 , 5));
		$order_b = substr($tmp2 , -5);
		$_code = $tmp1 ."-" . $order_a ."-" . $order_b;

		// - 과거 같은 주문 번호 여부 확인 ---
		$orderchk = _MQ("select count(*) as cnt from odtOrder where ordernum = '".  strtoupper($_code) ."'");
		if( $orderchk[cnt] > 0 ){
			$_code = shop_ordernum_create();
		}

		return $_code ;
	}
	// - 쇼핑몰 주문번호 생성 ---

	// - 상품코드 생성 ---
	function shop_pcode_create($type=null){
		// --> 주문번호 - 숫자조합으로 15글자 적용, 예)
		// --> 생성원리 1. 5개씩 3단락
		//	--> 생성예. 12345-23456-34567

		$random = rand(10000,99999);
        $sumTme = (time(Y)+time(m)+time(d)+time(H)+time(i)+time(s)+19)*997;
        $sumTempLength = strlen($sumTme);
        $checkSum = substr($sumTme,$sumTempLength-2,2);
        $code = "S".$checkSum.$random;

		// - 과거 같은 주문 번호 여부 확인 ---
		$orderchk = _MQ("select count(*) as cnt from odtProduct where code = '".  strtoupper($code) ."'");
		if( $orderchk[cnt] > 0 ){
			$code = shop_pcode_create();
		}

		return $code ;
	}
	// - 상품코드 생성 ---


	//  - 원데이넷 문자발송 함수 ---
	function onedaynet_sms_send($tran_phone, $tran_callback, $tran_msg) {
		global $row_setup, $_SERVER;

		//sms_send( 아이디 , 비번 , 받을 전번 , 보낸 전번 , 메시지 , 예약시간(형태 : 2015-12-10 13:21:25) , 서버아이피 )
		if( $tran_phone && $tran_callback && $tran_msg ) {
			$SMSDec = enc_array('d', $row_setup['sms_pw']);
			include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoap.php');
			$client = new soapclientW('http://mobitalk.gobeyond.co.kr/nusoap/sms.send.php');
			$result = $client->call('sms_send',array('id' => $row_setup['sms_id'], 'pw'=>$SMSDec['sms_pw'], 'receive_num'=>$tran_phone, 'send_num'=>$tran_callback, 'msg'=>$tran_msg, 'reserve_time'=>'', 'ip'=>'auto'));
			$result_json = json_decode($result, true);
			if(in_array(gettype($result_json), array('integer', 'NULL'))) $result_array = array($result_json); // 이전 string 반환 교정
			else $result_array = $result_json;

			insert_sms_send_log($result_array);
			return $result_array;
		}
	}
	//  - 원데이넷 문자발송 함수 ---


/**
 *
 * # 원데이넷 문자발송 함수 - 일괄발송
 *
 * @param array($send_array)
 * @detail ->
 *          $send_array = array(
 *                          array(
 *                              'receive_num'=>'010-0000-0000'
 *                              , 'send_num'=>'1544-6937'
 *                              , 'msg'=>'SMS 다중발송 테스트 010-0000-0000'
 *                              , 'reserve_time'=>''
 *                              , 'title'=>'테스트입니다'
 *                              , 'image'=>'123124521.jpg'
 *                              , 'image_del'=>'Y'
 *                          )
 *                      );
 * @detail -->
 *  ==> array()
 *      ==> [발송차순][receive_num] : 받을 전번
 *      ==> [발송차순][send_num] : 보낸 전번
 *      ==> [발송차순][msg] : 메시지
 *      ==> [발송차순][reserve_time] : 예약시간(형태 : 2011-04-05 13:21:25)
 *      ==> [발송차순][title] : 제목 (LMS/MMS 전송시 표시됨)
 *      ==> [발송차순][image] : 첨부이미지 (/upfiles/ 에 저장)
 *      ==> [발송차순][image_del] : 첨부이미지 삭제여부(Y 일 경우 전송완료 후 로컬 이미지 삭제)
 *
 * @return array(array('result_code', 'result_msg', 'send_num', 'receive_num'))
 * @detail ->
 *          $return = array(
 *              [0] => array(
 *                      [code] => S00
 *                      [data] => 정상적으로 발송되었습니다.
 *                      [send_num] => 1544-6937
 *                      [receive_num] => 010-0000-0000
 *                  ),
 *              [1] => array(
 *                      [code] => Y
 *                      [data] => 성공!
 *                      [send_num] => 1544-6937
 *                      [receive_num] => 010-0000-0000
 *                  )
 *          );
 * @detail -->
 *  ==> array()
 *      ==> [전송차순][result_code] = 결과코드
 *      ==> [전송차순][result_msg] = 결과 메시지
 *      ==> [전송차순][send_num] = 발신번호
 *      ==> [전송차순][receive_num] = 수신번호
**/
function onedaynet_sms_multisend($arr_send = array()) {
    global $_SERVER, $row_setup;

    // 처리 데이터가 없는 경우
    if(count($arr_send) <= 0) return;

    // 초기값 설정
	$SMSDec = enc_array('d', $row_setup['sms_pw']);
    $tran_id = $row_setup['sms_id']; $tran_pw = $SMSDec['sms_pw']; $arr_send_string = array(); $arr_send_image = array(); $result_array = array();

    // 이미지처리
    foreach($arr_send as $k=>$v) {
        if(trim($v['image']) <> '') array_push($arr_send_image, $arr_send[$k]); // 이미지가 있다면 MMS
        else array_push($arr_send_string, $arr_send[$k]); // 이미지가 없으면 SMS/LMS
    }

    // nusoap include
    include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoap.php');

    // SMS/LMS 발송
    if(count($arr_send_string) > 0) {

        $client = new soapclientW('http://mobitalk.gobeyond.co.kr/nusoap/mms.send_server_multi.php');
        // sms_send( 아이디 , 비번 , 메세지 배열 , 서버아이피)
        $result = $client->call('sms_send', array('id'=>$tran_id, 'pw'=>$tran_pw, 'arr_send'=>$arr_send_string, 'ip'=>'auto'));
        $result_json = json_decode($result, true);
        if(in_array(gettype($result_json), array('integer', 'NULL'))) $result_array = array($result_json); // 이전 string 반환 교정
        else $result_array = $result_json;
    }

    // MMS 발송
    if(count($arr_send_image) > 0) {

        include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoapmime.php');
        foreach($arr_send_image as $k=>$v) {

            $tran_phone = $v['receive_num']; $tran_callback = $v['send_num']; $tran_msg = $v['msg'];
            $tran_reservetime = $v['reserve_time']; $tran_title = $v['title']; $tran_img = $v['image']; $tran_img_del = $v['image_del'];
            $app_dir = $_SERVER['DOCUMENT_ROOT'].'/upfiles';
            $client = new soapclientmime('http://mobitalk.gobeyond.co.kr/nusoap/mms.send_server_one.php?wsdl', true);
            $client->setHTTPEncoding('deflate, gzip');

            if($tran_img){
                $file = '';
                $fp = @fopen( $app_dir.'/'.$tran_img, 'rb');
                if($fp) { while(!feof($fp)){ $file .= fgets($fp); } }
                @fclose($fp);
                $cid = $client->addAttachment($file, $tran_img);
            }

            // mobitalk_mms_send(아이디, 비번, 받을번호, 보낸번호, 메시지, 제목, 이미지, 예약시간, 서버아이피)
            $result = $client->call(
                'mobitalk_mms_send',
                array(
                    'tran_id'=>$tran_id, 'tran_pw'=>$tran_pw, 'tran_phone'=>$tran_phone, 'tran_callback'=>$tran_callback, 'tran_msg'=>$tran_msg,
                    'tran_title'=>$tran_title, 'tran_img'=>$tran_img, 'tran_reservetime'=>$tran_reservetime, 'tran_ip'=>'auto'
                )
            );
            $result_json = json_decode($result, true);
            if(in_array(gettype($result_json), array('integer', 'NULL'))) $result_json = array($result); // 이전 string 반환 교정
            $result_array = array_merge($result_array, $result_json);
        }
    }

    insert_sms_send_log($result_array);
    return $result_array;
}



	function mailCheck($email) {
		if(preg_match("/^[^@]+@[^@]+\.[^@\.]+$/i",$email)) { return true; }
		else { return false; }
	}

	function phone_print($num1,$num2,$num3) {
		return implode("-",array_filter(array($num1,$num2,$num3)));
	}
/*--------- // 티켓몰 4.0 솔루션에서 실제 사용될 함수 -----------------------------------*/

	function conn_hID($hID) {
		$id = @mysql_result(mysql_query("select id from odtMember where hID='".$hID."'"),0);
		if(!$id) $id = "error_".$hID;

		return $id;
	}

	function utf8_length($str) {
		$len = strlen($str);

		for ($i = $length = 0; $i < $len; $length++) {
			$high = ord($str{$i});
			if ($high < 0x80)//0<= code <128 범위의 문자(ASCII 문자)는 인덱스 1칸이동
				$i += 1;
			else if ($high < 0xE0)//128 <= code < 224 범위의 문자(확장 ASCII 문자)는 인덱스 2칸이동
				$i += 2;
			else if ($high < 0xF0)//224 <= code < 240 범위의 문자(유니코드 확장문자)는 인덱스 3칸이동
				$i += 3;
			else//그외 4칸이동 (미래에 나올문자)
				$i += 4;
		}

		return $length;
	}


	// - 메일추출 방지 ---
	function encode_email($email) {
		$len = strlen($email);
		if(!$len) return 0;
		for ($i=0; $i<$len; $i++) $encEmail_Func = $encEmail_Func."&#".ord(substr($email, $i, $i+1)).";";
		return $encEmail_Func;
	}
	// - 메일추출 방지 ---



	// - (이전)문자 자르기 ---
	function cut_str_short($Str, $size, $addStr="...")  {
		if(mb_strlen($Str, "UTF-8") > $size) {
			return mb_substr($Str, 0, $size, "UTF-8").$addStr;
		}
		else {
			return $Str;
		}
	}
	// - 문자 자르기 ---


	// - (이전)문자 자르기 ---
	function cut_str(&$contents,$cut_len=0,$cut_num=1) {

		 /// 문자열 길이
		 $cont_len = strlen($contents);

		 /// setting default values
		 if($cut_len <= 0) $cut_len = $cont_len;
		 else              $cut_len = intval($cut_len);
		 if($cut_num <= 0)    $cut_num = 1;
		 elseif($cut_num > 1) $cut_num = intval($cut_num);

		 /// 문자열을 자르기 위한 시작위치
		 $start_pos = 0;

		 /// 자를 갯수만큼 loop
		 for($cnt=1; $cnt <= $cut_num; $cnt++) {
			  /// 다음번에 자를 문자열이 남아 있을때
			  if($cont_len > ($start_pos + $cut_len)) {
					 $s_flag = false;
					 $tmp_str = substr($contents,$start_pos,$cut_len);
					 $tmp_pos = strrpos($tmp_str,' ');
					 if(!$tmp_pos) $tmp_pos = 0;

					 /// 자른 문자열에서 역으로 첫번째 space문자를 검출후 다시 space문자에서부터
					 /// 자른 문자열 끝까지 2byte문자 시작위치인지 여부를 체크해서
					 /// 2byte문자 시작위치이면 1byte 앞까지 문자를 잘라서 array에 넣음
					 /// $s_flag 는 2byte문자 시작위치인지에 대한 flag
					 for($i=$tmp_pos; $i < $cut_len; $i++) {
						   if(ord($tmp_str[$i]) > 127) {
								 if($s_flag) $s_flag = false;
								 else         $s_flag = true;
						   }
						   else $s_flag = false;
					 }

					 if($s_flag) {
						   $arr_cont[$cnt] = substr($tmp_str,0,$cut_len-1);
						   $start_pos += $cut_len - 1;
					 }
					 else {
						   $arr_cont[$cnt] = $tmp_str;
						   $start_pos += $cut_len;
					 }

					 /// 문자열을 $cut_num 갯수까지 자른후, 나머지를 array의 마지막에 넣음
					 if($cnt == $cut_num) {
							$arr_cont[$cnt+1] = substr($contents,$start_pos);
					 }
			  }
			  /// 다음번에 더이상 자를 문자열이 없으므로 for loop 빠져나감
			  else {
					 $arr_cont[$cnt] = substr($contents,$start_pos);
					 break;
			  }
		 }

		 /// array첫번째에 실제로 문자열을 자른 갯수를 넣는다
		 $arr_cont[0] = $cnt;

		 return $arr_cont;

	} // End of cut_str()
	// - (이전)문자 자르기 ---


	// - 문자 자르기 - 배열형식 ---
	function mb_cut_str(&$contents,$cut_len=0,$cut_num=1) {

		 /// 문자열 길이
		 $contents = iconv("UTF-8","EUC-KR" , $contents);
		 $cont_len = mb_strlen($contents );

		 /// setting default values
		 if($cut_len <= 0) $cut_len = $cont_len;
		 else              $cut_len = intval($cut_len);
		 if($cut_num <= 0)    $cut_num = 1;
		 elseif($cut_num > 1) $cut_num = intval($cut_num);

		 /// 문자열을 자르기 위한 시작위치
		 $start_pos = 0;

		 /// 자를 갯수만큼 loop
		 for($cnt=1; $cnt <= $cut_num; $cnt++) {
			  /// 다음번에 자를 문자열이 남아 있을때

			  if($cont_len > $start_pos ) {
					$s_flag = false;

					$chk_laststr1 = ord(mb_substr($contents,$cut_len-1,1));
					if( $chk_laststr1 > 127 ) {
						$cut_len --;
					}

					$tmp_str = mb_substr($contents,$start_pos,$cut_len);
					$tmp_pos = mb_strrpos($tmp_str,' ');

					if(!$tmp_pos) $tmp_pos = 0;

					$arr_cont[$cnt] = iconv("EUC-KR" , "UTF-8",$tmp_str);
					$start_pos += $cut_len;


					 /// 문자열을 $cut_num 갯수까지 자른후, 나머지를 array의 마지막에 넣음
					 if($cnt == $cut_num) {
							$arr_cont[$cnt+1] = iconv("EUC-KR" , "UTF-8",mb_substr($contents,$start_pos));
					 }
			  }
			  /// 다음번에 더이상 자를 문자열이 없으므로 for loop 빠져나감
			  else {
					 $arr_cont[$cnt] = iconv("EUC-KR" , "UTF-8",mb_substr($contents,$start_pos));
					 break;
			  }
		 }

		 /// array첫번째에 실제로 문자열을 자른 갯수를 넣는다
		 $arr_cont[0] = $cnt;
		 return $arr_cont;

	}
	// - 문자 자르기 - 배열형식 ---


	// 페이스북 로그인 정보를 통해 uid 추출
	function facebook_uid(){
		//$ex = explode("uid=",$_cookie );
		//return str_replace(array('"' , '\\') , '' , $ex[1]);
		global $_SESSION;
		return $_SESSION['fb_user']->id;
	}

	// 페이스북 로그인 정보를 통해 access_token 추출
	function facebook_access_token(){
		global $_SESSION;
		//$_SESSION['fb_token_url'] = $token_url ; // token_url - session save
		$response = CurlExec($_SESSION['fb_token_url']);

		$params = null;
		parse_str($response, $params);
		//_VA($params);
		return $params['access_token'];
	}

	// Post 방식의 curl 발송 위한 함수
	function CurlPostExec( $url , $data , $time = 100) {
		$cu = curl_init();
		curl_setopt($cu, CURLOPT_URL,  $url ); // 데이타를 보낼 URL 설정
		curl_setopt($cu, CURLOPT_POST,0); // 데이타를 get/post 로 보낼지 설정
		curl_setopt($cu, CURLOPT_RETURNTRANSFER,1); // REQUEST 에 대한 결과값을 받을건지 체크 #Resource ID 형태로 넘어옴 :: 내장 함수 curl_errno 로 체크
		$arr_url = parse_url($url);
		if( $arr_url[scheme] == "https") {curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, 0);}
		curl_setopt($cu, CURLOPT_TIMEOUT,$time); // REQUEST 에 대한 결과값을 받는 시간타임 설정
		curl_setopt($cu, CURLOPT_POSTFIELDS, $data );
		$str = curl_exec($cu); // 실행
		curl_close($cu);
		return $str;
	}

	// get 방식의 curl 읽기 위한 함수
	function CurlExec( $url , $time = 100) {
		$cu = curl_init();
		curl_setopt($cu, CURLOPT_URL,  $url ); // 데이타를 보낼 URL 설정
		//curl_setopt($cu, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; InfoPath.1)"); // 해당 데이타를 보낼 http head 정의 : 삭제해도 되긴함
		curl_setopt($cu, CURLOPT_POST,0); // 데이타를 get/post 로 보낼지 설정
		//curl_setopt($cu, CURLOPT_POSTFIELDS,"arg=$arg1"); // 보낼 데이타를 설정 형식은 GET 방식으로 설정 ex) $vars = "arg=$arg1&arg2=$arg2&arg3=$arg3";
		curl_setopt($cu, CURLOPT_RETURNTRANSFER,1); // REQUEST 에 대한 결과값을 받을건지 체크 #Resource ID 형태로 넘어옴 :: 내장 함수 curl_errno 로 체크
		$arr_url = parse_url($url);
		if( $arr_url[scheme] == "https") {curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, 0);}
		curl_setopt($cu, CURLOPT_TIMEOUT, $time); // REQUEST 에 대한 결과값을 받는 시간타임 설정
		$str = curl_exec($cu); // 실행
		curl_close($cu);
		return $str;
	}


	// goo.gl 을 이용한 shorten url 적용
	function get_shortURL($longURL){

		return $longURL; // 2018-03-12 더이상 지원안됨
		$api_key = get_google_key(); // 구글키

		if($api_key){
			$curlopt_url = "https://www.googleapis.com/urlshortener/v1/url?key=".$api_key;
			$ch = curl_init();
			//$timeout = 10;
			curl_setopt($ch, CURLOPT_URL, $curlopt_url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			$jsonArray = array('longUrl' => $longURL);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonArray));
			$shortURL = curl_exec($ch);
			curl_close($ch);
			$result_array = json_decode($shortURL, true);
			if($result_array['shortUrl']) return $result_array['shortUrl'];// durl.me
			else if($result_array['id']) return $result_array['id'];    // goo.gl
			else return false;

			$shortURL = curl_exec($ch);
			curl_close($ch);
		}
		return $shortURL;
	}



// ---------------- 소셜 적용 ---------------- //
	function Insert_facebook($pass_content,$fb_appid,$snsCheck){
		//echo "-->".$pass_content." | ".$fb_appid." | ".$snsCheck."<br>";
		if ($snsCheck!=0){
			$longURL = "http://".$_SERVER[HTTP_HOST]."/?sns_addressNo=".$snsCheck;
			$shortUrl = get_shortURL($longURL);
			$pass_content = $pass_content."  ".$shortUrl;
		}
		$app_facebook_uid = facebook_uid();
		$app_facebook_access_token = ($_SESSION['fb_token_access_token'] ? $_SESSION['fb_token_access_token'] : facebook_access_token());
		//echo "-->".$app_facebook_uid." | ".$app_facebook_access_token."<br>";
		$pass_link = 'http://goo.gl/WSXGI';
		$pass_img = '';
		$app_url = 'https://graph.facebook.com/' . $app_facebook_uid . '/feed';
		$app_data = "access_token=". $app_facebook_access_token ."&message=" . $pass_content . "&link=" . $pass_link . "&picture=" . $pass_img ;
		$output = CurlPostExec( $app_url , $app_data );
		$user = json_decode($output);
		$response_id = $user->id;
		if ($response_id){
			return $response_id;
		}
		else{
			return false;
		}

	}
	function Insert_facebook_re($pass_content,$fb_appid,$snsNum){
		$app_facebook_uid = facebook_uid();
		$app_facebook_access_token = facebook_access_token();
		$pass_reply_id = $snsNum;// 타켓 글의 고유번호
		$pass_link = 'http://goo.gl/WSXGI';
		$pass_img = '';
		$app_url = 'https://graph.facebook.com/' . $pass_reply_id . '/comments';
		$app_data = "access_token=". $app_facebook_access_token ."&message=" . $pass_content . "&link=" . $pass_link . "&picture=" . $pass_img ;
		$output = CurlPostExec( $app_url , $app_data );
		$user = json_decode($output);
		$response_id = $user->id;
		if ($response_id){
			return $response_id;
		}else{
			return false;
		}
	}
	function Delete_facebook($delete_id,$fb_appid){
		$app_facebook_uid = facebook_uid();
		$app_facebook_access_token = facebook_access_token();
		$app_url = 'https://graph.facebook.com/'.$delete_id;
		$app_data = "method=delete&access_token=". $app_facebook_access_token ;
		$output = CurlPostExec( $app_url , $app_data );
		$user = json_decode($output);
		if ($user ==1){
			return $user;
		}
		else{
			return false;
		}
	}
	function get_twtinfo(){
		require_once($_SERVER[DOCUMENT_ROOT].'/socialauth/twtapi/twitteroauth/twitteroauth.php');
		require_once($_SERVER[DOCUMENT_ROOT].'/socialauth/twtapi/config.php');
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);
		$content = $connection->get('https://api.twitter.com/1.1/users/lookup.json?screen_name='. $_SESSION['access_token']['screen_name'] .'&include_entities=true');
		return $content;
	}

	function get_snsimg($snsType){
		if ($snsType=="facebook"){
			$sns_ico_name = "facebook_ico.gif";
		}else if($snsType=="twitter"){
			$sns_ico_name = "twt_ico.gif";
		}else if($snsType=="me2day"){
			$sns_ico_name = "metoday_ico.gif";
		}else if($snsType=="yozm"){
			$sns_ico_name = "yozm_ico.gif";
		}else{
			$sns_ico_name = "ico_none.gif";
		}
		return $sns_ico_name;
	}

	function find_login_snsid(){
		global $_COOKIE , $_SESSION;
		if ($_SESSION['state'] && $_SESSION['fb_token_url']) $is_sns = $_SESSION['fb_user']->id; //페이스북 로그인확인값
		if ($_SESSION['access_token']['user_id'])$is_sns = $is_sns.$_SESSION['access_token']['user_id'];//트위터 로그인확인값
		return $is_sns;
	}

	function find_login_sns(){
		global $_COOKIE , $_SESSION;
		if ($_SESSION['state'] && $_SESSION['fb_token_url']) { $is_sns = "facebook"; }
		else if ($_SESSION['access_token']['user_id']){ $is_sns = "twitter"; }
		else{$is_sns = "no";}
		return $is_sns;
	}

	function sns_icon($P_SKIN){
		$is_sns = find_login_sns();
		$icon = "";
		if($is_sns == "facebook"){ //페이스북 로그인 / 로그아웃
			$icon .="<li><a href='/socialauth/twtapi/clearsessions.php' class='menu_tt'><img src='/pages/skin/$P_SKIN/img/so_facebook.png' width='39' height='39'  title='페이스북'/></a></li> ";
		}
		else{
			$confirm_sns = "facebook";
			$icon .="<li><img src='/pages/skin/$P_SKIN/img/so_facebook_bw.png' width='39' height='39' onClick=sns_login_check('$is_sns','$confirm_sns'); style='cursor:pointer;'></li> ";
		}
		if($is_sns == "twitter"){ //트위터 로그인 / 로그아웃
			   $icon .= "<li><a href='/socialauth/twtapi/clearsessions.php' class='menu_tt'><img src='/pages/skin/$P_SKIN/img/so_twitter.png' width='39' height='39'  title='트위터'/></a></li> ";
		}
		else{
			   $confirm_sns = "twitter";
			   $icon .= "<li><img src='/pages/skin/$P_SKIN/img/so_twitter_bw.png' width='39' height='39' onClick=sns_login_check('$is_sns','$confirm_sns'); style='cursor:pointer;' ></li> ";
		}
		return $icon;
   }


   function fb_out(){
		//global $row_setup;
		echo "<script>if(confirm('소셜 아이디로 로그인 하시겠습니까?')){ location.href=('/pages/append.facebook_pro.php?chk_value=1'); }</script>";
   }

   function confirm_sns2($fb_appid){
		if($_SESSION['access_token']['user_id']){$confirm_sns = "twitter";}
		if($_SESSION['state'] && $_SESSION['fb_token_url']){$confirm_sns = "facebook";}
		return $confirm_sns;
   }

	// 페이스북 로그인 정보 가져오기
	function fboauth(){
		global $_SESSION;
		$response = CurlExec($_SESSION['fb_token_url']);
		$params = null;
		parse_str($response, $params);
		$_SESSION['fb_token_access_token'] = $params['access_token'];
		$graph_url = "https://graph.facebook.com/me?access_token="  . $params['access_token'];
		return json_decode(CurlExec($graph_url));
	}

	// 페이스북 로그인 정보 가져오기 --> 이전 함수 사용하기
	function get_facebookinfo(){
		//$app_facebook_uid = facebook_uid($_COOKIE["fbsr_" . $fb_appid]);
		//$fb_graph_url = "https://graph.facebook.com/${app_facebook_uid}?" . str_replace("\"","",stripslashes($_COOKIE["fbsr_" . $fb_appid]));
		//$fb_graph_url_picture = "https://graph.facebook.com/${app_facebook_uid}/picture";
		//$user = json_decode(CurlExec($fb_graph_url));
		//return $user;
		global $_SESSION;
		return fboauth();
	}
// ---------------- 소셜 적용 ---------------- //


	## 관리자 로그인 처리
	function login_admin($serialnum,$_MranDsum,$_MaddSum,$keepTerm) {
		global $_COOKIE , $_SERVER;
		$_Mstring_join = $serialnum.$_MranDsum.$_MaddSum;
		if($_COOKIE['auth_adminid']) {
			samesiteCookie("auth_adminid", "" , -999 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
			return false;
		}
		else {
			samesiteCookie("auth_adminid", $serialnum , 0 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
			samesiteCookie("auth_adminid_sess", md5($_Mstring_join) , 0 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
			return true;
		}
	}

	## 입점업체 로그인 처리
	function login_subcompany($serialnum,$_MranDsum,$_MaddSum,$keepTerm) {
		global $_COOKIE , $_SERVER;
		$_Mstring_join = $serialnum.$_MranDsum.$_MaddSum;
		if($_COOKIE['auth_comid']) {
			samesiteCookie("auth_comid", "" , -999 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
		}
		samesiteCookie("auth_comid", $serialnum , 0 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
		samesiteCookie("auth_comid_sess", md5($_Mstring_join) , 0 , "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
		return true;
	}


	// 관리자 페이지 항목 설명
	// app_class => blue / orange (두가지 타입)
	function _DescStr($str , $app_class="blue"){
		return "<div class='guide_text'><span class='ic_". $app_class ."'></span><span class='". $app_class ."'>" . $str . "</span></div>";
	}




	#### 전송버튼 ####
	function _submitBTN($str , $var=null) { // var 은 get방식으로 "var1=변수1&var2=변수2" 형식으로 입력하여야 함
		global $pass_variable_string_url , $_PVSC;
		if($_PVSC) {
			$app_pvsc = enc('d' , $_PVSC);
		}
		else {
			$app_pvsc = enc('d' , $pass_variable_string_url);
		}
		return "
					<div class='bottom_btn_area'>
						<div class='btn_line_up_center'>
							<span class='shop_btn_pack btn_input_red'><input type='submit' name='' class='input_large' value='확인'></span>
							<span class='shop_btn_pack'><span class='blank_3'></span></span>
							<span class='shop_btn_pack btn_input_gray'><input type='button' name='' class='input_large' value='목록' onclick=\"location.href='${str}?${app_pvsc}&{$var}'\">
							</span>
						</div>
					</div>
		";
	}

	#### 전송버튼 ####
	function _submitBTNsub() {
		return "
					<div class='bottom_btn_area'>
						<div class='btn_line_up_center'>
							<span class='shop_btn_pack btn_input_red'><input type='submit' name='' class='input_large' value='확인'></span>
						</div>
					</div>
		";
	}







	#### select 형 input 처리 ####
	## _InputSelect( 이름 , 배열 , 정해진 값 , 이벤트 , 정해진(지정)배열 , 초기값)
	function _InputSelect( $_name , $_arr , $_chk , $_event , $_arr2=null , $initval =null) {
		if( !$initval ) { $initval = "-선택-";}
		$_str = "<select name='${_name}' ${_event}><option value=''>${initval}</option>";
		foreach( $_arr  as $key=>$val ) {
			if( $_arr2 !="" ){ $_appname = $_arr2[$key]; }
			else { $_appname = $val ; }
			$_str .= "<option value='${val}' ";
			if( $val == $_chk ) { $_str .= "selected"; }
			$_str .=">${_appname}</option>";
		}
		$_str .= "</select>";
		return $_str ;
	}

	#### select 형 input 처리 - 숫자연속형 ####
	## _InputSelectNum( 이름 , 시작값 , 종료값 , 정해진 값 , 이벤트 , 초기값)
	function _InputSelectNum( $_name , $_num1 , $_num2 , $_chk , $_event , $initval =null) {
		if( !$initval ) { $initval = "-";}
		$_str = "<select name='${_name}' ${_event} ><option value=''>${initval}</option>";
		for( $i=$_num1; $i<=$_num2; $i++ ){
			$_str .= "<option value='$i' ";
			if( $i == $_chk ) { $_str .= "selected"; }
			$_str .=" >" . $i."</option>" ;
		}
		$_str .= "</select>";
		return $_str ;
	}

	#### 1~12월 선택 ####
	function _InputSelectMonth( $_name , $_chk , $_event , $initval =null) {
		if( !$initval ) { $initval = "-";}
		$_str = "<select name='${_name}' ${_event}><option value=''>${initval}</option>";
		for( $i=1 ; $i<=12 ; $i++  ){
			$_str .= "<option value='" . sprintf("%02d" , $i) . "' ";
			if( $i == $_chk ) { $_str .= "selected"; }
			$_str .=">${i}월</option>";
		}
		$_str .= "</select>";
		return $_str ;
	}

	#### 1~31일 선택 ####
	function _InputSelectDay( $_name , $_chk , $_event , $initval =null) {
		if( !$initval ) { $initval = "-";}
		$_str = "<select name='${_name}' ${_event}><option value=''>${initval}</option>";
		for( $i=1 ; $i<=31 ; $i++  ){
			$_str .= "<option value='" . sprintf("%02d" , $i) . "' ";
			if( $i == $_chk ) { $_str .= "selected"; }
			$_str .=">${i}일</option>";
		}
		$_str .= "</select>";
		return $_str ;
	}

	#### radio 형 input 처리####
	## _InputRadio( 이름 , 배열 , 정해진 값 , 이벤트 , 정해진(지정)배열 , )
	function _InputRadio( $_name , $_arr , $_chk , $_event , $_arr2 ) {
		if( sizeof($_arr2) >0 ) {
			$arr_appname = $_arr2;
		}
		else {
			$arr_appname = $_arr;
		}
		foreach( $_arr as $k=>$v ){
			if( $k!=0 && $k%4==0 ) {$_str .= "</span><span class='multi'>";}
			$_str .= "<span class='multi' ><label for='${_name}{$v}'><input type=radio id='${_name}{$v}' name='${_name}' value='{$v}' ".$_event;
			if( $_chk == $v ) {
				$_str .= "checked";
			}
			$_str .=" > " . $arr_appname[$k] ."</label></span>&nbsp;&nbsp;&nbsp;" ;
		}
		return $_str;
	}
	#### checkbox 형 input 처리####
	## _InputCheckbox( 이름 , 배열 , 정해진 값(반드시 배열형태) , 이벤트 , 정해진(지정)배열 , )
	function _InputCheckbox( $_name , $_arr , $_chk , $_event , $_arr2 ) {
		if( sizeof($_arr2) >0 ) { $arr_appname = $_arr2; }
		else { $arr_appname = $_arr; }
		foreach( $_arr as $k=>$v ){
			// 배열값이 1개일경우 따로 분류하여 처리한다.
			if(sizeof($_arr)>1) {
				$_str .= "<span class='multi' ><label for='${_name}{$v}'><input type=checkbox id='${_name}{$v}' name='${_name}[]' value='{$v}' ";
				if( @in_array($v , $_chk) ) $_str .= "checked";
			}
			else {
				$_str .= "<span class='multi' ><label for='${_name}{$v}'><input type=checkbox id='${_name}{$v}' name='${_name}' value='{$v}' ";
				if( $v == $_chk) $_str .= "checked";
			}
			$_str .=" > " . $arr_appname[$k] ."</label></span>&nbsp;&nbsp;&nbsp;" ;
		}
		return $_str ;
	}

	// 전화번호 하이픈 넣기
	function tel_format($telNo) {
		 $telNo = preg_replace('/[^\d\n]+/', '', $telNo);
		 if(substr($telNo,0,1)!="0" && strlen($telNo)>8) $telNo = "0".$telNo;
		 $Pn3 = substr($telNo,-4);
		 if(substr($telNo,0,2)=="01") $Pn1 =  substr($telNo,0,3);
		 elseif(substr($telNo,0,2)=="02") $Pn1 =  substr($telNo,0,2);
		 elseif(substr($telNo,0,3)=="050") $Pn1 =  substr($telNo,0,4);
		 elseif(substr($telNo,0,1)=="0") $Pn1 =  substr($telNo,0,3);
		 $Pn2 = substr($telNo,strlen($Pn1),-4);
		 return implode("-",array_filter(array($Pn1,$Pn2,$Pn3)));
	}

	// 새로운 썸네일 적용
	// app_product_thumbnail( 폴더 , 원본이미지(정사각형, 직사각형) , 썸네일형태(장바구니 , 최근본상품 , 주문확인))
	// *지정항 mode는 반드시 넓이, 높이 수치가 지정되어야 함*
	function app_product_thumbnail( $app_path , $_img_name , $mode ){
		global $arr_product_size; // 이미지 크기 불러오기

		include_once "wideimage/lib/WideImage.php";
		$image = WideImage::load($app_path . "/" . $_img_name);

		$v = $arr_product_size[$mode];
		$app_thumbnail_img = $v[0] . "x" . $v[1] ."_" . $_img_name ;
		@unlink($app_path . $app_thumbnail_img); // 기존 이미지 삭제
		$image->resize($v[0],$v[1],'outside')->crop('center','center' , $v[0] , $v[1])->saveToFile( $app_path . "/" . $app_thumbnail_img );//썸네일 적용
	}

	// 썸네일 정보 불러오기
	// app_thumbnail( 모드 , 상품배열 )
	function app_thumbnail( $mode , $product_row ){
		global $arr_product_size ; // var.php 정보 참조
		switch($mode){
			case "장바구니": $app_img = $arr_product_size["장바구니"][0]."x".$arr_product_size["장바구니"][1]."_".$product_row[prolist_img] ; break;
			case "최근본상품": $app_img = $arr_product_size["최근본상품"][0]."x".$arr_product_size["최근본상품"][1]."_".$product_row[prolist_img2] ; break;
			case "주문확인": $app_img = $arr_product_size["주문확인"][0]."x".$arr_product_size["주문확인"][1]."_".$product_row[prolist_img2] ; break;
		}
		return $app_img ;
	}

	// 상품 아이콘 추가하기.
	// 인자 : 상품배열
	function get_product_icon_info($row) {
		global $row_setup;

		$result[today_open]     = $row[sale_date] == date('Y-m-d') 		? "<img src='/pages/images/upper_ic_4.gif' alt='오늘오픈' /> " : NULL;	// 오늘 오픈상품
		$result[today_end]      = $row[sale_type]!='A'&&$row[sale_enddate] <= date('Y-m-d',strtotime("+".$row_setup[s_main_close_day]." days")) 	? "<img src='/pages/images/upper_ic_5.gif' alt='마감임박' /> " : NULL;	// 마감임박상픔
		//$result[best]      		= $row[bestview] == "Y" 				? "<img src='/pages/images/upper_ic_1.gif' alt='베스트' /> " : NULL;	// 베스트상품

		// 배송상품 전용 아이콘.
		if($row[setup_delivery]=='Y') {
			$result[free_delivery]  = $row['del_type'] == 'free' ? "<img src='/pages/images/upper_ic_2.gif' alt='무료배송' /> " : NULL;		// 무료배송
		}

		$result = array_filter($result);	// 빈값 제거
		return sizeof($result) ? implode($result) : NULL;	// 상품 하단 아이콘
	}

	// 상품 아이콘 추가하기.
	// 인자 : 상품배열
	function get_product_icon_info_mobile($row) {
		global $row_setup;

		//$result[today_open]     = $row[sale_date] == date('Y-m-d') 		? "<img src='/m/images/upper_ic_4.gif' alt='오늘오픈' /> " : NULL;	// 오늘 오픈상품
		//$result[today_end]      = $row[sale_enddate] == date('Y-m-d') 	? "<img src='/m/images/ic_tend.gif' alt='오늘마감' /> " : NULL;	// 오늘 마감상픔
		$result[today_open]     = $row[sale_date] == date('Y-m-d') 		? "<img src='/pages/images/upper_ic_4.gif' alt='오늘오픈' /> " : NULL;	// 오늘 오픈상품
		// 오늘마감 아이콘 -> 마감임박 아이콘으로 변경 2017-08-29 SSJ
		//$result[today_end]      = $row[sale_enddate] == date('Y-m-d') 	? "<img src='/pages/images/upper_ic_5.gif' alt='오늘마감' /> " : NULL;	// 오늘 마감상픔
		$result[today_end]      = $row[sale_type]!='A'&&$row[sale_enddate] <= date('Y-m-d',strtotime("+".$row_setup[s_main_close_day]." days")) 	? "<img src='/pages/images/upper_ic_5.gif' alt='마감임박' /> " : NULL;	// 오늘 마감임박상픔
		//$result[best]      		= $row[bestview] == "Y" 				? "<img src='/pages/images/best_tag.gif' alt='베스트' /> " : NULL;	// 베스트상품


		// 배송상품 전용 아이콘.
		if($row[setup_delivery]=='Y') {
			// 무료배송 아이콘 노출조건 변경 2017-08-29 SSJ
			//$result[free_delivery]  = !$row[del_price] ? "<img src='/m/images/upper_ic_2.gif' alt='무료배송' /> " : NULL;		// 무료배송
			$result[free_delivery]  = $row['del_type'] == 'free' ? "<img src='/m/images/upper_ic_2.gif' alt='무료배송' /> " : NULL;		// 무료배송
		}

		$result = array_filter($result);	// 빈값 제거
		return sizeof($result) ? implode($result) : NULL;	// 상품 하단 아이콘

	}
	// - 문구 중 url 주소 자동링크 ---
	 function string_auto_link($str) {

		 // http
		 $str = preg_replace("/http:\/\/([0-9a-z-.\/@~?&=_]+)/i", "<a href=\"http://\\1\" target='_blank'>http://\\1</a>", $str);

		 // ftp
		 $str = preg_replace("/ftp:\/\/([0-9a-z-.\/@~?&=_]+)/i", "<a href=\"ftp://\\1\" target='_blank'>ftp://\\1</a>", $str);

		 // email
		 $str = preg_replace("/([_0-9a-z-]+(\.[_0-9a-z-]+)*)@([0-9a-z-]+(\.[0-9a-z-]+)*)/i", "<a href=\"mailto:\\1@\\3\">\\1@\\3</a>", $str);

		 return $str;

	 }

	 // 각 PG사별 전표를 출력한다
	 function link_credit_receipt($ordernum, $text='[전표보기]') {

		GLOBAL $_SERVER, $row_setup;

		$tmp = _MQ("select * from odtOrderCardlog where oc_oordernum='".$ordernum."' order by oc_uid desc limit 1");
		$arr_occontent = array(); $ex = explode("§§" , $tmp[oc_content]);
		foreach( $ex as $sk=>$sv ){ $ex2 = explode("||" , $sv); $arr_occontent[$ex2[0]] = $ex2[1]; }
		$ordr = _MQ("select * from odtOrder where ordernum = '".$ordernum."'");

		switch($row_setup[P_KBN]) {

			case "B":
				return "<a href='#none' onclick=\"window.open('https://cpadmin.billgate.net/billgate/common/authCardReceipt.jsp?mid=".$row_setup[P_ID]."&transNm=".$tmp[oc_tid]."&currTp=0000','C_receipt','width=400, height=750'); return false;\"><b>".$text."</b></a>";
			break;

			case "K":
				return "
				<a href='#none' onclick=\"showReceipt( '".$tmp[oc_tid]."', '".$ordernum."', '".$ordr[tPrice]."' );return false;\"><b>".$text."</b></a>
				<script>
					function showReceipt(tid,ordernum,amount) {
						popupWin =  window.open( 'https://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=card_bill&tno='+tid+'&order_no='+ordernum+'&trade_mony='+amount, 'popWinName','menubar=1,toolbar=0,width=470,height=815,resize=1,left=10,top=10' );
					}
				</script>";
			break;

			case "I":
				return "
				<a href='#none' onclick=\"showReceipt('".$tmp[oc_tid]."');return false;\"><b>".$text."</b></a>
				<script>
					function showReceipt(noTid) {
						popupWin =  window.open( 'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid=' + noTid + '&noMethod=1', 'popWinName','menubar=1,toolbar=0,width=450,height=667,resize=1,left=252,top=116' );
					}
				</script>";
			break;

			case "L":
				$CST_PLATFORM = $row_setup[P_MODE];
				$CST_MID = $row_setup[P_ID];
				$LGD_MID = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
				$LGD_MERTKEY = $row_setup[P_PW];
				$LGD_TID = $tmp[oc_tid];
				$authdata = md5($LGD_MID.$LGD_TID.$LGD_MERTKEY);
				return "<script language='JavaScript' src='http://pgweb.uplus.co.kr".($CST_PLATFORM=='test'?':7085':'')."/WEB_SERVER/js/receipt_link.js'></script>
				<a onclick=\"showReceiptByTID('".$LGD_MID."', '".$LGD_TID."', '".$authdata."');return false;\" href='#none'><b>".$text."</b></a>";
			break;

			case "A":
				return "<a href='#none' onclick=\"receipt('".$tmp[oc_tid]."','".$row_setup[P_ID]."','".substr($arr_occontent[rApprTm],0,8)."','".$arr_occontent[rDealNo]."');return false;\"><b>".$text."</b></a>
				<script>
					function receipt(adm_no, service_id, send_dt, send_no){
						url='http://www.allthegate.com/receipt/receipt.jsp'
						url=url+'?adm_no='+adm_no;
						url=url+'&service_id='+service_id;
						url=url+'&send_dt='+send_dt;
						url=url+'&send_no='+send_no;
						url=url+'&path=home';
						window.open(url, 'window','toolbar=no,location=no,directories=no,status=,menubar=no,scrollbars=no,resizable=no,width=423,height=668,top=0,left=150');
					}
				</script>";
			break;

			case "D":
				//return "<a href='#none' onclick=\"window.open('https://agent.daoupay.com/common/PayInfoPrintCreditCard.jsp?DAOUTRX=".$tmp[oc_tid]."','C_receipt','width=400, height=750'); return false;\"><b>".$text."</b></a>";
				return "<a href='#none' onclick=\"popup_receipt(); return false;\"><b>".$text."</b></a>
							<form id='frm_receipt' name='frm_receipt' action='https://agent.daoupay.com/common/PayInfoPrintDirectCard.jsp' method='post' target='C_receipt'>
							<input type='hidden' name='DAOUTRX' value='".$tmp[oc_tid]."'>
							</form>
							<script>
							function popup_receipt(){
								frm = document.getElementById('frm_receipt');
								window.open('','C_receipt','width=400, height=750');
								frm.submit();
							}
							</script>";
			break;

		}

	 }


	# LDD002: 기존 함수 변경
	/*
	is_mobile() 또는 is_mobile('cookie') 를 사용할 경우 기존 방식과 동일 하게 쿠키 값을 참조 하면 그 외
	is_mobile('real') 또는 is_mobile('???')시 실제 디바이스를 체크 한다.
	*/
	// 모바일 접속인지 체크한다.
	function is_mobile($check='cookie') {

		global $_COOKIE,$_SERVER;

		// PC모드를 선택한 상태라면 flase
		if($_COOKIE['AuthNoMobile'] == "pc" && $check == 'cookie') return false;

		if( preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $_SERVER['HTTP_USER_AGENT']) ) return true;
		else return false;
	}



	# LDD006
	// 문자열의 바이트를 구한다.
	function utf8_byte($str) {

		$len = strlen($str);
		for ($i = $length = $size = 0; $i < $len; $length++) {

			$high = ord($str{$i});

			if ($high < 0x80) {

				$i += 1;
				$size += 1;
			}
			else if ($high < 0xE0) {

				$i += 2;
				$size += 2;
			}
			else if ($high < 0xF0) {

				$i += 3;
				$size += 2;
			}
			else {

				$i += 4;
				$size += 2;
			}
		}

		return $size;
	}

	# LDD006
	// 문자열을 바이트로 분활 하여 배열에 추가 한다.
	function lms_to_sms_array($str, $byte=90) {

		$s = 0; // 바이트 카운트
		$string = ''; // 임시 문자열
		$array = array(); // 반환 배열

		// 바이트 제한별 배열 처리
		for($i=0; $i<strlen(trim($str)); $i++) {

			$string .= $str[$i];
			if(utf8_byte($string) > $byte) {

				$string = '';
				$s++;
				$string = $str[$i];
			}
			else {

				$array[$s] = $string;
			}
		}

		// 각 배열 문구의 앞뒤 공백 제거
		foreach($array as $k=>$v) {

			$array[$k] = trim($v);
		}

		return array_filter($array);
	}


	/*
	#LDD006
	LMS의 문구를 옵션에 따라 ReBuild한다.
	$string: 최종 발송문구
	$option: 빌드 옵션
		$option = 'D'; : 일반발송(90byte 이내: SMS, 90byte 이상: LMS) - 기본값
		$option = 'S'; : SMS 단일발송(90byte를 초과하는 내용을 제외하고 반환)
		$option = 'M'; : SMS 분할발송(90byte를 초과 하였을 경우 90byte를 기준으로 분할하여 배열 반환)
	*/
	function lms_string_build($string, $option='D') {

		// string을 SMS 규격으로 분할
		$ReString = lms_to_sms_array($string);

		// 옵션에 따른 string 처리
		if($option == 'M') return $ReString; // SMS 분할발
		else if($option == 'S') return $ReString[0]; // SMS 단일발송
		else return $string; // 일반발송
	}



	// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
	/*
	# LDD008
	SMS 문구를 치환
	- 기본정보: sms_msg_replace('내용')
	- 주문정보: sms_msg_replace('내용', '주문번호')
	- 치환자 추가:
	    └ 기본정보: sms_msg_replace('내용', '', array('{{치환자}}'=>'replace', '{{주소}}'=>'http://www.onedaynet.co.kr'))
	    └ 주문정보: sms_msg_replace('내용', '주문번호', array('{{치환자}}'=>'replace', '{{주소}}'=>'http://www.onedaynet.co.kr'))
	- 기본 치환 제외항목: {{상품문의}}, {{쿠폰번호}}, {{송장번호}}, {{배송일}}
	*/
	function sms_msg_replace($msg, $ordernum='', $AddOption=array(), $op_uid_temp = false) {
		global $row_setup, $row_member; // 설정정보, 회원정보

		# 기본정보 치환
		$strings = array(
					'{{사이트명}}'   => $row_setup['site_name'],
					'{{회원명}}'     => ($row_member['name']?$row_member['name']:'비회원'),
					'{{회원아이디}}' => ($row_member['id']?$row_member['id']:''),
					'{{회원이메일}}' => ($row_member['email']?$row_member['email']:'')
					);


		# 주문정보 치환
		if(trim($ordernum)) {

			$order = get_order_info($ordernum); // 주문정보
			$product = get_order_product_info($ordernum,rm_str($op_uid_temp)); // 주문상품 정보

			// 전체주문상품명
			$arr_product_name = array();
			if(sizeof($product) > 0 ) {
				foreach($product as $pk => $pv){$arr_product_name[] = trim($pv['op_pname'] . " - " . $pv['op_cnt'] ."개" .  ($pv['op_option1'] ? "(".implode(" ",array($pv['op_option1'],$pv['op_option2'],$pv['op_option3'])) . ")" : "")) ;}
			}

			$stringsAdd = array(
					'{{주문번호}}'		=> $ordernum,
					'{{구매자명}}'		=> $order['ordername'],
					'{{구매자이메일}}'	=> $order['orderemail'],
					'{{사용자명}}'		=> $order['username'],
					'{{사용자이메일}}'	=> $order['useremail'],
					'{{결제금액}}'		=> number_format($order['tPrice']),
					'{{입금계좌정보}}'	=> $order['paybankname'],
					'{{주문일}}'		=> (rm_str($order['orderdate']) > 0 ? date("Y-m-d" , strtotime($order['orderdate'])) : ""),
					'{{결제일}}'		=> (rm_str($order['paydate']) > 0 ? date("Y-m-d" , strtotime($order['paydate'])) : ""),

					'{{주문상품명}}'	=> $product[0]['op_pname']." ".implode(" ",array($product[0]['op_option1'],$product[0]['op_option2'],$product[0]['op_option3'])),
					'{{전체주문상품명}}'	=> implode("\n", array_values($arr_product_name) ),
					'{{주문상품수}}'	=> sizeof($product),
					'{{배송일}}'	=> (rm_str($product[0]['op_expressdate']) > 0 ? date("Y-m-d" , strtotime($product[0]['op_expressdate'])) : ""),
					'{{택배사명}}'	=> $product[0]['op_expressname'],
					'{{송장번호}}'	=> $product[0]['op_expressnum'],
					);
			$strings = array_merge($strings, $stringsAdd);
			// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		}

		# 사용자 지정 치환자
		$strings = array_merge($strings, $AddOption);

		# 치환적용
		foreach($strings as $k=>$v) { $msg = str_replace($k, trim($v), $msg); }

		# 지정되지 않은 치환자 삭제
		$msg = preg_replace('/\{\{.*\}\}/iU', '',$msg);

		# 반환
		return $msg;
	}
	// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----





	// --- 암호화 ---
	function onedaynet_encode( $str ){
		global $onedaynet_id;
		$key = pack('H*', md5($onedaynet_id));
		$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
		$pad = $size - (strlen($str) % $size);
		$str = $str . str_repeat(chr($pad), $pad);
		$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, $key, $iv);
		$data = mcrypt_generic($td, $str);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		return urlencode(enc('e' , $data)); // -- 익스버그 해결 -- 2019-10-01 LCY
	}


	// --- 복호화 ---
	function onedaynet_decode( $encode_str ){
		global $onedaynet_id;

		$encode_str = urldecode($encode_str); // -- 익스버그 해결 -- 2019-10-01 LCY
		$key = pack('H*', md5($onedaynet_id));
		$decrypted= mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$key, enc('d',$encode_str), MCRYPT_MODE_ECB);
		$dec_s = strlen($decrypted);
		$padding = ord($decrypted[$dec_s-1]);
		$decrypted = substr($decrypted, 0, -$padding);
		return $decrypted;
	}



	// 이미지 경로 치환 # LDD015
	// 2016-05-10 :: 수정 -- 보안서버 적용시 오류발생으로 인한
	function replace_image($str) {
		global $_SERVER;

		if(preg_match('/\/\//', $str)) {
			$tmp1 = explode('http', $str); // 2017-02-20  JJC
			$tmp2 = array_shift($tmp1); // 1번째 인자 날림... // 2017-02-20 JJC

			// $TmpStr = implode('', $tmp1); // 2017-02-20 JJC
			// $arr_url = parse_url($str);
			$TmpStr = 'http' . implode('', $tmp1); // 2017-02-20 JJC
			$arr_url = parse_url($TmpStr);

			$arr_url['path'] = str_replace('&', '&', str_replace('&amp', '&', str_replace('&', '&', $arr_url['path']))); // & 처리
			return $arr_url['scheme'] . "://" . $arr_url['host'] . ($arr_url['port'] ? ":" . $arr_url['port'] : "")  . $arr_url['path'];
		}
		else {
			$str = str_replace('&', '&', str_replace('&amp', '&', str_replace('&', '&', $str))); // & 처리
			return ($_SERVER["HTTPS"] == "on" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . $str;
		}
	}
	function replace_url($str) { return replace_image($str); }

	# 상품주소를 리라이트 주소로 변경 2015-11-16
	function rewrite_url($pcode, $add_url='') {
		global $row_setup;

		if($row_setup['rewrite_chk'] == 'no') return ($_SERVER['HTTPS']=='on'?'https://':'http://').$_SERVER["HTTP_HOST"].'/?pn=product.view&pcode='.$pcode.($add_url?'&'.$add_url:null);
		else return ($_SERVER['HTTPS']=='on'?'https://':'http://').$_SERVER["HTTP_HOST"].'/'.$pcode.($add_url?'?'.$add_url:null);
	}

	// - 모비톡 계정확인 함수 --
	function onedaynet_sms_user() {

		global $row_setup, $row_company;
		if($row_setup['sms_id'] && $row_setup['sms_pw'] && $row_company['tel']) {
			$SMSDec = enc_array('d', $row_setup['sms_pw']);
			include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoap.php');
			$client = new soapclientW('http://mobitalk.gobeyond.co.kr/nusoap/user.info.php');
			$result = $client->call('user_info', array('id'=>$row_setup['sms_id'], 'pw'=>$SMSDec['sms_pw'], 'tel'=>$row_company['tel']));
			return json_decode($result, true);
		}
	}
	// - 모비톡 계정확인 함수 --

	// - 문자 발송 오류 로그 기록 ---
	function insert_sms_send_log($Result=array()) {

		if(count($Result) <= 0) return;

		// 자동 인스톨 처리
		$InstallCK = mysql_query(' desc odtSMSLog ');
		if(!@mysql_num_rows($InstallCK)) {

			_MQ_noreturn("
				CREATE TABLE  `odtSMSLog` (
					`idx` INT( 11 ) NOT NULL AUTO_INCREMENT COMMENT  '고유키',
					`code` VARCHAR( 5 ) NOT NULL COMMENT  '에러코드',
					`msg` VARCHAR( 255 ) NOT NULL COMMENT  '에러메시지',
					`send_num` VARCHAR( 20 ) NOT NULL COMMENT  '보내는 번호',
					`receive_num` VARCHAR( 20 ) NOT NULL COMMENT  '받는번호',
					`rdate` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00' COMMENT  '기록일',
					PRIMARY KEY (  `idx` )
				) ENGINE = MYISAM COMMENT =  'SMS 발송 에러로그'
			");
		}

		// 로그 기록
		foreach($Result as $k=>$v) {

			if($v['code'] == 'S00' || !$v['code']) continue;
			_MQ_noreturn(" insert into `odtSMSLog` set `code` = '{$v['code']}', `msg` = '{$v['data']}', `send_num` = '{$v['send_num']}', `receive_num` = '{$v['receive_num']}', `rdate` = now() ");
		}
	}
	// - 문자 발송 오류 로그 기록 ---

	// 상품 썸네일 이미지 체크
	//		product_row - odtProduct 데이터 배열
	//		thumb_name - 장바구니(prolist_img 사용) , 최근본상품(prolist_img2 사용) , 주문확인(prolist_img2 사용)
	//		type - return 형태 : html (<img src='이미지경로' title='이미지명' alt='이미지명'>) , data - 이미지경로
	function product_thumb_img( $product_row , $thumb_name ,  $type){
		global $_SERVER ;
		// 썸네일이 있는지 체크
		$thumb_img_src = IMG_DIR_PRODUCT . app_thumbnail( $thumb_name , $product_row );
		switch($thumb_name){
			case "장바구니": $chk_img = $product_row['prolist_img'] ; break;
			case "최근본상품": $chk_img = $product_row['prolist_img2']; break;
			case "주문확인": $chk_img = $product_row['prolist_img2'] ;break;
		}
		if(!file_exists( $_SERVER["DOCUMENT_ROOT"] . $thumb_img_src )){
			$thumb_img_src = replace_image(IMG_DIR_PRODUCT.$chk_img); // 아무정보도 없을 시 원본 이미지를 출력
		}
		else{
			$thumb_img_src = replace_image($thumb_img_src);
		}
		if($chk_img){
			return ($type == "html" ? "<img src='". $thumb_img_src ."' title='".stripslashes($product_row[name])."' alt='".stripslashes($product_row[name])."' >" : $thumb_img_src);
		}
	}


	// 앱에서 실행시 각 앱으로 제귀 URL 만듦  2016-01-21 LDD
	function InAppUrl($url, $fix_type='auto', $oduser='') {

		/*
		$Agent = 'NAVER(inapp; search; 410; 6.7.1)'; // 네이버
		$Agent = 'DaumApps/5.5.2 DaumDevice/mobile'; // 다음
		$Agent = 'ref:nate_app;appver:5.1.3;ndruk:201601211025407446276;skai:2016012110254078132'; // 네이트
		*/
		$Agent = $_SERVER['HTTP_USER_AGENT'];
		$url = urlencode(replace_url($url));
		$AgentM = array(
				'NAVER(inapp;'=>'naver',
				'DaumApps/'=>'daum',
				'nate_app;'=>'nate',
				'onedaynet;'=>'onedaynet',
				'none'=>'none'
		); // agent matching
		$AgentSC = array(
				'naver'=>'naversearchapp://inappbrowser?url='.$url.'&target=inpage&version=6',
				'daum'=>'daumapps://web?url='.$url,
				'nate'=>urldecode($url),
				'onedaynet'=>'onedaynet_'.$oduser.'://web?url='.$url, // 추후 추가
				'none'=>urldecode($url)
		); // schema list

		// anget find
		foreach($AgentM as $k=>$v) {

			if(strpos($Agent, $k) === false) continue;
			$type = $v;
		}
		if(!$type) $type = 'none';
		if($fix_type != 'auto') $type = $fix_type;

		return $AgentSC[$type];
	}

// 세션생성
function mk_sess($Name, $Val) {
    if(PHP_VERSION < '5.3.0') session_register($Name);
    $$Name = $_SESSION[$Name] = $Val;
}
// 세션 view
function view_sess($Name, $Val) { return (isset($_SESSION[$Name])?$_SESSION[$Name]:''); }
// 쿠키생성
function mk_cookie($Name, $Val) {

    $CName = md5($Name);
    samesiteCookie(md5($Name), base64_encode($Val), 0, "/");
    $$CName = $_COOKIE[$CName] = base64_encode($Val);
}
// 쿠키 view
function view_cookie($Name) {
    $cookie = md5($Name);
    if (array_key_exists($cookie, $_COOKIE)) return base64_decode($_COOKIE[$cookie]);
    else return "";
}
// 관리자 세션로그인
function AdminLogin() {
    global $_SESSION, $_SERVER;
    $row_setup = info_basic();
    mk_sess('AuthAdminDiff', md5($row_setup['licenseNumber'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
    mk_cookie('AuthAdminDiff', $_SESSION['AuthAdminDiff']);
}
// 관리자 세션로그아웃
function AdminLogout() {
    @session_unset(); // 모든 세션변수를 언레지스터 시켜줌
    @samesiteCookie("auth_adminid","",time() - 100000,"/");
    @samesiteCookie("auth_adminid", "" , 0, "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
    @samesiteCookie("auth_adminid_sess","",time() - 100000,"/");
    @samesiteCookie("auth_adminid_sess", "" , 0, "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
    mk_cookie('AuthAdminDiff', '', time() - 100000);
}
// 관리자 로그인체크
function AdminLoginCheck() {
    global $_SESSION, $_SERVER;

    $row_setup = info_basic();
    $AuthDiffCookie = view_cookie('AuthAdminDiff');
    $AuthDiff[] = md5($row_setup['licenseNumber'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
    $AuthDiff[] = $_SESSION['AuthAdminDiff'];
    $AuthDiff[] = ($AuthDiffCookie?$AuthDiffCookie:'0');
    $AuthDiff = @array_flip($AuthDiff);

    if( count($AuthDiff) === 1 && isset($AuthDiffCookie)) { return true; }
    else { AdminLogout(); die("<script>top.location.href=('/totalAdmin/logout.php')</script>"); return false; }
}


// -- 함수수정
// - 배열 넘김시 사용되는 변수 encode / decode ---
function enc_array($mode, $str) {
    if(!function_exists('onedaynet_encode') || !function_exists('onedaynet_decode')) return '필수 함수가 없습니다.';
    if($mode == 'd') return unserialize(onedaynet_decode($str));
    else if($mode == 'e') return onedaynet_encode(serialize($str));
    else return 'error';
}
//예) 인코딩 => enc_array( 'e' ,  array('테스트입니다.'));
//예) 디코딩 => enc_array( 'd' ,  $인코딩);
// - 배열 넘김시 사용되는 변수 encode / decode ---





// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----

	# 원데이넷 문자/알림톡발송 함수 - 개별발송
	// ( onedaynet_sms_send 대체함수 )
	//						tran_phone : 수신전화, tran_callback : 발신전화, tran_msg : 메시지
	function onedaynet_alimtalk_send($tran_phone, $tran_callback, $tran_msg , $smsInfo = array()) {
		global $row_setup, $_SERVER;

		//sms_send( 아이디 , 비번 , 받을 전번 , 보낸 전번 , 메시지 , 예약시간(형태 : 2015-12-10 13:21:25) , 서버아이피 )
		if( $tran_phone && $tran_callback && $tran_msg ) {
			$SMSDec = enc_array('d', $row_setup['sms_pw']);
			include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoap.php');

			$client = new soapclientW('http://mobitalk.gobeyond.co.kr/nusoap/tot.send.php');

			// alimtalkYN : 알림톡 적용여부( Y / N ), alimtalk_uid : 템플릿 고유번호
			// alimtalk_add1 : 적용변수1 , alimtalk_add2 : 적용변수2 , alimtalk_add3 : 적용변수3 , alimtalk_add4 : 적용변수4 , alimtalk_add5 : 적용변수5 , alimtalk_add6 : 적용변수6 , alimtalk_add7 : 적용변수7 , alimtalk_add8 : 적용변수8
			$alimtalkYN = ($smsInfo['kakao_status'] ? $smsInfo['kakao_status'] : 'N');
			$alimtalk_uid = $smsInfo['kakao_templet_num'] ;
			$alimtalk_add1 = $smsInfo['kakao_add1'] ;
			$alimtalk_add2 = $smsInfo['kakao_add2'] ;
			$alimtalk_add3 = $smsInfo['kakao_add3'] ;
			$alimtalk_add4 = $smsInfo['kakao_add4'] ;
			$alimtalk_add5 = $smsInfo['kakao_add5'] ;
			$alimtalk_add6 = $smsInfo['kakao_add6'] ;
			$alimtalk_add7 = $smsInfo['kakao_add7'] ;
			$alimtalk_add8 = $smsInfo['kakao_add8'] ;

			$result = $client->call('sms_send',array('id' => $row_setup['sms_id'], 'pw'=>$SMSDec['sms_pw'], 'receive_num'=>$tran_phone, 'send_num'=>$tran_callback, 'msg'=>$tran_msg, 'reserve_time'=>'', 'ip'=>'auto' , 'alimtalkYN' => $alimtalkYN , 'alimtalk_uid' => $alimtalk_uid, 'alimtalk_add1' => $alimtalk_add1 , 'alimtalk_add2' => $alimtalk_add2 , 'alimtalk_add3' => $alimtalk_add3 , 'alimtalk_add4' => $alimtalk_add4 , 'alimtalk_add5' => $alimtalk_add5 , 'alimtalk_add6' => $alimtalk_add6 , 'alimtalk_add7' => $alimtalk_add7 , 'alimtalk_add8' => $alimtalk_add8 ));
			$result_json = json_decode($result, true);
			if(in_array(gettype($result_json), array('integer', 'NULL'))) $result_array = array($result_json); // 이전 string 반환 교정
			else $result_array = $result_json;

			insert_sms_send_log($result_array);
			return $result_array;
		}
	}
	# 원데이넷 문자/알림톡발송 함수 - 개별발송



	# 원데이넷 문자/알림톡발송 함수 - 일괄발송
	// (onedaynet_sms_multisend 대체문자)
	//		* 알림톡일 경우 MMS을 발송할 수는 없음.
	//				$arr_send : 문자발송 배열
	//						receive_num : 수신번호 , send_num : 발신번호  , msg : 메시지 , image : 첨부이미지, reserve_time : 예약시간
	//						alimtalkYN : 알림톡 적용여부( Y / N ), alimtalk_uid : 템플릿 고유번호
	//						alimtalk_add1 : 적용변수1 , alimtalk_add2 : 적용변수2 , alimtalk_add3 : 적용변수3 , alimtalk_add4 : 적용변수4 , alimtalk_add5 : 적용변수5 , alimtalk_add6 : 적용변수6 , alimtalk_add7 : 적용변수7 , alimtalk_add8 : 적용변수8
	function onedaynet_alimtalk_multisend($arr_send = array()) {
		global $_SERVER, $row_setup;

		// 처리 데이터가 없는 경우
		if(count($arr_send) <= 0) return;

		// 초기값 설정
		$SMSDec = enc_array('d', $row_setup['sms_pw']);
		$tran_id = $row_setup['sms_id']; $tran_pw = $SMSDec['sms_pw']; $arr_send_string = array(); $arr_send_image = array(); $result_array = array();

		// 이미지처리
		foreach($arr_send as $k=>$v) {
			if(trim($v['image']) <> '' ) {
				array_push($arr_send_image, $arr_send[$k]); // 이미지가 있다면 MMS
			}
			else {
				array_push($arr_send_string, $arr_send[$k]); // 이미지가 없으면 SMS/LMS
			}
		}

		// nusoap include
		include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoap.php');


		// SMS/LMS 발송
		if(count($arr_send_string) > 0) {

			$client = new soapclientW('http://mobitalk.gobeyond.co.kr/nusoap/tot.send_server_multi.php');

			// sms_send( 아이디 , 비번 , 메세지 배열 , 서버아이피)
			$result = $client->call('sms_send', array('id'=>$tran_id, 'pw'=>$tran_pw, 'arr_send'=>$arr_send_string, 'ip'=>'auto'));
			$result_json = json_decode($result, true);
			if(in_array(gettype($result_json), array('integer', 'NULL'))) $result_array = array($result_json); // 이전 string 반환 교정
			else $result_array = $result_json;
		}

		// MMS 발송
		if(count($arr_send_image) > 0) {

			include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoapmime.php');
			foreach($arr_send_image as $k=>$v) {

				$tran_phone = $v['receive_num']; $tran_callback = $v['send_num']; $tran_msg = $v['msg'];
				$tran_reservetime = $v['reserve_time']; $tran_title = $v['title']; $tran_img = $v['image']; $tran_img_del = $v['image_del'];
				$app_dir = $_SERVER['DOCUMENT_ROOT'].'/upfiles';
				$client = new soapclientmime('http://mobitalk.gobeyond.co.kr/nusoap/tot.send_server_one?wsdl', true);
				$client->setHTTPEncoding('deflate, gzip');

				if($tran_img){
					$file = '';
					$fp = @fopen( $app_dir.'/'.$tran_img, 'rb');
					if($fp) { while(!feof($fp)){ $file .= fgets($fp); } }
					@fclose($fp);
					$cid = $client->addAttachment($file, $tran_img);
				}

				// mobitalk_mms_send(아이디, 비번, 받을번호, 보낸번호, 메시지, 제목, 이미지, 예약시간, 서버아이피)
				$result = $client->call(
					'mobitalk_mms_send',
					array(
						'tran_id'=>$tran_id, 'tran_pw'=>$tran_pw, 'tran_phone'=>$tran_phone, 'tran_callback'=>$tran_callback, 'tran_msg'=>$tran_msg,
						'tran_title'=>$tran_title, 'tran_img'=>$tran_img, 'tran_reservetime'=>$tran_reservetime, 'tran_ip'=>'auto',
						'alimtalkYN' => ($alimtalkYN ? $alimtalkYN : 'N') , 'alimtalk_uid' => $v['alimtalk_uid'] , 'alimtalk_add1' => $v['alimtalk_add1'], 'alimtalk_add2' => $v['alimtalk_add2'], 'alimtalk_add3' => $v['alimtalk_add3'], 'alimtalk_add4' => $v['alimtalk_add4'], 'alimtalk_add5' => $v['alimtalk_add5'], 'alimtalk_add6' => $v['alimtalk_add6'], 'alimtalk_add7' => $v['alimtalk_add7'], 'alimtalk_add8' => $v['alimtalk_add8']
					)
				);
				$result_json = json_decode($result, true);
				if(in_array(gettype($result_json), array('integer', 'NULL'))) $result_json = array($result); // 이전 string 반환 교정
				$result_array = array_merge($result_array, $result_json);
			}
		}

		insert_sms_send_log($result_array);
		return $result_array;
	}
	# 원데이넷 문자/알림톡발송 함수 - 일괄발송


	# 원데이넷 문자/알림톡발송을 위한 - 문자설정 배열 설정
	//		smsInfo : 지정한 문자의 배열정보
	//		arr_replace : 치환자 배열
	function smsinfo_array($smsInfo , $arr_replace){
		return array(
			'alimtalkYN' => ($smsInfo['kakao_status'] ? $smsInfo['kakao_status'] : 'N') ,
			'alimtalk_uid' => $smsInfo['kakao_templet_num'] ,
			'alimtalk_add1' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add1']) ,
			'alimtalk_add2' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add2']) ,
			'alimtalk_add3' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add3']) ,
			'alimtalk_add4' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add4']) ,
			'alimtalk_add5' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add5']) ,
			'alimtalk_add6' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add6']) ,
			'alimtalk_add7' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add7']) ,
			'alimtalk_add8' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add8'])
		);
	}
	# 원데이넷 문자/알림톡발송을 위한 - 문자설정 배열 설정

	/*
		# LDD008
		SMS 문구를 치환 - 배열화
			- 기본정보: sms_msg_replace_array('내용')
			- 주문정보: sms_msg_replace_array('내용', '주문번호')
			- 치환자 추가:
				└ 기본정보: sms_msg_replace('내용', '', array('{{치환자}}'=>'replace', '{{주소}}'=>'http://www.onedaynet.co.kr'))
				└ 주문정보: sms_msg_replace('내용', '주문번호', array('{{치환자}}'=>'replace', '{{주소}}'=>'http://www.onedaynet.co.kr'))
			- 기본 치환 제외항목: {{상품문의}}, {{쿠폰번호}}, {{송장번호}}, {{배송일}}
			- return 방식 array('msg' => $문자메시지문자열 , 'change' => $치환자배열);
	*/
	function sms_msg_replace_array($msg, $ordernum='', $AddOption=array(), $op_uid_temp = false) {
		global $row_setup, $row_member; // 설정정보, 회원정보

		# 기본정보 치환
		$strings = array(
					'{{사이트명}}'   => $row_setup['site_name'],
					'{{회원명}}'     => ($row_member['name']?$row_member['name']:'비회원'),
					'{{회원아이디}}' => ($row_member['id']?$row_member['id']:''),
					'{{회원이메일}}' => ($row_member['email']?$row_member['email']:'')
					);


		# 주문정보 치환
		if(trim($ordernum)) {

			$order = get_order_info($ordernum); // 주문정보
			$product = get_order_product_info($ordernum,$op_uid_temp); // 주문상품 정보

			// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
			// 전체주문상품명
			$arr_product_name = array();
			if(sizeof($product) > 0 ) {
				foreach($product as $pk => $pv){$arr_product_name[] = trim($pv['op_pname'] . " - " . $pv['op_cnt'] ."개" .  ($pv['op_option1'] ? "(".implode(" ",array($pv['op_option1'],$pv['op_option2'],$pv['op_option3'])) . ")" : "")) ;}
			}
			$stringsAdd = array(
					'{{주문번호}}'		=> $ordernum,
					'{{구매자명}}'		=> $order['ordername'],
					'{{구매자이메일}}'	=> $order['orderemail'],
					'{{사용자명}}'		=> $order['username'],
					'{{사용자이메일}}'	=> $order['useremail'],
					'{{결제금액}}'		=> number_format($order['tPrice']),
					'{{입금계좌정보}}'	=> $order['paybankname'],
					'{{주문일}}'		=> (rm_str($order['orderdate']) > 0 ? date("Y-m-d" , strtotime($order['orderdate'])) : ""),
					'{{결제일}}'		=> (rm_str($order['paydate']) > 0 ? date("Y-m-d" , strtotime($order['paydate'])) : ""),

					'{{주문상품명}}'	=> $product[0]['op_pname']." ".implode(" ",array($product[0]['op_option1'],$product[0]['op_option2'],$product[0]['op_option3'])),
					'{{전체주문상품명}}'	=> implode("\n", array_values($arr_product_name) ),
					'{{주문상품수}}'	=> sizeof($product),
					'{{배송일}}'	=> (rm_str($product[0]['op_expressdate']) > 0 ? date("Y-m-d" , strtotime($product[0]['op_expressdate'])) : ""),
					'{{택배사명}}'	=> $product[0]['op_expressname'],
					'{{송장번호}}'	=> $product[0]['op_expressnum'],
					);
			$strings = array_merge($strings, $stringsAdd);
			// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----
		}

		# 사용자 지정 치환자
		$strings = array_merge($strings, $AddOption);

		# 치환적용
		foreach($strings as $k=>$v) { $msg = str_replace($k, trim($v), $msg); }

		# 지정되지 않은 치환자 삭제
		$msg = preg_replace('/\{\{.*\}\}/iU', '',$msg);

		# 반환
		return array('msg' => $msg , 'change' => $strings);
	}

	// 문자메시지 발송을 위한 배열화 함수
	//			$row_sms : 문자메시지 설정 정보 - 배열
	//			$smskbn : 문자 발송 유형
	//			$arr_sms_msg : 발송메시지 & 치환자배열
	//			$sms_to : 수신전화번호
	//			$sms_from : 발송전화번호
	//			$sms_msg : 발송메시지
	//			$sms_title : LMS/MMS - 타이틀명
	//			$sms_file : MMS 이미지
	//	--> arr_send 배열 return 함
	function sms_array_build($row_sms , $smskbn , $arr_sms_msg , $sms_to , $sms_from , $sms_msg , $sms_title , $sms_file){
		if(!$sms_file) {
			$sms_msg = lms_string_build($sms_msg, $row_sms[$smskbn]['sms_send_type']);
			if(is_array($sms_msg)) {
				foreach($sms_msg as $kkk=>$vvv) {
					//$arr_send[] = array('receive_num'=> $sms_to , 'send_num'=> $sms_from , 'msg'=> $vvv  , 'reserve_time'=>'' , 'title'=> $sms_title , 'image'=> $sms_file );
					$arr_send[] = array_merge(array('receive_num'=> $sms_to , 'send_num'=> $sms_from , 'msg'=> $vvv, 'reserve_time'=>'', 'title'=>$sms_title, 'image'=>$sms_file ) , smsinfo_array($row_sms[$smskbn] , $arr_sms_msg['change']));
				}
			}
			else {
				//$arr_send[] = array('receive_num'=> $sms_to , 'send_num'=> $sms_from , 'msg'=> $sms_msg  , 'reserve_time'=>'' , 'title'=> $sms_title , 'image'=> $sms_file );
				$arr_send[] = array_merge(array('receive_num'=> $sms_to , 'send_num'=> $sms_from , 'msg'=> $sms_msg, 'reserve_time'=>'', 'title'=>$sms_title, 'image'=>$sms_file ) , smsinfo_array($row_sms[$smskbn] , $arr_sms_msg['change']));
			}
		}
		else {
			//$arr_send[] = array('receive_num'=> $sms_to , 'send_num'=> $sms_from , 'msg'=> $sms_msg  , 'reserve_time'=>'' , 'title'=> $sms_title , 'image'=> $sms_file );
			$arr_send[] = array_merge(array('receive_num'=> $sms_to , 'send_num'=> $sms_from , 'msg'=> $sms_msg, 'reserve_time'=>'', 'title'=>$sms_title, 'image'=>$sms_file ) , smsinfo_array($row_sms[$smskbn] , $arr_sms_msg['change']));
		}
		return $arr_send;
	}

// ----- JJC : 알림톡 패치 : 2018-06-14  --- 알림톡 폼 추가 -----


// -- 사용자 로그인 정보 보안 강화 처리 -- 2019-05-20 LCY
	// 사용자 세션로그인
	function UserLogin($_uid) {
		global $_SESSION, $_SERVER;
		$row_setup = info_basic();

		if(is_mobile() === true) mk_sess('AuthUserDiff', md5($row_setup['licenseNumber'].$_SERVER['HTTP_USER_AGENT'].$_uid.$_uid)); // 모바일에서는 아이피가 수시로 바뀌기 때문에 아이피 항목 제외
		else mk_sess('AuthUserDiff', md5($row_setup['licenseNumber'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_uid.$_uid));
		mk_cookie('AuthUserDiff', $_SESSION['AuthUserDiff']);
	}
	// 사용자 세션로그아웃
	function UserLogout() {
		@session_unset(); // 모든 세션변수를 언레지스터 시켜줌
		@samesiteCookie("auth_memberid","",time() - 100000,"/");
		@samesiteCookie("auth_memberid", "" , 0, "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
		@samesiteCookie("auth_memberid_sess","",time() - 100000,"/");
		@samesiteCookie("auth_memberid_sess", "" , 0, "/" , "." . str_replace("www." , "" , $_SERVER[HTTP_HOST]));
		mk_cookie('AuthUserDiff', '', time() - 100000);
	}
	// 사용자 로그인체크
	function UserLoginCheck($_mode = 'move') {
		global $_SESSION, $_SERVER;

		$row_setup = info_basic();
		$AuthDiffCookie = view_cookie('AuthUserDiff');


		if(is_mobile() === true) $AuthDiff[] = md5($row_setup['licenseNumber'].$_SERVER['HTTP_USER_AGENT'].$_COOKIE['auth_memberid'].$_COOKIE['auth_memberid']); // 모바일에서는 아이피가 수시로 바뀌기 때문에 아이피 항목 제외
		else $AuthDiff[] = md5($row_setup['licenseNumber'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_COOKIE['auth_memberid'].$_COOKIE['auth_memberid']);
		$AuthDiff[] = $_SESSION['AuthUserDiff'];
		$AuthDiff[] = ($AuthDiffCookie?$AuthDiffCookie:'0');
		$AuthDiff = @array_flip($AuthDiff);

		if( count($AuthDiff) === 1 && isset($AuthDiffCookie)) { return true; }
		else {
			if($_mode == 'move' && !preg_match('/member.login.pro.php/i', $_SERVER['REQUEST_URI'])) {
				UserLogout();
				die("<script>alert('로그인 세션이 만료되었습니다.'); top.location.href=('/pages/member.login.pro.php?_mode=logout')</script>");
			}
			return false;
		}

	}
// -- 사용자 로그인 정보 보안 강화 처리 -- 2019-05-20 LCY


	// -- 웹 취약점 보완 패치 -- 2019-09-16 {
    // JJC : 2019-09-03 : XSS filter 함수 ----- 게시판 저장 전 content 변경
    if( function_exists('RemoveXSS') == false){
        function RemoveXSS($val) {

            // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
            // this prevents some character re-spacing such as <java\0script>
            // note that you have to handle splits with \n, \r, and \t later since they *are*
            // allowed in some inputs
            $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

            // straight replacements, the user should never need these since they're normal characters
            // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&
            // #X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>

            $search = 'abcdefghijklmnopqrstuvwxyz';
            $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $search .= '1234567890!@#$%^&*()';
            $search .= '~`";:?+/={}[]-_|\'\\';
            for ($i = 0; $i < strlen($search); $i++) {
                // ;? matches the ;, which is optional
                // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

                // &#x0040 @ search for the hex values
                $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val);
                // with a ;

                // &#00064 @ 0{0,7} matches '0' zero to seven times
                $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
            }

            // now the only remaining whitespace attacks are \t, \n, and \r
            $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
            $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
            $ra = array_merge($ra1, $ra2);

            $found = true; // keep replacing as long as the previous round replaced something
            while ($found == true) {
                $val_before = $val;
                for ($i = 0; $i < sizeof($ra); $i++) {
                    $pattern = '/';
                    for ($j = 0; $j < strlen($ra[$i]); $j++) {
                        if ($j > 0) {
                            $pattern .= '(';
                            $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                            $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                            $pattern .= ')?';
                        }
                        $pattern .= $ra[$i][$j];
                     }
                     $pattern .= '/i';
                    $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
                    $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                    if ($val_before == $val) {
                        // no replacements were made, so exit the loop
                        $found = false;
                    }
                }
            }
            return $val;
        }
    }

    // -- 웹 취약점 보완 패치 -- 2019-09-16 }