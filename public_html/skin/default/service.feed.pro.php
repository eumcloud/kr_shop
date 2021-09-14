<?
if( @file_exists("../include/config_database.php") ) {
    $_path_str = "..";
}
else {
    $_path_str = ".";
}
include_once( dirname(__FILE__) . "/../../include/inc.php");

// 필드 추가 -------------
    $arr_row = array();
    $res = mysql_query(" desc feedTable ");
    while( $r = mysql_fetch_assoc($res) ){
        $arr_row[$r[Field]] = $r[Type];
    }

    // 해당 필드 없을 경우 필드 자동추가
    if( !in_array("ft_area1" , array_keys($arr_row)) ){
        mysql_query(" ALTER TABLE feedTable ADD ft_area1 VARCHAR( 100 ) NULL COMMENT '구독하기 1차 카테고리', ADD INDEX ( ft_area1 ) ") or die(mysql_error());
    }
// 필드 추가 -------------


/*

$ft_area1 = $_POST[_area1];


if( $_POST[_mode] == "blind" ) {
    $ft_email = $_POST[feedEmail];
}
else {
    if($_POST[emailCheck]) {
        $ft_email = $_POST[feedEmail];
    }

    if($_POST[smsCheck]) {
        $ft_sms = $_POST[feedSms];
    }
}
*/

?>

<?


if (!$_POST[email_read]) { echo "<script>alert('이메일 주소를 입력 해주세요')</script>"; return false; }

$ft_email = $_POST[email_read];

//print_R($_REQUEST); exit;


$ique = " select count(*) from feedTable where ft_email = '".$ft_email."'";
$ires = mysql_query($ique);
if( mysql_result($ires,0,0) == 0 )
{

    $que = " insert into feedTable set ft_email = '".$ft_email."' , ft_regidate=now() ";
    $res = mysql_query($que);
    if($res) {

        // 쿠키 만료일 1개월로 잡음
        //samesiteCookie("AuthAreaCheckArea1" , $ft_area1 , time() + 3600 * 24 * 30 , "/" , str_replace("www." , "" , $_SERVER[HTTP_HOST]));

        error_alt('구독신청이 완료되었습니다.');
        exit;

    }

}
else 
{
    error_alt('이미 등록된 이메일 입니다.');
    exit;
}





/* 
create table feedTable (
ft_idx int auto_increment primary key,
ft_email varchar(100) not null default '',
ft_sms varchar(100) not null default '',
ft_regidate datetime not null);
*/
?>