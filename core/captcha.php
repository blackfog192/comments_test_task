<?php
  
  /**
  * Generation and output of captcha
  */

  $letters = 'ABCDEFGKIJKLMNOPQRSTUVWXYZ1234567890'; // captcha symbols

  $caplen = 6; // Number of captcha characters
  $width = 120; $height = 40; // Size of captcha
  $font = '../template/fonts/happy_monkey.ttf'; // Font
  $fontsize = 17; // Font size of captcha

  header('Content-type: image/png'); 

  $im = imagecreatetruecolor($width, $height); 
  imagesavealpha($im, true); 
  $bg = imagecolorallocatealpha($im, 0, 0, 0, 127);
  imagefill($im, 0, 0, $bg); 

  $captcha = '';
  for ($i = 0; $i < $caplen; $i++) {
      $captcha .= $letters[rand(0, strlen($letters)-1)];
      $x = ($width - 20) / $caplen * $i + 10;
      $x = rand($x, $x + 4);
      $y = $height - (($height - $fontsize) / 2);
      $curcolor = imagecolorallocate($im, rand(0, 100), rand(0, 100), rand(0, 100));
      $angle = rand(-25, 25);
      imagettftext($im, $fontsize, $angle, $x, $y, $curcolor, $font, $captcha[$i]);
  }

  session_start();
  $_SESSION['captcha'] = $captcha;

  imagepng($im);
  imagedestroy($im);

?>