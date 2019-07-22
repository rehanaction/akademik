<? if($arr_tagihan){?>
<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
                    <tr>
                    	<th >Id Tagihan</th>
                    	<th >Periode Tagihan</th>
                    	<th >Jumlah Tagihan</th>
                    	<th >Jumlah Bayar</th>
                        <th ></th>
                    </tr>
                    <? 
						foreach($arr_tagihan as $i => $tagihan){
							if(empty($tagihan['iddeposit']))
								$t_key = $tagihan['idtagihan'];
							else
								$t_key = 'D'.$tagihan['iddeposit'];
							
							$jumlah = $tagihan['nominaltagihan']+$tagihan['denda']-$tagihan['potongan'];
					?>
                    <tr>
                    	<td><?=$tagihan['idtagihan']?></td>
                        <td><?=$tagihan['bulantahun']?$tagihan['bulantahun']:$tagihan['periode']?></td>
                        <td align="right"><?=CStr::FormatNumber($jumlah)?></td>
                        <td align="right">
                        	<? if($tagihan['isangsur']==0){?>
                            <?= UI::createTextBox($t_key,0,'ControlRead ControlNumber','20','20',true,'style="text-align:right"')?>
                            <? } else {?>
                            <?= UI::createTextBox($t_key,0,'ControlStyle ControlNumber','20','20',true,'style="text-align:right"')?>
                            <? }?>
                        </td>
                        <td>
                        <input type="checkbox" name="tagihan[]" id="cek<?=$t_key?>" value="<?=$t_key?>" onChange="hitungTotal('<?=$t_key?>','<?=$jumlah?>')">
                        </td>
                    </tr>
						<? }?>
                   
                    <tr>
						<th style="text-align:right" align="right" ></th>
                    	<th style="text-align:right" align="right" colspan="2">TOTAL</th>
                        <th style="text-align:right">
                        	<input type="hidden" name="jumlahtotal" id="jumlahtotal" value="0">
                            <?= UI::createTextBox('labeltotal',0,'ControlRead','20','20',true,'readonly style="text-align:right"')?>
                        </th>
                        <th></th>
                    </tr>
                    <tr>
						<th style="text-align:right" align="right">Tanggal Bayar</th>
						<th style="text-align:left" align="left" >
							<?= UI::createTextBox('tglbayar',date('d-m-Y'),'ControlStyle','10','10') ?>
							<img src="images/cal.png" id="tglbayar_trg" style="cursor:pointer;" title="Pilih tanggal bayar">
							<script type="text/javascript">
							Calendar.setup({
								inputField     :    "tglbayar",
								ifFormat       :    "%d-%m-%Y",
								button         :    "tglbayar_trg",
								align          :    "Br",
								singleClick    :    true
							});
							</script>
						</th>
                    	<th style="text-align:right" align="right">Jumlah Uang</th>
                        <th style="text-align:right">
                            <?= UI::createTextBox('jumlahbayar',0,'ControlStyle ControlNumber','20','20',true,'style="text-align:right"')?>
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
