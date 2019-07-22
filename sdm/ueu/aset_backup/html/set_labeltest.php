<?php
/*
    header('Content-Type: image/png');
    $im = @imagecreate(110, 20)
        or die('Cannot Initialize new GD image stream');
    $background_color = imagecolorallocate($im, 0, 0, 0);
    $text_color = imagecolorallocate($im, 233, 14, 91);
    imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);
    imagepng($im);
    imagedestroy($im);
*/
require_once('../../helpers/label.class.php');
//echo 'ok';
$row = array();
$row['noseri'] = '000001';
$row['idbarang'] = '3020104001';
$row['namabarang'] = 'Sepeda Motor';
$row['sumberdana'] = 'SPP';
$row['tglperolehan'] = '17 Jun 2013';

Label::cetak($row);

?>

