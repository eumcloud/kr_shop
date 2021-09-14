<?php

    session_start();
    
    include_once("inc.php");

    _MQ_noreturn("
		update odtOrder set taxorder = '{$tax}' where ordernum = '{$ordernum}'
    ");

?>