<?php
include_once("inc.php");

// string 만들기
switch( $pass_mode ){

    // 1차 , 2차 옵션 삭제
    case "3depth_del":
    case "2depth_del":
    case "1depth_del":

        if(in_array($pass_mode , array("1depth_del" , "2depth_del"))) {
            // 삭제전 하위정보 확인
            $ique = " select count(*) from odtProductOption where oto_pcode='{$pass_code}' and find_in_set('{$pass_uid}' , oto_parent) > 0 and oto_depth <= '". (rm_str($pass_mode)+1) ."' "; // 1차 정보
            $ires = mysql_query($ique);
            if(mysql_result($ires,0,0) > 0) {
                echo "is_subcategory"; // 하위 카테고리가 있음 표시
                exit;
            }
        }

        // 순번재조정
        $r = _MQ(" select * from odtProductOption where oto_uid='" . $pass_uid . "' ");
        _MQ_noreturn(" update odtProductOption set oto_sort=oto_sort-1  where oto_pcode='" . $r['oto_pcode'] . "' and oto_depth='". $r['oto_depth'] ."' and oto_parent='" . $r['oto_uid'] . "' and oto_sort > '". $r['oto_sort'] ."' ");

        // 삭제
        mysql_query("delete from odtProductOption where oto_uid='{$pass_uid}'");
    break;





    // 3차 옵션 추가
    case "3depth_add":

        $ique = " select * from odtProductOption where oto_pcode='{$pass_code}' and oto_uid='{$pass_uid}' "; // 2차 정보
        $ires = mysql_query($ique);
        $ir = mysql_fetch_assoc($ires);

        // 순번추출
        $r = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='3' and oto_parent='" . $ir['oto_parent'].",".$ir['oto_uid'] . "' ");
        $max_sort = $r[max_sort] + 1;

        // 항목추가 - 3차
        mysql_query("
            insert odtProductOption set
                oto_pcode='{$pass_code}',
                oto_poptionname='',
                oto_depth='3',
                oto_parent='{$ir['oto_parent']},{$ir['oto_uid']}',
                oto_sort='". $max_sort ."'
        ");
        break;

    // 2차 옵션 추가
    case "2depth_add":

        $ique = " select * from odtProductOption where oto_pcode='{$pass_code}' and oto_uid='{$pass_uid}' "; // 1차 정보
        $ires = mysql_query($ique);
        $ir = mysql_fetch_assoc($ires);

        // 순번추출
        $r = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='2' and oto_parent='" . $ir['oto_uid'] . "' ");
        $max_sort = $r['max_sort'] + 1;

        // 항목추가 - 2차
        mysql_query("
            insert odtProductOption set
                oto_pcode='{$pass_code}',
                oto_poptionname='',
                oto_depth='2',
                oto_parent='{$ir['oto_uid']}',
                oto_sort='". $max_sort ."'
        ");
        $uid_2depth = mysql_insert_id($connect);

        // 순번추출 - 3차
        $r3 = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='3' and find_in_set('" . $uid_2depth . "' , oto_parent) > 0 ");
        $max_sort3 = $r3['max_sort'] + 1;

        // 항목추가 - 3차
        mysql_query("
            insert odtProductOption set
                oto_pcode='{$pass_code}',
                oto_poptionname='',
                oto_depth='3',
                oto_parent='{$ir['oto_uid']},{$uid_2depth}',
                oto_sort='". $max_sort3 ."'
        ");
     break;


    // 1차 옵션 추가
    case "1depth_add":


        // 순번추출 - 1차
        $r = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='1' ");
        $max_sort = $r['max_sort'] + 1;

        // 항목추가 - 1차
        _MQ_noreturn("
            insert odtProductOption set
                oto_pcode='{$pass_code}',
                oto_poptionname='',
                oto_depth='1',
                oto_sort='". $max_sort ."'
        ");
        $uid_1depth = mysql_insert_id();

        // 순번추출 - 2차
        $r2 = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='2' and oto_parent='" . $uid_1depth . "' ");
        $max_sort2 = $r2['max_sort'] + 1;

        // 항목추가 - 2차
        _MQ_noreturn("
            insert odtProductOption set
                oto_pcode='{$pass_code}',
                oto_poptionname='',
                oto_depth='2',
                oto_parent='{$uid_1depth}',
                oto_sort='". $max_sort2 ."'
        ");
        $uid_2depth = mysql_insert_id();

        // 순번추출 - 3차
        $r3 = _MQ(" select ifnull(max(oto_sort),0) as max_sort from odtProductOption where oto_pcode='" . $pass_code . "' and oto_depth='3' and find_in_set('" . $uid_2depth . "' , oto_parent) > 0 ");
        $max_sort3 = $r3['max_sort'] + 1;

        // 항목추가 - 3차
        _MQ_noreturn("
            insert odtProductOption set
                oto_pcode='{$pass_code}',
                oto_poptionname='',
                oto_depth='3',
                oto_parent='{$uid_1depth},{$uid_2depth}',
                oto_sort='". $max_sort3 ."'
        ");
    break;
}

//1차 추출
$que = " select * from odtProductOption where oto_pcode='{$pass_code}' and oto_depth='1' order by oto_sort asc , oto_uid asc ";
$res = _MQ_assoc($que);
if(sizeof($res) <= 0) die('<!-- 내용없을경우 모두공통 --><div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 옵션정보가 없습니다.</div></div><!-- // 내용없을경우 모두공통 -->');
?>

<div class="option_data_box">
    <form action="_product_option.pro.php" name="frm_option" id="frm_option" target="common_frame" method="post">
        <input type="hidden" name="pass_code" value="<?php echo $pass_code; ?>">
        <input type="hidden" name="pass_type" value="">
        <input type="hidden" name="pass_depth" value="">
        <input type="hidden" name="pass_uid" value="">

        <ul>
            <?php
            foreach($res as $k=>$r) {
                $que2 = " select * from odtProductOption where oto_pcode='{$pass_code}' and oto_depth='2' and oto_parent='{$r['oto_uid']}' order by oto_sort asc , oto_uid asc ";
                $res2 = _MQ_assoc($que2);
            ?>
                <?php /* 1차 옵션 { */ ?>
                <li class="depth_1 if_nextdepth<?=$r['oto_view'] == 'Y'?null:' if_option_hide';?>">
                    <div class="opt">
                        <div class="txt">1차 옵션</div>
                        <span class="shop_btn_pack"><a href="javascript:category_apply('2depth_add', '<?=$r['oto_uid']?>');" class="small red" title="" >2 차 옵션추가</a></span>
                    </div>

                    <div class="value">
                        <!-- 이름입력 -->
                        <span class="wrapping_name">
                            <span class="txt_box">1차 옵션명</span>
                            <input type="text" name="oto_info[<?=$r['oto_uid']?>][oto_poptionname]" class="input_design" placeholder="1차 옵션명입력" value="<?=$r['oto_poptionname']?>" />
                        </span>
                        <!-- 순서변경 버튼 -->
                        <span class="btn_updown">
                            <span class="shop_btn_pack"><a href="javascript:f_sort('U' , '1', '<?=$r['oto_uid']?>' );" class="small white" title="위로" ><span class="shapeup"></span></a></span>
                            <span class="shop_btn_pack"><a href="javascript:f_sort('D' , '1', '<?=$r['oto_uid']?>' );" class="small white" title="아래로" ><span class="shapedw"></span></a></span>
                        </span>
                        <!-- 삭제버튼 -->
                        <span class="btn_delete"><span class="shop_btn_pack"><a href="javascript:category_apply('1depth_del', '<?=$r['oto_uid']?>');" class="small gray" title="옵션을 삭제합니다." >삭제</a></span></span>
                        <!-- 끼워넣기버튼 -->
                        <span class="btn_add"><span class="shop_btn_pack"><a href="javascript:f_insert('1', '<?=$r['oto_uid']?>');" class="small white" title="바로 아래로 옵션을 추가합니다.">끼워넣기</a></span></span>
                        <!-- 숨기기체크 -> 숨김시 li. 추가 -->
                        <label class="btn_hide" title="<?=$r['oto_view'] == 'Y' ? "옵션 숨기기" : '옵션 보이기';?>"><input type="checkbox" class="btn_hide_input" name="oto_info[<?=$r['oto_uid']?>][oto_view]" value="1" <?=$r['oto_view'] == 'Y' ? "checked" : NULL;?> /></label>
                    </div>
                </li>
                <?php /* } 1차 옵션 */ ?>

                    <?php /* 2차 옵션 { */ ?>
                    <?php
                    foreach($res2 as $k2=>$r2) {

                        //3차 추출
                    $que3 = " select * from odtProductOption where oto_pcode='{$pass_code}' and oto_depth='3' and oto_parent='$r[oto_uid],$r2[oto_uid]' order by oto_sort asc , oto_uid asc ";
                    $res3 = _MQ_assoc($que3);
                    ?>
                    <li class="depth_2 if_nextdepth<?=$r2['oto_view'] == 'Y'?null:' if_option_hide';?>">
                        <!-- 순서항목 타이틀 -->
                        <div class="opt">
                            <div class="txt">2차 옵션</div>
                            <span class="shop_btn_pack"><a href="javascript:category_apply('3depth_add' , '<?=$r2['oto_uid']?>');" class="small blue" title="" >3 차 옵션추가</a></span>
                        </div>
                        
                        <!-- 값입력전체박스 -->
                        <div class="value">
                            <!-- 이름입력 -->
                            <span class="wrapping_name">
                                <span class="txt_box">2차 옵션명</span>
                                <input type="text" name="oto_info[<?=$r2['oto_uid']?>][oto_poptionname]" class="input_design" value="<?=$r2['oto_poptionname']?>" placeholder="1차 옵션명입력" />
                            </span>
                            <!-- 순서변경 버튼 -->
                            <span class="btn_updown">
                                <span class="shop_btn_pack"><a href="javascript:f_sort('U' , '2', '<?=$r2['oto_uid']?>' );" class="small white" title="위로" ><span class="shapeup"></span></a></span>
                                <span class="shop_btn_pack"><a href="javascript:f_sort('D' , '2', '<?=$r2['oto_uid']?>' );" class="small white" title="아래로" ><span class="shapedw"></span></a></span>
                            </span>
                            <!-- 삭제버튼 -->
                            <span class="btn_delete"><span class="shop_btn_pack"><a href="javascript:category_apply('2depth_del' , '<?=$r2['oto_uid']?>');" class="small gray" title="해당 옵션을 삭제합니다." >삭제</a></span></span>
                            <!-- 끼워넣기버튼 -->
                            <span class="btn_add"><span class="shop_btn_pack"><a href="javascript:f_insert('2', '<?=$r2['oto_uid']?>');" class="small white" title="바로 아래로 옵션을 추가합니다." >끼워넣기</a></span></span>
                            <!-- 숨기기체크 -->
                            <label class="btn_hide" title="<?=$r2['oto_view'] == 'Y' ? "옵션 숨기기" : '옵션 보이기';?>"><input type="checkbox" class="btn_hide_input" name="oto_info[<?=$r2['oto_uid']?>][oto_view]" value="1" <?=$r2['oto_view'] == 'Y'? "checked" : NULL;?> /></label>
                        </div>
                    </li>

                        <?php /* 3차 옵션 { */ ?>
                        <?php
                        foreach($res3 as $k3=>$r3) {
                            if($r3['oto_poptionname'] == "" || !$r3['oto_poptionname'])  $save_chk ++;
                        ?>
                        <li class="depth_3<?=$r3['oto_view'] == 'Y'?null:' if_option_hide';?>">
                            <!-- 순서항목 타이틀 -->
                            <div class="opt">
                                <div class="txt">3차 옵션</div>
                            </div>
                            
                            <!-- 값입력전체박스 -->
                            <div class="value">
                                <!-- 이름입력 -->
                                <span class="wrapping_name">
                                    <span class="txt_box">3차 옵션명</span>
                                    <input type="text" name="oto_info[<?=$r3['oto_uid']?>][oto_poptionname]" class="input_design" value="<?=$r3['oto_poptionname']?>" placeholder="1차 옵션명입력" />
                                </span>
                                <!-- 값 입력 -->
                                <span class="wrapping_num">
                                    <span class="txt_box">공급가(추가)</span>
                                    <input type="text" name="oto_info[<?=$r3['oto_uid']?>][oto_poptionpurprice]" class="input_design input_price" value="<?=$r3['oto_poptionpurprice']?>" placeholder="0" />
                                </span>
                                <span class="wrapping_num">                 
                                    <span class="txt_box">판매가(추가)</span>
                                    <input type="text" name="oto_info[<?=$r3['oto_uid']?>][oto_poptionprice]" class="input_design input_price" value="<?=$r3['oto_poptionprice']?>" placeholder="0" />
                                </span>
                                <span class="wrapping_num">
                                    <span class="txt_box">수량(재고)</span>
                                    <input type="text" name="oto_info[<?=$r3['oto_uid']?>][oto_cnt]" class="input_design input_number" value="<?=$r3['oto_cnt']?>" placeholder="0" />
                                </span>
                                <span class="wrapping_num">
                                    <span class="txt_box">판매</span>
                                    <input type="text" name="" class="input_design input_number" placeholder="0" readonly="readonly" value="<?=$r3['oto_salecnt']?>" disabled />
                                </span>
                                <!-- 순서변경 버튼 -->
                                <span class="btn_updown">
                                    <span class="shop_btn_pack"><a href="javascript:f_sort('U' , '3', '<?=$r3['oto_uid']?>' );" class="small white" title="위로" ><span class="shapeup"></span></a></span>
                                    <span class="shop_btn_pack"><a href="javascript:f_sort('D' , '3', '<?=$r3['oto_uid']?>' );" class="small white" title="아래로" ><span class="shapedw"></span></a></span>
                                </span>
                                <!-- 삭제버튼 -->
                                <span class="btn_delete"><span class="shop_btn_pack"><a href="javascript:category_apply('3depth_del' , '<?=$r3['oto_uid']?>');" class="small gray" title="해당 옵션을 삭제합니다." >삭제</a></span></span>
                                <!-- 끼워넣기버튼 -->
                                <span class="btn_add"><span class="shop_btn_pack"><a href="javascript:f_insert('3', '<?=$r3['oto_uid']?>');" class="small white" title="바로 아래로 옵션을 추가합니다." >끼워넣기</a></span></span>
                                <!-- 숨기기체크 -->
                                <label class="btn_hide" title="<?=$r3['oto_view'] == 'Y' ? "옵션 숨기기" : '옵션 보이기';?>"><input type="checkbox" class="btn_hide_input" name="oto_info[<?=$r3['oto_uid']?>][oto_view]" value="1" <?=$r3['oto_view'] == 'Y' ? "checked" : NULL;?> /></label>
                            </div>
                        </li>
                        <?php } ?>
                        <?php /* } 3차 옵션 */ ?>
                    <?php /* } 2차 옵션 */ ?>
                    <?php } ?>
            <?php } ?>
        </ul>
        <input type="hidden" name="no_save_num" value="<?php echo $save_chk; ?>">
    </form>
</div>

<script>
// 옵션 숨기기 효과 {
$(function() {

    $('.btn_hide_input').on('click', function() {

        var checked = $(this).is(':checked');
        if(checked === true) {

            $(this).closest('li').removeClass('if_option_hide');
            $(this).closest('label').attr('title', '옵션 숨기기');
            $('.ui-tooltip-content').html('옵션 숨기기');
        }
        else {

            $(this).closest('li').addClass('if_option_hide');
            $(this).closest('label').attr('title', '옵션 보이기');
            $('.ui-tooltip-content').html('옵션 보이기');
        }

        category_apply();
    });
});
// } 옵션 숨기기 효과
</script>