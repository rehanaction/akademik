<?
        $imgdir = 'images';
	if($r_format == 'doc' or $r_format == 'xls') {
		$imgurl = $_SERVER['SERVER_ADDR'].$_SERVER['SCRIPT_NAME'];
		$pos = strrpos($imgurl,'/');
		$imgurl = substr($imgurl,0,$pos);
		
		$imgdir = 'http://'.$imgurl.'/'.$imgdir;
	}
        
?>
<table width="<?=$p_tbwidth?>" cellpadding="4" cellspacing="0" style="border:thin" border="1">
	<tr>
    	<td width="70" height="70"><img src="<?= $imgdir?>/esaunggul.png"></td>
        <td>
        	<font size="+2">
                <strong><?= $conf['namauniversitas']?></strong>
            </font>
            <br>
            <?= $conf['alamat']?><br>            
            <?= $conf['alamatb']?><br>
             <?= $conf['alamatc']?><br>     
			
        </td>
    </tr>
</table>
