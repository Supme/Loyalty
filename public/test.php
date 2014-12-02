<?php
/**
 * @package ly.
 * @author Supme
 * @copyright Supme 2014
 * @license http://opensource.org/licenses/MIT MIT License	
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF
 *	ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR
 *	PURPOSE.
 *
 *	Please see the license.txt file for more information.
 *
 */

// load the (optional) Composer auto-loader
if (file_exists('../vendor/autoload.php')) {
    require '../vendor/autoload.php';
}

$mail = new PHPMailer();
$mail->IsSMTP(); // set mailer to use SMTP

$mail->Host = "mail.kladr.biz";
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';
$mail->Username = 'jlr@kladr.biz';
$mail->Password = '18fQR_-80FHG';
$mail->Port = '587';

$mail->From = 'jlr@kladr.biz';
$mail->FromName = 'finance@jaguarlandrover.ru';
//$mail->ReturnPath = 'ams@kladr.biz';
$mail->AddAddress('d.sidorov@dmbasis.ru');
$mail->AddReplyTo('jlr@kladr.biz');
$mail->IsHTML(true);
$mail->Subject = 'Test From and Reply To';
$mail->Body = 'Hello. I am testing send email';
$mail->SMTPDebug = 1;

if(!$mail->Send())

{

    echo $mail->ErrorInfo;

}else{

    echo 'Email was sent!';

}
