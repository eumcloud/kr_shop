<?php
# LDD010
include_once("inc.php");

// string 만들기
switch( $pass_mode ){
    
    // 1차 , 2차 옵션 삭제
    case "2depth_del":
    case "1depth_del":

        // 삭제전 하위정보 확인
        $ique = " select count(*) from odtProductAddoption where pao_pcode='{$pass_code}' and find_in_set('{$pass_uid}' , pao_parent) > 0 "; // 1차 정보
        $ires = mysql_query($ique);
        if(mysql_result($ires,0,0) > 0) {
            echo "is_subcategory"; // 하위 카테고리가 있음 표시
            exit;
        }

        // 순번재조정
        $r = _MQ(" select * from odtProductAddoption where pao_uid='" . $pass_uid . "' ");
        _MQ_noreturn(" update odtProductAddoption set pao_sort=pao_sort-1  where pao_pcode='" . $r['pao_pcode'] . "' and pao_depth='". $r['pao_depth'] ."' and pao_parent='" . $r['pao_uid'] . "' and pao_sort > '". $r['pao_sort'] ."' ");

        // 삭제
        _MQ_noreturn("delete from odtProductAddoption where pao_uid='{$pass_uid}'");

        break;




    // 2차 옵션 추가
    case "2depth_add":

        $ique = " select * from odtProductAddoption where pao_pcode='{$pass_code}' and pao_uid='{$pass_uid}' "; // 1차 정보
        $ir = _MQ($ique);

        // 순번추출
        $r = _MQ(" select ifnull(max(pao_sort),0) as max_sort from odtProductAddoption where pao_pcode='" . $pass_code . "' and pao_depth='2' and pao_parent='" . $ir['pao_uid'] . "' ");
        $max_sort = $r['max_sort'] + 1;

        // 항목추가 - 2차
        _MQ_noreturn("
            insert odtProductAddoption set
                pao_pcode='{$pass_code}',
                pao_poptionname='',
                pao_depth='2',
                pao_parent='{$ir['pao_uid']}',
                pao_sort='". $max_sort ."'
        ");

        break;



    // 1차 옵션 추가
    case "1depth_add":

        // 순번추출 - 1차
        $r = _MQ(" select ifnull(max(pao_sort),0) as max_sort from odtProductAddoption where pao_pcode='" . $pass_code . "' and pao_depth='1' ");
        $max_sort = $r['max_sort'] + 1;

        // 항목추가 - 1차
        _MQ_noreturn("
            insert odtProductAddoption set
                pao_pcode='{$pass_code}',
                pao_poptionname='',
                pao_depth='1',
                pao_sort='". $max_sort ."'
        ");
        $uid_1depth = mysql_insert_id();

        // 순번추출 - 2차
        $r2 = _MQ(" select ifnull(max(pao_sort),0) as max_sort from odtProductAddoption where pao_pcode='" . $pass_code . "' and pao_depth='2' and pao_parent='" . $uid_1depth . "' ");
        $max_sort2 = $r2['max_sort'] + 1;

        // 항목추가 - 2차
        _MQ_noreturn("
            insert odtProductAddoption set
                pao_pcode='{$pass_code}',
                pao_poptionname='',
                pao_depth='2',
                pao_parent='{$uid_1depth}',
                pao_sort='". $max_sort2 ."'
        ");

        break; 

}

//1차 추출
$que = " select * from odtProductAddoption where pao_pcode='{$pass_code}' and pao_depth='1' order by pao_uid ";
$res = _MQ_assoc($que);
if(sizeof($res) <= 0) die('<!-- 내용없을경우 모두공통 --><div class="cm_no_conts"><div class="no_icon"></div><div class="gtxt">등록된 옵션정보가 없습니다.</div></div><!-- // 내용없을경우 모두공통 -->');
?>

<div class="option_data_box">
    <form action="_product_addoption.pro.php" name="frm_option" id="frm_option" target="common_frame"  method="post">
        <input type="hidden" name="pass_code" value="<?php echo $pass_code; ?>">
        <input type="hidden" name="pass_type" value="">
        <input type="hidden" name="pass_depth" value="">
        <input type="hidden" name="pass_uid" value="">

        <ul>
            <?php
            foreach($res as $k=>$r) {
                $que2 = " select * from odtProductAddoption where pao_pcode='{$pass_code}' and pao_depth='2' and pao_parent='{$r['pao_uid']}' order by pao_sort asc , pao_uid asc ";
                $res2 = _MQ_assoc($que2);
            ?>
                <?php /* 1차 옵션 { */ ?>
                <li class="depth_1 if_nextdepth<?=$r['pao_view'] == 'Y'?null:' if_option_hide';?>">
                    <div class="opt">
                        <div class="txt">1차 옵션</div>
                        <span class="shop_btn_pack"><a href="javascript:category_apply('2depth_add', '<?=$r['pao_uid']?>');" class="small red" title="" >2 차 옵션추가</a></span>
                    </div>

                    <div class="value">
                        <!-- 이름입력 -->
                        <span class="wrapping_name">
                            <span class="txt_box">1차 옵션명</span>
                            <input type="text" name="pao_info[<?=$r['pao_uid']?>][pao_poptionname]" class="input_design" placeholder="1차 옵션명입력" value="<?=$r['pao_poptionname']?>" />
                        </span>
                        <!-- 순서변경 버튼 -->
                        <span class="btn_updown">
                            <span class="shop_btn_pack"><a href="javascript:f_sort('U' , '1', '<?=$r['pao_uid']?>' );" class="small white" title="위로" ><span class="shapeup"></span></a></span>
                            <span class="shop_btn_pack"><a href="javascript:f_sort('D' , '1', '<?=$r['pao_uid']?>' );" class="small white" title="아래로" ><span class="shapedw"></span></a></span>
                        </span>
                        <!-- 삭제버튼 -->
                        <span class="btn_delete"><span class="shop_btn_pack"><a href="javascript:category_apply('1depth_del', '<?=$r['pao_uid']?>');" class="small gray" title="옵션을 삭제합니다." >삭제</a></span></span>
                        <!-- 끼워넣기버튼 -->
                        <span class="btn_add"><span class="shop_btn_pack"><a href="javascript:f_insert('1', '<?=$r['pao_uid']?>');" class="small white" title="바로 아래로 옵션을 추가합니다.">끼워넣기</a></span></span>
                        <!-- 숨기기체크 -> 숨김시 li. 추가 -->
                        <label class="btn_hide" title="<?=$r['pao_view'] == 'Y' ? "옵션 숨기기" : '옵션 보이기';?>"><input type="checkbox" class="btn_hide_input" name="pao_info[<?=$r['pao_uid']?>][pao_view]" value="1" <?=$r['pao_view'] == 'Y' ? "checked" : NULL;?> /></label>
                    </div>
                </li>
                <?php /* } 1차 옵션 */ ?>
                <?php /* 2차 옵션 { */ ?>
                    <?php
                    foreach($res2 as $k2=>$r2) {
                        if($r2['pao_poptionname'] == "" || !$r2['pao_poptionname']) $save_chk ++;
                    ?>
                    <li class="depth_2<?=$r2['pao_view'] == 'Y'?null:' if_option_hide';?>">
                        <!-- 순서항목 타이틀 -->
                        <div class="opt">
                            <div class="txt">2차 옵션</div>
                        </div>
                        
                        <!-- 값입력전체박스 -->
                        <div class="value">
                            <!-- 이름입력 -->
                            <span class="wrapping_name">
                                <span class="txt_box">2차 옵션명</span>
                                <input type="text" name="pao_info[<?=$r2['pao_uid']?>][pao_poptionname]" class="input_design" value="<?=$r2['pao_poptionname']?>" placeholder="1차 옵션명입력" />
                            </span>
                            <!-- 값 입력 -->
                            <span class="wrapping_num">
                                <span class="txt_box">공급가</span>
                                <input type="text" name="pao_info[<?=$r2['pao_uid']?>][pao_poptionpurprice]" class="input_design input_price" value="<?=$r2['pao_poptionpurprice']?>" placeholder="0" />
                            </span>
                            <span class="wrapping_num">                 
                                <span class="txt_box">판매가</span>
                                <input type="text" name="pao_info[<?=$r2['pao_uid']?>][pao_poptionprice]" class="input_design input_price" value="<?=$r2['pao_poptionprice']?>" placeholder="0" />
                            </span>
                            <span class="wrapping_num">
                                <span class="txt_box">수량(재고)</span>
                                <input type="text" name="pao_info[<?=$r2['pao_uid']?>][pao_cnt]" class="input_design input_number" value="<?=$r2['pao_cnt']?>" placeholder="0" />
                            </span>
                            <span class="wrapping_num">
                                <span class="txt_box">판매</span>
                                <input type="text" name="" class="input_design input_number" placeholder="0" readonly="readonly" value="<?=$r2['pao_salecnt']?>" disabled />
                            </span>
                            <!-- 순서변경 버튼 -->
                            <span class="btn_updown">
                                <span class="shop_btn_pack"><a href="javascript:f_sort('U' , '2', '<?=$r2['pao_uid']?>' );" class="small white" title="위로" ><span class="shapeup"></span></a></span>
                                <span class="shop_btn_pack"><a href="javascript:f_sort('D' , '2', '<?=$r2['pao_uid']?>' );" class="small white" title="아래로" ><span class="shapedw"></span></a></span>
                            </span>
                            <!-- 삭제버튼 -->
                            <span class="btn_delete"><span class="shop_btn_pack"><a href="javascript:category_apply('2depth_del' , '<?=$r2['pao_uid']?>');" class="small gray" title="해당 옵션을 삭제합니다." >삭제</a></span></span>
                            <!-- 끼워넣기버튼 -->
                            <span class="btn_add"><span class="shop_btn_pack"><a href="javascript:f_insert('2', '<?=$r2['pao_uid']?>');" class="small white" title="바로 아래로 옵션을 추가합니다." >끼워넣기</a></span></span>
                            <!-- 숨기기체크 -->
                            <label class="btn_hide" title="<?=$r2['pao_view'] == 'Y' ? "옵션 숨기기" : '옵션 보이기';?>"><input type="checkbox" class="btn_hide_input" name="pao_info[<?=$r2['pao_uid']?>][pao_view]" value="1" <?=$r2['pao_view'] == 'Y' ? "checked" : NULL;?> /></label>
                        </div>
                    </li>
                    <?php } ?>
                <?php /* } 2차 옵션 */ ?>
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

            $(this).closest('li').removeClass('if_option_hide').addClass('if_option_hide');
            $(this).closest('label').attr('title', '옵션 보이기');
            $('.ui-tooltip-content').html('옵션 보이기');
        }

        category_apply();
    });
});
// } 옵션 숨기기 효과
</script>