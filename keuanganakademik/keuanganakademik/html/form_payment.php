<? if($data){?>
<table width="100%" cellpadding="4" cellspacing="0" style="border:thin" border="1" >
	<tr>
    	<td colspan="5">
        <table width="100%">
        	<tr>
            	<td width="15%">Petugas</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['nama']?>
                </td>
            </tr>
            <tr>
            	<td width="15%">Tgl Pembayaran</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?= CStr::formatDateInd($data['tglbayar']) ?>
                </td>
            </tr>
            <tr>
            	<td width="15%">No. Pembayaran</td>
                <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
                <?=$data['refno']?> &nbsp;&nbsp;
                <? if($data){?>
                <span style="cursor:pointer" onClick="showPage('notoken','<?= Route::navAddress('rep_payment2') ?>')"> 
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
        <th>Jenis Tagihan</th>
        <th width="15%">Jumlah Tagihan</th>
        <th width="15%">Jumlah Bayar</th>
    </tr>
    <? if($datadetail)
        foreach($datadetail as $i => $tagihan){?>
    <tr>
        <td><?=$tagihan['idtagihan']?></td>
        <td><?=$tagihan['namajenistagihan']?></td>
        <td align="right">Rp. <?=CStr::FormatNumber($tagihan['nominaltagihan'])?></td>
        <td align="right">Rp. <?=CStr::FormatNumber($tagihan['nominalbayar'])?>
        </td>
    </tr>
        <? }?>
   
    <tr>
        <th style="text-align:right" colspan="3" align="right">TOTAL</th>
        <th style="text-align:right">
            Rp. <?=CStr::FormatNumber($data['jumlahbayar'])?>
        </th>
    </tr>
    <tr>
        <th style="text-align:right" colspan="3" align="right">
            Timer : <input type="text" size="8" name="d2" id="d2" readonly />
            <br />
            <font color="#999999"><em>(Reversal / Pembatalan Pembayaran dapat di lakukan sebelum batas waktunya)</em></font>
        </th>
        <th valign="top">
        	<input type="hidden" name="idpembayaran" id="idpembayaran" value="<?=$data['idpembayaran']?>" />
        <? if($data and $c_reversal){?>
        	<input type="button" value="Reversal Pembayaran" onClick="goReversal()">
		<? }?>
        </th>
    </tr>
</table>
<? }?>