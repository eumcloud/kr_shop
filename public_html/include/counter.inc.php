<?
	$_COUNTER_CHECKER = parse_url($_SERVER["HTTP_REFERER"]);
	
	// 이전페이지가 본 사이트가 아니어야 함
//	if(!preg_match("/".$_SERVER["HTTP_HOST"]."/i" , $_COUNTER_CHECKER["host"])) {
	if(1) {
		// 공통파일 불러오기
		if(!$ARR_LOC_PER_PRICE ) {
			if( @file_exists("../include/config_database.php") ) {
				$_path_str = "..";
			}
			else {
				$_path_str = ".";
			}
			include_once( $_path_str . "/include/inc.php");
		}

		/* 브라우저 및 운영체제 추출 2015-11-19 LMH */
		$user_agent     =   $_SERVER['HTTP_USER_AGENT'];

		function getOS() { 
			global $user_agent;
			$os_platform    =   "Unknown OS Platform";
			$os_array       =   array(
									'/windows nt 6.2/i'		=> 'Windows 8',
									'/windows nt 6.1/i'		=> 'Windows 7',
									'/windows nt 6.0/i'		=> 'Windows Vista',
									'/windows nt 5.2/i'		=> 'Windows Server 2003/XP x64',
									'/windows nt 5.1/i'		=> 'Windows XP',
									'/windows xp/i'			=> 'Windows XP',
									'/windows nt 5.0/i'		=> 'Windows 2000',
									'/windows me/i'			=> 'Windows ME',
									'/win98/i'				=> 'Windows 98',
									'/win95/i'				=> 'Windows 95',
									'/win16/i'				=> 'Windows 3.11',
									'/macintosh|mac os x/i'	=> 'Mac OS X',
									'/mac_powerpc/i'		=> 'Mac OS 9',
									'/linux/i'				=> 'Linux',
									'/ubuntu/i'				=> 'Ubuntu',
									'/iphone/i'				=> 'iPhone',
									'/ipod/i'				=> 'iPod',
									'/ipad/i'				=> 'iPad',
									'/android/i'			=> 'Android',
									'/blackberry/i'			=> 'BlackBerry',
									'/webos/i'				=> 'Mobile'
								);
			foreach($os_array as $regex => $value) { if(preg_match($regex, $user_agent)) { $os_platform = $value; } }
			return $os_platform;
		}

		function getBrowser() {
			global $user_agent;
			$browser = "Unknown Browser";
			if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false) { $browser = 'Internet Explorer'; $version = '11'; } 
			else if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 10.0') !== false) { $browser = 'Internet Explorer'; $version = '10'; }
			else if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.0') !== false) { $browser = 'Internet Explorer'; $version = '9'; }
			else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/4.0') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0') !== false) { $browser = 'Internet Explorer'; $version = '8'; }
			else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/4.0') == false && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0') !== false) { $browser = 'Internet Explorer'; $version = '7'; }
			else {
				$browser_array = array(
									'/mobile/i'		=> 'Handheld Browser',
									'/msie/i'		=> 'Internet Explorer',
									'/firefox/i'	=> 'Firefox',
									'/safari/i'		=> 'Safari',
									'/chrome/i'		=> 'Chrome',
									'/opera/i'		=> 'Opera',
									'/netscape/i'	=> 'Netscape',
									'/maxthon/i'	=> 'Maxthon',
									'/konqueror/i'	=> 'Konqueror',
									'/FxiOS/i'		=> 'Firefox'
								);

				foreach($browser_array as $regex => $value) { 
					if(preg_match($regex, $user_agent)) {
						$browser    =   $value;
						$known = array('Version', $value, 'other');
						$pattern = '#(?<browser>' . join('|', $known).')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
						if(!preg_match_all($pattern, $user_agent, $matches)) {}
						$i = count($matches['browser']);
						if($i != 1) {
							if(strripos($user_agent,"Version") < strripos($user_agent,$value)){ $version= $matches['version'][0]; }
							else { $version= $matches['version'][1]; }
						} else { $version= $matches['version'][0]; }
					}
				}
			}
			return $browser." ".$version;
		}

		require_once dirname(__FILE__).'/Mobile_Detect/Mobile_Detect.php';
		$detect = new Mobile_Detect;
		$user_os        =   getOS();
		$user_browser   =   getBrowser();
		$userAgent = $user_os."|||".$user_browser."|||".$_SERVER['HTTP_USER_AGENT'];
		/* 브라우저 및 운영체제 추출 2015-11-19 LMH */

		$counter_config_result = mysql_query("SELECT * FROM odtCounterConfig");
		$counter_config_row    = mysql_fetch_array($counter_config_result);
		
		$counter_result        = mysql_query("SELECT SUM(Visit_Num) FROM odtCounterData");
		$Total_Num             = mysql_result($counter_result,0,0);
		
		$CoIP            = $REMOTE_ADDR;
		$CoRoute         = $HTTP_REFERER;
		$CoKinds         = $HTTP_USER_AGENT;
		$CoKinds         = str_replace(")","",$CoKinds);
		$CoKindsDivision = explode(";",$CoKinds);
		$CoKinds_Browser = $CoKindsDivision[1];
		$CoKinds_OS      = $CoKindsDivision[2];
		$ToDay_Year      = date("Y");
		$ToDay_Month     = date("m");
		$ToDay_Day       = date("d");
		$ToDay_Hour      = date("H");
		$ToDay_Minute    = date("i");
		$ToDay_Second    = date("s");
		$ToDay_Week      = date("D");
		$ToDay_Time      = time();
		
		if($counter_config_row[Cookie_Use] == "A") {
			$Counter_ON = "Y";
			
			samesiteCookie("odtCounter_Term",0,0,"/");
		}

		if($counter_config_row[Cookie_Use] == "T") {
			$Cookie_Term = $counter_config_row[Cookie_Term];
			$temp = $ToDay_Time - $_COOKIE[odtCounter_Term];
			
			if($temp>$Cookie_Term) {
				$Counter_ON = "Y";
				
				samesiteCookie("odtCounter_Term",0,0,"/");
				samesiteCookie("odtCounter_Term",$ToDay_Time,$ToDay_Time+365*24*3600,"/");
			}
		}

		if($counter_config_row[Cookie_Use] == "O") {
			$temp1 = date('Y-m-d',$_COOKIE[odtCounter_Term2]);
			$temp2 = date('Y-m-d');
			
			if($temp1 != $temp2) {
				$Counter_ON = "Y";

	//			samesiteCookie("odtCounter_Term2",0,0,"/");
				samesiteCookie("odtCounter_Term2",$ToDay_Time,$ToDay_Time+100*24*3600,"/");
			}
		}

		if($counter_config_row[Admin_Check_Use] == "N" && $counter_config_row[Admin_IP] == $CoIP) $Counter_ON = "N";
		
		if($counter_config_row[Now_Connect_Use] == "Y") {
			$temp = $ToDay_Time-$counter_config_row[Now_Connect_Term];
			
			mysql_query("DELETE FROM odtCounterPerson WHERE Time < $temp");
			mysql_query("INSERT INTO odtCounterPerson (Connect_IP, Time) VALUES ('$CoIP', '$ToDay_Time')");
		}



		# 로봇인지 체크
		if( 
			preg_match("/googlebot|yahoo|naver/i" , strtolower($CoKinds_Browser)) 
			||
			preg_match("/googlebot|yahoo|naver/i" , strtolower($CoKinds_OS)) 
		) {
			$isRobot = true;
		} else {
			$isRobot = false;
		}
	/* 순수 아이피 체크는 같은 아이피 사용자들을 모두 제외하기 때문에 문제가 있음.*/
		### 오늘 이미 카운터된 아이피인지 체크
		//만약 처음이면 임시 테이블 삭제
		mysql_query("delete from odtCounterOnly where regi < '".date('Y-m-d')."'");
		// 오늘 이미 같은 아이피 방문이 있으면 로봇으로 처리.
		$isCnt = mysql_result(mysql_query("select count(*) from odtCounterOnly where ip = '".$CoIP."'"),0);
		if($isCnt < 1) {
			mysql_query("insert into odtCounterOnly set ip ='".$CoIP."', regi = '".date('Y-m-d')."'");
		} else {
			if($counter_config_row[Cookie_Use] == "O") {
				$isRobot = true;
			}
		}
	/* 순수 아이피 체크는 같은 아이피 사용자들을 모두 제외하기 때문에 문제가 있음. */

		# 카운터 차단 아이피
		$count_deny_ip = array("222.122.78.16","222.122.78.15","118.130.232.254","61.111.15");
		if(@array_search($CoIP,$count_deny_ip) == true) $isRobot = true;

		if($Counter_ON == "Y" && !$isRobot) {
			$Total_Num++;
			
			if($counter_config_row[Counter_Use] == "Y") {
				if($_POST[referer]) $CoRoute .= "&referer=".$_POST[referer];

				mysql_query("INSERT INTO odtCounter (Connect_IP, Time, Year, Month, Day, Hour, Week, OS, Browser, Connect_Route) VALUES ('$CoIP', '$ToDay_Time', '$ToDay_Year', '$ToDay_Month', '$ToDay_Day', '$ToDay_Hour', '$ToDay_Week', '$CoKinds_OS', '$CoKinds_Browser', '$CoRoute')");
				
				$data_query = "SELECT serialnum FROM odtCounterData WHERE Year = '$ToDay_Year' AND Month = '$ToDay_Month' AND Day = '$ToDay_Day'";
				$data_result = mysql_num_rows(mysql_query($data_query));
				
				if($data_result) {
					mysql_query("UPDATE odtCounterData SET Hour$ToDay_Hour = Hour$ToDay_Hour+1, Visit_Num = Hour00+Hour01+Hour02+Hour03+Hour04+Hour05+Hour06+Hour07+Hour08+Hour09+Hour10+Hour11+Hour12+Hour13+Hour14+Hour15+Hour16+Hour17+Hour18+Hour19+Hour20+Hour21+Hour22+Hour23 WHERE Year = '$ToDay_Year' AND Month = '$ToDay_Month' AND Day = '$ToDay_Day'");
				}
				else {
					mysql_query("INSERT INTO odtCounterData (Year, Month, Day, Hour$ToDay_Hour, Week, Visit_Num) VALUES ('$ToDay_Year', '$ToDay_Month', '$ToDay_Day', '1', '$ToDay_Week', '1')");
				}
				
				if($CoKinds_Browser      == " MSIE 9.0") $CoKinds_Browser = "MSIE 9.0";
				else if($CoKinds_Browser == " MSIE 8.0") $CoKinds_Browser = "MSIE 8.0";
				else if($CoKinds_Browser == " MSIE 7.0") $CoKinds_Browser = "MSIE 7.0";
				else if($CoKinds_Browser == " MSIE 6.0") $CoKinds_Browser = "MSIE 6.0";
				else if($CoKinds_Browser == " MSIE 5.5") $CoKinds_Browser = "MSIE 5.5";
				else if($CoKinds_Browser == " MSIE 5.01") $CoKinds_Browser = "MSIE 5.01";
				else if($CoKinds_Browser == " MSIE 5.0") $CoKinds_Browser = "MSIE 5.0";
				else if($CoKinds_Browser == " MSIE 4.0") $CoKinds_Browser = "MSIE 4.0";
				else if($CoKinds_Browser == " MSIE 6.0b") $CoKinds_Browser = "MSIE 6.0b";
				else $CoKinds_Browser = "";

				$CoKinds_Browser = $user_browser; // 2015-11-19 LMH
				
				$browser_query = "SELECT serialnum FROM odtCounterOSBrowser WHERE Kinds = 'B' AND Name = '$CoKinds_Browser'";
				$browser_result = mysql_num_rows(mysql_query($browser_query)); 
				
				if($browser_result) 
					mysql_query("UPDATE odtCounterOSBrowser SET Visit_Num = Visit_Num+1 WHERE Kinds = 'B' AND Name = '$CoKinds_Browser'");
				else mysql_query("INSERT INTO odtCounterOSBrowser (Name, Kinds, Visit_Num) VALUES ('$CoKinds_Browser', 'B', '1')");
				
				if($CoKinds_OS      == " Windows NT 6.1") $CoKinds_OS = "Windows 7";     
				else if($CoKinds_OS == " Windows NT 6.0") $CoKinds_OS = "Windows Vista"; 
				else if($CoKinds_OS == " Windows NT 5.1") $CoKinds_OS = "Windows XP";    
				else if($CoKinds_OS == " Windows NT 5.0") $CoKinds_OS = "Windows 2000";  
				else if($CoKinds_OS == " Windows 98") $CoKinds_OS = "Windows 98";        
				else if($CoKinds_OS == " Windows NT") $CoKinds_OS = "Windows NT";        
				else if($CoKinds_OS == " Windows 95") $CoKinds_OS = "Windows 95";        
				else if($CoKinds_OS == " Windows NT 4.0") $CoKinds_OS = "Windows NT 4.0";
				else if($CoKinds_OS == " Windows ME") $CoKinds_OS = "Windows ME";        
				else if($CoKinds_OS == " Windows 3.1") $CoKinds_OS = "Windows 3.1";      
				else $CoKinds_OS = "";

				$CoKinds_OS = $user_os; // 2015-11-19 LMH
				
				$osb_query = "SELECT serialnum FROM odtCounterOSBrowser WHERE Kinds = 'O' AND Name = '$CoKinds_OS'";
				$osb_result = mysql_num_rows(mysql_query($osb_query)); 
				
				if($osb_result) 
					mysql_query("UPDATE odtCounterOSBrowser SET Visit_Num = Visit_Num+1 WHERE Kinds = 'O' AND Name = '$CoKinds_OS'");
				else mysql_query("INSERT INTO odtCounterOSBrowser (Name, Kinds, Visit_Num) VALUES ('$CoKinds_OS', 'O', '1')");
				
				$route_query = "SELECT serialnum FROM odtCounterRoute WHERE Connect_Route = '$CoRoute'";
				$route_result = mysql_num_rows(mysql_query($route_query)); 
				
				if($route_result) 
					mysql_query("UPDATE odtCounterRoute SET Time = '$ToDay_Time', Visit_Num = Visit_Num+1 WHERE Connect_Route = '$CoRoute'");
				else mysql_query("INSERT INTO odtCounterRoute (Connect_Route, Time, Visit_Num) VALUES ('$CoRoute', '$ToDay_Time', '1')");
			}
		}

		mysql_query("UPDATE odtCounterConfig SET Total_Num = '$Total_Num' WHERE serialnum = 1");

	}
?>