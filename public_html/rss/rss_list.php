<!-- style sheet -->
<style type="text/css">
body
{
background-color: #fff;
color: #585858;
font-size: 9pt;
font-family: "굴림", helvetica, sans-serif;
}
li{list-style:none}
div.subject
{
	border-top: solid 1px #CCC;
	border-bottom: solid 1px #CCC;
	width: 100%;
	height:40px;
	background-color:#EDECE9;
	font-weight: bold;
}
div.content
{
	border-top: solid 1px #CCC;
	border-bottom: solid 1px #CCC;
	width: 100%;
	height:40px;
	background-color:#ffffff;
	margin-top : 0.3em;
	padding-top: 0.5em;
}
*
{
margin: 0.0em;
padding: 0.2em;
font-size:12px;
}
a
{
text-decoration: underline;
color: #F16C00;
}
a:hover
{
text-decoration: none;
}
p
{
line-height: 0.1em;
}

#title ul
{
list-style: none;
}

#title li
{
float: left;
}

#menu ul
{
list-style: none;
}

#menu li
{
display:inline
}

#menu li.site
{
	width:160px;
	text-align:center;
	padding-top:8px;
}
#menu li.link
{
	padding:8px 0px 0px 0px;
	/*background-color:#3CC;*/
}
#menu li.ver
{
	width:80px;
	text-align:center;
	padding-top:8px;
	/*background-color:#3CC;*/
}
#menu li.preview
{
	width:87px;
	padding-top:8px;
	text-align:center;
}
#menu li a.link
{
	padding-top:8px;
/*display: block;
padding: 0.1em 0.0em 1.2em 0.1em;
background: #fff url('') repeat-x;
border: solid 1px #fff;
color: #616161;
font-weight: bold;
text-transform: lowercase;
text-decoration: none;
*/
}

#menu li a.active
{
	/*display: block;
	background: #FF790B url('') repeat-x;
	color: #fff;
	border: solid 1px #DB7623;*/
}
img
{
padding: 0px;
border:none;
}


</style>

<!-- 제목표시줄 -->
<div class="subject">
  <ul id="menu" >
	<li class="site">소셜메타사이트</li>
    <li class="ver">상태</li>
    <li class="preview">미리보기</li>
    <li class="link">주소(URL)</li>
  </ul>    
</div>
<p>

<!-- 사이트목록 -->
<?
	//RSS정보파일 XML리딩
	$xml = simplexml_load_file("http://www.onedaynet.co.kr/rss/support.php");
    for ($i = 0; $i < count($xml->item); $i++) 
    {
        $title           = "";
        $homepage           = "";
        $link = "";
        $Serverversion = "";
        $file = "";
        $title           = $xml->item[$i]->title;				//제목
        $homepage        = $xml->item[$i]->homepage;            //홈페이지
        $Serverversion   = $xml->item[$i]->version;             //버젼
        $file            = $xml->item[$i]->file;                //파일명
        $link            = 'http://'.$_SERVER[HTTP_HOST].'/rss/'.$file;
 
		##파일이 존재하는지 확인한다.
		$preview= "<a href='http://www.onedaynet.co.kr/?Pid=u03b06' target='_blank'><img src='./images/btn_view.gif' ></a>";
		$version = "&nbsp;";
		if(file_exists($file)) {
//			$target = simplexml_load_file("http://$_SERVER[HTTP_HOST]/rss/$file");
//			$curversion = $target->fileversion;
            $target = implode("",@file($file));
            $target_ex1 = explode("<fileversion>" , $target);
            $target_ex2 = explode("</fileversion>" , $target_ex1[1]);
            $curversion = trim($target_ex2[0]);

			$preview = "<a href='$link' target='_blank'><img src='./images/btn_view.gif' ></a>";
			if($curversion*1 < $Serverversion*1) {
				$version = "<font color='red'>구버젼</font>";
			} else {
				$version = "<font color='blue'>최신버젼</font>";
			}
		}	//END IF
?>
        <!-- 표시되는 목록 -->
        <div class="content">
          <ul id="menu" >
            <li class="site">
            	<a href="<?=$homepage?>" class="active" target="_blank"><?=$title?></a>
            </li>
            <li class="ver"><?=$version?></li>
            <li class=""><?=$preview?></li>
            <li class="link"><?=$link?></li>
          </ul>    
        </div>
<?             
    }	//END FOR   
?>
