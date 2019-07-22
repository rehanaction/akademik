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
    	<td width="70" height="70"><img src="<?= $imgdir?>/logo.jpg" width="100%"></td>
        <td>
        	<font size="+1">
                <strong>STIE INABA</strong>
            </font>
            <br>
            Jl. Soekarno Hatta No.448 Bandung, Jawa Barat 40266
			<br>
            Telp. (022) 7563919
            <br>
            Homepage: www.inaba.ac.id
            <br>
            Email: info@inaba.ac.id
        </td>
    </tr>
</table>
