<?
############################################################
## Onedaynet RSS 데이터정보 : Ver 0.1-beta-
## Create by Tindevil@nate.com
## BetaVersion
############################################################
## 자유로운 편집 및 사용이 가능합니다. 제작자주석삭제는 허락하지 않습니다.
############################################################

// 설정파일 불러오기 
if(@file_exists("../include/inc.php")) {
    @include_once("../include/inc.php");   
}
else {
    @include_once("../include/inc.php");   
}
// 설정파일 불러오기

##메세지박스를 표시하는 스크립트
function msgbox($data) {
    echo "<script>alert($data);</script>";
}

function getScalar($addonquery) {
    $addonResult = mysql_query($addonquery);
    $addonRecord = mysql_fetch_array($addonResult);
    return $addonRecord[0];
}
function getRow($addonquery) {
    $addonResult = mysql_query($addonquery);
    $addonRecord = mysql_fetch_array($addonResult);
    return $addonRecord;
}
function getRowCount($addonquery) {
    $addonResult = @mysql_query($addonquery);
   return @mysql_num_rows($addonResult);
}
function getRows($addonquery) {
    $addonResult = mysql_query($addonquery);
    return $addonResult;
}

?>