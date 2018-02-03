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
  'query'               => "Предварительная заявка",
  'participant'         => "Полная заявка",
  'withdrawn'           => "Заявка снята игроком",
  'rejected'            => "Заявка отклонена",
	];

$ordStatuses = [
  'query'               => 0,
  'participant'         => 1,
  'withdrawn'           => 2,
  'rejected'            => 3,
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
	"other"    => "Другое",
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
	"no-quota"  => "Не учился в Дурмштранге",
	"other"     => "Другое",
	];

$langYesNo = [
	0 => "Нет",
	1 => "Да",
	];

$langPublicities = [
  "full"              => "ФИО, ник и соцсети",
  "no-facebook"       => "ФИО и ник",
  "nick-and-facebook" => "Ник и соцсети",
  "nick-only"         => "Только ник",
  "occupied"          => "Только «Роль занята»",
  "nothing"           => "Не указывать"
  ];

$langBloods = [
  "pureblood" => "Чистокровный",
  "halfblood" => "Полукровка",
  "mudblood"  => "Магглорожденный",
  ];

$langBlocks = [
  "teachers"     => "Сотрудники Хогвартса",
  "gryffindor"   => "Студенты Гриффиндора",
  "slytherin"    => "Студенты Слизерина",
  "ravenclaw"    => "Студенты Рейвенкло",
  "hufflepuff"   => "Студенты Хаффлпаффа",
  "hogsmeade"    => "Жители Хогсмида",
  "hogsmeade2"   => "Гости Хогсмида",
  "ministry"     => "Министерство Магии",
  "aurors"       => "Аврорат",
  ];

?>
