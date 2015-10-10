<?php
if(!defined("INCMS")) die();

function mail_init($content="text/html", $charset="utf-8"){
	$host = $_SERVER["HTTP_HOST"];
	$from = "no-reply@{$host}";
	ini_set("SMTP", "mail." . $host);
	ini_set("sendmail_from", $from);

	$header = "From: Durmstrang <{$from}>\r\n";
	$header.= "Content-Type: $content; charset=$charset\r\n";

	return $header;
	}

function send_mail($email, $subject, $text, $headers=""){
	if(!$headers)
		$headers = MAILHEADERS;
	$subject = '=?utf-8?B?' . base64_encode($subject) . '?=';
	$text = $text;
	return mail($email, $subject, $text, $headers);
	}

function send_mails_via_bcc($emails, $subject, $text){
	$headers = MAILHEADERS . "Bcc: " . implode(",", $emails) . "\r\n";
	return send_mail("", $subject, $text, $headers);
	}

function send_mail_to_all($emails, $subject, $text){
	$ret = true;
	foreach($emails as $email){
		$temp = send_mail($email, $subject, $text);
		$ret = $ret & $temp;
		}
	return $ret;
	}

function send_mail_to_first($emails, $subject, $text){
	$ret = false;
	foreach($emails as $email){
		$ret = send_mail($email, $subject, $text);
		if($ret)
			break;
		}
	return $ret;
	}

function send_mail_to_admin($subject, $text){
	$sql = "SELECT email
		FROM ".PREF."users
		WHERE is_admin<>0";
	$result = query($sql);
	$emails = fetch_column($result);
	$emails[] = "durmstrang.kiev.ua@gmail.com";
	return send_mail_to_all($emails, $subject, $text);
	}

function send_mail_to_LU($subject, $text){
	global $LU;
	$emails = [$LU["email"]];
	return send_mail_to_first($emails, $subject, $text);
	}

function send_mail_by_userid($userid, $subject, $text){
	$userid = (int)$userid;
	$sql = "SELECT u.email
		FROM ".PREF."users AS u
		WHERE u.id=$userid
		LIMIT 1";
	$result = query($sql);
	$rows = num_rows($result);
	if(!$rows)
		return;
	$emails = fetch_row($result);
	return send_mail_to_first($emails, $subject, $text);
	}

function xmail_helper($email, $subject, $text, $un, $attachment){
	$utd = UTD;
	$subject = '=?utf-8?B?' . base64_encode($subject) . '?=';

	$head = <<<EOT
From: $utd <admin@{$_SERVER["SERVER_NAME"]}>
To: $email
Mime-Version: 1.0
Content-Type:multipart/mixed; boundary="$un"


EOT;

	$zag = <<<EOT
--$un
Content-Type:text/html; charset=utf-8
Content-Transfer-Encoding: 8bit


$text

EOT;

	$zag.= $attachment;
	return mail($email, $subject, $zag, $head);
	}

function mail_get_attach($un, $filename, $contents){
	$zag = "";
	$basename = basename($filename);
	$zag.= <<<EOT
--$un
Content-Type: application/octet-stream; name="$basename"
Content-Transfer-Encoding:base64
Content-Disposition:attachment; filename="$basename"


EOT;
	$zag.= chunk_split(base64_encode($contents)) . "\n";
	return $zag;
	}

function xmail($email, $subject, $text, $filenames){
	$zag = "";
	$un  = "----------".strtoupper(uniqid(time()));
	if(is_scalar($filenames))
		$filenames = [$filenames];

	foreach($filenames as $filename){
		$zag.= mail_get_attach($un, $filename, file_get_contents($filename));
		}
	return xmail_helper($email, $subject, $text, $un, $zag);
	}

function xmail_invoices($email, $subject, $text, $invoices){
	$zag = "";
	$un  = "----------".strtoupper(uniqid(time()));

	foreach($invoices as $invoiceid){
		$zag.= mail_get_attach($un, "invoice$invoiceid.xls", getinvoice("order", $invoiceid));
		}
	return xmail_helper($email, $subject, $text, $un, $zag);
	}

?>
