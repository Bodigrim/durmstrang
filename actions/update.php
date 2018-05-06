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

function prepareDiff($old, $new){
	$diff = Diff::compare($old, $new);

	$diffSkipped = [];
	$ellipsis = ["…", Diff::UNMODIFIED];
	foreach($diff as $key => $word){
		$skip = $word[1] == Diff::UNMODIFIED;
		foreach([-3, -2, -1, 1, 2, 3] as $shift){
			$skip = $skip && (!isset($diff[$key + $shift]) || $diff[$key + $shift][1] == Diff::UNMODIFIED);
			}
		if($skip){
			$lastWord = count($diffSkipped) ? $diffSkipped[count($diffSkipped) - 1] : ["", Diff::UNMODIFIED];
			if($lastWord != $ellipsis){
				$diffSkipped[] = $ellipsis;
				}
			} else {
			$diffSkipped[] = $word;
			}
		}

	return $diffSkipped;
	}

function sendMailUpdatedApplication($old, $new){
	global $langPublicities, $langBloods, $langBlocks;
	$renderData =
		[ "old"         => $old
		, "new"         => $new
		, "diff"        =>
			[ "contraindication" => prepareDiff($old["contraindication"], $new["contraindication"])
			, "quenta"           => prepareDiff($old["quenta"], $new["quenta"])
			, "fear"             => prepareDiff($old["fear"], $new["fear"])
			, "possesions"       => prepareDiff($old["possesions"], $new["possesions"])
			, "charms"           => prepareDiff($old["charms"], $new["charms"])
			, "potions"          => prepareDiff($old["potions"], $new["potions"])
			, "abilities"        => prepareDiff($old["abilities"], $new["abilities"])
			, "wish"             => prepareDiff($old["wish"], $new["wish"])
			, "antiwish"         => prepareDiff($old["antiwish"], $new["antiwish"])
			, "addendum"         => prepareDiff($old["addendum"], $new["addendum"])
			, "master_note"      => prepareDiff($old["master_note"], $new["master_note"])
			, "quenta_public"    => prepareDiff($old["quenta_public"], $new["quenta_public"])
			]
		, "publicities" => $langPublicities
		, "bloods"      => $langBloods
		, "blocks"      => $langBlocks
		];
	$adminSubject = "Заявка обновлена";
	$adminText = constructTwig()->render("mails/updated-application.twig", $renderData);
	send_mail_to_admin($adminSubject, $adminText);
}

function sendMailGroupMemberChange($userid, $userMail, $userName, $oldGroupid, $newGroupid){
	if($oldGroupid){
		sendMailGroupMemberLeft($userid, $userMail, $userName, $oldGroupid);
	}
	if($newGroupid){
		sendMailGroupMemberAdded($userid, $userMail, $userName, $newGroupid);
	}
}

function fetchUserData($userid){
	$sql = "SELECT u.*
		FROM ".PREF."users AS u
		WHERE u.id = $userid
		LIMIT 1";
	$result = query($sql);
	return fetch_assoc($result);
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
$charms           = $post_get->getvar("charms");
$potions          = $post_get->getvar("potions");
$abilities        = $post_get->getvar("abilities");
$addendum         = $post_get->getvar("addendum");
$block            = $post_get->getvar("block");
$wish             = $post_get->getvar("wish");
$antiwish         = $post_get->getvar("antiwish");
$master_note      = $post_get->getvar("master_note");
$quenta_public    = $post_get->getvar("quenta_public");
$is_photo_public  = $post_get->getcheck("is_photo_public");

$oldUserData = fetchUserData($userid);
$oldName = $oldUserData["name"];

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
		, charms = '$charms'
		, potions = '$potions'
		, abilities = '$abilities'
		, addendum = '$addendum'
		, block = '$block'
		, wish = '$wish'
		, antiwish = '$antiwish'
		, quenta_public = '$quenta_public'
		, is_photo_public = '$is_photo_public'

	WHERE id = $userid
	LIMIT 1";
query($sql);

$updated = (bool)affected_rows();

if(isAdmin($editorid)){
	$sql = "UPDATE ".PREF."users
		SET master_note = '$master_note'
		WHERE id = $userid
		LIMIT 1";
	query($sql);
	$updatedMaster_note = (bool)affected_rows();
	}
else {
	$updatedMaster_note = false;
	}


if($updated || $updatedMaster_note){
	$newUserData = fetchUserData($userid);
	sendMailUpdatedApplication($oldUserData, $newUserData);
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
	foreach(glob("../photos-low/*" . $filename) as $previewName) {
		unlink($previewName);
	}

	$updatedPhoto = true;
	} else {
	$updatedPhoto = false;
	}

if($updated || $updatedPhoto){
	$link = $_SERVER["HTTP_HOST"] . "/edit.php?" . http_build_query(["email" => $email]);
	if($editorid==emailToId($email)){
		markUpdated(emailToId($email));
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
