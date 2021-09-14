<?php
// 필요한 설정파일 불러오기
include_once("../include/inc.php");

// 특수문자 변환
function specialchars_replace($str, $len=0) {
    if ($len) {
        $str = substr($str, 0, $len);
    }

    $str = preg_replace("/&/", "&amp;", $str);
    $str = preg_replace("/</", "&lt;", $str);
    $str = preg_replace("/>/", "&gt;", $str);
    return $str;
}

// 악성태그 변환
function bad_tag_convert($code) 
{
    return preg_replace("/\<([\/]?)(script|iframe)([^\>]*)\>/i", "&lt;$1$2$3&gt;", $code);
}

// 3.31
// HTML SYMBOL 변환
// &nbsp; &amp; &middot; 등을 정상으로 출력
function html_symbol($str)
{
    return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}


// TEXT 형식으로 변환
function get_text($str, $html=0)
{
    /* 3.22 막음 (HTML 체크 줄바꿈시 출력 오류때문)
    $source[] = "/  /";
    $target[] = " &nbsp;";
    */

    // 3.31
    // TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
    if ($html == 0) {
        $str = html_symbol($str);
    }

    $source[] = "/</";
    $target[] = "&lt;";
    $source[] = "/>/";
    $target[] = "&gt;";
    //$source[] = "/\"/";
    //$target[] = "&#034;";
    $source[] = "/\'/";
    $target[] = "&#039;";
    //$source[] = "/}/"; $target[] = "&#125;";
    if ($html) {
        $source[] = "/\n/";
        $target[] = "<br/>";
    }

    return preg_replace($source, $target, $str);
}


// way.co.kr 의 wayboard 참고
function url_auto_link($str)
{
    global $config;

    // 속도 향상 031011
    $str = preg_replace("/&lt;/", "\t_lt_\t", $str);
    $str = preg_replace("/&gt;/", "\t_gt_\t", $str);
    $str = preg_replace("/&amp;/", "&", $str);
    $str = preg_replace("/&quot;/", "\"", $str);
    $str = preg_replace("/&nbsp;/", "\t_nbsp_\t", $str);
    $str = preg_replace("/([^(http:\/\/)]|\(|^)(www\.[^[:space:]]+)/i", "\\1<A HREF=\"http://\\2\" TARGET='$config[cf_link_target]'>\\2</A>", $str);
    $str = preg_replace("/([^(HREF=\"?'?)|(SRC=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,]+)/i", "\\1<A HREF=\"\\2\" TARGET='$config[cf_link_target]'>\\2</A>", $str);
    // 이메일 정규표현식 수정 061004
    //$str = preg_replace("/(([a-z0-9_]|\-|\.)+@([^[:space:]]*)([[:alnum:]-]))/i", "<a href='mailto:\\1'>\\1</a>", $str);
    $str = preg_replace("/([0-9a-z]([-_\.]?[0-9a-z])*@[0-9a-z]([-_\.]?[0-9a-z])*\.[a-z]{2,4})/i", "<a href='mailto:\\1'>\\1</a>", $str);
    $str = preg_replace("/\t_nbsp_\t/", "&nbsp;" , $str);
    $str = preg_replace("/\t_lt_\t/", "&lt;", $str);
    $str = preg_replace("/\t_gt_\t/", "&gt;", $str);

    return $str;
}


// 내용을 변환
function conv_content($content, $html)
{
    if ($html)
    {
        $source = array();
        $target = array();

        $source[] = "//";
        $target[] = "";

        if ($html == 2) { // 자동 줄바꿈
            $source[] = "/\n/";
            $target[] = "<br/>";
        }

        // 테이블 태그의 갯수를 세어 테이블이 깨지지 않도록 한다.
        $table_begin_count = substr_count(strtolower($content), "<table");
        $table_end_count = substr_count(strtolower($content), "</table");
        for ($i=$table_end_count; $i<$table_begin_count; $i++)
        {
            $content .= "</table>";
        }

        $content = preg_replace($source, $target, $content);
        $content = bad_tag_convert($content);

        // XSS (Cross Site Script) 막기
        // 완벽한 XSS 방지는 없다.
        // 081022 : CSRF 방지
        //$content = preg_replace("/(on)(abort|blur|change|click|dblclick|dragdrop|error|focus|keydown|keypress|keyup|load|mousedown|mousemove|mouseout|mouseover|mouseup|mouseenter|mouseleave|move|reset|resize|select|submit|unload)/i", "$1<!-- XSS Filter -->$2", $content);
        //$content = preg_replace("/(on)([^\=]+)/i", "&#111;&#110;$2", $content);
        $content = preg_replace("/(on)([a-z]+)([^a-z]*)(\=)/i", "&#111;&#110;$2$3$4", $content);
        $content = preg_replace("/(dy)(nsrc)/i", "&#100;&#121;$2", $content);
        $content = preg_replace("/(lo)(wsrc)/i", "&#108;&#111;$2", $content);
        $content = preg_replace("/(sc)(ript)/i", "&#115;&#99;$2", $content);
        $content = preg_replace("/(ex)(pression)/i", "&#101&#120;$2", $content);
        /*
        $content = preg_replace("/\#/", "&#35;", $content);
        $content = preg_replace("/\</", "&lt;", $content);
        $content = preg_replace("/\>/", "&gt;", $content);
        $content = preg_replace("/\(/", "&#40;", $content);
        $content = preg_replace("/\)/", "&#41;", $content);
        */
    }
    else // text 이면
    {
        // & 처리 : &amp; &nbsp; 등의 코드를 정상 출력함
        $content = html_symbol($content);

        // 공백 처리
        //$content = preg_replace("/  /", "&nbsp; ", $content);
        $content = str_replace("  ", "&nbsp; ", $content);
        $content = str_replace("\n ", "\n&nbsp;", $content);

        $content = get_text($content, 1);

        $content = url_auto_link($content);
    }

    return $content;
}

function saleItem($cateCode)
{
    global $row_setup;
    $today = date('Y-m-d');
    $que = "select code from odtProduct where code = parent_code and cateCode = '".$cateCode."' and sale_date <= '${today}' order by sale_date desc limit 1";
    $res = mysql_query($que);
    return @mysql_result($res,0);
}

Header("Content-type: text/xml"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");   

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?>

<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
<title><?=$row_company[homepage_title]?></title>
<link><?="http://".$_SERVER[HTTP_HOST]?></link>
<description><?=$row_company[homepage_title]?> - RSS SERVICE</description>
<language>ko</language>

<?
// 상품목록추출 //////////////////////////////////////////////////////////////
$Query  = "select * from odtProduct where sale_date <= CURDATE() and sale_enddate >= CURDATE() and stock > 0 order by sale_date asc  ";
$Result = _MQ_assoc($Query);
foreach ($Result as $key => $row) {
?>

    <item>
    <title><?=specialchars_replace($proName)?></title>
    <link><?=htmlspecialchars("http://".$_SERVER[HTTP_HOST]."/?pn=product.view&pcode=".$code)?></link>
    <description><![CDATA[
    <img src='<?=$row[main_img]?>'><br><br>
    <?=$row[name]?>&nbsp;&nbsp;<span id='price'><?=number_format($row[price])?>원</span><br>
    <?=stripslashes($row[comment1])?><br>
    ]]>
    </description>
    <dc:date><?=date('r', strtotime($row[sale_date]))?></dc:date>
    </item>
<?
}
?>
</channel>
</rss>
