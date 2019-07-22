<?php
require_once(Route::getModelPath('pendidikanbeasiswamaba'));
require_once(Route::getModelPath('combo'));
require_once(Route::getUIPath('form'));

$r_nopendaftar =
$a_propinsi = mCombo::propinsi($conn);
$a_kota = mCombo::getKota($conn);

$a_input = array();
$a_input[] = array('kolom' => 'idbeasiswa', 'label' => 'Beasiswa', 'type' => 'S', 'option' => $a_beasiswa);
$a_input[] = array('kolom' => 'namasd', 'label' => 'Nama Sekolah');
$a_input[] = array('kolom' => 'kodepropinsisd', 'label' => 'Propinsi', 'type' => 'S', 'option' => $a_propinsi, 'add' => 'onchange="loadKotaSD()"', 'empty' => '-- Pilih Propinsi --');
$a_input[] = array('kolom' => 'kodekotasd', 'label' => 'Kota', 'type' => 'S', 'option' => $a_kota, 'empty' => true, 'empty' => '-- Pilih Kota --');
$a_input[] = array('kolom' => 'tahunmasuksd', 'label' => 'Tahun Masuk');
$a_input[] = array('kolom' => 'tahunlulussd', 'label' => 'Tahun Lulus');
$a_input[] = array('kolom' => 'namasmp', 'label' => 'Nama Sekolah');
$a_input[] = array('kolom' => 'kodepropinsismp', 'label' => 'Propinsi', 'type' => 'S', 'option' => $a_propinsi, 'add' => 'onchange="loadKotaSMP()"', 'empty' => '-- Pilih Propinsi --');
$a_input[] = array('kolom' => 'kodekotasmp', 'label' => 'Kota', 'type' => 'S', 'option' => $a_kota, 'empty' => true, 'empty' => '-- Pilih Kota --');
$a_input[] = array('kolom' => 'tahunmasuksmp', 'label' => 'Tahun Masuk');
$a_input[] = array('kolom' => 'tahunlulussmp', 'label' => 'Tahun Lulus');
$a_input[] = array('kolom' => 'namasma', 'label' => 'Nama Sekolah');
$a_input[] = array('kolom' => 'kodepropinsisma', 'label' => 'Propinsi', 'type' => 'S', 'option' => $a_propinsi, 'add' => 'onchange="loadKotaSMP()"', 'empty' => '-- Pilih Propinsi --');
$a_input[] = array('kolom' => 'kodekotasma', 'label' => 'Kota', 'type' => 'S', 'option' => $a_kota, 'empty' => true, 'empty' => '-- Pilih Kota --');
$a_input[] = array('kolom' => 'tahunmasuksma', 'label' => 'Tahun Masuk');
$a_input[] = array('kolom' => 'tahunlulussma', 'label' => 'Tahun Lulus');
$a_input[] = array('kolom' => 'jurusansma', 'label' => 'Jurusan / Peminatan');
$a_input[] = array('kolom' => 'raport_10_1', 'label' => '', 'type' => 'N','maxlength' => 5, 'size' => '5px');
$a_input[] = array('kolom' => 'raport_10_2', 'label' => '', 'type' => 'N','maxlength' => 5, 'size' => 5);
$a_input[] = array('kolom' => 'raport_11_1', 'label' => '', 'type' => 'N','maxlength' => 5, 'size' => 5);
$a_input[] = array('kolom' => 'raport_11_2', 'label' => '', 'type' => 'N','maxlength' => 5, 'size' => 5);
$a_input[] = array('kolom' => 'raport_12_1', 'label' => '', 'type' => 'N','maxlength' => 5, 'size' => 5);
$a_input[] = array('kolom' => 'raport_12_2', 'label' => '', 'type' => 'N','maxlength' => 5, 'size' => 5);
$a_input[] = array('kolom' => 'raport_ratarata', 'label' => 'Rata-rata nilai Rapor Semester I s/d V', 'type' => 'N','maxlength' => 5, 'size' => 5);
$a_input[] = array('kolom' => 'nemsmu', 'label' => 'Nilai UNAS', 'type' => 'N','maxlength' => 5, 'size' => 5);

$r_key = $q[0];
$r_subkey = $q[1];
$r_act = $q[2];

//save post
$r_post = array();
$r_post['namasd'] =$q[3];
$r_post['kodepropinsisd'] =$q[4];
$r_post['kodekotasd'] =$q[5];
$r_post['tahunmasuksd'] =$q[6];
$r_post['tahunlulussd'] =$q[7];
$r_post['namasmp'] =$q[8];
$r_post['kodepropinsismp'] =$q[9];
$r_post['kodekotasmp'] =$q[10];
$r_post['tahunmasuksmp'] =$q[11];
$r_post['tahunlulussmp'] =$q[12];
$r_post['namasma'] =$q[13];
$r_post['kodepropinsisma'] =$q[14];
$r_post['kodekotasma'] =$q[15];
$r_post['tahunmasuksma'] =$q[16];
$r_post['tahunlulussma'] =$q[17];
$r_post['raport_10_1'] =$q[18];
$r_post['raport_10_2'] =$q[19];
$r_post['raport_11_1'] =$q[20];
$r_post['raport_11_2'] =$q[21];
$r_post['raport_12_1'] =$q[22];
$r_post['raport_12_2'] =$q[23];
$r_post['raport_ratarata'] =$q[24];
$r_post['jurusansma'] =$q[25];
$r_post['nemsmu'] =$q[26];

