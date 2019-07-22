<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require_once("inc_vars_resolve.php"); // globalize POST, GET, & COOKIE  vars

/*
#
# If you want to use a different barcode format , activate the appropriate line of variable "$type" by removing the "#" at the start
# The default format is i2or5 when all lines here are deactivated.
#
# Take note that when you use a format other than i2of5, the barcode becomes longer (about twice the length of i2of5)
# so that you might need to either tweak the actual scripts to accomodate the new barcode length
# or use a finer  bar line resolution (1 pixel wide) by activating the line for the resolution variable "$xres". 
# The latter is recommended for its simplicity but you need to use at least a laser printer to print out your barcodes.
#
# Take note: When you have used different a format before, delete all the cached barcodes from the
#  /cache/barcodes/
# directory to enable the generation of the new barcode images
#
*/

# Code Interleaved 2 of 5 (default, you do not need to activate the line below if you use this format)
# $type = 'I25';

# Code 39 (activate the following line)
#  $type = 'C39';

# Code 128-A (activate the following line)
# $type = 'C128A';

# Code 128-B (activate the following line)
# $type = 'C128B';

# Code 128-C (activate the following line)
# $type = 'C128C';

# If you have chosen a format other that I25, you might need to activate this line too! To use a finer barcode line and a narrower barcode image.
# Do not forget to delete all cached barcode images from /cache/barcodes/ before you start using the new resolution!
#  $xres = 1;

/*
Barcode Render Class for PHP using the GD graphics library 
Copyright (C) 2001  Karim Mribti
								
   Version  0.0.7a  2001-04-01  
								
This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.
																  
This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.
											   
You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
																		 
Copy of GNU Lesser General Public License at: http://www.gnu.org/copyleft/lesser.txt
													 
Source code home page: http://www.mribti.com/barcode/
Contact author at: barcode@mribti.com
*/
  
  define (__TRACE_ENABLED__, false);
  define (__DEBUG_ENABLED__, false);
  
  require("barcode.php");  
  require("i25object.php");
  require("c39object.php");
  require("c128aobject.php");
  require("c128bobject.php");
  require("c128cobject.php");
              			   
  if (!isset($style))  $style   = BCD_DEFAULT_STYLE;
  if (!isset($width))  $width   = BCD_DEFAULT_WIDTH;
  if (!isset($height)) $height  = BCD_DEFAULT_HEIGHT;
  if (!isset($xres))   $xres    = BCD_DEFAULT_XRES;
  if (!isset($font))   $font    = BCD_DEFAULT_FONT;

  include("../dbconn.inc.php");
  $conn->debug = false;

  // strip dot and slash
  $r_idbarang = substr($_REQUEST["code"],0,strpos($_REQUEST["code"],"."));
  $r_noseri = (int)substr($_REQUEST["code"],strpos($_REQUEST["code"],".")+1,4);
  $r_idunit = (int)$_REQUEST["idunit"];
  $code = str_replace(".","",$_REQUEST["code"]);

  // SELECT SQL ------------------------------------
  $strSQL = "select d.idunit, u.namaunit, d.idbarang, d.noseri, d.namabarang, d.refidlokasi, l.namalokasi, d.tglterima, d.idsumberdana 
            from in_seri d left join in_lokasi l on d.idunit = l.idunit and d.refidlokasi = l.idlokasi 
            join ms_unit u on d.idunit = u.kodeunit 
            where d.idunit = '$r_idunit' and d.idbarang = '$r_idbarang' and d.noseri = $r_noseri";

  // Execute SQL
   $rs = $conn->Execute($strSQL);

   if (!$rs->EOF) {
	$c_idunit = $rs->fields["idunit"];
	$c_namaunit = $rs->fields["namaunit"];
	$c_idbarang = $rs->fields["idbarang"];
	$c_noseri = str_pad($rs->fields["noseri"], 4, "0", STR_PAD_LEFT);
	$c_namabarang = $rs->fields["namabarang"];
	$c_idlokasi = $rs->fields["refidlokasi"];
	$c_namalokasi = $rs->fields["namalokasi"];
	$c_tglterima = date("d-M-y",strtotime($rs->fields["tglterima"])); 
	$c_idsumberdana = $rs->fields["idsumberdana"];

  switch ($type)
  {
    case "I25":
			  $obj = new I25Object($width, $height, $style, $code);
			  break;
    case "C39":
			  $obj = new C39Object($width, $height, $style, $code);
			  break;
    case "C128A":
			  $obj = new C128AObject($width, $height, $style, $code);
			  break;
    case "C128B":
			  $obj = new C128BObject($width, $height, $style, $code);
			  break;
    case "C128C":
			$obj = new C128CObject($width, $height, $style, $code);
			  break;
	default:
			//echo "Need bar code type ex. C39";
			
			# Default format is I25

			$obj = new I25Object($width, $height, $style, $code);
			$obj = false;
  }
   
  if ($obj) {
      $obj->SetFont($font);   
      $obj->DrawObject($xres);
      $bImage = $obj->GetBarCodeImage();
      $lImage = @ImageCreateFromPNG ("logo.png"); 
      $pImage = @ImageCreate (350, 135);
      $bgcolor = ImageColorAllocate ($pImage, 255, 255, 255);
      $penred = ImageColorAllocate ($pImage, 233, 14, 91);
      $penblue = ImageColorAllocate ($pImage, 0, 0, 255);
      $penblack = ImageColorAllocate ($pImage, 0, 0, 0);

      // get Logo Image
      ImageCopy ($pImage, $lImage, 10, 35, 0, 0, 64, 64);
      // get Bar Code Image
      ImageCopy ($pImage, $bImage, 220, 90, 0, 0, 125, 35);

      // Draw Text
      $fontPath = Config::dirPath."data/barcode/twcent.ttf";
      ImageTTFText ($pImage, 10, 0, 40, 20, $penblack, $fontPath, "UNIVERSITAS AIRLANGGA (UNAIR)");
      ImageTTFText ($pImage, 12, 0, 90, 50, $penblack, $fontPath, $c_idbarang . " (" . $c_noseri . ") - " . $c_idsumberdana);
      ImageTTFText ($pImage, 12, 0, 90, 70, $penblack, $fontPath, $c_namabarang);
      ImageTTFText ($pImage, 10, 0, 90, 90, $penblack, $fontPath, $c_idlokasi . " " . $c_namalokasi);
      ImageString ($pImage, 2, 15, 102,  $c_namaunit, $penblack);
      ImageString ($pImage, 2, 15, 115,  $c_tglterima, $penblack);

      // Draw Border
      //ImageRectangle ($pImage, 0, 0, 349, 134, $penblack);

      // Write Image
      Header("Content-Type: image/png");
      ImagePNG($pImage);

      // clean up
      ImageDestroy($pImage);
      ImageDestroy($bImage);
      ImageDestroy($lImage);
      //$obj->FlushObject();
      $obj->DestroyObject();
      unset($obj); 
  }
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

?>
