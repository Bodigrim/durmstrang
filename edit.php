<?php

include "include/config.php";

$post_get = new GetVarClass("_GET");
$email    = $post_get->getemail("email");
$justUpdated = $post_get->getvar("justUpdated");

if(!$email)
	die("Редактирование заявки невозможно: введите корректный e-mail.  Вернитесь на <a href=\"/\">главную страницу</a>.");

$editorid = loginbycookie();
if(!canEdit($editorid, $email))
	die("У вас недостаточно прав доступа, чтобы редактировать заявку {$email}. Вернитесь на <a href=\"/\">главную страницу</a>.");

$sql = "SELECT *
	FROM ".PREF."users
	WHERE email='$email'
	LIMIT 1";
$result = query($sql);
$userData = fetch_assoc($result);
$photoname = photoFileName($email);

$userid = (int)emailToId($email);

if(isAdmin($editorid)){
	unmarkUpdated($userid);
	unmarkUnread($userid);
	}

$sql = "SELECT m.id, u.name, m.message
	FROM ".PREF."messages AS m
	LEFT JOIN ".PREF."users AS u ON m.authorid=u.id
	WHERE m.userid=$userid
	ORDER BY m.id";
$result = query($sql);
$messages = fetch_assocs($result);

$render_data = $userData + [
	"justUpdated"      => (bool)$justUpdated,
	"isAdmin"          => (bool)isAdmin($editorid),
	"photo"            => file_exists("photos/$photoname.jpg") ? "$photoname.jpg" : "",
	"publicities"      => $langPublicities,
	"bloods"           => $langBloods,
	"blocks"           => $langBlocks,
	"messages"         => $messages,
  ];

$ret = constructTwig()->render("edit.twig", $render_data);

echo $ret;

?>
