<?php
if(!defined("INCMS")) die();

function redirect($url="", $die=1){

	if(!$url)
		$url = $_SERVER["REQUEST_URI"];

	$safeurl = htmlspecialchars($url);

	if(DEBUG_REDIRECT)
		throw new Exception("<br/>Redirected to <a href=\"$safeurl\">$url</a>");

	if(!headers_sent()){
		header("Location: $url");
		}
	else {
		echo "<script>location='$safeurl'</script>";
		}

	if($die)
		die();
	}

function redirect_back($default=""){
	if(!$default && isset($_SERVER['HTTP_REFERER'])){
		$default = $_SERVER['HTTP_REFERER'];
		}

	$req_get = new GetVarClass("_REQUEST");
	$back = $req_get->getrelurl("back", $default);

	if($back){
		redirect($back);
		}
	else {
		die("Cannot redirect back!");
		}
	}

function bcrypt($password, $salt=BCRYPT_SALT, $strength=BCRYPT_STRENGTH) {
	if(strlen($salt)!=22)
		throw new Exception("Salt $salt have length non equal to 22!");
	$ret = crypt($password, '$2a$' . $strength . '$' . $salt);
	$ret = substr($ret, 29);
	return $ret;
	}

function bcrypt_by_id($password, $id){
	$salt = substr(bcrypt($id), 0, 22);
	return bcrypt($password, $salt);
	}

function antispamhash($id){
	$ret = bcrypt($id, ANTISPAM_SALT, "04");
	return $ret;
	}

function photoFileName($email){
	return strtr(antispamhash($email), "/", "_");
	}

function array_flatten($ar){
	return array_reduce($ar, "array_merge", []);
	}

function array_transpose($array){
	array_unshift($array, null);
	return call_user_func_array('array_map', $array);
	}

function tidyHtml($html){
  $config = [
    "indent" => 2,
    "clean" => false,
    "char-encoding" => "utf8",
    ];

  $tidy = new tidy;
  $tidy->parseString($html, $config, 'utf8');
  $tidy->cleanRepair();

  $ret = $tidy->html()->child[1]->value;
  $ret = substr($ret, 7, -7);

  return $ret;
  }

function tidyEscaper($twig, $str, $charset){
	return tidyHtml($str);
	}

function constructTwig(){
	static $twig;

	if($twig)
		return $twig;

	$template_dir = "twig/templates";
	$cache_dir = "twig/cache";
	if(!file_exists($template_dir)){
		$template_dir = "../" . $template_dir;
		$cache_dir = "../" . $cache_dir;
		}
	$loader = new Twig_Loader_Filesystem($template_dir);
	$twig = new Twig_Environment($loader, [
		"cache" => $cache_dir,
		"auto_reload" => true,
		]);
	$twig->addExtension(new Twig_Extensions_Extension_I18n());
	$twig->getExtension('core')->setEscaper('tidy', 'tidyEscaper');

	return $twig;
	}

function set_login_cookies($userid, $email, $pwhash){
	$cookie_get = new GetVarClass("_COOKIE");
	$session = $cookie_get->getvar("session", "[0-9a-f]+");

	if(!$session){
		$session = antispamhash(mt_rand());
		}

	$ip = $_SERVER['REMOTE_ADDR'];
	$page = db_escape($_SERVER['REQUEST_URI']);
	$valid = date("Y-m-d H:i:s", time() + 7 * 86400);
	$sql = "INSERT INTO ".PREF."sessions (userid, name, valid, ip, page, pwhash)
		VALUE ($userid, '$session', '$valid', INET_ATON('$ip'), '$page', '$pwhash')
		ON DUPLICATE KEY UPDATE valid='$valid', ip=INET_ATON('$ip'), page='$page', pwhash='$pwhash'";
	query($sql);

	cookie_set_httponly("email",   $email,   false, "/");
	cookie_set_httponly("session", $session, false, "/");
	}

class UserRenderData
{
	private $name;
	private $email;
	private $password;

	function __construct($userid){
		$sql = "SELECT name, email, pw
			FROM ".PREF."users
			WHERE active=1 AND id=$userid
			LIMIT 1";
		$result = query($sql);
		$rows = num_rows($result);
		if(!$rows)
			throw new Exception("UserRenderData->__construct: userid {$userid} not found");

		list($this->name, $this->email, $this->password) = fetch_row($result);
	}

