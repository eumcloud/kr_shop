<?
	// 카테고리 정보가 없을 시 2015-11-21
	$is_cuid = get_category_info($cuid);
	if(count($is_cuid)<1) error_msg("카테고리 정보가 없습니다.");

	unset($category_2depth_html,$category_3depth_html);
	if($category_total_info['depth1_display'] == "지역") {

		/*---- 2차 카테고리 ----*/			
		$sub_assoc = explode(",",$category_total_info['depth1_lineup']); // 묶음탭
		foreach($sub_assoc as $sub_key => $sub_val) {
			$sub_row = _MQ("select catecode,subcate_main from odtCategory where cHidden='no' and subcate_display_choice = '".$sub_val."' and find_in_set('".$category_total_info['depth1_catecode']."',parent_catecode) > 0 order by subcate_main='Y' desc, cateidx asc limit 1");
			$sub_url = "/?pn=product.".($sub_row['subcate_main'] == "Y" ? "main" : "list")."&sub_cuid=".$sub_val."&cuid=".$sub_row['catecode']."";	// 첫번째 카테고리는 메인으로 이동시킨다.
			$category_2depth_html .= "<a href='".($sub_row['catecode'] ? $sub_url : "javascript:alert(\"카테고리 미지정\")")."' class='".($_GET['sub_cuid'] == $sub_val || ($sub_row['subcate_main'] == "Y" && !$_GET['sub_cuid']) ? "btn_hit" : "")." btn_ctg'>".$sub_val."</a>";
		}

		/*---- 3차 카테고리 ----*/			
		$category_sub_assoc = _MQ_assoc("select * from odtCategory where subcate_display_choice = '".$_GET['sub_cuid']."' and find_in_set('".$category_total_info['depth1_catecode']."',parent_catecode) > 0 and catedepth = 2 and cHidden='no' order by cateidx asc ");
		foreach($category_sub_assoc as $category_sub_key => $category_sub_row) {
			if($category_sub_row['catecode']) {
				$category_3depth_html .= "
					<div class='set'>
						<div class='first'><a href='/?pn=product.list&sub_cuid=".$_GET['sub_cuid']."&cuid=".$category_sub_row['catecode']."' class='".($category_sub_row['catecode'] == $category_total_info['depth2_catecode'] ? "btn_hit" : "")." btn'>".$category_sub_row['catename']."</a></div>
						<div class='second'><ul>
					";
				$category_3depth_assoc = _MQ_assoc("select * from odtCategory where find_in_set('".$category_sub_row['catecode']."',parent_catecode) and catedepth = 3 and cHidden='no' order by cateidx asc ");
				foreach($category_3depth_assoc as $category_3depth_key => $category_3depth_row) {
					$category_3depth_html .= "<li><a href='/?pn=product.list&sub_cuid=".$_GET['sub_cuid']."&cuid=".$category_3depth_row['catecode']."' class='".($category_3depth_row['catecode'] == $_GET['cuid'] ? "btn_hit" : "")." btn_sub'>".$category_3depth_row['catename']."<em class='eng'>".number_format($category_3depth_row['c_pro_cnt'])."</em></a><span class='line'></span></li>";
				}
				$category_3depth_html .= "</ul></div></div>";
			}
		}
		if($category_3depth_html) $category_3depth_html = "<span class='list_line'></span><div class='local_ctg'>".$category_3depth_html."</div>";
		/*---- // 3차 카테고리 ----*/			

	} else if($category_total_info['depth1_display'] == "기획전") { 

		$depth2_assoc = _MQ_assoc("select catename,catecode,cateimg from odtCategory where find_in_set(".$category_total_info['depth1_catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by cateidx asc");
		foreach($depth2_assoc as $depth2_key => $depth2_row) {
			$sub_url = "/?pn=product.promotion&cuid=".$depth2_row['catecode'];
			$category_2depth_html .= "<li class='".($category_total_info['depth2_catecode'] == $depth2_row['catecode'] ? "hit" : "")."'><span class='line'></span><a href='".$sub_url."' class='btn'>".$depth2_row['catename']."</a></li>";
			if( $depth2_key>0&&($depth2_key+1)%6==0 ) { $category_2depth_html .= "</ul><ul>"; }
		}
		$category_2depth_html = "<ul>".$category_2depth_html."</ul>";

	} else {

		$depth2_assoc = _MQ_assoc("select catename,catecode,cateimg,subcate_main from odtCategory where find_in_set(".$category_total_info['depth1_catecode'].",parent_catecode) and catedepth=2 and cHidden='no' order by cateidx asc");
		foreach($depth2_assoc as $depth2_key => $depth2_row) {
			$sub_url = "/?pn=product.".($depth2_row['subcate_main'] == "Y" ? "main" : "list")."&cuid=".$depth2_row['catecode'];	// 첫번째 카테고리는 메인으로 이동시킨다.
			if($depth2_row['cateimg']) {
				/*$category_2depth_html .= "<a href='".$sub_url."' class='".($category_total_info['depth2_catecode'] == $depth2_row[catecode] || ($depth2_row[subcate_main] == "Y" && !$_GET[cuid]) ? "hit" : "")."'><span class='ic'><img src='".IMG_DIR_PRODUCT.$depth2_row[cateimg]."' alt='".$depth2_row[catename]."' /></span><em>".$depth2_row[catename]."</em></a>";*/
				$category_2depth_html .= "<a href='".$sub_url."' class='".($category_total_info['depth2_catecode'] == $depth2_row['catecode'] || ($depth2_row['subcate_main'] == "Y" && !$_GET['cuid']) ? "btn_hit" : "")." btn_ctg'>".$depth2_row['catename']."</a>";
			} else {
				$category_2depth_html .= "<a href='".$sub_url."' class='".($category_total_info['depth2_catecode'] == $depth2_row['catecode'] || ($depth2_row['subcate_main'] == "Y" && !$_GET['cuid']) ? "btn_hit" : "")." btn_ctg'>".$depth2_row['catename']."</a>";
			}
		}

	}


?>