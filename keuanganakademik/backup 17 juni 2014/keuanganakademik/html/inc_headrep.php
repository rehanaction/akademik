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
    	<td width="70" height="70"><img src="images/uwp.gif"></td>
        <td align="center">
        	<font size="+2">
                <strong>UNIVERSITAS NAHDLATUL ULAMA SURABAYA</strong>
            </font>
            <br>
            Kampus A : Jl. SMEA No. 57 Surabaya
            <br>
			Kampus B : Jl. Raya Jemursari 51-57 Surabaya
			<br>
            Telp. (031) 8291920 Fax. (031) 8291920 http://unusa.ac.id
        </td>
    </tr>
</table>