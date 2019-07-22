<?
require_once(Route::getModelPath('pengajuanbeasiswapendaftar'));

$a_input = array();
$a_input[] = array('kolom' => 'jumlahanakkeluarga', 'label' => 'Jumlah anak dalam keluarga', 'type' => 'N','maxlength' => 5, 'size' => '5px');
$a_input[] = array('kolom' => 'anakke', 'label' => 'Anda anak ke', 'type' => 'N','maxlength' => 5, 'size' => 5);


$r_key = $q[0];
$r_subkey = $q[1];
$r_act = $q[2];

//data post edit
$r_post = array();
$r_post['jumlahanakkeluarga'] = $q[3];
$r_post['anakke'] = $q[4];


if($r_act == 'saveedit')
{

	$record2 = array();
	foreach($r_post as $i=>$v){
		$record2[$i] = $v;
	}
	list($p_posterr,$p_postmsg) = mPengajuanBeasiswaPd::updateRecord($conn,$record2,$r_subkey,true);

}

$row = mPengajuanBeasiswaPd::getDataAnak($conn,$r_key);

?>
<?php if($r_act == 'editdetail') {
	?>
<table class="table table-bordered table-striped">
	<tr>
		<td colspan="2" class="DataBG">
			Keluarga
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="220">Jumlah anak dalam keluarga</td>
		<td class="RightColumnBG"><?= UI::createTextBox('jumlahanakkeluarga',$row['jumlahanakkeluarga'],'',4,50,true); ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG">Anda anak ke</td>
		<td class="RightColumnBG"><?= UI::createTextBox('anakke',$row['anakke'],'',4,50,true); ?></td>
	</tr>
</table>
<?php } else {?>
	<table class="table table-bordered table-striped">
		<tr>
			<td colspan="2" class="DataBG">
				Keluarga
			</td>
		</tr>
		<tr>
			<td class="LeftColumnBG" width="220">Jumlah anak dalam keluarga</td>
			<td class="RightColumnBG"><?= UI::createTextBox('jumlahanakkeluarga',$row['jumlahanakkeluarga'],'',4,50,false); ?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG">Anda anak ke</td>
			<td class="RightColumnBG"><?= UI::createTextBox('anakke',$row['anakke'],'',4,50,false); ?></td>
		</tr>
	</table>
	<?php } ?>

<script>
function editDetailAnak(elem){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	param[2] = elem.getAttribute('act'); //act


	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadjumlahanak", q: param }
				});

	jqxhr.done(function(data) {
		$("#biodata-beasiswa").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function batalDetailAnak(elem){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadjumlahanak", q: param }
				});

	jqxhr.done(function(data) {
		$("#biodata-beasiswa").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function saveDetailAnak(elem){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	param[2] = elem.getAttribute('act'); //act
	param[3] = $("#jumlahanakkeluarga").val();
	param[4] = $("#anakke").val();

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadjumlahanak", q: param }
				});

	jqxhr.done(function(data) {
		$("#biodata-beasiswa").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
</script>
