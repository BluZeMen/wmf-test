<?php
require_once 'edit.php';
include_once 'incl_all.php';

$edited = null;
if(isset($_FILES['translation']['tmp_name'])){
    $edited = Edit::setTranslationsFromHttpFile('translation') ? 'ok' : 'nope';
}
?>
<div class="form-container">
    <form id="form-editor" action="<?=urlTo('editor')?>" method="POST" enctype="multipart/form-data" onsubmit="onEdit()">
        <p class="form-title"><?=lstr('Editor')?></p>
        <input type="hidden" name="MAX_FILE_SIZE" value="8000000" />

        <label><?=lstr('TranslationsFile')?></label><input type="file" name="translation" tabindex="1" accept="application/json" /><br>

        <input type="submit" value="<?=lstr('SetupBtn')?>" tabindex="2" /><br>
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