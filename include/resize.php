<?php
if(!defined("INCMS")) die();

/*
 * Written by Maxim Chernyak (mx_)
 *
 * Please visit http://www.mediumexposure.com for more PHP stuff.
 * If you have any criticism, suggestions, improvements, or you simply liked the function, I would appreciate if you dropped me a note on my blog (mentioned above).
 *
 * Feel free to use anywhere, anyhow. : )
 *
 */

function readImg($filename, $type){
  switch($type){
  case IMAGETYPE_GIF:
    $image = imagecreatefromgif($filename);
    break;
  case IMAGETYPE_JPEG:
    $image = imagecreatefromjpeg($filename);
    break;
  case IMAGETYPE_PNG:
    $image = imagecreatefrompng($filename);
    break;
  default:
    die("Unsupported image type $type!");
    }
  return $image;
  }

function writeImg($image, $filename, $type){
  switch($type){
  case IMAGETYPE_GIF:
    $ret = imagegif($image, $filename);
    break;
  case IMAGETYPE_JPEG:
    $ret = imagejpeg($image, $filename);
    break;
  case IMAGETYPE_PNG:
    $ret = imagepng($image, $filename);
    break;
  default:
    throw new Exception("Unsupported image type $type!");
    }
  return $ret;
  }

function calcResizeSizes($in_width, $in_height, $out_width, $out_height, $proportional=false){
  if($proportional){
    $proportion = $in_width / $in_height;

    if($out_width > $out_height && $out_height != 0){
      return [$out_height * $proportion, $out_height];
      }
    elseif($out_width < $out_height && $out_width != 0){
      return [$out_width, $out_width / $proportion];
      }
    elseif($out_width == 0){
      return [$out_height * $proportion, $out_height];
      }
    elseif($out_height == 0){
      return [$out_width, $out_width / $proportion];
      }
    else{
      return [$out_width, $out_height];
      }
    }
  else {
    return [( $out_width <= 0 ) ? $in_width : $out_width, ( $out_height <= 0 ) ? $in_height : $out_height];
    }
  }

function resizeImg($image, $out_width, $out_height){
  $in_width  = imagesx($image);
  $in_height = imagesy($image);

  $image_resized = imagecreatetruecolor($out_width, $out_height);
  imagecolortransparent($image_resized, imagecolorallocate($image_resized, 0, 0, 0) );
  imagealphablending($image_resized, false);
  imagesavealpha($image_resized, true);
  imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $out_width, $out_height, $in_width, $in_height);

  return $image_resized;
  }

function smartResizeImg($infile, $outfile, $out_width=0, $out_height=0, $proportional=false){
  if(!file_exists($infile))
    throw new Exception("Infile $infile does not exist");

  $info = @getimagesize($infile);
  if($info===false)
    throw new Exception("Cannot getimagesize $infile");
  list($in_width, $in_height, $image_type) = $info;

  $image = readImg($infile, $image_type);
  list($final_width, $final_height) = calcResizeSizes($in_width, $in_height, $out_width, $out_height, $proportional);
  $image_resized = resizeImg($image, $final_width, $final_height);
  return writeImg($image_resized, $outfile, $image_type);
  }

function addWatermark($filename, $watermark_file="../design/watermark.png"){
  if(!file_exists($filename) || !file_exists($watermark_file))
    return false;

  $info = @getimagesize($filename);
  if($info===false){
    return false;
    }
  list($w, $h, $image_type) = $info;
  $image = readImg($filename, $image_type);

  $info = @getimagesize($watermark_file);
  if($info===false){
    return false;
    }
  list($ww, $hh, $watermark_type) = $info;
  $watermark = readImg($watermark_file, $watermark_type);

  $proportion = max( $ww / $w, $hh / $h );
  $neww = intval($ww / $proportion);
  $newh = intval($hh / $proportion);

  $watermark_resized = resizeImg($watermark, $neww, $newh);
  $ww = imagesx($watermark_resized);
  $hh = imagesy($watermark_resized);


  imagecopy($image, $watermark_resized, $w - $ww, 0, 0, 0, $ww, $hh);

  return writeImg($image, $filename, $image_type);
  }
?>
