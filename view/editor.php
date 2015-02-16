<?php
require_once 'edit.php';
include_once 'incl_all.php';
require_once 'dao/user.php';

$edited = null;

if(isset($_FILES['translation']['tmp_name'])){
    $edited = Edit::setTranslationsFromHttpFile('translation') ? 'ok' : 'nope';
}

if(isset($_POST['password']) && isset($_POST['password-check'])){
    if($_POST['password'] == $_POST['password-check']) {
        $edited = Edit::setNewPassword($_POST['password']) ? 'ok' : 'nope';
    }else{
        $edited = 'nope';
    }
}

?>
<div class="form-container">
    <form id="form-editor" action="<?=urlTo('editor')?>" method="POST" enctype="multipart/form-data" onsubmit="onEdit()">
        <p class="form-title"><?=lstr('Editor')?></p>
        <input type="hidden" name="MAX_FILE_SIZE" value="8000000" />

        <label><?=lstr('TranslationsFile')?></label><input type="file" name="translation" tabindex="1" accept="application/json" /><br>

        <label><?=lstr('NewPassword')?></label><input type="password" placeholder="<?=lstr('PH_Password')?>" name="password" tabindex="3" maxlength="<?=User::MAX_LEN_PASSWORD?>" /><br>
        <label><?=lstr('NewPasswordAgain')?></label><input type="password" name="password-check" tabindex="4" placeholder="<?=lstr('PH_PasswordAgain')?>" maxlength="<?=User::MAX_LEN_PASSWORD?>" /><br>
        <input type="submit" value="<?=lstr('SetupBtn')?>" tabindex="5" /><br>
        <br>
        <a id="go-to" class="data-name" href="<?=urlTo('profile')?>"><?=lstr('GoToProfile')?></a><br>
        <?php if($edited == 'suc'){?>
            <p><?=lstr('OperationSuccess')?></p>
        <?php }?>
        <?php if($edited == 'fail'){?>
            <p><?=lstr('OperationFail')?></p>
        <?php }?>
    </form>
    <script>
        function onEdit() {
            return true;
        }
    </script>
</div>