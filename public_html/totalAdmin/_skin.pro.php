<?

include 'inc.php';

if(array_key_exists('use', $_GET)) {
    if(empty($use)) { $folder = 'default'; } else { $folder = $use; }
    $symlink = '../pages';
    @unlink($symlink);
    @symlink('./skin/'.$folder, $symlink);
	_MQ_noreturn("update odtSetup set P_SKIN='$use' where serialnum='1'");
}

if(array_key_exists('use_mobile', $_GET)) {
    if(empty($use_mobile)) { $folder = 'default_mobile'; } else { $folder = $use_mobile; }
    $symlink = '../m';
    @unlink($symlink);
    @symlink('./skin/'.$folder, $symlink);
    _MQ_noreturn("update odtSetup set P_SKIN_M='$use_mobile' where serialnum='1'");
}

?>

<script>
alert('스킨이 변경되었습니다.');
top.location.reload();
</script>