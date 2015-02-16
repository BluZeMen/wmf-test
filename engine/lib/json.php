<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 04.01.15
 * Time: 22:12
 */

function json_encode_u($data)
{
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        function($val){
            return mb_decode_numericentity('&#'.intval($val[1], 16).';', array(0, 0xffff, 0, 0xffff), 'utf-8');
        }, json_encode($data)
    );
}