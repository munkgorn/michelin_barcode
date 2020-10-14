<?php
// Variable
$from = 'myname@domain.tld';
$fromname = 'myname';
$to = 'sendto@domain.tld';
$subject = 'Test SMTP Mailer';
$message = 'Test sending mail by SMTP class\nRegards';

// Main code
require("class.phpmailer.php");
$mail = new PHPMailer();
$mail->IsSMTP();
$mail->IsHTML(true); //หากส่งในรูปแบบ html ถ้าส่งเป็น text ก็ลบบรรทัดนี้ออกได้
$mail->CharSet = "utf-8"; //กำหนด charset ของภาษาในเมล์ให้ถูกต้อง เช่น tis-620 หรือ utf-8

$mail->Host = "localhost"; // SMTP server
$mail->SMTPAuth = "true";
$mail->Username = "name@yourdomain.com"; // ชื่อ emil ที่ท่านใช้ login ควรสร้าง email user แยกต่างหากเพื่อใช้ส่งเมล์จากเว็บโดยเฉพาะเพื่อให้ตรวจสอบได้ง่าย
$mail->Password = "password"; // รหัสผ่านของ email ที่ระบุด้านบน

$mail->From = $from; // ผู้รับจะเห็นอีเมล์นี้เป็น ผู้ส่งเมล์
$mail->FromName = $fromname; // ผู้รับจะเห็นชื่อนี้เป็น ชื่อผู้ส่ง
$mail->AddAddress($to);
$mail->Subject = $subject;
$mail->Body = $message;

$mail->Send();
?>
