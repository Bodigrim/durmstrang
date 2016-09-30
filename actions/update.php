<?php

include "../include/config.php";

$post_get = new GetVarClass();
$email = $post_get->getemail("email");

if(!$email)
	die("Редактирование заявки невозможно: введите корректный e-mail. ");

$editorid = loginbycookie();
if(!canEdit($editorid, $email))
	die("У вас недостаточно прав доступа, чтобы редактировать заявку {$email}. ");

$name             = $post_get->getvar("name");
$nick             = $post_get->getvar("nick");
$city             = $post_get->getvar("city");
$age              = $post_get->getadate("age");
$contacts         = $post_get->getvar("contacts");
$contraindication = $post_get->getvar("contraindication");
$chronicdesease   = $post_get->getvar("chronicdesease");
$wishes           = $post_get->getvar("wishes");
$publicity        = $post_get->getenumkeys("publicity", $langPublicities);
$character_name   = $post_get->getvar("character_name");
$character_age    = $post_get->getadate("character_age");
$country          = $post_get->getenumkeys("country", $langCountries);
$birth            = $post_get->getenumkeys("birth", $langBirthes);
$rank             = $post_get->getenumkeys("rank", $langRanks);
$quota            = $post_get->getenumkeys("quota", $langQuotas);
$quenta           = $post_get->getvar("quenta");
$wishes2          = $post_get->getvar("wishes2");
$go_royal_wedding = $post_get->getvar("go_royal_wedding", "0|1", "0");

if(isAdmin($editorid)){
	$master_note = $post_get->getvar("master_note");
	}
else {
	$sql = "SELECT master_note
		FROM ".PREF."users
		WHERE email='$email'
		LIMIT 1";
	$master_note = (string)db_result00($sql);
	}

$sql = "UPDATE ".PREF."users
	SET name='$name',
		nick='$nick',
		city='$city',
		age='$age',
		contacts='$contacts',
		contraindication='$contraindication',
		chronicdesease='$chronicdesease',
		wishes='$wishes',
		publicity='$publicity',
		character_name='$character_name',
		character_age='$character_age',
		country='$country',
		birth='$birth',
		rank='$rank',
		quota='$quota',
		quenta='$quenta',
		wishes2='$wishes2',
		master_note='$master_note',
		go_royal_wedding=$go_royal_wedding

	WHERE email='$email'
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
		rename($filename, $options->neoname);
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
		send_mail_by_userid(emailToId($email), "Мастер отредактировал вашу заявку", "<a href=\"$link\">Просмотреть</a>");
		}
	}

redirect(isAdmin($editorid) ? "/table.php#{$email}" : "/edit.php?" . http_build_query(["email" => idToEmail($editorid), "justUpdated" => true]));

?>