if($r_act == 'saveedit')
{
	$conn->debug=true;
	$check = mPendidikanBeasiswa::getData($conn,$r_subkey);
	//var_dump($check);die;
	$record2 = array();
	if(empty($check)){
		foreach($r_post as $i=>$v){
			$record2[$i] = $v;
		}
		$record2['idpengajuanbeasiswa'] = $r_subkey;
		list($p_posterr,$p_postmsg) = mPendidikanBeasiswa::insertRecord($conn,$record2);
	}else{
		foreach($r_post as $i=>$v){
			$record2[$i] = $v;
		}
		list($p_posterr,$p_postmsg) = mPendidikanBeasiswa::updateRecord($conn,$record2,$r_subkey,true);
	}
}

$row = mPendidikanBeasiswa::getDataEdit($conn,$a_input,$r_subkey,$post);
?>
<div class="DivError" style="display:none"></div>
<div class="DivSuccess" style="display:none"></div>
<?php if($r_act == 'editdetail') { ?>
<table class="table table-bordered table-striped">
	<tr>
		<td colspan="2" class="DataBG">
			Riwayat Pendidikan
		</td>
	</tr>
	<tr>
		<td colspan="2"><b>1. Sekolah Dasar (SD)</b></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'namasd') ?></td>
		<td><?= Page::getDataInputOnly($row,'namasd') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'kodepropinsisd') ?></td>
		<td><?= Page::getDataInputOnly($row,'kodepropinsisd') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'kodekotasd') ?></td>
		<td><?= Page::getDataInputOnly($row,'kodekotasd') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'tahunmasuksd') ?></td>
		<td><?= Page::getDataInputOnly($row,'tahunmasuksd') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'tahunlulussd') ?></td>
		<td><?= Page::getDataInputOnly($row,'tahunlulussd') ?></td>
	</tr>
	<tr>
		<td colspan="2"><b>2. Sekolah Lanjutan Tingkat Pertama (SLTP)</b></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'namasmp') ?></td>
		<td><?= Page::getDataInputOnly($row,'namasmp') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'kodepropinsismp') ?></td>
		<td><?= Page::getDataInputOnly($row,'kodepropinsismp') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'kodekotasmp') ?></td>
		<td><?= Page::getDataInputOnly($row,'kodekotasmp') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'tahunmasuksmp') ?></td>
		<td><?= Page::getDataInputOnly($row,'tahunmasuksmp') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'tahunlulussmp') ?></td>
		<td><?= Page::getDataInputOnly($row,'tahunlulussmp') ?></td>
	</tr>
	<tr>
		<td colspan="2"><b>3. Sekolah Lanjutan Tingkat Atas (SLTA)</b></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'namasma') ?></td>
		<td><?= Page::getDataInputOnly($row,'namasma') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'jurusansma') ?></td>
		<td><?= Page::getDataInputOnly($row,'jurusansma') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'kodepropinsisma') ?></td>
		<td><?= Page::getDataInputOnly($row,'kodepropinsisma') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'kodekotasma') ?></td>
		<td><?= Page::getDataInputOnly($row,'kodekotasma') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'tahunmasuksma') ?></td>
		<td><?= Page::getDataInputOnly($row,'tahunmasuksma') ?></td>
	</tr>
	<tr>
		<td><?= Page::getDataLabel($row,'tahunlulussma') ?></td>
		<td><?= Page::getDataInputOnly($row,'tahunlulussma') ?></td>
	</tr>
	<tr>
		<td>Nilai Raport * </td>
		<td>
			<table border=0>
				<tr>
					<td colspan=2>Kelas X</td>
					<td colspan=2>Kelas XI</td>
					<td colspan=1>Kelas XII</td>
				</tr>
				<tr>
					<td>Smt. 1</td>
					<td>Smt. 2</td>
					<td>Smt. 1</td>
					<td>Smt. 2</td>
					<td>Smt. 1</td>
				</tr>
				<tr>
					<td><?= Page::getDataValue($row,'raport_10_1')?></td>
					<td><?= Page::getDataValue($row,'raport_10_2')?></td>
					<td><?= Page::getDataValue($row,'raport_11_1')?></td>
					<td><?= Page::getDataValue($row,'raport_11_2')?></td>
					<td><?= Page::getDataValue($row,'raport_12_1')?></td>
				</tr>
			</table>
		</td>
	</tr>
	<?
	$rata = Page::getDataValue($row,'raport_10_1')+Page::getDataValue($row,'raport_10_2')+Page::getDataValue($row,'raport_11_1')+Page::getDataValue($row,'raport_11_2')+ Page::getDataValue($row,'raport_12_1');
	$row['raport_ratarata'] = $rata;
	echo Page::getDataTR($row,'raport_ratarata');
	echo Page::getDataTR($row,'nemsmu');
	?>
