<?php

include "include/config.php";

$editorid = loginbycookie();

$sql = "SELECT u.*
  FROM ".PREF."users AS u
  WHERE u.active=1
    AND u.name<>''
    AND u.publicity<>'nothing'
    AND u.block IN ('gryffindor', 'slytherin', 'ravenclaw', 'hufflepuff')
    AND u.character_name<>''
    AND u.status IN ('query', 'participant')
  ORDER BY u.block ASC, u.character_age ASC";
$result = query($sql);
$userData = fetch_assocs($result);

$romanNumerals =
  [ 1 => "I"
  , 2 => "II"
  , 3 => "III"
  , 4 => "IV"
  , 5 => "V"
  , 6 => "VI"
  , 7 => "VII"
  ];

$faculties = [];
foreach($userData as $user){
  $block = $user["block"];
  $grade = computeSchoolYears($user["character_age"])["grade"];
  $gradeRoman = isset($romanNumerals[$grade]) ? $romanNumerals[$grade] : "Неизвестный";
  $speciality = $user["speciality"];

  $faculties[$block][$gradeRoman][] =
    [ "name"       => $user["character_name"]
    , "speciality" => $speciality
    ];

  if($speciality){
    foreach(explode(",", $speciality) as $spec){
      $specs[$spec][] =
        [ "name"       => $user["character_name"]
        , "speciality" => $speciality
        ];
      }
    }
  }

foreach($specs as &$students){
  usort($students, function($a, $b){return strnatcmp($a["name"], $b["name"]);});
  }
uasort($specs, function($a, $b){return count($b) - count($a);});

$render_data = [
  "faculties"    => $faculties,
  "blocks"       => $langBlocks,
  "specs"        => $specs,
  "specialities" => $langSpecialities,
  "isAdmin"      => (bool)isAdmin($editorid),
  "isLoggedIn"   => (bool)$editorid,
  ];

$ret = constructTwig()->render("students.twig", $render_data);

echo $ret;

?>
