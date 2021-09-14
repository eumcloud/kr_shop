<?php
// 관리자가 지정한 패스워드 변경일자 가져오는 부분. 악용방지를 위해 기간 재 검사 2015-10-22 lcy
member_chk(); // 로그인 체크 (비회원이 URL 접속 하는걸 방지하기위해)
$cpwd_ck_day = date('Y-m-d',$row_member[cpasswd_ck]); // 비밀번호 변경 갱신일을 날로 변경 
$cpwd_ck =  strtotime($cpwd_ck_day."  +  ".$row_setup[member_cpw_period]."  month"); // 관리자가 설정한 개월 수를 계산하여 초로계산

$cpwd_day = date('Y-m-d',$cpwd_ck); // 일 수로 변경

if($cpwd_day <= date("Y-m-d",time())){

    // 이페이지가 계속 뜨는것을 방지 하기 위해 7일 후로 임시 업데이트 

    $next_day = time();
    _MQ_noreturn("UPDATE odtMember SET cpasswd_ck='".$next_day."' WHERE id='".$row_member[id]."'");

?>
<div class="common_page common_only">


    <!-- ●●●●●●●●●● 비밀번호변경안내 -->
    <div class="cm_member_password">            


            <div class="top_txt">
                <dl>
                    <dd>회원님의 소중한 개인정보를 안전하게 보호하기 위한</dd>
                    <dt>비밀번호 변경을 안내 해드립니다.</dt>
                </dl>
                <div class="guide_txt">
                    <ul>
                        <li><!-- 관리자:사이트명 --><strong><?=$row_setup[site_name]?></strong>에서는 소중한 개인정보 보호를 위해 비밀번호 변경안내 정책이 시행되고 있습니다. </li>
                        <li>비밀번호를 변경하신 지 <!-- 관리자:설정개월 --><strong><?=$row_setup[member_cpw_period]?>개월</strong>이 지난 경우에 아래과 같이 변경안내를 드리고 있습니다.</li>
                        <li>"다음에 변경하기" 버튼을 눌러 변경을 연기하시면 다음에 다시 안내해 드립니다.</li>
                        <li>조금 불편하시더라도, <strong>지금 비밀번호를 변경하시면</strong> 더욱 안전한 웹사이트 이용이 가능합니다.</li>
                    </ul>
                </div>
            </div>


            <div class="cm_member_form">
            <form name="c_pw" id="c_pw" action="/pages/member.join.pro.php" target="common_frame" novalidate="novalidate" method="post">
            <input type="hidden" name="realCheck" value="1">
            <input type="hidden" name="nickCheck1" value="1">
            <input type="hidden" name="_mode" value="cpw">
                <div class="add_txt">
                    <span class="lineup">비밀번호를 변경하실 경우, <br><strong>영문, 숫자로 6자이상</strong> 입력해 주세요.</span>
                </div>
                <ul>
                    <li class="ess ">
                        <span class="opt">현재 비밀번호</span>
                        <div class="value">
                            <input type="password" name="_passwd" class="input_design">
                        </div>                  
                    </li>
                    <li class="ess ">
                        <span class="opt">새 비밀번호</span>
                        <div class="value">
                            <input type="password" name="_cpasswd" class="input_design">
                        </div>                  
                    </li>
                    <li class="ess ">
                        <span class="opt">새 비밀번호 확인</span>
                        <div class="value">
                            <input type="password" name="_recpasswd" class="input_design">
                        </div>                  
                    </li>
                </ul>
            </form> <!-- form end -->
            </div>


            <!-- 가운데정렬버튼 -->
            <div class="cm_bottom_button">
                <ul>
                    <li><span class="button_pack"><a onclick="form_c_pw()" title="비밀번호 지금 변경하기" class="btn_lg_color">지금 변경하기</a></span></li>
                    <li><span class="button_pack"><a  href="/?" title="비밀번호 다음에 변경하기" class="btn_lg_black">다음에 변경하기</a></span></li>
                </ul>
            </div>
            <!-- / 가운데정렬버튼 -->  


    </div>  
    <!-- / 비밀번호변경안내 -->


</div>

<script>
function form_c_pw() {
    $("#c_pw").submit();
}
$(document).ready(function(){
    $("#c_pw").validate({
        rules: {
            _cpasswd    : { required: ($("input[name=_cpasswd]").val()!="" ? true : false), minlength: 6 }

        },
        messages: {
            _cpasswd    : { required: "비밀번호를 입력하세요.",minlength: "비밀번호는 최소 6글자이상입니다." }
        }
    });
});  
</script>


<?php }else{

    error_loc_msg('/?',"이미 만료된 폐이지 입니다.");

}?>