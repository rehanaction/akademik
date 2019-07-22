<?php
	// fungsi pembantu modul akademik
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class Label {
	    function cetak($p=""){
	        $p_dir = '/var/www/simueu/ueu/includes/barcode/';
	        //$p_dir = '/var/www/esaunggul/www/ueu/includes/barcode/';
            //require_once('aset.class.php');
	        //$p_dir = $conf['barcode_dir'];
	        
	        error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
            require_once($p_dir.'inc_vars_resolve.php'); // globalize POST, GET, & COOKIE  vars

            define (__TRACE_ENABLED__, false);
            define (__DEBUG_ENABLED__, false);

            require($p_dir.'barcode.php');
            require($p_dir.'i25object.php');
            require($p_dir.'c39object.php');
            require($p_dir.'c128aobject.php');
            require($p_dir.'c128bobject.php');
            require($p_dir.'c128cobject.php');
            
            if (!isset($style))  $style   = BCD_DEFAULT_STYLE;
            if (!isset($width))  $width   = BCD_DEFAULT_WIDTH;
            if (!isset($height)) $height  = BCD_DEFAULT_HEIGHT;
            if (!isset($xres))   $xres    = BCD_DEFAULT_XRES;
            if (!isset($font))   $font    = BCD_DEFAULT_FONT;
            
            $code = $p['idbarang1'].$p['noseri'];
            
            $obj = new I25Object($width, $height, $style, $code);
            //echo 'ok'; die();
            //echo 'ok'.'-'.$font.'-'.$xres.'-'.$width.'-'.$height.'-'.$style.'-'.$code; die();

            if($obj) {
                $obj->SetFont($font);   
                $obj->DrawObject($xres);
                $bImage = $obj->GetBarCodeImage();
                $lImage = @ImageCreateFromPNG ($p_dir.'logo.png'); 
                $pImage = @ImageCreate (320, 135);
                $bgcolor = ImageColorAllocate ($pImage, 255, 255, 255);
                $penred = ImageColorAllocate ($pImage, 233, 14, 91);
                $penblue = ImageColorAllocate ($pImage, 0, 0, 255);
                $penblack = ImageColorAllocate ($pImage, 0, 0, 0);

                // get Logo Image
                ImageCopy ($pImage, $lImage, 10, 0, 0, 0, 64, 64);
                // get Bar Code Image
                ImageCopy ($pImage, $bImage, 180, 72, 0, 0, 125, 45);

                // Draw Text
                $fontPath = $p_dir.'twcent.ttf';
                ImageTTFText ($pImage, 14, 0, 90, 25, $penblack, $fontPath, "Universitas Esa Unggul");
                ImageTTFText ($pImage, 14, 0, 75, 55, $penblack, $fontPath, $p['idbarang1']." (". Aset::setFormatNoSeri($p['noseri']) .")");
                //ImageTTFText ($pImage, 13, 0, 70, 73, $penblack, $fontPath, $p['namabarang']);
                ImageString ($pImage, 3, 20, 63,  $p['namabarang'], $penblack);
                ImageString ($pImage, 3, 20, 78,  $p['idlokasi'], $penblack);
                ImageString ($pImage, 2, 20, 95,  $p['sumberdana'], $penblack);
                ImageString ($pImage, 2, 20, 105,  $p['tglperolehan'], $penblack);
                ImageString ($pImage, 1, 205, 115,  $code, $penblack);

                // Draw Border
                ImageRectangle ($pImage, 0, 0, 319, 134, $penblack);

                // Write Image
                Header("Content-Type: image/png");
                ImagePNG($pImage);

                // clean up
                ImageDestroy($pImage);
                ImageDestroy($bImage);
                ImageDestroy($lImage);
                $obj->FlushObject();
                $obj->DestroyObject();
                unset($obj); 
            }
            else 
            {
                // Give Error Message
                $pImage = @ImageCreate (350, 135);
                $bgcolor = ImageColorAllocate ($pImage, 255, 255, 255);
                $penblack = ImageColorAllocate ($pImage, 0, 0, 0);
                ImageRectangle ($pImage, 0, 0, 349, 134, $penblack);
                ImageString ($pImage, 5, 75, 50,  "Image not available", $penblack);
                // Write Image
                Header("Content-Type: image/png");
                ImagePNG($pImage);
                ImageDestroy($pImage);
                die;
            }
            
	    }
	}
?>
