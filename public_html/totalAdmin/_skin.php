<?PHP
	
	include_once("inc.header.php");

	$_folders = array('default','default_mobile');
	if ($handle = opendir($_SERVER[DOCUMENT_ROOT].'/skin')) {
		while (false !== ($entry = readdir($handle))) { if ($entry != "." && $entry != ".." && $entry != "default" && $entry != "default_mobile") { array_push($_folders,$entry); } }
		closedir($handle);
	}

	$current = $row_setup[P_SKIN];
	$current_mobile = $row_setup[P_SKIN_M];

?>
<script>function doublecheck(e){if(e)var r=e;else var r="정말 진행하시겠습니까?";return 1==confirm(r)?!0:!1}</script>


<div class="content_section_inner" style="margin-bottom: 0; padding-bottom: 10px;">
<div class=""><?=_DescStr("스킨변경은 레이아웃 및 그에 관련된 연동 프로그램도 모두 변경 시킵니다. 이에 따라 문제가 발생될 수 있으니 변경 시 반드시 사전 확인 후 진행하시기 바랍니다.")?></div>
</div>

<div class="sub_title"><span class="icon"></span><span class="title">PC 스킨 관리</span></div>
<div class="content_section_inner">

	<? if(count($_folders) > 0) { foreach($_folders as $v) { 
		$_skin_root = $_SERVER[DOCUMENT_ROOT].'/skin/'.$v;
		$source = file_get_contents($_skin_root.'/info.xml');
		$_skin = new SimpleXMLElement($source);
		if($_skin->mode=='PC') {
	?>
	<div style="width: 200px; overflow: hidden; border: 1px solid #ccc; float: left; margin: 0 5px 5px 0; background: #fff; text-align: center; <?=($current==$v)?'border-color: #ff6600;':''?>">
		<div style="background-image:url('/skin/<?=$v?>/thumb.png'); background-size: cover; background-position: center top; background-repeat: no-repeat; width: 200px; height: 200px;">&nbsp;</div>
		<div style="padding: 10px; padding-top: 0;">
			<h3 style="padding: 5px 0;margin:0; line-height:100%;" title="버전 <?=$_skin->version?>"><?=$_skin->title?></h3>
			<p style="height: 25px; overflow: auto;"><?=$_skin->info?></p>
			<? if($current==$v) { ?>
			<span style="display: block; background: #ff6600; color: #fff; padding: 5px; margin-top: 10px;">사용중</span>
			<? } else { ?>
			<a onclick="return doublecheck('사용중인 스킨이 <?=$_skin->title?> 스킨으로 변경됩니다.\n계속하시겠습니까?');" href="./_skin.pro.php?use=<?=$v?>" target="common_frame" style="display: block; background: #eee; padding: 5px; margin-top: 15px;">사용하기</a>
			<? } ?>
		</div>
	</div>
	<? }}} ?>
</div>

<div style="border-top: 1px solid #ccc; margin-bottom: 20px;"></div>

<div class="sub_title"><span class="icon"></span><span class="title">MOBILE 스킨 관리</span></div>
<div class="content_section_inner">

	<? if(count($_folders) > 0) { foreach($_folders as $v) { 
		$_skin_root = $_SERVER[DOCUMENT_ROOT].'/skin/'.$v;
		$source = file_get_contents($_skin_root.'/info.xml');
		$_skin = new SimpleXMLElement($source);
		if($_skin->mode=='MOBILE') {
	?>
	<div style="width: 200px; overflow: hidden; border: 1px solid #ccc; float: left; margin: 0 5px 5px 0; background: #fff; text-align: center; <?=($current_mobile==$v)?'border-color: #ff6600;':''?>">
		<div style="background-image:url('/skin/<?=$v?>/thumb.png'); background-size: cover; background-position: center top; background-repeat: no-repeat; width: 200px; height: 200px;">&nbsp;</div>
		<div style="padding: 10px; padding-top: 0;">
			<h3 style="padding: 5px 0;margin:0; line-height:100%;" title="버전 <?=$_skin->version?>"><?=$_skin->title?></h3>
			<p style="height: 25px; overflow: auto;"><?=$_skin->info?></p>
			<? if($current_mobile==$v) { ?>
			<span style="display: block; background: #ff6600; color: #fff; padding: 5px; margin-top: 10px;">사용중</span>
			<? } else { ?>
			<a onclick="return doublecheck('사용중인 스킨이 <?=$_skin->title?> 스킨으로 변경됩니다.\n계속하시겠습니까?');" href="./_skin.pro.php?use_mobile=<?=$v?>" target="common_frame" style="display: block; background: #eee; padding: 5px; margin-top: 15px;">사용하기</a>
			<? } ?>
		</div>
	</div>
	<? }}} ?>
</div>

<?PHP
	include_once("inc.footer.php");
?>