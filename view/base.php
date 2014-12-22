<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?=isset($alt_page_title) ? $alt_page_title : lstr('PageTitle')?></title>
    <meta http-equiv="content-language" content="<?=View::getLanguage()?>" />

    <link rel="stylesheet" href="styles/base.css">

    <?php if($__view_of_content == 'login' || $__view_of_content == 'register'){ ?>
        <link rel="stylesheet" href="styles/form-base.css">
    <?php }//end of condition?>

    <?php putViewStyle($__view_of_content); ?>

    <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300|Open+Sans:600italic|Playfair+Display:700italic,400italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
    <![endif]-->
</head>
<body>
<section id="content">
    <header>
        <div class="logo">
            <h1><?=lstr('MainTitle')?></h1>
            <h2><?=lstr('Agency')?></h2>
            <p><?=lstr('Description')?></p>
        </div>
        <div id="lang-switch">
            <?php
            include 'lang-switch.php';
            ?>
        </div>
    </header>
    <?php
        include "view/$__view_of_content.php";
    ?>
    <div id="error-output">
        <?php

        if(isset($__errors))
            foreach($__errors as $e){
                echo "<p>$e</p>";
            }

        if(!isset($error_code))
            $error_code = null;

        if(isset($_GET['error'])) {
            $error_code = $_GET['error'];
        }elseif(isset($__errors['error_code'])){
            $error_code = $__errors['error_code'];
        }

        if($__view_of_content == 'login'){
            switch($error_code) {
                case Auth::OK : break;
                case Auth::BAD_LOGIN_DATA : {
                    echo lstr('BAD_LOGIN_DATA');
                    break;
                }
                case Auth::NO_SUCH_USER : {
                    echo lstr('NO_SUCH_USER');
                    break;
                }
                default:{
                    echo lstr('INTERNAL_ERROR');
                    break;
                }
            }

        }elseif($__view_of_content == 'register'){
            switch($error_code) {
                case Auth::OK : break;

                case Auth::EMAIL_ALREADY_EXIST : {
                    echo lstr('EMAIL_ALREADY_EXIST');
                    break;
                }
                case Auth::BAD_REGISTRATION_DATA : {
                    echo lstr('BAD_REGISTRATION_DATA');
                    break;
                }
                case Auth::BAD_REGISTRATION_IMAGE : {
                    echo lstr('BAD_REGISTRATION_IMAGE');
                    break;
                }
                default:{
                    echo lstr('INTERNAL_ERROR');
                    break;
                }
            }
        }

        ?>
    </div>
</section>
<script src="scripts/utils.js"></script>
<script>
    function setErrorText(errText){
        sel('#error-output').value = '<p>'+errText+'</p>';
        sel('#error-output').style.display = 'block';
    }

    function putErrorText(errText){
        sel('#error-output').value += '<p>'+errText+'</p>';
        sel('#error-output').style.display = 'block';
    }


</script>
</body>
</html>