<table width="100%" cellpadding="4" cellspacing="0" style="border:thin" border="1" >
	<tr>
    	<td colspan="5">
        <table width="100%">
        	<tr>
            	<td width="15%">Kasir</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['nip']?>
                </td>
            </tr>
            <tr>
            	<td width="15%">Tgl Pembayaran</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=CStr::formatDate($data['tglbayar'])?>
                </td>
            </tr>
            <tr>
            	<td width="15%">No. Kuitansi</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['refno']?> &nbsp;&nbsp;
                <? if($data){?>
                <span style="cursor:pointer" onClick="showPage('notoken','<?= Route::navAddress('rep_payment') ?>')"> 
                <font color="#0000FF"><u>( Cetak Kwitansi )</u></font>
                </span>
                <? }?>
                </td>
            </tr>
            <tr>
            	<td width="15%">Periode Bayar</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['periodebayar']?> 
                </td>
            </tr>
        </table>
        </td>
    </tr>
    <tr>
        <td colspan="5" bgcolor="#CCCCCC"><strong>Detail Pembayaran</strong></td>
    </tr>
    <tr bgcolor="#CCCCCC">
        <th width="15%">Id Tagihan</th>
        <th width="15%">Periode Tagihan</th>
        <th width="15%">Jumlah Tagihan</th>
        <th width="15%">Jumlah Bayar</th>
    </tr>
    <? if($datadetail)
       //foreach($datadetail as $i => $tagihan){
       $tagihan = $datadetail;s
       ?>
    <tr>
        <td><?=$tagihan['idtagihan']?></td>
        <td><?=$tagihan['bulantahun']?$tagihan['bulantahun']:$tagihan['periode']?></td>
        <td align="right"><?=CStr::FormatNumber($tagihan['jumlahbayar'])?></td>
        <td align="right"><?=CStr::FormatNumber($tagihan['jumlahuang'])?>
        </td>
    </tr>
        <? //}?>
   
    <tr>
        <th style="text-align:right" colspan="3" align="right">TOTAL</th>
        <th style="text-align:right">
            <?=CStr::FormatNumber($data['jumlahbayar'])?>
        </th>
    </tr>
    <tr>
        <th style="text-align:right" colspan="3" align="right">Jumlah Uang</th>
        <th style="text-align:right">
         	<?=CStr::FormatNumber($data['jumlahuang'])?>
        </th>
    </tr>
    <tr>
        <th style="text-align:right" colspan="3" align="right">Cash Back</th>
        <th style="text-align:right">
        	<?=CStr::FormatNumber($data['jumlahuang']-$data['jumlahbayar'])?>
        </th>
    </tr>
        	<input type="hidden" name="idpembayaran" id="idpembayaran" value="<?=$data['idpembayaran']?>" />
</table>
