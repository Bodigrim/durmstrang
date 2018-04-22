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

class House {
  public $id;
  public $name;
  public $capacity;
  public $left;
  public $top;

  function __construct($id, $name, $capacity, $left, $top){
    $this->id       = $id;
    $this->name     = $name;
    $this->capacity = $capacity;
    $this->left     = $left;
    $this->top      = $top;
  }
}

$housesList =
  [ new House( 0, "Лестница к воде", 99, 65,    0)
  , new House( 1, "Кукушка",          3, 14,   40)
  , new House( 2, "Охотник",          3, 29,   40)
  , new House( 3, "Дубок",            3, 52,  120)
  , new House( 4, "Вход",             0, 14, 2200)
  , new House( 5, "Рыболов",          3, 86,   40)
  , new House( 6, "?",                3, 86,  200)
  , new House( 7, "Спутник",          3, 86,  400)
  , new House( 8, "Руслан",           6, 86,  600)
  , new House( 9, "Ивасик",           6, 86,  800)
  , new House(10, "Зал. Бильярд",     0, 83, 1000)
  , new House(11, "Мастерка",         6, 77, 1200)
  , new House(12, "Солнышко",         3, 71, 1400)
  , new House(13, "Лес",              0, 64, 1600)
  , new House(14, "Цистерна",         0, 57, 1860)
  , new House(15, "Развалюха",        0, 43, 2000)
  , new House(16, "Ночь",             6,  7,  200)
  , new House(17, "Березки",          6,  0,  400)
  , new House(18, "Боровик",          3,  0,  560)
  , new House(19, "Пролисок",         3,  0,  720)
  , new House(20, "Про...",           3,  0,  880)
  , new House(21, "Парус",            3,  0, 1040)
  , new House(22, "Мишка",            5,  6, 1200)
  , new House(23, "Муха",             5,  4, 1360)
  , new House(24, "Сторожка",         4,  4, 1520)
  , new House(25, "Туалет",           0,  0, 1680)
  , new House(26, "Душ",              0,  3, 1840)
  , new House(27, "Лес",              0,  3, 2000)
  , new House(28, "Мрия",             3,  0,   40)
  , new House(29, "Конек",            5, 34,  320)
  , new House(30, "Кр. Шапочка",      5, 34,  480)
  , new House(31, "Кот",              5, 34,  640)
  , new House(32, "Петушок",          5, 34,  800)
  , new House(33, "Иван",             6, 34,  960)
  , new House(34, "Гуси-лебеди",      3, 34, 1130)
  , new House(35, "Буратино",         5, 43, 1240)
  , new House(36, "Песня",            6, 26, 1280)
  , new House(37, "Мойдодыр",         5, 47,  320)
  , new House(38, "Детская площадка", 0, 48,  640)
  , new House(39, "Теннисный стол",   0, 47,  840)
  , new House(40, "Эстакада",         0, 38, 1600)
  , new House(41, "Три метлы",        4, 29, 1840)
  , new House(42, "Светлячок",        3, 42,   40)
  ];

?>
