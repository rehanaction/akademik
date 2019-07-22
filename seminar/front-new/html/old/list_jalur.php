
<?php require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
        <h3>Informasi Jalur Pendaftaran yang Dibuka</h3>
      </div>
      <form method="post" action="index.php?page=list_jadwal" id="form_beranda">
      <table class="table" >
        <tr style="background:#000; color:#fff;">
            <th width="30%">Tanggal Pendaftaran</th>
            <th width="25%">Jalur</th>
            <th width="15%">Status</th>
            <th>Info Lebih Lanjut</th>
            <th></th>
        </tr>
        <?
            while($jp=$jalur->FetchRow()){  
                $jalurpenerimaan = $jp['jalurpenerimaan'];
                $idgelombang = $jp['idgelombang'];
                $periodedaftar = $jp['periodedaftar'];
                $tglawaldaftar = $jp['tglawaldaftar'];
                $tglakhirdaftar = $jp['tglakhirdaftar'];
				$tglsekarang = date('Y-m-d');
                $isopen = $jp['isopen'];
                $isbayar = $jp['isbayar'];
                $key = $periodedaftar.'|'.$idgelombang.'|'.$jalurpenerimaan;
        ?>
		<tr>
			<td class="tgl-pendaftaran"><?=(!empty($tglawaldaftar) && !empty($tglakhirdaftar)) ? Date::indoDate($tglawaldaftar)." s/d ".Date::indoDate($tglakhirdaftar) : 'Jadwal belum ditentukan' ?></td>
			<td>
				<button type="submit" name="jlr" id="btnSave" class="link" value="<?= $jalurpenerimaan?>">
					<b><?=$jalurpenerimaan?></b>
				</button>
				<br>
				<small>Periode <?=Pendaftaran::getNamaPeriode($jp['periodedaftar'],true)?> <br> Gelombang <?=$idgelombang?></small>
			</td>
			<td><?= ($isopen=='t') ?  "<span class='label label-success'>Buka</span>" : "<span class='label label-default'>Tutup</span>" ?></td>
			<td align="center">
			  <?if($jp['isopen']=='t' and ($tglsekarang >= $tglawaldaftar and $tglsekarang <= $tglakhirdaftar)){	?>
				  <button style="margin-bottom: 5px;" type="button" 
					<?if($isbayar=='t') {?>
						onClick="goView('data_token&q=<?= base64_encode($key) ?>')"
					<? }else{?>
						onClick="goChange('<?=$key?>')"
					<?} ?> 
					class="btn btn-success btn-sm">
						<span class="glyphicon glyphicon-list"></span> Daftar
				</button>				
			  <?}else{ ?>
				  <button class="btn btn-danger btn-sm">Pendaftaran Tutup</button>
			<?}?>
				<button type="submit" name="jlr" id="btnSave" value="<?= $jalurpenerimaan?>" class="btn btn-primary btn-sm">
					<span class="glyphicon glyphicon-search"></span> Lihat informasi pendaftaran
				</button>      
			</td>
		</tr>
        <? } ?>
    </table>
			<br/><br/><br/><br/><br/>
			
			<input type="hidden" name="periodedaftar" id="periodedaftar">
            <input type="hidden" name="jalurpenerimaan" id="jalurpenerimaan">
            <input type="hidden" name="idgelombang" id="idgelombang">
            </form>
    </div>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>  
<script type="text/javascript">
	
	function goChange(periode,gelombang,jalur){
		
		$("#periodedaftar").val(periode);
		$("#jalurpenerimaan").val(jalur);
		$("#idgelombang").val(gelombang);
		
		$("#form_beranda").attr("action", "index.php?page=data_input");
		$("#form_beranda").submit();
	}
	
	function goUpload(elem){
		var str = elem.id.split("|");
		
		$("#periodedaftar").val(str[0]);
		$("#jalurpenerimaan").val(str[1]);
		$("#idgelombang").val(str[2]);
		
		$("#form_beranda").attr("action", "index.php?page=set_upload");
		$("#form_beranda").submit();		
	}
	
</script> 
