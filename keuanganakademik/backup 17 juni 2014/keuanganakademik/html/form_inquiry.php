<? if($arr_tagihan){?>
<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
                    <tr>
                    	<th width="15%">Id Tagihan</th>
                    	<th>Jenis Tagihan</th>
                    	<th width="15%">Periode Tagihan</th>
                    	<th width="15%">Jumlah Tagihan</th>
                    	<th width="15%">Jumlah Bayar</th>
                        <th width="2%"></th>
                    </tr>
                    <? 
						foreach($arr_tagihan as $i => $tagihan){?>
                    <tr>
                    	<td><?=$tagihan['idtagihan']?></td>
                        <td><?=$tagihan['jenistagihan'].' - '.$tagihan['namajenistagihan']?></td>
                        <td><?=$tagihan['bulantahun']?$tagihan['bulantahun']:$tagihan['periode']?></td>
                        <td align="right"><?=CStr::FormatNumber($tagihan['nominaltagihan'])?></td>
                        <td align="right">
                        	<? if($tagihan['iangsur']==0){?>
                            <?= UI::createTextBox($tagihan['idtagihan'],0,'ControlRead','20','20',true,'style="text-align:right"')?>
                            <? } else {?>
                            <?= UI::createTextBox($tagihan['idtagihan'],0,'ControlStyle','20','20',true,'style="text-align:right"')?>
                            <? }?>
                        </td>
                        <td>
                        <input type="checkbox" name="tagihan[]" id="cek<?=$tagihan['idtagihan']?>" value="<?=$tagihan['idtagihan']?>" onChange="hitungTotal('<?=$tagihan['idtagihan']?>','<?=$tagihan['nominaltagihan']?>')">
                        </td>
                    </tr>
						<? }?>
                   
                    <tr>
                    	<th style="text-align:right" colspan="4" align="right">TOTAL</th>
                        <th style="text-align:right">
                        	<input type="hidden" name="jumlahtotal" id="jumlahtotal" value="0">
                            <?= UI::createTextBox('labeltotal',0,'ControlRead','20','20',true,'readonly style="text-align:right"')?>
                        </th>
                        <th></th>
                    </tr>
                    <tr>
                    	<th style="text-align:right" colspan="4" align="right">Jumlah Uang</th>
                        <th style="text-align:right">
                            <?= UI::createTextBox('jumlahbayar',0,'ControlStyle','20','20',true,'style="text-align:right"')?>
                        </th>
                        <th></th>
                    </tr>
                    <tr>
                    	<th style="text-align:right" colspan="4" align="right"></th>
                        <th>
                        	<? if($c_payment){?>
                            	<input type="button" value="Payment Tagihan" onClick="goPayment()">
                        	<? }?>
                        </th>
                        <th></th>
                    </tr>
                    </table>
<? }?>