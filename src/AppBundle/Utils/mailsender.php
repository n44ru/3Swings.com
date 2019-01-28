<?php
/**
 * Created by PhpStorm.
 * User: carlosmanuel
 * Date: 6/13/17
 * Time: 10:44 p.m.
 */

namespace AppBundle\Utils;

use AdminBundle\Entity\Userlogs;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\Date;

class mailsender
{
    public function send($title, $msg)
    {
        $cabeceras = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        //
        $msg .= '<br><br>'.'This e-mail is for use by our members and contains information that may be Privileged, confidential or copyrighted under applicable law. If you are not the intended recipient, you are hereby formally notified that any use, copying or distribution of this e-mail, in whole or in part, is strictly prohibited. Please notify the sender by return e-mail and delete this e-mail from your system.';

        $email_admin = 'info3swings@gmail.com';
        mail($email_admin,$title,$msg,$cabeceras);
    }
    // Send to anyone.
    public function send_mail($to, $title, $msg, $from)
    {
        $cabeceras = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $cabeceras .= 'From:'.$from. "\r\n";
        // add the firm of the mail.
        $msg .= '<br><br>'.'This e-mail is for use by our members and contains information that may be Privileged, confidential or copyrighted under applicable law. If you are not the intended recipient, you are hereby formally notified that any use, copying or distribution of this e-mail, in whole or in part, is strictly prohibited. Please notify the sender by return e-mail and delete this e-mail from your system.';
        mail($to,$title,$msg,$cabeceras);
    }
}