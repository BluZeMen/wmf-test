<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 13.12.14
 * Time: 23:26
 */

require_once 'engine/incl_all.php';
require_once 'engine/render.php';

echo 'rendering begins<br>';
render('register.php', ['page_title' => 'Регистрация']);
echo 'rendering finished';