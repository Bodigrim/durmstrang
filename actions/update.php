<?php

include "../include/config.php";

function sendMailGroupRegistration($userid, $groupid, $groupName){
	$renderData =
		[ "groupName" => $groupName
		];

	$userSubject = "Зарегистрирована группа поселения";
	$userText = constructTwig()->render("mails/group-registration.twig", $renderData);
	send_mail_by_userid($userid, $userSubject, $userText);

	$adminSubject = "Создана новая группа поселения";
	$adminText = constructTwig()->render("mails/group-registration-admin.twig", $renderData);
	send_mail_to_admin($adminSubject, $adminText);
}

function sendMailUserRegistration($userMail, $userName, $userSurname){
	$renderData =
		[ "userMail"    => $userMail
		, "userName"    => $userName
		, "userSurname" => $userSurname
		];

	$adminSubject = "Зарегистрирован новый пользователь";
	$adminText = constructTwig()->render("mails/user-registration-admin.twig", $renderData);
	send_mail_to_admin($adminSubject, $adminText);
}

function sendMailGroupMemberLeft($userid, $userMail, $userName, $groupid){
	$renderData =
		[ "groupName" => groupIdToGroupName($groupid)
		, "userName"  => $userName
		, "userMail"  => $userMail
		];
	$userSubject = "Участник покинул твою группу";
	$userText = constructTwig()->render("mails/group-member-left.twig", $renderData);
	send_mail_by_userid($groupid, $userSubject, $userText);
}

function sendMailGroupMemberAdded($userid, $userMail, $userName, $groupid){
	$renderData =
		[ "groupName" => groupIdToGroupName($groupid)
		, "userName"  => $userName
		, "userMail"  => $userMail
		];
	$userSubject = "К твоей группе присоединился новый участник";
	$userText = constructTwig()->render("mails/group-member-added.twig", $renderData);
	send_mail_by_userid($groupid, $userSubject, $userText);
}

function sendMailGroupMemberChange($userid, $userMail, $userName, $oldGroupid, $newGroupid){
	if($oldGroupid){
		sendMailGroupMemberLeft($userid, $userMail, $userName, $oldGroupid);
	}
	if($newGroupid){
		sendMailGroupMemberAdded($userid, $userMail, $userName, $newGroupid);
	}
}

$post_get = new GetVarClass();
$email = $post_get->getemail("email");

if(!$email)
	die("Редактирование заявки невозможно: введите корректный e-mail. ");

$editorid = loginbycookie();
if(!canEdit($editorid, $email))
	die("У вас недостаточно прав доступа, чтобы редактировать заявку {$email}. ");

$userid = emailToId($email);

$name             = $post_get->getvar("name");
$surname          = $post_get->getvar("surname");
$nick             = $post_get->getvar("nick");
$age              = $post_get->getadate("age");
$contacts         = $post_get->getvar("contacts");
$facebook         = $post_get->getvar("facebook");
$telegram         = trim($post_get->getvar("telegram"));
$publicity        = $post_get->getvar("publicity");
$contraindication = $post_get->getvar("contraindication");
$character_name   = $post_get->getvar("character_name");
$character_age    = $post_get->getadate("character_age");
$blood            = $post_get->getvar("blood");
$quenta           = $post_get->getvar("quenta");
$fear             = $post_get->getvar("fear");
$possesions       = $post_get->getvar("possesions");
$addendum         = $post_get->getvar("addendum");
$block            = $post_get->getvar("block");
$wish             = $post_get->getvar("wish");
$antiwish         = $post_get->getvar("antiwish");

$sql = "SELECT u.name
	FROM ".PREF."users AS u
	WHERE id = $userid
	LIMIT 1";
$result = query($sql);
list($oldName) = fetch_row($result);

$sql = "UPDATE ".PREF."users
	SET name = '$name'
	  , surname = '$surname'
		, nick = '$nick'
		, age = '$age'
		, contacts = '$contacts'
		, facebook = '$facebook'
		, telegram = '$telegram'
		, publicity = '$publicity'
		, contraindication = '$contraindication'
		, character_name = '$character_name'
		, character_age = '$character_age'
		, blood = '$blood'
		, quenta = '$quenta'
		, fear = '$fear'
		, possesions = '$possesions'
		, addendum = '$addendum'
		, block = '$block'
		, wish = '$wish'
		, antiwish = '$antiwish'

	WHERE id = $userid
	LIMIT 1";
query($sql);

$updated = (bool)affected_rows();

if(isset($_FILES["photo"]) && $_FILES["photo"]['error']!=4){
	$options = new FileUploadOptions();
	$options->key = "photo";
	$options->extensions = ["png", "jpg", "jpeg", "gif"];
	$options->dir = "../photos/";
	$options->is_critical = true;
	$options->neoname = photoFileName($email) . ".jpg";
	if(file_exists("../photos/{$options->neoname}")){
		unlink("../photos/{$options->neoname}");
		}

	$filename = file_upload($options);
	if($filename!=$options->neoname){
		rename("../photos/" . $filename, "../photos/" . $options->neoname);
	}

	$updated = true;
	}

if($updated){
	$link = $_SERVER["HTTP_HOST"] . "/edit.php?" . http_build_query(["email" => $email]);
	if($editorid==emailToId($email)){
		markUpdated(emailToId($email));
		// send_mail_to_admin("$name обновил заявку", "<a href=\"$link\">Просмотреть</a>");
		}
	else {
		send_mail_by_userid(emailToId($email), "Админ отредактировал вашу заявку", "<a href=\"$link\">Просмотреть</a>");
		}
	}

if($oldName != $name){
	sendMailUserRegistration($email, $name, $surname);
}

redirect(isAdmin($editorid) ? "/table.php#{$email}" : "/edit.php?" . http_build_query(["email" => idToEmail($editorid), "justUpdated" => true]));

?>
