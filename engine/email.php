<?php
/**
 * Created by PhpStorm.
 * User: bzm
 * Date: 05.01.15
 * Time: 22:40
 */

require_once 'lib/PHP-mailer/class.phpmailer.php';
require_once 'dao/user.php';
include_once 'incl_all.php';

class Email
{
    public static function sendRecoverPassword($user, $recoverToken)
    {
        $subject = "Recover password on 'Here are the things'";
        $toName = $user->fname.' '.$user->sname;
        $link = $_SERVER['SERVER_NAME']."/do.php?a=editor&at=$recoverToken";
        $session_lifetime = (float)ini_get('session.gc_maxlifetime');
        $session_lifetime = $session_lifetime / (60.0 * 60.0); //hrs
        $text = "We heard that you lost your 'Here are the things'  password. Sorry about that!

                But don't worry! You can use the following link within the next day to reset your password:

                $link

                If you don't use this link within $session_lifetime hours, it will expire. To get a new password reset link, repeat actions.

                Thanks,
                Your friends at 'Here are the things'";


        return self::send($subject, $text, $user->email, $toName);
    }

    public static function sendConfirmRegistration($user, $registrationToken)
    {
        $subject = "Confirming registration on 'Here are the things'";
        $toName = $user->fname.' '.$user->sname;
        $link = $_SERVER['SERVER_NAME']."/do.php?a=profile&at=$registrationToken";
        $session_lifetime = (float)ini_get('session.gc_maxlifetime');
        $session_lifetime = $session_lifetime / (60.0 * 60.0); //hrs
        $text = "We heard that you want to register in 'Here are the things'.

                Dear $toName, please, use this link to confirm you registration:

                $link

                If you don't use this link within $session_lifetime hours, it will expire.

                Thanks,
                Your friends at 'Here are the things'";


        return self::send($subject, $text, $user->email, $toName);
    }

    public static function send_test($subject, $html, $to, $toName, $from = PROJ_CONF::PROJ_NAME, $fromEmail = null)
    {
        if($fromEmail === null){
            $fromEmail = 'no-reply@'.PROJ_CONF::PROJ_DOMAIN;
        }
        echo "<h1>Mail view</h1>";
        echo "From: $from, $fromEmail<br>";
        echo "To: $to, $toName<br>";
        echo "Subject: $subject<br><br>";
        echo "$html";
        return true;
    }

    public static function send($subject, $html, $to, $toName, $from = PROJ_CONF::PROJ_NAME, $fromEmail = null)
    {
        if(DEBUG::ENABLED){
            return self::send_test($subject, $html, $to, $toName, $from, $fromEmail);
        }

        if($fromEmail === null){
            $fromEmail = 'no-reply@'.PROJ_CONF::PROJ_DOMAIN;
        }
        $mail = new PHPMailer(); // defaults to using php "mail()"
        $mail->SetFrom($fromEmail, $from);
        $mail->AddAddress($to, $toName);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->AltBody = $html; // optional, comment out and test
        $mail->MsgHTML($html);
        return $mail->Send();
    }
}