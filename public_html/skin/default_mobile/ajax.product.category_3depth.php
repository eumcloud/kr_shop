<?
	if( !$_path_str ) {
		if( @file_exists("../include/config_database.php") ) {
			$_path_str = "..";
		}
		else {
			$_path_str = ".";
		}
	}

	include_once(dirname(__FILE__)."/../../include/inc.php");

	$local_3depth_assoc = _MQ_assoc("select * from odtCategory where parent_catecode='".$parent."' and catedepth=3 and cHidden ='no' order by cateidx asc");
	$category_3depth_html = "<option value='/m/?pn=product.list&cuid=".$cuid."'>전체</option>";
	foreach($local_3depth_assoc as $local_3depth_key => $local_3depth_row) {
		//$is_select = () ? "selected" : NULL;
		$sub_url = "/m/?pn=product.".($local_3depth_row[subcate_main] == "Y" ? "main" : "list")."&sub_cuid=".$sub_cuid."&cuid=".$local_3depth_row[catecode];
		$category_3depth_html .= "<option id='cuid_".$local_3depth_row[catecode]."' value='".$sub_url."' ".$is_select.">".$local_3depth_row[catename]."</option>";
	}
	echo $category_3depth_html;
?>