	function toArray(){
		return
			[ "userName"     => $this->name
			, "userEmail"    => $this->email
			, "userPassword" => $this->password
			];
	}

}

function remindPassword($userid){
	$renderData = (new UserRenderData($userid))->toArray();

	$userSubject = "Ты забыл пароль, но это не беда";
	$userText = constructTwig()->render("mails/password-reminder.twig", $renderData);
	send_mail_by_userid($userid, $userSubject, $userText);
	}

function sendMailAfterUserRegistration($userid){
	$renderData = (new UserRenderData($userid))->toArray();

	$userSubject = "Ты зарегистрировался на РИ «Хогвартс. Наследие. 1975»";
	$userText = constructTwig()->render("mails/user-registration.twig", $renderData);
	send_mail_by_userid($userid, $userSubject, $userText);
	}

function sendMailAfterUserCompletion($userid){
	$renderData = (new UserRenderData($userid))->toArray();

	$adminSubject = "Зарегистрирован новый пользователь";
	$adminText = constructTwig()->render("mails/user-registration-admin.twig", $renderData);
	send_mail_to_admin($adminSubject, $adminText);
}

function loginbycookie(){
	$cookie_get = new GetVarClass("_COOKIE");

	$email   = $cookie_get->getvar("email");
	$session = $cookie_get->getvar("session");
	if(!$email || !$session)
		return 0;

	$sql = "SELECT u.id
		FROM ".PREF."sessions AS s
		INNER JOIN ".PREF."users AS u ON u.id=s.userid
		WHERE u.email='$email' AND s.name='$session' AND s.pwhash=u.pwhash
			AND s.valid>NOW() AND u.active='1'
		LIMIT 1";
	$result = query($sql);
	$userid = (int)db_result00($sql);

	if(rand(0,100)==0){
		$sql = "DELETE FROM ".PREF."sessions
			WHERE valid<NOW()";
		query($sql);
		}

	return $userid;
	}

function emailToId($email){
	$email = (string)$email;
	$sql = "SELECT id
		FROM ".PREF."users
		WHERE active=1 AND email='$email'
		LIMIT 1";
	$ret = (int)db_result00($sql);
	return $ret;
	}

function idToEmail($id){
	$id = (int)$id;
	$sql = "SELECT email
		FROM ".PREF."users
		WHERE active=1 AND id=$id
		LIMIT 1";
	$ret = (string)db_result00($sql);
	return $ret;
	}

function groupIdToGroupName($id){
	$id = (int)$id;
	$sql = "SELECT group_name
		FROM ".PREF."users
		WHERE active=1 AND id=$id
		LIMIT 1";
	$ret = (string)db_result00($sql);
	return $ret;
	}

function isAdmin($id){
	$id = (int)$id;
	$sql = "SELECT is_admin
		FROM ".PREF."users
		WHERE active=1 AND id=$id
		LIMIT 1";
	$ret = (bool)db_result00($sql);
	return $ret;
	}

function markUpdated($id){
	$id = (int)$id;
	$sql = "UPDATE ".PREF."users
		SET updated=1
		WHERE id=$id
		LIMIT 1";
	query($sql);
	}

function unmarkUpdated($id){
	$id = (int)$id;
	$sql = "UPDATE ".PREF."users
		SET updated=0
		WHERE id=$id
		LIMIT 1";
	query($sql);
	}

function markUnread($id){
	$id = (int)$id;
	$sql = "UPDATE ".PREF."users
		SET unread=1
		WHERE id=$id
		LIMIT 1";
	query($sql);
	}

function unmarkUnread($id){
	$id = (int)$id;
	$sql = "UPDATE ".PREF."users
		SET unread=0
		WHERE id=$id
		LIMIT 1";
	query($sql);
	}

function canEdit($editorid, $email){
	$userid = emailToId($email);

	$ret = isAdmin($editorid) || $editorid==$userid;
	return $ret;
	}

function sendDownloadHeaders($contentType, $filename){
  if(headers_sent())
    throw new Exception("sendDownloadHeaders: headers has been already sent");

  header("Content-Type: $contentType");
  header("Content-Disposition: attachment;filename=\"$filename\"");
  header('Cache-Control: max-age=0');
}

function multspell($n, $form1, $form2, $form3){
  /* анкета, анкеты, анкет */
  return $n >= 5 && $n <= 20 || $n % 10 > 4 || $n % 10 == 0
  	? $form3
  	: ($n % 10 == 1 ? $form1 : $form2);
  }

?>
