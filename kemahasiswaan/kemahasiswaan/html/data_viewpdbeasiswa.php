		<div class="panel panel-default" style="margin-top:20px;">
		  <div class="panel-heading">
			  <span class="glyphicon glyphicon-user"></span> Data Pendaftar
			  </div>
		  <div class="panel-body">
		<?	$a_required = array();
			foreach($row as $t_row) {
				if($t_row['notnull'])
					$a_required[] = $t_row['id'];
				}
		?>
			<table width="100%" cellpadding="4" cellspacing="2" class="table table-bordered table-striped">
				<tr>
					<td align="center" valign="top" rowspan="<?= $r_key ? '13' : '6'?>">
						<? if (!empty($r_key)) {?>

						<?= uForm::getImageMahasiswa($conn,$r_key,true) ?>
						<? } ?>
					</td>
				</tr>
				<? if (!empty($r_key)) {?>
				<tr>
					<td>Nomor Pendaftaran </td>
					<td>:</td>
					<td><?= $r_key?></td>
				</tr>
				<tr>
					<td>Token / PIN</td>
					<td>:</td>
					<td><?= $r_token?></td>
				</tr>
					<? } ?>
				<tr>
					<td><?= Page::getDataLabel($row,'nama')?></td>
					<td>:</td>
					<td><?= Page::getDataValue($row,'nama')?></td>
				</tr>
				<? if (empty($r_key)){ ?>
				<tr>
					<td><?= Page::getDataLabel($row,'sistemkuliah')?></td>
					<td>:</td>
					<td><?= Page::getDataValue($row,'sistemkuliah')?></td>
				</tr>
				<?	}?>
				<? if (!empty ($r_key)){ ?>
				<tr>
					<td>Jalur Penerimaan</td>
					<td>:</td>
					<td><?= $r_jalur?></td>
				</tr>
				<tr>
					<td>Periode Daftar</td>
					<td>:</td>
					<td><?= Pendaftaran::getNamaPeriode($r_periode)?></td>
				</tr>
				<tr>
					<td>Gelombang</td>
					<td>:</td>
					<td><?= $r_gel?></td>
				</tr>
				<tr>
					<td>Sistem Kuliah</td>
					<td>:</td>
					<td><?= $arrSistemkuliah[$r_sistemkuliah]?></td>
				</tr>
				<?}?>

				 <tr>
					<td><?= Page::getDataLabel($row,'idperingkat')?></td>
					<td>:</td>
					<td><?= Page::getDataValue($row,'idperingkat')?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($row,'pilihan1')?></td>
					<td>:</td>
					<td><?= $arrJurusan[Page::getDataValue($row,'pilihan1')]?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($row,'pilihan2')?></td>
					<td>:</td>
					<td><?= $arrJurusan[Page::getDataValue($row,'pilihan2')]?></td>
				</tr>
			</table>
		  </div>
		</div>

		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a href="#pilihan">Pilihan Jurusan</a></li>
			<li><a href="#biodata">Data Biodata</a></li>
			<li><a href="#riwayatpendidikan">Riwayat Pendidikan</a></li>
			<li><a href="#prestasi">Prestasi</a></li>
			<li><a href="#organisasi">Organisasi</a></li>
			<li><a href="#pelatihan">Pelatihan</a></li>
			<li><a href="#kerja">Pengalaman Kerja</a></li>
			<li><a href="#informasi">Data Keluarga</a></li>
			<li><a href="#potensi">Data Potensi Diri</a></li>
			<li><a href="#syarat">Syarat</a></li>
			<!--<li><a href="#berkas">Syarat Beasiswa</a></li> -->
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="pilihan">
				<div id="v-alasanpd"></div>
			</div>
			<? require_once($conf['view_dir'].'xinc_tabbiodata.php'); ?>
			<? require_once($conf['view_dir'].'xinc_tabinformasi.php'); ?>
			<div class="tab-pane" id="prestasi">
				<div id="v-prestasi"></div>
			</div>
			<div class="tab-pane" id="organisasi">
				<div id="v-organisasi"></div>
			</div>
			<div class="tab-pane" id="pelatihan">
				<div id="v-pelatihan"></div>
			</div>
			<div class="tab-pane" id="kerja">
				<div id="v-kerja"></div>
			</div>
			<div class="tab-pane" id="riwayatpendidikan">
				<div id="v-riwayatpd"></div>
			</div>
			<div class="tab-pane" id="potensi">
				<div id="v-potensi"></div>
			</div>
			<? require_once($conf['view_dir'].'xinc_tabberkas.php'); ?>
		</div>

<script>
  $('#myTab a').click(function (e) {
	  e.preventDefault()
	  $(this).tab('show')
	})
</script>
	<input type="hidden" name="act" id="act">
	<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
	<input type="hidden" name="detail" id="detail">
	<input type="hidden" name="subkey" id="subkey">

<script>
loadRiwayatPd();
loadAlasanPd();
loadPrestasi();
loadOrganisasi();
loadPelatihan();
loadKerja();
loadBiodata();
loadPotensi();
function loadRiwayatPd(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";
	param[2] = "<?= $r_nopendaftar ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadriwayatpd", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-riwayatpd").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadAlasanPd(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadalasanpd", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-alasanpd").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadPrestasi(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadprestasi", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-prestasi").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadOrganisasi(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadorganisasi", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-organisasi").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadPelatihan(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpelatihan", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-pelatihan").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadKerja(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadkerja", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-kerja").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadBiodata(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadbiodata", q: param }
				});

	jqxhr.done(function(data) {
		$("#biodata-beasiswa").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadPotensi(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpotensi", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-potensi").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
</script>
