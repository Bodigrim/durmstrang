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

function sendMailUserRegistration($userMail, $userName){
	$renderData =
		[ "userMail" => $userMail
		, "userName" => $userName
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
$nick             = $post_get->getvar("nick");
$city             = $post_get->getvar("city");
$age              = $post_get->getadate("age");
$contacts         = $post_get->getvar("contacts");
$contraindication = $post_get->getvar("contraindication");
$room             = $post_get->getvar("room");

$sql = "SELECT u.name, u.group_owner, u.group_name, u.group_id
	FROM ".PREF."users AS u
	WHERE id = $userid
	LIMIT 1";
$result = query($sql);
list($oldName, $oldGroupOwner, $oldGroupName, $oldGroupId) = fetch_row($result);

$settlement = $post_get->getvar("settlement", "self|owner|member", "self");
switch($settlement){
	case "self":
		$groupOwner = 0;
		$groupName = "";
		$groupId = 0;

		if($oldGroupId != $groupId){
			sendMailGroupMemberLeft($userid, $email, $name, $oldGroupId);
		}

		break;
	case "owner":
		$groupOwner = 1;
		$groupName = $post_get->getvar("group_name");
		if(!$groupName){
			$groupName = randomDefaultGroupName();
		}
		$groupId = $userid;

		if($oldGroupId != $groupId){
			sendMailGroupRegistration($userid, $groupId, $groupName);
		}

		break;
	case "member":
		$groupOwner = 0;
		$groupName = "";
		$groupId = $post_get->getint("group_id", 0);

		if($oldGroupId != $groupId){
			sendMailGroupMemberChange($userid, $email, $name, $oldGroupId, $groupId);
		}

		break;
	default:
		throw new Exception("update: unexpected value of settlement " . $settlement);
}

$sql = "UPDATE ".PREF."users
	SET name = '$name'
		, nick = '$nick'
		, city = '$city'
		, age = '$age'
		, contacts = '$contacts'
		, contraindication = '$contraindication'
		, room = '$room'
		, group_owner = $groupOwner
		, group_name = '$groupName'
		, group_id = $groupId

	WHERE id = $userid
	LIMIT 1";
query($sql);

$updated = (bool)affected_rows();

if(!$groupOwner){
	$sql = "UPDATE ".PREF."users
		SET group_id = 0
		WHERE group_id = $userid";
	query($sql);
}

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
	sendMailUserRegistration($email, $name);
}

redirect(isAdmin($editorid) ? "/table.php#{$email}" : "/edit.php?" . http_build_query(["email" => idToEmail($editorid), "justUpdated" => true]));

?>
