<?php
require_once 'dao/userpic.php';
include_once 'incl_all.php';

?>
<div class="form-container">
    <form id="form-register" action="<?=urlTo('register')?>" method="POST" enctype="multipart/form-data" onsubmit="onRegister()">
        <p class="form-title"><?=lstr('Register')?></p>
        <a href="<?=urlTo('login')?>"><?=lstr('LoginLink')?></a><br>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?= UserPic::MAX_FILESIZE?>" />

        <label><?=lstr('Email')?></label><input type="email" placeholder="<?=lstr('PH_Email')?>" name="email" tabindex="1" maxlength="<?=User::MAX_LEN_EMAIL?>" required /><br>
        <label><?=lstr('Password')?></label><input type="password" placeholder="<?=lstr('PH_Password')?>" name="password" tabindex="2" maxlength="<?=User::MAX_LEN_PASSWORD?>" required /><br>
        <label><?=lstr('PasswordAgain')?></label><input type="password" name="password-check" placeholder="<?=lstr('PH_PasswordAgain')?>" maxlength="<?=User::MAX_LEN_PASSWORD?>" tabindex="3" required /><br>
        <label><?=lstr('Avatar')?></label><input type="file" name="avatar" tabindex="4" accept="image/jpeg,image/png,image/gif" /><br>
        <label><?=lstr('FirstName')?></label><input type="text" placeholder="<?=lstr('PH_FirstName')?>" maxlength="<?=User::MAX_LEN_FNAME?>" name="fname" tabindex="5" required /><br>
        <label><?=lstr('Surname')?></label><input type="text" name="sname" placeholder="<?=lstr('PH_Surname')?>" maxlength="<?=User::MAX_LEN_SNAME?>" tabindex="6" required /><br>
        <label><?=lstr('Locale')?></label>
        <select name="locale" tabindex="8">
            <option value="ru-ru" selected>Русский</option>
            <option value="en-us">English(US)</option>
        </select><br>
        <p><?=lstr('RegisterInfo')?>.</p>
        <div class="iagree">
            <input type="checkbox" name="iagree" tabindex="9" required />
            <span><?=lstr('IAgreeWith')?> <a href="<?=urlTo('reader', array('doc' => 'register-agreement'))?>"><?=lstr('ResourceRules')?></a></span>.
        </div>
        <input type="submit" value="<?=lstr('RegisterBtn')?>" tabindex="10" /><br>

    </form>
    <script>
        function onRegister() {
            var form = sel('#form-register');
            var mail = form.getElementsByName('email')[0].value;
            var passw = form.getElementsByName('password')[0].value;
            var passw_ch = form.getElementsByName('password-check')[0].value;
            var fname = form.getElementsByName('fname')[0].value;
            var sname = form.getElementsByName('sname')[0].value;

            <?php require_once 'dao/user.php';?>
            //checking length of submitting fields;
            const MAX_LEN_MAIL = <?= User::MAX_LEN_EMAIL ?>;
            const MAX_LEN_PASSW = <?= User::MAX_LEN_PASSWORD ?>;
            const MAX_LEN_FNAME = <?= User::MAX_LEN_FNAME ?>;
            const MAX_LEN_SNAME = <?= User::MAX_LEN_SNAME?>;

            if (mail.length > MAIL_MAX_LEN) {
                putErrorText("Слишком длинный адрес почты");
                return false;
            }

            if (passw.length > MAX_LEN_PASSW) {
                putErrorText("Слишком длинный пароль");
                return false;
            }

            if (fname.length > MAX_LEN_FNAME) {
                putErrorText("Слишком длинное имя");
                return false;
            }

            if (sname.length > MAX_LEN_SNAME) {
                putErrorText("Слишком длинная фамилия");
                return false;
            }


            return isValidEmail(document.getElementsByName('email')[0].value);
        }
    </script>
</div>