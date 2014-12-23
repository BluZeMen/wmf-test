<div class="profile-data">
    <img src="getf.php?up=<?=Auth::getLoggedUser()->avatar ?>" alt="Юзерпик"><br>

    <a class="logout" class="data-name" href="<?=urlTo('logout')?>"><?=lstr('Exit')?></a>
    <br>
    <span class="data-name"><?=lstr('FirstName')?></span>
    <span class="data-param"><?=Auth::getLoggedUser()->fname ?></span>
    <br>
    <span class="data-name"><?=lstr('Surname')?></span>
    <span class="data-param"><?=Auth::getLoggedUser()->sname ?></span>
    <br>
    <span class="data-name"><?=lstr('Email')?></span>
    <span class="data-param"><?=Auth::getLoggedUser()->email ?></span>
    <br>
    <a class="data-name go-to" href="<?=urlTo('editor')?>"><?=lstr('GoToEditor')?></a><br>

</div>
