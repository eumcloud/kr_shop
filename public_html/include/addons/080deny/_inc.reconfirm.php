<?php # /totalAdmin/_member.list.php 에서 실행
    // ---------------------------- 수신거부 고객을 포함하여 발송시 재확인 ----------------------------
?>
<style type="text/css">
    /* ●●●●●●●●●● 레이어팝업 */
    .cm_ly_pop_tp {border:3px solid #2c2f34; border-radius:10px; overflow:hidden; background: #2c2f34; box-shadow:0 0 8px rgba(0,0,0,0.3);}
    /* 기본형 */
    .cm_ly_pop_tp .title_box {padding:15px 20px; color:#fff; font-size:18px; position:relative; background: #2c2f34; font-weight:600}
    .cm_ly_pop_tp .btn_close {position:absolute; top:50%; right:0; width:21px; height:21px; margin:-11px 20px 0 0; background:transparent url('/pages/images/cm_images/member_pop_close.gif') no-repeat; }
    .cm_ly_pop_tp .inner_box {overflow:hidden; padding:0px; background:#fff;}
</style>
<div class="cm_ly_pop_tp sms_chk_again_page" style="display:none;">

    <!--  레이어팝업 공통타이틀 영역 -->
    <div class="title_box">SMS 스팸방지 재확인<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>

    <!-- 하얀색박스공간 -->
    <div class="inner_box">

        <div class="mailing_wrap" style="background:#cbd3d5; padding:30px">
            <div style="width:600px; margin:0 auto; background:#fff;">


                <div style="background:#f1f1f1; color:#666; font-family:'나눔고딕','돋움'; font-size:17px; text-align:center; line-height:1.5; padding:30px 20px; letter-spacing:-1px; border-bottom:1px solid #ddd">
                    <strong style="font-family:'나눔고딕','돋움'; color:#000; font-weight:600">SMS 스팸방지 재확인</strong><br />
                    <div class=""  style="text-align: center; margin:10px 0; ">
                        <p> - 발송되는 내용에 광고성, 또는 이벤트성 문구가 삽입되어있는지 확인해 주세요.<br>
                        <span style='color:#f54;'>(새해인사/생일축하/기념일/축하문자/안부인사 등등)</span></p>
                        <p> - 주문관련 발송이라 하더라도 발송내용에는 광고성 또는 이벤트성 문구가 삽입되어선 안됩니다. </p>
                        <p> - 야간의 경우 21시 부터 다음날 8시 전까지 는 별도의 고객 수신동의 가 있어야 합니다. </p>
                    </div>

                    <span class="_type_deny"  style="display:none;font-size: 13px; color:blue;">
                    발송대상자 중 수신거부 회원이[<strong class='_deny_cnt'></strong>명] 포함되어 있습니다.<br> 
                    수신거부 회원을 제외하고 발송하시겠습니까?<br> <br> 
                    </span>
                    <div class='bottom_btn_area _type_deny'>
                        <div class='btn_line_up_center'>
                            <span class='shop_btn_pack btn_input_red'><input type='button' onclick="sms_chk_send('deny')"  class='input_large' value='제외 발송'></span>
                            <span class='shop_btn_pack'><span class='blank_3 '></span></span>
                            <span class='shop_btn_pack btn_input_white'><input type='button' onclick="sms_chk_send('allow')"  class='input_large' value='포함 발송'></span>
                            <span class='shop_btn_pack'><span class='blank_3'></span></span>
                            <span class='shop_btn_pack btn_input_gray'><input type='button'  class='input_large close' value='발송 취소'></span>
                        </div>
                    </div>

                    <div class='bottom_btn_area _type_allow'>
                        <div class='btn_line_up_center'>
                            <span class='shop_btn_pack btn_input_white'><input type='button' onclick="sms_chk_send('allow')"  class='input_large' value='발송'></span>
                            <span class='shop_btn_pack'><span class='blank_3'></span></span>
                            <span class='shop_btn_pack btn_input_gray'><input type='button'  class='input_large close' value='발송 취소'></span>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

<script>
    function sms_chk_again_view(_deny_cnt){
        if(_deny_cnt > 0){ // 제외회원이 있을경우
            $('._type_deny').show();
            $('._type_allow').hide();
            $('._deny_cnt').text(_deny_cnt);
        }else{ // 없을경우 :: 일반발송
            $('._type_deny').hide();
            $('._type_allow').show();
        }
        $('.sms_chk_again_page').lightbox_me({centered: true, closeEsc: true,onLoad: function() { }});
    }

    // 타입정의 :: dney :: 제외발송, allow :: 포함 및 일반발송 
    function sms_chk_send(_type)
    {

        if( $("input[name=_mode]").val() == 'select'){ // 선택이라면
            _mode = 'sms_chk_again_select';
        }else{
            _mode = 'sms_chk_again_search';
        }

        if(_type  == 'deny'){       // 제외처리, 포함 X 처리 일 시
            $.ajax({
                url: "/include/addons/080deny/_inc.reconfirm.pro.php",
                type: "POST",
                dataType:'json',
                data: "_mode="+_mode+"&_action=send&_type=deny&pass_var=" + $("form[name=frm]").serialize(),
                success: function(data){

                    if(_mode == 'sms_chk_again_select'){ // 선택회원 발송이라면  
                        if(data.rst == 'success'){ // 성공이라면

                            for(i=0;i<data.deny_arr.length; i++){ // 제외항목 체크 해제
                                $('.class_id[value="'+data.deny_arr[i]+'"]').prop('checked',false);
                            }

                            document.frm.submit();
                            return false;                           
                        }else{
                            alert(data.msg);
                            return false;
                        }

                    }else{ // 검색회원 발송
                            if(data.rst == 'success'){
                                $("input[name=_search_que]").val(data._search_que); // 검색쿼리를 갱신
                                $("input[name=_search_dque]").val(data._search_dque); // 검색쿼리를 갱신
                                document.frm.submit();
                                return false;
                            }else{ // 검색회원이 없을 시
                                alert(data.msg);
                                return false;
                            }
                    }

                }
            });
        }else{
                document.frm.submit();
        }

    }

</script>
<?php
    // ---------------------------- 수신거부 고객을 포함하여 발송시 재확인 ----------------------------
?>