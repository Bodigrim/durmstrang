<?php
if(!defined("INCMS")) die();

/* An argument func can be a callback function or simply SQL-query */
function gSomeOptions($key, $func){
	global $_CACHE;

	if(isset($_CACHE[$key]))
		return $_CACHE[$key];

	if(is_string($func)){
		$result = query($func);
		$ret = [];
		while(list($id, $name) = fetch_row($result)){
			$ret[$id] = $name;
			}
		}
	else {
		$ret = $func();
		}

	$_CACHE[$key] = $ret;
	return $ret;
	}

function convOptions2Html($options, $old, $withempty){
	$ret = "";

	if($withempty){
		if($old)
			$ret.= "<option value=\"0\">---</option>\n";
		else
			$ret.= "<option selected value=\"0\">---</option>\n";
		}

	foreach($options as $id=>$name){
		$safename = htmlspecialchars($name);
		$temp = $id==$old ? "selected" : "";
		$ret.= "<option $temp value=\"$id\">$safename</option>\n";
		}

	return $ret;
	}

$langStatuses = [
	"query"    => "На рассмотрении",
	"rejected" => "Отклонена",
	"accepted" => "Утверждена",
	"archive"  => "Архив"
	];

$langPublicities = [
	"name" => "Фамилию и имя",
	"nick" => "Ник",
	"null" => "Укажите просто «роль занята»",
	];

$langCountries = [
	"Беларусь" => "Беларусь",
  "Болгария" => "Болгария",
  "Босния и Герцеговина" => "Босния и Герцеговина",
  "Венгрия" => "Венгрия",
  "Латвия" => "Латвия",
  "Литва" => "Литва",
  "Македония" => "Македония",
  "Молдова" => "Молдова",
  "Румыния" => "Румыния",
  "Польша" => "Польша",
  "Сербия" => "Сербия",
  "Словакия" => "Словакия",
  "Словения" => "Словения",
  "Украина" => "Украина",
  "Хорватия" => "Хорватия",
  "Черногория" => "Черногория",
  "Чехия" => "Чехия",
  "Восточная Германия" => "Восточная Германия",
  "Косово" => "Косово",
  ];

$langBirthes = [
	"noble-magic" => "Магическая аристократическая семья",
	"magic"       => "Магическая незнатная семья",
	"mix"         => "Смешанная семья (маги и не-маги)",
	"simple"      => "Семья не-магов",
	];

$langRanks = [
	"student1" => "Студент 1 курса",
	"student2" => "Студент 2 курса",
	"student3" => "Студент 3 курса",
	"postdoc"  => "Аспирант",
	"tutor"    => "Преподаватель",
	"staff"    => "Технический персонал",
	];

$langQuotas = [
	"noble"     => "Квота для аристократов",
	"country"   => "Квота от страны",
	"jew"       => "Квота от иудейской общины",
	"gipsy"     => "Квота от цыганской общины",
	"solomonar" => "Договор с Крысоловами",
	"family"    => "Вне квоты, за счет семьи",
	"mecenat"   => "Вне квоты, за счет спонсора или мецената",
	"unknown"   => "Я преподаватель",
	];

?>
