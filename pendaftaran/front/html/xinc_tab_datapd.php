<?php
//$conn->debug = true;
require_once(Route::getModelPath('pendaftarbeasiswa'));
require_once(Route::getModelPath('pengajuanbeasiswapendaftar'));
require_once(Route::getModelPath('combo'));
require_once(Route::getUIPath('form'));


$a_input = array();	
$a_input[] = array('kolom' => 'idbeasiswa', 'label' => 'Beasiswa', 'type' => 'S', 'option' => $a_beasiswa);
$a_input[] = array('kolom' => 'alasan1', 'label' => 'Alasan 1');
$a_input[] = array('kolom' => 'alasan2', 'label' => 'Alasan 2');

$r_key = $q[0];
$r_subkey = $q[1];
$r_act = $q[2];

//save post
$r_post = array();
$r_post['alasan1'] =$q[3];
$r_post['alasan2'] =$q[4];


if($r_act == 'saveedit')
{
	$conn->debug=true;
	//$check = mPendidikanBeasiswa::getData($conn,$r_subkey);

	//var_dump($check);die;
	
	$record2 = array();
	foreach($r_post as $i=>$v){
		$record2[$i] = $v;
	}
	list($p_posterr,$p_postmsg) = mPengajuanBeasiswaPd::updateRecord($conn,$record2,$r_subkey,true);	

}

$row = mPendaftarBeasiswa::getDataAlasan($conn,$r_key);

?>	
<div class="DivError" style="display:none"></div>
<div class="DivSuccess" style="display:none"></div>
<?php if($r_act == 'editdetail') { ?>
<table class="table table-bordered table-striped">
	<tr>
		<td colspan="3" class="DataBG">
			Data pemilihan jurusan 
			<div class="pull-right">	
				<button id="<?=$r_subkey?>" act="saveedit" class="btn btn-success" type="button" name="saveedit" onclick="saveDetailPiljur(this)"> 
					Simpan
				</button>	
				<button id="<?=$r_subkey?>" act="bataledit" class="btn btn-warning" type="button" name="bataledit" onclick="batalDetailPiljur(this)"> 
					Batal Edit
				</button>	
			</div>
		</td>
	</tr>	
	<tbody>
		<tr>
			<td class="LeftColumnBG" width="80">Pilihan 1</td>
			<td width="10">:</td>
			<td class="RightColumnBG"><?=$row['pilihan1']?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG" width="80">Pilihan 2</td>
			<td width="10">:</td>
			<td class="RightColumnBG"><?=$row['pilihan2']?></td>
		</tr>
		<tr>
			<td colspan="3">Alasan Memilih Jurusan :</td>
		</tr>	
		<tr>
			<td class="LeftColumnBG">Alasan 1</td>
			<td>:</td>
			<td class="RightColumnBG"><?= UI::createTextArea('alasan1',$row['alasan1'],'',4,50,true); ?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG">Alasan 2</td>
			<td>:</td>
			<td class="RightColumnBG"><?= UI::createTextArea('alasan2',$row['alasan2'],'',4,50,true); ?></td>
		</tr>	
	</tbody>
</table>
<?PHP }  else { ?>
<table class="table table-bordered table-striped">
	<tr>
		<td colspan="3" class="DataBG">
			Data pemilihan jurusan
			<div class="pull-right">	
				<button id="<?=$r_subkey?>" act="editdetail" class="btn btn-primary" type="button" name="editdetail" onclick="editDetailPiljur(this)"> 
					Edit
				</button>
			</div>
		</td>
	</tr>
	<tbody>
		<tr>
			<td class="LeftColumnBG" width="80">Pilihan 1</td>
			<td width="10">:</td>
			<td class="RightColumnBG"><?=$row['pilihan1']?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG" width="80">Pilihan 2</td>
			<td width="10">:</td>
			<td class="RightColumnBG"><?=$row['pilihan2']?></td>
		</tr>
		<tr>
			<td colspan="3">Alasan Memilih Jurusan :</td>
		</tr>	
		<tr>
			<td class="LeftColumnBG">Alasan 1</td>
			<td>:</td>
			<td class="RightColumnBG"><?= UI::createTextArea('alasan1',$row['alasan1'],'',4,50,false); ?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG">Alasan 2</td>
			<td>:</td>
			<td class="RightColumnBG"><?= UI::createTextArea('alasan2',$row['alasan2'],'',4,50,false); ?></td>
		</tr>	
	</tbody>
</table>
<?php } ?>
<script>
function editDetailPiljur(elem){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	param[2] = elem.getAttribute('act'); //act

	
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

function saveDetailPiljur(elem){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	param[2] = elem.getAttribute('act'); //act
	param[3] = $("#alasan1").val();
	param[4] = $("#alasan2").val();
	
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
function batalDetailPiljur(elem){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	
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
</script>
