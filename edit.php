<?php

function computeSchoolYears($birthday){
	$date = strptime($birthday, "%Y-%m-%d");
	$year = 1900 + $date["tm_year"];
	$month = 1 + $date["tm_mon"];
	$from  = $month < 9 ? $year + 11 : $year + 12;
	$to    = $from + 7;
	$grade = $from >= 1968 && $from <= 1974 ? 1975 - $from : "";
	return ["from" => $from, "to" => $to, "grade" => $grade];
}

include "include/config.php";

$post_get    = new GetVarClass("_GET");
$email       = $post_get->getemail("email");
$justUpdated = $post_get->getvar("justUpdated");
$print       = $post_get->getvar("print");

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
	"schoolYears"      => computeSchoolYears($userData["character_age"]),
  ];

$template = $print ? "print.twig" : "edit.twig";

$ret = constructTwig()->render($template, $render_data);

echo $ret;

?>