</table>
<?php } else {?>

<table class="table table-bordered table-striped">
	<tr>
		<td colspan="2" class="DataBG">
			Riwayat Pendidikan
		</td>
	</tr>
	<tr>
		<td colspan="2"><b>1. Sekolah Dasar (SD)</b></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'namasd') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'namasd') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'kodepropinsisd') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'kodepropinsisd') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'kodekotasd') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'kodekotasd') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'tahunmasuksd') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'tahunmasuksd') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'tahunlulussd') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'tahunlulussd') ?></td>
	</tr>

	<tr>
		<td colspan="2"><b>2. Sekolah Lanjutan Tingkat Pertama (SLTP)</b></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'namasmp') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'namasmp') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'kodepropinsismp') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'kodepropinsismp') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'kodekotasmp') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'kodekotasmp') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'tahunmasuksmp') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'tahunmasuksmp') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'tahunlulussmp') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'tahunlulussmp') ?></td>
	</tr>

	<tr>
		<td colspan="2"><b>3. Sekolah Lanjutan Tingkat Atas (SLTA)</b></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'namasma') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'namasma') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'jurusansma') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'jurusansma') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'kodepropinsisma') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'kodepropinsisma') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'kodekotasma') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'kodekotasma') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'tahunmasuksma') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'tahunmasuksma') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220"><?= Page::getDataLabel($row,'tahunlulussma') ?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'tahunlulussma') ?></td>
	</tr>

	<tr>
		<td>Nilai Raport * </td>
		<td>
			<table border=0>
				<tr>
					<td colspan=2>Kelas X</td>
					<td colspan=2>Kelas XI</td>
					<td colspan=1>Kelas XII</td>
				</tr>
				<tr>
					<td>Smt. 1</td>
					<td>Smt. 2</td>
					<td>Smt. 1</td>
					<td>Smt. 2</td>
					<td>Smt. 1</td>
				</tr>
				<tr>
					<td><?= Page::getDataValue($row,'raport_10_1')?></td>
					<td><?= Page::getDataValue($row,'raport_10_2')?></td>
					<td><?= Page::getDataValue($row,'raport_11_1')?></td>
					<td><?= Page::getDataValue($row,'raport_11_2')?></td>
					<td><?= Page::getDataValue($row,'raport_12_1')?></td>
				</tr>
			</table>
		</td>
	</tr>
	<?php
	$rata = Page::getDataValue($row,'raport_10_1')+Page::getDataValue($row,'raport_10_2')+Page::getDataValue($row,'raport_11_1')+Page::getDataValue($row,'raport_11_2')+ Page::getDataValue($row,'raport_12_1');
	$row['raport_ratarata'] = $rata;
	echo Page::getDataTR($row,'raport_ratarata');
	echo Page::getDataTR($row,'nemsmu');
	?>
</table>
<?php } ?>
<script>
$(document).ready(function() {
	loadKotaSD();
});

// ajax ganti kota
function loadKotaSD() {
	var param = new Array();
	param[0] = $("#kodepropinsisd").val();
	param[1] = "<?= $r_kodekotasd ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});

	jqxhr.done(function(data) {
		$("#kodekotasd").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function editDetailPd(elem){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	param[2] = elem.getAttribute('act'); //act


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

function saveDetailPd(elem){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	param[2] = elem.getAttribute('act'); //act
	param[3] = $("#namasd").val();
	param[4] = $("#kodepropinsisd").val();
	param[5] = $("#kodekotasd").val();
	param[6] = $("#tahunmasuksd").val();
	param[7] = $("#tahunlulussd").val();
	param[8] = $("#namasmp").val();
	param[9] = $("#kodepropinsismp").val();
	param[10] = $("#kodekotasmp").val();
	param[11] = $("#tahunmasuksmp").val();
	param[12] = $("#tahunlulussmp").val();
	param[13] = $("#namasma").val();
	param[14] = $("#kodepropinsisma").val();
	param[15] = $("#kodekotasma").val();
	param[16] = $("#tahunmasuksma").val();
	param[17] = $("#tahunlulussma").val();
	param[18] = $("#raport_10_1").val();
	param[19] = $("#raport_10_2").val();
	param[20] = $("#raport_11_1").val();
	param[21] = $("#raport_11_2").val();
	param[22] = $("#raport_12_1").val();
	param[23] = $("#raport_12_2").val();
	param[24] = $("#raport_ratarata").val();
	param[25] = $("#jurusansma").val();
	param[26] = $("#nemsmu").val();



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
function batalDetailPd(elem){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey

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
</script>
