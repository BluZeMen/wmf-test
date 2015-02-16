<?php
require_once 'dao/user.php';
require_once 'dao/userpic.php';

$action = isset($recoverPassword) ? urlTo('rec-pass') : urlTo('login');
?>
<div class="form-container">
    <form id="form-login" action="<?=$action?>" method="POST" onsubmit="onLogin()">
        <p class="form-title"><?=lstr('Login')?></p>
        <input type="email" name="email" tabindex="1" placeholder="<?=lstr('EnterEmail')?>" maxlength="<?=User::MAX_LEN_EMAIL?>" required /><br>
        <?php if(!isset($recoverPassword)){?>
        <input type="password" name="password" placeholder="<?=lstr('EnterPassword')?>" maxlength="<?=User::MAX_LEN_PASSWORD?>" tabindex="2" required /><br>
        <?php }?>
        <input type="submit" value="<?=lstr('LoginBtn')?>" tabindex="3" />
        <div class="oid-container">
            <a href="/vk">ВК</a>
            <a href="/ya">Ya</a>
            <a href="/Gp">G+</a>
            <a href="/fb">Fb</a>
        </div>
        <div class="hsep">
            <div class="hsep-b"></div>
            <p><?=lstr('Or')?></p>
            <div class="hsep-e"></div>
        </div>
        <a href="<?=urlTo('register')?>"><?=lstr('Register')?></a><br>
        <a href="<?=urlTo('rec-pass')?>"><?=lstr('ForgotPass')?></a>
    </form>
    <script>
        function onLogin(){
            //todo: email pass spec symb.
            return isValidEmail(document.getElementsByName('email')[0].value);
        }
    </script>
</div>