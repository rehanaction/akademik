<div class="tab-pane" id="syarat">
	<form name="filesyarat" id="filesyarat" method="post"  enctype="multipart/form-data">
		<table class="table table-bordered table-striped">
		<?
		//var_dump($rowd);
		foreach($rowd as $row){
		?>
		<tr>
			<td><?=$row['namasyaratbeasiswa']?></td>
			<td class="inline">
				<?php 
					if(!empty($row['fileberkas'])){
				?>
					<a href="<?=$conf['upload_dir'].'syaratbeasiswamaba/'.$r_idpengajuan.';'.$row['kodesyaratbeasiswa'].';'.$r_idbeasiswa.'.'.end(explode('.',$row['fileberkas']))?>" target="_blank"><?=$row['fileberkas']?></a>
				<?php
					}
				?>
				<input type="file" name="fileberkas_<?=$row['kodesyaratbeasiswa']?>" id="fileberkas" ><br>
				<button class="btn-success" type="button" data-id="<?=$r_idpengajuan.'|'.$row['kodesyaratbeasiswa'].'|'.$r_idbeasiswa?>" value="Upload" onclick="goUploadSyarat(this)"> Upload</button>
				<?php 
				if(!empty($row['fileberkas'])){
				?>
					<u class="ULink" data-id="<?=$r_idpengajuan.'|'.$rowdd['kodesyaratbeasiswa'].'|'.$r_idbeasiswa?>" data-type="<?=end(explode('.',$rowdd['fileberkas']))?>" onclick="goDeleteFileSyarat(this)">Hapus file</u>

				<?php 
				}
				?>
			</td>
		</tr>
		<?
		}
		?>
		</table>
		 <input type="hidden" name="act" id="act">
		 <input type="hidden" name="subkey" id="subkey">
	</form>
<script>

function goUploadSyarat(elem) {
	filesyarat.act.value='upload';
	filesyarat.subkey.value = $(elem).attr('data-id');
    document.getElementById("filesyarat").submit();
}

function goDeleteFileSyarat(elem) {
	document.getElementById("act").value = "deletefile";
	document.getElementById("subkey").value = $(elem).attr('data-id');
	document.getElementById("filetype").value = $(elem).attr('data-type');
	goSubmit();
}
</script>	
</div>
