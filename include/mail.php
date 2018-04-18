<?php
if(!defined("INCMS")) die();

function send_mail($email, $subject, $text){
	$SPApiProxy = new SendpulseApi(SENDPULSE_API_USER_ID, SENDPULSE_API_SECRET, "session");

	$email = [
	  'html'    => $text,
	  'text'    => strip_tags($text),
	  'subject' => $subject,
	  'from'    => [
	    'name'  => SENDPULSE_FROM_NAME,
	    'email' => SENDPULSE_FROM_EMAIL,
	  ],
	  'to'      => [
	    [
	      'name'  => $email,
	      'email' => $email,
	    ],
	  ],
	];

	return $SPApiProxy->smtpSendMail($email)->result;
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
		WHERE is_admin<>0 AND no_admin_mail=0";
	$result = query($sql);
	$emails = fetch_column($result);
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

?>
