<? if($row_setup['recaptcha_api']&&$row_setup['recaptcha_secret'] ) { ?>
<li class="ess editor">
    <div class="value" style="text-align:center; overflow:hidden;">
        <!-- 스팸방지 들어감 -->
        <script src="//www.google.com/recaptcha/api.js"></script>
        <div class="g-recaptcha" data-sitekey="<?php echo $row_setup['recaptcha_api']; ?>" style="width:304px; margin:0 auto;"></div>
         <input type="hidden" name="recaptcha_action_use" value="Y" />
    </div>
</li>
<? } ?>